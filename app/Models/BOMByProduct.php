<?php

namespace App\Models;

use CodeIgniter\Model;

class BOMByProduct extends Model
{
    protected $table = 'bom_by_products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'bom_id',
        'byprod_item_id',
        'yield_pct',
        'yield_qty',
        'valuation_method',
        'unit_value',
        'total_value',
        'is_co_product',
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
        'byprod_item_id' => 'required|integer',
        'yield_pct' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
        'yield_qty' => 'required|numeric|greater_than[0]',
        'valuation_method' => 'required|in_list[standard_cost,market_price,negotiated_price,zero_value]',
        'unit_value' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'is_co_product' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'bom_id' => [
            'required' => 'BOM ID is required',
            'integer' => 'Invalid BOM ID'
        ],
        'byprod_item_id' => [
            'required' => 'By-product item is required',
            'integer' => 'Invalid by-product item ID'
        ],
        'yield_pct' => [
            'required' => 'Yield percentage is required',
            'numeric' => 'Yield percentage must be a number',
            'greater_than' => 'Yield percentage must be greater than 0',
            'less_than_equal_to' => 'Yield percentage cannot exceed 100'
        ],
        'yield_qty' => [
            'required' => 'Yield quantity is required',
            'numeric' => 'Yield quantity must be a number',
            'greater_than' => 'Yield quantity must be greater than 0'
        ],
        'valuation_method' => [
            'required' => 'Valuation method is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function bom()
    {
        return $this->belongsTo('App\Models\BillOfMaterials', 'bom_id', 'id');
    }

    public function byProduct()
    {
        return $this->belongsTo('App\Models\Item', 'byprod_item_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('bom_by_products.*, items.item_code, items.item_name, items.unit_of_measurement, items.standard_cost, bill_of_materials.bom_number, bill_of_materials.revision')
                        ->join('items', 'items.id = bom_by_products.byprod_item_id')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_by_products.bom_id');

        if ($id) {
            return $builder->where('bom_by_products.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByBOM($bomId)
    {
        return $this->select('bom_by_products.*, items.item_code, items.item_name, items.unit_of_measurement, items.standard_cost')
                    ->join('items', 'items.id = bom_by_products.byprod_item_id')
                    ->where('bom_id', $bomId)
                    ->orderBy('yield_pct', 'DESC')
                    ->findAll();
    }

    public function getByItem($itemId)
    {
        return $this->select('bom_by_products.*, bill_of_materials.bom_number, bill_of_materials.revision, bill_of_materials.status')
                    ->join('bill_of_materials', 'bill_of_materials.id = bom_by_products.bom_id')
                    ->where('byprod_item_id', $itemId)
                    ->orderBy('bill_of_materials.created_at', 'DESC')
                    ->findAll();
    }

    public function getCoProducts($bomId)
    {
        return $this->select('bom_by_products.*, items.item_code, items.item_name, items.unit_of_measurement')
                    ->join('items', 'items.id = bom_by_products.byprod_item_id')
                    ->where('bom_id', $bomId)
                    ->where('is_co_product', 1)
                    ->orderBy('yield_pct', 'DESC')
                    ->findAll();
    }

    public function getByProducts($bomId)
    {
        return $this->select('bom_by_products.*, items.item_code, items.item_name, items.unit_of_measurement')
                    ->join('items', 'items.id = bom_by_products.byprod_item_id')
                    ->where('bom_id', $bomId)
                    ->where('is_co_product', 0)
                    ->orderBy('yield_pct', 'DESC')
                    ->findAll();
    }

    public function createByProduct($data)
    {
        $byProductData = [
            'bom_id' => $data['bom_id'],
            'byprod_item_id' => $data['byprod_item_id'],
            'yield_pct' => $data['yield_pct'],
            'yield_qty' => $data['yield_qty'],
            'valuation_method' => $data['valuation_method'],
            'unit_value' => isset($data['unit_value']) ? $data['unit_value'] : 0,
            'total_value' => isset($data['total_value']) ? $data['total_value'] : 0,
            'is_co_product' => isset($data['is_co_product']) ? $data['is_co_product'] : 0,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        // Calculate total value if not provided
        if (!isset($data['total_value']) && isset($data['unit_value']) && isset($data['yield_qty'])) {
            $byProductData['total_value'] = $data['unit_value'] * $data['yield_qty'];
        }

        return $this->insert($byProductData);
    }

    public function updateByProduct($id, $data)
    {
        $byProduct = $this->find($id);
        if (!$byProduct) {
            return false;
        }

        $updateData = [
            'yield_pct' => isset($data['yield_pct']) ? $data['yield_pct'] : $byProduct['yield_pct'],
            'yield_qty' => isset($data['yield_qty']) ? $data['yield_qty'] : $byProduct['yield_qty'],
            'valuation_method' => isset($data['valuation_method']) ? $data['valuation_method'] : $byProduct['valuation_method'],
            'unit_value' => isset($data['unit_value']) ? $data['unit_value'] : $byProduct['unit_value'],
            'is_co_product' => isset($data['is_co_product']) ? $data['is_co_product'] : $byProduct['is_co_product'],
            'notes' => isset($data['notes']) ? $data['notes'] : $byProduct['notes']
        ];

        // Recalculate total value
        if (isset($updateData['unit_value']) || isset($updateData['yield_qty'])) {
            $unitValue = isset($updateData['unit_value']) ? $updateData['unit_value'] : $byProduct['unit_value'];
            $yieldQty = isset($updateData['yield_qty']) ? $updateData['yield_qty'] : $byProduct['yield_qty'];
            $updateData['total_value'] = $unitValue * $yieldQty;
        }

        return $this->update($id, $updateData);
    }

    public function calculateByProductValue($byProductId, $parentQuantity = 1)
    {
        $byProduct = $this->find($byProductId);
        if (!$byProduct) {
            return 0;
        }

        $item = model('Item')->find($byProduct['byprod_item_id']);
        if (!$item) {
            return 0;
        }

        $unitValue = 0;
        switch ($byProduct['valuation_method']) {
            case 'standard_cost':
                $unitValue = $item['standard_cost'];
                break;
            case 'market_price':
                $unitValue = isset($item['selling_price']) ? $item['selling_price'] : $item['standard_cost'];
                break;
            case 'negotiated_price':
                $unitValue = $byProduct['unit_value'];
                break;
            case 'zero_value':
                $unitValue = 0;
                break;
            default:
                $unitValue = $item['standard_cost'];
        }

        $actualYieldQty = ($byProduct['yield_pct'] / 100) * $parentQuantity;
        return $unitValue * $actualYieldQty;
    }

    public function getByProductSummary($bomId)
    {
        $byProducts = $this->getByBOM($bomId);
        $summary = [
            'total_by_products' => count($byProducts),
            'co_products' => 0,
            'by_products' => 0,
            'total_yield_pct' => 0,
            'total_value' => 0,
            'by_product_details' => []
        ];

        foreach ($byProducts as $byProduct) {
            if ($byProduct['is_co_product']) {
                $summary['co_products']++;
            } else {
                $summary['by_products']++;
            }

            $summary['total_yield_pct'] += $byProduct['yield_pct'];
            $summary['total_value'] += $byProduct['total_value'];

            $summary['by_product_details'][] = [
                'item_code' => $byProduct['item_code'],
                'item_name' => $byProduct['item_name'],
                'yield_pct' => $byProduct['yield_pct'],
                'yield_qty' => $byProduct['yield_qty'],
                'valuation_method' => $byProduct['valuation_method'],
                'unit_value' => $byProduct['unit_value'],
                'total_value' => $byProduct['total_value'],
                'is_co_product' => $byProduct['is_co_product']
            ];
        }

        return $summary;
    }

    public function validateYieldPercentages($bomId)
    {
        $byProducts = $this->getByBOM($bomId);
        $totalYield = 0;

        foreach ($byProducts as $byProduct) {
            $totalYield += $byProduct['yield_pct'];
        }

        // Total yield should not exceed 100% for co-products
        $coProducts = $this->getCoProducts($bomId);
        $coProductYield = 0;

        foreach ($coProducts as $coProduct) {
            $coProductYield += $coProduct['yield_pct'];
        }

        return [
            'total_yield' => $totalYield,
            'co_product_yield' => $coProductYield,
            'is_valid' => $coProductYield <= 100,
            'message' => $coProductYield > 100 ? 'Total co-product yield cannot exceed 100%' : 'Valid yield percentages'
        ];
    }

    public function getByProductStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('items.item_name, COUNT(*) as usage_count, AVG(yield_pct) as avg_yield_pct, AVG(total_value) as avg_value')
                        ->join('items', 'items.id = bom_by_products.byprod_item_id')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_by_products.bom_id')
                        ->groupBy('items.id, items.item_name');
        
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('usage_count', 'DESC')->findAll();
    }

    public function getByProductAnalytics($itemId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(bill_of_materials.created_at) as date, COUNT(*) as byproduct_count, AVG(yield_pct) as avg_yield_pct, SUM(total_value) as total_value')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_by_products.bom_id')
                        ->groupBy('DATE(bill_of_materials.created_at)');
        
        if ($itemId) {
            $builder->where('byprod_item_id', $itemId);
        }
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getHighYieldByProducts($bomId, $threshold = 20)
    {
        return $this->select('bom_by_products.*, items.item_code, items.item_name')
                    ->join('items', 'items.id = bom_by_products.byprod_item_id')
                    ->where('bom_id', $bomId)
                    ->where('yield_pct >', $threshold)
                    ->orderBy('yield_pct', 'DESC')
                    ->findAll();
    }

    public function getByProductUsageHistory($itemId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('bom_by_products.*, bill_of_materials.bom_number, bill_of_materials.revision, bill_of_materials.status')
                        ->join('bill_of_materials', 'bill_of_materials.id = bom_by_products.bom_id')
                        ->where('byprod_item_id', $itemId);
        
        if ($startDate) {
            $builder->where('bill_of_materials.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('bill_of_materials.created_at <=', $endDate);
        }

        return $builder->orderBy('bill_of_materials.created_at', 'DESC')->findAll();
    }

    public function getValuationMethods()
    {
        return [
            'standard_cost' => 'Standard Cost',
            'market_price' => 'Market Price',
            'negotiated_price' => 'Negotiated Price',
            'zero_value' => 'Zero Value'
        ];
    }

    public function getByProductTypes()
    {
        return [
            '0' => 'By-Product',
            '1' => 'Co-Product'
        ];
    }

    public function calculateCreditToParent($bomId, $parentQuantity = 1)
    {
        $byProducts = $this->getByBOM($bomId);
        $totalCredit = 0;

        foreach ($byProducts as $byProduct) {
            $credit = $this->calculateByProductValue($byProduct['id'], $parentQuantity);
            $totalCredit += $credit;
        }

        return $totalCredit;
    }

    public function getByProductCostImpact($bomId)
    {
        $byProducts = $this->getByBOM($bomId);
        $costImpact = [
            'total_by_product_value' => 0,
            'cost_reduction_pct' => 0,
            'by_product_details' => []
        ];

        $bom = model('BillOfMaterials')->find($bomId);
        if (!$bom) {
            return $costImpact;
        }

        $totalBOMCost = $bom['total_cost'];
        $totalByProductValue = 0;

        foreach ($byProducts as $byProduct) {
            $byProductValue = $byProduct['total_value'];
            $totalByProductValue += $byProductValue;

            $costImpact['by_product_details'][] = [
                'item_code' => $byProduct['item_code'],
                'item_name' => $byProduct['item_name'],
                'yield_pct' => $byProduct['yield_pct'],
                'value' => $byProductValue,
                'cost_reduction_pct' => $totalBOMCost > 0 ? ($byProductValue / $totalBOMCost) * 100 : 0
            ];
        }

        $costImpact['total_by_product_value'] = $totalByProductValue;
        $costImpact['cost_reduction_pct'] = $totalBOMCost > 0 ? ($totalByProductValue / $totalBOMCost) * 100 : 0;

        return $costImpact;
    }
}
