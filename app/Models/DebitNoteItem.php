<?php

namespace App\Models;

use CodeIgniter\Model;

class DebitNoteItem extends Model
{
    protected $table = 'debit_note_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'debit_note_id',
        'product_id',
        'purchase_order_item_id',
        'goods_receipt_item_id',
        'quantity',
        'unit_rate',
        'gst_rate',
        'gst_amount',
        'line_total',
        'return_quantity',
        'return_reason',
        'description',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'debit_note_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_rate' => 'required|numeric|greater_than[0]',
        'line_total' => 'required|numeric|greater_than[0]',
        'return_quantity' => 'required|numeric|greater_than[0]'
    ];

    protected $validationMessages = [
        'debit_note_id' => [
            'required' => 'Debit note ID is required',
            'integer' => 'Invalid debit note ID'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'return_quantity' => [
            'required' => 'Return quantity is required',
            'numeric' => 'Return quantity must be a number',
            'greater_than' => 'Return quantity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function debitNote()
    {
        return $this->belongsTo('App\Models\DebitNote', 'debit_note_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo('App\Models\PurchaseOrderItem', 'purchase_order_item_id', 'id');
    }

    public function goodsReceiptItem()
    {
        return $this->belongsTo('App\Models\GoodsReceiptItem', 'goods_receipt_item_id', 'id');
    }

    // Methods
    public function getWithProduct($id = null)
    {
        $builder = $this->select('debit_note_items.*, products.product_name, products.product_code, products.unit')
                        ->join('products', 'products.id = debit_note_items.product_id');

        if ($id) {
            return $builder->where('debit_note_items.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByDebitNote($debitNoteId)
    {
        return $this->select('debit_note_items.*, products.product_name, products.product_code, products.unit')
                    ->join('products', 'products.id = debit_note_items.product_id')
                    ->where('debit_note_id', $debitNoteId)
                    ->findAll();
    }

    public function getByProduct($productId)
    {
        return $this->select('debit_note_items.*, debit_notes.debit_note_number, debit_notes.debit_note_date')
                    ->join('debit_notes', 'debit_notes.id = debit_note_items.debit_note_id')
                    ->where('product_id', $productId)
                    ->orderBy('debit_notes.debit_note_date', 'DESC')
                    ->findAll();
    }

    public function getByPurchaseOrderItem($poItemId)
    {
        return $this->where('purchase_order_item_id', $poItemId)->findAll();
    }

    public function getByGoodsReceiptItem($grnItemId)
    {
        return $this->where('goods_receipt_item_id', $grnItemId)->findAll();
    }

    public function getTotalReturnsByProduct($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('SUM(return_quantity) as total_return_quantity, SUM(line_total) as total_amount')
                        ->join('debit_notes', 'debit_notes.id = debit_note_items.debit_note_id')
                        ->where('product_id', $productId);

        if ($startDate) {
            $builder->where('debit_notes.debit_note_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('debit_notes.debit_note_date <=', $endDate);
        }

        return $builder->first();
    }

    public function getReturnReasonStats($productId = null)
    {
        $builder = $this->select('return_reason, COUNT(*) as count, SUM(line_total) as amount')
                        ->join('debit_notes', 'debit_notes.id = debit_note_items.debit_note_id');

        if ($productId) {
            $builder->where('product_id', $productId);
        }

        return $builder->groupBy('return_reason')
                      ->orderBy('count', 'DESC')
                      ->findAll();
    }

    public function getSupplierReturnMetrics($supplierId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('products.product_name, COUNT(debit_note_items.id) as return_count, SUM(return_quantity) as total_return_quantity, SUM(line_total) as total_amount')
                        ->join('debit_notes', 'debit_notes.id = debit_note_items.debit_note_id')
                        ->join('products', 'products.id = debit_note_items.product_id')
                        ->where('debit_notes.supplier_id', $supplierId);

        if ($startDate) {
            $builder->where('debit_notes.debit_note_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('debit_notes.debit_note_date <=', $endDate);
        }

        return $builder->groupBy('products.id, products.product_name')
                      ->orderBy('total_amount', 'DESC')
                      ->findAll();
    }

    public function calculateLineTotal($quantity, $unitRate, $gstRate = 0)
    {
        $subtotal = $quantity * $unitRate;
        $gstAmount = ($subtotal * $gstRate) / 100;
        return $subtotal + $gstAmount;
    }

    public function validateReturnQuantities($debitNoteId, $grnItemId, $returnQuantity)
    {
        // Check if return quantity doesn't exceed received quantity
        $grnItem = model('GoodsReceiptItem')->find($grnItemId);
        if (!$grnItem) {
            return false;
        }

        // Get already returned quantity for this GRN item
        $returnedQuantity = $this->selectSum('return_quantity')
                                 ->where('goods_receipt_item_id', $grnItemId)
                                 ->where('debit_note_id !=', $debitNoteId)
                                 ->first()['return_quantity'] ?? 0;

        $availableQuantity = $grnItem['accepted_quantity'] - $returnedQuantity;
        
        return $returnQuantity <= $availableQuantity;
    }

    public function getDebitNoteSummary($debitNoteId)
    {
        $items = $this->getByDebitNote($debitNoteId);
        
        $summary = [
            'total_items' => count($items),
            'total_return_quantity' => 0,
            'subtotal' => 0,
            'total_gst' => 0,
            'grand_total' => 0
        ];

        foreach ($items as $item) {
            $summary['total_return_quantity'] += $item['return_quantity'];
            $summary['subtotal'] += ($item['return_quantity'] * $item['unit_rate']);
            $summary['total_gst'] += $item['gst_amount'];
            $summary['grand_total'] += $item['line_total'];
        }

        return $summary;
    }

    public function getReturnTrends($productId, $months = 12)
    {
        $startDate = date('Y-m-d', strtotime("-{$months} months"));
        
        return $this->select('MONTH(debit_notes.debit_note_date) as month, YEAR(debit_notes.debit_note_date) as year, SUM(return_quantity) as total_returns, COUNT(*) as return_count')
                    ->join('debit_notes', 'debit_notes.id = debit_note_items.debit_note_id')
                    ->where('product_id', $productId)
                    ->where('debit_notes.debit_note_date >=', $startDate)
                    ->groupBy('YEAR(debit_notes.debit_note_date), MONTH(debit_notes.debit_note_date)')
                    ->orderBy('year, month', 'ASC')
                    ->findAll();
    }

    public function getQualityMetrics($supplierId = null)
    {
        $builder = $this->select('products.product_name, AVG(return_quantity) as avg_return_quantity, COUNT(*) as return_frequency')
                        ->join('debit_notes', 'debit_notes.id = debit_note_items.debit_note_id')
                        ->join('products', 'products.id = debit_note_items.product_id')
                        ->groupBy('products.id, products.product_name')
                        ->orderBy('return_frequency', 'DESC');

        if ($supplierId) {
            $builder->where('debit_notes.supplier_id', $supplierId);
        }

        return $builder->findAll();
    }
}
