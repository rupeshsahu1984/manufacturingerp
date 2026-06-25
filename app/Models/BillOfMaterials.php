<?php

namespace App\Models;

use CodeIgniter\Model;

class BillOfMaterials extends Model
{
    protected $table = 'bom';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bom_number',
        'item_id_fg',
        'revision',
        'description',
        'uom',
        'qty_per',
        'effective_from',
        'effective_to',
        'bom_type',
        'is_phantom',
        'is_active',
        'status',
        'total_material_cost',
        'total_labor_cost',
        'total_overhead_cost',
        'total_cost',
        'approval_status',
        'approved_by',
        'approved_at',
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
        'bom_number' => 'required|is_unique[bill_of_materials.bom_number,id,{id}]',
        'item_id_fg' => 'required|integer',
        'revision' => 'required|alpha_numeric',
        'uom' => 'required',
        'qty_per' => 'required|numeric|greater_than[0]',
        'effective_from' => 'required|valid_date',
        'bom_type' => 'required|in_list[manufacturing,engineering,planning]',
        'status' => 'required|in_list[draft,under_review,released,obsolete]'
    ];

    protected $validationMessages = [
        'bom_number' => [
            'required' => 'BOM number is required',
            'is_unique' => 'BOM number must be unique'
        ],
        'item_id_fg' => [
            'required' => 'Finished good item is required',
            'integer' => 'Invalid item ID'
        ],
        'revision' => [
            'required' => 'Revision is required'
        ],
        'qty_per' => [
            'required' => 'Quantity per is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function finishedGood()
    {
        return $this->belongsTo('App\Models\Item', 'item_id_fg', 'id');
    }

    public function components()
    {
        return $this->hasMany('App\Models\BOMComponent', 'bom_id', 'id');
    }

    public function operations()
    {
        return $this->hasMany('App\Models\BOMOperation', 'bom_id', 'id');
    }

    public function byProducts()
    {
        return $this->hasMany('App\Models\BOMByProduct', 'bom_id', 'id');
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
        $table = $this->table;
        $uomExpr = Item::sqlItemsUnitAs('unit_of_measurement');
        $builder = $this->select("{$table}.*, items.item_code, items.item_name, {$uomExpr}, users.username as approved_by_name, creators.username as created_by_name", false)
            ->join('items', "items.id = {$table}.item_id_fg", 'left')
            ->join('users', "users.id = {$table}.approved_by", 'left')
            ->join('users creators', "creators.id = {$table}.created_by", 'left');

        if ($id) {
            return $builder->where("{$table}.id", $id)->first();
        }

        return $builder->findAll();
    }

    public function getByItem($itemId)
    {
        return $this->where('item_id_fg', $itemId)
                    ->orderBy('revision', 'DESC')
                    ->findAll();
    }

    public function getActiveBOM($itemId, $asOfDate = null)
    {
        $asOfDate = isset($asOfDate) ? $asOfDate : date('Y-m-d');
        
        return $this->where('item_id_fg', $itemId)
                    ->where('is_active', 1)
                    ->where('status', 'released')
                    ->where('effective_from <=', $asOfDate)
                    ->where('(effective_to IS NULL OR effective_to >=)', $asOfDate)
                    ->orderBy('revision', 'DESC')
                    ->first();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByType($bomType)
    {
        return $this->where('bom_type', $bomType)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getPhantomBOMs()
    {
        return $this->where('is_phantom', 1)
                    ->where('status', 'released')
                    ->findAll();
    }

    public function generateBOMNumber()
    {
        $prefix = 'BOM';
        $year = date('Y');
        $month = date('m');
        
        $lastBOM = $this->select('bom_number')
                        ->like('bom_number', "{$prefix}{$year}{$month}")
                        ->orderBy('bom_number', 'DESC')
                        ->first();
        
        if ($lastBOM) {
            $lastNumber = intval(substr($lastBOM['bom_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $newNumber);
    }

    public function createBOM($data)
    {
        $bomData = [
            'bom_number' => isset($data['bom_number']) ? $data['bom_number'] : $this->generateBOMNumber(),
            'item_id_fg' => $data['item_id_fg'],
            'revision' => $data['revision'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'uom' => $data['uom'],
            'qty_per' => $data['qty_per'],
            'effective_from' => $data['effective_from'],
            'effective_to' => isset($data['effective_to']) ? $data['effective_to'] : null,
            'bom_type' => $data['bom_type'],
            'is_phantom' => isset($data['is_phantom']) ? $data['is_phantom'] : 0,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : 1,
            'status' => isset($data['status']) ? $data['status'] : 'draft',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($bomData);
    }

    public function updateBOM($id, $data)
    {
        $bom = $this->find($id);
        if (!$bom) {
            return false;
        }

        // If changing status to released, deactivate other active BOMs for same item
        if (isset($data['status']) && $data['status'] == 'released' && $bom['status'] != 'released') {
            $this->where('item_id_fg', $bom['item_id_fg'])
                 ->where('id !=', $id)
                 ->where('is_active', 1)
                 ->set(['is_active' => 0])
                 ->update();
        }

        return $this->update($id, $data);
    }

    public function approveBOM($id, $approvedBy, $notes = '')
    {
        $updateData = [
            'approval_status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'status' => 'released',
            'notes' => $notes
        ];

        return $this->update($id, $updateData);
    }

    public function calculateCosts($bomId)
    {
        $bom = $this->find($bomId);
        if (!$bom) {
            return false;
        }

        $totalMaterialCost = 0;
        $totalLaborCost = 0;
        $totalOverheadCost = 0;

        // Calculate material costs from components
        $components = model('BOMComponent')->where('bom_id', $bomId)->findAll();
        foreach ($components as $component) {
            $item = model('Item')->find($component['component_item_id']);
            if ($item) {
                $totalMaterialCost += ($component['qty'] * $item['standard_cost'] * (1 + $component['scrap_pct'] / 100));
            }
        }

        // Calculate labor and overhead costs from operations
        $operations = model('BOMOperation')->where('bom_id', $bomId)->findAll();
        foreach ($operations as $operation) {
            $workcenter = model('Workcenter')->find($operation['workcenter_id']);
            if ($workcenter) {
                $totalLaborCost += ($operation['setup_time'] + ($operation['run_time_per_unit'] * $bom['qty_per'])) * $workcenter['labor_rate'] / 60;
                $totalOverheadCost += ($operation['setup_time'] + ($operation['run_time_per_unit'] * $bom['qty_per'])) * $workcenter['overhead_rate'] / 60;
            }
        }

        $totalCost = $totalMaterialCost + $totalLaborCost + $totalOverheadCost;

        $updateData = [
            'total_material_cost' => $totalMaterialCost,
            'total_labor_cost' => $totalLaborCost,
            'total_overhead_cost' => $totalOverheadCost,
            'total_cost' => $totalCost
        ];

        return $this->update($bomId, $updateData);
    }

    public function explodeBOM($bomId, $quantity = 1, $level = 0, $maxLevel = 10)
    {
        if ($level >= $maxLevel) {
            return [];
        }

        $bom = $this->find($bomId);
        if (!$bom || $bom['status'] != 'released') {
            return [];
        }

        $exploded = [];
        $components = model('BOMComponent')->where('bom_id', $bomId)->findAll();

        foreach ($components as $component) {
            $item = model('Item')->find($component['component_item_id']);
            if (!$item) continue;

            $requiredQty = $component['qty'] * $quantity * (1 + $component['scrap_pct'] / 100);
            
            $exploded[] = [
                'level' => $level,
                'item_id' => $component['component_item_id'],
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'uom' => $item['uom'] ?? '',
                'qty_per' => $component['qty'],
                'required_qty' => $requiredQty,
                'scrap_pct' => $component['scrap_pct'],
                'yield_pct' => $component['yield_pct'],
                'is_alternate' => $component['is_alternate'],
                'priority' => $component['priority'],
                'unit_cost' => $item['standard_cost'],
                'total_cost' => $requiredQty * $item['standard_cost']
            ];

            // Recursively explode sub-assemblies
            if (($item['material_type'] ?? '') === 'semi_finished') {
                $subBOM = $this->getActiveBOM($item['id']);
                if ($subBOM && !$subBOM['is_phantom']) {
                    $subExploded = $this->explodeBOM($subBOM['id'], $requiredQty, $level + 1, $maxLevel);
                    $exploded = array_merge($exploded, $subExploded);
                }
            }
        }

        return $exploded;
    }

    public function getBOMStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('status, bom_type, COUNT(*) as count, AVG(total_cost) as avg_cost')
                        ->groupBy('status, bom_type');
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getBOMAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as bom_count, AVG(total_cost) as avg_cost, SUM(total_cost) as total_cost')
                        ->groupBy('DATE(created_at)');
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getBOMTypes()
    {
        return [
            'manufacturing' => 'Manufacturing BOM',
            'engineering' => 'Engineering BOM',
            'planning' => 'Planning BOM'
        ];
    }

    public function getBOMStatuses()
    {
        return [
            'draft' => 'Draft',
            'under_review' => 'Under Review',
            'released' => 'Released',
            'obsolete' => 'Obsolete'
        ];
    }

    public function getBOMByRevision($itemId, $revision)
    {
        return $this->where('item_id_fg', $itemId)
                    ->where('revision', $revision)
                    ->first();
    }

    public function getBOMHistory($itemId)
    {
        return $this->where('item_id_fg', $itemId)
                    ->orderBy('revision', 'DESC')
                    ->findAll();
    }

    public function checkCircularReference($itemId, $bomId = null)
    {
        $visited = [];
        return $this->hasCircularReference($itemId, $bomId, $visited);
    }

    private function hasCircularReference($itemId, $bomId, &$visited)
    {
        if (in_array($itemId, $visited)) {
            return true;
        }

        $visited[] = $itemId;
        $boms = $this->where('item_id_fg', $itemId)->findAll();

        foreach ($boms as $bom) {
            if ($bomId && $bom['id'] == $bomId) {
                continue; // Skip current BOM being edited
            }

            $components = model('BOMComponent')->where('bom_id', $bom['id'])->findAll();
            foreach ($components as $component) {
                $item = model('Item')->find($component['component_item_id']);
                if ($item && $item['item_type'] == 'semi_finished') {
                    if ($this->hasCircularReference($item['id'], $bomId, $visited)) {
                        return true;
                    }
                }
            }
        }

        array_pop($visited);
        return false;
    }
}
