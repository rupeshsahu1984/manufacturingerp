<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseBillItem extends Model
{
    protected $table = 'purchase_bill_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bill_id',
        'product_id',
        'quantity',
        'unit_price',
        'gst_rate',
        'cgst_rate',
        'sgst_rate',
        'igst_rate',
        'gst_amount',
        'total_amount'
    ];

    protected $useTimestamps = false;

    // Validation rules
    protected $validationRules = [
        'bill_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric',
        'unit_price' => 'required|numeric',
        'total_amount' => 'required|numeric'
    ];

    protected $validationMessages = [
        'bill_id' => [
            'required' => 'Bill ID is required'
        ],
        'product_id' => [
            'required' => 'Product is required'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get bill items with product details
     */
    public function getBillItems($billId)
    {
        $items = $this->select('pbi.id, pbi.bill_id, pbi.product_id, pbi.quantity, pbi.unit_price, pbi.gst_rate, pbi.cgst_rate, pbi.sgst_rate, pbi.igst_rate, pbi.gst_amount, pbi.total_amount, p.product_name, p.product_code, p.unit')
            ->from('purchase_bill_items pbi')
            ->join('products p', 'pbi.product_id = p.id', 'left')
            ->where('pbi.bill_id', $billId)
            ->orderBy('pbi.id', 'ASC')
            ->findAll();
        
        // Remove duplicates based on item ID (in case there are actual duplicate records)
        $uniqueItems = [];
        $seenIds = [];
        
        foreach ($items as $item) {
            if (!in_array($item['id'], $seenIds)) {
                $uniqueItems[] = $item;
                $seenIds[] = $item['id'];
            }
        }
        
        return $uniqueItems;
    }

    /**
     * Calculate item totals
     */
    public function calculateItemTotals($quantity, $unitPrice, $gstRate = 18.00)
    {
        $subtotal = $quantity * $unitPrice;
        $gstAmount = ($subtotal * $gstRate) / 100;
        $totalAmount = $subtotal + $gstAmount;

        return [
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Get bill items summary
     */
    public function getBillItemsSummary($billId)
    {
        $result = $this->select('SUM(quantity) as total_quantity, SUM(total_amount) as total_amount')
            ->where('bill_id', $billId)
            ->first();

        return [
            'total_quantity' => isset($result['total_quantity']) ? $result['total_quantity'] : 0,
            'total_amount' => isset($result['total_amount']) ? $result['total_amount'] : 0
        ];
    }

    /**
     * Delete items by bill ID
     */
    public function deleteItemsByBillId($billId)
    {
        return $this->where('bill_id', $billId)->delete();
    }
} 