<?php

namespace App\Models;

use CodeIgniter\Model;

class BOMOperation extends Model
{
    protected $table = 'bom_operations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bom_id',
        'operation_seq',
        'operation_name',
        'workcenter_id',
        'setup_time',
        'run_time_per_unit',
        'queue_time',
        'move_time',
        'labor_rate',
        'overhead_rate',
        'machine_rate',
        'is_critical',
        'can_split',
        'can_overlap',
        'setup_family',
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
        'bom_id' => 'required|integer',
        'operation_seq' => 'required|integer|greater_than[0]',
        'operation_name' => 'required',
        'workcenter_id' => 'required|integer',
        'setup_time' => 'required|numeric|greater_than_equal_to[0]',
        'run_time_per_unit' => 'required|numeric|greater_than_equal_to[0]',
        'labor_rate' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'overhead_rate' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'machine_rate' => 'permit_empty|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'bom_id' => [
            'required' => 'BOM ID is required',
            'integer' => 'Invalid BOM ID'
        ],
        'operation_seq' => [
            'required' => 'Operation sequence is required',
            'integer' => 'Operation sequence must be a number',
            'greater_than' => 'Operation sequence must be greater than 0'
        ],
        'operation_name' => [
            'required' => 'Operation name is required'
        ],
        'workcenter_id' => [
            'required' => 'Workcenter is required',
            'integer' => 'Invalid workcenter ID'
        ],
        'setup_time' => [
            'required' => 'Setup time is required',
            'numeric' => 'Setup time must be a number',
            'greater_than_equal_to' => 'Setup time must be 0 or greater'
        ],
        'run_time_per_unit' => [
            'required' => 'Run time per unit is required',
            'numeric' => 'Run time must be a number',
            'greater_than_equal_to' => 'Run time must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function bom()
    {
        return $this->belongsTo('App\Models\BillOfMaterials', 'bom_id', 'id');
    }

    public function workcenter()
    {
        return $this->belongsTo('App\Models\Workcenter', 'workcenter_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('bom_operations.*, workcenters.workcenter_name, workcenters.workcenter_code, bill_of_materials.bom_number, bill_of_materials.revision')
                        ->join('workcenters', 'workcenters.id = bom_operations.workcenter_id')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_operations.bom_id');

        if ($id) {
            return $builder->where('bom_operations.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByBOM($bomId)
    {
        return $this->select('bom_operations.*, workcenters.workcenter_name, workcenters.workcenter_code')
                    ->join('workcenters', 'workcenters.id = bom_operations.workcenter_id')
                    ->where('bom_id', $bomId)
                    ->orderBy('operation_seq', 'ASC')
                    ->findAll();
    }

    public function getByWorkcenter($workcenterId)
    {
        return $this->select('bom_operations.*, bill_of_materials.bom_number, bill_of_materials.revision, bill_of_materials.status')
                    ->join('bill_of_materials', 'bill_of_materials.id = bom_operations.bom_id')
                    ->where('workcenter_id', $workcenterId)
                    ->where('bill_of_materials.status', 'released')
                    ->orderBy('operation_seq', 'ASC')
                    ->findAll();
    }

    public function getBySequence($bomId, $operationSeq)
    {
        return $this->where('bom_id', $bomId)
                    ->where('operation_seq', $operationSeq)
                    ->first();
    }

    public function createOperation($data)
    {
        $operationData = [
            'bom_id' => $data['bom_id'],
            'operation_seq' => $data['operation_seq'],
            'operation_name' => $data['operation_name'],
            'workcenter_id' => $data['workcenter_id'],
            'setup_time' => $data['setup_time'],
            'run_time_per_unit' => $data['run_time_per_unit'],
            'queue_time' => isset($data['queue_time']) ? $data['queue_time'] : 0,
            'move_time' => isset($data['move_time']) ? $data['move_time'] : 0,
            'labor_rate' => isset($data['labor_rate']) ? $data['labor_rate'] : 0,
            'overhead_rate' => isset($data['overhead_rate']) ? $data['overhead_rate'] : 0,
            'machine_rate' => isset($data['machine_rate']) ? $data['machine_rate'] : 0,
            'is_critical' => isset($data['is_critical']) ? $data['is_critical'] : 0,
            'can_split' => isset($data['can_split']) ? $data['can_split'] : 0,
            'can_overlap' => isset($data['can_overlap']) ? $data['can_overlap'] : 0,
            'setup_family' => isset($data['setup_family']) ? $data['setup_family'] : '',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'attachments' => isset($data['attachments']) ? $data['attachments'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($operationData);
    }

    public function updateOperation($id, $data)
    {
        $operation = $this->find($id);
        if (!$operation) {
            return false;
        }

        $updateData = [
            'operation_name' => isset($data['operation_name']) ? $data['operation_name'] : $operation['operation_name'],
            'workcenter_id' => isset($data['workcenter_id']) ? $data['workcenter_id'] : $operation['workcenter_id'],
            'setup_time' => isset($data['setup_time']) ? $data['setup_time'] : $operation['setup_time'],
            'run_time_per_unit' => isset($data['run_time_per_unit']) ? $data['run_time_per_unit'] : $operation['run_time_per_unit'],
            'queue_time' => isset($data['queue_time']) ? $data['queue_time'] : $operation['queue_time'],
            'move_time' => isset($data['move_time']) ? $data['move_time'] : $operation['move_time'],
            'labor_rate' => isset($data['labor_rate']) ? $data['labor_rate'] : $operation['labor_rate'],
            'overhead_rate' => isset($data['overhead_rate']) ? $data['overhead_rate'] : $operation['overhead_rate'],
            'machine_rate' => isset($data['machine_rate']) ? $data['machine_rate'] : $operation['machine_rate'],
            'is_critical' => isset($data['is_critical']) ? $data['is_critical'] : $operation['is_critical'],
            'can_split' => isset($data['can_split']) ? $data['can_split'] : $operation['can_split'],
            'can_overlap' => isset($data['can_overlap']) ? $data['can_overlap'] : $operation['can_overlap'],
            'setup_family' => isset($data['setup_family']) ? $data['setup_family'] : $operation['setup_family'],
            'notes' => isset($data['notes']) ? $data['notes'] : $operation['notes'],
            'attachments' => isset($data['attachments']) ? $data['attachments'] : $operation['attachments']
        ];

        return $this->update($id, $updateData);
    }

    public function calculateOperationTime($operationId, $quantity = 1)
    {
        $operation = $this->find($operationId);
        if (!$operation) {
            return 0;
        }

        $totalTime = $operation['setup_time'] + ($operation['run_time_per_unit'] * $quantity);
        return $totalTime;
    }

    public function calculateOperationCost($operationId, $quantity = 1)
    {
        $operation = $this->find($operationId);
        if (!$operation) {
            return 0;
        }

        $totalTime = $this->calculateOperationTime($operationId, $quantity);
        $totalCost = 0;

        if ($operation['labor_rate'] > 0) {
            $totalCost += ($totalTime / 60) * $operation['labor_rate'];
        }

        if ($operation['overhead_rate'] > 0) {
            $totalCost += ($totalTime / 60) * $operation['overhead_rate'];
        }

        if ($operation['machine_rate'] > 0) {
            $totalCost += ($totalTime / 60) * $operation['machine_rate'];
        }

        return $totalCost;
    }

    public function getOperationCosts($bomId, $quantity = 1)
    {
        $operations = $this->getByBOM($bomId);
        $costs = [];

        foreach ($operations as $operation) {
            $costs[] = [
                'operation_id' => $operation['id'],
                'operation_seq' => $operation['operation_seq'],
                'operation_name' => $operation['operation_name'],
                'workcenter_name' => $operation['workcenter_name'],
                'setup_time' => $operation['setup_time'],
                'run_time_per_unit' => $operation['run_time_per_unit'],
                'total_time' => $this->calculateOperationTime($operation['id'], $quantity),
                'labor_cost' => ($operation['labor_rate'] > 0) ? ($operation['labor_rate'] * $this->calculateOperationTime($operation['id'], $quantity) / 60) : 0,
                'overhead_cost' => ($operation['overhead_rate'] > 0) ? ($operation['overhead_rate'] * $this->calculateOperationTime($operation['id'], $quantity) / 60) : 0,
                'machine_cost' => ($operation['machine_rate'] > 0) ? ($operation['machine_rate'] * $this->calculateOperationTime($operation['id'], $quantity) / 60) : 0,
                'total_cost' => $this->calculateOperationCost($operation['id'], $quantity)
            ];
        }

        return $costs;
    }

    public function getCriticalPath($bomId)
    {
        $operations = $this->getByBOM($bomId);
        $criticalPath = [];
        $totalTime = 0;

        foreach ($operations as $operation) {
            if ($operation['is_critical']) {
                $operationTime = $this->calculateOperationTime($operation['id'], 1);
                $totalTime += $operationTime;
                
                $criticalPath[] = [
                    'operation_id' => $operation['id'],
                    'operation_seq' => $operation['operation_seq'],
                    'operation_name' => $operation['operation_name'],
                    'workcenter_name' => $operation['workcenter_name'],
                    'operation_time' => $operationTime,
                    'cumulative_time' => $totalTime
                ];
            }
        }

        return [
            'critical_path' => $criticalPath,
            'total_critical_time' => $totalTime
        ];
    }

    public function getWorkcenterLoad($workcenterId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('bom_operations.*, bill_of_materials.bom_number, bill_of_materials.revision, bill_of_materials.status')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_operations.bom_id')
                        ->where('workcenter_id', $workcenterId)
                        ->where('bill_of_materials.status', 'released');
        
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        $operations = $builder->findAll();
        $totalLoad = 0;

        foreach ($operations as $operation) {
            $totalLoad += $this->calculateOperationTime($operation['id'], 1);
        }

        return [
            'workcenter_id' => $workcenterId,
            'total_operations' => count($operations),
            'total_load_minutes' => $totalLoad,
            'total_load_hours' => $totalLoad / 60
        ];
    }

    public function getSetupFamilyOperations($bomId, $setupFamily)
    {
        return $this->where('bom_id', $bomId)
                    ->where('setup_family', $setupFamily)
                    ->orderBy('operation_seq', 'ASC')
                    ->findAll();
    }

    public function getOperationStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('workcenters.workcenter_name, COUNT(*) as operation_count, AVG(setup_time) as avg_setup_time, AVG(run_time_per_unit) as avg_run_time')
                        ->join('workcenters', 'workcenters.id = bom_operations.workcenter_id')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_operations.bom_id')
                        ->groupBy('workcenters.id, workcenters.workcenter_name');
        
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('operation_count', 'DESC')->findAll();
    }

    public function getOperationAnalytics($workcenterId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(bill_of_materials.created_at) as date, COUNT(*) as operation_count, AVG(setup_time) as avg_setup_time, AVG(run_time_per_unit) as avg_run_time')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_operations.bom_id')
                        ->groupBy('DATE(bill_of_materials.created_at)');
        
        if ($workcenterId) {
            $builder->where('workcenter_id', $workcenterId);
        }
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getLongSetupOperations($bomId, $threshold = 60)
    {
        return $this->where('bom_id', $bomId)
                    ->where('setup_time >', $threshold)
                    ->orderBy('setup_time', 'DESC')
                    ->findAll();
    }

    public function getHighRunTimeOperations($bomId, $threshold = 10)
    {
        return $this->where('bom_id', $bomId)
                    ->where('run_time_per_unit >', $threshold)
                    ->orderBy('run_time_per_unit', 'DESC')
                    ->findAll();
    }

    public function getOperationSequence($bomId)
    {
        return $this->where('bom_id', $bomId)
                    ->orderBy('operation_seq', 'ASC')
                    ->findAll();
    }

    public function reorderOperations($bomId, $newSequence)
    {
        $operations = $this->getByBOM($bomId);
        $success = true;

        foreach ($newSequence as $index => $operationId) {
            $result = $this->update($operationId, ['operation_seq' => $index + 1]);
            if (!$result) {
                $success = false;
            }
        }

        return $success;
    }

    public function validateOperationSequence($bomId)
    {
        $operations = $this->getByBOM($bomId);
        $sequence = [];

        foreach ($operations as $operation) {
            $sequence[] = $operation['operation_seq'];
        }

        sort($sequence);
        $expectedSequence = range(1, count($sequence));

        return $sequence === $expectedSequence;
    }

    public function getOperationDependencies($bomId)
    {
        $operations = $this->getByBOM($bomId);
        $dependencies = [];

        foreach ($operations as $operation) {
            $dependencies[] = [
                'operation_id' => $operation['id'],
                'operation_seq' => $operation['operation_seq'],
                'operation_name' => $operation['operation_name'],
                'workcenter_id' => $operation['workcenter_id'],
                'can_split' => $operation['can_split'],
                'can_overlap' => $operation['can_overlap'],
                'setup_family' => $operation['setup_family'],
                'predecessors' => $this->getPredecessors($bomId, $operation['operation_seq']),
                'successors' => $this->getSuccessors($bomId, $operation['operation_seq'])
            ];
        }

        return $dependencies;
    }

    private function getPredecessors($bomId, $operationSeq)
    {
        if ($operationSeq <= 1) {
            return [];
        }

        return $this->where('bom_id', $bomId)
                    ->where('operation_seq <', $operationSeq)
                    ->orderBy('operation_seq', 'DESC')
                    ->findAll();
    }

    private function getSuccessors($bomId, $operationSeq)
    {
        return $this->where('bom_id', $bomId)
                    ->where('operation_seq >', $operationSeq)
                    ->orderBy('operation_seq', 'ASC')
                    ->findAll();
    }
}
