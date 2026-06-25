<?php

namespace App\Models;

use CodeIgniter\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'supplier_code',
        'supplier_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'gst_number',
        'pan_number',
        'bank_name',
        'bank_account',
        'bank_ifsc',
        'payment_terms',
        'credit_limit',
        'supplier_category',
        'return_policy',
        'credit_terms',
        'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'supplier_code' => 'required|max_length[20]|is_unique[suppliers.supplier_code,id,{id}]',
        'supplier_name' => 'required|max_length[100]',
        'email' => 'permit_empty|valid_email',
        'gst_number' => 'permit_empty|max_length[20]',
        'pan_number' => 'permit_empty|max_length[20]',
        'supplier_category' => 'required|in_list[raw_material,packaging,service]'
    ];

    protected $validationMessages = [
        'supplier_code' => [
            'required' => 'Supplier code is required',
            'is_unique' => 'Supplier code already exists'
        ],
        'supplier_name' => [
            'required' => 'Supplier name is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique supplier code
     */
    public function generateSupplierCode()
    {
        $prefix = 'SUP';
        $year = date('Y');
        $month = date('m');
        
        // Get the last supplier code for this month
        $lastCode = $this->select('supplier_code')
            ->like('supplier_code', $prefix . $year . $month)
            ->orderBy('supplier_code', 'DESC')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode['supplier_code'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get suppliers with filters
     */
    public function getSuppliers($filters = [])
    {
        $builder = $this->select('*');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('supplier_name', $filters['search'])
                ->orLike('supplier_code', $filters['search'])
                ->orLike('contact_person', $filters['search'])
                ->orLike('email', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['category'])) {
            $builder->where('supplier_category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return $builder->orderBy('supplier_name', 'ASC')->findAll();
    }

    /**
     * Get supplier statistics
     */
    public function getSupplierStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'raw_material' => $this->where('supplier_category', 'raw_material')->countAllResults(),
            'packaging' => $this->where('supplier_category', 'packaging')->countAllResults(),
            'service' => $this->where('supplier_category', 'service')->countAllResults()
        ];

        return $stats;
    }

    /**
     * Get suppliers by category
     */
    public function getSuppliersByCategory($category)
    {
        return $this->where('supplier_category', $category)
            ->where('status', 'active')
            ->orderBy('supplier_name', 'ASC')
            ->findAll();
    }

    /**
     * Get supplier with purchase history
     */
    public function getSupplierWithHistory($id)
    {
        $supplier = $this->find($id);
        
        if (!$supplier) {
            return null;
        }

        // Initialize empty arrays for related data
        $supplier['purchase_orders'] = [];
        $supplier['purchase_bills'] = [];
        $supplier['purchase_returns'] = [];

        // Try to get purchase orders if model exists
        try {
            if (class_exists('\App\Models\PurchaseOrder')) {
                $purchaseOrderModel = new \App\Models\PurchaseOrder();
                $supplier['purchase_orders'] = $purchaseOrderModel->where('supplier_id', $id)->findAll();
            }
        } catch (Exception $e) {
            // Model doesn't exist or table doesn't exist
        }

        // Try to get purchase bills if model exists
        try {
            if (class_exists('\App\Models\PurchaseBill')) {
                $purchaseBillModel = new \App\Models\PurchaseBill();
                $supplier['purchase_bills'] = $purchaseBillModel->where('supplier_id', $id)->findAll();
            }
        } catch (Exception $e) {
            // Model doesn't exist or table doesn't exist
        }

        // Try to get purchase returns if model exists
        try {
            if (class_exists('\App\Models\PurchaseReturn')) {
                $purchaseReturnModel = new \App\Models\PurchaseReturn();
                $supplier['purchase_returns'] = $purchaseReturnModel->where('supplier_id', $id)->findAll();
            }
        } catch (Exception $e) {
            // Model doesn't exist or table doesn't exist
        }

        return $supplier;
    }

    /**
     * Get supplier performance metrics
     */
    public function getSupplierPerformance($id)
    {
        $purchaseOrderModel = new \App\Models\PurchaseOrder();
        $purchaseBillModel = new \App\Models\PurchaseBill();
        $purchaseReturnModel = new \App\Models\PurchaseReturn();

        $metrics = [
            'total_orders' => $purchaseOrderModel->where('supplier_id', $id)->countAllResults(),
            'total_bills' => $purchaseBillModel->where('supplier_id', $id)->countAllResults(),
            'total_returns' => $purchaseReturnModel->where('supplier_id', $id)->countAllResults(),
            'total_amount' => $purchaseBillModel->selectSum('total_amount')
                ->where('supplier_id', $id)
                ->where('status !=', 'cancelled')
                ->first()['total_amount'] ?? 0,
            'paid_amount' => $purchaseBillModel->selectSum('paid_amount')
                ->where('supplier_id', $id)
                ->where('status !=', 'cancelled')
                ->first()['paid_amount'] ?? 0
        ];

        $metrics['outstanding_amount'] = $metrics['total_amount'] - $metrics['paid_amount'];
        $metrics['payment_percentage'] = $metrics['total_amount'] > 0 ? 
            ($metrics['paid_amount'] / $metrics['total_amount']) * 100 : 0;

        return $metrics;
    }

    /**
     * Check if supplier has outstanding payments
     */
    public function hasOutstandingPayments($id)
    {
        $purchaseBillModel = new \App\Models\PurchaseBill();
        $outstanding = $purchaseBillModel->selectSum('total_amount')
            ->where('supplier_id', $id)
            ->where('status', 'received')
            ->first()['total_amount'] ?? 0;

        $paid = $purchaseBillModel->selectSum('paid_amount')
            ->where('supplier_id', $id)
            ->where('status', 'received')
            ->first()['paid_amount'] ?? 0;

        return ($outstanding - $paid) > 0;
    }

    /**
     * Get suppliers with outstanding payments
     */
    public function getSuppliersWithOutstandingPayments()
    {
        $purchaseBillModel = new \App\Models\PurchaseBill();
        
        return $this->select('suppliers.*, 
            COALESCE(SUM(pb.total_amount), 0) as total_amount,
            COALESCE(SUM(pb.paid_amount), 0) as paid_amount,
            COALESCE(SUM(pb.total_amount) - COALESCE(SUM(pb.paid_amount), 0), 0) as outstanding_amount')
            ->join('purchase_bills pb', 'suppliers.id = pb.supplier_id', 'left')
            ->where('pb.status', 'received')
            ->groupBy('suppliers.id')
            ->having('outstanding_amount >', 0)
            ->orderBy('outstanding_amount', 'DESC')
            ->findAll();
    }

    /**
     * Get active suppliers
     */
    public function getActiveSuppliers()
    {
        return $this->where('status', 'active')
            ->orderBy('supplier_name', 'ASC')
            ->findAll();
    }

    /**
     * Get suppliers for dropdown/select
     */
    public function getSuppliersForSelect()
    {
        return $this->select('id, supplier_code, supplier_name')
            ->where('status', 'active')
            ->orderBy('supplier_name', 'ASC')
            ->findAll();
    }

    /** @deprecated Use getActiveSuppliers — kept for controllers expecting this name */
    public function getAllActive(): array
    {
        return $this->getActiveSuppliers();
    }

    /** Purchase hub / views expect this name */
    public function getWithRelations($id = null)
    {
        if ($id !== null) {
            $row = $this->find($id);
            return $row ? $this->normalizeSupplierRow($row) : null;
        }

        $rows = $this->orderBy('supplier_name', 'ASC')->findAll();
        foreach ($rows as $i => $row) {
            $rows[$i] = $this->normalizeSupplierRow($row);
        }

        return $rows;
    }

    /**
     * Map DB columns to keys expected by purchase views (category, city, state, rating).
     */
    private function normalizeSupplierRow(array $row): array
    {
        if (! isset($row['category']) && isset($row['supplier_category'])) {
            $row['category'] = $row['supplier_category'];
        }
        $row['category'] = $row['category'] ?? 'other';
        $row['city'] = $row['city'] ?? '';
        $row['state'] = $row['state'] ?? '';
        $row['rating'] = isset($row['rating']) ? (float) $row['rating'] : 0.0;

        return $row;
    }
} 