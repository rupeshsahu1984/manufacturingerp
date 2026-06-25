<?php

namespace App\Models;

use CodeIgniter\Model;

class StockCountItem extends Model
{
    protected $table = 'stock_count_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'count_id',
        'item_id',
        'batch_id',
        'warehouse_id',
        'location_id',
        'system_quantity',
        'counted_quantity',
        'variance_quantity',
        'unit_cost',
        'variance_value',
        'variance_percentage',
        'count_method',
        'counted_by',
        'counted_at',
        'verified_by',
        'verified_at',
        'verification_status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'count_id' => 'required|integer',
        'item_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'system_quantity' => 'required|numeric|greater_than_equal_to[0]',
        'counted_quantity' => 'required|numeric|greater_than_equal_to[0]',
        'unit_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'count_method' => 'required|in_list[manual,barcode,rfid,hybrid]',
        'verification_status' => 'required|in_list[pending,verified,rejected]'
    ];

    protected $validationMessages = [
        'count_id' => [
            'required' => 'Count ID is required',
            'integer' => 'Invalid count ID'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'system_quantity' => [
            'required' => 'System quantity is required',
            'numeric' => 'System quantity must be a number',
            'greater_than_equal_to' => 'System quantity must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function count()
    {
        return $this->belongsTo('App\Models\StockCount', 'count_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Models\BatchTracking', 'batch_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\WarehouseLocation', 'location_id', 'id');
    }

    public function countedBy()
    {
        return $this->belongsTo('App\Models\User', 'counted_by', 'id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo('App\Models\User', 'verified_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('stock_count_items.*, items.item_code, items.item_name, items.unit_of_measurement, batch_tracking.batch_number, warehouses.warehouse_name, warehouse_locations.location_name, users.username as counted_by_name, verifiers.username as verified_by_name')
                        ->join('items', 'items.id = stock_count_items.item_id')
                        ->join('batch_tracking', 'batch_tracking.id = stock_count_items.batch_id', 'left')
                        ->join('warehouses', 'warehouses.id = stock_count_items.warehouse_id')
                        ->join('warehouse_locations', 'warehouse_locations.id = stock_count_items.location_id', 'left')
                        ->join('users', 'users.id = stock_count_items.counted_by', 'left')
                        ->join('users verifiers', 'verifiers.id = stock_count_items.verified_by', 'left');

        if ($id) {
            return $builder->where('stock_count_items.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByCount($countId)
    {
        return $this->select('stock_count_items.*, items.item_code, items.item_name, items.unit_of_measurement, batch_tracking.batch_number')
                    ->join('items', 'items.id = stock_count_items.item_id')
                    ->join('batch_tracking', 'batch_tracking.id = stock_count_items.batch_id', 'left')
                    ->where('count_id', $countId)
                    ->orderBy('items.item_name', 'ASC')
                    ->findAll();
    }

    public function getByItem($itemId)
    {
        return $this->select('stock_count_items.*, stock_counts.count_number, stock_counts.count_date, stock_counts.status')
                    ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                    ->where('item_id', $itemId)
                    ->orderBy('stock_counts.count_date', 'DESC')
                    ->findAll();
    }

    public function getByBatch($batchId)
    {
        return $this->select('stock_count_items.*, stock_counts.count_number, stock_counts.count_date, stock_counts.status')
                    ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                    ->where('batch_id', $batchId)
                    ->orderBy('stock_counts.count_date', 'DESC')
                    ->findAll();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->select('stock_count_items.*, stock_counts.count_number, stock_counts.count_date, stock_counts.status, items.item_code, items.item_name')
                    ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                    ->join('items', 'items.id = stock_count_items.item_id')
                    ->where('warehouse_id', $warehouseId)
                    ->orderBy('stock_counts.count_date', 'DESC')
                    ->findAll();
    }

    public function getByVerificationStatus($verificationStatus)
    {
        return $this->select('stock_count_items.*, stock_counts.count_number, stock_counts.count_date, items.item_code, items.item_name')
                    ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                    ->join('items', 'items.id = stock_count_items.item_id')
                    ->where('verification_status', $verificationStatus)
                    ->orderBy('stock_counts.count_date', 'DESC')
                    ->findAll();
    }

    public function createCountItem($data)
    {
        // Get system quantity from current stock
        $systemQuantity = model('CurrentStock')->getStockBalance(
            $data['item_id'],
            $data['warehouse_id'],
            isset($data['location_id']) ? $data['location_id'] : null
        );

        $countItemData = [
            'count_id' => $data['count_id'],
            'item_id' => $data['item_id'],
            'batch_id' => isset($data['batch_id']) ? $data['batch_id'] : null,
            'warehouse_id' => $data['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'system_quantity' => $systemQuantity,
            'counted_quantity' => $data['counted_quantity'],
            'variance_quantity' => $data['counted_quantity'] - $systemQuantity,
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : 0,
            'variance_value' => ($data['counted_quantity'] - $systemQuantity) * (isset($data['unit_cost']) ? $data['unit_cost'] : 0),
            'variance_percentage' => $systemQuantity > 0 ? (abs($data['counted_quantity'] - $systemQuantity) / $systemQuantity) * 100 : 0,
            'count_method' => isset($data['count_method']) ? $data['count_method'] : 'manual',
            'counted_by' => isset($data['counted_by']) ? $data['counted_by'] : session()->get('user_id'),
            'counted_at' => date('Y-m-d H:i:s'),
            'verification_status' => 'pending',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($countItemData);
    }

    public function updateCountItem($id, $data)
    {
        $countItem = $this->find($id);
        if (!$countItem) {
            return false;
        }

        $newCountedQuantity = $data['counted_quantity'];
        $systemQuantity = $countItem['system_quantity'];
        $unitCost = isset($data['unit_cost']) ? $data['unit_cost'] : $countItem['unit_cost'];

        $updateData = [
            'counted_quantity' => $newCountedQuantity,
            'variance_quantity' => $newCountedQuantity - $systemQuantity,
            'unit_cost' => $unitCost,
            'variance_value' => ($newCountedQuantity - $systemQuantity) * $unitCost,
            'variance_percentage' => $systemQuantity > 0 ? (abs($newCountedQuantity - $systemQuantity) / $systemQuantity) * 100 : 0,
            'count_method' => isset($data['count_method']) ? $data['count_method'] : $countItem['count_method'],
            'counted_by' => isset($data['counted_by']) ? $data['counted_by'] : session()->get('user_id'),
            'counted_at' => date('Y-m-d H:i:s'),
            'notes' => isset($data['notes']) ? $data['notes'] : $countItem['notes']
        ];

        return $this->update($id, $updateData);
    }

    public function verifyCountItem($id, $verifiedBy, $verificationStatus, $notes = '')
    {
        $updateData = [
            'verification_status' => $verificationStatus,
            'verified_by' => $verifiedBy,
            'verified_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ];

        return $this->update($id, $updateData);
    }

    public function getCountItemSummary($countId)
    {
        $items = $this->getByCount($countId);
        
        $summary = [
            'total_items' => count($items),
            'total_system_quantity' => 0,
            'total_counted_quantity' => 0,
            'total_variance_quantity' => 0,
            'total_variance_value' => 0,
            'items_with_variance' => 0,
            'items_verified' => 0,
            'items_pending_verification' => 0
        ];

        foreach ($items as $item) {
            $summary['total_system_quantity'] += $item['system_quantity'];
            $summary['total_counted_quantity'] += $item['counted_quantity'];
            $summary['total_variance_quantity'] += abs($item['variance_quantity']);
            $summary['total_variance_value'] += abs($item['variance_value']);
            
            if ($item['variance_quantity'] != 0) {
                $summary['items_with_variance']++;
            }
            
            if ($item['verification_status'] == 'verified') {
                $summary['items_verified']++;
            } elseif ($item['verification_status'] == 'pending') {
                $summary['items_pending_verification']++;
            }
        }

        return $summary;
    }

    public function getVarianceReport($countId)
    {
        $items = $this->getByCount($countId);
        
        $varianceReport = [
            'count_id' => $countId,
            'total_items' => count($items),
            'items_with_variance' => 0,
            'items_without_variance' => 0,
            'total_variance_value' => 0,
            'variance_details' => []
        ];

        foreach ($items as $item) {
            if ($item['variance_quantity'] != 0) {
                $varianceReport['items_with_variance']++;
                $varianceReport['total_variance_value'] += abs($item['variance_value']);
                
                $varianceReport['variance_details'][] = [
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'],
                    'system_quantity' => $item['system_quantity'],
                    'counted_quantity' => $item['counted_quantity'],
                    'variance_quantity' => $item['variance_quantity'],
                    'variance_value' => $item['variance_value'],
                    'variance_percentage' => $item['variance_percentage'],
                    'verification_status' => $item['verification_status']
                ];
            } else {
                $varianceReport['items_without_variance']++;
            }
        }

        return $varianceReport;
    }

    public function getCountItemStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('items.item_name, COUNT(*) as count_count, AVG(stock_count_items.variance_percentage) as avg_variance, SUM(ABS(stock_count_items.variance_value)) as total_variance_value')
                        ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                        ->join('items', 'items.id = stock_count_items.item_id')
                        ->groupBy('items.id, items.item_name');
        
        if ($startDate) {
            $builder->where('stock_counts.count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('stock_counts.count_date <=', $endDate);
        }

        return $builder->orderBy('total_variance_value', 'DESC')->findAll();
    }

    public function getCountItemAnalytics($itemId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(stock_counts.count_date) as date, COUNT(*) as item_count, AVG(variance_percentage) as avg_variance, SUM(ABS(variance_value)) as total_variance_value')
                        ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                        ->groupBy('DATE(stock_counts.count_date)');
        
        if ($itemId) {
            $builder->where('stock_count_items.item_id', $itemId);
        }
        if ($startDate) {
            $builder->where('stock_counts.count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('stock_counts.count_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getCountItemByWarehouse($warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('stock_count_items.*, stock_counts.count_number, stock_counts.count_date, stock_counts.status, items.item_code, items.item_name')
                        ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                        ->join('items', 'items.id = stock_count_items.item_id')
                        ->where('stock_count_items.warehouse_id', $warehouseId);
        
        if ($startDate) {
            $builder->where('stock_counts.count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('stock_counts.count_date <=', $endDate);
        }

        return $builder->orderBy('stock_counts.count_date', 'DESC')->findAll();
    }

    public function getCountItemHistory($itemId, $warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('stock_count_items.*, stock_counts.count_number, stock_counts.count_date, stock_counts.status, warehouses.warehouse_name')
                        ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                        ->join('warehouses', 'warehouses.id = stock_count_items.warehouse_id')
                        ->where('stock_count_items.item_id', $itemId);
        
        if ($warehouseId) {
            $builder->where('stock_count_items.warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('stock_counts.count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('stock_counts.count_date <=', $endDate);
        }

        return $builder->orderBy('stock_counts.count_date', 'DESC')->findAll();
    }

    public function getCountMethods()
    {
        return [
            'manual' => 'Manual Count',
            'barcode' => 'Barcode Scan',
            'rfid' => 'RFID Scan',
            'hybrid' => 'Hybrid (Manual + Scan)'
        ];
    }

    public function getVerificationStatuses()
    {
        return [
            'pending' => 'Pending',
            'verified' => 'Verified',
            'rejected' => 'Rejected'
        ];
    }

    public function getHighVarianceItems($countId, $threshold = 10)
    {
        return $this->select('stock_count_items.*, items.item_code, items.item_name')
                    ->join('items', 'items.id = stock_count_items.item_id')
                    ->where('count_id', $countId)
                    ->where('variance_percentage >', $threshold)
                    ->orderBy('variance_percentage', 'DESC')
                    ->findAll();
    }

    public function getCountItemPerformance($warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('COUNT(*) as total_items, AVG(variance_percentage) as avg_variance, MIN(variance_percentage) as min_variance, MAX(variance_percentage) as max_variance, COUNT(CASE WHEN variance_quantity = 0 THEN 1 END) as perfect_matches')
                        ->join('stock_counts', 'stock_counts.id = stock_count_items.count_id')
                        ->where('stock_counts.count_status', 'completed');
        
        if ($warehouseId) {
            $builder->where('stock_count_items.warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('stock_counts.count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('stock_counts.count_date <=', $endDate);
        }

        return $builder->first();
    }
}
