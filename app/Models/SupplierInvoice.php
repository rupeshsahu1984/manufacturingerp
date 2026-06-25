<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierInvoice extends Model
{
    protected $table = 'supplier_invoices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'invoice_number',
        'invoice_date',
        'supplier_id',
        'purchase_order_id',
        'goods_receipt_id',
        'subtotal',
        'gst_amount',
        'transport_cost',
        'total_amount',
        'payment_terms',
        'due_date',
        'status',
        'notes',
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
        'invoice_number' => 'required|is_unique[supplier_invoices.invoice_number,id,{id}]',
        'invoice_date' => 'required|valid_date',
        'supplier_id' => 'required|integer',
        'purchase_order_id' => 'required|integer',
        'subtotal' => 'required|numeric',
        'total_amount' => 'required|numeric',
        'status' => 'required|in_list[draft,pending,approved,paid,partially_paid,overdue,cancelled]'
    ];

    protected $validationMessages = [
        'invoice_number' => [
            'required' => 'Invoice number is required',
            'is_unique' => 'Invoice number must be unique'
        ],
        'supplier_id' => [
            'required' => 'Supplier is required',
            'integer' => 'Invalid supplier ID'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo('App\Models\PurchaseOrder', 'purchase_order_id', 'id');
    }

    public function goodsReceipt()
    {
        return $this->belongsTo('App\Models\GoodsReceipt', 'goods_receipt_id', 'id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\SupplierInvoiceItem', 'invoice_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\SupplierPayment', 'invoice_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('supplier_invoices.*, suppliers.supplier_name, suppliers.supplier_code, purchase_orders.po_number')
                        ->join('suppliers', 'suppliers.id = supplier_invoices.supplier_id')
                        ->join('purchase_orders', 'purchase_orders.id = supplier_invoices.purchase_order_id');

        if ($id) {
            return $builder->where('supplier_invoices.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getAllWithRelations()
    {
        return $this->select('supplier_invoices.*, suppliers.supplier_name, suppliers.supplier_code, purchase_orders.po_number')
                    ->join('suppliers', 'suppliers.id = supplier_invoices.supplier_id')
                    ->join('purchase_orders', 'purchase_orders.id = supplier_invoices.purchase_order_id')
                    ->orderBy('supplier_invoices.created_at', 'DESC')
                    ->findAll();
    }

    public function getBySupplier($supplierId)
    {
        return $this->where('supplier_id', $supplierId)
                    ->orderBy('invoice_date', 'DESC')
                    ->findAll();
    }

    public function getByPurchaseOrder($poId)
    {
        return $this->where('purchase_order_id', $poId)
                    ->orderBy('invoice_date', 'DESC')
                    ->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('invoice_date', 'DESC')
                    ->findAll();
    }

    public function getOverdueInvoices()
    {
        $today = date('Y-m-d');
        return $this->where('due_date <', $today)
                    ->whereIn('status', ['pending', 'approved', 'partially_paid'])
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getOverdueCount()
    {
        $today = date('Y-m-d');
        return $this->where('due_date <', $today)
                    ->whereIn('status', ['pending', 'approved', 'partially_paid'])
                    ->countAllResults();
    }

    public function getStats()
    {
        $stats = [
            'total_invoices' => $this->countAll(),
            'pending_invoices' => $this->where('status', 'pending')->countAllResults(),
            'approved_invoices' => $this->where('status', 'approved')->countAllResults(),
            'paid_invoices' => $this->where('status', 'paid')->countAllResults(),
            'overdue_invoices' => $this->getOverdueInvoices(),
            'total_amount' => $this->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'pending_amount' => $this->whereIn('status', ['pending', 'approved', 'partially_paid'])
                                   ->selectSum('total_amount')->first()['total_amount'] ?? 0
        ];

        return $stats;
    }

    public function getMonthlyStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $builder = $this->select('MONTH(invoice_date) as month, COUNT(*) as count, SUM(total_amount) as amount')
                        ->where('YEAR(invoice_date)', $year)
                        ->groupBy('MONTH(invoice_date)')
                        ->orderBy('month', 'ASC');

        return $builder->findAll();
    }

    public function getSupplierPerformance($supplierId = null)
    {
        $builder = $this->select('suppliers.supplier_name, COUNT(supplier_invoices.id) as invoice_count, SUM(supplier_invoices.total_amount) as total_amount')
                        ->join('suppliers', 'suppliers.id = supplier_invoices.supplier_id')
                        ->groupBy('suppliers.id, suppliers.supplier_name')
                        ->orderBy('total_amount', 'DESC');

        if ($supplierId) {
            $builder->where('suppliers.id', $supplierId);
        }

        return $builder->findAll();
    }

    public function generateInvoiceNumber()
    {
        $prefix = 'SI';
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = $this->where('invoice_number LIKE', $prefix . $year . $month . '%')
                            ->orderBy('invoice_number', 'DESC')
                            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice['invoice_number'], -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function updateInvoiceStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getAnalytics($filters = [])
    {
        $analytics = [
            'monthly_trends' => $this->getMonthlyStats(),
            'supplier_performance' => $this->getSupplierPerformance(),
            'status_distribution' => $this->select('status, COUNT(*) as count')
                                        ->groupBy('status')
                                        ->findAll(),
            'payment_trends' => $this->select('DATE(created_at) as date, SUM(total_amount) as amount')
                                    ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                                    ->groupBy('DATE(created_at)')
                                    ->orderBy('date', 'ASC')
                                    ->findAll()
        ];

        return $analytics;
    }
}
