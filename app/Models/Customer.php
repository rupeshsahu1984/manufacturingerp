<?php

namespace App\Models;

use CodeIgniter\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'customer_code',
        'customer_name',
        'contact_person',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'state',
        'pincode',
        'gst_number',
        'pan_number',
        'credit_limit',
        'payment_terms',
        'return_policy',
        'debit_note_config',
        'sales_zone',
        'sales_region',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'customer_name' => 'required|min_length[3]|max_length[255]',
        'contact_person' => 'required|min_length[2]|max_length[255]',
        'phone' => 'required|min_length[10]|max_length[15]',
        'email' => 'permit_empty|valid_email',
        'address' => 'required|min_length[5]',
        'city' => 'required|min_length[2]|max_length[100]',
        'state' => 'required|min_length[2]|max_length[100]',
        'pincode' => 'required|min_length[6]|max_length[10]',
        'credit_limit' => 'permit_empty|numeric',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'customer_name' => [
            'required' => 'Customer name is required',
            'min_length' => 'Customer name must be at least 3 characters long',
            'max_length' => 'Customer name cannot exceed 255 characters'
        ],
        'contact_person' => [
            'required' => 'Contact person is required',
            'min_length' => 'Contact person name must be at least 2 characters long'
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'min_length' => 'Phone number must be at least 10 digits',
            'max_length' => 'Phone number cannot exceed 15 digits'
        ],
        'email' => [
            'valid_email' => 'Please enter a valid email address'
        ],
        'address' => [
            'required' => 'Address is required',
            'min_length' => 'Address must be at least 5 characters long'
        ],
        'city' => [
            'required' => 'City is required',
            'min_length' => 'City name must be at least 2 characters long'
        ],
        'state' => [
            'required' => 'State is required',
            'min_length' => 'State name must be at least 2 characters long'
        ],
        'pincode' => [
            'required' => 'Pincode is required',
            'min_length' => 'Pincode must be at least 6 digits',
            'max_length' => 'Pincode cannot exceed 10 digits'
        ],
        'credit_limit' => [
            'numeric' => 'Credit limit must be a number'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique customer code
     */
    public function generateCustomerCode($customerName)
    {
        $prefix = strtoupper(preg_replace('/[^A-Z]/', '', $customerName));
        $prefix = substr($prefix, 0, 3);
        
        $lastCode = $this->select('customer_code')
            ->like('customer_code', $prefix, 'after')
            ->orderBy('customer_code', 'DESC')
            ->first();
        
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode['customer_code'], 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get customers with filters
     */
    public function getCustomers($filters = [])
    {
        $builder = $this->select('customers.*, u.full_name as created_by_name')
            ->join('users u', 'customers.created_by = u.id', 'left');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('customer_name', $search)
                ->orLike('customer_code', $search)
                ->orLike('contact_person', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->orLike('gst_number', $search)
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('customers.status', $filters['status']);
        }

        if (!empty($filters['sales_zone'])) {
            $builder->where('sales_zone', $filters['sales_zone']);
        }

        if (!empty($filters['sales_region'])) {
            $builder->where('sales_region', $filters['sales_region']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('customers.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('customers.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('customers.created_at', 'DESC')->findAll();
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'with_credit_limit' => $this->where('credit_limit >', 0)->countAllResults(),
            'total_credit_limit' => $this->selectSum('credit_limit')->first()['credit_limit'] ?? 0
        ];

        // Count by sales zone
        $zones = $this->select('sales_zone, COUNT(*) as count')
            ->where('sales_zone IS NOT NULL')
            ->groupBy('sales_zone')
            ->findAll();
        
        $stats['zones'] = $zones;

        // Count by sales region
        $regions = $this->select('sales_region, COUNT(*) as count')
            ->where('sales_region IS NOT NULL')
            ->groupBy('sales_region')
            ->findAll();
        
        $stats['regions'] = $regions;

        return $stats;
    }

    /**
     * Get customers by sales zone
     */
    public function getCustomersByZone($zone)
    {
        return $this->where('sales_zone', $zone)
            ->where('status', 'active')
            ->findAll();
    }

    /**
     * Get customers by sales region
     */
    public function getCustomersByRegion($region)
    {
        return $this->where('sales_region', $region)
            ->where('status', 'active')
            ->findAll();
    }

    /**
     * Get customer with complete history
     */
    public function getCustomerWithHistory($customerId)
    {
        $customer = $this->find($customerId);
        
        if (!$customer) {
            return null;
        }

        // Get sales orders
        $salesOrderModel = new \App\Models\SalesOrder();
        $customer['sales_orders'] = $salesOrderModel->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get invoices
        $invoiceModel = new \App\Models\Invoice();
        $customer['invoices'] = $invoiceModel->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get outstanding amount
        $customer['outstanding_amount'] = $this->getOutstandingAmount($customerId);

        return $customer;
    }

    /**
     * Get customer performance metrics
     */
    public function getCustomerPerformance($customerId)
    {
        $salesOrderModel = new \App\Models\SalesOrder();
        $invoiceModel = new \App\Models\Invoice();

        $totalOrders = $salesOrderModel->where('customer_id', $customerId)->countAllResults();
        $totalInvoices = $invoiceModel->where('customer_id', $customerId)->countAllResults();
        $totalAmount = $invoiceModel->selectSum('total_amount')
            ->where('customer_id', $customerId)
            ->first()['total_amount'] ?? 0;
        
        $outstandingAmount = $this->getOutstandingAmount($customerId);
        $paymentEfficiency = $totalAmount > 0 ? (($totalAmount - $outstandingAmount) / $totalAmount) * 100 : 0;

        return [
            'total_orders' => $totalOrders,
            'total_invoices' => $totalInvoices,
            'total_amount' => $totalAmount,
            'outstanding_amount' => $outstandingAmount,
            'payment_efficiency' => round($paymentEfficiency, 2)
        ];
    }

    /**
     * Get outstanding amount for customer
     */
    public function getOutstandingAmount($customerId)
    {
        $invoiceModel = new \App\Models\Invoice();
        
        $result = $invoiceModel->selectSum('total_amount')
            ->where('customer_id', $customerId)
            ->where('status', 'unpaid')
            ->first();
        
        return isset($result['total_amount']) ? $result['total_amount'] : 0;
    }

    /**
     * Get customers with outstanding payments
     */
    public function getCustomersWithOutstandingPayments()
    {
        $invoiceModel = new \App\Models\Invoice();
        
        return $this->select('customers.*, SUM(i.total_amount) as outstanding_amount')
            ->join('invoices i', 'customers.id = i.customer_id')
            ->where('i.status', 'unpaid')
            ->groupBy('customers.id')
            ->having('outstanding_amount >', 0)
            ->orderBy('outstanding_amount', 'DESC')
            ->findAll();
    }

    /**
     * Check if customer has outstanding payments
     */
    public function hasOutstandingPayments($customerId)
    {
        $outstandingAmount = $this->getOutstandingAmount($customerId);
        return $outstandingAmount > 0;
    }

    /**
     * Get customer summary
     */
    public function getCustomerSummary($customerId)
    {
        $customer = $this->find($customerId);
        
        if (!$customer) {
            return null;
        }

        $performance = $this->getCustomerPerformance($customerId);
        $outstandingAmount = $this->getOutstandingAmount($customerId);

        return [
            'customer' => $customer,
            'performance' => $performance,
            'outstanding_amount' => $outstandingAmount,
            'credit_utilization' => $customer['credit_limit'] > 0 ? 
                ($outstandingAmount / $customer['credit_limit']) * 100 : 0
        ];
    }

    /**
     * Get unique sales zones
     */
    public function getSalesZones()
    {
        return $this->select('DISTINCT sales_zone')
            ->where('sales_zone IS NOT NULL')
            ->where('sales_zone !=', '')
            ->findAll();
    }

    /**
     * Get unique sales regions
     */
    public function getSalesRegions()
    {
        return $this->select('DISTINCT sales_region')
            ->where('sales_region IS NOT NULL')
            ->where('sales_region !=', '')
            ->findAll();
    }

    /**
     * Get active customers
     */
    public function getActiveCustomers()
    {
        return $this->where('status', 'active')
            ->orderBy('customer_name', 'ASC')
            ->findAll();
    }

    /**
     * Get customers for dropdown/select
     */
    public function getCustomersForSelect()
    {
        return $this->select('id, customer_code, customer_name')
            ->where('status', 'active')
            ->orderBy('customer_name', 'ASC')
            ->findAll();
    }
} 