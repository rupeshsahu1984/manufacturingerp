<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseReturnItem extends Model
{
    protected $table = 'purchase_return_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'purchase_return_id',
        'product_id',
        'quantity',
        'return_quantity',
        'original_quantity',
        'unit_price',
        'total_amount',
        'reason',
        'condition',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'purchase_return_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|decimal',
        'unit_price' => 'required|decimal',
        'total_amount' => 'required|decimal',
        'reason' => 'permit_empty|string',
        'condition' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'purchase_return_id' => [
            'required' => 'Purchase Return ID is required',
            'integer' => 'Purchase Return ID must be an integer'
        ],
        'product_id' => [
            'required' => 'Product ID is required',
            'integer' => 'Product ID must be an integer'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'decimal' => 'Quantity must be a decimal number'
        ],
        'unit_price' => [
            'required' => 'Unit price is required',
            'decimal' => 'Unit price must be a decimal number'
        ],
        'total_amount' => [
            'required' => 'Total amount is required',
            'decimal' => 'Total amount must be a decimal number'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get items by purchase return ID
     */
    public function getItemsByPurchaseReturnId($purchaseReturnId)
    {
        return $this->where('purchase_return_id', $purchaseReturnId)->findAll();
    }

    /**
     * Get items with product details
     */
    public function getItemsWithProductDetails($purchaseReturnId)
    {
        return $this->select('purchase_return_items.*, products.product_name, products.product_code')
            ->join('products', 'products.id = purchase_return_items.product_id')
            ->where('purchase_return_items.purchase_return_id', $purchaseReturnId)
            ->findAll();
    }
}
