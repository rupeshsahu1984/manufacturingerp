<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'po_id',
        'purchase_order_id', // Keep for backward compatibility
        'product_id',
        'quantity',
        'unit_price',
        'total_amount',
        'description',
        'unit',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    // Validation
    protected $validationRules = [
        'po_id' => 'required|numeric',
        'purchase_order_id' => 'permit_empty|numeric', // For backward compatibility
        'product_id' => 'required|numeric',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than_equal_to[0]',
        'total_amount' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'purchase_order_id' => [
            'required' => 'Purchase order ID is required',
            'numeric' => 'Invalid purchase order ID'
        ],
        'product_id' => [
            'required' => 'Product is required',
            'numeric' => 'Invalid product ID'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'unit_price' => [
            'required' => 'Unit price is required',
            'numeric' => 'Unit price must be a number',
            'greater_than_equal_to' => 'Unit price must be 0 or greater'
        ],
        'total_amount' => [
            'required' => 'Total amount is required',
            'numeric' => 'Total amount must be a number',
            'greater_than_equal_to' => 'Total amount must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get items by purchase order ID
     */
    public function getItemsByPurchaseOrderId($purchaseOrderId)
    {
        return $this->select('purchase_order_items.*, products.product_name, products.product_code')
                   ->join('products', 'products.id = purchase_order_items.product_id', 'left')
                   ->where('purchase_order_items.po_id', $purchaseOrderId)
                   ->orderBy('purchase_order_items.id', 'ASC')
                   ->findAll();
    }

    /**
     * Get item with product details
     */
    public function getItemWithProduct($itemId)
    {
        return $this->select('purchase_order_items.*, products.product_name, products.product_code')
                   ->join('products', 'products.id = purchase_order_items.product_id', 'left')
                   ->where('purchase_order_items.id', $itemId)
                   ->first();
    }

    /**
     * Get items by product ID
     */
    public function getItemsByProductId($productId)
    {
        return $this->select('purchase_order_items.*, purchase_orders.po_number, purchase_orders.order_date, purchase_orders.status')
                   ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.po_id', 'left')
                   ->where('purchase_order_items.product_id', $productId)
                   ->orderBy('purchase_orders.order_date', 'DESC')
                   ->findAll();
    }

    /**
     * Calculate total amount for an item
     */
    public function calculateTotalAmount($quantity, $unitPrice)
    {
        return $quantity * $unitPrice;
    }

    /**
     * Get total quantity ordered for a product
     */
    public function getTotalQuantityOrdered($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->selectSum('quantity')
                ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
                ->where('purchase_order_items.product_id', $productId)
                ->whereIn('purchase_orders.status', ['ordered', 'received']);

        if ($startDate) {
            $builder->where('purchase_orders.order_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.order_date <=', $endDate);
        }

        $result = $builder->first();
        return isset($result['quantity']) ? $result['quantity'] : 0;
    }

    /**
     * Get total value ordered for a product
     */
    public function getTotalValueOrdered($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->selectSum('total_amount')
                ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
                ->where('purchase_order_items.product_id', $productId)
                ->whereIn('purchase_orders.status', ['ordered', 'received']);

        if ($startDate) {
            $builder->where('purchase_orders.order_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.order_date <=', $endDate);
        }

        $result = $builder->first();
        return isset($result['total_amount']) ? $result['total_amount'] : 0;
    }

    /**
     * Get items summary for a purchase order
     */
    public function getItemsSummary($purchaseOrderId)
    {
        return $this->select('
                COUNT(*) as total_items,
                SUM(quantity) as total_quantity,
                SUM(total_amount) as total_value,
                AVG(unit_price) as avg_unit_price
            ')
            ->where('po_id', $purchaseOrderId)
            ->first();
    }

    /**
     * Get top ordered products
     */
    public function getTopOrderedProducts($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('
                products.product_name,
                products.product_code,
                SUM(purchase_order_items.quantity) as total_quantity,
                SUM(purchase_order_items.total_amount) as total_value,
                AVG(purchase_order_items.unit_price) as avg_unit_price
            ')
            ->join('products', 'products.id = purchase_order_items.product_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->whereIn('purchase_orders.status', ['ordered', 'received'])
            ->groupBy('purchase_order_items.product_id')
            ->orderBy('total_quantity', 'DESC')
            ->limit($limit);

        if ($startDate) {
            $builder->where('purchase_orders.order_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.order_date <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get items by supplier
     */
    public function getItemsBySupplier($supplierId, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('
                purchase_order_items.*,
                products.product_name,
                products.product_code,
                purchase_orders.po_number,
                purchase_orders.order_date,
                purchase_orders.status
            ')
            ->join('products', 'products.id = purchase_order_items.product_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->where('purchase_orders.supplier_id', $supplierId)
            ->orderBy('purchase_orders.order_date', 'DESC');

        if ($startDate) {
            $builder->where('purchase_orders.order_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.order_date <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get supplier summary
     */
    public function getSupplierSummary($supplierId, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('
                COUNT(DISTINCT purchase_order_items.product_id) as unique_products,
                SUM(purchase_order_items.quantity) as total_quantity,
                SUM(purchase_order_items.total_amount) as total_value,
                AVG(purchase_order_items.unit_price) as avg_unit_price
            ')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->where('purchase_orders.supplier_id', $supplierId)
            ->whereIn('purchase_orders.status', ['ordered', 'received']);

        if ($startDate) {
            $builder->where('purchase_orders.order_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.order_date <=', $endDate);
        }

        return $builder->first();
    }

    /**
     * Delete items by purchase order ID
     */
    public function deleteByPurchaseOrderId($purchaseOrderId)
    {
        return $this->where('po_id', $purchaseOrderId)->delete();
    }

    /**
     * Get items with low stock alerts
     */
    public function getLowStockAlerts($threshold = 10)
    {
        return $this->select('
                products.product_name,
                products.product_code,
                products.current_stock,
                SUM(purchase_order_items.quantity) as ordered_quantity,
                SUM(CASE WHEN purchase_orders.status = "received" THEN purchase_order_items.quantity ELSE 0 END) as received_quantity
            ')
            ->join('products', 'products.id = purchase_order_items.product_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->where('products.current_stock <=', $threshold)
            ->whereIn('purchase_orders.status', ['ordered', 'received'])
            ->groupBy('purchase_order_items.product_id')
            ->having('received_quantity < ordered_quantity')
            ->findAll();
    }

    /**
     * Get purchase order items for dashboard
     */
    public function getDashboardItems($limit = 10)
    {
        return $this->select('
                purchase_order_items.*,
                products.product_name,
                products.product_code,
                purchase_orders.po_number,
                purchase_orders.order_date,
                purchase_orders.status
            ')
            ->join('products', 'products.id = purchase_order_items.product_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->orderBy('purchase_orders.order_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get items by date range
     */
    public function getItemsByDateRange($startDate, $endDate)
    {
        return $this->select('
                purchase_order_items.*,
                products.product_name,
                products.product_code,
                purchase_orders.po_number,
                purchase_orders.order_date,
                purchase_orders.status
            ')
            ->join('products', 'products.id = purchase_order_items.product_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->where('purchase_orders.order_date >=', $startDate)
            ->where('purchase_orders.order_date <=', $endDate)
            ->orderBy('purchase_orders.order_date', 'DESC')
            ->findAll();
    }
}
