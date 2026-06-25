<?php

namespace App\Models;

use CodeIgniter\Model;

class BatchMovement extends Model
{
    protected $table = 'batch_movements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'batch_id',
        'movement_type',
        'quantity',
        'from_warehouse_id',
        'from_location_id',
        'to_warehouse_id',
        'to_location_id',
        'movement_date',
        'reference_type',
        'reference_id',
        'reference_number',
        'unit_cost',
        'total_cost',
        'movement_reason',
        'approved_by',
        'approved_at',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'batch_id' => 'required|integer',
        'movement_type' => 'required|in_list[in,out,transfer,adjustment,return,scrap]',
        'quantity' => 'required|numeric|greater_than[0]',
        'movement_date' => 'required|valid_date',
        'reference_type' => 'required|in_list[purchase_order,sales_order,production_order,stock_transfer,stock_count,adjustment,return,scrap]'
    ];

    protected $validationMessages = [
        'batch_id' => [
            'required' => 'Batch ID is required',
            'integer' => 'Invalid batch ID'
        ],
        'movement_type' => [
            'required' => 'Movement type is required'
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
    public function batch()
    {
        return $this->belongsTo('App\Models\BatchTracking', 'batch_id', 'id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'from_warehouse_id', 'id');
    }

    public function fromLocation()
    {
        return $this->belongsTo('App\Models\WarehouseLocation', 'from_location_id', 'id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'to_warehouse_id', 'id');
    }

    public function toLocation()
    {
        return $this->belongsTo('App\Models\WarehouseLocation', 'to_location_id', 'id');
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
        $builder = $this->select('batch_movements.*, batch_tracking.batch_number, items.item_code, items.item_name, from_wh.warehouse_name as from_warehouse_name, from_loc.location_name as from_location_name, to_wh.warehouse_name as to_warehouse_name, to_loc.location_name as to_location_name, users.username as approved_by_name, creators.username as created_by_name')
                        ->join('batch_tracking', 'batch_tracking.id = batch_movements.batch_id')
                        ->join('items', 'items.id = batch_tracking.item_id')
                        ->join('warehouses from_wh', 'from_wh.id = batch_movements.from_warehouse_id', 'left')
                        ->join('warehouse_locations from_loc', 'from_loc.id = batch_movements.from_location_id', 'left')
                        ->join('warehouses to_wh', 'to_wh.id = batch_movements.to_warehouse_id', 'left')
                        ->join('warehouse_locations to_loc', 'to_loc.id = batch_movements.to_location_id', 'left')
                        ->join('users', 'users.id = batch_movements.approved_by', 'left')
                        ->join('users creators', 'creators.id = batch_movements.created_by', 'left');

        if ($id) {
            return $builder->where('batch_movements.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByBatch($batchId)
    {
        return $this->where('batch_id', $batchId)
                    ->orderBy('movement_date', 'ASC')
                    ->findAll();
    }

    public function getByMovementType($movementType)
    {
        return $this->where('movement_type', $movementType)
                    ->orderBy('movement_date', 'DESC')
                    ->findAll();
    }

    public function getByReference($referenceType, $referenceId)
    {
        return $this->where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->orderBy('movement_date', 'ASC')
                    ->findAll();
    }

    public function getByWarehouse($warehouseId, $asSource = true)
    {
        if ($asSource) {
            return $this->where('from_warehouse_id', $warehouseId)
                        ->orderBy('movement_date', 'DESC')
                        ->findAll();
        } else {
            return $this->where('to_warehouse_id', $warehouseId)
                        ->orderBy('movement_date', 'DESC')
                        ->findAll();
        }
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('movement_date >=', $startDate)
                    ->where('movement_date <=', $endDate)
                    ->orderBy('movement_date', 'DESC')
                    ->findAll();
    }

    public function getByItem($itemId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('batch_movements.*, batch_tracking.batch_number')
                        ->join('batch_tracking', 'batch_tracking.id = batch_movements.batch_id')
                        ->where('batch_tracking.item_id', $itemId);
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'DESC')->findAll();
    }

    public function createMovement($data)
    {
        $movementData = [
            'batch_id' => $data['batch_id'],
            'movement_type' => $data['movement_type'],
            'quantity' => $data['quantity'],
            'from_warehouse_id' => isset($data['from_warehouse_id']) ? $data['from_warehouse_id'] : null,
            'from_location_id' => isset($data['from_location_id']) ? $data['from_location_id'] : null,
            'to_warehouse_id' => isset($data['to_warehouse_id']) ? $data['to_warehouse_id'] : null,
            'to_location_id' => isset($data['to_location_id']) ? $data['to_location_id'] : null,
            'movement_date' => isset($data['movement_date']) ? $data['movement_date'] : date('Y-m-d H:i:s'),
            'reference_type' => $data['reference_type'],
            'reference_id' => $data['reference_id'],
            'reference_number' => isset($data['reference_number']) ? $data['reference_number'] : null,
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : 0,
            'total_cost' => $data['quantity'] * (isset($data['unit_cost']) ? $data['unit_cost'] : 0),
            'movement_reason' => isset($data['movement_reason']) ? $data['movement_reason'] : '',
            'approved_by' => isset($data['approved_by']) ? $data['approved_by'] : null,
            'approved_at' => $data['approved_by'] ? date('Y-m-d H:i:s') : null,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($movementData);
    }

    public function approveMovement($movementId, $approvedBy, $notes = '')
    {
        return $this->update($movementId, [
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
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

    public function getMovementAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(movement_date) as date, COUNT(*) as movement_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
                        ->groupBy('DATE(movement_date)');
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getBatchMovementHistory($batchId, $startDate = null, $endDate = null)
    {
        $builder = $this->where('batch_id', $batchId);
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'ASC')->findAll();
    }

    public function getBatchBalance($batchId, $asOfDate = null)
    {
        $builder = $this->select('SUM(CASE WHEN movement_type = "in" THEN quantity ELSE 0 END) as total_in, SUM(CASE WHEN movement_type = "out" THEN quantity ELSE 0 END) as total_out, SUM(CASE WHEN movement_type = "transfer" THEN quantity ELSE 0 END) as total_transfer, SUM(CASE WHEN movement_type = "adjustment" THEN quantity ELSE 0 END) as total_adjustment');
        
        if ($asOfDate) {
            $builder->where('movement_date <=', $asOfDate);
        }

        $result = $builder->where('batch_id', $batchId)->first();
        
        if ($result) {
            $result['net_quantity'] = (isset($result['total_in']) ? $result['total_in'] : 0) - (isset($result['total_out']) ? $result['total_out'] : 0) + (isset($result['total_transfer']) ? $result['total_transfer'] : 0) + (isset($result['total_adjustment']) ? $result['total_adjustment'] : 0);
        }

        return $result;
    }

    public function getMovementTypes()
    {
        return [
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transfer' => 'Stock Transfer',
            'adjustment' => 'Stock Adjustment',
            'return' => 'Stock Return',
            'scrap' => 'Stock Scrap'
        ];
    }

    public function getReferenceTypes()
    {
        return [
            'purchase_order' => 'Purchase Order',
            'sales_order' => 'Sales Order',
            'production_order' => 'Production Order',
            'stock_transfer' => 'Stock Transfer',
            'stock_count' => 'Stock Count',
            'adjustment' => 'Stock Adjustment',
            'return' => 'Stock Return',
            'scrap' => 'Stock Scrap'
        ];
    }

    public function getMovementSummary($batchId)
    {
        $movements = $this->getByBatch($batchId);
        
        $summary = [
            'batch_id' => $batchId,
            'total_movements' => count($movements),
            'total_in' => 0,
            'total_out' => 0,
            'total_transfer' => 0,
            'total_adjustment' => 0,
            'total_cost' => 0
        ];

        foreach ($movements as $movement) {
            switch ($movement['movement_type']) {
                case 'in':
                    $summary['total_in'] += $movement['quantity'];
                    break;
                case 'out':
                    $summary['total_out'] += $movement['quantity'];
                    break;
                case 'transfer':
                    $summary['total_transfer'] += $movement['quantity'];
                    break;
                case 'adjustment':
                    $summary['total_adjustment'] += $movement['quantity'];
                    break;
            }
            $summary['total_cost'] += $movement['total_cost'];
        }

        $summary['net_quantity'] = $summary['total_in'] - $summary['total_out'] + $summary['total_transfer'] + $summary['total_adjustment'];

        return $summary;
    }

    public function getMovementByWarehouse($warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('batch_movements.*, batch_tracking.batch_number, items.item_code, items.item_name')
                        ->join('batch_tracking', 'batch_tracking.id = batch_movements.batch_id')
                        ->join('items', 'items.id = batch_tracking.item_id')
                        ->where('batch_movements.from_warehouse_id', $warehouseId)
                        ->orWhere('batch_movements.to_warehouse_id', $warehouseId);
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('movement_date', 'DESC')->findAll();
    }

    public function getMovementPerformance($startDate = null, $endDate = null)
    {
        $builder = $this->select('COUNT(*) as total_movements, COUNT(CASE WHEN approved_by IS NOT NULL THEN 1 END) as approved_movements, COUNT(CASE WHEN approved_by IS NULL THEN 1 END) as pending_movements, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
                        ->where('movement_date >=', isset($startDate) ? $startDate : date('Y-m-d', strtotime('-30 days')))
                        ->where('movement_date <=', isset($endDate) ? $endDate : date('Y-m-d'));
        
        $result = $builder->first();
        
        if ($result && $result['total_movements'] > 0) {
            $result['approval_rate'] = round(($result['approved_movements'] / $result['total_movements']) * 100, 2);
            $result['pending_rate'] = round(($result['pending_movements'] / $result['total_movements']) * 100, 2);
        } else {
            $result['approval_rate'] = 0;
            $result['pending_rate'] = 0;
        }

        return $result;
    }

    public function getMovementTrends($startDate = null, $endDate = null, $groupBy = 'day')
    {
        $dateFormat = $groupBy == 'hour' ? 'DATE_FORMAT(movement_date, "%Y-%m-%d %H:00:00")' : 'DATE(movement_date)';
        
        $builder = $this->select("{$dateFormat} as period, COUNT(*) as movement_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost")
                        ->groupBy('period');
        
        if ($startDate) {
            $builder->where('movement_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('movement_date <=', $endDate);
        }

        return $builder->orderBy('period', 'ASC')->findAll();
    }
}
