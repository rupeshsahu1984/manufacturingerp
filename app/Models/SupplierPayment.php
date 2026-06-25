<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierPayment extends Model
{
    protected $table = 'supplier_payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'payment_number',
        'invoice_id',
        'supplier_id',
        'payment_date',
        'payment_amount',
        'payment_method',
        'reference_number',
        'bank_name',
        'account_number',
        'cheque_number',
        'payment_notes',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'payment_number' => 'required|is_unique[supplier_payments.payment_number,id,{id}]',
        'invoice_id' => 'required|integer',
        'supplier_id' => 'required|integer',
        'payment_date' => 'required|valid_date',
        'payment_amount' => 'required|numeric|greater_than[0]',
        'payment_method' => 'required|in_list[cash,bank_transfer,cheque,online,other]',
        'status' => 'required|in_list[pending,completed,failed,cancelled]'
    ];

    protected $validationMessages = [
        'payment_number' => [
            'required' => 'Payment number is required',
            'is_unique' => 'Payment number must be unique'
        ],
        'payment_amount' => [
            'required' => 'Payment amount is required',
            'numeric' => 'Payment amount must be a number',
            'greater_than' => 'Payment amount must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function invoice()
    {
        return $this->belongsTo('App\Models\SupplierInvoice', 'invoice_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('supplier_payments.*, supplier_invoices.invoice_number, suppliers.supplier_name, suppliers.supplier_code')
                        ->join('supplier_invoices', 'supplier_invoices.id = supplier_payments.invoice_id')
                        ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id');

        if ($id) {
            return $builder->where('supplier_payments.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getAllWithRelations()
    {
        return $this->select('supplier_payments.*, supplier_invoices.invoice_number, suppliers.supplier_name, suppliers.supplier_code')
                    ->join('supplier_invoices', 'supplier_invoices.id = supplier_payments.invoice_id')
                    ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id')
                    ->orderBy('supplier_payments.payment_date', 'DESC')
                    ->findAll();
    }

    public function getByInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }

    public function getBySupplier($supplierId)
    {
        return $this->select('supplier_payments.*, supplier_invoices.invoice_number')
                    ->join('supplier_invoices', 'supplier_invoices.id = supplier_payments.invoice_id')
                    ->where('supplier_payments.supplier_id', $supplierId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('payment_date >=', $startDate)
                    ->where('payment_date <=', $endDate)
                    ->orderBy('payment_date', 'ASC')
                    ->findAll();
    }

    public function getPaymentHistory($invoiceId)
    {
        return $this->select('supplier_payments.*, suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id')
                    ->where('invoice_id', $invoiceId)
                    ->orderBy('payment_date', 'ASC')
                    ->findAll();
    }

    public function getTotalPaidByInvoice($invoiceId)
    {
        $result = $this->selectSum('payment_amount')
                       ->where('invoice_id', $invoiceId)
                       ->where('status', 'completed')
                       ->first();
        
        return isset($result['payment_amount']) ? $result['payment_amount'] : 0;
    }

    public function getTotalPaidBySupplier($supplierId, $startDate = null, $endDate = null)
    {
        $builder = $this->selectSum('payment_amount')
                        ->where('supplier_id', $supplierId)
                        ->where('status', 'completed');

        if ($startDate) {
            $builder->where('payment_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('payment_date <=', $endDate);
        }

        $result = $builder->first();
        return isset($result['payment_amount']) ? $result['payment_amount'] : 0;
    }

    public function getPaymentStats()
    {
        $stats = [
            'total_payments' => $this->countAll(),
            'completed_payments' => $this->where('status', 'completed')->countAllResults(),
            'pending_payments' => $this->where('status', 'pending')->countAllResults(),
            'failed_payments' => $this->where('status', 'failed')->countAllResults(),
            'total_amount' => $this->selectSum('payment_amount')->first()['payment_amount'] ?? 0,
            'completed_amount' => $this->where('status', 'completed')->selectSum('payment_amount')->first()['payment_amount'] ?? 0
        ];

        return $stats;
    }

    public function getMonthlyPaymentStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $builder = $this->select('MONTH(payment_date) as month, COUNT(*) as count, SUM(payment_amount) as amount')
                        ->where('YEAR(payment_date)', $year)
                        ->where('status', 'completed')
                        ->groupBy('MONTH(payment_date)')
                        ->orderBy('month', 'ASC');

        return $builder->findAll();
    }

    public function getPaymentMethodStats()
    {
        return $this->select('payment_method, COUNT(*) as count, SUM(payment_amount) as amount')
                    ->where('status', 'completed')
                    ->groupBy('payment_method')
                    ->orderBy('amount', 'DESC')
                    ->findAll();
    }

    public function generatePaymentNumber()
    {
        $prefix = 'SP';
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = $this->where('payment_number LIKE', $prefix . $year . $month . '%')
                            ->orderBy('payment_number', 'DESC')
                            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment['payment_number'], -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function updatePaymentStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getPaymentAnalytics()
    {
        $analytics = [
            'monthly_trends' => $this->getMonthlyPaymentStats(),
            'payment_methods' => $this->getPaymentMethodStats(),
            'supplier_payments' => $this->select('suppliers.supplier_name, COUNT(supplier_payments.id) as payment_count, SUM(payment_amount) as total_amount')
                                        ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id')
                                        ->where('status', 'completed')
                                        ->groupBy('suppliers.id, suppliers.supplier_name')
                                        ->orderBy('total_amount', 'DESC')
                                        ->findAll(),
            'daily_payments' => $this->select('DATE(payment_date) as date, SUM(payment_amount) as amount')
                                    ->where('status', 'completed')
                                    ->where('payment_date >=', date('Y-m-d', strtotime('-30 days')))
                                    ->groupBy('DATE(payment_date)')
                                    ->orderBy('date', 'ASC')
                                    ->findAll()
        ];

        return $analytics;
    }

    public function validatePaymentAmount($invoiceId, $paymentAmount)
    {
        $invoice = model('SupplierInvoice')->find($invoiceId);
        if (!$invoice) {
            return false;
        }

        $totalPaid = $this->getTotalPaidByInvoice($invoiceId);
        $remainingAmount = $invoice['total_amount'] - $totalPaid;
        
        return $paymentAmount <= $remainingAmount;
    }
}
