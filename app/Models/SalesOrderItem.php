<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderItem extends Model
{
    protected $table            = 'sales_order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'so_id', 'product_id', 'unit', 'unit_price', 'discount', 
        'quantity', 'line_total', 'cgst', 'sgst', 'igst', 
        'tax_amount', 'ship_qty', 'available_stock', 'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'so_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getSalesOrderItems($salesOrderId)
    {
        return $this->db->table('sales_order_items soi')
                       ->select('soi.*, p.product_name, p.product_code')
                       ->join('products p', 'p.id = soi.product_id', 'left')
                       ->where('soi.so_id', $salesOrderId)
                       ->get()
                       ->getResultArray();
    }

    public function calculateLineTotal($unitPrice, $quantity, $discount = 0, $cgst = 0, $sgst = 0, $igst = 0)
    {
        $subtotal = $unitPrice * $quantity;
        $discountAmount = ($subtotal * $discount) / 100;
        $taxableAmount = $subtotal - $discountAmount;
        
        $cgstAmount = ($taxableAmount * $cgst) / 100;
        $sgstAmount = ($taxableAmount * $sgst) / 100;
        $igstAmount = ($taxableAmount * $igst) / 100;
        
        $totalTax = $cgstAmount + $sgstAmount + $igstAmount;
        $lineTotal = $taxableAmount + $totalTax;
        
        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'taxable_amount' => $taxableAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'total_tax' => $totalTax,
            'line_total' => $lineTotal
        ];
    }

    public function getDispatchItemsWithDetails($salesOrderId)
    {
        return $this->db->table('sales_order_items soi')
                       ->select('soi.*, p.product_name, p.product_code')
                       ->join('products p', 'p.id = soi.product_id', 'left')
                       ->where('soi.so_id', $salesOrderId)
                       ->where('soi.quantity >', 0)
                       ->get()
                       ->getResultArray();
    }
}
