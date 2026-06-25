<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_date',
        'payment_terms',
        'delivery_address',
        'notes',
        'terms_conditions',
        'is_urgent',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'created_by',
        'updated_by',
        'approved_by',
        'ordered_by',
        'received_by',
        'cancelled_by',
        'approved_at',
        'ordered_at',
        'received_at',
        'cancelled_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'po_number' => 'required|is_unique[purchase_orders.po_number,id,{id}]',
        'supplier_id' => 'required|numeric',
        'order_date' => 'required|valid_date',
        'expected_date' => 'required|valid_date',
        'status' => 'required|in_list[draft,pending,approved,ordered,received,cancelled]'
    ];

    protected $validationMessages = [
        'po_number' => [
            'required' => 'PO number is required',
            'is_unique' => 'PO number already exists'
        ],
        'supplier_id' => [
            'required' => 'Supplier is required',
            'numeric' => 'Invalid supplier ID'
        ],
        'order_date' => [
            'required' => 'Order date is required',
            'valid_date' => 'Invalid order date'
        ],
        'expected_date' => [
            'required' => 'Expected date is required',
            'valid_date' => 'Invalid expected date'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Invalid status'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get purchase orders with optional filters
     */
    public function getPurchaseOrders($filters = [])
    {
        $builder = $this->builder();
        
        // Join with supplier table and count items
        $builder->select('purchase_orders.*, suppliers.supplier_name, suppliers.contact_person, COUNT(purchase_order_items.id) as item_count')
                ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                ->join('purchase_order_items', 'purchase_order_items.po_id = purchase_orders.id', 'left')
                ->groupBy('purchase_orders.id');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('po_number', $filters['search'])
                    ->orLike('suppliers.supplier_name', $filters['search'])
                    ->orLike('suppliers.contact_person', $filters['search'])
                    ->groupEnd();
        }
        
        if (!empty($filters['status'])) {
            $builder->where('purchase_orders.status', $filters['status']);
        }
        
        if (!empty($filters['supplier_id'])) {
            $builder->where('purchase_orders.supplier_id', $filters['supplier_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('purchase_orders.order_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('purchase_orders.order_date <=', $filters['date_to']);
        }
        
        // Order by
        $builder->orderBy('purchase_orders.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /** List or single PO with items for purchase hub controllers */
    public function getWithRelations($id = null)
    {
        if ($id !== null) {
            return $this->getPurchaseOrderWithItems($id);
        }

        return $this->getPurchaseOrders();
    }

    /**
     * Get purchase order with items
     */
    public function getPurchaseOrderWithItems($id)
    {
        $purchaseOrder = $this->select('purchase_orders.*, suppliers.supplier_name, suppliers.contact_person, suppliers.phone, suppliers.email, suppliers.address, suppliers.gst_number')
                             ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                             ->find($id);
        
        if (!$purchaseOrder) {
            return null;
        }

        // Get items
        $purchaseOrderItemModel = new PurchaseOrderItem();
        $purchaseOrder['items'] = $purchaseOrderItemModel->getItemsByPurchaseOrderId($id);

        return $purchaseOrder;
    }

    /**
     * Get purchase order statistics
     */
    public function getPurchaseOrderStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'draft' => $this->where('status', 'draft')->countAllResults(),
            'pending' => $this->where('status', 'pending')->countAllResults(),
            'approved' => $this->where('status', 'approved')->countAllResults(),
            'ordered' => $this->where('status', 'ordered')->countAllResults(),
            'received' => $this->where('status', 'received')->countAllResults(),
            'cancelled' => $this->where('status', 'cancelled')->countAllResults(),
            'total_amount' => $this->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'urgent' => $this->where('is_urgent', 1)->countAllResults()
        ];
        
        return $stats;
    }

    /**
     * Generate PO number
     */
    public function generatePONumber()
    {
        $prefix = 'PO';
        $year = date('Y');
        $month = date('m');
        
        // Get last PO number for current year/month
        $lastPO = $this->select('po_number')
                      ->like('po_number', $prefix . $year . $month)
                      ->orderBy('po_number', 'DESC')
                      ->first();
        
        if ($lastPO) {
            // Extract sequence number and increment
            $sequence = intval(substr($lastPO['po_number'], -4)) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get purchase orders by status
     */
    public function getPurchaseOrdersByStatus($status)
    {
        return $this->select('purchase_orders.*, suppliers.supplier_name')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                   ->where('purchase_orders.status', $status)
                   ->orderBy('purchase_orders.created_at', 'DESC')
                   ->findAll();
    }

    public function getPendingForGRN()
    {
        return $this->select('purchase_orders.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->whereIn('purchase_orders.status', ['approved', 'ordered', 'pending'])
            ->orderBy('purchase_orders.expected_date', 'ASC')
            ->findAll();
    }

    /**
     * Get overdue purchase orders
     */
    public function getOverduePurchaseOrders()
    {
        return $this->select('purchase_orders.*, suppliers.supplier_name')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                   ->where('purchase_orders.expected_date <', date('Y-m-d'))
                   ->whereIn('purchase_orders.status', ['ordered', 'approved'])
                   ->orderBy('purchase_orders.expected_date', 'ASC')
                   ->findAll();
    }

    /**
     * Get purchase orders by supplier
     */
    public function getPurchaseOrdersBySupplier($supplierId)
    {
        return $this->where('supplier_id', $supplierId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get purchase order summary by date range
     */
    public function getPurchaseOrderSummary($startDate, $endDate)
    {
        return $this->select('
                COUNT(*) as total_orders,
                SUM(total_amount) as total_value,
                AVG(total_amount) as avg_order_value,
                COUNT(CASE WHEN status = "received" THEN 1 END) as received_orders,
                COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_orders
            ')
            ->where('order_date >=', $startDate)
            ->where('order_date <=', $endDate)
            ->first();
    }

    /**
     * Get purchase orders for dashboard
     */
    public function getDashboardPurchaseOrders($limit = 10)
    {
        return $this->select('purchase_orders.*, suppliers.supplier_name')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                   ->orderBy('purchase_orders.created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Check if PO number exists
     */
    public function isPONumberExists($poNumber, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('po_number', $poNumber);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get purchase order by PO number
     */
    public function getPurchaseOrderByPONumber($poNumber)
    {
        return $this->select('purchase_orders.*, suppliers.supplier_name')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                   ->where('po_number', $poNumber)
                   ->first();
    }

    /**
     * Update purchase order status
     */
    public function updateStatus($id, $status, $userId = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add status-specific fields
        switch ($status) {
            case 'approved':
                $data['approved_by'] = $userId;
                $data['approved_at'] = date('Y-m-d H:i:s');
                break;
            case 'ordered':
                $data['ordered_by'] = $userId;
                $data['ordered_at'] = date('Y-m-d H:i:s');
                break;
            case 'received':
                $data['received_by'] = $userId;
                $data['received_at'] = date('Y-m-d H:i:s');
                break;
            case 'cancelled':
                $data['cancelled_by'] = $userId;
                $data['cancelled_at'] = date('Y-m-d H:i:s');
                break;
        }

        return $this->update($id, $data);
    }

    /**
     * Get purchase orders with item count
     */
    public function getPurchaseOrdersWithItemCount($filters = [])
    {
        $builder = $this->builder();
        
        $builder->select('purchase_orders.*, suppliers.supplier_name, suppliers.contact_person, COUNT(poi.id) as item_count')
                ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                ->join('purchase_order_items poi', 'poi.po_id = purchase_orders.id', 'left')
                ->groupBy('purchase_orders.id');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('po_number', $filters['search'])
                    ->orLike('suppliers.supplier_name', $filters['search'])
                    ->groupEnd();
        }
        
        if (!empty($filters['status'])) {
            $builder->where('purchase_orders.status', $filters['status']);
        }
        
        $builder->orderBy('purchase_orders.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get received purchase orders (for purchase returns)
     */
    public function getReceivedPurchaseOrders()
    {
        return $this->select('purchase_orders.*, suppliers.supplier_name, suppliers.contact_person')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                   ->where('purchase_orders.status', 'received')
                   ->orderBy('purchase_orders.order_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get purchase order items
     */
    public function getPurchaseOrderItems($purchaseOrderId)
    {
        $purchaseOrderItemModel = new PurchaseOrderItem();
        $items = $purchaseOrderItemModel->getItemsByPurchaseOrderId($purchaseOrderId);
        
        // Format items for purchase return view
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'] ?? '',
                'product_code' => $item['product_code'] ?? '',
                'original_quantity' => $item['quantity'] ?? 0,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_amount' => $item['total_amount'] ?? 0
            ];
        }
        
        return $formattedItems;
    }
}
