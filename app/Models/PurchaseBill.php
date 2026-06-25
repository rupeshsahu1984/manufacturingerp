<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseBill extends Model
{
    protected $table = 'purchase_bills';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bill_number',
        'po_id',
        'supplier_id',
        'bill_date',
        'due_date',
        'invoice_number',
        'subtotal',
        'gst_amount',
        'total_amount',
        'paid_amount',
        'status',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'bill_number' => 'required|max_length[20]|is_unique[purchase_bills.bill_number,id,{id}]',
        'supplier_id' => 'required|integer',
        'bill_date' => 'required|valid_date',
        'total_amount' => 'required|numeric'
    ];

    protected $validationMessages = [
        'bill_number' => [
            'required' => 'Bill number is required',
            'is_unique' => 'Bill number already exists'
        ],
        'supplier_id' => [
            'required' => 'Supplier is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique bill number
     */
    public function generateBillNumber()
    {
        $prefix = 'BILL';
        $year = date('Y');
        $month = date('m');
        
        // Get the last bill number for this month
        $lastCode = $this->select('bill_number')
            ->like('bill_number', $prefix . $year . $month)
            ->orderBy('bill_number', 'DESC')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode['bill_number'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get purchase bills with filters
     */
    public function getPurchaseBills($filters = [])
    {
        $builder = $this->select('pb.id, pb.bill_number, pb.po_id, pb.supplier_id, pb.bill_date, pb.due_date, pb.invoice_number, pb.subtotal, pb.gst_amount, pb.total_amount, pb.paid_amount, pb.status, pb.created_by, pb.created_at, pb.updated_at, s.supplier_name, s.supplier_code')
            ->from('purchase_bills pb')
            ->join('suppliers s', 'pb.supplier_id = s.id', 'left')
            ->groupBy('pb.id');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('pb.bill_number', $filters['search'])
                ->orLike('pb.invoice_number', $filters['search'])
                ->orLike('s.supplier_name', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['supplier_id'])) {
            $builder->where('pb.supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('pb.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('pb.bill_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('pb.bill_date <=', $filters['date_to']);
        }

        return $builder->orderBy('pb.bill_date', 'DESC')
            ->orderBy('pb.id', 'DESC')
            ->findAll();
    }

    /**
     * Get purchase bill statistics
     */
    public function getBillStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'draft' => $this->where('status', 'draft')->countAllResults(),
            'received' => $this->where('status', 'received')->countAllResults(),
            'paid' => $this->where('status', 'paid')->countAllResults(),
            'overdue' => $this->where('status', 'overdue')->countAllResults(),
            'total_amount' => $this->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'paid_amount' => $this->selectSum('paid_amount')->first()['paid_amount'] ?? 0
        ];

        $stats['outstanding_amount'] = $stats['total_amount'] - $stats['paid_amount'];

        return $stats;
    }

    /**
     * Get purchase bill with items
     */
    public function getPurchaseBillWithItems($id)
    {
        $bill = $this->select('pb.*, s.supplier_name, s.supplier_code, s.contact_person, s.email, s.phone, s.address')
            ->from('purchase_bills pb')
            ->join('suppliers s', 'pb.supplier_id = s.id')
            ->where('pb.id', $id)
            ->first();

        if (!$bill) {
            return null;
        }

        // Get bill items
        $billItemModel = new PurchaseBillItem();
        $bill['items'] = $billItemModel->getBillItems($id);

        return $bill;
    }

    /**
     * Create purchase bill with items
     */
    public function createBillWithItems($billData, $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert bill
            $billId = $this->insert($billData);

            if (!$billId) {
                throw new \Exception('Failed to create purchase bill');
            }

            // Insert bill items
            $billItemModel = new PurchaseBillItem();
            foreach ($items as $item) {
                $item['bill_id'] = $billId;
                if (!$billItemModel->insert($item)) {
                    throw new \Exception('Failed to create bill item');
                }
            }

            $db->transComplete();
            return $db->transStatus() ? $billId : false;

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Update purchase bill with items
     */
    public function updateBillWithItems($id, $billData, $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update bill
            if (!$this->update($id, $billData)) {
                throw new \Exception('Failed to update purchase bill');
            }

            // Delete existing items
            $billItemModel = new PurchaseBillItem();
            $billItemModel->where('bill_id', $id)->delete();

            // Insert new items
            foreach ($items as $item) {
                $item['bill_id'] = $id;
                if (!$billItemModel->insert($item)) {
                    throw new \Exception('Failed to create bill item');
                }
            }

            $db->transComplete();
            return $db->transStatus();

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Update bill status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Record payment
     */
    public function recordPayment($id, $amount)
    {
        $bill = $this->find($id);
        
        if (!$bill) {
            return false;
        }

        $newPaidAmount = $bill['paid_amount'] + $amount;
        $newStatus = $newPaidAmount >= $bill['total_amount'] ? 'paid' : 'received';

        return $this->update($id, [
            'paid_amount' => $newPaidAmount,
            'status' => $newStatus
        ]);
    }

    /**
     * Get overdue bills
     */
    public function getOverdueBills()
    {
        return $this->select('pb.*, s.supplier_name, s.supplier_code')
            ->from('purchase_bills pb')
            ->join('suppliers s', 'pb.supplier_id = s.id')
            ->where('pb.due_date <', date('Y-m-d'))
            ->whereIn('pb.status', ['received', 'draft'])
            ->orderBy('pb.due_date', 'ASC')
            ->findAll();
    }

    /**
     * Get bills by supplier
     */
    public function getBillsBySupplier($supplierId)
    {
        return $this->where('supplier_id', $supplierId)
            ->orderBy('bill_date', 'DESC')
            ->findAll();
    }

    /**
     * Get outstanding amount by supplier
     */
    public function getOutstandingBySupplier($supplierId)
    {
        $result = $this->selectSum('total_amount')
            ->selectSum('paid_amount')
            ->where('supplier_id', $supplierId)
            ->where('status !=', 'cancelled')
            ->first();

        $totalAmount = isset($result['total_amount']) ? $result['total_amount'] : 0;
        $paidAmount = isset($result['paid_amount']) ? $result['paid_amount'] : 0;

        return $totalAmount - $paidAmount;
    }

    /**
     * Check if bill can be paid
     */
    public function canPayBill($id)
    {
        $bill = $this->find($id);
        
        if (!$bill) {
            return false;
        }

        return in_array($bill['status'], ['received', 'draft']) && 
               $bill['paid_amount'] < $bill['total_amount'];
    }

    /**
     * Get bill summary for dashboard
     */
    public function getBillSummary()
    {
        $summary = [
            'total_bills' => $this->countAll(),
            'total_amount' => $this->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'paid_amount' => $this->selectSum('paid_amount')->first()['paid_amount'] ?? 0,
            'overdue_count' => $this->where('due_date <', date('Y-m-d'))
                ->whereIn('status', ['received', 'draft'])
                ->countAllResults()
        ];

        $summary['outstanding_amount'] = $summary['total_amount'] - $summary['paid_amount'];
        $summary['payment_percentage'] = $summary['total_amount'] > 0 ? 
            ($summary['paid_amount'] / $summary['total_amount']) * 100 : 0;

        return $summary;
    }

    /**
     * Get recent bills
     */
    public function getRecentBills($limit = 5)
    {
        return $this->select('pb.*, s.supplier_name')
            ->from('purchase_bills pb')
            ->join('suppliers s', 'pb.supplier_id = s.id')
            ->orderBy('pb.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get bills with details and filters
     */
    public function getBillsWithDetails($filters = [])
    {
        $builder = $this->select('pb.*, s.supplier_name')
            ->from('purchase_bills pb')
            ->join('suppliers s', 'pb.supplier_id = s.id');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('pb.bill_number', $filters['search'])
                ->orLike('s.supplier_name', $filters['search'])
                ->groupEnd();
        }
        
        if (!empty($filters['supplier'])) {
            $builder->where('pb.supplier_id', $filters['supplier']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('pb.status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('pb.bill_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('pb.bill_date <=', $filters['date_to']);
        }
        
        // Order by
        $builder->orderBy('pb.created_at', 'DESC');
        
        return $builder->findAll();
    }

} 