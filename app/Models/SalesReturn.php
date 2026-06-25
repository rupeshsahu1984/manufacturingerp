<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesReturn extends Model
{
    protected $table = 'sales_returns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'return_number',
        'invoice_id',
        'customer_id',
        'return_date',
        'return_reason',
        'subtotal',
        'gst_amount',
        'total_amount',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'return_number' => 'required|max_length[20]|is_unique[sales_returns.return_number,id,{id}]',
        'invoice_id' => 'required|integer',
        'customer_id' => 'required|integer',
        'return_date' => 'required|valid_date',
        'return_reason' => 'required|max_length[500]',
        'subtotal' => 'required|numeric',
        'gst_amount' => 'required|numeric',
        'total_amount' => 'required|numeric',
        'status' => 'required|in_list[draft,submitted,approved,processed,cancelled]'
    ];

    protected $validationMessages = [
        'return_number' => [
            'required' => 'Return number is required.',
            'max_length' => 'Return number cannot exceed 20 characters.',
            'is_unique' => 'Return number must be unique.'
        ],
        'invoice_id' => [
            'required' => 'Invoice is required.',
            'integer' => 'Invalid invoice selected.'
        ],
        'customer_id' => [
            'required' => 'Customer is required.',
            'integer' => 'Invalid customer selected.'
        ],
        'return_date' => [
            'required' => 'Return date is required.',
            'valid_date' => 'Invalid return date format.'
        ],
        'return_reason' => [
            'required' => 'Return reason is required.',
            'max_length' => 'Return reason cannot exceed 500 characters.'
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list' => 'Invalid status selected.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getSalesReturnsWithDetails($filters = [])
    {
        $builder = $this->db->table('sales_returns sr')
            ->select('sr.*, c.customer_name, c.email as customer_email, c.phone as customer_phone, i.invoice_number')
            ->join('customers c', 'c.id = sr.customer_id', 'left')
            ->join('invoices i', 'i.id = sr.invoice_id', 'left')
            ->orderBy('sr.created_at', 'DESC');

        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('sr.return_number', $filters['search'])
                ->orLike('c.customer_name', $filters['search'])
                ->orLike('i.invoice_number', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['customer'])) {
            $builder->where('sr.customer_id', $filters['customer']);
        }

        if (!empty($filters['status'])) {
            $builder->where('sr.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('sr.return_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('sr.return_date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }

    public function getSalesReturnWithDetails($id)
    {
        $salesReturn = $this->db->table('sales_returns sr')
            ->select('sr.*, c.customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address, i.invoice_number, i.invoice_date')
            ->join('customers c', 'c.id = sr.customer_id', 'left')
            ->join('invoices i', 'i.id = sr.invoice_id', 'left')
            ->where('sr.id', $id)
            ->get()
            ->getRowArray();

        if (!$salesReturn) {
            return null;
        }

        // Get sales return items
        $salesReturnItemModel = new \App\Models\SalesReturnItem();
        $salesReturn['items'] = $salesReturnItemModel->getItemsByReturnId($id);

        return $salesReturn;
    }

    public function getSalesReturnStats()
    {
        $total = $this->countAll();
        $pending = $this->builder()->where('status', 'submitted')->countAllResults();
        $approved = $this->builder()->where('status', 'approved')->countAllResults();
        $processed = $this->builder()->where('status', 'processed')->countAllResults();
        $cancelled = $this->builder()->where('status', 'cancelled')->countAllResults();
        $draft = $this->builder()->where('status', 'draft')->countAllResults();
        $result = $this->selectSum('total_amount')->first();
        $totalAmount = $result['total_amount'] ?? 0;

        return [
            'total' => $total,
            'total_returns' => $total,
            'pending' => $pending,
            'pending_returns' => $pending,
            'approved' => $approved,
            'approved_returns' => $approved,
            'processed' => $processed,
            'processed_returns' => $processed,
            'cancelled' => $cancelled,
            'cancelled_returns' => $cancelled,
            'draft' => $draft,
            'total_amount' => $totalAmount
        ];
    }

    public function generateUniqueReturnNumber()
    {
        $prefix = 'SR';
        $year = date('Y');
        $month = date('m');
        
        // Get the last return number for this month
        $lastReturn = $this->where('return_number LIKE', $prefix . $year . $month . '%')
            ->orderBy('return_number', 'DESC')
            ->first();
        
        if ($lastReturn) {
            $lastNumber = intval(substr($lastReturn['return_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getReturnsByCustomer($customer_id)
    {
        return $this->where('customer_id', $customer_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getReturnsByInvoice($invoice_id)
    {
        return $this->where('invoice_id', $invoice_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getReturnsByStatus($status)
    {
        return $this->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getReturnsByDateRange($start_date, $end_date)
    {
        return $this->where('return_date >=', $start_date)
            ->where('return_date <=', $end_date)
            ->orderBy('return_date', 'DESC')
            ->findAll();
    }

    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getReturnItems($return_id)
    {
        $builder = $this->db->table('sales_return_items sri')
            ->select('sri.*, p.product_name, p.product_code as product_sku, p.description as product_description')
            ->join('products p', 'p.id = sri.product_id', 'left')
            ->where('sri.return_id', $return_id);

        return $builder->get()->getResultArray();
    }
}
