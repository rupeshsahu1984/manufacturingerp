<?php

namespace App\Models;

use CodeIgniter\Model;

class CurrentStock extends Model
{
    protected $table = 'current_stock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'item_id',
        'warehouse_id',
        'location_id',
        'batch_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'unit_cost',
        'total_cost',
        'valuation_method',
        'last_movement_date',
        'last_movement_type',
        'last_movement_quantity',
        'reorder_level',
        'minimum_stock',
        'maximum_stock',
        'safety_stock',
        'stock_status',
        'is_active',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'item_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than_equal_to[0]',
        'reserved_quantity' => 'required|numeric|greater_than_equal_to[0]',
        'available_quantity' => 'required|numeric|greater_than_equal_to[0]',
        'unit_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'valuation_method' => 'required|in_list[fifo,lifo,weighted_average,standard_cost]',
        'stock_status' => 'required|in_list[normal,low,out_of_stock,overstock,expired,blocked]'
    ];

    protected $validationMessages = [
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'warehouse_id' => [
            'required' => 'Warehouse is required',
            'integer' => 'Invalid warehouse ID'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than_equal_to' => 'Quantity must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\WarehouseLocation', 'location_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Models\BatchTracking', 'batch_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('current_stock.*, items.item_code, items.item_name, items.unit_of_measurement, items.reorder_level as item_reorder_level, items.minimum_stock as item_minimum_stock, warehouses.warehouse_name, warehouse_locations.location_name, batch_tracking.batch_number')
                        ->join('items', 'items.id = current_stock.item_id')
                        ->join('warehouses', 'warehouses.id = current_stock.warehouse_id')
                        ->join('warehouse_locations', 'warehouse_locations.id = current_stock.location_id', 'left')
                        ->join('batch_tracking', 'batch_tracking.id = current_stock.batch_id', 'left');

        if ($id) {
            return $builder->where('current_stock.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByItem($itemId, $warehouseId = null)
    {
        $builder = $this->where('item_id', $itemId);
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('warehouse_id', 'ASC')->findAll();
    }

    public function getByWarehouse($warehouseId, $locationId = null)
    {
        $builder = $this->where('warehouse_id', $warehouseId);
        
        if ($locationId) {
            $builder->where('location_id', $locationId);
        }

        return $builder->orderBy('item_id', 'ASC')->findAll();
    }

    public function getByLocation($locationId)
    {
        return $this->where('location_id', $locationId)
                    ->orderBy('item_id', 'ASC')
                    ->findAll();
    }

    public function getByBatch($batchId)
    {
        return $this->where('batch_id', $batchId)->first();
    }

    public function getStockBalance($itemId, $warehouseId, $locationId = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId);
        
        if ($locationId) {
            $builder->where('location_id', $locationId);
        }

        $result = $builder->selectSum('quantity')->first();
        return isset($result['quantity']) ? $result['quantity'] : 0;
    }

    public function getAvailableStock($itemId, $warehouseId, $locationId = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId);
        
        if ($locationId) {
            $builder->where('location_id', $locationId);
        }

        $result = $builder->selectSum('available_quantity')->first();
        return isset($result['available_quantity']) ? $result['available_quantity'] : 0;
    }

    public function getReservedStock($itemId, $warehouseId, $locationId = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId);
        
        if ($locationId) {
            $builder->where('location_id', $locationId);
        }

        $result = $builder->selectSum('reserved_quantity')->first();
        return isset($result['reserved_quantity']) ? $result['reserved_quantity'] : 0;
    }

    public function getStockValue($itemId, $warehouseId, $locationId = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId);
        
        if ($locationId) {
            $builder->where('location_id', $locationId);
        }

        $result = $builder->selectSum('total_cost')->first();
        return isset($result['total_cost']) ? $result['total_cost'] : 0;
    }

    public function getLowStockItems($warehouseId = null)
    {
        $builder = $this->select('current_stock.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name')
                        ->join('items', 'items.id = current_stock.item_id')
                        ->join('warehouses', 'warehouses.id = current_stock.warehouse_id')
                        ->where('current_stock.quantity <= current_stock.reorder_level')
                        ->where('current_stock.quantity >', 0);

        if ($warehouseId) {
            $builder->where('current_stock.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('current_stock.quantity', 'ASC')->findAll();
    }

    public function getOutOfStockItems($warehouseId = null)
    {
        $builder = $this->select('current_stock.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name')
                        ->join('items', 'items.id = current_stock.item_id')
                        ->join('warehouses', 'warehouses.id = current_stock.warehouse_id')
                        ->where('current_stock.quantity', 0);

        if ($warehouseId) {
            $builder->where('current_stock.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('items.item_name', 'ASC')->findAll();
    }

    public function getOverstockItems($warehouseId = null)
    {
        $builder = $this->select('current_stock.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name')
                        ->join('items', 'items.id = current_stock.item_id')
                        ->join('warehouses', 'warehouses.id = current_stock.warehouse_id')
                        ->where('current_stock.quantity > current_stock.maximum_stock')
                        ->where('current_stock.maximum_stock >', 0);

        if ($warehouseId) {
            $builder->where('current_stock.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('current_stock.quantity', 'DESC')->findAll();
    }

    public function getStockByStatus($status, $warehouseId = null)
    {
        $builder = $this->select('current_stock.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name')
                        ->join('items', 'items.id = current_stock.item_id')
                        ->join('warehouses', 'warehouses.id = current_stock.warehouse_id')
                        ->where('current_stock.stock_status', $status);

        if ($warehouseId) {
            $builder->where('current_stock.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('items.item_name', 'ASC')->findAll();
    }

    public function updateStock($itemId, $warehouseId, $locationId, $batchId, $quantity, $operation = 'add', $unitCost = null)
    {
        $stock = $this->where('item_id', $itemId)
                      ->where('warehouse_id', $warehouseId)
                      ->where('location_id', $locationId)
                      ->where('batch_id', $batchId)
                      ->first();

        if (!$stock) {
            // Create new stock record
            $stockData = [
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'batch_id' => $batchId,
                'quantity' => $operation == 'add' ? $quantity : 0,
                'reserved_quantity' => 0,
                'available_quantity' => $operation == 'add' ? $quantity : 0,
                'unit_cost' => isset($unitCost) ? $unitCost : 0,
                'total_cost' => ($operation == 'add' ? $quantity : 0) * (isset($unitCost) ? $unitCost : 0),
                'valuation_method' => 'fifo', // Default method
                'last_movement_date' => date('Y-m-d H:i:s'),
                'last_movement_type' => $operation,
                'last_movement_quantity' => $quantity,
                'reorder_level' => 0,
                'minimum_stock' => 0,
                'maximum_stock' => 0,
                'safety_stock' => 0,
                'stock_status' => 'normal',
                'is_active' => 1,
                'created_by' => session()->get('user_id')
            ];

            return $this->insert($stockData);
        }

        // Update existing stock
        $newQuantity = $operation == 'add' ? $stock['quantity'] + $quantity : max(0, $stock['quantity'] - $quantity);
        $newAvailableQuantity = $operation == 'add' ? $stock['available_quantity'] + $quantity : max(0, $stock['available_quantity'] - $quantity);

        // Update unit cost if provided
        $newUnitCost = isset($unitCost) ? $unitCost : $stock['unit_cost'];
        $newTotalCost = $newQuantity * $newUnitCost;

        // Update stock status
        $newStockStatus = $this->calculateStockStatus($newQuantity, $stock['reorder_level'], $stock['minimum_stock'], $stock['maximum_stock']);

        $updateData = [
            'quantity' => $newQuantity,
            'available_quantity' => $newAvailableQuantity,
            'unit_cost' => $newUnitCost,
            'total_cost' => $newTotalCost,
            'last_movement_date' => date('Y-m-d H:i:s'),
            'last_movement_type' => $operation,
            'last_movement_quantity' => $quantity,
            'stock_status' => $newStockStatus
        ];

        return $this->update($stock['id'], $updateData);
    }

    private function calculateStockStatus($quantity, $reorderLevel, $minimumStock, $maximumStock)
    {
        if ($quantity == 0) {
            return 'out_of_stock';
        } elseif ($quantity <= $minimumStock) {
            return 'low';
        } elseif ($quantity <= $reorderLevel) {
            return 'low';
        } elseif ($maximumStock > 0 && $quantity > $maximumStock) {
            return 'overstock';
        } else {
            return 'normal';
        }
    }

    public function reserveStock($itemId, $warehouseId, $locationId, $batchId, $quantity)
    {
        $stock = $this->where('item_id', $itemId)
                      ->where('warehouse_id', $warehouseId)
                      ->where('location_id', $locationId)
                      ->where('batch_id', $batchId)
                      ->first();

        if (!$stock || $stock['available_quantity'] < $quantity) {
            return false;
        }

        $newReservedQuantity = $stock['reserved_quantity'] + $quantity;
        $newAvailableQuantity = $stock['available_quantity'] - $quantity;

        return $this->update($stock['id'], [
            'reserved_quantity' => $newReservedQuantity,
            'available_quantity' => $newAvailableQuantity
        ]);
    }

    public function releaseReservedStock($itemId, $warehouseId, $locationId, $batchId, $quantity)
    {
        $stock = $this->where('item_id', $itemId)
                      ->where('warehouse_id', $warehouseId)
                      ->where('location_id', $locationId)
                      ->where('batch_id', $batchId)
                      ->first();

        if (!$stock || $stock['reserved_quantity'] < $quantity) {
            return false;
        }

        $newReservedQuantity = $stock['reserved_quantity'] - $quantity;
        $newAvailableQuantity = $stock['available_quantity'] + $quantity;

        return $this->update($stock['id'], [
            'reserved_quantity' => $newReservedQuantity,
            'available_quantity' => $newAvailableQuantity
        ]);
    }

    public function getStockStats($warehouseId = null)
    {
        $builder = $this->select('COUNT(*) as total_items, SUM(quantity) as total_quantity, SUM(total_cost) as total_value, COUNT(CASE WHEN stock_status = "low" THEN 1 END) as low_stock_items, COUNT(CASE WHEN stock_status = "out_of_stock" THEN 1 END) as out_of_stock_items, COUNT(CASE WHEN stock_status = "overstock" THEN 1 END) as overstock_items');
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->first();
    }

    public function getStockAging($warehouseId = null, $days = 30)
    {
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $builder = $this->select('current_stock.*, items.item_code, items.item_name, warehouses.warehouse_name')
                        ->join('items', 'items.id = current_stock.item_id')
                        ->join('warehouses', 'warehouses.id = current_stock.warehouse_id')
                        ->where('current_stock.last_movement_date <', $cutoffDate)
                        ->where('current_stock.quantity >', 0);

        if ($warehouseId) {
            $builder->where('current_stock.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('current_stock.last_movement_date', 'ASC')->findAll();
    }

    public function getStockByValuationMethod($valuationMethod, $warehouseId = null)
    {
        $builder = $this->where('valuation_method', $valuationMethod);
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('item_id', 'ASC')->findAll();
    }

    public function updateStockStatus($itemId, $warehouseId, $locationId, $batchId)
    {
        $stock = $this->where('item_id', $itemId)
                      ->where('warehouse_id', $warehouseId)
                      ->where('location_id', $locationId)
                      ->where('batch_id', $batchId)
                      ->first();

        if (!$stock) {
            return false;
        }

        $newStockStatus = $this->calculateStockStatus(
            $stock['quantity'],
            $stock['reorder_level'],
            $stock['minimum_stock'],
            $stock['maximum_stock']
        );

        return $this->update($stock['id'], ['stock_status' => $newStockStatus]);
    }

    public function getStockStatuses()
    {
        return [
            'normal' => 'Normal',
            'low' => 'Low Stock',
            'out_of_stock' => 'Out of Stock',
            'overstock' => 'Overstock',
            'expired' => 'Expired',
            'blocked' => 'Blocked'
        ];
    }

    public function getValuationMethods()
    {
        return [
            'fifo' => 'FIFO (First In First Out)',
            'lifo' => 'LIFO (Last In First Out)',
            'weighted_average' => 'Weighted Average Cost',
            'standard_cost' => 'Standard Cost'
        ];
    }

    public function getStockAnalytics($warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as item_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_value')
                        ->groupBy('DATE(created_at)');
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }
}
