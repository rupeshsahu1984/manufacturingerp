<?php

namespace App\Models;

use CodeIgniter\Model;

class MRPRun extends Model
{
    protected $table = 'mrp_runs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'run_number',
        'run_date',
        'run_type',
        'horizon_start',
        'horizon_end',
        'planning_horizon',
        'frozen_horizon',
        'net_change',
        'regenerative',
        'include_forecast',
        'include_safety_stock',
        'include_work_orders',
        'include_purchase_orders',
        'include_sales_orders',
        'lot_sizing_rule',
        'lead_time_rule',
        'yield_factor',
        'scrap_factor',
        'status',
        'total_items_processed',
        'items_with_requirements',
        'total_planned_orders',
        'execution_time',
        'error_count',
        'warning_count',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'run_number' => 'required|is_unique[mrp_runs.run_number,id,{id}]',
        'run_date' => 'required|valid_date',
        'run_type' => 'required|in_list[net_change,regenerative,simulation]',
        'horizon_start' => 'required|valid_date',
        'horizon_end' => 'required|valid_date',
        'planning_horizon' => 'required|integer|greater_than[0]',
        'frozen_horizon' => 'required|integer|greater_than_equal_to[0]',
        'status' => 'required|in_list[planned,running,completed,failed,cancelled]'
    ];

    protected $validationMessages = [
        'run_number' => [
            'required' => 'MRP run number is required',
            'is_unique' => 'MRP run number must be unique'
        ],
        'run_date' => [
            'required' => 'Run date is required',
            'valid_date' => 'Invalid run date'
        ],
        'run_type' => [
            'required' => 'Run type is required'
        ],
        'horizon_start' => [
            'required' => 'Horizon start date is required',
            'valid_date' => 'Invalid horizon start date'
        ],
        'horizon_end' => [
            'required' => 'Horizon end date is required',
            'valid_date' => 'Invalid horizon end date'
        ],
        'planning_horizon' => [
            'required' => 'Planning horizon is required',
            'integer' => 'Planning horizon must be a number',
            'greater_than' => 'Planning horizon must be greater than 0'
        ],
        'frozen_horizon' => [
            'required' => 'Frozen horizon is required',
            'integer' => 'Frozen horizon must be a number',
            'greater_than_equal_to' => 'Frozen horizon must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function mrpRecords()
    {
        return $this->hasMany('App\Models\MaterialRequirementsPlanning', 'mrp_run_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('mrp_runs.*, users.username as created_by_name')
                        ->join('users', 'users.id = mrp_runs.created_by', 'left');

        if ($id) {
            return $builder->where('mrp_runs.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('run_date', 'DESC')
                    ->findAll();
    }

    public function getByType($runType)
    {
        return $this->where('run_type', $runType)
                    ->orderBy('run_date', 'DESC')
                    ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('run_date >=', $startDate)
                    ->where('run_date <=', $endDate)
                    ->orderBy('run_date', 'DESC')
                    ->findAll();
    }

    public function getLatestRun()
    {
        return $this->orderBy('run_date', 'DESC')
                    ->first();
    }

    public function getSuccessfulRuns($limit = 10)
    {
        return $this->where('status', 'completed')
                    ->orderBy('run_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function generateRunNumber()
    {
        $prefix = 'MRP';
        $year = date('Y');
        $month = date('m');
        
        $lastRun = $this->select('run_number')
                        ->like('run_number', "{$prefix}{$year}{$month}")
                        ->orderBy('run_number', 'DESC')
                        ->first();
        
        if ($lastRun) {
            $lastNumber = intval(substr($lastRun['run_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $newNumber);
    }

    public function createMRPRun($data)
    {
        $mrpRunData = [
            'run_number' => isset($data['run_number']) ? $data['run_number'] : $this->generateRunNumber(),
            'run_date' => isset($data['run_date']) ? $data['run_date'] : date('Y-m-d H:i:s'),
            'run_type' => $data['run_type'],
            'horizon_start' => $data['horizon_start'],
            'horizon_end' => $data['horizon_end'],
            'planning_horizon' => $data['planning_horizon'],
            'frozen_horizon' => $data['frozen_horizon'],
            'net_change' => isset($data['net_change']) ? $data['net_change'] : 0,
            'regenerative' => isset($data['regenerative']) ? $data['regenerative'] : 0,
            'include_forecast' => isset($data['include_forecast']) ? $data['include_forecast'] : 1,
            'include_safety_stock' => isset($data['include_safety_stock']) ? $data['include_safety_stock'] : 1,
            'include_work_orders' => isset($data['include_work_orders']) ? $data['include_work_orders'] : 1,
            'include_purchase_orders' => isset($data['include_purchase_orders']) ? $data['include_purchase_orders'] : 1,
            'include_sales_orders' => isset($data['include_sales_orders']) ? $data['include_sales_orders'] : 1,
            'lot_sizing_rule' => isset($data['lot_sizing_rule']) ? $data['lot_sizing_rule'] : 'lot_for_lot',
            'lead_time_rule' => isset($data['lead_time_rule']) ? $data['lead_time_rule'] : 'standard',
            'yield_factor' => isset($data['yield_factor']) ? $data['yield_factor'] : 1.0,
            'scrap_factor' => isset($data['scrap_factor']) ? $data['scrap_factor'] : 0.0,
            'status' => isset($data['status']) ? $data['status'] : 'planned',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($mrpRunData);
    }

    public function updateMRPRun($id, $data)
    {
        $mrpRun = $this->find($id);
        if (!$mrpRun) {
            return false;
        }

        // Cannot update completed or running MRP runs
        if (in_array($mrpRun['status'], ['completed', 'running'])) {
            return false;
        }

        $updateData = [
            'horizon_start' => isset($data['horizon_start']) ? $data['horizon_start'] : $mrpRun['horizon_start'],
            'horizon_end' => isset($data['horizon_end']) ? $data['horizon_end'] : $mrpRun['horizon_end'],
            'planning_horizon' => isset($data['planning_horizon']) ? $data['planning_horizon'] : $mrpRun['planning_horizon'],
            'frozen_horizon' => isset($data['frozen_horizon']) ? $data['frozen_horizon'] : $mrpRun['frozen_horizon'],
            'include_forecast' => isset($data['include_forecast']) ? $data['include_forecast'] : $mrpRun['include_forecast'],
            'include_safety_stock' => isset($data['include_safety_stock']) ? $data['include_safety_stock'] : $mrpRun['include_safety_stock'],
            'include_work_orders' => isset($data['include_work_orders']) ? $data['include_work_orders'] : $mrpRun['include_work_orders'],
            'include_purchase_orders' => isset($data['include_purchase_orders']) ? $data['include_purchase_orders'] : $mrpRun['include_purchase_orders'],
            'include_sales_orders' => isset($data['include_sales_orders']) ? $data['include_sales_orders'] : $mrpRun['include_sales_orders'],
            'lot_sizing_rule' => isset($data['lot_sizing_rule']) ? $data['lot_sizing_rule'] : $mrpRun['lot_sizing_rule'],
            'lead_time_rule' => isset($data['lead_time_rule']) ? $data['lead_time_rule'] : $mrpRun['lead_time_rule'],
            'yield_factor' => isset($data['yield_factor']) ? $data['yield_factor'] : $mrpRun['yield_factor'],
            'scrap_factor' => isset($data['scrap_factor']) ? $data['scrap_factor'] : $mrpRun['scrap_factor'],
            'notes' => isset($data['notes']) ? $data['notes'] : $mrpRun['notes']
        ];

        return $this->update($id, $updateData);
    }

    public function startMRPRun($id)
    {
        $mrpRun = $this->find($id);
        if (!$mrpRun || $mrpRun['status'] != 'planned') {
            return false;
        }

        $startTime = microtime(true);
        
        $updateData = [
            'status' => 'running',
            'execution_time' => 0
        ];

        $this->update($id, $updateData);

        try {
            // Execute MRP logic
            $result = $this->executeMRPLogic($mrpRun);
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds

            $updateData = [
                'status' => $result['success'] ? 'completed' : 'failed',
                'total_items_processed' => $result['total_items'],
                'items_with_requirements' => $result['items_with_requirements'],
                'total_planned_orders' => $result['total_planned_orders'],
                'execution_time' => $executionTime,
                'error_count' => $result['error_count'],
                'warning_count' => $result['warning_count']
            ];

            return $this->update($id, $updateData);

        } catch (Exception $e) {
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);

            $updateData = [
                'status' => 'failed',
                'execution_time' => $executionTime,
                'error_count' => 1,
                'notes' => 'Error: ' . $e->getMessage()
            ];

            $this->update($id, $updateData);
            return false;
        }
    }

    private function executeMRPLogic($mrpRun)
    {
        $result = [
            'success' => true,
            'total_items' => 0,
            'items_with_requirements' => 0,
            'total_planned_orders' => 0,
            'error_count' => 0,
            'warning_count' => 0
        ];

        // Get all items that need MRP processing
        $items = model('Item')->where('item_type IN', ['raw_material', 'semi_finished', 'finished_goods'])
                              ->where('is_active', 1)
                              ->findAll();

        $result['total_items'] = count($items ?? []);

        foreach ($items as $item) {
            try {
                // Calculate net requirements for this item
                $mrpRecords = model('MaterialRequirementsPlanning')->calculateNetRequirements(
                    $item['id'],
                    $mrpRun['horizon_start'],
                    $mrpRun['horizon_end'],
                    $mrpRun['id']
                );

                if ($mrpRecords) {
                    // Create MRP records
                    foreach ($mrpRecords as $mrpRecord) {
                        model('MaterialRequirementsPlanning')->createMRPRecord($mrpRecord);
                    }

                    // Check if item has net requirements
                    $hasRequirements = false;
                    foreach ($mrpRecords as $mrpRecord) {
                        if ($mrpRecord['net_requirement'] > 0) {
                            $hasRequirements = true;
                            $result['total_planned_orders'] += $mrpRecord['planned_order_receipt'];
                        }
                    }

                    if ($hasRequirements) {
                        $result['items_with_requirements']++;
                    }
                }

            } catch (Exception $e) {
                $result['error_count']++;
                log_message('error', 'MRP Error for Item ID ' . $item['id'] . ': ' . $e->getMessage());
            }
        }

        // Generate planned orders
        if ($result['success']) {
            $plannedOrders = model('MaterialRequirementsPlanning')->generatePlannedOrders($mrpRun['id']);
            $result['total_planned_orders'] = count($plannedOrders);
        }

        return $result;
    }

    public function cancelMRPRun($id)
    {
        $mrpRun = $this->find($id);
        if (!$mrpRun || $mrpRun['status'] != 'planned') {
            return false;
        }

        $updateData = [
            'status' => 'cancelled',
            'notes' => 'Cancelled by user'
        ];

        return $this->update($id, $updateData);
    }

    public function getMRPRunStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('run_type, status, COUNT(*) as count, AVG(execution_time) as avg_execution_time, AVG(total_items_processed) as avg_items_processed')
                        ->groupBy('run_type, status');
        
        if ($startDate) {
            $builder->where('run_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('run_date <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getMRPRunAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(run_date) as date, COUNT(*) as run_count, AVG(execution_time) as avg_execution_time, SUM(total_planned_orders) as total_planned_orders')
                        ->groupBy('DATE(run_date)');
        
        if ($startDate) {
            $builder->where('run_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('run_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getMRPRunPerformance($limit = 10)
    {
        return $this->select('run_number, run_date, execution_time, total_items_processed, items_with_requirements, total_planned_orders, status')
                    ->where('status', 'completed')
                    ->orderBy('execution_time', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getMRPRunErrors($limit = 10)
    {
        return $this->select('run_number, run_date, error_count, warning_count, notes, status')
                    ->where('error_count >', 0)
                    ->orderBy('run_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getMRPRunTypes()
    {
        return [
            'net_change' => 'Net Change',
            'regenerative' => 'Regenerative',
            'simulation' => 'Simulation'
        ];
    }

    public function getMRPRunStatuses()
    {
        return [
            'planned' => 'Planned',
            'running' => 'Running',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled'
        ];
    }

    public function getMRPRunSummary($mrpRunId)
    {
        $mrpRun = $this->find($mrpRunId);
        if (!$mrpRun) {
            return null;
        }

        $mrpRecords = model('MaterialRequirementsPlanning')->getByMRPRun($mrpRunId);
        $summary = [
            'run_number' => $mrpRun['run_number'],
            'run_date' => $mrpRun['run_date'],
            'run_type' => $mrpRun['run_type'],
            'status' => $mrpRun['status'],
            'horizon_start' => $mrpRun['horizon_start'],
            'horizon_end' => $mrpRun['horizon_end'],
            'total_items' => count($mrpRecords),
            'items_with_requirements' => 0,
            'total_net_requirements' => 0,
            'total_planned_orders' => 0,
            'execution_time' => $mrpRun['execution_time'],
            'error_count' => $mrpRun['error_count'],
            'warning_count' => $mrpRun['warning_count']
        ];

        foreach ($mrpRecords as $record) {
            if ($record['net_requirement'] > 0) {
                $summary['items_with_requirements']++;
                $summary['total_net_requirements'] += $record['net_requirement'];
            }
            $summary['total_planned_orders'] += $record['planned_order_receipt'];
        }

        return $summary;
    }

    public function validateMRPRun($id)
    {
        $mrpRun = $this->find($id);
        if (!$mrpRun) {
            return ['valid' => false, 'message' => 'MRP run not found'];
        }

        $errors = [];

        // Check horizon dates
        if (strtotime($mrpRun['horizon_start']) >= strtotime($mrpRun['horizon_end'])) {
            $errors[] = 'Horizon start date must be before horizon end date';
        }

        // Check planning horizon
        $horizonDays = (strtotime($mrpRun['horizon_end']) - strtotime($mrpRun['horizon_start'])) / (24 * 60 * 60);
        if ($horizonDays < $mrpRun['planning_horizon']) {
            $errors[] = 'Planning horizon cannot exceed the date range';
        }

        // Check frozen horizon
        if ($mrpRun['frozen_horizon'] >= $mrpRun['planning_horizon']) {
            $errors[] = 'Frozen horizon must be less than planning horizon';
        }

        if (empty($errors)) {
            return ['valid' => true, 'message' => 'MRP run is valid'];
        } else {
            return ['valid' => false, 'message' => implode(', ', $errors)];
        }
    }

    public function cloneMRPRun($id, $newRunData = [])
    {
        $mrpRun = $this->find($id);
        if (!$mrpRun) {
            return false;
        }

        $cloneData = [
            'run_type' => isset($newRunData['run_type']) ? $newRunData['run_type'] : $mrpRun['run_type'],
            'horizon_start' => isset($newRunData['horizon_start']) ? $newRunData['horizon_start'] : $mrpRun['horizon_start'],
            'horizon_end' => isset($newRunData['horizon_end']) ? $newRunData['horizon_end'] : $mrpRun['horizon_end'],
            'planning_horizon' => isset($newRunData['planning_horizon']) ? $newRunData['planning_horizon'] : $mrpRun['planning_horizon'],
            'frozen_horizon' => isset($newRunData['frozen_horizon']) ? $newRunData['frozen_horizon'] : $mrpRun['frozen_horizon'],
            'include_forecast' => isset($newRunData['include_forecast']) ? $newRunData['include_forecast'] : $mrpRun['include_forecast'],
            'include_safety_stock' => isset($newRunData['include_safety_stock']) ? $newRunData['include_safety_stock'] : $mrpRun['include_safety_stock'],
            'include_work_orders' => isset($newRunData['include_work_orders']) ? $newRunData['include_work_orders'] : $mrpRun['include_work_orders'],
            'include_purchase_orders' => isset($newRunData['include_purchase_orders']) ? $newRunData['include_purchase_orders'] : $mrpRun['include_purchase_orders'],
            'include_sales_orders' => isset($newRunData['include_sales_orders']) ? $newRunData['include_sales_orders'] : $mrpRun['include_sales_orders'],
            'lot_sizing_rule' => isset($newRunData['lot_sizing_rule']) ? $newRunData['lot_sizing_rule'] : $mrpRun['lot_sizing_rule'],
            'lead_time_rule' => isset($newRunData['lead_time_rule']) ? $newRunData['lead_time_rule'] : $mrpRun['lead_time_rule'],
            'yield_factor' => isset($newRunData['yield_factor']) ? $newRunData['yield_factor'] : $mrpRun['yield_factor'],
            'scrap_factor' => isset($newRunData['scrap_factor']) ? $newRunData['scrap_factor'] : $mrpRun['scrap_factor'],
            'notes' => 'Cloned from MRP Run: ' . $mrpRun['run_number']
        ];

        return $this->createMRPRun($cloneData);
    }

    public function exportMRPRunReport($mrpRunId, $format = 'csv')
    {
        $mrpRun = $this->find($mrpRunId);
        if (!$mrpRun) {
            return false;
        }

        $mrpRecords = model('MaterialRequirementsPlanning')->getByMRPRun($mrpRunId);
        
        if ($format == 'csv') {
            return $this->exportToCSV($mrpRun, $mrpRecords);
        } elseif ($format == 'excel') {
            return $this->exportToExcel($mrpRun, $mrpRecords);
        }
        
        return ['mrp_run' => $mrpRun, 'mrp_records' => $mrpRecords];
    }

    private function exportToCSV($mrpRun, $mrpRecords)
    {
        $csv = "MRP Run Report\n";
        $csv .= "Run Number: {$mrpRun['run_number']}\n";
        $csv .= "Run Date: {$mrpRun['run_date']}\n";
        $csv .= "Run Type: {$mrpRun['run_type']}\n";
        $csv .= "Horizon: {$mrpRun['horizon_start']} to {$mrpRun['horizon_end']}\n\n";
        
        $csv .= "Item Code,Item Name,Period Start,Period End,Gross Requirement,Scheduled Receipts,Projected On Hand,Net Requirement,Planned Order Receipt,Planned Order Release,Priority,Status\n";
        
        foreach ($mrpRecords as $record) {
            $csv .= "{$record['item_code']},{$record['item_name']},{$record['period_start']},{$record['period_end']},{$record['gross_requirement']},{$record['scheduled_receipts']},{$record['projected_on_hand']},{$record['net_requirement']},{$record['planned_order_receipt']},{$record['planned_order_release']},{$record['priority']},{$record['status']}\n";
        }
        
        return $csv;
    }

    private function exportToExcel($mrpRun, $mrpRecords)
    {
        // Implementation for Excel export
        // This would typically use a library like PhpSpreadsheet
        return ['mrp_run' => $mrpRun, 'mrp_records' => $mrpRecords];
    }
}
