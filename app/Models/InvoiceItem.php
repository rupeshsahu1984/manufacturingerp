<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invoice_id', 'product_id', 'quantity', 'unit_price', 
        'gst_rate', 'gst_amount', 'total_amount'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'invoice_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than[0]'
    ];

    protected $validationMessages = [
        'invoice_id' => [
            'required' => 'Invoice ID is required',
            'integer' => 'Invoice ID must be a valid integer'
        ],
        'product_id' => [
            'required' => 'Product is required',
            'integer' => 'Product ID must be a valid integer'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a valid number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'unit_price' => [
            'required' => 'Unit price is required',
            'numeric' => 'Unit price must be a valid number',
            'greater_than' => 'Unit price must be greater than 0'
        ],
        'line_total' => [
            'required' => 'Line total is required',
            'numeric' => 'Line total must be a valid number',
            'greater_than' => 'Line total must be greater than 0'
        ]
    ];

    // Get invoice items with product details
    public function getInvoiceItemsWithDetails($invoiceId)
    {
        return $this->select('invoice_items.*, products.product_name, products.product_code')
                   ->join('products', 'products.id = invoice_items.product_id', 'left')
                   ->where('invoice_items.invoice_id', $invoiceId)
                   ->orderBy('invoice_items.id', 'ASC')
                   ->findAll();
    }

    // Get invoice items by invoice ID
    public function getInvoiceItems($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)
                   ->orderBy('id', 'ASC')
                   ->findAll();
    }

    // Calculate invoice total
    public function calculateInvoiceTotal($invoiceId)
    {
        $result = $this->selectSum('line_total')
                      ->where('invoice_id', $invoiceId)
                      ->first();
        
        return isset($result['line_total']) ? $result['line_total'] : 0;
    }

    // Get invoice items summary
    public function getInvoiceItemsSummary($invoiceId)
    {
        return $this->select('COUNT(*) as total_items, SUM(quantity) as total_quantity, SUM(line_total) as total_amount')
                   ->where('invoice_id', $invoiceId)
                   ->first();
    }

    // Delete invoice items by invoice ID
    public function deleteInvoiceItems($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->delete();
    }

    // Get invoice items with product and category info
    public function getInvoiceItemsWithProductDetails($invoiceId)
    {
        return $this->select('invoice_items.*, products.product_name, products.product_code, categories.category_name')
                   ->join('products', 'products.id = invoice_items.product_id', 'left')
                   ->join('categories', 'categories.id = products.category_id', 'left')
                   ->where('invoice_items.invoice_id', $invoiceId)
                   ->orderBy('invoice_items.id', 'ASC')
                   ->findAll();
    }
}
