<?php

namespace App\Models;

use CodeIgniter\Model;

class DebitNote extends Model
{
    protected $table = 'debit_notes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'debit_note_number',
        'debit_note_date',
        'supplier_id',
        'purchase_order_id',
        'goods_receipt_id',
        'invoice_id',
        'return_reason',
        'subtotal',
        'gst_amount',
        'total_amount',
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
        'debit_note_number' => 'required|is_unique[debit_notes.debit_note_number,id,{id}]',
        'debit_note_date' => 'required|valid_date',
        'supplier_id' => 'required|integer',
        'return_reason' => 'required',
        'subtotal' => 'required|numeric',
        'total_amount' => 'required|numeric',
        'status' => 'required|in_list[draft,pending,approved,processed,cancelled]'
    ];

    protected $validationMessages = [
        'debit_note_number' => [
            'required' => 'Debit note number is required',
            'is_unique' => 'Debit note number must be unique'
        ],
        'supplier_id' => [
            'required' => 'Supplier is required',
            'integer' => 'Invalid supplier ID'
        ],
        'return_reason' => [
            'required' => 'Return reason is required'
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

    public function invoice()
    {
        return $this->belongsTo('App\Models\SupplierInvoice', 'invoice_id', 'id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\DebitNoteItem', 'debit_note_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('debit_notes.*, suppliers.supplier_name, suppliers.supplier_code, purchase_orders.po_number, supplier_invoices.invoice_number')
                        ->join('suppliers', 'suppliers.id = debit_notes.supplier_id')
                        ->join('purchase_orders', 'purchase_orders.id = debit_notes.purchase_order_id', 'left')
                        ->join('supplier_invoices', 'supplier_invoices.id = debit_notes.invoice_id', 'left');

        if ($id) {
            return $builder->where('debit_notes.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getAllWithRelations()
    {
        return $this->select('debit_notes.*, suppliers.supplier_name, suppliers.supplier_code, purchase_orders.po_number, supplier_invoices.invoice_number')
                    ->join('suppliers', 'suppliers.id = debit_notes.supplier_id')
                    ->join('purchase_orders', 'purchase_orders.id = debit_notes.purchase_order_id', 'left')
                    ->join('supplier_invoices', 'supplier_invoices.id = debit_notes.invoice_id', 'left')
                    ->orderBy('debit_notes.created_at', 'DESC')
                    ->findAll();
    }

    public function getBySupplier($supplierId)
    {
        return $this->where('supplier_id', $supplierId)
                    ->orderBy('debit_note_date', 'DESC')
                    ->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('debit_note_date', 'DESC')
                    ->findAll();
    }

    public function getByPurchaseOrder($poId)
    {
        return $this->where('purchase_order_id', $poId)
                    ->orderBy('debit_note_date', 'DESC')
                    ->findAll();
    }

    public function getByInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)
                    ->orderBy('debit_note_date', 'DESC')
                    ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('debit_note_date >=', $startDate)
                    ->where('debit_note_date <=', $endDate)
                    ->orderBy('debit_note_date', 'ASC')
                    ->findAll();
    }

    public function getDebitNoteStats()
    {
        $stats = [
            'total_debit_notes' => $this->countAll(),
            'pending_debit_notes' => $this->where('status', 'pending')->countAllResults(),
            'approved_debit_notes' => $this->where('status', 'approved')->countAllResults(),
            'processed_debit_notes' => $this->where('status', 'processed')->countAllResults(),
            'total_amount' => $this->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'pending_amount' => $this->whereIn('status', ['pending', 'approved'])
                                   ->selectSum('total_amount')->first()['total_amount'] ?? 0
        ];

        return $stats;
    }

    public function getMonthlyStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $builder = $this->select('MONTH(debit_note_date) as month, COUNT(*) as count, SUM(total_amount) as amount')
                        ->where('YEAR(debit_note_date)', $year)
                        ->groupBy('MONTH(debit_note_date)')
                        ->orderBy('month', 'ASC');

        return $builder->findAll();
    }

    public function getReturnReasonStats()
    {
        return $this->select('return_reason, COUNT(*) as count, SUM(total_amount) as amount')
                    ->groupBy('return_reason')
                    ->orderBy('count', 'DESC')
                    ->findAll();
    }

    public function getSupplierDebitNotes($supplierId = null)
    {
        $builder = $this->select('suppliers.supplier_name, COUNT(debit_notes.id) as debit_note_count, SUM(debit_notes.total_amount) as total_amount')
                        ->join('suppliers', 'suppliers.id = debit_notes.supplier_id')
                        ->groupBy('suppliers.id, suppliers.supplier_name')
                        ->orderBy('total_amount', 'DESC');

        if ($supplierId) {
            $builder->where('suppliers.id', $supplierId);
        }

        return $builder->findAll();
    }

    public function generateDebitNoteNumber()
    {
        $prefix = 'DN';
        $year = date('Y');
        $month = date('m');
        
        $lastDebitNote = $this->where('debit_note_number LIKE', $prefix . $year . $month . '%')
                              ->orderBy('debit_note_number', 'DESC')
                              ->first();

        if ($lastDebitNote) {
            $lastNumber = intval(substr($lastDebitNote['debit_note_number'], -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function updateDebitNoteStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getReturnReasons()
    {
        return [
            'defective_goods' => 'Defective Goods',
            'wrong_specification' => 'Wrong Specification',
            'damaged_during_transit' => 'Damaged During Transit',
            'quantity_mismatch' => 'Quantity Mismatch',
            'quality_issues' => 'Quality Issues',
            'late_delivery' => 'Late Delivery',
            'price_dispute' => 'Price Dispute',
            'other' => 'Other'
        ];
    }

    public function createFromReturn($returnData)
    {
        $debitNoteData = [
            'debit_note_number' => $this->generateDebitNoteNumber(),
            'debit_note_date' => date('Y-m-d'),
            'supplier_id' => $returnData['supplier_id'],
            'purchase_order_id' => isset($returnData['purchase_order_id']) ? $returnData['purchase_order_id'] : null,
            'goods_receipt_id' => isset($returnData['goods_receipt_id']) ? $returnData['goods_receipt_id'] : null,
            'invoice_id' => isset($returnData['invoice_id']) ? $returnData['invoice_id'] : null,
            'return_reason' => $returnData['return_reason'],
            'subtotal' => $returnData['subtotal'],
            'gst_amount' => isset($returnData['gst_amount']) ? $returnData['gst_amount'] : 0,
            'total_amount' => $returnData['total_amount'],
            'status' => 'draft',
            'notes' => isset($returnData['notes']) ? $returnData['notes'] : '',
            'created_by' => isset($returnData['created_by']) ? $returnData['created_by'] : 1
        ];

        return $this->insert($debitNoteData);
    }

    public function getDebitNoteAnalytics()
    {
        $analytics = [
            'monthly_trends' => $this->getMonthlyStats(),
            'return_reasons' => $this->getReturnReasonStats(),
            'supplier_debit_notes' => $this->getSupplierDebitNotes(),
            'status_distribution' => $this->select('status, COUNT(*) as count')
                                        ->groupBy('status')
                                        ->findAll(),
            'daily_debit_notes' => $this->select('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as amount')
                                       ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                                       ->groupBy('DATE(created_at)')
                                       ->orderBy('date', 'ASC')
                                       ->findAll()
        ];

        return $analytics;
    }
}
