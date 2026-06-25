<?php

namespace App\Controllers;

use App\Models\BOM;
use App\Models\BOMItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\ManufacturingOrder;

class ManufacturingController extends BaseController
{
    protected $bomModel;
    protected $bomItemModel;
    protected $productModel;
    protected $stockModel;
    protected $manufacturingOrderModel;

    public function __construct()
    {
        $this->bomModel = new BOM();
        $this->bomItemModel = new BOMItem();
        $this->productModel = new Product();
        $this->stockModel = new Stock();
        $this->manufacturingOrderModel = new ManufacturingOrder();
    }

    /**
     * Display manufacturing dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Manufacturing Dashboard - PRODX',
            'pendingOrders' => [],
            'activeProductions' => [],
            'completedProductions' => [],
            'productionStats' => [],
        ];
        try {
            $data['pendingOrders'] = $this->manufacturingOrderModel->getPendingOrders();
            $data['activeProductions'] = $this->manufacturingOrderModel->getActiveProductions();
            $data['completedProductions'] = $this->manufacturingOrderModel->getCompletedProductions();
            $data['productionStats'] = $this->manufacturingOrderModel->getProductionStats();
        } catch (\Throwable $e) {
            log_message('error', 'ManufacturingController::index: ' . $e->getMessage());
            $data['load_error'] = 'Manufacturing data could not be loaded. Ensure manufacturing order tables exist.';
        }

        return view('manufacturing/index', $data);
    }

    /**
     * Create manufacturing order
     */
    public function create()
    {
        $data = [
            'title' => 'Create Manufacturing Order - PRODX',
            'boms' => [],
            'finishedProducts' => [],
        ];
        try {
            $data['boms'] = $this->bomModel->getActiveBOMs();
            $data['finishedProducts'] = $this->productModel->getFinishedGoods();
        } catch (\Throwable $e) {
            log_message('error', 'ManufacturingController::create: ' . $e->getMessage());
        }

        return view('manufacturing/create', $data);
    }

    /**
     * Store manufacturing order and start production
     */
    public function store()
    {
        $rules = [
            'bom_id' => 'required|integer',
            'production_quantity' => 'required|numeric|greater_than[0]',
            'planned_start_date' => 'required|valid_date',
            'planned_completion_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bomId = $this->request->getPost('bom_id');
        $productionQuantity = $this->request->getPost('production_quantity');
        
        // Get BOM details
        $bom = $this->bomModel->getBOMWithItems($bomId);
        if (!$bom) {
            return redirect()->back()->withInput()->with('error', 'BOM not found.');
        }

        // Check stock availability
        $stockCheck = $this->checkStockAvailability($bom, $productionQuantity);
        if (!$stockCheck['available']) {
            return redirect()->back()->withInput()->with('error', 'Insufficient stock: ' . $stockCheck['message']);
        }

        $data = [
            'bom_id' => $bomId,
            'production_quantity' => $productionQuantity,
            'planned_start_date' => $this->request->getPost('planned_start_date'),
            'planned_completion_date' => $this->request->getPost('planned_completion_date'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'pending',
            'created_by' => session()->get('user_id') ?? 1
        ];

        try {
            $orderId = $this->manufacturingOrderModel->createOrder($data);
            
            if ($orderId) {
                return redirect()->to('manufacturing')->with('success', 'Manufacturing order created successfully!');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create manufacturing order.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Start production process
     */
    public function startProduction($orderId)
    {
        $order = $this->manufacturingOrderModel->find($orderId);
        if (!$order) {
            return redirect()->to('manufacturing')->with('error', 'Manufacturing order not found.');
        }

        if ($order['status'] !== 'pending') {
            return redirect()->to('manufacturing')->with('error', 'Order is not in pending status.');
        }

        $bom = $this->bomModel->getBOMWithItems($order['bom_id']);
        if (!$bom) {
            return redirect()->to('manufacturing')->with('error', 'BOM not found.');
        }

        // Check stock availability again
        $stockCheck = $this->checkStockAvailability($bom, $order['production_quantity']);
        if (!$stockCheck['available']) {
            return redirect()->to('manufacturing')->with('error', 'Insufficient stock: ' . $stockCheck['message']);
        }

        try {
            // Start production and update stock
            $this->processProduction($bom, $order);
            
            // Update order status
            $this->manufacturingOrderModel->update($orderId, [
                'status' => 'in_progress',
                'actual_start_date' => date('Y-m-d H:i:s'),
                'updated_by' => session()->get('user_id') ?? 1
            ]);

            return redirect()->to('manufacturing')->with('success', 'Production started successfully! Stock updated.');
        } catch (\Exception $e) {
            return redirect()->to('manufacturing')->with('error', 'Error starting production: ' . $e->getMessage());
        }
    }

    /**
     * Complete production process
     */
    public function completeProduction($orderId)
    {
        $order = $this->manufacturingOrderModel->find($orderId);
        if (!$order) {
            return redirect()->to('manufacturing')->with('error', 'Manufacturing order not found.');
        }

        if ($order['status'] !== 'in_progress') {
            return redirect()->to('manufacturing')->with('error', 'Order is not in progress.');
        }

        try {
            // Update order status
            $this->manufacturingOrderModel->update($orderId, [
                'status' => 'completed',
                'actual_completion_date' => date('Y-m-d H:i:s'),
                'updated_by' => session()->get('user_id') ?? 1
            ]);

            return redirect()->to('manufacturing')->with('success', 'Production completed successfully!');
        } catch (\Exception $e) {
            return redirect()->to('manufacturing')->with('error', 'Error completing production: ' . $e->getMessage());
        }
    }

    /**
     * Check stock availability for production
     */
    private function checkStockAvailability($bom, $productionQuantity)
    {
        foreach ($bom['items'] as $item) {
            $requiredQuantity = $item['total_quantity'] * $productionQuantity;
            $currentStock = $this->stockModel->getCurrentStock($item['material_id']);
            
            if ($currentStock < $requiredQuantity) {
                $material = $this->productModel->find($item['material_id']);
                return [
                    'available' => false,
                    'message' => $material['product_name'] . ' - Required: ' . $requiredQuantity . ', Available: ' . $currentStock
                ];
            }
        }
        
        return ['available' => true, 'message' => ''];
    }

    /**
     * Process production and update stock
     */
    private function processProduction($bom, $order)
    {
        $productionQuantity = $order['production_quantity'];
        
        // Update raw material stock (decrease)
        foreach ($bom['items'] as $item) {
            $consumedQuantity = $item['total_quantity'] * $productionQuantity;
            $this->stockModel->decreaseStock($item['material_id'], $consumedQuantity, 'manufacturing', $order['id']);
        }
        
        // Update finished goods stock (increase)
        $finishedProductId = $bom['finished_product_id'];
        $this->stockModel->increaseStock($finishedProductId, $productionQuantity, 'manufacturing', $order['id']);
        
        // Update waste materials stock (increase)
        if (isset($bom['waste_materials']) && !empty($bom['waste_materials'])) {
            foreach ($bom['waste_materials'] as $wasteItem) {
                $generatedQuantity = $wasteItem['quantity_generated'] * $productionQuantity;
                $this->stockModel->increaseStock($wasteItem['material_id'], $generatedQuantity, 'manufacturing_waste', $order['id']);
            }
        }
    }

    /**
     * Get production details
     */
    public function show($orderId)
    {
        $order = $this->manufacturingOrderModel->getOrderWithDetails($orderId);
        if (!$order) {
            return redirect()->to('manufacturing')->with('error', 'Manufacturing order not found.');
        }

        $data = [
            'title' => 'Manufacturing Order Details - PRODX',
            'order' => $order,
            'bom' => $this->bomModel->getBOMWithItems($order['bom_id'])
        ];

        return view('manufacturing/show', $data);
    }
}
