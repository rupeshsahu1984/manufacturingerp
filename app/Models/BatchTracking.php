<?php

namespace App\Models;

use CodeIgniter\Model;

class BatchTracking extends Model
{
    protected $table = 'batch_tracking';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'batch_number',
        'item_id',
        'warehouse_id',
        'location_id',
        'initial_quantity',
        'current_quantity',
        'manufacturing_date',
        'expiry_date',
        'production_order_id',
        'supplier_id',
        'unit_cost',
        'total_cost',
        'quality_status',
        'batch_status',
        'fifo_sequence',
        'lifo_sequence',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'batch_number' => 'required|is_unique[batch_tracking.batch_number,id,{id}]',
        'item_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'initial_quantity' => 'required|numeric|greater_than[0]',
        'current_quantity' => 'required|numeric|greater_than_equal_to[0]',
        'manufacturing_date' => 'required|valid_date',
        'expiry_date' => 'permit_empty|valid_date',
        'unit_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'quality_status' => 'required|in_list[pending,approved,rejected,quarantine]',
        'batch_status' => 'required|in_list[active,blocked,expired,consumed,recalled]'
    ];

    protected $validationMessages = [
        'batch_number' => [
            'required' => 'Batch number is required',
            'is_unique' => 'Batch number must be unique'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'initial_quantity' => [
            'required' => 'Initial quantity is required',
            'numeric' => 'Initial quantity must be a number',
            'greater_than' => 'Initial quantity must be greater than 0'
        ],
        'current_quantity' => [
            'required' => 'Current quantity is required',
            'numeric' => 'Current quantity must be a number',
            'greater_than_equal_to' => 'Current quantity must be 0 or greater'
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

    public function productionOrder()
    {
        return $this->belongsTo('App\Models\ProductionOrder', 'production_order_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    public function batchMovements()
    {
        return $this->hasMany('App\Models\BatchMovement', 'batch_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('batch_tracking.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name, warehouse_locations.location_name, suppliers.supplier_name')
                        ->join('items', 'items.id = batch_tracking.item_id')
                        ->join('warehouses', 'warehouses.id = batch_tracking.warehouse_id')
                        ->join('warehouse_locations', 'warehouse_locations.id = batch_tracking.location_id', 'left')
                        ->join('suppliers', 'suppliers.id = batch_tracking.supplier_id', 'left');

        if ($id) {
            return $builder->where('batch_tracking.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByItem($itemId, $warehouseId = null)
    {
        $builder = $this->where('item_id', $itemId);
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('manufacturing_date', 'ASC')->findAll();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->where('current_quantity >', 0)
                    ->orderBy('expiry_date', 'ASC')
                    ->findAll();
    }

    public function getByBatchNumber($batchNumber)
    {
        return $this->where('batch_number', $batchNumber)->first();
    }

    public function getExpiringBatches($days = 30)
    {
        $expiryDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->where('expiry_date <=', $expiryDate)
                    ->where('expiry_date >=', date('Y-m-d'))
                    ->where('current_quantity >', 0)
                    ->where('batch_status', 'active')
                    ->orderBy('expiry_date', 'ASC')
                    ->findAll();
    }

    public function getExpiredBatches()
    {
        return $this->where('expiry_date <', date('Y-m-d'))
                    ->where('current_quantity >', 0)
                    ->where('batch_status', 'active')
                    ->orderBy('expiry_date', 'ASC')
                    ->findAll();
    }

    public function getActiveBatches($itemId, $warehouseId)
    {
        return $this->where('item_id', $itemId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('current_quantity >', 0)
                    ->where('batch_status', 'active')
                    ->where('quality_status', 'approved')
                    ->orderBy('manufacturing_date', 'ASC')
                    ->findAll();
    }

    public function getFIFOBatches($itemId, $warehouseId, $quantity)
    {
        $batches = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouse_id)
                        ->where('current_quantity >', 0)
                        ->where('batch_status', 'active')
                        ->where('quality_status', 'approved')
                        ->orderBy('manufacturing_date', 'ASC')
                        ->orderBy('fifo_sequence', 'ASC')
                        ->findAll();

        return $this->allocateBatches($batches, $quantity);
    }

    public function getLIFOBatches($itemId, $warehouseId, $quantity)
    {
        $batches = $this->where('item_id', $itemId)
                        ->where('warehouse_id', $warehouse_id)
                        ->where('current_quantity >', 0)
                        ->where('batch_status', 'active')
                        ->where('quality_status', 'approved')
                        ->orderBy('manufacturing_date', 'DESC')
                        ->orderBy('lifo_sequence', 'DESC')
                        ->findAll();

        return $this->allocateBatches($batches, $quantity);
    }

    private function allocateBatches($batches, $requiredQuantity)
    {
        $allocatedBatches = [];
        $remainingQuantity = $requiredQuantity;

        foreach ($batches as $batch) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $availableQuantity = min($batch['current_quantity'], $remainingQuantity);
            
            $allocatedBatches[] = [
                'batch_id' => $batch['id'],
                'batch_number' => $batch['batch_number'],
                'available_quantity' => $availableQuantity,
                'unit_cost' => $batch['unit_cost'],
                'expiry_date' => $batch['expiry_date']
            ];

            $remainingQuantity -= $availableQuantity;
        }

        return $allocatedBatches;
    }

    public function getBatchStats($itemId = null, $warehouseId = null)
    {
        $builder = $this->select('COUNT(*) as total_batches, SUM(current_quantity) as total_quantity, SUM(total_cost) as total_value');
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->first();
    }

    public function getBatchQualityStats($itemId = null, $warehouseId = null)
    {
        $builder = $this->select('quality_status, COUNT(*) as count, SUM(current_quantity) as total_quantity');
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->groupBy('quality_status')->findAll();
    }

    public function getBatchStatusStats($itemId = null, $warehouseId = null)
    {
        $builder = $this->select('batch_status, COUNT(*) as count, SUM(current_quantity) as total_quantity');
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->groupBy('batch_status')->findAll();
    }

    public function getExpiryAlerts($days = 30)
    {
        $expiryDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->select('batch_tracking.*, items.item_code, items.item_name, warehouses.warehouse_name')
                    ->join('items', 'items.id = batch_tracking.item_id')
                    ->join('warehouses', 'warehouses.id = batch_tracking.warehouse_id')
                    ->where('batch_tracking.expiry_date <=', $expiryDate)
                    ->where('batch_tracking.expiry_date >=', date('Y-m-d'))
                    ->where('batch_tracking.current_quantity >', 0)
                    ->where('batch_tracking.batch_status', 'active')
                    ->orderBy('batch_tracking.expiry_date', 'ASC')
                    ->findAll();
    }

    public function generateBatchNumber($itemId, $prefix = null)
    {
        $item = model('Item')->find($itemId);
        if (!$item) {
            return false;
        }

        if (!$prefix) {
            $prefix = 'BT';
        }

        $date = date('Ymd');
        $itemCode = $item['item_code'];
        
        $lastBatch = $this->where('batch_number LIKE', $prefix . $date . $itemCode . '%')
                          ->orderBy('batch_number', 'DESC')
                          ->first();

        if ($lastBatch) {
            $lastNumber = intval(substr($lastBatch['batch_number'], -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . $itemCode . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function createBatch($data)
    {
        $batchData = [
            'batch_number' => isset($data['batch_number']) ? $data['batch_number'] : $this->generateBatchNumber($data['item_id']),
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'initial_quantity' => $data['quantity'],
            'current_quantity' => $data['quantity'],
            'manufacturing_date' => isset($data['manufacturing_date']) ? $data['manufacturing_date'] : date('Y-m-d'),
            'expiry_date' => isset($data['expiry_date']) ? $data['expiry_date'] : null,
            'production_order_id' => isset($data['production_order_id']) ? $data['production_order_id'] : null,
            'supplier_id' => isset($data['supplier_id']) ? $data['supplier_id'] : null,
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : 0,
            'total_cost' => ($data['quantity'] * (isset($data['unit_cost']) ? $data['unit_cost'] : 0)),
            'quality_status' => isset($data['quality_status']) ? $data['quality_status'] : 'pending',
            'batch_status' => 'active',
            'fifo_sequence' => $this->getNextFIFOSequence($data['item_id']),
            'lifo_sequence' => $this->getNextLIFOSequence($data['item_id']),
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($batchData);
    }

    private function getNextFIFOSequence($itemId)
    {
        $lastBatch = $this->where('item_id', $itemId)
                          ->orderBy('fifo_sequence', 'DESC')
                          ->first();
        
        return (isset($lastBatch['fifo_sequence']) ? $lastBatch['fifo_sequence'] : 0) + 1;
    }

    private function getNextLIFOSequence($itemId)
    {
        $lastBatch = $this->where('item_id', $itemId)
                          ->orderBy('lifo_sequence', 'DESC')
                          ->first();
        
        return (isset($lastBatch['lifo_sequence']) ? $lastBatch['lifo_sequence'] : 0) + 1;
    }

    public function updateBatchQuantity($batchId, $quantity, $operation = 'reduce')
    {
        $batch = $this->find($batchId);
        if (!$batch) {
            return false;
        }

        if ($operation == 'reduce') {
            $newQuantity = max(0, $batch['current_quantity'] - $quantity);
        } else {
            $newQuantity = $batch['current_quantity'] + $quantity;
        }

        $updateData = ['current_quantity' => $newQuantity];
        
        // Update batch status if quantity becomes 0
        if ($newQuantity == 0) {
            $updateData['batch_status'] = 'consumed';
        }

        return $this->update($batchId, $updateData);
    }

    public function blockBatch($batchId, $reason = '')
    {
        return $this->update($batchId, [
            'batch_status' => 'blocked',
            'notes' => $reason ? $reason : 'Batch blocked'
        ]);
    }

    public function unblockBatch($batchId)
    {
        return $this->update($batchId, [
            'batch_status' => 'active',
            'notes' => 'Batch unblocked'
        ]);
    }

    public function recallBatch($batchId, $reason = '')
    {
        return $this->update($batchId, [
            'batch_status' => 'recalled',
            'notes' => $reason ? $reason : 'Batch recalled'
        ]);
    }

    public function getQualityStatuses()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'quarantine' => 'Quarantine'
        ];
    }

    public function getBatchStatuses()
    {
        return [
            'active' => 'Active',
            'blocked' => 'Blocked',
            'expired' => 'Expired',
            'consumed' => 'Consumed',
            'recalled' => 'Recalled'
        ];
    }

    public function getBatchAnalytics($itemId = null, $warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as batch_count, SUM(initial_quantity) as total_quantity, SUM(total_cost) as total_value')
                        ->groupBy('DATE(created_at)');
        
        if ($itemId) {
            $builder->where('item_id', $itemId);
        }
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
