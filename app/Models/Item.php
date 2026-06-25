<?php

namespace App\Models;

use CodeIgniter\Model;

class Item extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'item_code',
        'item_name',
        'material_type',
        'category_id',
        'subcategory_id',
        'description',
        'uom',
        'hsn_code',
        'sac_code',
        'reorder_level',
        'safety_stock',
        'min_stock',
        'max_stock',
        'standard_cost',
        'selling_price',
        'preferred_supplier_id',
        'barcode',
        'rfid_tag',
        'weight',
        'weight_uom',
        'dimensions',
        'dimension_uom',
        'shelf_life_days',
        'storage_conditions',
        'hazardous',
        'status',
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
        'item_code' => 'required|min_length[3]|max_length[50]|is_unique[items.item_code,id,{id}]',
        'item_name' => 'required|min_length[3]|max_length[200]',
        'material_type' => 'required|in_list[raw_material,packaging,finished_goods,waste]',
        'category_id' => 'required|integer|is_not_unique[categories.id]',
        'uom' => 'required|min_length[1]|max_length[20]',
        'reorder_level' => 'required|numeric|greater_than_equal_to[0]',
        'safety_stock' => 'required|numeric|greater_than_equal_to[0]',
        'min_stock' => 'required|numeric|greater_than_equal_to[0]',
        'standard_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'selling_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'status' => 'required|in_list[active,inactive,discontinued,obsolete]'
    ];

    protected $validationMessages = [
        'item_code' => [
            'required' => 'Item code is required',
            'min_length' => 'Item code must be at least 3 characters long',
            'max_length' => 'Item code cannot exceed 50 characters',
            'is_unique' => 'Item code must be unique'
        ],
        'item_name' => [
            'required' => 'Item name is required',
            'min_length' => 'Item name must be at least 3 characters long'
        ],
        'reorder_level' => [
            'required' => 'Reorder level is required',
            'numeric' => 'Reorder level must be a number',
            'greater_than_equal_to' => 'Reorder level must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['generateItemCode', 'setDefaults'];
    protected $beforeUpdate = ['setDefaults'];

    /**
     * Generate unique item code
     */
    protected function generateItemCode(array $data)
    {
        if (!isset($data['data']['item_code']) || empty($data['data']['item_code'])) {
            $data['data']['item_code'] = $this->generateUniqueCode(isset($data['data']['material_type']) ? $data['data']['material_type'] : 'raw_material');
        }
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'active';
        }
        
        if (!isset($data['data']['hazardous'])) {
            $data['data']['hazardous'] = false;
        }
        
        return $data;
    }

    /**
     * Generate unique item code based on type
     */
    public function generateUniqueCode($itemType)
    {
        $prefix = strtoupper(substr($itemType, 0, 2));
        $year = date('Y');
        $month = date('m');
        
        // Get last code for this type and month
        $lastCode = $this->select('item_code')
            ->like('item_code', $prefix . $year . $month)
            ->orderBy('item_code', 'DESC')
            ->first();
        
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode['item_code'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get item with full details and relationships
     */
    public function getItemWithDetails($itemId = null)
    {
        $hasSubcategory = $this->db->fieldExists('subcategory_id', $this->table);
        $subcategorySelect = $hasSubcategory ? 'subcategories.category_name as subcategory_name,' : 'NULL as subcategory_name,';

        $builder = $this->select('items.*, 
                                 categories.category_name,
                                 ' . $subcategorySelect . '
                                 suppliers.supplier_name as preferred_supplier_name,
                                 COALESCE(stock.current_stock, 0) as current_stock,
                                 COALESCE(stock.reserved_stock, 0) as reserved_stock,
                                 COALESCE(stock.available_stock, 0) as available_stock')
            ->join('categories', 'categories.id = items.category_id', 'left');

        if ($hasSubcategory) {
            $builder->join('categories as subcategories', 'subcategories.id = items.subcategory_id', 'left');
        }

        $builder->join('suppliers', 'suppliers.id = items.preferred_supplier_id', 'left')
            ->join('(SELECT item_id, 
                            SUM(CASE WHEN status = "available" THEN quantity ELSE 0 END) as current_stock,
                            SUM(CASE WHEN status = "reserved" THEN quantity ELSE 0 END) as reserved_stock,
                            SUM(CASE WHEN status = "available" THEN quantity ELSE 0 END) - SUM(CASE WHEN status = "reserved" THEN quantity ELSE 0 END) as available_stock
                     FROM stock GROUP BY item_id) as stock', 'stock.item_id = items.id', 'left');
        
        if ($itemId) {
            $builder->where('items.id', $itemId);
            return $builder->first();
        }
        
        return $builder->orderBy('items.item_name', 'ASC')->findAll();
    }

    /**
     * Get items by category
     */
    public function getItemsByCategory($categoryId, $includeSubcategories = true)
    {
        $builder = $this->where('items.status', 'active');
        
        if ($includeSubcategories) {
            $builder->groupStart()
                ->where('items.category_id', $categoryId)
                ->orWhere('items.subcategory_id', $categoryId)
                ->groupEnd();
        } else {
            $builder->where('items.category_id', $categoryId);
        }
        
        return $builder->orderBy('items.item_name', 'ASC')->findAll();
    }

    /**
     * Get items by type
     */
    public function getItemsByType($itemType)
    {
        return $this->where('material_type', $itemType)
            ->where('status', 'active')
            ->orderBy('item_name', 'ASC')
            ->findAll();
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems()
    {
        $db = \Config\Database::connect();
        
        // Build subquery for stock calculation
        $subquery = $db->table('stock')
            ->select('item_id, SUM(CASE WHEN status = "available" AND transaction_type != "out" THEN quantity ELSE 0 END) as current_stock')
            ->groupBy('item_id')
            ->getCompiledSelect(false);
        
        return $this->select('items.*, 
                             COALESCE(stock_summary.current_stock, 0) as current_stock,
                             categories.category_name')
            ->join('categories', 'categories.id = items.category_id', 'left')
            ->join("({$subquery}) as stock_summary", 'stock_summary.item_id = items.id', 'left')
            ->where('items.status', 'active')
            ->having('current_stock <= items.reorder_level')
            ->orderBy('current_stock', 'ASC')
            ->findAll();
    }

    /**
     * Get items needing reorder
     */
    public function getReorderItems()
    {
        $db = \Config\Database::connect();
        
        // Build subquery for stock calculation - use proper escaping
        $subquery = $db->table('stock')
            ->select('item_id, SUM(CASE WHEN status = \'available\' AND transaction_type != \'out\' THEN quantity ELSE 0 END) as current_stock')
            ->groupBy('item_id')
            ->getCompiledSelect(false);
        
        return $this->select('items.*, 
                             COALESCE(stock_summary.current_stock, 0) as current_stock,
                             (items.reorder_level - COALESCE(stock_summary.current_stock, 0)) as reorder_quantity,
                             categories.category_name,
                             suppliers.supplier_name as preferred_supplier_name')
            ->join('categories', 'categories.id = items.category_id', 'left')
            ->join('suppliers', 'suppliers.id = items.preferred_supplier_id', 'left')
            ->join("({$subquery}) as stock_summary", 'stock_summary.item_id = items.id', 'left')
            ->where('items.status', 'active')
            ->having('current_stock <= items.reorder_level')
            ->orderBy('(items.reorder_level - current_stock)', 'DESC')
            ->findAll();
    }

    /**
     * SQL fragment "… AS {$as}" picking the best unit column present on `items` (avoids unknown-column errors).
     */
    public static function sqlItemsUnitAs(string $as = 'unit_of_measurement'): string
    {
        static $fragment = null;
        if ($fragment !== null) {
            return str_replace('__AS__', $as, $fragment);
        }

        try {
            $fields = \Config\Database::connect()->getFieldNames('items');
        } catch (\Throwable $e) {
            return "'' AS {$as}";
        }

        $cols = [];
        foreach (['uom', 'unit', 'unit_of_measure'] as $c) {
            if (in_array($c, $fields, true)) {
                $cols[] = 'items.' . $c;
            }
        }

        if ($cols === []) {
            $fragment = "'' AS __AS__";
        } elseif (count($cols) === 1) {
            $fragment = $cols[0] . ' AS __AS__';
        } else {
            $fragment = 'COALESCE(' . implode(', ', $cols) . ') AS __AS__';
        }

        return str_replace('__AS__', $as, $fragment);
    }

    /**
     * Search items
     */
    public function searchItems($search, $filters = [])
    {
        $builder = $this->select('items.*, 
                                 categories.category_name,
                                 suppliers.supplier_name as preferred_supplier_name')
            ->join('categories', 'categories.id = items.category_id', 'left')
            ->join('suppliers', 'suppliers.id = items.preferred_supplier_id', 'left');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('items.item_name', $search)
                ->orLike('items.item_code', $search)
                ->orLike('items.description', $search)
                ->orLike('items.barcode', $search)
                ->orLike('items.hsn_code', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['type'])) {
            $builder->where('items.material_type', $filters['type']);
        }
        
        if (!empty($filters['category_id'])) {
            $builder->where('items.category_id', $filters['category_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('items.status', $filters['status']);
        }
        
        if (!empty($filters['supplier_id'])) {
            $builder->where('items.preferred_supplier_id', $filters['supplier_id']);
        }
        
        return $builder->orderBy('items.item_name', 'ASC')->findAll();
    }

    /**
     * Get all active items
     */
    public function getActiveItems()
    {
        return $this->where('status', 'active')
            ->orderBy('item_name', 'ASC')
            ->findAll();
    }

    /**
     * Get item statistics
     */
    public function getItemStats()
    {
        $stats = [
            'total_items' => $this->countAll(),
            'active_items' => $this->where('status', 'active')->countAllResults(),
            'raw_materials' => $this->where('material_type', 'raw_material')->where('status', 'active')->countAllResults(),
            'finished_goods' => $this->where('material_type', 'finished_goods')->where('status', 'active')->countAllResults(),
            'consumables' => $this->where('material_type', 'packaging')->where('status', 'active')->countAllResults(),
            'low_stock_items' => count($this->getLowStockItems()),
            'items_needing_reorder' => count($this->getReorderItems())
        ];
        
        return $stats;
    }

    /**
     * Get items by supplier
     */
    public function getItemsBySupplier($supplierId)
    {
        return $this->where('preferred_supplier_id', $supplierId)
            ->where('status', 'active')
            ->orderBy('item_name', 'ASC')
            ->findAll();
    }

    /**
     * Update item stock levels
     */
    public function updateStockLevels($itemId)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }
        
        // Get current stock from stock table
        $stockModel = new Stock();
        $currentStock = $stockModel->getItemStock($itemId);
        
        // Update reorder status
        $needsReorder = $currentStock['available_stock'] <= $item['reorder_level'];
        
        // You can add logic here to auto-create purchase requisition
        if ($needsReorder && $item['preferred_supplier_id']) {
            // Auto-create PR logic can be implemented here
        }
        
        return true;
    }

    /**
     * Export items to CSV
     */
    public function exportToCSV($filters = [])
    {
        $items = $this->searchItems('', $filters);
        
        $csv = "Item Code,Item Name,Type,Category,Subcategory,Description,UOM,HSN Code,SAC Code,Reorder Level,Safety Stock,Min Stock,Max Stock,Standard Cost,Selling Price,Preferred Supplier,Barcode,RFID Tag,Weight,Dimensions,Shelf Life,Storage Conditions,Hazardous,Status\n";
        
        foreach ($items as $item) {
            $csv .= "{$item['item_code']},{$item['item_name']}," . (isset($item['material_type']) ? $item['material_type'] : 'N/A') . "," . 
                    (isset($item['category_name']) ? $item['category_name'] : 'N/A') . "," .
                    (isset($item['subcategory_name']) ? $item['subcategory_name'] : 'N/A') . "," .
                    (isset($item['description']) ? $item['description'] : 'N/A') . "," .
                    "{$item['uom']}," .
                    (isset($item['hsn_code']) ? $item['hsn_code'] : 'N/A') . "," .
                    (isset($item['sac_code']) ? $item['sac_code'] : 'N/A') . "," .
                    "{$item['reorder_level']},{$item['safety_stock']},{$item['min_stock']}," .
                    (isset($item['max_stock']) ? $item['max_stock'] : 'N/A') . "," .
                    (isset($item['standard_cost']) ? $item['standard_cost'] : 'N/A') . "," .
                    (isset($item['selling_price']) ? $item['selling_price'] : 'N/A') . "," .
                    (isset($item['preferred_supplier_name']) ? $item['preferred_supplier_name'] : 'N/A') . "," .
                    (isset($item['barcode']) ? $item['barcode'] : 'N/A') . "," .
                    (isset($item['rfid_tag']) ? $item['rfid_tag'] : 'N/A') . "," .
                    (isset($item['weight']) ? $item['weight'] : 'N/A') . "," .
                    (isset($item['dimensions']) ? $item['dimensions'] : 'N/A') . "," .
                    (isset($item['shelf_life_days']) ? $item['shelf_life_days'] : 'N/A') . "," .
                    (isset($item['storage_conditions']) ? $item['storage_conditions'] : 'N/A') . "," .
                    ($item['hazardous'] ? 'Yes' : 'No') . ",{$item['status']}\n";
        }
        
        return $csv;
    }
}
