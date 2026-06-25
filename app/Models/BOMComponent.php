<?php

namespace App\Models;

use CodeIgniter\Model;

class BOMComponent extends Model
{
    protected $table = 'bom_components';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bom_id',
        'component_item_id',
        'qty',
        'uom',
        'scrap_pct',
        'yield_pct',
        'is_alternate',
        'priority',
        'position',
        'reference_designator',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'bom_id' => 'required|integer',
        'component_item_id' => 'required|integer',
        'qty' => 'required|numeric|greater_than[0]',
        'uom' => 'required',
        'scrap_pct' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'yield_pct' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
        'priority' => 'permit_empty|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'bom_id' => [
            'required' => 'BOM ID is required',
            'integer' => 'Invalid BOM ID'
        ],
        'component_item_id' => [
            'required' => 'Component item is required',
            'integer' => 'Invalid component item ID'
        ],
        'qty' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'scrap_pct' => [
            'numeric' => 'Scrap percentage must be a number',
            'greater_than_equal_to' => 'Scrap percentage must be 0 or greater'
        ],
        'yield_pct' => [
            'numeric' => 'Yield percentage must be a number',
            'greater_than' => 'Yield percentage must be greater than 0',
            'less_than_equal_to' => 'Yield percentage cannot exceed 100'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function bom()
    {
        return $this->belongsTo('App\Models\BillOfMaterials', 'bom_id', 'id');
    }

    public function component()
    {
        return $this->belongsTo('App\Models\Item', 'component_item_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('bom_components.*, items.item_code, items.item_name, items.unit_of_measurement, items.standard_cost, items.item_type, bill_of_materials.bom_number, bill_of_materials.revision')
                        ->join('items', 'items.id = bom_components.component_item_id')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_components.bom_id');

        if ($id) {
            return $builder->where('bom_components.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByBOM($bomId)
    {
        return $this->select('bom_components.*, items.item_code, items.item_name, items.unit_of_measurement, items.standard_cost, items.item_type')
                    ->join('items', 'items.id = bom_components.component_item_id')
                    ->where('bom_id', $bomId)
                    ->orderBy('position', 'ASC')
                    ->orderBy('priority', 'ASC')
                    ->findAll();
    }

    public function getByItem($itemId)
    {
        return $this->select('bom_components.*, bill_of_materials.bom_number, bill_of_materials.revision, bill_of_materials.status')
                    ->join('bill_of_materials', 'bill_of_materials.id = bom_components.bom_id')
                    ->where('component_item_id', $itemId)
                    ->orderBy('bill_of_materials.created_at', 'DESC')
                    ->findAll();
    }

    public function getAlternates($bomId, $componentItemId)
    {
        return $this->where('bom_id', $bomId)
                    ->where('component_item_id', $componentItemId)
                    ->where('is_alternate', 1)
                    ->orderBy('priority', 'ASC')
                    ->findAll();
    }

    public function getPrimaryComponent($bomId, $componentItemId)
    {
        return $this->where('bom_id', $bomId)
                    ->where('component_item_id', $componentItemId)
                    ->where('is_alternate', 0)
                    ->first();
    }

    public function createComponent($data)
    {
        $componentData = [
            'bom_id' => $data['bom_id'],
            'component_item_id' => $data['component_item_id'],
            'qty' => $data['qty'],
            'uom' => $data['uom'],
            'scrap_pct' => isset($data['scrap_pct']) ? $data['scrap_pct'] : 0,
            'yield_pct' => isset($data['yield_pct']) ? $data['yield_pct'] : 100,
            'is_alternate' => isset($data['is_alternate']) ? $data['is_alternate'] : 0,
            'priority' => isset($data['priority']) ? $data['priority'] : 1,
            'position' => isset($data['position']) ? $data['position'] : 1,
            'reference_designator' => isset($data['reference_designator']) ? $data['reference_designator'] : '',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($componentData);
    }

    public function updateComponent($id, $data)
    {
        $component = $this->find($id);
        if (!$component) {
            return false;
        }

        $updateData = [
            'qty' => isset($data['qty']) ? $data['qty'] : $component['qty'],
            'uom' => isset($data['uom']) ? $data['uom'] : $component['uom'],
            'scrap_pct' => isset($data['scrap_pct']) ? $data['scrap_pct'] : $component['scrap_pct'],
            'yield_pct' => isset($data['yield_pct']) ? $data['yield_pct'] : $component['yield_pct'],
            'is_alternate' => isset($data['is_alternate']) ? $data['is_alternate'] : $component['is_alternate'],
            'priority' => isset($data['priority']) ? $data['priority'] : $component['priority'],
            'position' => isset($data['position']) ? $data['position'] : $component['position'],
            'reference_designator' => isset($data['reference_designator']) ? $data['reference_designator'] : $component['reference_designator'],
            'notes' => isset($data['notes']) ? $data['notes'] : $component['notes']
        ];

        return $this->update($id, $updateData);
    }

    public function calculateRequiredQuantity($qty, $scrapPct = 0, $yieldPct = 100)
    {
        if ($yieldPct <= 0) {
            return 0;
        }
        
        $effectiveQty = $qty * (1 + $scrapPct / 100);
        return $effectiveQty / ($yieldPct / 100);
    }

    public function getComponentCost($componentId, $quantity = 1)
    {
        $component = $this->find($componentId);
        if (!$component) {
            return 0;
        }

        $item = model('Item')->find($component['component_item_id']);
        if (!$item) {
            return 0;
        }

        $requiredQty = $this->calculateRequiredQuantity(
            $component['qty'] * $quantity,
            $component['scrap_pct'],
            $component['yield_pct']
        );

        return $requiredQty * $item['standard_cost'];
    }

    public function getComponentAvailability($componentId, $warehouseId = null)
    {
        $component = $this->find($componentId);
        if (!$component) {
            return 0;
        }

        $item = model('Item')->find($component['component_item_id']);
        if (!$item) {
            return 0;
        }

        if ($warehouseId) {
            $stock = model('CurrentStock')->getStockBalance(
                $component['component_item_id'],
                $warehouseId
            );
        } else {
            $stock = model('CurrentStock')->getTotalStockBalance($component['component_item_id']);
        }

        return $stock;
    }

    public function checkComponentAvailability($bomId, $warehouseId = null)
    {
        $components = $this->getByBOM($bomId);
        $availability = [];

        foreach ($components as $component) {
            $availableQty = $this->getComponentAvailability($component['id'], $warehouseId);
            $requiredQty = $this->calculateRequiredQuantity(
                $component['qty'],
                $component['scrap_pct'],
                $component['yield_pct']
            );

            $availability[] = [
                'component_id' => $component['id'],
                'item_code' => $component['item_code'],
                'item_name' => $component['item_name'],
                'required_qty' => $requiredQty,
                'available_qty' => $availableQty,
                'shortage_qty' => max(0, $requiredQty - $availableQty),
                'is_available' => $availableQty >= $requiredQty
            ];
        }

        return $availability;
    }

    public function getComponentStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('items.item_name, COUNT(*) as usage_count, AVG(bom_components.qty) as avg_qty, AVG(bom_components.scrap_pct) as avg_scrap_pct')
                        ->join('items', 'items.id = bom_components.component_item_id')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_components.bom_id')
                        ->groupBy('items.id, items.item_name');
        
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('usage_count', 'DESC')->findAll();
    }

    public function getComponentAnalytics($itemId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(bill_of_materials.created_at) as date, COUNT(*) as component_count, AVG(bom_components.qty) as avg_qty, AVG(bom_components.scrap_pct) as avg_scrap_pct')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_components.bom_id')
                        ->groupBy('DATE(bill_of_materials.created_at)');
        
        if ($itemId) {
            $builder->where('bom_components.component_item_id', $itemId);
        }
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getHighScrapComponents($bomId, $threshold = 5)
    {
        return $this->select('bom_components.*, items.item_code, items.item_name')
                    ->join('items', 'items.id = bom_components.component_item_id')
                    ->where('bom_id', $bomId)
                    ->where('scrap_pct >', $threshold)
                    ->orderBy('scrap_pct', 'DESC')
                    ->findAll();
    }

    public function getLowYieldComponents($bomId, $threshold = 90)
    {
        return $this->select('bom_components.*, items.item_code, items.item_name')
                    ->join('items', 'items.id = bom_components.component_item_id')
                    ->where('bom_id', $bomId)
                    ->where('yield_pct <', $threshold)
                    ->orderBy('yield_pct', 'ASC')
                    ->findAll();
    }

    public function getComponentUsageHistory($itemId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('bom_components.*, bill_of_materials.bom_number, bill_of_materials.revision, bill_of_materials.status')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_components.bom_id')
                        ->where('component_item_id', $itemId);
        
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('bill_of_materials.created_at', 'DESC')->findAll();
    }

    public function getComponentAlternatives($itemId)
    {
        return $this->select('bom_components.*, bill_of_materials.bom_number, bill_of_materials.revision, items.item_code, items.item_name')
                    ->join('bill_of_materials', 'bill_of_materials.id = bom_components.bom_id')
                    ->join('items', 'items.id = bill_of_materials.item_id_fg')
                    ->where('component_item_id', $itemId)
                    ->where('is_alternate', 1)
                    ->orderBy('priority', 'ASC')
                    ->findAll();
    }

    public function validateUOMCompatibility($bomId, $componentItemId, $uom)
    {
        $component = $this->where('bom_id', $bomId)
                          ->where('component_item_id', $componentItemId)
                          ->first();
        
        if (!$component) {
            return true; // New component
        }

        $item = model('Item')->find($componentItemId);
        if (!$item) {
            return false;
        }

        // Check if UOM is compatible with item's base UOM
        return $this->isUOMCompatible($uom, $item['unit_of_measurement']);
    }

    private function isUOMCompatible($uom1, $uom2)
    {
        // Basic UOM compatibility check - can be enhanced with conversion factors
        $compatibleGroups = [
            'weight' => ['kg', 'g', 'lb', 'oz'],
            'length' => ['m', 'cm', 'mm', 'ft', 'in'],
            'volume' => ['l', 'ml', 'gal', 'qt'],
            'count' => ['pcs', 'units', 'each']
        ];

        foreach ($compatibleGroups as $group => $units) {
            if (in_array(strtolower($uom1), $units) && in_array(strtolower($uom2), $units)) {
                return true;
            }
        }

        return strtolower($uom1) === strtolower($uom2);
    }
}
