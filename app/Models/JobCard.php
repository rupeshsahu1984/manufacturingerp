<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCard extends Model
{
    protected $table = 'job_cards';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'job_card_number',
        'work_order_id',
        'operation_id',
        'item_id',
        'planned_qty',
        'actual_qty',
        'scrap_qty',
        'rework_qty',
        'good_qty',
        'setup_time_planned',
        'setup_time_actual',
        'run_time_planned',
        'run_time_actual',
        'total_time_planned',
        'total_time_actual',
        'efficiency_pct',
        'start_time',
        'end_time',
        'status',
        'operator_id',
        'workcenter_id',
        'machine_id',
        'tool_id',
        'quality_status',
        'qc_required',
        'qc_passed',
        'qc_failed',
        'notes',
        'attachments',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'job_card_number' => 'required|is_unique[job_cards.job_card_number,id,{id}]',
        'work_order_id' => 'required|integer',
        'operation_id' => 'required|integer',
        'item_id' => 'required|integer',
        'planned_qty' => 'required|numeric|greater_than[0]',
        'setup_time_planned' => 'required|numeric|greater_than_equal_to[0]',
        'run_time_planned' => 'required|numeric|greater_than_equal_to[0]',
        'status' => 'required|in_list[draft,released,in_progress,completed,closed,cancelled]'
    ];

    protected $validationMessages = [
        'job_card_number' => [
            'required' => 'Job card number is required',
            'is_unique' => 'Job card number must be unique'
        ],
        'work_order_id' => [
            'required' => 'Work order is required',
            'integer' => 'Invalid work order ID'
        ],
        'operation_id' => [
            'required' => 'Operation is required',
            'integer' => 'Invalid operation ID'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'planned_qty' => [
            'required' => 'Planned quantity is required',
            'numeric' => 'Planned quantity must be a number',
            'greater_than' => 'Planned quantity must be greater than 0'
        ],
        'setup_time_planned' => [
            'required' => 'Planned setup time is required',
            'numeric' => 'Setup time must be a number',
            'greater_than_equal_to' => 'Setup time must be 0 or greater'
        ],
        'run_time_planned' => [
            'required' => 'Planned run time is required',
            'numeric' => 'Run time must be a number',
            'greater_than_equal_to' => 'Run time must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function workOrder()
    {
        return $this->belongsTo('App\Models\WorkOrder', 'work_order_id', 'id');
    }

    public function operation()
    {
        return $this->belongsTo('App\Models\BOMOperation', 'operation_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id', 'id');
    }

    public function workcenter()
    {
        return $this->belongsTo('App\Models\Workcenter', 'workcenter_id', 'id');
    }

    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'machine_id', 'id');
    }

    public function tool()
    {
        return $this->belongsTo('App\Models\Tool', 'tool_id', 'id');
    }

    public function timeBookings()
    {
        return $this->hasMany('App\Models\JobCardTimeBooking', 'job_card_id', 'id');
    }

    public function materialConsumptions()
    {
        return $this->hasMany('App\Models\JobCardMaterialConsumption', 'job_card_id', 'id');
    }

    public function qualityChecks()
    {
        return $this->hasMany('App\Models\JobCardQualityCheck', 'job_card_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('job_cards.*, work_orders.wo_number, bom_operations.operation_name, items.item_code, items.item_name, users.username as operator_name, workcenters.workcenter_name, machines.machine_name, tools.tool_name')
                        ->join('work_orders', 'work_orders.id = job_cards.work_order_id')
                        ->join('bom_operations', 'bom_operations.id = job_cards.operation_id')
                        ->join('items', 'items.id = job_cards.item_id')
                        ->join('users', 'users.id = job_cards.operator_id', 'left')
                        ->join('workcenters', 'workcenters.id = job_cards.workcenter_id', 'left')
                        ->join('machines', 'machines.id = job_cards.machine_id', 'left')
                        ->join('tools', 'tools.id = job_cards.tool_id', 'left');

        if ($id) {
            return $builder->where('job_cards.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByWorkOrder($workOrderId)
    {
        return $this->select('job_cards.*, bom_operations.operation_name, bom_operations.operation_seq, items.item_code, items.item_name')
                    ->join('bom_operations', 'bom_operations.id = job_cards.operation_id')
                    ->join('items', 'items.id = job_cards.item_id')
                    ->where('work_order_id', $workOrderId)
                    ->orderBy('bom_operations.operation_seq', 'ASC')
                    ->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByOperator($operatorId)
    {
        return $this->where('operator_id', $operatorId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByWorkcenter($workcenterId)
    {
        return $this->where('workcenter_id', $workcenterId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('start_time >=', $startDate)
                    ->where('start_time <=', $endDate)
                    ->orderBy('start_time', 'ASC')
                    ->findAll();
    }

    public function getActiveJobCards()
    {
        return $this->whereIn('status', ['released', 'in_progress'])
                    ->orderBy('start_time', 'ASC')
                    ->findAll();
    }

    public function generateJobCardNumber()
    {
        $prefix = 'JC';
        $year = date('Y');
        $month = date('m');
        
        $lastJC = $this->select('job_card_number')
                       ->like('job_card_number', "{$prefix}{$year}{$month}")
                       ->orderBy('job_card_number', 'DESC')
                       ->first();
        
        if ($lastJC) {
            $lastNumber = intval(substr($lastJC['job_card_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $newNumber);
    }

    public function createJobCard($data)
    {
        $jobCardData = [
            'job_card_number' => isset($data['job_card_number']) ? $data['job_card_number'] : $this->generateJobCardNumber(),
            'work_order_id' => $data['work_order_id'],
            'operation_id' => $data['operation_id'],
            'item_id' => $data['item_id'],
            'planned_qty' => $data['planned_qty'],
            'setup_time_planned' => $data['setup_time_planned'],
            'run_time_planned' => $data['run_time_planned'],
            'total_time_planned' => (isset($data['setup_time_planned']) ? $data['setup_time_planned'] : 0) + ((isset($data['run_time_planned']) ? $data['run_time_planned'] : 0) * (isset($data['planned_qty']) ? $data['planned_qty'] : 1)),
            'status' => isset($data['status']) ? $data['status'] : 'draft',
            'operator_id' => isset($data['operator_id']) ? $data['operator_id'] : null,
            'workcenter_id' => isset($data['workcenter_id']) ? $data['workcenter_id'] : null,
            'machine_id' => isset($data['machine_id']) ? $data['machine_id'] : null,
            'tool_id' => isset($data['tool_id']) ? $data['tool_id'] : null,
            'quality_status' => isset($data['quality_status']) ? $data['quality_status'] : 'pending',
            'qc_required' => isset($data['qc_required']) ? $data['qc_required'] : 0,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'attachments' => isset($data['attachments']) ? $data['attachments'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($jobCardData);
    }

    public function updateJobCard($id, $data)
    {
        $jobCard = $this->find($id);
        if (!$jobCard) {
            return false;
        }

        $updateData = [
            'planned_qty' => isset($data['planned_qty']) ? $data['planned_qty'] : $jobCard['planned_qty'],
            'setup_time_planned' => isset($data['setup_time_planned']) ? $data['setup_time_planned'] : $jobCard['setup_time_planned'],
            'run_time_planned' => isset($data['run_time_planned']) ? $data['run_time_planned'] : $jobCard['run_time_planned'],
            'operator_id' => isset($data['operator_id']) ? $data['operator_id'] : $jobCard['operator_id'],
            'workcenter_id' => isset($data['workcenter_id']) ? $data['workcenter_id'] : $jobCard['workcenter_id'],
            'machine_id' => isset($data['machine_id']) ? $data['machine_id'] : $jobCard['machine_id'],
            'tool_id' => isset($data['tool_id']) ? $data['tool_id'] : $jobCard['tool_id'],
            'qc_required' => isset($data['qc_required']) ? $data['qc_required'] : $jobCard['qc_required'],
            'notes' => isset($data['notes']) ? $data['notes'] : $jobCard['notes'],
            'attachments' => isset($data['attachments']) ? $data['attachments'] : $jobCard['attachments']
        ];

        // Recalculate total planned time
        $updateData['total_time_planned'] = $updateData['setup_time_planned'] + ($updateData['run_time_planned'] * $updateData['planned_qty']);

        return $this->update($id, $updateData);
    }

    public function releaseJobCard($id)
    {
        $jobCard = $this->find($id);
        if (!$jobCard || $jobCard['status'] != 'draft') {
            return false;
        }

        $updateData = [
            'status' => 'released'
        ];

        return $this->update($id, $updateData);
    }

    public function startJobCard($id, $operatorId = null)
    {
        $jobCard = $this->find($id);
        if (!$jobCard || $jobCard['status'] != 'released') {
            return false;
        }

        $updateData = [
            'status' => 'in_progress',
            'start_time' => date('Y-m-d H:i:s'),
            'operator_id' => isset($operatorId) ? $operatorId : $jobCard['operator_id']
        ];

        return $this->update($id, $updateData);
    }

    public function completeJobCard($id, $actualQty, $scrapQty = 0, $reworkQty = 0)
    {
        $jobCard = $this->find($id);
        if (!$jobCard || $jobCard['status'] != 'in_progress') {
            return false;
        }

        $totalQty = $actualQty + $scrapQty + $reworkQty;
        if ($totalQty > $jobCard['planned_qty']) {
            return false; // Cannot complete more than planned
        }

        $endTime = date('Y-m-d H:i:s');
        $totalActualTime = 0;

        if ($jobCard['start_time']) {
            $totalActualTime = strtotime($endTime) - strtotime($jobCard['start_time']);
        }

        $goodQty = $actualQty - $scrapQty - $reworkQty;
        $efficiency = $this->calculateEfficiency($jobCard, $totalActualTime, $goodQty);

        $updateData = [
            'status' => 'completed',
            'end_time' => $endTime,
            'actual_qty' => $actualQty,
            'scrap_qty' => $scrapQty,
            'rework_qty' => $reworkQty,
            'good_qty' => $goodQty,
            'total_time_actual' => $totalActualTime,
            'efficiency_pct' => $efficiency
        ];

        // Update completion quantities
        $this->update($id, $updateData);

        // Process material consumption
        $this->processMaterialConsumption($id, $goodQty);

        // Update work order progress
        $this->updateWorkOrderProgress($jobCard['work_order_id']);

        return true;
    }

    public function closeJobCard($id)
    {
        $jobCard = $this->find($id);
        if (!$jobCard || $jobCard['status'] != 'completed') {
            return false;
        }

        $updateData = [
            'status' => 'closed'
        ];

        return $this->update($id, $updateData);
    }

    public function calculateEfficiency($jobCard, $totalActualTime, $actualQty)
    {
        if ($totalActualTime <= 0 || $jobCard['total_time_planned'] <= 0) {
            return 0;
        }

        $plannedTime = $jobCard['total_time_planned'];
        $efficiency = ($plannedTime / $totalActualTime) * 100;

        return min(100, max(0, round($efficiency, 2)));
    }

    public function processMaterialConsumption($jobCardId, $quantity)
    {
        $jobCard = $this->find($jobCardId);
        if (!$jobCard) {
            return false;
        }

        // Get BOM components for this operation
        $bom = model('BillOfMaterials')->find($jobCard['work_order_id']);
        if (!$bom) {
            return false;
        }

        $components = model('BOMComponent')->getByBOM($bom['id']);
        
        foreach ($components as $component) {
            $consumedQty = model('BOMComponent')->calculateRequiredQuantity(
                $component['qty'] * $quantity,
                $component['scrap_pct'],
                $component['yield_pct']
            );

            // Create material consumption record
            model('JobCardMaterialConsumption')->createConsumption([
                'job_card_id' => $jobCardId,
                'item_id' => $component['component_item_id'],
                'quantity' => $consumedQty,
                'consumption_date' => date('Y-m-d H:i:s')
            ]);
        }

        return true;
    }

    public function updateWorkOrderProgress($workOrderId)
    {
        $workOrder = model('WorkOrder')->find($workOrderId);
        if (!$workOrder) {
            return false;
        }

        $jobCards = $this->getByWorkOrder($workOrderId);
        $totalPlannedQty = 0;
        $totalCompletedQty = 0;

        foreach ($jobCards as $jobCard) {
            $totalPlannedQty += $jobCard['planned_qty'];
            if ($jobCard['status'] == 'completed') {
                $totalCompletedQty += $jobCard['good_qty'];
            }
        }

        if ($totalPlannedQty > 0) {
            $progress = ($totalCompletedQty / $totalPlannedQty) * 100;
            
            // Update work order completion quantity
            model('WorkOrder')->update($workOrderId, [
                'completion_qty' => $totalCompletedQty
            ]);
        }

        return true;
    }

    public function recordTimeBooking($jobCardId, $operatorId, $startTime, $endTime, $activityType, $notes = '')
    {
        $timeData = [
            'job_card_id' => $jobCardId,
            'operator_id' => $operatorId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'activity_type' => $activityType,
            'duration_minutes' => (strtotime($endTime) - strtotime($startTime)) / 60,
            'notes' => $notes
        ];

        return model('JobCardTimeBooking')->createTimeBooking($timeData);
    }

    public function getJobCardStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('status, COUNT(*) as count, AVG(efficiency_pct) as avg_efficiency, AVG(total_time_actual) as avg_time_actual')
                        ->groupBy('status');
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getJobCardAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(start_time) as date, COUNT(*) as job_card_count, AVG(efficiency_pct) as avg_efficiency, SUM(actual_qty) as total_actual_qty')
                        ->groupBy('DATE(start_time)');
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getJobCardEfficiency($jobCardId)
    {
        $jobCard = $this->find($jobCardId);
        if (!$jobCard) {
            return 0;
        }

        if ($jobCard['status'] != 'completed') {
            return 0;
        }

        return isset($jobCard['efficiency_pct']) ? $jobCard['efficiency_pct'] : 0;
    }

    public function getJobCardProgress($jobCardId)
    {
        $jobCard = $this->find($jobCardId);
        if (!$jobCard) {
            return 0;
        }

        if ($jobCard['status'] == 'completed' || $jobCard['status'] == 'closed') {
            return 100;
        }

        if ($jobCard['status'] == 'draft' || $jobCard['status'] == 'released') {
            return 0;
        }

        // Calculate progress based on time elapsed
        if ($jobCard['start_time'] && $jobCard['total_time_planned'] > 0) {
            $elapsedTime = time() - strtotime($jobCard['start_time']);
            $progress = ($elapsedTime / $jobCard['total_time_planned']) * 100;
            return min(100, max(0, round($progress, 2)));
        }

        return 0;
    }

    public function getJobCardStatuses()
    {
        return [
            'draft' => 'Draft',
            'released' => 'Released',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled'
        ];
    }

    public function getQualityStatuses()
    {
        return [
            'pending' => 'Pending',
            'passed' => 'Passed',
            'failed' => 'Failed',
            'rework' => 'Rework'
        ];
    }

    public function getJobCardSummary($workOrderId)
    {
        $jobCards = $this->getByWorkOrder($workOrderId);
        $summary = [
            'total_job_cards' => count($jobCards),
            'completed_job_cards' => 0,
            'in_progress_job_cards' => 0,
            'total_planned_qty' => 0,
            'total_actual_qty' => 0,
            'total_scrap_qty' => 0,
            'total_rework_qty' => 0,
            'total_good_qty' => 0,
            'avg_efficiency' => 0,
            'total_time_planned' => 0,
            'total_time_actual' => 0
        ];

        $totalEfficiency = 0;
        $completedCount = 0;

        foreach ($jobCards as $jobCard) {
            $summary['total_planned_qty'] += $jobCard['planned_qty'];
            $summary['total_time_planned'] += $jobCard['total_time_planned'];

            if ($jobCard['status'] == 'completed') {
                $summary['completed_job_cards']++;
                $summary['total_actual_qty'] += $jobCard['actual_qty'];
                $summary['total_scrap_qty'] += $jobCard['scrap_qty'];
                $summary['total_rework_qty'] += $jobCard['rework_qty'];
                $summary['total_good_qty'] += $jobCard['good_qty'];
                $summary['total_time_actual'] += $jobCard['total_time_actual'];

                if ($jobCard['efficiency_pct'] > 0) {
                    $totalEfficiency += $jobCard['efficiency_pct'];
                    $completedCount++;
                }
            } elseif ($jobCard['status'] == 'in_progress') {
                $summary['in_progress_job_cards']++;
            }
        }

        if ($completedCount > 0) {
            $summary['avg_efficiency'] = round($totalEfficiency / $completedCount, 2);
        }

        return $summary;
    }

    public function exportJobCardReport($workOrderId, $format = 'csv')
    {
        $jobCards = $this->getByWorkOrder($workOrderId);
        $workOrder = model('WorkOrder')->find($workOrderId);
        
        if ($format == 'csv') {
            return $this->exportToCSV($workOrder, $jobCards);
        } elseif ($format == 'excel') {
            return $this->exportToExcel($workOrder, $jobCards);
        }
        
        return ['work_order' => $workOrder, 'job_cards' => $jobCards];
    }

    private function exportToCSV($workOrder, $jobCards)
    {
        $csv = "Job Card Report\n";
        $csv .= "Work Order: {$workOrder['wo_number']}\n";
        $csv .= "Item: {$workOrder['item_id_fg']}\n";
        $csv .= "Planned Quantity: {$workOrder['order_qty']}\n\n";
        
        $csv .= "Job Card,Operation,Planned Qty,Actual Qty,Scrap Qty,Rework Qty,Good Qty,Setup Time,Run Time,Total Time,Efficiency,Status\n";
        
        foreach ($jobCards as $jobCard) {
            $csv .= "{$jobCard['job_card_number']},{$jobCard['operation_name']},{$jobCard['planned_qty']},{$jobCard['actual_qty']},{$jobCard['scrap_qty']},{$jobCard['rework_qty']},{$jobCard['good_qty']},{$jobCard['setup_time_planned']},{$jobCard['run_time_planned']},{$jobCard['total_time_planned']},{$jobCard['efficiency_pct']},{$jobCard['status']}\n";
        }
        
        return $csv;
    }

    private function exportToExcel($workOrder, $jobCards)
    {
        // Implementation for Excel export
        // This would typically use a library like PhpSpreadsheet
        return ['work_order' => $workOrder, 'job_cards' => $jobCards];
    }
}
