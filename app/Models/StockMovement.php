<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'movement_number',
        'movement_date',
        'item_id',
        'warehouse_id',
        'location_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'source_type',
        'source_id',
        'source_reference',
        'destination_type',
        'destination_id',
        'destination_reference',
        'batch_number',
        'expiry_date',
        'manufacturing_date',
        'reason',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'movement_number' => 'required|is_unique[stock_movements.movement_number,id,{id}]',
        'movement_date' => 'required|valid_date',
        'item_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'movement_type' => 'required|in_list[in,out,transfer,adjustment]',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'source_type' => 'required|in_list[purchase_grn,production_output,stock_transfer,returns,sales_dispatch,production_consumption,stock_transfer_out,scrap_wastage,adjustment,other]',
        'status' => 'required|in_list[pending,approved,completed,cancelled]'
    ];

    protected $validationMessages = [
        'movement_number' => [
            'required' => 'Movement number is required',
            'is_unique' => 'Movement number must be unique'
        ],
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
            'greater_than' => 'Quantity must be greater than 0'
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

    public function approvedBy()
    {
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('stock_movements.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name, warehouse_locations.location_name, users.username as approved_by_name')
                        ->join('items', 'items.id = stock_movements.item_id')
                        ->join('warehouses', 'warehouses.id = stock_movements.warehouse_id')
                        ->join('warehouse_locations', 'warehouse_locations.id = stock_movements.location_id', 'left')
                        ->join('users', 'users.id = stock_movements.approved_by', 'left');

        if ($id) {
            return $builder->where('stock_movements.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByItem($itemId, $warehouseId = null)
    {
        $builder = $this->where('item_id', $itemId);
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('movement_date', 'DESC')->findAll();
    }

    public function getByWarehouse($warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->where('warehouse_id', $warehouseId);
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'DESC')->findAll();
    }

    public function getByType($movementType, $startDate = null, $endDate = null)
    {
        $builder = $this->where('movement_type', $movementType);
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'DESC')->findAll();
    }

    public function getBySource($sourceType, $sourceId)
    {
        return $this->where('source_type', $sourceType)
                    ->where('source_id', $sourceId)
                    ->orderBy('movement_date', 'ASC')
                    ->findAll();
    }

    public function getStockInMovements($itemId, $warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId)
                        ->where('movement_type', 'in');
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'ASC')->findAll();
    }

    public function getStockOutMovements($itemId, $warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId)
                        ->where('movement_type', 'out');
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'ASC')->findAll();
    }

    public function getStockLedger($itemId, $warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->where('item_id', $itemId);
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'ASC')->findAll();
    }

    public function getStockBalance($itemId, $warehouseId, $asOfDate = null)
    {
        $builder = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouseId);
        
        if ($asOfDate) {
            $builder->where('movement_date <=', $asOfDate);
        }

        $movements = $builder->findAll();
        
        $balance = 0;
        foreach ($movements as $movement) {
            if ($movement['movement_type'] == 'in') {
                $balance += $movement['quantity'];
            } else {
                $balance -= $movement['quantity'];
            }
        }

        return $balance;
    }

    public function getStockValue($itemId, $warehouseId, $asOfDate = null)
    {
        $balance = $this->getStockBalance($itemId, $warehouseId, $asOfDate);
        
        // Get the latest unit cost for this item
        $latestMovement = $this->where('item_id', $itemId)
                               ->where('warehouse_id', $warehouseId)
                               ->where('unit_cost >', 0)
                               ->orderBy('movement_date', 'DESC')
                               ->first();
        
        if ($latestMovement && $latestMovement['unit_cost']) {
            return $balance * $latestMovement['unit_cost'];
        }

        return 0;
    }

    public function getMovementStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('movement_type, COUNT(*) as count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
                        ->groupBy('movement_type');
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getWarehouseMovementStats($warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('items.item_name, SUM(CASE WHEN movement_type = "in" THEN quantity ELSE 0 END) as total_in, SUM(CASE WHEN movement_type = "out" THEN quantity ELSE 0 END) as total_out')
                        ->join('items', 'items.id = stock_movements.item_id')
                        ->where('warehouse_id', $warehouseId)
                        ->groupBy('items.id, items.item_name');
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('items.item_name', 'ASC')->findAll();
    }

    public function generateMovementNumber()
    {
        $prefix = 'SM';
        $year = date('Y');
        $month = date('m');
        
        $lastMovement = $this->where('movement_number LIKE', $prefix . $year . $month . '%')
                             ->orderBy('movement_number', 'DESC')
                             ->first();

        if ($lastMovement) {
            $lastNumber = intval(substr($lastMovement['movement_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function createStockIn($data)
    {
        $movementData = [
            'movement_number' => $this->generateMovementNumber(),
            'movement_date' => isset($data['movement_date']) ? $data['movement_date'] : date('Y-m-d'),
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'movement_type' => 'in',
            'quantity' => $data['quantity'],
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : 0,
            'total_cost' => ($data['quantity'] * (isset($data['unit_cost']) ? $data['unit_cost'] : 0)),
            'source_type' => $data['source_type'],
            'source_id' => isset($data['source_id']) ? $data['source_id'] : null,
            'source_reference' => isset($data['source_reference']) ? $data['source_reference'] : null,
            'batch_number' => isset($data['batch_number']) ? $data['batch_number'] : null,
            'expiry_date' => isset($data['expiry_date']) ? $data['expiry_date'] : null,
            'manufacturing_date' => isset($data['manufacturing_date']) ? $data['manufacturing_date'] : null,
            'reason' => isset($data['reason']) ? $data['reason'] : 'Stock In',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'status' => 'completed',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($movementData);
    }

    public function createStockOut($data)
    {
        $movementData = [
            'movement_number' => $this->generateMovementNumber(),
            'movement_date' => isset($data['movement_date']) ? $data['movement_date'] : date('Y-m-d'),
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'movement_type' => 'out',
            'quantity' => $data['quantity'],
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : 0,
            'total_cost' => ($data['quantity'] * (isset($data['unit_cost']) ? $data['unit_cost'] : 0)),
            'destination_type' => $data['destination_type'],
            'destination_id' => isset($data['destination_id']) ? $data['destination_id'] : null,
            'destination_reference' => isset($data['destination_reference']) ? $data['destination_reference'] : null,
            'batch_number' => isset($data['batch_number']) ? $data['batch_number'] : null,
            'reason' => isset($data['reason']) ? $data['reason'] : 'Stock Out',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'status' => 'completed',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($movementData);
    }

    public function getMovementTypes()
    {
        return [
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transfer' => 'Stock Transfer',
            'adjustment' => 'Stock Adjustment'
        ];
    }

    public function getSourceTypes()
    {
        return [
            'purchase_grn' => 'Purchase GRN',
            'production_output' => 'Production Output',
            'stock_transfer' => 'Stock Transfer',
            'returns' => 'Returns',
            'sales_dispatch' => 'Sales Dispatch',
            'production_consumption' => 'Production Consumption',
            'stock_transfer_out' => 'Stock Transfer Out',
            'scrap_wastage' => 'Scrap/Wastage',
            'adjustment' => 'Adjustment',
            'other' => 'Other'
        ];
    }

    public function getMovementStatuses()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
    }

    public function approveMovement($movementId, $approvedBy)
    {
        return $this->update($movementId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function completeMovement($movementId)
    {
        return $this->update($movementId, ['status' => 'completed']);
    }

    public function cancelMovement($movementId)
    {
        return $this->update($movementId, ['status' => 'cancelled']);
    }
}
