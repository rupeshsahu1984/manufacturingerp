<?php

namespace App\Controllers;

use App\Models\BOM;
use App\Models\BOMItem;
use App\Models\Product;
use App\Models\Category;

class ProductionSettingsController extends BaseController
{
    protected $bomModel;
    protected $bomItemModel;
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->bomModel = new BOM();
        $this->bomItemModel = new BOMItem();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * Display production settings dashboard
     */
    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'finished_product_id' => $this->request->getGet('finished_product_id'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Production Settings - PRODX',
            'boms' => $this->bomModel->getBOMs($filters),
            'stats' => $this->bomModel->getBOMStats(),
            'finished_products' => $this->productModel->getFinishedGoods(),
            'material_stats' => $this->productModel->getMaterialStats(),
            'recent_materials' => $this->productModel->getRecentMaterials(5),
            'filters' => $filters
        ];

        return view('production_settings/index', $data);
    }

    /**
     * Show BOM creation form
     */
    public function create()
    {
        $data = [
            'title' => 'Create Production Settings - PRODX',
            'finishedProducts' => $this->productModel->getFinishedGoods(),
            'materials' => $this->productModel->getProductsForBOM(),
            'wasteMaterials' => $this->productModel->getWasteMaterials(),
            'categories' => $this->categoryModel->findAll()
        ];

        return view('production_settings/create', $data);
    }

    /**
     * Store new BOM with waste calculation
     */
    public function store()
    {
        $rules = [
            'finished_product_id' => 'required|integer',
            'description' => 'permit_empty|max_length[500]',
            'version' => 'required|max_length[20]',
            'production_quantity' => 'permit_empty|numeric|greater_than[0]',
            'batch_size' => 'permit_empty|numeric|greater_than[0]',
            'production_efficiency' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
            'base_unit' => 'permit_empty|max_length[20]',
            'materials' => 'required|array|min_length[1]',
            'materials.*.material_id' => 'required|integer',
            'materials.*.quantity' => 'required|numeric|greater_than[0]',
            'materials.*.waste_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'waste_materials' => 'permit_empty|array',
            'waste_materials.*.material_id' => 'permit_empty|integer',
            'waste_materials.*.quantity' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'finished_product_id' => $this->request->getPost('finished_product_id'),
            'description' => $this->request->getPost('description'),
            'version' => $this->request->getPost('version'),
            'production_quantity' => $this->request->getPost('production_quantity') ?? 1,
            'batch_size' => $this->request->getPost('batch_size') ?? 1,
            'production_efficiency' => $this->request->getPost('production_efficiency') ?? 95,
            'base_unit' => $this->request->getPost('base_unit') ?? 'pieces',
            'status' => 'draft',
            'created_by' => session()->get('user_id') ?? 1
        ];

        $materials = $this->request->getPost('materials');
        $wasteMaterials = $this->request->getPost('waste_materials') ?? [];
        $productionQuantity = $data['production_quantity'];
        $productionEfficiency = $data['production_efficiency'];
        
        // Calculate waste and total quantities with production planning
        foreach ($materials as &$material) {
            $wasteAmount = isset($material['waste_amount']) ? $material['waste_amount'] : 0;
            $baseQuantity = $material['quantity'];
            
            // Adjust for production efficiency
            $efficiencyFactor = $productionEfficiency / 100;
            $adjustedQuantity = $baseQuantity / $efficiencyFactor;
            
            // Calculate waste quantities (waste amount per product)
            $adjustedWasteAmount = $wasteAmount / $efficiencyFactor;
            $totalQuantity = $adjustedQuantity + $adjustedWasteAmount;
            
            // Calculate waste percentage for display
            $wastePercentage = $adjustedQuantity > 0 ? ($adjustedWasteAmount / $adjustedQuantity) * 100 : 0;
            
            // Get material details
            $materialInfo = $this->productModel->find($material['material_id']);
            $unitCost = isset($materialInfo['unit_price']) ? $materialInfo['unit_price'] : 0;
            $totalCost = $totalQuantity * $unitCost;
            
            // Store calculated values
            $material['quantity_required'] = $adjustedQuantity;
            $material['waste_amount'] = $adjustedWasteAmount;
            $material['waste_percentage'] = $wastePercentage;
            $material['total_quantity'] = $totalQuantity;
            $material['unit_cost'] = $unitCost;
            $material['total_cost'] = $totalCost;
            $material['efficiency_adjusted'] = true;
        }

        // Process waste materials
        foreach ($wasteMaterials as &$wasteMaterial) {
            if (!empty($wasteMaterial['material_id']) && !empty($wasteMaterial['quantity'])) {
                $baseQuantity = $wasteMaterial['quantity'];
                
                // Adjust for production efficiency
                $efficiencyFactor = $productionEfficiency / 100;
                $adjustedQuantity = $baseQuantity / $efficiencyFactor;
                
                // Get waste material details
                $wasteMaterialInfo = $this->productModel->find($wasteMaterial['material_id']);
                $unitValue = isset($wasteMaterialInfo['unit_price']) ? $wasteMaterialInfo['unit_price'] : 0;
                $totalValue = $adjustedQuantity * $unitValue;
                
                // Store calculated values
                $wasteMaterial['quantity_generated'] = $adjustedQuantity;
                $wasteMaterial['unit_value'] = $unitValue;
                $wasteMaterial['total_value'] = $totalValue;
                $wasteMaterial['efficiency_adjusted'] = true;
            }
        }

        try {
            $bomId = $this->bomModel->createBOMWithItems($data, $materials, $wasteMaterials);
            
            if ($bomId) {
                return redirect()->to('production-settings')->with('success', 'Production settings created successfully with waste material tracking!');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create production settings.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show BOM details with waste calculation
     */
    public function show($id)
    {
        $bom = $this->bomModel->getBOMWithItems($id);
        
        if (!$bom) {
            return redirect()->to('production-settings')->with('error', 'Production settings not found.');
        }

        // Calculate production metrics
        $productionMetrics = $this->calculateProductionMetrics($bom);

        $data = [
            'title' => 'Production Settings Details - PRODX',
            'bom' => $bom,
            'production_metrics' => $productionMetrics
        ];

        return view('production_settings/show', $data);
    }

    /**
     * Show BOM edit form
     */
    public function edit($id)
    {
        $bom = $this->bomModel->getBOMWithItems($id);
        
        if (!$bom) {
            return redirect()->to('production-settings')->with('error', 'Production settings not found.');
        }

        $data = [
            'title' => 'Edit Production Settings - PRODX',
            'bom' => $bom,
            'bomItems' => isset($bom['items']) ? $bom['items'] : [],
            'finishedProducts' => $this->productModel->getFinishedGoods(),
            'materials' => $this->productModel->getProductsForBOM(),
            'categories' => $this->categoryModel->findAll()
        ];

        return view('production_settings/edit', $data);
    }

    /**
     * Update BOM with waste calculation
     */
    public function update($id)
    {
        $bom = $this->bomModel->find($id);
        
        if (!$bom) {
            return redirect()->to('production-settings')->with('error', 'Production settings not found.');
        }

        $rules = [
            'finished_product_id' => 'required|integer',
            'description' => 'permit_empty|max_length[500]',
            'version' => 'required|max_length[20]',
            'items' => 'required|array|min_length[1]',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity_required' => 'required|numeric|greater_than[0]',
            'items.*.waste_percentage' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'finished_product_id' => $this->request->getPost('finished_product_id'),
            'description' => $this->request->getPost('description'),
            'version' => $this->request->getPost('version'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        $items = $this->request->getPost('items');
        
        // Calculate waste and total quantities
        foreach ($items as &$item) {
            $wastePercentage = isset($item['waste_percentage']) ? $item['waste_percentage'] : 0;
            $wasteQuantity = ($item['quantity_required'] * $wastePercentage) / 100;
            $totalQuantity = $item['quantity_required'] + $wasteQuantity;
            
            $item['waste_quantity'] = $wasteQuantity;
            $item['total_quantity'] = $totalQuantity;
            
            // Get material cost
            $material = $this->productModel->find($item['material_id']);
            $item['unit_cost'] = isset($material['unit_price']) ? $material['unit_price'] : 0;
            $item['total_cost'] = $totalQuantity * $item['unit_cost'];
        }

        try {
            $success = $this->bomModel->updateBOMWithItems($id, $data, $items);
            
            if ($success) {
                return redirect()->to('production-settings')->with('success', 'Production settings updated successfully!');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update production settings.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Calculate production metrics for a given quantity
     */
    public function calculateProduction($id)
    {
        $bom = $this->bomModel->getBOMWithItems($id);
        
        if (!$bom) {
            return $this->response->setJSON(['success' => false, 'message' => 'Production settings not found.']);
        }

        $productionQuantity = $this->request->getPost('quantity') ?? 1;
        $productionMetrics = $this->calculateProductionMetrics($bom, $productionQuantity);

        return $this->response->setJSON([
            'success' => true,
            'metrics' => $productionMetrics
        ]);
    }

    /**
     * Calculate production metrics
     */
    private function calculateProductionMetrics($bom, $productionQuantity = 1)
    {
        $totalMaterialCost = 0;
        $totalWasteCost = 0;
        $materialBreakdown = [];
        $wasteBreakdown = [];

        foreach ($bom['items'] as $item) {
            $wastePercentage = isset($item['waste_percentage']) ? $item['waste_percentage'] : 0;
            $wasteQuantity = ($item['quantity_required'] * $wastePercentage) / 100;
            $totalQuantity = $item['quantity_required'] + $wasteQuantity;
            
            // For production quantity
            $requiredQuantity = $item['quantity_required'] * $productionQuantity;
            $wasteQuantityForProduction = $wasteQuantity * $productionQuantity;
            $totalQuantityForProduction = $totalQuantity * $productionQuantity;
            
            $materialCost = $requiredQuantity * $item['unit_cost'];
            $wasteCost = $wasteQuantityForProduction * $item['unit_cost'];
            
            $totalMaterialCost += $materialCost;
            $totalWasteCost += $wasteCost;

            $materialBreakdown[] = [
                'material_name' => $item['material_name'],
                'material_code' => $item['material_code'],
                'required_quantity' => $requiredQuantity,
                'waste_quantity' => $wasteQuantityForProduction,
                'total_quantity' => $totalQuantityForProduction,
                'unit' => $item['unit'],
                'unit_cost' => $item['unit_cost'],
                'material_cost' => $materialCost,
                'waste_cost' => $wasteCost,
                'waste_percentage' => $wastePercentage
            ];

            if ($wasteQuantityForProduction > 0) {
                $wasteBreakdown[] = [
                    'material_name' => $item['material_name'],
                    'material_code' => $item['material_code'],
                    'waste_quantity' => $wasteQuantityForProduction,
                    'unit' => $item['unit'],
                    'waste_cost' => $wasteCost,
                    'waste_percentage' => $wastePercentage
                ];
            }
        }

        $finishedProduct = $this->productModel->find($bom['finished_product_id']);
        $unitCost = $totalMaterialCost / $productionQuantity;
        $wastePercentage = $totalMaterialCost > 0 ? ($totalWasteCost / $totalMaterialCost) * 100 : 0;

        return [
            'production_quantity' => $productionQuantity,
            'finished_product' => $finishedProduct,
            'total_material_cost' => $totalMaterialCost,
            'total_waste_cost' => $totalWasteCost,
            'unit_cost' => $unitCost,
            'waste_percentage' => $wastePercentage,
            'material_breakdown' => $materialBreakdown,
            'waste_breakdown' => $wasteBreakdown,
            'efficiency' => 100 - $wastePercentage
        ];
    }

    /**
     * Get material requirements for production
     */
    public function getMaterialRequirements($id)
    {
        $bom = $this->bomModel->getBOMWithItems($id);
        
        if (!$bom) {
            return $this->response->setJSON(['success' => false, 'message' => 'Production settings not found.']);
        }

        $productionQuantity = $this->request->getPost('quantity') ?? 1;
        $requirements = $this->bomItemModel->getMaterialRequirementsForQuantity($id, $productionQuantity);

        return $this->response->setJSON([
            'success' => true,
            'requirements' => $requirements
        ]);
    }

    /**
     * Check material availability for production
     */
    public function checkAvailability($id)
    {
        $bom = $this->bomModel->getBOMWithItems($id);
        
        if (!$bom) {
            return $this->response->setJSON(['success' => false, 'message' => 'Production settings not found.']);
        }

        $productionQuantity = $this->request->getPost('quantity') ?? 1;
        $availability = $this->bomItemModel->checkMaterialAvailabilityForProduction($id, $productionQuantity);

        return $this->response->setJSON([
            'success' => true,
            'availability' => $availability
        ]);
    }

    /**
     * Delete BOM
     */
    public function delete($id)
    {
        $bom = $this->bomModel->find($id);
        
        if (!$bom) {
            return redirect()->to('production-settings')->with('error', 'Production settings not found.');
        }

        try {
            $this->bomModel->delete($id);
            return redirect()->to('production-settings')->with('success', 'Production settings deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->to('production-settings')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Toggle BOM status
     */
    public function toggleStatus($id)
    {
        $bom = $this->bomModel->find($id);
        
        if (!$bom) {
            return $this->response->setJSON(['success' => false, 'message' => 'Production settings not found.']);
        }

        $newStatus = $bom['status'] === 'active' ? 'inactive' : 'active';
        
        try {
            $this->bomModel->update($id, ['status' => $newStatus]);
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
} 