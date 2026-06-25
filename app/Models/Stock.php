<?php

namespace App\Models;

use CodeIgniter\Model;

class Stock extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'item_id',
        'warehouse_id',
        'batch_number',
        'quantity',
        'unit_cost',
        'total_cost',
        'status',
        'location',
        'rack',
        'bin',
        'expiry_date',
        'manufacturing_date',
        'source_document',
        'source_document_id',
        'source_type',
        'transaction_date',
        'transaction_type',
        'reference_number',
        'notes',
        'approved_by',
        'created_by',
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
        'item_id' => 'required|integer|is_not_unique[items.id]',
        'warehouse_id' => 'required|integer|is_not_unique[warehouses.id]',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'status' => 'required|in_list[available,reserved,blocked,expired,damaged,quarantine]',
        'transaction_type' => 'required|in_list[stock_in,stock_out,transfer_in,transfer_out,adjustment,count]',
        'source_type' => 'permit_empty|in_list[grn,production,sales,transfer,adjustment,count,return]'
    ];

    protected $validationMessages = [
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Item ID must be a number',
            'is_not_unique' => 'Selected item does not exist'
        ],
        'warehouse_id' => [
            'required' => 'Warehouse is required',
            'integer' => 'Warehouse ID must be a number',
            'is_not_unique' => 'Selected warehouse does not exist'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['calculateTotalCost', 'setTransactionDate'];
    protected $beforeUpdate = ['calculateTotalCost'];

    /**
     * Calculate total cost
     */
    protected function calculateTotalCost(array $data)
    {
        if (isset($data['data']['quantity']) && isset($data['data']['unit_cost'])) {
            $data['data']['total_cost'] = $data['data']['quantity'] * $data['data']['unit_cost'];
        }
        return $data;
    }

    /**
     * Set transaction date
     */
    protected function setTransactionDate(array $data)
    {
        if (!isset($data['data']['transaction_date'])) {
            $data['data']['transaction_date'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get current stock for an item
     */
    public function getItemStock($itemId, $warehouseId = null)
    {
        // Only count stock in records (transaction_type='in') for current stock calculation
        // Stock out records (transaction_type='out') are for audit trail only
        $builder = $this->select('item_id, 
                                 warehouse_id,
                                 SUM(CASE WHEN status = "available" AND transaction_type != "out" THEN quantity ELSE 0 END) as available_stock,
                                 SUM(CASE WHEN status = "reserved" AND transaction_type != "out" THEN quantity ELSE 0 END) as reserved_stock,
                                 SUM(CASE WHEN status = "blocked" AND transaction_type != "out" THEN quantity ELSE 0 END) as blocked_stock,
                                 SUM(CASE WHEN status = "quarantine" AND transaction_type != "out" THEN quantity ELSE 0 END) as quarantine_stock,
                                 SUM(CASE WHEN transaction_type != "out" THEN quantity ELSE 0 END) as total_stock,
                                 AVG(CASE WHEN transaction_type != "out" THEN unit_cost ELSE NULL END) as average_cost')
            ->where('item_id', $itemId)
            ->groupBy('item_id');
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }
        
        $result = $builder->first();
        
        if ($result) {
            $result['current_stock'] = $result['available_stock'];
            $result['net_stock'] = $result['available_stock'] - $result['reserved_stock'];
        }
        
        return $result ?: [
            'available_stock' => 0,
            'reserved_stock' => 0,
            'blocked_stock' => 0,
            'quarantine_stock' => 0,
            'total_stock' => 0,
            'current_stock' => 0,
            'net_stock' => 0,
            'average_cost' => 0
        ];
    }

    /**
     * Get current stock for a product (alias for getItemStock)
     */
    public function getCurrentStock($productId, $warehouseId = null)
    {
        $stockInfo = $this->getItemStock($productId, $warehouseId);
        return isset($stockInfo['current_stock']) ? $stockInfo['current_stock'] : 0;
    }

    /**
     * Get available stock for a product
     */
    public function getAvailableStock($productId, $warehouseId = null)
    {
        $stockInfo = $this->getItemStock($productId, $warehouseId);
        return isset($stockInfo['available_stock']) ? $stockInfo['available_stock'] : 0;
    }

    /**
     * Get all warehouses
     */
    public function getWarehouses()
    {
        $warehouseModel = new \App\Models\Warehouse();
        return $warehouseModel->where('status', 'active')->findAll();
    }

    /**
     * Get stock by warehouse
     */
    public function getStockByWarehouse($warehouseId)
    {
        $uomSql = \App\Models\Item::sqlItemsUnitAs('uom');

        return $this->select("stock.*, 
                             items.item_name,
                             items.item_code,
                             {$uomSql},
                             items.reorder_level,
                             categories.category_name", false)
            ->join('items', 'items.id = stock.item_id', 'left')
            ->join('categories', 'categories.id = items.category_id', 'left')
            ->where('stock.warehouse_id', $warehouseId)
            ->where('stock.status', 'available')
            ->where('stock.quantity >', 0)
            ->orderBy('items.item_name', 'ASC')
            ->findAll();
    }

    /**
     * Get stock movements
     */
    public function getStockMovements($filters = [])
    {
        $builder = $this->select('stock.id,
                                 stock.item_id,
                                 stock.warehouse_id,
                                 stock.quantity,
                                 stock.unit_cost,
                                 stock.total_cost,
                                 stock.status,
                                 stock.transaction_type,
                                 stock.transaction_date,
                                 stock.source_document,
                                 stock.source_type,
                                 stock.batch_number,
                                 stock.location,
                                 stock.rack,
                                 stock.bin,
                                 stock.expiry_date,
                                 stock.manufacturing_date,
                                 stock.notes,
                                 stock.created_at,
                                 stock.updated_at,
                                 items.item_name,
                                 items.item_code,
                                 items.unit_of_measure,
                                 items.unit,
                                 items.max_stock,
                                 items.reorder_level,
                                 warehouses.warehouse_name')
            ->join('items', 'items.id = stock.item_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id', 'left');
        
        if (!empty($filters['item_id'])) {
            $builder->where('stock.item_id', $filters['item_id']);
        }
        
        if (!empty($filters['warehouse_id'])) {
            $builder->where('stock.warehouse_id', $filters['warehouse_id']);
        }
        
        if (!empty($filters['transaction_type'])) {
            $builder->where('stock.transaction_type', $filters['transaction_type']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('stock.status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('stock.transaction_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('stock.transaction_date <=', $filters['date_to']);
        }
        
        $movements = $builder->orderBy('stock.id', 'DESC')->orderBy('stock.transaction_date', 'DESC')->findAll();
        
        // Add current_stock for each movement
        foreach ($movements as &$movement) {
            // Get current stock for this item and warehouse
            $currentStockInfo = $this->getItemStock($movement['item_id'], $movement['warehouse_id']);
            $movement['current_stock'] = $currentStockInfo['current_stock'] ?? 0;
            
            // Use unit_of_measure or unit
            $movement['uom'] = $movement['unit_of_measure'] ?? $movement['unit'] ?? '';
        }
        unset($movement); // Unset reference
        
        return $movements;
    }

    /**
     * Add stock (Stock In)
     */
    public function addStock($data)
    {
        $data['transaction_type'] = 'stock_in';
        $data['status'] = 'available';
        
        return $this->insert($data);
    }

    /**
     * Remove stock (Stock Out)
     */
    public function removeStock($itemId, $warehouseId, $quantity, $status = 'available', $reference = null)
    {
        // Get available stock
        $availableStock = $this->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('status', $status)
            ->where('quantity >', 0)
            ->orderBy('created_at', 'ASC') // FIFO
            ->findAll();
        
        if (empty($availableStock)) {
            return false;
        }
        
        $remainingQuantity = $quantity;
        $totalCost = 0;
        
        foreach ($availableStock as $stock) {
            if ($remainingQuantity <= 0) break;
            
            $deductQuantity = min($remainingQuantity, $stock['quantity']);
            $remainingQuantity -= $deductQuantity;
            
            // Update stock quantity
            $newQuantity = $stock['quantity'] - $deductQuantity;
            $totalCost += $deductQuantity * $stock['unit_cost'];
            
            if ($newQuantity > 0) {
                $this->update($stock['id'], ['quantity' => $newQuantity]);
            } else {
                $this->delete($stock['id']);
            }
            
            // Record stock out transaction
            $this->insert([
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'quantity' => $deductQuantity,
                'unit_cost' => $stock['unit_cost'],
                'status' => 'available',
                'transaction_type' => 'stock_out',
                'source_document' => $reference,
                'notes' => 'Stock out - ' . $reference
            ]);
        }
        
        return $remainingQuantity === 0;
    }

    /**
     * Transfer stock between warehouses
     */
    public function transferStock($itemId, $fromWarehouseId, $toWarehouseId, $quantity, $reference = null)
    {
        // Remove from source warehouse
        if (!$this->removeStock($itemId, $fromWarehouseId, $quantity, 'available', $reference)) {
            return false;
        }
        
        // Add to destination warehouse
        $stockData = [
            'item_id' => $itemId,
            'warehouse_id' => $toWarehouseId,
            'quantity' => $quantity,
            'status' => 'available',
            'transaction_type' => 'transfer_in',
            'source_document' => $reference,
            'notes' => 'Transfer from warehouse ' . $fromWarehouseId
        ];
        
        return $this->addStock($stockData);
    }

    /**
     * Reserve stock
     */
    public function reserveStock($itemId, $warehouseId, $quantity, $reference = null)
    {
        // Check if enough stock is available
        $availableStock = $this->getItemStock($itemId, $warehouseId);
        if ($availableStock['available_stock'] < $quantity) {
            return false;
        }
        
        // Create reserved stock entry
        $data = [
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'quantity' => $quantity,
            'status' => 'reserved',
            'transaction_type' => 'adjustment',
            'source_document' => $reference,
            'notes' => 'Stock reserved - ' . $reference
        ];
        
        return $this->insert($data);
    }

    /**
     * Get stock aging report
     */
    public function getStockAgingReport($warehouseId = null)
    {
        $builder = $this->select('stock.*, 
                                 items.item_name,
                                 items.item_code,
                                 warehouses.warehouse_name,
                                 DATEDIFF(CURDATE(), stock.created_at) as days_in_stock')
            ->join('items', 'items.id = stock.item_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id', 'left')
            ->where('stock.status', 'available')
            ->where('stock.quantity >', 0);
        
        if ($warehouseId) {
            $builder->where('stock.warehouse_id', $warehouseId);
        }
        
        $stock = $builder->findAll();
        
        // Categorize by age
        $aging = [
            '0-30' => [],
            '31-60' => [],
            '61-90' => [],
            '91-180' => [],
            '180+' => []
        ];
        
        foreach ($stock as $item) {
            $days = $item['days_in_stock'];
            if ($days <= 30) $aging['0-30'][] = $item;
            elseif ($days <= 60) $aging['31-60'][] = $item;
            elseif ($days <= 90) $aging['61-90'][] = $item;
            elseif ($days <= 180) $aging['91-180'][] = $item;
            else $aging['180+'][] = $item;
        }
        
        return $aging;
    }

    /**
     * Get stock valuation report
     */
    public function getStockValuationReport($valuationMethod = 'fifo')
    {
        $uomSql = \App\Models\Item::sqlItemsUnitAs('uom');
        $items = $this->select("stock.item_id, 
                               items.item_name,
                               items.item_code,
                               {$uomSql},
                               SUM(stock.quantity) as total_quantity,
                               AVG(stock.unit_cost) as average_cost", false)
            ->join('items', 'items.id = stock.item_id', 'left')
            ->where('stock.status', 'available')
            ->where('stock.quantity >', 0)
            ->groupBy('stock.item_id')
            ->findAll();
        
        $totalValue = 0;
        foreach ($items as &$item) {
            $item['total_value'] = $item['total_quantity'] * $item['average_cost'];
            $totalValue += $item['total_value'];
        }
        
        return [
            'items' => $items,
            'total_value' => $totalValue,
            'valuation_method' => $valuationMethod
        ];
    }

    /**
     * Export stock report to CSV
     */
    public function exportStockReport($filters = [])
    {
        $stock = $this->getStockMovements($filters);
        
        $csv = "Date,Item Code,Item Name,Warehouse,Transaction Type,Quantity,Unit Cost,Total Cost,Status,Reference,Notes\n";
        
        foreach ($stock as $item) {
            $csv .= "{$item['transaction_date']},{$item['item_code']},{$item['item_name']},{$item['warehouse_name']},{$item['transaction_type']},{$item['quantity']},{$item['unit_cost']},{$item['total_cost']},{$item['status']},{$item['source_document']},{$item['notes']}\n";
        }
        
        return $csv;
    }

    /**
     * Get stock statistics
     */
    public function getStockStats()
    {
        $db = \Config\Database::connect();
        
        // Get total stock items count
        $totalItems = $db->table('stock')
            ->select('COUNT(DISTINCT item_id) as total_items')
            ->where('transaction_type !=', 'out')
            ->where('status', 'available')
            ->get()
            ->getRowArray();
        
        // Get total quantity
        $totalQuantity = $db->table('stock')
            ->select('SUM(CASE WHEN transaction_type != "out" AND status = "available" THEN quantity ELSE 0 END) as total_quantity')
            ->get()
            ->getRowArray();
        
        // Get total value
        $totalValue = $db->table('stock')
            ->select('SUM(CASE WHEN transaction_type != "out" AND status = "available" THEN total_cost ELSE 0 END) as total_value')
            ->get()
            ->getRowArray();
        
        // Get low stock items count correctly by joining products with current stock summary
        $lowStockCountList = $db->table('products p')
            ->select('p.id, p.product_name, p.reorder_level, COALESCE(s_sum.current_stock, 0) as current_stock')
            ->join('(SELECT product_id, SUM(quantity) as current_stock FROM stock WHERE status = "available" AND transaction_type != "out" GROUP BY product_id) s_sum', 's_sum.product_id = p.id', 'left')
            ->where('p.status', 'active')
            ->having('current_stock < p.reorder_level')
            ->get()
            ->getResultArray();
            
        $lowStockCount = count($lowStockCountList);
        
        return [
            'total_items' => $totalItems['total_items'] ?? 0,
            'total_quantity' => $totalQuantity['total_quantity'] ?? 0,
            'total_value' => $totalValue['total_value'] ?? 0,
            'low_stock_items' => $lowStockCount
        ];
    }
}
