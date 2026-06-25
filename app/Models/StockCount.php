<?php

namespace App\Models;

use CodeIgniter\Model;

class StockCount extends Model
{
    protected $table = 'stock_counts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'count_number',
        'count_date',
        'warehouse_id',
        'location_id',
        'count_type',
        'count_method',
        'count_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'total_items',
        'total_counted',
        'total_variance',
        'variance_percentage',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'count_number' => 'required|is_unique[stock_counts.count_number,id,{id}]',
        'count_date' => 'required|valid_date',
        'warehouse_id' => 'required|integer',
        'count_type' => 'required|in_list[cycle,annual,spot,random]',
        'count_method' => 'required|in_list[manual,barcode,rfid,hybrid]',
        'count_status' => 'required|in_list[draft,in_progress,completed,approved,cancelled]',
        'approval_status' => 'required|in_list[pending,approved,rejected]'
    ];

    protected $validationMessages = [
        'count_number' => [
            'required' => 'Count number is required',
            'is_unique' => 'Count number must be unique'
        ],
        'warehouse_id' => [
            'required' => 'Warehouse is required',
            'integer' => 'Invalid warehouse ID'
        ],
        'count_type' => [
            'required' => 'Count type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
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

    public function items()
    {
        return $this->hasMany('App\Models\StockCountItem', 'count_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('stock_counts.*, warehouses.warehouse_name, warehouse_locations.location_name, users.username as approved_by_name, creators.username as created_by_name')
                        ->join('warehouses', 'warehouses.id = stock_counts.warehouse_id')
                        ->join('warehouse_locations', 'warehouse_locations.id = stock_counts.location_id', 'left')
                        ->join('users', 'users.id = stock_counts.approved_by', 'left')
                        ->join('users creators', 'creators.id = stock_counts.created_by', 'left');

        if ($id) {
            return $builder->where('stock_counts.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->orderBy('count_date', 'DESC')
                    ->findAll();
    }

    public function getByLocation($locationId)
    {
        return $this->where('location_id', $locationId)
                    ->orderBy('count_date', 'DESC')
                    ->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('count_status', $status)
                    ->orderBy('count_date', 'DESC')
                    ->findAll();
    }

    public function getByApprovalStatus($approvalStatus)
    {
        return $this->where('approval_status', $approvalStatus)
                    ->orderBy('count_date', 'DESC')
                    ->findAll();
    }

    public function getPendingCounts()
    {
        return $this->where('count_status', 'draft')
                    ->where('approval_status', 'pending')
                    ->orderBy('count_date', 'ASC')
                    ->findAll();
    }

    public function getInProgressCounts()
    {
        return $this->where('count_status', 'in_progress')
                    ->orderBy('count_date', 'ASC')
                    ->findAll();
    }

    public function getCompletedCounts()
    {
        return $this->where('count_status', 'completed')
                    ->orderBy('count_date', 'DESC')
                    ->findAll();
    }

    public function getCountStats($warehouseId = null)
    {
        $builder = $this->select('count_status, COUNT(*) as count, AVG(variance_percentage) as avg_variance');
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->groupBy('count_status')->findAll();
    }

    public function getCountAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(count_date) as date, COUNT(*) as count_count, AVG(variance_percentage) as avg_variance')
                        ->groupBy('DATE(count_date)');
        
        if ($startDate) {
            $builder->where('count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('count_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function generateCountNumber()
    {
        $prefix = 'SC';
        $year = date('Y');
        $month = date('m');
        
        $lastCount = $this->where('count_number LIKE', $prefix . $year . $month . '%')
                          ->orderBy('count_number', 'DESC')
                          ->first();

        if ($lastCount) {
            $lastNumber = intval(substr($lastCount['count_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function createCount($data)
    {
        $countData = [
            'count_number' => $this->generateCountNumber(),
            'count_date' => isset($data['count_date']) ? $data['count_date'] : date('Y-m-d'),
            'warehouse_id' => $data['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'count_type' => isset($data['count_type']) ? $data['count_type'] : 'cycle',
            'count_method' => isset($data['count_method']) ? $data['count_method'] : 'manual',
            'count_status' => 'draft',
            'approval_status' => 'pending',
            'total_items' => 0,
            'total_counted' => 0,
            'total_variance' => 0,
            'variance_percentage' => 0,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($countData);
    }

    public function startCount($countId)
    {
        return $this->update($countId, [
            'count_status' => 'in_progress',
            'notes' => 'Count started'
        ]);
    }

    public function completeCount($countId)
    {
        // Calculate totals from count items
        $countItems = model('StockCountItem')->getByCount($countId);
        
        $totalItems = count($countItems);
        $totalCounted = 0;
        $totalVariance = 0;

        foreach ($countItems as $item) {
            $totalCounted += $item['counted_quantity'];
            $totalVariance += abs($item['variance_quantity']);
        }

        $variancePercentage = $totalItems > 0 ? ($totalVariance / $totalCounted) * 100 : 0;

        return $this->update($countId, [
            'count_status' => 'completed',
            'total_items' => $totalItems,
            'total_counted' => $totalCounted,
            'total_variance' => $totalVariance,
            'variance_percentage' => round($variancePercentage, 2),
            'notes' => 'Count completed'
        ]);
    }

    public function approveCount($countId, $approvedBy, $notes = '')
    {
        return $this->update($countId, [
            'approval_status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
    }

    public function rejectCount($countId, $rejectionReason)
    {
        return $this->update($countId, [
            'approval_status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'count_status' => 'cancelled'
        ]);
    }

    public function cancelCount($countId, $reason = '')
    {
        return $this->update($countId, [
            'count_status' => 'cancelled',
            'notes' => $reason ? $reason : 'Count cancelled'
        ]);
    }

    public function getCountTypes()
    {
        return [
            'cycle' => 'Cycle Count',
            'annual' => 'Annual Count',
            'spot' => 'Spot Count',
            'random' => 'Random Count'
        ];
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

    public function getCountStatuses()
    {
        return [
            'draft' => 'Draft',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'approved' => 'Approved',
            'cancelled' => 'Cancelled'
        ];
    }

    public function getApprovalStatuses()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];
    }

    public function getCountSummary($countId)
    {
        $count = $this->find($countId);
        if (!$count) {
            return null;
        }

        $items = model('StockCountItem')->getByCount($countId);
        
        $summary = [
            'count_id' => $countId,
            'count_number' => $count['count_number'],
            'total_items' => count($items),
            'total_counted' => 0,
            'total_variance' => 0,
            'variance_percentage' => 0,
            'status' => $count['count_status'],
            'approval_status' => $count['approval_status']
        ];

        foreach ($items as $item) {
            $summary['total_counted'] += $item['counted_quantity'];
            $summary['total_variance'] += abs($item['variance_quantity']);
        }

        if ($summary['total_counted'] > 0) {
            $summary['variance_percentage'] = round(($summary['total_variance'] / $summary['total_counted']) * 100, 2);
        }

        return $summary;
    }

    public function getCountVarianceReport($countId)
    {
        $count = $this->find($countId);
        if (!$count) {
            return null;
        }

        $items = model('StockCountItem')->getByCount($countId);
        
        $varianceReport = [
            'count_id' => $countId,
            'count_number' => $count['count_number'],
            'count_date' => $count['count_date'],
            'warehouse_id' => $count['warehouse_id'],
            'total_items' => count($items),
            'items_with_variance' => 0,
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
                    'variance_percentage' => $item['variance_percentage']
                ];
            }
        }

        return $varianceReport;
    }

    public function getCountHistory($warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('stock_counts.*, warehouses.warehouse_name')
                        ->join('warehouses', 'warehouses.id = stock_counts.warehouse_id');
        
        if ($warehouseId) {
            $builder->where('stock_counts.warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('stock_counts.count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('stock_counts.count_date <=', $endDate);
        }

        return $builder->orderBy('stock_counts.count_date', 'DESC')->findAll();
    }

    public function getCountPerformance($warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('COUNT(*) as total_counts, AVG(variance_percentage) as avg_variance, MIN(variance_percentage) as min_variance, MAX(variance_percentage) as max_variance')
                        ->where('count_status', 'completed');
        
        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }
        if ($startDate) {
            $builder->where('count_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('count_date <=', $endDate);
        }

        return $builder->first();
    }
}
