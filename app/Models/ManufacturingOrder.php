<?php

namespace App\Models;

use CodeIgniter\Model;

class ManufacturingOrder extends Model
{
    protected $table = 'manufacturing_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'bom_id', 'production_quantity', 'planned_start_date', 'planned_completion_date',
        'actual_start_date', 'actual_completion_date', 'priority', 'status', 'notes',
        'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Get pending manufacturing orders
     */
    public function getPendingOrders()
    {
        return $this->select('manufacturing_orders.*, b.version, p.product_name as finished_product_name')
            ->join('boms b', 'manufacturing_orders.bom_id = b.id')
            ->join('products p', 'b.finished_product_id = p.id')
            ->where('manufacturing_orders.status', 'pending')
            ->orderBy('manufacturing_orders.priority', 'DESC')
            ->orderBy('manufacturing_orders.planned_start_date', 'ASC')
            ->findAll();
    }

    /**
     * Get active productions
     */
    public function getActiveProductions()
    {
        return $this->select('manufacturing_orders.*, b.version, p.product_name as finished_product_name')
            ->join('boms b', 'manufacturing_orders.bom_id = b.id')
            ->join('products p', 'b.finished_product_id = p.id')
            ->where('manufacturing_orders.status', 'in_progress')
            ->orderBy('manufacturing_orders.actual_start_date', 'DESC')
            ->findAll();
    }

    /**
     * Get completed productions
     */
    public function getCompletedProductions($limit = 10)
    {
        return $this->select('manufacturing_orders.*, b.version, p.product_name as finished_product_name')
            ->join('boms b', 'manufacturing_orders.bom_id = b.id')
            ->join('products p', 'b.finished_product_id = p.id')
            ->where('manufacturing_orders.status', 'completed')
            ->orderBy('manufacturing_orders.actual_completion_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get production statistics
     */
    public function getProductionStats()
    {
        $stats = [
            'total_orders' => $this->countAll(),
            'pending_orders' => $this->where('status', 'pending')->countAllResults(),
            'active_productions' => $this->where('status', 'in_progress')->countAllResults(),
            'completed_productions' => $this->where('status', 'completed')->countAllResults(),
            'total_quantity_produced' => $this->selectSum('production_quantity')
                ->where('status', 'completed')
                ->get()
                ->getRow()
                ->production_quantity ?? 0
        ];

        return $stats;
    }

    /**
     * Create manufacturing order
     */
    public function createOrder($data)
    {
        return $this->insert($data);
    }

    /**
     * Get order with details
     */
    public function getOrderWithDetails($orderId)
    {
        return $this->select('manufacturing_orders.*, b.version, b.description as bom_description, p.product_name as finished_product_name, p.product_code as finished_product_code')
            ->join('boms b', 'manufacturing_orders.bom_id = b.id')
            ->join('products p', 'b.finished_product_id = p.id')
            ->where('manufacturing_orders.id', $orderId)
            ->first();
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status, $limit = null)
    {
        $query = $this->select('manufacturing_orders.*, b.version, p.product_name as finished_product_name')
            ->join('boms b', 'manufacturing_orders.bom_id = b.id')
            ->join('products p', 'b.finished_product_id = p.id')
            ->where('manufacturing_orders.status', $status)
            ->orderBy('manufacturing_orders.created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }

    /**
     * Get orders by date range
     */
    public function getOrdersByDateRange($startDate, $endDate)
    {
        return $this->select('manufacturing_orders.*, b.version, p.product_name as finished_product_name')
            ->join('boms b', 'manufacturing_orders.bom_id = b.id')
            ->join('products p', 'b.finished_product_id = p.id')
            ->where('manufacturing_orders.created_at >=', $startDate)
            ->where('manufacturing_orders.created_at <=', $endDate)
            ->orderBy('manufacturing_orders.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get production efficiency
     */
    public function getProductionEfficiency($orderId)
    {
        $order = $this->find($orderId);
        if (!$order) {
            return 0;
        }

        $plannedDays = (strtotime($order['planned_completion_date']) - strtotime($order['planned_start_date'])) / (60 * 60 * 24);
        $actualDays = 0;

        if ($order['actual_start_date'] && $order['actual_completion_date']) {
            $actualDays = (strtotime($order['actual_completion_date']) - strtotime($order['actual_start_date'])) / (60 * 60 * 24);
        }

        if ($plannedDays > 0) {
            return ($plannedDays / $actualDays) * 100;
        }

        return 0;
    }
}
