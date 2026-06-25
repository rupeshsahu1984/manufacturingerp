<?php

namespace App\Models;

use CodeIgniter\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'product_code',
        'product_name',
        'description',
        'category_id',
        'unit',
        'unit_price',
        'selling_price',
        'reorder_level',
        'material_type',
        'waste_percentage',
        'is_recyclable',
        'gst_rate',
        'cgst_rate',
        'sgst_rate',
        'igst_rate',
        'hsn_code',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'product_code' => 'required|min_length[3]|max_length[50]|is_unique[products.product_code,id,{id}]',
        'product_name' => 'required|min_length[3]|max_length[255]',
        'category_id' => 'required|integer',
        'unit' => 'required|max_length[20]',
        'unit_price' => 'permit_empty|numeric',
        'reorder_level' => 'permit_empty|numeric',
        'material_type' => 'required|in_list[raw_material,packaging,finished_goods,waste]',
        'waste_percentage' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'is_recyclable' => 'permit_empty|in_list[0,1]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'product_code' => [
            'required' => 'Product code is required',
            'min_length' => 'Product code must be at least 3 characters long',
            'max_length' => 'Product code cannot exceed 50 characters',
            'is_unique' => 'Product code must be unique'
        ],
        'product_name' => [
            'required' => 'Product name is required',
            'min_length' => 'Product name must be at least 3 characters long',
            'max_length' => 'Product name cannot exceed 255 characters'
        ],
        'category_id' => [
            'required' => 'Category is required',
            'integer' => 'Category must be a valid selection'
        ],
        'unit' => [
            'required' => 'Unit is required',
            'max_length' => 'Unit cannot exceed 20 characters'
        ],
        'unit_price' => [
            'numeric' => 'Unit price must be a number'
        ],
        'reorder_level' => [
            'numeric' => 'Reorder level must be a number'
        ],
        'material_type' => [
            'required' => 'Material type is required',
            'in_list' => 'Material type must be one of: raw_material, packaging, finished_goods, waste'
        ],
        'waste_percentage' => [
            'numeric' => 'Waste percentage must be a number',
            'greater_than_equal_to' => 'Waste percentage must be between 0 and 100',
            'less_than_equal_to' => 'Waste percentage must be between 0 and 100'
        ],
        'is_recyclable' => [
            'in_list' => 'Recyclable status must be yes or no'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique product code
     */
    public function generateProductCode($productName, $materialType)
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Z]/', '', $productName), 0, 2));
        
        // Add material type prefix
        switch ($materialType) {
            case 'raw_material':
                $prefix = 'RM' . $prefix;
                break;
            case 'packaging':
                $prefix = 'PK' . $prefix;
                break;
            case 'finished_goods':
                $prefix = 'FG' . $prefix;
                break;
            case 'waste':
                $prefix = 'WT' . $prefix;
                break;
        }
        
        $lastCode = $this->select('product_code')
            ->like('product_code', $prefix, 'after')
            ->orderBy('product_code', 'DESC')
            ->first();
        
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode['product_code'], strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get products with filters
     */
    public function getProducts($filters = [])
    {
        $builder = $this->select('products.*, c.category_name, u.full_name as created_by_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->join('users u', 'products.created_by = u.id', 'left');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('product_name', $search)
                ->orLike('product_code', $search)
                ->orLike('description', $search)
                ->orLike('c.category_name', $search)
                ->groupEnd();
        }

        if (!empty($filters['material_type'])) {
            $builder->where('products.material_type', $filters['material_type']);
        }

        if (!empty($filters['category_id'])) {
            $builder->where('products.category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('products.status', $filters['status']);
        }

        if (!empty($filters['is_recyclable'])) {
            $builder->where('products.is_recyclable', $filters['is_recyclable']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('products.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('products.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('products.created_at', 'DESC')->findAll();
    }

    /**
     * Get product statistics
     */
    public function getProductStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'raw_material' => $this->where('material_type', 'raw_material')->countAllResults(),
            'packaging' => $this->where('material_type', 'packaging')->countAllResults(),
            'finished_goods' => $this->where('material_type', 'finished_goods')->countAllResults(),
            'waste' => $this->where('material_type', 'waste')->countAllResults(),
            'recyclable' => $this->where('is_recyclable', 1)->countAllResults(),
            'non_recyclable' => $this->where('is_recyclable', 0)->countAllResults()
        ];

        // Count by category
        $categories = $this->select('c.category_name, COUNT(*) as count')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->groupBy('products.category_id')
            ->findAll();
        
        $stats['categories'] = $categories;

        return $stats;
    }

    /**
     * Get products by material type
     */
    public function getProductsByMaterialType($materialType)
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.material_type', $materialType)
            ->where('products.status', 'active')
            ->findAll();
    }

    /**
     * Get products for BOM (raw materials and packaging)
     */
    public function getProductsForBOM()
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->whereIn('products.material_type', ['raw_material', 'packaging'])
            ->where('products.status', 'active')
            ->orderBy('products.material_type', 'ASC')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get finished goods for BOM creation
     */
    public function getFinishedGoods()
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.material_type', 'finished_goods')
            ->where('products.status', 'active')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get waste materials
     */
    public function getWasteMaterials()
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.material_type', 'waste')
            ->where('products.status', 'active')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get sales materials (produced materials and waste materials)
     */
    public function getSalesMaterials()
    {
        // Get products first, then calculate stock separately
        $products = $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->whereIn('products.material_type', ['finished_goods', 'waste'])
            ->where('products.status', 'active')
            ->orderBy('products.material_type', 'ASC')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
        
        // Calculate stock for each product by matching with items table
        $db = \Config\Database::connect();
        foreach ($products as &$product) {
            // Try to find matching item by code
            $item = $db->table('items')
                ->where('item_code', $product['product_code'])
                ->get()
                ->getRowArray();
            
            if ($item) {
                // Get available stock for this item
                $stockResult = $db->table('stock')
                    ->selectSum('quantity')
                    ->where('item_id', $item['id'])
                    ->where('status', 'available')
                    ->get()
                    ->getRowArray();
                
                $product['current_stock'] = $stockResult['quantity'] ?? 0;
                $product['available_stock'] = $stockResult['quantity'] ?? 0;
            } else {
                $product['current_stock'] = 0;
                $product['available_stock'] = 0;
            }
        }
        
        return $products;
    }

    /**
     * Get products with stock information
     */
    public function getProductsWithStock()
    {
        return $this->select('products.*, c.category_name, COALESCE(s.quantity, 0) as current_stock')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->join('stock s', 'products.id = s.product_id', 'left')
            ->where('products.status', 'active')
            ->orderBy('products.material_type', 'ASC')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get products below reorder level
     */
    public function getProductsBelowReorderLevel()
    {
        return $this->select('products.*, c.category_name, COALESCE(s.quantity, 0) as current_stock')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->join('stock s', 'products.id = s.product_id', 'left')
            ->where('products.status', 'active')
            ->where('products.reorder_level >', 0)
            ->where('COALESCE(s.quantity, 0) <=', 'products.reorder_level', false)
            ->orderBy('products.material_type', 'ASC')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get product with BOM information
     */
    public function getProductWithBOM($productId)
    {
        $product = $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->find($productId);
        
        if (!$product) {
            return null;
        }

        // Get BOM if it's a finished good
        if ($product['material_type'] === 'finished_goods') {
            $bomModel = new \App\Models\BOM();
            $product['bom'] = $bomModel->getBOMByFinishedProduct($productId);
        }

        return $product;
    }

    /**
     * Get product performance metrics
     */
    public function getProductPerformance($productId)
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return null;
        }

        // Get stock information
        $stockModel = new \App\Models\Stock();
        $currentStock = $stockModel->getCurrentStock($productId);

        // Get purchase history
        $purchaseBillItemModel = new \App\Models\PurchaseBillItem();
        $purchaseHistory = $purchaseBillItemModel->getProductPurchaseHistory($productId);

        // Get sales history (if finished goods)
        $salesHistory = [];
        if ($product['material_type'] === 'finished_goods') {
            $salesOrderItemModel = new \App\Models\SalesOrderItem();
            $salesHistory = $salesOrderItemModel->getProductSalesHistory($productId);
        }

        return [
            'product' => $product,
            'current_stock' => $currentStock,
            'purchase_history' => $purchaseHistory,
            'sales_history' => $salesHistory
        ];
    }

    /**
     * Search products for AJAX
     */
    public function searchProducts($search, $materialType = null)
    {
        $builder = $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.status', 'active');

        if ($materialType) {
            $builder->where('products.material_type', $materialType);
        }

        $builder->groupStart()
            ->like('product_name', $search)
            ->orLike('product_code', $search)
            ->orLike('c.category_name', $search)
            ->groupEnd();

        return $builder->orderBy('products.product_name', 'ASC')->findAll();
    }

    /**
     * Get unique material types
     */
    public function getMaterialTypes()
    {
        return $this->select('DISTINCT material_type')
            ->where('material_type IS NOT NULL')
            ->where('material_type !=', '')
            ->findAll();
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId)
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.category_id', $categoryId)
            ->where('products.status', 'active')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get product summary
     */
    public function getProductSummary($productId)
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return null;
        }

        $stockModel = new \App\Models\Stock();
        $currentStock = $stockModel->getCurrentStock($productId);

        return [
            'product' => $product,
            'current_stock' => $currentStock,
            'below_reorder_level' => $product['reorder_level'] > 0 && $currentStock <= $product['reorder_level']
        ];
    }

    /**
     * Update product stock
     */
    public function updateStock($productId, $quantity, $type = 'add')
    {
        $stockModel = new \App\Models\Stock();
        return $stockModel->updateStock($productId, $quantity, $type);
    }

    /**
     * Get product cost (for BOM calculations)
     */
    public function getProductCost($productId)
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return 0;
        }

        // For finished goods, calculate from BOM
        if ($product['material_type'] === 'finished_goods') {
            $bomModel = new \App\Models\BOM();
            return $bomModel->calculateFinishedProductCost($productId);
        }

        // For raw materials and packaging, use unit price
        return isset($product['unit_price']) ? $product['unit_price'] : 0;
    }

    /**
     * Get material statistics for dashboard
     */
    public function getMaterialStats()
    {
        $total = $this->where('status', 'active')->countAllResults();
        
        $rawMaterials = $this->where('material_type', 'raw_material')
            ->where('status', 'active')
            ->countAllResults();
            
        $packaging = $this->where('material_type', 'packaging')
            ->where('status', 'active')
            ->countAllResults();
            
        // Count low stock items (below reorder level)
        // Note: current_stock is calculated from stock table, not stored in products table
        $lowStock = 0; // Will be calculated separately if needed

        return [
            'total' => $total,
            'raw_materials' => $rawMaterials,
            'packaging' => $packaging,
            'low_stock' => $lowStock
        ];
    }

    /**
     * Get recent materials for dashboard
     */
    public function getRecentMaterials($limit = 5)
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.status', 'active')
            ->orderBy('products.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get active products
     */
    public function getActiveProducts()
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.status', 'active')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get products for dropdown/select
     */
    public function getProductsForSelect()
    {
        return $this->select('id, product_code, product_name, unit_price')
            ->where('status', 'active')
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get products with GST information
     */
    public function getProductsWithGST()
    {
        return $this->select('products.*, c.category_name, COALESCE(s.quantity, 0) as current_stock')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->join('stock s', 'products.id = s.product_id', 'left')
            ->where('products.status', 'active')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get product GST rate
     */
    public function getProductGSTRate($productId)
    {
        $product = $this->find($productId);
        return $product ? (isset($product['gst_rate']) ? $product['gst_rate'] : 18.00) : 18.00; // Default 18%
    }

    /**
     * Calculate GST amount for a product
     */
    public function calculateGSTAmount($productId, $quantity, $unitPrice)
    {
        $gstRate = $this->getProductGSTRate($productId);
        $subtotal = $quantity * $unitPrice;
        return $subtotal * ($gstRate / 100);
    }

    /**
     * Get products by HSN code
     */
    public function getProductsByHSN($hsnCode)
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.hsn_code', $hsnCode)
            ->where('products.status', 'active')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get unique HSN codes
     */
    public function getUniqueHSNCodes()
    {
        return $this->select('hsn_code')
            ->distinct()
            ->where('hsn_code IS NOT NULL')
            ->where('hsn_code !=', '')
            ->where('status', 'active')
            ->findAll();
    }

    /**
     * Get GST summary statistics
     */
    public function getGSTSummary()
    {
        $summary = [];
        
        // Get products by GST rate
        $gstRates = $this->select('gst_rate, COUNT(*) as count')
            ->where('gst_rate IS NOT NULL')
            ->where('status', 'active')
            ->groupBy('gst_rate')
            ->findAll();
            
        foreach ($gstRates as $rate) {
            $summary['gst_rates'][$rate['gst_rate']] = $rate['count'];
        }
        
        // Get HSN code summary
        $hsnCodes = $this->select('hsn_code, COUNT(*) as count')
            ->where('hsn_code IS NOT NULL')
            ->where('hsn_code !=', '')
            ->where('status', 'active')
            ->groupBy('hsn_code')
            ->findAll();
            
        foreach ($hsnCodes as $hsn) {
            $summary['hsn_codes'][$hsn['hsn_code']] = $hsn['count'];
        }
        
        return $summary;
    }

    /**
     * Get finished goods for dropdown selection
     */
    public function getFinishedGoodsForDropdown()
    {
        return $this->select('id, product_code, product_name, description, unit, selling_price, unit_price, gst_rate, cgst_rate, sgst_rate, igst_rate, hsn_code, category_id')
            ->where('material_type', 'finished_goods')
            ->where('status', 'active')
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get product details by ID for auto-filling
     */
    public function getProductDetailsForAutoFill($productId)
    {
        return $this->select('products.*, c.category_name')
            ->join('categories c', 'products.category_id = c.id', 'left')
            ->where('products.id', $productId)
            ->where('products.status', 'active')
            ->first();
    }
} 