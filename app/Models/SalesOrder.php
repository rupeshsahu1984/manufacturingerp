<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrder extends Model
{
    protected $table            = 'sales_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'so_number', 'invoice_no', 'customer_id', 'order_date', 'delivery_date',
        'customer_address', 'customer_mobile', 'customer_gstn',
        'transport_amount', 'transport_tax', 'description',
        'subtotal', 'discount_total', 'gst_amount', 'total_amount',
        'status', 'payment_terms', 'delivery_address',
        'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'so_number' => 'required|max_length[20]',
        'customer_id' => 'required|integer',
        'order_date' => 'required|valid_date'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function generateUniqueSoNumber()
    {
        $prefix = 'SO';
        $year = date('Y');
        $month = date('m');
        
        // Get the last SO number for this year/month
        $lastSO = $this->where('so_number LIKE', "$prefix$year$month%")
                       ->orderBy('so_number', 'DESC')
                       ->first();
        
        if ($lastSO) {
            // Extract the sequence number and increment
            $lastSequence = intval(substr($lastSO['so_number'], -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . $year . $month . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    public function getSalesOrdersWithDetails($filters = [])
    {
        $builder = $this->db->table('sales_orders so')
                           ->select('so.*, c.customer_name, c.customer_code')
                           ->join('customers c', 'c.id = so.customer_id', 'left')
                           ->orderBy('so.created_at', 'DESC');

        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('so.so_number', $filters['search'])
                    ->orLike('c.customer_name', $filters['search'])
                    ->orLike('c.customer_code', $filters['search'])
                    ->groupEnd();
        }

        if (!empty($filters['customer'])) {
            $builder->where('so.customer_id', $filters['customer']);
        }

        if (!empty($filters['status'])) {
            $builder->where('so.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('so.order_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('so.order_date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }

    public function getSalesOrderWithItems($id)
    {
        // Get sales order details
        $salesOrder = $this->find($id);
        
        if (!$salesOrder) {
            return null;
        }

        // Get customer details
        $customerModel = new Customer();
        $customer = $customerModel->find($salesOrder['customer_id']);
        if ($customer) {
            $salesOrder['customer_name'] = $customer['customer_name'];
            $salesOrder['customer_code'] = $customer['customer_code'];
        }

        // Get order items with product details
        $itemModel = new SalesOrderItem();
        $items = $itemModel->getSalesOrderItems($id);
        
        $salesOrder['items'] = $items;
        
        return $salesOrder;
    }

    public function getSalesOrderStats()
    {
        $stats = [
            'total_orders' => $this->countAll(),
            'pending_orders' => $this->whereIn('status', ['draft', 'confirmed', 'processing'])->countAllResults(),
            'ready_orders' => $this->where('status', 'ready')->countAllResults(),
            'dispatched_orders' => $this->whereIn('status', ['dispatched', 'delivered'])->countAllResults(),
            'cancelled_orders' => $this->where('status', 'cancelled')->countAllResults()
        ];

        return $stats;
    }

    public function getConfirmedSalesOrders()
    {
        return $this->where('status', 'confirmed')->findAll();
    }

    public function getReadyForDispatch()
    {
        return $this->where('status', 'ready')->findAll();
    }
}
