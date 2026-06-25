<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerPayment extends Model
{
    protected $table = 'customer_payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'payment_number',
        'customer_id',
        'invoice_id',
        'payment_date',
        'payment_amount',
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'payment_number' => 'required|max_length[20]|is_unique[customer_payments.payment_number,id,{id}]',
        'customer_id' => 'required|integer',
        'invoice_id' => 'permit_empty|integer',
        'payment_date' => 'required|valid_date',
        'payment_amount' => 'required|numeric|greater_than[0]',
        'payment_method' => 'required|in_list[cash,bank_transfer,cheque,credit_card,online]',
        'reference_number' => 'permit_empty|max_length[50]',
        'notes' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'payment_number' => [
            'required' => 'Payment number is required.',
            'max_length' => 'Payment number cannot exceed 20 characters.',
            'is_unique' => 'Payment number must be unique.'
        ],
        'customer_id' => [
            'required' => 'Customer is required.',
            'integer' => 'Invalid customer selected.'
        ],
        'invoice_id' => [
            'integer' => 'Invalid invoice selected.'
        ],
        'payment_date' => [
            'required' => 'Payment date is required.',
            'valid_date' => 'Invalid payment date format.'
        ],
        'payment_amount' => [
            'required' => 'Payment amount is required.',
            'numeric' => 'Payment amount must be a number.',
            'greater_than' => 'Payment amount must be greater than 0.'
        ],
        'payment_method' => [
            'required' => 'Payment method is required.',
            'in_list' => 'Invalid payment method selected.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getPaymentsWithDetails($filters = [])
    {
        $builder = $this->db->table('customer_payments cp')
            ->select('cp.*, c.customer_name, c.email as customer_email, c.phone as customer_phone, i.invoice_number')
            ->join('customers c', 'c.id = cp.customer_id', 'left')
            ->join('invoices i', 'i.id = cp.invoice_id', 'left')
            ->orderBy('cp.created_at', 'DESC');

        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('cp.payment_number', $filters['search'])
                ->orLike('c.customer_name', $filters['search'])
                ->orLike('i.invoice_number', $filters['search'])
                ->orLike('cp.reference_number', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['customer'])) {
            $builder->where('cp.customer_id', $filters['customer']);
        }

        if (!empty($filters['payment_method'])) {
            $builder->where('cp.payment_method', $filters['payment_method']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('cp.payment_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('cp.payment_date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }

    public function getPaymentWithDetails($id)
    {
        $builder = $this->db->table('customer_payments cp')
            ->select('cp.*, c.customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address, i.invoice_number, i.invoice_date, i.total_amount as invoice_total, i.paid_amount as invoice_paid')
            ->join('customers c', 'c.id = cp.customer_id', 'left')
            ->join('invoices i', 'i.id = cp.invoice_id', 'left')
            ->where('cp.id', $id);

        return $builder->get()->getRowArray();
    }

    public function getPaymentStats()
    {
        $stats = [
            'total_payments' => $this->countAll(),
            'total_amount' => $this->selectSum('payment_amount')->first()['payment_amount'] ?? 0,
            'cash_payments' => $this->where('payment_method', 'cash')->countAllResults(),
            'bank_transfer_payments' => $this->where('payment_method', 'bank_transfer')->countAllResults(),
            'cheque_payments' => $this->where('payment_method', 'cheque')->countAllResults(),
            'credit_card_payments' => $this->where('payment_method', 'credit_card')->countAllResults(),
            'online_payments' => $this->where('payment_method', 'online')->countAllResults()
        ];

        return $stats;
    }

    public function generateUniquePaymentNumber()
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');
        
        // Get the last payment number for this month
        $lastPayment = $this->where('payment_number LIKE', $prefix . $year . $month . '%')
            ->orderBy('payment_number', 'DESC')
            ->first();
        
        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment['payment_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getPaymentsByCustomer($customer_id)
    {
        return $this->where('customer_id', $customer_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getPaymentsByInvoice($invoice_id)
    {
        return $this->where('invoice_id', $invoice_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getPaymentsByMethod($payment_method)
    {
        return $this->where('payment_method', $payment_method)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getPaymentsByDateRange($start_date, $end_date)
    {
        return $this->where('payment_date >=', $start_date)
            ->where('payment_date <=', $end_date)
            ->orderBy('payment_date', 'DESC')
            ->findAll();
    }

    public function getTotalAmountByCustomer($customer_id, $start_date = null, $end_date = null)
    {
        $builder = $this->selectSum('payment_amount')
            ->where('customer_id', $customer_id);

        if ($start_date) {
            $builder->where('payment_date >=', $start_date);
        }

        if ($end_date) {
            $builder->where('payment_date <=', $end_date);
        }

        $result = $builder->first();
        return isset($result['payment_amount']) ? $result['payment_amount'] : 0;
    }

    public function getTotalAmountByMethod($payment_method, $start_date = null, $end_date = null)
    {
        $builder = $this->selectSum('payment_amount')
            ->where('payment_method', $payment_method);

        if ($start_date) {
            $builder->where('payment_date >=', $start_date);
        }

        if ($end_date) {
            $builder->where('payment_date <=', $end_date);
        }

        $result = $builder->first();
        return isset($result['payment_amount']) ? $result['payment_amount'] : 0;
    }
}
