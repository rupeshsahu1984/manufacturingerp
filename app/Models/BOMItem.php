<?php

namespace App\Models;

use CodeIgniter\Model;

class BOMItem extends Model
{
    protected $table = 'bom_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bom_id',
        'material_id',
        'quantity_required',
        'waste_percentage',
        'waste_quantity',
        'total_quantity',
        'unit_cost',
        'total_cost'
    ];

    protected $useTimestamps = false;

    // Validation rules
    protected $validationRules = [
        'bom_id' => 'required|integer',
        'material_id' => 'required|integer',
        'quantity_required' => 'required|numeric|greater_than[0]',
        'waste_percentage' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'waste_quantity' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'total_quantity' => 'permit_empty|numeric|greater_than[0]',
        'unit_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'total_cost' => 'permit_empty|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'bom_id' => [
            'required' => 'BOM ID is required',
            'integer' => 'BOM ID must be a valid number'
        ],
        'material_id' => [
            'required' => 'Material is required',
            'integer' => 'Material must be a valid selection'
        ],
        'quantity_required' => [
            'required' => 'Quantity required is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'unit_cost' => [
            'required' => 'Unit cost is required',
            'numeric' => 'Unit cost must be a number',
            'greater_than_equal_to' => 'Unit cost must be 0 or greater'
        ],
        'total_cost' => [
            'required' => 'Total cost is required',
            'numeric' => 'Total cost must be a number',
            'greater_than_equal_to' => 'Total cost must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get BOM items with material details
     */
    public function getBOMItems($bomId)
    {
        return $this->select('bi.*, p.product_name as material_name, p.product_code as material_code, p.unit, p.material_type')
            ->from('bom_items bi')
            ->join('products p', 'bi.material_id = p.id')
            ->where('bi.bom_id', $bomId)
            ->orderBy('p.material_type', 'ASC')
            ->orderBy('p.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Calculate item totals
     */
    public function calculateItemTotals($quantity, $unitCost)
    {
        $totalCost = $quantity * $unitCost;

        return [
            'quantity_required' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost
        ];
    }

    /**
     * Get BOM items summary
     */
    public function getBOMItemsSummary($bomId)
    {
        $result = $this->select('COUNT(*) as total_items, SUM(quantity_required) as total_quantity, SUM(total_cost) as total_cost')
            ->where('bom_id', $bomId)
            ->first();

        return [
            'total_items' => isset($result['total_items']) ? $result['total_items'] : 0,
            'total_quantity' => isset($result['total_quantity']) ? $result['total_quantity'] : 0,
            'total_cost' => isset($result['total_cost']) ? $result['total_cost'] : 0
        ];
    }

    /**
     * Delete items by BOM ID
     */
    public function deleteItemsByBOMId($bomId)
    {
        return $this->where('bom_id', $bomId)->delete();
    }

    /**
     * Get material requirements for production quantity
     */
    public function getMaterialRequirementsForQuantity($bomId, $productionQuantity)
    {
        $items = $this->getBOMItems($bomId);
        $requirements = [];

        foreach ($items as $item) {
            $requiredQuantity = $item['quantity_required'] * $productionQuantity;
            $totalCost = $requiredQuantity * $item['unit_cost'];

            $requirements[] = [
                'material_id' => $item['material_id'],
                'material_name' => $item['material_name'],
                'material_code' => $item['material_code'],
                'quantity_required' => $requiredQuantity,
                'unit' => $item['unit'],
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $totalCost
            ];
        }

        return $requirements;
    }

    /**
     * Check if material exists in BOM
     */
    public function materialExistsInBOM($bomId, $materialId)
    {
        return $this->where('bom_id', $bomId)
            ->where('material_id', $materialId)
            ->countAllResults() > 0;
    }

    /**
     * Get BOM items by material type
     */
    public function getBOMItemsByMaterialType($bomId, $materialType)
    {
        return $this->select('bi.*, p.product_name as material_name, p.product_code as material_code, p.unit')
            ->from('bom_items bi')
            ->join('products p', 'bi.material_id = p.id')
            ->where('bi.bom_id', $bomId)
            ->where('p.material_type', $materialType)
            ->orderBy('p.product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get raw materials for BOM
     */
    public function getRawMaterialsForBOM($bomId)
    {
        return $this->getBOMItemsByMaterialType($bomId, 'raw_material');
    }

    /**
     * Get packaging materials for BOM
     */
    public function getPackagingMaterialsForBOM($bomId)
    {
        return $this->getBOMItemsByMaterialType($bomId, 'packaging');
    }

    /**
     * Calculate total cost by material type
     */
    public function getTotalCostByMaterialType($bomId, $materialType)
    {
        $result = $this->select('SUM(bi.total_cost) as total_cost')
            ->from('bom_items bi')
            ->join('products p', 'bi.material_id = p.id')
            ->where('bi.bom_id', $bomId)
            ->where('p.material_type', $materialType)
            ->first();

        return isset($result['total_cost']) ? $result['total_cost'] : 0;
    }

    /**
     * Get material cost breakdown
     */
    public function getMaterialCostBreakdown($bomId)
    {
        $rawMaterialsCost = $this->getTotalCostByMaterialType($bomId, 'raw_material');
        $packagingCost = $this->getTotalCostByMaterialType($bomId, 'packaging');

        return [
            'raw_materials_cost' => $rawMaterialsCost,
            'packaging_cost' => $packagingCost,
            'total_material_cost' => $rawMaterialsCost + $packagingCost
        ];
    }

    /**
     * Validate BOM item
     */
    public function validateBOMItem($data)
    {
        // Check if material exists
        $productModel = new \App\Models\Product();
        $material = $productModel->find($data['material_id']);
        
        if (!$material) {
            return ['valid' => false, 'message' => 'Selected material does not exist'];
        }

        // Check if material is suitable for BOM (raw material or packaging)
        if (!in_array($material['material_type'], ['raw_material', 'packaging'])) {
            return ['valid' => false, 'message' => 'Only raw materials and packaging can be added to BOM'];
        }

        // Check if material is already in BOM
        if ($this->materialExistsInBOM($data['bom_id'], $data['material_id'])) {
            return ['valid' => false, 'message' => 'Material is already in this BOM'];
        }

        return ['valid' => true, 'message' => 'BOM item is valid'];
    }

    /**
     * Get BOM items with stock information
     */
    public function getBOMItemsWithStock($bomId)
    {
        $items = $this->getBOMItems($bomId);
        $stockModel = new \App\Models\Stock();

        foreach ($items as &$item) {
            $currentStock = $stockModel->getCurrentStock($item['material_id']);
            $item['current_stock'] = $currentStock;
            $item['available'] = $currentStock >= $item['quantity_required'];
            $item['shortage'] = max(0, $item['quantity_required'] - $currentStock);
        }

        return $items;
    }

    /**
     * Check material availability for production
     */
    public function checkMaterialAvailabilityForProduction($bomId, $productionQuantity)
    {
        $items = $this->getBOMItemsWithStock($bomId);
        $availability = [];
        $canProduce = true;

        foreach ($items as $item) {
            $requiredQuantity = $item['quantity_required'] * $productionQuantity;
            $available = $item['current_stock'] >= $requiredQuantity;

            $availability[] = [
                'material_id' => $item['material_id'],
                'material_name' => $item['material_name'],
                'material_code' => $item['material_code'],
                'required_quantity' => $requiredQuantity,
                'available_quantity' => $item['current_stock'],
                'available' => $available,
                'shortage' => max(0, $requiredQuantity - $item['current_stock'])
            ];

            if (!$available) {
                $canProduce = false;
            }
        }

        return [
            'can_produce' => $canProduce,
            'availability' => $availability
        ];
    }

    /**
     * Get BOM items for export
     */
    public function getBOMItemsForExport($bomId)
    {
        return $this->select('bi.quantity_required, bi.unit_cost, bi.total_cost, p.product_name, p.product_code, p.unit, p.material_type')
            ->from('bom_items bi')
            ->join('products p', 'bi.material_id = p.id')
            ->where('bi.bom_id', $bomId)
            ->orderBy('p.material_type', 'ASC')
            ->orderBy('p.product_name', 'ASC')
            ->findAll();
    }
} 