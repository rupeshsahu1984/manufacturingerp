<?php

namespace App\Models;

use CodeIgniter\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invoice_number',
        'so_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'gst_amount',
        'total_amount',
        'paid_amount',
        'status',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'invoice_number' => 'required|max_length[20]|is_unique[invoices.invoice_number,id,{id}]',
        'so_id' => 'permit_empty|integer',
        'customer_id' => 'required|integer',
        'invoice_date' => 'required|valid_date',
        'due_date' => 'permit_empty|valid_date',
        'subtotal' => 'permit_empty|decimal',
        'gst_amount' => 'permit_empty|decimal',
        'total_amount' => 'permit_empty|decimal',
        'paid_amount' => 'permit_empty|decimal',
        'status' => 'required|in_list[draft,sent,paid,overdue,cancelled]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'invoice_number' => [
            'required' => 'Invoice Number is required',
            'max_length' => 'Invoice Number cannot exceed 20 characters',
            'is_unique' => 'Invoice Number must be unique'
        ],
        'customer_id' => [
            'required' => 'Customer is required',
            'integer' => 'Customer ID must be a valid integer'
        ],
        'invoice_date' => [
            'required' => 'Invoice date is required',
            'valid_date' => 'Invoice date must be a valid date'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: draft, sent, paid, overdue, cancelled'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateInvoiceNumber'];
    protected $beforeUpdate = [];

    /**
     * Generate unique invoice number
     */
    protected function generateInvoiceNumber(array $data)
    {
        if (!isset($data['data']['invoice_number']) || empty($data['data']['invoice_number'])) {
            $data['data']['invoice_number'] = $this->generateUniqueInvoiceNumber();
        }
        return $data;
    }

    /**
     * Generate unique invoice number
     */
    public function generateUniqueInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        // Get the last invoice number for this year/month
        $lastInvoice = $this->select('invoice_number')
            ->like('invoice_number', $prefix . $year . $month, 'after')
            ->orderBy('invoice_number', 'DESC')
            ->first();
        
        if ($lastInvoice) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastInvoice['invoice_number'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get invoice with customer details
     */
    public function getInvoiceWithCustomer($id)
    {
        return $this->select('invoices.*, customers.customer_name, customers.contact_person, customers.email, customers.phone')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->where('invoices.id', $id)
            ->first();
    }

    /**
     * Get invoice with items
     */
    public function getInvoiceWithItems($id)
    {
        $invoice = $this->getInvoiceWithCustomer($id);
        
        if (!$invoice) {
            return null;
        }

        // Get invoice items
        $db = \Config\Database::connect();
        $builder = $db->table('invoice_items ii');
        $builder->select('ii.*, p.product_name, p.product_code');
        $builder->join('products p', 'p.id = ii.product_id', 'left');
        $builder->where('ii.invoice_id', $id);
        
        $invoice['items'] = $builder->get()->getResultArray();

        return $invoice;
    }

    /**
     * Get invoice with details (alias for getInvoiceWithItems for compatibility)
     */
    public function getInvoiceWithDetails($id)
    {
        return $this->getInvoiceWithItems($id);
    }

    /**
     * Get invoices by customer
     */
    public function getInvoicesByCustomer($customerId, $limit = null)
    {
        $query = $this->select('invoices.*, customers.customer_name')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->where('invoices.customer_id', $customerId)
            ->orderBy('invoices.created_at', 'DESC');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->findAll();
    }

    /**
     * Get invoices by status
     */
    public function getInvoicesByStatus($status)
    {
        return $this->select('invoices.*, customers.customer_name')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->where('invoices.status', $status)
            ->orderBy('invoices.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices()
    {
        return $this->select('invoices.*, customers.customer_name')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->where('invoices.due_date <', date('Y-m-d'))
            ->where('invoices.status !=', 'paid')
            ->where('invoices.status !=', 'cancelled')
            ->orderBy('invoices.due_date', 'ASC')
            ->findAll();
    }

    /**
     * Get invoices summary
     */
    public function getInvoicesSummary()
    {
        $db = \Config\Database::connect();
        
        // Total invoices
        $totalInvoices = $this->countAllResults();
        
        // Invoices by status
        $invoicesByStatus = $this->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->findAll();
        
        // Total amount
        $totalAmount = $this->selectSum('total_amount')->first()['total_amount'] ?? 0;
        
        // Total paid amount
        $totalPaidAmount = $this->selectSum('paid_amount')->first()['paid_amount'] ?? 0;
        
        // Outstanding amount
        $outstandingAmount = $totalAmount - $totalPaidAmount;
        
        // Recent invoices
        $recentInvoices = $this->select('invoices.*, customers.customer_name')
            ->join('customers', 'customers.id = invoices.customer_id')
            ->orderBy('invoices.created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        return [
            'total_invoices' => $totalInvoices,
            'invoices_by_status' => $invoicesByStatus,
            'total_amount' => $totalAmount,
            'total_paid_amount' => $totalPaidAmount,
            'outstanding_amount' => $outstandingAmount,
            'recent_invoices' => $recentInvoices
        ];
    }

    /**
     * Update invoice status
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
        $invoice = $this->find($id);
        
        if (!$invoice) {
            return false;
        }
        
        $newPaidAmount = $invoice['paid_amount'] + $amount;
        $status = $newPaidAmount >= $invoice['total_amount'] ? 'paid' : 'sent';
        
        return $this->update($id, [
            'paid_amount' => $newPaidAmount,
            'status' => $status
        ]);
    }

    /**
     * Calculate totals for invoice
     */
    public function calculateTotals($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('invoice_items');
        
        $result = $builder->selectSum('total_amount')
            ->where('invoice_id', $id)
            ->first();
        
        $subtotal = isset($result['total_amount']) ? $result['total_amount'] : 0;
        $gstAmount = $subtotal * 0.18; // 18% GST
        $totalAmount = $subtotal + $gstAmount;
        
        return [
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Get outstanding amount for customer
     */
    public function getOutstandingAmountByCustomer($customerId)
    {
        $result = $this->selectSum('total_amount')
            ->where('customer_id', $customerId)
            ->where('status !=', 'paid')
            ->where('status !=', 'cancelled')
            ->first();
        
        return isset($result['total_amount']) ? $result['total_amount'] : 0;
    }

    /**
     * Get invoices with details and filters
     */
    public function getInvoicesWithDetails($filters = [])
    {
        $builder = $this->select('invoices.*, customers.customer_name')
                       ->join('customers', 'customers.id = invoices.customer_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('invoices.invoice_number', $filters['search'])
                   ->orLike('customers.customer_name', $filters['search'])
                   ->groupEnd();
        }
        
        if (!empty($filters['customer'])) {
            $builder->where('invoices.customer_id', $filters['customer']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('invoices.status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('invoices.invoice_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('invoices.invoice_date <=', $filters['date_to']);
        }
        
        // Order by
        $builder->orderBy('invoices.created_at', 'DESC');
        
        return $builder->findAll();
    }

    /**
     * Get invoice statistics
     */
    public function getInvoiceStats()
    {
        $total = $this->countAll();
        $paid = $this->where('status', 'paid')->countAllResults();
        $overdue = $this->where('status', 'overdue')->countAllResults();
        $total_amount = $this->selectSum('total_amount')->first()['total_amount'] ?? 0;

        return [
            'total' => $total,
            'paid' => $paid,
            'overdue' => $overdue,
            'total_amount' => $total_amount
        ];
    }

    /**
     * Get recent invoices
     */
    public function getRecentInvoices($limit = 5)
    {
        return $this->select('invoices.*, customers.customer_name')
                   ->join('customers', 'customers.id = invoices.customer_id', 'left')
                   ->orderBy('invoices.created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get delivered invoices (for sales returns)
     */
    public function getDeliveredInvoices()
    {
        return $this->select('invoices.*, customers.customer_name')
                   ->join('customers', 'customers.id = invoices.customer_id', 'left')
                   ->where('invoices.status', 'paid')
                   ->orderBy('invoices.invoice_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get outstanding invoices (for customer payments)
     */
    public function getOutstandingInvoices()
    {
        return $this->select('invoices.*, customers.customer_name')
                   ->join('customers', 'customers.id = invoices.customer_id', 'left')
                   ->where('invoices.status !=', 'paid')
                   ->where('invoices.status !=', 'cancelled')
                   ->orderBy('invoices.invoice_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get outstanding invoices by customer
     */
    public function getOutstandingInvoicesByCustomer($customer_id)
    {
        return $this->select('invoices.*, customers.customer_name')
                   ->join('customers', 'customers.id = invoices.customer_id', 'left')
                   ->where('invoices.customer_id', $customer_id)
                   ->where('invoices.status !=', 'paid')
                   ->where('invoices.status !=', 'cancelled')
                   ->orderBy('invoices.invoice_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get confirmed sales orders (for invoice creation)
     */
    public function getConfirmedSalesOrders()
    {
        $builder = $this->db->table('sales_orders so')
            ->select('so.*, c.customer_name')
            ->join('customers c', 'c.id = so.customer_id', 'left')
            ->where('so.status', 'confirmed')
            ->orderBy('so.order_date', 'DESC');

        return $builder->get()->getResultArray();
    }
}
