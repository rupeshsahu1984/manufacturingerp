<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardTimeBooking extends Model
{
    protected $table = 'job_card_time_bookings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'job_card_id',
        'operator_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'activity_type',
        'break_type',
        'break_reason',
        'machine_id',
        'tool_id',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'job_card_id' => 'required|integer',
        'operator_id' => 'required|integer',
        'start_time' => 'required|valid_date',
        'end_time' => 'required|valid_date',
        'duration_minutes' => 'required|numeric|greater_than[0]',
        'activity_type' => 'required|in_list[setup,production,maintenance,break,idle,other]'
    ];

    protected $validationMessages = [
        'job_card_id' => [
            'required' => 'Job card is required',
            'integer' => 'Invalid job card ID'
        ],
        'operator_id' => [
            'required' => 'Operator is required',
            'integer' => 'Invalid operator ID'
        ],
        'start_time' => [
            'required' => 'Start time is required',
            'valid_date' => 'Invalid start time'
        ],
        'end_time' => [
            'required' => 'End time is required',
            'valid_date' => 'Invalid end time'
        ],
        'duration_minutes' => [
            'required' => 'Duration is required',
            'numeric' => 'Duration must be a number',
            'greater_than' => 'Duration must be greater than 0'
        ],
        'activity_type' => [
            'required' => 'Activity type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function jobCard()
    {
        return $this->belongsTo('App\Models\JobCard', 'job_card_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id', 'id');
    }

    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'machine_id', 'id');
    }

    public function tool()
    {
        return $this->belongsTo('App\Models\Tool', 'tool_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('job_card_time_bookings.*, job_cards.job_card_number, users.username as operator_name, machines.machine_name, tools.tool_name')
                        ->join('job_cards', 'job_cards.id = job_card_time_bookings.job_card_id')
                        ->join('users', 'users.id = job_card_time_bookings.operator_id')
                        ->join('machines', 'machines.id = job_card_time_bookings.machine_id', 'left')
                        ->join('tools', 'tools.id = job_card_time_bookings.tool_id', 'left');

        if ($id) {
            return $builder->where('job_card_time_bookings.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByJobCard($jobCardId)
    {
        return $this->select('job_card_time_bookings.*, users.username as operator_name, machines.machine_name, tools.tool_name')
                    ->join('users', 'users.id = job_card_time_bookings.operator_id')
                    ->join('machines', 'machines.id = job_card_time_bookings.machine_id', 'left')
                    ->join('tools', 'tools.id = job_card_time_bookings.tool_id', 'left')
                    ->where('job_card_id', $jobCardId)
                    ->orderBy('start_time', 'ASC')
                    ->findAll();
    }

    public function getByOperator($operatorId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('job_card_time_bookings.*, job_cards.job_card_number, job_cards.status as job_card_status')
                        ->join('job_cards', 'job_cards.id = job_card_time_bookings.job_card_id')
                        ->where('operator_id', $operatorId);
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->orderBy('start_time', 'ASC')->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->select('job_card_time_bookings.*, job_cards.job_card_number, users.username as operator_name')
                    ->join('job_cards', 'job_cards.id = job_card_time_bookings.job_card_id')
                    ->join('users', 'users.id = job_card_time_bookings.operator_id')
                    ->where('start_time >=', $startDate)
                    ->where('start_time <=', $endDate)
                    ->orderBy('start_time', 'ASC')
                    ->findAll();
    }

    public function getByActivityType($activityType, $startDate = null, $endDate = null)
    {
        $builder = $this->where('activity_type', $activityType);
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->orderBy('start_time', 'ASC')->findAll();
    }

    public function getActiveBookings($operatorId = null)
    {
        $builder = $this->where('end_time IS NULL');
        
        if ($operatorId) {
            $builder->where('operator_id', $operatorId);
        }

        return $builder->orderBy('start_time', 'ASC')->findAll();
    }

    public function createTimeBooking($data)
    {
        $timeData = [
            'job_card_id' => $data['job_card_id'],
            'operator_id' => $data['operator_id'],
            'start_time' => $data['start_time'],
            'end_time' => isset($data['end_time']) ? $data['end_time'] : null,
            'duration_minutes' => isset($data['duration_minutes']) ? $data['duration_minutes'] : 0,
            'activity_type' => $data['activity_type'],
            'break_type' => isset($data['break_type']) ? $data['break_type'] : null,
            'break_reason' => isset($data['break_reason']) ? $data['break_reason'] : null,
            'machine_id' => isset($data['machine_id']) ? $data['machine_id'] : null,
            'tool_id' => isset($data['tool_id']) ? $data['tool_id'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        // Calculate duration if not provided
        if (!isset($data['duration_minutes']) && isset($data['start_time']) && isset($data['end_time'])) {
            $timeData['duration_minutes'] = (strtotime($data['end_time']) - strtotime($data['start_time'])) / 60;
        }

        return $this->insert($timeData);
    }

    public function updateTimeBooking($id, $data)
    {
        $timeBooking = $this->find($id);
        if (!$timeBooking) {
            return false;
        }

        $updateData = [
            'end_time' => isset($data['end_time']) ? $data['end_time'] : $timeBooking['end_time'],
            'break_type' => isset($data['break_type']) ? $data['break_type'] : $timeBooking['break_type'],
            'break_reason' => isset($data['break_reason']) ? $data['break_reason'] : $timeBooking['break_reason'],
            'machine_id' => isset($data['machine_id']) ? $data['machine_id'] : $timeBooking['machine_id'],
            'tool_id' => isset($data['tool_id']) ? $data['tool_id'] : $timeBooking['tool_id'],
            'notes' => isset($data['notes']) ? $data['notes'] : $timeBooking['notes']
        ];

        // Recalculate duration if end time changed
        if (isset($data['end_time']) && $data['end_time'] != $timeBooking['end_time']) {
            $updateData['duration_minutes'] = (strtotime($data['end_time']) - strtotime($timeBooking['start_time'])) / 60;
        }

        return $this->update($id, $updateData);
    }

    public function startTimeBooking($jobCardId, $operatorId, $activityType, $machineId = null, $toolId = null)
    {
        $timeData = [
            'job_card_id' => $jobCardId,
            'operator_id' => $operatorId,
            'start_time' => date('Y-m-d H:i:s'),
            'activity_type' => $activityType,
            'machine_id' => $machineId,
            'tool_id' => $toolId
        ];

        return $this->insert($timeData);
    }

    public function endTimeBooking($id, $endTime = null)
    {
        $timeBooking = $this->find($id);
        if (!$timeBooking || $timeBooking['end_time']) {
            return false;
        }

        $endTime = isset($endTime) ? $endTime : date('Y-m-d H:i:s');
        $duration = (strtotime($endTime) - strtotime($timeBooking['start_time'])) / 60;

        $updateData = [
            'end_time' => $endTime,
            'duration_minutes' => $duration
        ];

        return $this->update($id, $updateData);
    }

    public function pauseTimeBooking($id, $pauseReason = '')
    {
        $timeBooking = $this->find($id);
        if (!$timeBooking || $timeBooking['end_time']) {
            return false;
        }

        // End current booking
        $this->endTimeBooking($id);

        // Create break booking
        $breakData = [
            'job_card_id' => $timeBooking['job_card_id'],
            'operator_id' => $timeBooking['operator_id'],
            'start_time' => date('Y-m-d H:i:s'),
            'activity_type' => 'break',
            'break_reason' => $pauseReason
        ];

        return $this->insert($breakData);
    }

    public function resumeTimeBooking($jobCardId, $operatorId, $activityType, $machineId = null, $toolId = null)
    {
        $timeData = [
            'job_card_id' => $jobCardId,
            'operator_id' => $operatorId,
            'start_time' => date('Y-m-d H:i:s'),
            'activity_type' => $activityType,
            'machine_id' => $machineId,
            'tool_id' => $toolId
        ];

        return $this->insert($timeData);
    }

    public function getOperatorTimeSummary($operatorId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('activity_type, SUM(duration_minutes) as total_minutes, COUNT(*) as booking_count')
                        ->where('operator_id', $operatorId)
                        ->where('end_time IS NOT NULL')
                        ->groupBy('activity_type');
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getJobCardTimeSummary($jobCardId)
    {
        $bookings = $this->getByJobCard($jobCardId);
        $summary = [
            'total_bookings' => count($bookings),
            'total_time_minutes' => 0,
            'setup_time_minutes' => 0,
            'production_time_minutes' => 0,
            'break_time_minutes' => 0,
            'idle_time_minutes' => 0,
            'other_time_minutes' => 0,
            'operator_breakdown' => []
        ];

        $operatorTimes = [];

        foreach ($bookings as $booking) {
            $duration = isset($booking['duration_minutes']) ? $booking['duration_minutes'] : 0;
            $summary['total_time_minutes'] += $duration;

            switch ($booking['activity_type']) {
                case 'setup':
                    $summary['setup_time_minutes'] += $duration;
                    break;
                case 'production':
                    $summary['production_time_minutes'] += $duration;
                    break;
                case 'break':
                    $summary['break_time_minutes'] += $duration;
                    break;
                case 'idle':
                    $summary['idle_time_minutes'] += $duration;
                    break;
                default:
                    $summary['other_time_minutes'] += $duration;
            }

            // Track operator times
            $operatorName = $booking['operator_name'];
            if (!isset($operatorTimes[$operatorName])) {
                $operatorTimes[$operatorName] = 0;
            }
            $operatorTimes[$operatorName] += $duration;
        }

        $summary['operator_breakdown'] = $operatorTimes;

        return $summary;
    }

    public function getTimeBookingStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('activity_type, COUNT(*) as count, SUM(duration_minutes) as total_minutes, AVG(duration_minutes) as avg_minutes')
                        ->where('end_time IS NOT NULL')
                        ->groupBy('activity_type');
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getTimeBookingAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(start_time) as date, COUNT(*) as booking_count, SUM(duration_minutes) as total_minutes, AVG(duration_minutes) as avg_minutes')
                        ->where('end_time IS NOT NULL')
                        ->groupBy('DATE(start_time)');
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getOperatorEfficiency($operatorId, $startDate = null, $endDate = null)
    {
        $bookings = $this->getByOperator($operatorId, $startDate, $endDate);
        $totalProductionTime = 0;
        $totalBreakTime = 0;
        $totalIdleTime = 0;

        foreach ($bookings as $booking) {
            $duration = isset($booking['duration_minutes']) ? $booking['duration_minutes'] : 0;
            
            switch ($booking['activity_type']) {
                case 'production':
                    $totalProductionTime += $duration;
                    break;
                case 'break':
                    $totalBreakTime += $duration;
                    break;
                case 'idle':
                    $totalIdleTime += $duration;
                    break;
            }
        }

        $totalTime = $totalProductionTime + $totalBreakTime + $totalIdleTime;
        
        if ($totalTime <= 0) {
            return 0;
        }

        $efficiency = ($totalProductionTime / $totalTime) * 100;
        return round($efficiency, 2);
    }

    public function getMachineUtilization($machineId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(start_time) as date, SUM(duration_minutes) as total_minutes, COUNT(*) as booking_count')
                        ->where('machine_id', $machineId)
                        ->where('end_time IS NOT NULL')
                        ->groupBy('DATE(start_time)');
        
        if ($startDate) {
            $builder->where('start_time >=', $startDate);
        }
        if ($endDate) {
            $builder->where('start_time <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getActivityTypes()
    {
        return [
            'setup' => 'Setup',
            'production' => 'Production',
            'maintenance' => 'Maintenance',
            'break' => 'Break',
            'idle' => 'Idle',
            'other' => 'Other'
        ];
    }

    public function getBreakTypes()
    {
        return [
            'lunch' => 'Lunch Break',
            'tea' => 'Tea Break',
            'rest' => 'Rest Break',
            'maintenance' => 'Maintenance Break',
            'other' => 'Other Break'
        ];
    }

    public function validateTimeOverlap($operatorId, $startTime, $endTime, $excludeId = null)
    {
        $builder = $this->where('operator_id', $operatorId)
                        ->where('(start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND start_time < ?)', 
                                [$endTime, $startTime, $endTime, $startTime, $startTime, $endTime]);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        $overlaps = $builder->findAll();
        return empty($overlaps);
    }

    public function getTimeSheetReport($operatorId, $startDate, $endDate, $format = 'csv')
    {
        $bookings = $this->getByOperator($operatorId, $startDate, $endDate);
        $operator = model('User')->find($operatorId);
        
        if ($format == 'csv') {
            return $this->exportTimeSheetToCSV($operator, $bookings, $startDate, $endDate);
        }
        
        return ['operator' => $operator, 'bookings' => $bookings, 'start_date' => $startDate, 'end_date' => $endDate];
    }

    private function exportTimeSheetToCSV($operator, $bookings, $startDate, $endDate)
    {
        $csv = "Time Sheet Report\n";
        $csv .= "Operator: {$operator['username']}\n";
        $csv .= "Period: {$startDate} to {$endDate}\n\n";
        
        $csv .= "Date,Job Card,Activity Type,Start Time,End Time,Duration (min),Machine,Tool,Notes\n";
        
        foreach ($bookings as $booking) {
            $csv .= date('Y-m-d', strtotime($booking['start_time'])) . ",";
            $csv .= $booking['job_card_number'] . ",";
            $csv .= $booking['activity_type'] . ",";
            $csv .= date('H:i', strtotime($booking['start_time'])) . ",";
            $csv .= $booking['end_time'] ? date('H:i', strtotime($booking['end_time'])) : '' . ",";
            $csv .= $booking['duration_minutes'] . ",";
            $csv .= isset($booking['machine_name']) ? $booking['machine_name'] : '' . ",";
            $csv .= isset($booking['tool_name']) ? $booking['tool_name'] : '' . ",";
            $csv .= $booking['notes'] . "\n";
        }
        
        return $csv;
    }
}
