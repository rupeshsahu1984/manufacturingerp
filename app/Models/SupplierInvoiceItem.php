<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierInvoiceItem extends Model
{
    protected $table = 'supplier_invoice_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'invoice_id',
        'product_id',
        'purchase_order_item_id',
        'quantity',
        'unit_rate',
        'gst_rate',
        'gst_amount',
        'line_total',
        'description',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'invoice_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_rate' => 'required|numeric|greater_than[0]',
        'line_total' => 'required|numeric|greater_than[0]'
    ];

    protected $validationMessages = [
        'invoice_id' => [
            'required' => 'Invoice ID is required',
            'integer' => 'Invalid invoice ID'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function invoice()
    {
        return $this->belongsTo('App\Models\SupplierInvoice', 'invoice_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo('App\Models\PurchaseOrderItem', 'purchase_order_item_id', 'id');
    }

    // Methods
    public function getWithProduct($id = null)
    {
        $builder = $this->select('supplier_invoice_items.*, products.product_name, products.product_code, products.unit')
                        ->join('products', 'products.id = supplier_invoice_items.product_id');

        if ($id) {
            return $builder->where('supplier_invoice_items.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByInvoice($invoiceId)
    {
        return $this->select('supplier_invoice_items.*, products.product_name, products.product_code, products.unit')
                    ->join('products', 'products.id = supplier_invoice_items.product_id')
                    ->where('invoice_id', $invoiceId)
                    ->findAll();
    }

    public function getByProduct($productId)
    {
        return $this->select('supplier_invoice_items.*, supplier_invoices.invoice_number, supplier_invoices.invoice_date')
                    ->join('supplier_invoices', 'supplier_invoices.id = supplier_invoice_items.invoice_id')
                    ->where('product_id', $productId)
                    ->orderBy('supplier_invoices.invoice_date', 'DESC')
                    ->findAll();
    }

    public function getByPurchaseOrderItem($poItemId)
    {
        return $this->where('purchase_order_item_id', $poItemId)->findAll();
    }

    public function getTotalByProduct($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('SUM(quantity) as total_quantity, SUM(line_total) as total_amount')
                        ->join('supplier_invoices', 'supplier_invoices.id = supplier_invoice_items.invoice_id')
                        ->where('product_id', $productId);

        if ($startDate) {
            $builder->where('supplier_invoices.invoice_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('supplier_invoices.invoice_date <=', $endDate);
        }

        return $builder->first();
    }

    public function getPriceTrends($productId, $months = 12)
    {
        $startDate = date('Y-m-d', strtotime("-{$months} months"));
        
        return $this->select('MONTH(supplier_invoices.invoice_date) as month, YEAR(supplier_invoices.invoice_date) as year, AVG(unit_rate) as avg_price, COUNT(*) as invoice_count')
                    ->join('supplier_invoices', 'supplier_invoices.id = supplier_invoice_items.invoice_id')
                    ->where('product_id', $productId)
                    ->where('supplier_invoices.invoice_date >=', $startDate)
                    ->groupBy('YEAR(supplier_invoices.invoice_date), MONTH(supplier_invoices.invoice_date)')
                    ->orderBy('year, month', 'ASC')
                    ->findAll();
    }

    public function getSupplierPriceComparison($productId, $supplierIds = [])
    {
        $builder = $this->select('suppliers.supplier_name, AVG(unit_rate) as avg_price, MIN(unit_rate) as min_price, MAX(unit_rate) as max_price, COUNT(*) as invoice_count')
                        ->join('supplier_invoices', 'supplier_invoices.id = supplier_invoice_items.invoice_id')
                        ->join('suppliers', 'suppliers.id = supplier_invoices.supplier_id')
                        ->where('product_id', $productId)
                        ->groupBy('suppliers.id, suppliers.supplier_name')
                        ->orderBy('avg_price', 'ASC');

        if (!empty($supplierIds)) {
            $builder->whereIn('suppliers.id', $supplierIds);
        }

        return $builder->findAll();
    }

    public function calculateLineTotal($quantity, $unitRate, $gstRate = 0)
    {
        $subtotal = $quantity * $unitRate;
        $gstAmount = ($subtotal * $gstRate) / 100;
        return $subtotal + $gstAmount;
    }

    public function validateQuantities($invoiceId, $poItemId, $quantity)
    {
        // Check if quantity doesn't exceed PO quantity
        $poItem = model('PurchaseOrderItem')->find($poItemId);
        if (!$poItem) {
            return false;
        }

        // Get already invoiced quantity for this PO item
        $invoicedQuantity = $this->selectSum('quantity')
                                 ->where('purchase_order_item_id', $poItemId)
                                 ->where('invoice_id !=', $invoiceId)
                                 ->first()['quantity'] ?? 0;

        $availableQuantity = $poItem['quantity'] - $invoicedQuantity;
        
        return $quantity <= $availableQuantity;
    }

    public function getInvoiceSummary($invoiceId)
    {
        $items = $this->getByInvoice($invoiceId);
        
        $summary = [
            'total_items' => count($items),
            'total_quantity' => 0,
            'subtotal' => 0,
            'total_gst' => 0,
            'grand_total' => 0
        ];

        foreach ($items as $item) {
            $summary['total_quantity'] += $item['quantity'];
            $summary['subtotal'] += ($item['quantity'] * $item['unit_rate']);
            $summary['total_gst'] += $item['gst_amount'];
            $summary['grand_total'] += $item['line_total'];
        }

        return $summary;
    }
}
