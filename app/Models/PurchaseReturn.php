<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_returns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'return_number',
        'purchase_order_id',
        'grn_id',
        'bill_id',
        'supplier_id',
        'return_date',
        'return_reason',
        'return_method',
        'return_instructions',
        'total_quantity',
        'total_amount',
        'subtotal',
        'tax_amount',
        'restocking_fee',
        'is_urgent',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'return_number' => 'permit_empty|max_length[50]|is_unique[purchase_returns.return_number,id,{id}]',
        'grn_id' => 'permit_empty|integer',
        'bill_id' => 'permit_empty|integer',
        'supplier_id' => 'required|integer',
        'return_date' => 'required|valid_date',
        'return_reason' => 'required|in_list[damaged,defective,wrong_item,quality_issue,expired,overstock,other,excess,damage,qc_fail]',
        'total_quantity' => 'permit_empty|decimal',
        'total_amount' => 'permit_empty|decimal',
        'status' => 'required|in_list[draft,approved,sent,received]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'return_number' => [
            'max_length' => 'Return Number cannot exceed 50 characters',
            'is_unique' => 'Return Number must be unique'
        ],
        'supplier_id' => [
            'required' => 'Supplier is required',
            'integer' => 'Supplier ID must be a valid integer'
        ],
        'return_date' => [
            'required' => 'Return date is required',
            'valid_date' => 'Return date must be a valid date'
        ],
        'return_reason' => [
            'required' => 'Return reason is required',
            'in_list' => 'Return reason must be one of: excess, damage, qc_fail, wrong_item'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: draft, approved, sent, received'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generatePrnNumber'];
    protected $beforeUpdate = [];

    /**
     * Generate return number (alias for generateUniquePrnNumber for backward compatibility)
     */
    public function generateReturnNumber()
    {
        return $this->generateUniquePrnNumber();
    }

    /**
     * Generate unique PRN number
     */
    protected function generatePrnNumber(array $data)
    {
        // Generate return_number if not provided
        if (!isset($data['data']['return_number']) || empty($data['data']['return_number'])) {
            $generatedNumber = $this->generateUniquePrnNumber();
            $data['data']['return_number'] = $generatedNumber;
        }
        return $data;
    }

    /**
     * Generate unique PRN number
     */
    public function generateUniquePrnNumber()
    {
        $prefix = 'PRN';
        $year = date('Y');
        $month = date('m');
        
        // Get the last number using return_number (the actual column name in database)
        $lastPrn = $this->builder()
            ->select('return_number')
            ->like('return_number', $prefix . $year . $month, 'after')
            ->orderBy('return_number', 'DESC')
            ->get()
            ->getRowArray();
        
        if ($lastPrn && isset($lastPrn['return_number'])) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastPrn['return_number'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get purchase return with supplier details
     */
    public function getPurchaseReturnWithSupplier($id)
    {
        return $this->builder()
            ->select('purchase_returns.*, suppliers.supplier_name, suppliers.contact_person, suppliers.email, suppliers.phone')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->where('purchase_returns.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get purchase return with items
     */
    public function getPurchaseReturnWithItems($id)
    {
        $purchaseReturn = $this->getPurchaseReturnWithSupplier($id);
        
        if (!$purchaseReturn) {
            return null;
        }

        // Get purchase return items
        $db = \Config\Database::connect();
        $builder = $db->table('purchase_return_items pri');
        $builder->select('pri.*, p.product_name, p.product_code, p.description as specifications');
        $builder->join('products p', 'p.id = pri.product_id');
        $builder->where('pri.purchase_return_id', $id);
        
        $purchaseReturn['items'] = $builder->get()->getResultArray();

        return $purchaseReturn;
    }

    /**
     * Get purchase returns by supplier
     */
    public function getPurchaseReturnsBySupplier($supplierId, $limit = null)
    {
        $query = $this->select('purchase_returns.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->where('purchase_returns.supplier_id', $supplierId)
            ->orderBy('purchase_returns.created_at', 'DESC');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->findAll();
    }

    /**
     * Get purchase returns by status
     */
    public function getPurchaseReturnsByStatus($status)
    {
        return $this->select('purchase_returns.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->where('purchase_returns.status', $status)
            ->orderBy('purchase_returns.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get purchase returns by reason
     */
    public function getPurchaseReturnsByReason($reason)
    {
        return $this->select('purchase_returns.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->where('purchase_returns.return_reason', $reason)
            ->orderBy('purchase_returns.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get purchase returns summary
     */
    public function getPurchaseReturnsSummary()
    {
        $db = \Config\Database::connect();
        
        // Total purchase returns
        $totalReturns = $this->countAllResults();
        
        // Returns by status
        $returnsByStatus = $this->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->findAll();
        
        // Returns by reason
        $returnsByReason = $this->select('return_reason, COUNT(*) as count')
            ->groupBy('return_reason')
            ->findAll();
        
        // Total amount
        $totalAmount = $this->selectSum('total_amount')->first()['total_amount'] ?? 0;
        
        // Recent returns
        $recentReturns = $this->select('purchase_returns.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->orderBy('purchase_returns.created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        return [
            'total_returns' => $totalReturns,
            'returns_by_status' => $returnsByStatus,
            'returns_by_reason' => $returnsByReason,
            'total_amount' => $totalAmount,
            'recent_returns' => $recentReturns
        ];
    }

    /**
     * Update purchase return status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Calculate totals for purchase return
     */
    public function calculateTotals($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('purchase_return_items');
        
        $result = $builder->selectSum('total_amount')
            ->where('purchase_return_id', $id)
            ->first();
        
        $totalAmount = isset($result['total_amount']) ? $result['total_amount'] : 0;
        
        return [
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Get purchase returns with filters
     */
    public function getPurchaseReturns($filters = [])
    {
        // Use the model's findAll first to get basic data, then add joins
        $purchaseReturns = $this->findAll();
        
        // Get additional details for each return
        $result = [];
        foreach ($purchaseReturns as $return) {
            // Get supplier info
            $supplier = $this->db->table('suppliers')
                ->select('supplier_name, contact_person')
                ->where('id', $return['supplier_id'])
                ->get()
                ->getRowArray();
            
            // Get purchase order info if grn_id exists
            $poNumber = null;
            $purchaseOrderId = null;
            if (!empty($return['grn_id'])) {
                $grn = $this->db->table('goods_receipt_notes')
                    ->select('purchase_order_id')
                    ->where('id', $return['grn_id'])
                    ->get()
                    ->getRowArray();
                
                if ($grn && !empty($grn['purchase_order_id'])) {
                    $po = $this->db->table('purchase_orders')
                        ->select('id, po_number')
                        ->where('id', $grn['purchase_order_id'])
                        ->get()
                        ->getRowArray();
                    
                    if ($po) {
                        $poNumber = $po['po_number'];
                        $purchaseOrderId = $po['id'];
                    }
                }
            }
            
            // Merge all data
            $result[] = array_merge($return, [
                'return_number' => $return['return_number'] ?? $return['prn_number'] ?? '',
                'supplier_name' => $supplier['supplier_name'] ?? '',
                'contact_person' => $supplier['contact_person'] ?? '',
                'po_number' => $poNumber ?? '',
                'purchase_order_id' => $purchaseOrderId ?? 0,
                'is_urgent' => $return['is_urgent'] ?? 0,
                'return_reason' => $return['return_reason'] ?? ''
            ]);
        }
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $result = array_filter($result, function($return) use ($search) {
                return stripos($return['return_number'], $search) !== false ||
                       stripos($return['supplier_name'], $search) !== false ||
                       stripos($return['po_number'], $search) !== false;
            });
        }
        
        if (!empty($filters['status'])) {
            $result = array_filter($result, function($return) use ($filters) {
                return $return['status'] == $filters['status'];
            });
        }
        
        if (!empty($filters['supplier_id'])) {
            $result = array_filter($result, function($return) use ($filters) {
                return $return['supplier_id'] == $filters['supplier_id'];
            });
        }
        
        if (!empty($filters['date_from'])) {
            $result = array_filter($result, function($return) use ($filters) {
                return $return['return_date'] >= $filters['date_from'];
            });
        }
        
        if (!empty($filters['date_to'])) {
            $result = array_filter($result, function($return) use ($filters) {
                return $return['return_date'] <= $filters['date_to'];
            });
        }
        
        // Sort by created_at descending
        usort($result, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_values($result);
    }

    /**
     * Get purchase return statistics
     */
    public function getPurchaseReturnStats()
    {
        $total = $this->countAll();
        $draft = $this->builder()->where('status', 'draft')->countAllResults();
        $approved = $this->builder()->where('status', 'approved')->countAllResults();
        $sent = $this->builder()->where('status', 'sent')->countAllResults();
        $received = $this->builder()->where('status', 'received')->countAllResults();
        $totalAmountResult = $this->builder()->selectSum('total_amount')->get()->getRowArray();
        $totalAmount = $totalAmountResult['total_amount'] ?? 0;

        // Map to view expectations (pending = sent, completed = received)
        return [
            'total' => $total,
            'pending' => $sent, // Map 'sent' to 'pending' for view compatibility
            'approved' => $approved,
            'processed' => $sent, // Map 'sent' to 'processed' for view compatibility
            'completed' => $received, // Map 'received' to 'completed' for view compatibility
            'draft' => $draft,
            'sent' => $sent,
            'received' => $received,
            'total_amount' => $totalAmount
        ];
    }

    public function getApprovedForDebitNote()
    {
        return $this->whereIn('status', ['approved', 'received', 'completed'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
