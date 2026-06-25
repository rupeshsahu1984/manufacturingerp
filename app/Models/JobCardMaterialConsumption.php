<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardMaterialConsumption extends Model
{
    protected $table = 'job_card_material_consumptions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'job_card_id',
        'item_id',
        'quantity',
        'uom',
        'unit_cost',
        'total_cost',
        'consumption_date',
        'consumption_type',
        'batch_number',
        'warehouse_id',
        'location_id',
        'operator_id',
        'machine_id',
        'tool_id',
        'scrap_qty',
        'scrap_reason',
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
        'item_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'uom' => 'required',
        'consumption_date' => 'required|valid_date',
        'consumption_type' => 'required|in_list[planned,actual,scrap,rework]'
    ];

    protected $validationMessages = [
        'job_card_id' => [
            'required' => 'Job card is required',
            'integer' => 'Invalid job card ID'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'uom' => [
            'required' => 'Unit of measurement is required'
        ],
        'consumption_date' => [
            'required' => 'Consumption date is required',
            'valid_date' => 'Invalid consumption date'
        ],
        'consumption_type' => [
            'required' => 'Consumption type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function jobCard()
    {
        return $this->belongsTo('App\Models\JobCard', 'job_card_id', 'id');
    }

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
        $builder = $this->select('job_card_material_consumptions.*, job_cards.job_card_number, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name, warehouse_locations.location_name, users.username as operator_name, machines.machine_name, tools.tool_name')
                        ->join('job_cards', 'job_cards.id = job_card_material_consumptions.job_card_id')
                        ->join('items', 'items.id = job_card_material_consumptions.item_id')
                        ->join('warehouses', 'warehouses.id = job_card_material_consumptions.warehouse_id', 'left')
                        ->join('warehouse_locations', 'warehouse_locations.id = job_card_material_consumptions.location_id', 'left')
                        ->join('users', 'users.id = job_card_material_consumptions.operator_id', 'left')
                        ->join('machines', 'machines.id = job_card_material_consumptions.machine_id', 'left')
                        ->join('tools', 'tools.id = job_card_material_consumptions.tool_id', 'left');

        if ($id) {
            return $builder->where('job_card_material_consumptions.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByJobCard($jobCardId)
    {
        return $this->select('job_card_material_consumptions.*, items.item_code, items.item_name, items.unit_of_measurement, warehouses.warehouse_name, warehouse_locations.location_name')
                    ->join('items', 'items.id = job_card_material_consumptions.item_id')
                    ->join('warehouses', 'warehouses.id = job_card_material_consumptions.warehouse_id', 'left')
                    ->join('warehouse_locations', 'warehouse_locations.id = job_card_material_consumptions.location_id', 'left')
                    ->where('job_card_id', $jobCardId)
                    ->orderBy('consumption_date', 'ASC')
                    ->findAll();
    }

    public function getByItem($itemId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('job_card_material_consumptions.*, job_cards.job_card_number, job_cards.status as job_card_status')
                        ->join('job_cards', 'job_cards.id = job_card_material_consumptions.job_card_id')
                        ->where('item_id', $itemId);
        
        if ($startDate) {
            $builder->where('consumption_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('consumption_date <=', $endDate);
        }

        return $builder->orderBy('consumption_date', 'DESC')->findAll();
    }

    public function getByConsumptionType($consumptionType, $startDate = null, $endDate = null)
    {
        $builder = $this->where('consumption_type', $consumptionType);
        
        if ($startDate) {
            $builder->where('consumption_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('consumption_date <=', $endDate);
        }

        return $builder->orderBy('consumption_date', 'DESC')->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->select('job_card_material_consumptions.*, job_cards.job_card_number, items.item_code, items.item_name, users.username as operator_name')
                    ->join('job_cards', 'job_cards.id = job_card_material_consumptions.job_card_id')
                    ->join('items', 'items.id = job_card_material_consumptions.item_id')
                    ->join('users', 'users.id = job_card_material_consumptions.operator_id', 'left')
                    ->where('consumption_date >=', $startDate)
                    ->where('consumption_date <=', $endDate)
                    ->orderBy('consumption_date', 'ASC')
                    ->findAll();
    }

    public function getByWarehouse($warehouseId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('job_card_material_consumptions.*, job_cards.job_card_number, items.item_code, items.item_name')
                        ->join('job_cards', 'job_cards.id = job_card_material_consumptions.job_card_id')
                        ->join('items', 'items.id = job_card_material_consumptions.item_id')
                        ->where('warehouse_id', $warehouseId);
        
        if ($startDate) {
            $builder->where('consumption_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('consumption_date <=', $endDate);
        }

        return $builder->orderBy('consumption_date', 'DESC')->findAll();
    }

    public function createConsumption($data)
    {
        $consumptionData = [
            'job_card_id' => $data['job_card_id'],
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
            'uom' => isset($data['uom']) ? $data['uom'] : '',
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : 0,
            'total_cost' => isset($data['total_cost']) ? $data['total_cost'] : 0,
            'consumption_date' => isset($data['consumption_date']) ? $data['consumption_date'] : date('Y-m-d H:i:s'),
            'consumption_type' => isset($data['consumption_type']) ? $data['consumption_type'] : 'actual',
            'batch_number' => isset($data['batch_number']) ? $data['batch_number'] : null,
            'warehouse_id' => isset($data['warehouse_id']) ? $data['warehouse_id'] : null,
            'location_id' => isset($data['location_id']) ? $data['location_id'] : null,
            'operator_id' => isset($data['operator_id']) ? $data['operator_id'] : null,
            'machine_id' => isset($data['machine_id']) ? $data['machine_id'] : null,
            'tool_id' => isset($data['tool_id']) ? $data['tool_id'] : null,
            'scrap_qty' => isset($data['scrap_qty']) ? $data['scrap_qty'] : 0,
            'scrap_reason' => isset($data['scrap_reason']) ? $data['scrap_reason'] : '',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        // Calculate total cost if not provided
        if (!isset($data['total_cost']) && isset($data['unit_cost']) && isset($data['quantity'])) {
            $consumptionData['total_cost'] = $data['unit_cost'] * $data['quantity'];
        }

        return $this->insert($consumptionData);
    }

    public function updateConsumption($id, $data)
    {
        $consumption = $this->find($id);
        if (!$consumption) {
            return false;
        }

        $updateData = [
            'quantity' => isset($data['quantity']) ? $data['quantity'] : $consumption['quantity'],
            'uom' => isset($data['uom']) ? $data['uom'] : $consumption['uom'],
            'unit_cost' => isset($data['unit_cost']) ? $data['unit_cost'] : $consumption['unit_cost'],
            'consumption_type' => isset($data['consumption_type']) ? $data['consumption_type'] : $consumption['consumption_type'],
            'batch_number' => isset($data['batch_number']) ? $data['batch_number'] : $consumption['batch_number'],
            'warehouse_id' => isset($data['warehouse_id']) ? $data['warehouse_id'] : $consumption['warehouse_id'],
            'location_id' => isset($data['location_id']) ? $data['location_id'] : $consumption['location_id'],
            'operator_id' => isset($data['operator_id']) ? $data['operator_id'] : $consumption['operator_id'],
            'machine_id' => isset($data['machine_id']) ? $data['machine_id'] : $consumption['machine_id'],
            'tool_id' => isset($data['tool_id']) ? $data['tool_id'] : $consumption['tool_id'],
            'scrap_qty' => isset($data['scrap_qty']) ? $data['scrap_qty'] : $consumption['scrap_qty'],
            'scrap_reason' => isset($data['scrap_reason']) ? $data['scrap_reason'] : $consumption['scrap_reason'],
            'notes' => isset($data['notes']) ? $data['notes'] : $consumption['notes']
        ];

        // Recalculate total cost
        if (isset($updateData['unit_cost']) || isset($updateData['quantity'])) {
            $unitCost = isset($updateData['unit_cost']) ? $updateData['unit_cost'] : $consumption['unit_cost'];
            $quantity = isset($updateData['quantity']) ? $updateData['quantity'] : $consumption['quantity'];
            $updateData['total_cost'] = $unitCost * $quantity;
        }

        return $this->update($id, $updateData);
    }

    public function recordScrap($jobCardId, $itemId, $scrapQty, $scrapReason, $operatorId = null)
    {
        $scrapData = [
            'job_card_id' => $jobCardId,
            'item_id' => $itemId,
            'quantity' => $scrapQty,
            'consumption_type' => 'scrap',
            'scrap_qty' => $scrapQty,
            'scrap_reason' => $scrapReason,
            'operator_id' => $operatorId,
            'consumption_date' => date('Y-m-d H:i:s')
        ];

        return $this->insert($scrapData);
    }

    public function getMaterialConsumptionSummary($jobCardId)
    {
        $consumptions = $this->getByJobCard($jobCardId);
        $summary = [
            'total_consumptions' => count($consumptions),
            'total_quantity' => 0,
            'total_cost' => 0,
            'planned_consumption' => 0,
            'actual_consumption' => 0,
            'scrap_consumption' => 0,
            'rework_consumption' => 0,
            'item_breakdown' => []
        ];

        $itemBreakdown = [];

        foreach ($consumptions as $consumption) {
            $summary['total_quantity'] += $consumption['quantity'];
            $summary['total_cost'] += $consumption['total_cost'];

            switch ($consumption['consumption_type']) {
                case 'planned':
                    $summary['planned_consumption'] += $consumption['quantity'];
                    break;
                case 'actual':
                    $summary['actual_consumption'] += $consumption['quantity'];
                    break;
                case 'scrap':
                    $summary['scrap_consumption'] += $consumption['quantity'];
                    break;
                case 'rework':
                    $summary['rework_consumption'] += $consumption['quantity'];
                    break;
            }

            // Track item breakdown
            $itemCode = $consumption['item_code'];
            if (!isset($itemBreakdown[$itemCode])) {
                $itemBreakdown[$itemCode] = [
                    'item_name' => $consumption['item_name'],
                    'total_quantity' => 0,
                    'total_cost' => 0,
                    'consumption_types' => []
                ];
            }

            $itemBreakdown[$itemCode]['total_quantity'] += $consumption['quantity'];
            $itemBreakdown[$itemCode]['total_cost'] += $consumption['total_cost'];

            if (!isset($itemBreakdown[$itemCode]['consumption_types'][$consumption['consumption_type']])) {
                $itemBreakdown[$itemCode]['consumption_types'][$consumption['consumption_type']] = 0;
            }
            $itemBreakdown[$itemCode]['consumption_types'][$consumption['consumption_type']] += $consumption['quantity'];
        }

        $summary['item_breakdown'] = $itemBreakdown;

        return $summary;
    }

    public function getMaterialConsumptionStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('consumption_type, COUNT(*) as count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost, AVG(unit_cost) as avg_unit_cost')
                        ->groupBy('consumption_type');
        
        if ($startDate) {
            $builder->where('consumption_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('consumption_date <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getMaterialConsumptionAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(consumption_date) as date, COUNT(*) as consumption_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
                        ->groupBy('DATE(consumption_date)');
        
        if ($startDate) {
            $builder->where('consumption_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('consumption_date <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getScrapAnalysis($startDate = null, $endDate = null)
    {
        $builder = $this->select('items.item_name, SUM(job_card_material_consumptions.scrap_qty) as total_scrap_qty, COUNT(*) as scrap_count, AVG(job_card_material_consumptions.unit_cost) as avg_unit_cost')
                        ->join('items', 'items.id = job_card_material_consumptions.item_id')
                        ->where('consumption_type', 'scrap')
                        ->groupBy('items.id, items.item_name');
        
        if ($startDate) {
            $builder->where('consumption_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('consumption_date <=', $endDate);
        }

        return $builder->orderBy('total_scrap_qty', 'DESC')->findAll();
    }

    public function getMaterialEfficiency($jobCardId)
    {
        $consumptions = $this->getByJobCard($jobCardId);
        $plannedQty = 0;
        $actualQty = 0;

        foreach ($consumptions as $consumption) {
            if ($consumption['consumption_type'] == 'planned') {
                $plannedQty += $consumption['quantity'];
            } elseif ($consumption['consumption_type'] == 'actual') {
                $actualQty += $consumption['quantity'];
            }
        }

        if ($plannedQty <= 0) {
            return 0;
        }

        $efficiency = (($plannedQty - $actualQty) / $plannedQty) * 100;
        return round($efficiency, 2);
    }

    public function getConsumptionTypes()
    {
        return [
            'planned' => 'Planned',
            'actual' => 'Actual',
            'scrap' => 'Scrap',
            'rework' => 'Rework'
        ];
    }

    public function getScrapReasons()
    {
        return [
            'machine_failure' => 'Machine Failure',
            'operator_error' => 'Operator Error',
            'material_defect' => 'Material Defect',
            'tool_wear' => 'Tool Wear',
            'setup_error' => 'Setup Error',
            'quality_issue' => 'Quality Issue',
            'other' => 'Other'
        ];
    }

    public function validateMaterialAvailability($itemId, $quantity, $warehouseId = null)
    {
        $availableQty = 0;
        
        if ($warehouseId) {
            $availableQty = model('CurrentStock')->getStockBalance($itemId, $warehouseId);
        } else {
            $availableQty = model('CurrentStock')->getTotalStockBalance($itemId);
        }

        return [
            'required_qty' => $quantity,
            'available_qty' => $availableQty,
            'shortage_qty' => max(0, $quantity - $availableQty),
            'is_available' => $availableQty >= $quantity
        ];
    }

    public function exportMaterialConsumptionReport($jobCardId, $format = 'csv')
    {
        $consumptions = $this->getByJobCard($jobCardId);
        $jobCard = model('JobCard')->find($jobCardId);
        
        if ($format == 'csv') {
            return $this->exportToCSV($jobCard, $consumptions);
        }
        
        return ['job_card' => $jobCard, 'consumptions' => $consumptions];
    }

    private function exportToCSV($jobCard, $consumptions)
    {
        $csv = "Material Consumption Report\n";
        $csv .= "Job Card: {$jobCard['job_card_number']}\n";
        $csv .= "Status: {$jobCard['status']}\n\n";
        
        $csv .= "Item Code,Item Name,Quantity,UOM,Unit Cost,Total Cost,Consumption Type,Date,Warehouse,Location,Operator,Notes\n";
        
        foreach ($consumptions as $consumption) {
            $warehouseName = isset($consumption['warehouse_name']) ? $consumption['warehouse_name'] : '';
            $locationName = isset($consumption['location_name']) ? $consumption['location_name'] : '';
            $operatorName = isset($consumption['operator_name']) ? $consumption['operator_name'] : '';
            $csv .= "{$consumption['item_code']},{$consumption['item_name']},{$consumption['quantity']},{$consumption['uom']},{$consumption['unit_cost']},{$consumption['total_cost']},{$consumption['consumption_type']},{$consumption['consumption_date']},{$warehouseName},{$locationName},{$operatorName},{$consumption['notes']}\n";
        }
        
        return $csv;
    }
}
