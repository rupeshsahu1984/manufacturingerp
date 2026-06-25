<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardQualityCheck extends Model
{
    protected $table = 'job_card_quality_checks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'job_card_id',
        'check_type',
        'check_method',
        'check_date',
        'inspector_id',
        'sample_size',
        'sample_qty',
        'accepted_qty',
        'rejected_qty',
        'rework_qty',
        'scrap_qty',
        'defect_types',
        'defect_quantities',
        'quality_score',
        'tolerance_min',
        'tolerance_max',
        'actual_value',
        'is_within_tolerance',
        'check_result',
        'check_status',
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
        'job_card_id' => 'required|integer',
        'check_type' => 'required|in_list[visual,dimensional,functional,material,other]',
        'check_method' => 'required|in_list[100_percent,sampling,statistical,automated]',
        'check_date' => 'required|valid_date',
        'inspector_id' => 'required|integer',
        'sample_qty' => 'required|numeric|greater_than[0]',
        'accepted_qty' => 'required|numeric|greater_than_equal_to[0]',
        'rejected_qty' => 'required|numeric|greater_than_equal_to[0]',
        'check_result' => 'required|in_list[pass,fail,conditional_pass]',
        'check_status' => 'required|in_list[pending,in_progress,completed,approved,rejected]'
    ];

    protected $validationMessages = [
        'job_card_id' => [
            'required' => 'Job card is required',
            'integer' => 'Invalid job card ID'
        ],
        'check_type' => [
            'required' => 'Check type is required'
        ],
        'check_method' => [
            'required' => 'Check method is required'
        ],
        'check_date' => [
            'required' => 'Check date is required',
            'valid_date' => 'Invalid check date'
        ],
        'inspector_id' => [
            'required' => 'Inspector is required',
            'integer' => 'Invalid inspector ID'
        ],
        'sample_qty' => [
            'required' => 'Sample quantity is required',
            'numeric' => 'Sample quantity must be a number',
            'greater_than' => 'Sample quantity must be greater than 0'
        ],
        'accepted_qty' => [
            'required' => 'Accepted quantity is required',
            'numeric' => 'Accepted quantity must be a number',
            'greater_than_equal_to' => 'Accepted quantity must be 0 or greater'
        ],
        'rejected_qty' => [
            'required' => 'Rejected quantity is required',
            'numeric' => 'Rejected quantity must be a number',
            'greater_than_equal_to' => 'Rejected quantity must be 0 or greater'
        ],
        'check_result' => [
            'required' => 'Check result is required'
        ],
        'check_status' => [
            'required' => 'Check status is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function jobCard()
    {
        return $this->belongsTo('App\Models\JobCard', 'job_card_id', 'id');
    }

    public function inspector()
    {
        return $this->belongsTo('App\Models\User', 'inspector_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('job_card_quality_checks.*, job_cards.job_card_number, users.username as inspector_name, creators.username as created_by_name')
                        ->join('job_cards', 'job_cards.id = job_card_quality_checks.job_card_id')
                        ->join('users', 'users.id = job_card_quality_checks.inspector_id')
                        ->join('users creators', 'creators.id = job_card_quality_checks.created_by', 'left');

        if ($id) {
            return $builder->where('job_card_quality_checks.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByJobCard($jobCardId)
    {
        return $this->select('job_card_quality_checks.*, users.username as inspector_name')
                    ->join('users', 'users.id = job_card_quality_checks.inspector_id')
                    ->where('job_card_id', $jobCardId)
                    ->orderBy('check_date', 'DESC')
                    ->findAll();
    }

    public function getByInspector($inspectorId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('job_card_quality_checks.*, job_cards.job_card_number, job_cards.status as job_card_status')
                        ->join('job_cards', 'job_cards.id = job_card_quality_checks.job_card_id')
                        ->where('inspector_id', $inspectorId);
        
        if ($startDate) {
            $builder->where('check_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('check_date <=', $endDate);
        }

        return $builder->orderBy('check_date', 'DESC')->findAll();
    }

    public function getByCheckType($checkType, $startDate = null, $endDate = null)
    {
        $builder = $this->where('check_type', $checkType);
        
        if ($startDate) {
            $builder->where('check_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('check_date <=', $endDate);
        }

        return $builder->orderBy('check_date', 'DESC')->findAll();
    }

    public function getByCheckResult($checkResult, $startDate = null, $endDate = null)
    {
        $builder = $this->where('check_result', $checkResult);
        
        if ($startDate) {
            $builder->where('check_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('check_date <=', $endDate);
        }

        return $builder->orderBy('check_date', 'DESC')->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->select('job_card_quality_checks.*, job_cards.job_card_number, users.username as inspector_name')
                    ->join('job_cards', 'job_cards.id = job_card_quality_checks.job_card_id')
                    ->join('users', 'users.id = job_card_quality_checks.inspector_id')
                    ->where('check_date >=', $startDate)
                    ->where('check_date <=', $endDate)
                    ->orderBy('check_date', 'ASC')
                    ->findAll();
    }

    public function getPendingChecks()
    {
        return $this->whereIn('check_status', ['pending', 'in_progress'])
                    ->orderBy('check_date', 'ASC')
                    ->findAll();
    }

    public function createQualityCheck($data)
    {
        $qualityCheckData = [
            'job_card_id' => $data['job_card_id'],
            'check_type' => $data['check_type'],
            'check_method' => $data['check_method'],
            'check_date' => isset($data['check_date']) ? $data['check_date'] : date('Y-m-d H:i:s'),
            'inspector_id' => $data['inspector_id'],
            'sample_size' => isset($data['sample_size']) ? $data['sample_size'] : 0,
            'sample_qty' => $data['sample_qty'],
            'accepted_qty' => $data['accepted_qty'],
            'rejected_qty' => $data['rejected_qty'],
            'rework_qty' => isset($data['rework_qty']) ? $data['rework_qty'] : 0,
            'scrap_qty' => isset($data['scrap_qty']) ? $data['scrap_qty'] : 0,
            'defect_types' => isset($data['defect_types']) ? $data['defect_types'] : '',
            'defect_quantities' => isset($data['defect_quantities']) ? $data['defect_quantities'] : '',
            'quality_score' => isset($data['quality_score']) ? $data['quality_score'] : 0,
            'tolerance_min' => isset($data['tolerance_min']) ? $data['tolerance_min'] : null,
            'tolerance_max' => isset($data['tolerance_max']) ? $data['tolerance_max'] : null,
            'actual_value' => isset($data['actual_value']) ? $data['actual_value'] : null,
            'is_within_tolerance' => isset($data['is_within_tolerance']) ? $data['is_within_tolerance'] : null,
            'check_result' => $data['check_result'],
            'check_status' => isset($data['check_status']) ? $data['check_status'] : 'pending',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'attachments' => isset($data['attachments']) ? $data['attachments'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        // Calculate quality score if not provided
        if (!isset($data['quality_score']) && isset($data['sample_qty']) && $data['sample_qty'] > 0) {
            $qualityCheckData['quality_score'] = ($data['accepted_qty'] / $data['sample_qty']) * 100;
        }

        // Check if within tolerance
        if (isset($data['tolerance_min']) && isset($data['tolerance_max']) && isset($data['actual_value'])) {
            $qualityCheckData['is_within_tolerance'] = ($data['actual_value'] >= $data['tolerance_min'] && $data['actual_value'] <= $data['tolerance_max']) ? 1 : 0;
        }

        return $this->insert($qualityCheckData);
    }

    public function updateQualityCheck($id, $data)
    {
        $qualityCheck = $this->find($id);
        if (!$qualityCheck) {
            return false;
        }

        $updateData = [
            'check_type' => isset($data['check_type']) ? $data['check_type'] : $qualityCheck['check_type'],
            'check_method' => isset($data['check_method']) ? $data['check_method'] : $qualityCheck['check_method'],
            'sample_size' => isset($data['sample_size']) ? $data['sample_size'] : $qualityCheck['sample_size'],
            'sample_qty' => isset($data['sample_qty']) ? $data['sample_qty'] : $qualityCheck['sample_qty'],
            'accepted_qty' => isset($data['accepted_qty']) ? $data['accepted_qty'] : $qualityCheck['accepted_qty'],
            'rejected_qty' => isset($data['rejected_qty']) ? $data['rejected_qty'] : $qualityCheck['rejected_qty'],
            'rework_qty' => isset($data['rework_qty']) ? $data['rework_qty'] : $qualityCheck['rework_qty'],
            'scrap_qty' => isset($data['scrap_qty']) ? $data['scrap_qty'] : $qualityCheck['scrap_qty'],
            'defect_types' => isset($data['defect_types']) ? $data['defect_types'] : $qualityCheck['defect_types'],
            'defect_quantities' => isset($data['defect_quantities']) ? $data['defect_quantities'] : $qualityCheck['defect_quantities'],
            'tolerance_min' => isset($data['tolerance_min']) ? $data['tolerance_min'] : $qualityCheck['tolerance_min'],
            'tolerance_max' => isset($data['tolerance_max']) ? $data['tolerance_max'] : $qualityCheck['tolerance_max'],
            'actual_value' => isset($data['actual_value']) ? $data['actual_value'] : $qualityCheck['actual_value'],
            'check_result' => isset($data['check_result']) ? $data['check_result'] : $qualityCheck['check_result'],
            'check_status' => isset($data['check_status']) ? $data['check_status'] : $qualityCheck['check_status'],
            'notes' => isset($data['notes']) ? $data['notes'] : $qualityCheck['notes'],
            'attachments' => isset($data['attachments']) ? $data['attachments'] : $qualityCheck['attachments']
        ];

        // Recalculate quality score
        if (isset($updateData['sample_qty']) || isset($updateData['accepted_qty'])) {
            $sampleQty = isset($updateData['sample_qty']) ? $updateData['sample_qty'] : $qualityCheck['sample_qty'];
            $acceptedQty = isset($updateData['accepted_qty']) ? $updateData['accepted_qty'] : $qualityCheck['accepted_qty'];
            if ($sampleQty > 0) {
                $updateData['quality_score'] = ($acceptedQty / $sampleQty) * 100;
            }
        }

        // Recheck tolerance
        if (isset($updateData['tolerance_min']) || isset($updateData['tolerance_max']) || isset($updateData['actual_value'])) {
            $toleranceMin = isset($updateData['tolerance_min']) ? $updateData['tolerance_min'] : $qualityCheck['tolerance_min'];
            $toleranceMax = isset($updateData['tolerance_max']) ? $updateData['tolerance_max'] : $qualityCheck['tolerance_max'];
            $actualValue = isset($updateData['actual_value']) ? $updateData['actual_value'] : $qualityCheck['actual_value'];
            
            if ($toleranceMin !== null && $toleranceMax !== null && $actualValue !== null) {
                $updateData['is_within_tolerance'] = ($actualValue >= $toleranceMin && $actualValue <= $toleranceMax) ? 1 : 0;
            }
        }

        return $this->update($id, $updateData);
    }

    public function startQualityCheck($id)
    {
        $qualityCheck = $this->find($id);
        if (!$qualityCheck || $qualityCheck['check_status'] != 'pending') {
            return false;
        }

        $updateData = [
            'check_status' => 'in_progress'
        ];

        return $this->update($id, $updateData);
    }

    public function completeQualityCheck($id, $checkResult, $acceptedQty, $rejectedQty, $reworkQty = 0, $scrapQty = 0)
    {
        $qualityCheck = $this->find($id);
        if (!$qualityCheck || $qualityCheck['check_status'] != 'in_progress') {
            return false;
        }

        $totalQty = $acceptedQty + $rejectedQty + $reworkQty + $scrapQty;
        if ($totalQty != $qualityCheck['sample_qty']) {
            return false; // Quantities must match sample quantity
        }

        $qualityScore = ($acceptedQty / $qualityCheck['sample_qty']) * 100;

        $updateData = [
            'check_status' => 'completed',
            'check_result' => $checkResult,
            'accepted_qty' => $acceptedQty,
            'rejected_qty' => $rejectedQty,
            'rework_qty' => $reworkQty,
            'scrap_qty' => $scrapQty,
            'quality_score' => $qualityScore
        ];

        // Update job card quality status
        $this->updateJobCardQualityStatus($qualityCheck['job_card_id'], $checkResult);

        return $this->update($id, $updateData);
    }

    public function approveQualityCheck($id)
    {
        $qualityCheck = $this->find($id);
        if (!$qualityCheck || $qualityCheck['check_status'] != 'completed') {
            return false;
        }

        $updateData = [
            'check_status' => 'approved'
        ];

        return $this->update($id, $updateData);
    }

    public function rejectQualityCheck($id, $rejectionReason = '')
    {
        $qualityCheck = $this->find($id);
        if (!$qualityCheck || $qualityCheck['check_status'] != 'completed') {
            return false;
        }

        $updateData = [
            'check_status' => 'rejected',
            'notes' => $rejectionReason
        ];

        return $this->update($id, $updateData);
    }

    public function updateJobCardQualityStatus($jobCardId, $checkResult)
    {
        $jobCard = model('JobCard')->find($jobCardId);
        if (!$jobCard) {
            return false;
        }

        $qualityStatus = 'pending';
        switch ($checkResult) {
            case 'pass':
                $qualityStatus = 'passed';
                break;
            case 'fail':
                $qualityStatus = 'failed';
                break;
            case 'conditional_pass':
                $qualityStatus = 'conditional';
                break;
        }

        model('JobCard')->update($jobCardId, [
            'quality_status' => $qualityStatus
        ]);

        return true;
    }

    public function getQualityCheckSummary($jobCardId)
    {
        $qualityChecks = $this->getByJobCard($jobCardId);
        $summary = [
            'total_checks' => count($qualityChecks),
            'passed_checks' => 0,
            'failed_checks' => 0,
            'conditional_checks' => 0,
            'total_sample_qty' => 0,
            'total_accepted_qty' => 0,
            'total_rejected_qty' => 0,
            'total_rework_qty' => 0,
            'total_scrap_qty' => 0,
            'overall_quality_score' => 0,
            'check_type_breakdown' => []
        ];

        $checkTypeBreakdown = [];
        $totalQualityScore = 0;
        $validChecks = 0;

        foreach ($qualityChecks as $check) {
            $summary['total_sample_qty'] += $check['sample_qty'];
            $summary['total_accepted_qty'] += $check['accepted_qty'];
            $summary['total_rejected_qty'] += $check['rejected_qty'];
            $summary['total_rework_qty'] += $check['rework_qty'];
            $summary['total_scrap_qty'] += $check['scrap_qty'];

            switch ($check['check_result']) {
                case 'pass':
                    $summary['passed_checks']++;
                    break;
                case 'fail':
                    $summary['failed_checks']++;
                    break;
                case 'conditional_pass':
                    $summary['conditional_checks']++;
                    break;
            }

            // Track check type breakdown
            $checkType = $check['check_type'];
            if (!isset($checkTypeBreakdown[$checkType])) {
                $checkTypeBreakdown[$checkType] = [
                    'count' => 0,
                    'passed' => 0,
                    'failed' => 0,
                    'conditional' => 0
                ];
            }

            $checkTypeBreakdown[$checkType]['count']++;
            switch ($check['check_result']) {
                case 'pass':
                    $checkTypeBreakdown[$checkType]['passed']++;
                    break;
                case 'fail':
                    $checkTypeBreakdown[$checkType]['failed']++;
                    break;
                case 'conditional_pass':
                    $checkTypeBreakdown[$checkType]['conditional']++;
                    break;
            }

            if ($check['quality_score'] > 0) {
                $totalQualityScore += $check['quality_score'];
                $validChecks++;
            }
        }

        if ($validChecks > 0) {
            $summary['overall_quality_score'] = round($totalQualityScore / $validChecks, 2);
        }

        $summary['check_type_breakdown'] = $checkTypeBreakdown;

        return $summary;
    }

    public function getQualityCheckStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('check_type, check_result, COUNT(*) as count, AVG(quality_score) as avg_quality_score, SUM(sample_qty) as total_sample_qty, SUM(accepted_qty) as total_accepted_qty')
                        ->groupBy('check_type, check_result');
        
        if ($startDate) {
            $builder->where('check_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('check_date <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getQualityCheckAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(check_date) as date, COUNT(*) as check_count, AVG(quality_score) as avg_quality_score, SUM(sample_qty) as total_sample_qty, SUM(accepted_qty) as total_accepted_qty')
                        ->groupBy('DATE(check_date)');
        
        if ($startDate) {
            $builder->where('check_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('check_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getDefectAnalysis($startDate = null, $endDate = null)
    {
        $builder = $this->select('defect_types, COUNT(*) as defect_count, SUM(rejected_qty) as total_rejected_qty, SUM(scrap_qty) as total_scrap_qty')
                        ->where('defect_types !=', '')
                        ->groupBy('defect_types');
        
        if ($startDate) {
            $builder->where('check_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('check_date <=', $endDate);
        }

        return $builder->orderBy('total_rejected_qty', 'DESC')->findAll();
    }

    public function getCheckTypes()
    {
        return [
            'visual' => 'Visual Inspection',
            'dimensional' => 'Dimensional Check',
            'functional' => 'Functional Test',
            'material' => 'Material Analysis',
            'other' => 'Other'
        ];
    }

    public function getCheckMethods()
    {
        return [
            '100_percent' => '100% Inspection',
            'sampling' => 'Sampling',
            'statistical' => 'Statistical Process Control',
            'automated' => 'Automated Testing'
        ];
    }

    public function getCheckResults()
    {
        return [
            'pass' => 'Pass',
            'fail' => 'Fail',
            'conditional_pass' => 'Conditional Pass'
        ];
    }

    public function getCheckStatuses()
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];
    }

    public function exportQualityCheckReport($jobCardId, $format = 'csv')
    {
        $qualityChecks = $this->getByJobCard($jobCardId);
        $jobCard = model('JobCard')->find($jobCardId);
        
        if ($format == 'csv') {
            return $this->exportToCSV($jobCard, $qualityChecks);
        }
        
        return ['job_card' => $jobCard, 'quality_checks' => $qualityChecks];
    }

    private function exportToCSV($jobCard, $qualityChecks)
    {
        $csv = "Quality Check Report\n";
        $csv .= "Job Card: {$jobCard['job_card_number']}\n";
        $csv .= "Status: {$jobCard['status']}\n\n";
        
        $csv .= "Check Type,Method,Date,Inspector,Sample Qty,Accepted Qty,Rejected Qty,Rework Qty,Scrap Qty,Quality Score,Result,Status,Notes\n";
        
        foreach ($qualityChecks as $check) {
            $csv .= "{$check['check_type']},{$check['check_method']},{$check['check_date']},{$check['inspector_name']},{$check['sample_qty']},{$check['accepted_qty']},{$check['rejected_qty']},{$check['rework_qty']},{$check['scrap_qty']},{$check['quality_score']},{$check['check_result']},{$check['check_status']},{$check['notes']}\n";
        }
        
        return $csv;
    }
}
