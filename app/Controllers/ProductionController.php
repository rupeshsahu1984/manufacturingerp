<?php

namespace App\Controllers;

use App\Models\BillOfMaterials;
use App\Models\BOMComponent;
use App\Models\BOMOperation;
use App\Models\BOMByProduct;
use App\Models\WorkOrder;
use App\Models\JobCard;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\User;

class ProductionController extends BaseController
{
    protected $bomModel;
    protected $bomComponentModel;
    protected $bomOperationModel;
    protected $bomByProductModel;
    protected $workOrderModel;
    protected $jobCardModel;
    protected $itemModel;
    protected $warehouseModel;
    protected $userModel;

    public function __construct()
    {
        $this->bomModel = new BillOfMaterials();
        $this->bomComponentModel = new BOMComponent();
        $this->bomOperationModel = new BOMOperation();
        $this->bomByProductModel = new BOMByProduct();
        $this->workOrderModel = new WorkOrder();
        $this->jobCardModel = new JobCard();
        $this->itemModel = new Item();
        $this->warehouseModel = new Warehouse();
        $this->userModel = new User();
    }

    /**
     * Production Dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Production Dashboard - PRODX',
            'totalBOMs' => $this->bomModel->countAll(),
            'activeBOMs' => $this->bomModel->where('status', 'released')->countAllResults(),
            'pendingWorkOrders' => $this->workOrderModel->whereIn('status', ['released', 'draft'])->countAllResults(),
            'inProgressWorkOrders' => $this->workOrderModel->where('status', 'in_progress')->countAllResults(),
            'completedWorkOrders' => $this->workOrderModel->where('status', 'completed')->countAllResults(),
            'recentBOMs' => $this->bomModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
            'recentWorkOrders' => $this->workOrderModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
            'bomStats' => $this->bomModel->getBOMStats(),
            'productionAnalytics' => $this->bomModel->getBOMAnalytics()
        ];

        return view('production/index', $data);
    }

    /**
     * Bill of Materials (BOM) Management
     */
    public function boms()
    {
        $data = [
            'title' => 'Bill of Materials - PRODX',
            'boms' => [],
            'items' => [],
            'bomTypes' => [],
            'bomStatuses' => [],
        ];
        try {
            $data['boms'] = $this->bomModel->getWithRelations();
            $data['items'] = $this->itemModel->whereIn('material_type', ['finished_goods', 'raw_material', 'packaging'])->where('status', 'active')->findAll();
            $data['bomTypes'] = $this->bomModel->getBOMTypes();
            $data['bomStatuses'] = $this->bomModel->getBOMStatuses();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::boms: ' . $e->getMessage());
            $data['bom_error'] = 'BOM list could not be loaded. Check BOM and items tables match the expected schema.';
        }

        return view('production/boms/index', $data);
    }

    public function bomCreate()
    {
        $data = [
            'title' => 'Create BOM - PRODX',
            'items' => $this->itemModel->whereIn('material_type', ['finished_goods', 'raw_material', 'packaging'])->where('status', 'active')->findAll(),
            'bomTypes' => $this->bomModel->getBOMTypes(),
            'bomNumber' => $this->bomModel->generateBOMNumber()
        ];

        return view('production/boms/create', $data);
    }

    public function bomStore()
    {
        $rules = [
            'item_id_fg' => 'required|integer',
            'revision' => 'required',
            'uom' => 'required',
            'qty_per' => 'required|numeric|greater_than[0]',
            'effective_from' => 'required|valid_date',
            'bom_type' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check for circular references
        if ($this->bomModel->checkCircularReference($this->request->getPost('item_id_fg'))) {
            return redirect()->back()->withInput()->with('error', 'Circular reference detected in BOM structure.');
        }

        $bomData = [
            'bom_number' => $this->request->getPost('bom_number'),
            'item_id_fg' => $this->request->getPost('item_id_fg'),
            'revision' => $this->request->getPost('revision'),
            'description' => $this->request->getPost('description'),
            'uom' => $this->request->getPost('uom'),
            'qty_per' => $this->request->getPost('qty_per'),
            'effective_from' => $this->request->getPost('effective_from'),
            'effective_to' => $this->request->getPost('effective_to') ?: null,
            'bom_type' => $this->request->getPost('bom_type'),
            'is_phantom' => $this->request->getPost('is_phantom') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'status' => 'draft',
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $bomId = $this->bomModel->createBOM($bomData);

        if ($bomId) {
            // Add components
            $components = $this->request->getPost('components');
            if ($components) {
                foreach ($components as $index => $component) {
                    if (!empty($component['item_id'])) {
                        $componentData = [
                            'bom_id' => $bomId,
                            'component_item_id' => $component['item_id'],
                            'qty' => $component['qty'],
                            'uom' => $component['uom'],
                            'scrap_pct' => isset($component['scrap_pct']) ? $component['scrap_pct'] : 0,
                            'yield_pct' => isset($component['yield_pct']) ? $component['yield_pct'] : 100,
                            'is_alternate' => isset($component['is_alternate']) ? $component['is_alternate'] : 0,
                            'priority' => isset($component['priority']) ? $component['priority'] : 1,
                            'position' => $index + 1,
                            'reference_designator' => isset($component['reference_designator']) ? $component['reference_designator'] : '',
                            'notes' => isset($component['notes']) ? $component['notes'] : '',
                            'created_by' => session()->get('user_id') ?? 1
                        ];
                        $this->bomComponentModel->createComponent($componentData);
                    }
                }
            }

            // Add operations
            $operations = $this->request->getPost('operations');
            if ($operations) {
                foreach ($operations as $index => $operation) {
                    if (!empty($operation['workcenter_id'])) {
                        $operationData = [
                            'bom_id' => $bomId,
                            'operation_seq' => $index + 1,
                            'workcenter_id' => $operation['workcenter_id'],
                            'operation_name' => $operation['operation_name'],
                            'setup_time' => isset($operation['setup_time']) ? $operation['setup_time'] : 0,
                            'run_time_per_unit' => isset($operation['run_time_per_unit']) ? $operation['run_time_per_unit'] : 0,
                            'labor_rate' => isset($operation['labor_rate']) ? $operation['labor_rate'] : 0,
                            'machine_rate' => isset($operation['machine_rate']) ? $operation['machine_rate'] : 0,
                            'notes' => isset($operation['notes']) ? $operation['notes'] : '',
                            'created_by' => session()->get('user_id') ?? 1
                        ];
                        $this->bomOperationModel->createOperation($operationData);
                    }
                }
            }

            // Add by-products
            $byProducts = $this->request->getPost('by_products');
            if ($byProducts) {
                foreach ($byProducts as $byProduct) {
                    if (!empty($byProduct['item_id'])) {
                        $byProductData = [
                            'bom_id' => $bomId,
                            'byprod_item_id' => $byProduct['item_id'],
                            'yield_qty' => $byProduct['yield_qty'],
                            'yield_pct' => isset($byProduct['yield_pct']) ? $byProduct['yield_pct'] : 0,
                            'valuation_method' => isset($byProduct['valuation_method']) ? $byProduct['valuation_method'] : 'standard_cost',
                            'notes' => isset($byProduct['notes']) ? $byProduct['notes'] : '',
                            'created_by' => session()->get('user_id') ?? 1
                        ];
                        $this->bomByProductModel->createByProduct($byProductData);
                    }
                }
            }

            return redirect()->to('/production/boms')->with('success', 'BOM created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create BOM.');
        }
    }

    public function bomView($id)
    {
        $bom = $this->bomModel->getWithRelations($id);
        if (!$bom) {
            return redirect()->to('/production/boms')->with('error', 'BOM not found.');
        }

        $data = [
            'title' => 'View BOM - PRODX',
            'bom' => $bom,
            'components' => $this->bomComponentModel->getByBOM($id),
            'operations' => $this->bomOperationModel->getByBOM($id),
            'byProducts' => $this->bomByProductModel->getByBOM($id)
        ];

        return view('production/boms/view', $data);
    }

    public function bomEdit($id)
    {
        $bom = $this->bomModel->getWithRelations($id);
        if (!$bom) {
            return redirect()->to('/production/boms')->with('error', 'BOM not found.');
        }

        $data = [
            'title' => 'Edit BOM - PRODX',
            'bom' => $bom,
            'items' => $this->itemModel->whereIn('material_type', ['finished_goods', 'raw_material', 'packaging'])->where('status', 'active')->findAll(),
            'bomTypes' => $this->bomModel->getBOMTypes(),
            'components' => $this->bomComponentModel->getByBOM($id),
            'operations' => $this->bomOperationModel->getByBOM($id),
            'byProducts' => $this->bomByProductModel->getByBOM($id)
        ];

        return view('production/boms/edit', $data);
    }

    public function bomUpdate($id)
    {
        $rules = [
            'item_id_fg' => 'required|integer',
            'revision' => 'required',
            'uom' => 'required',
            'qty_per' => 'required|numeric|greater_than[0]',
            'effective_from' => 'required|valid_date',
            'bom_type' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check for circular references
        if ($this->bomModel->checkCircularReference($this->request->getPost('item_id_fg'), $id)) {
            return redirect()->back()->withInput()->with('error', 'Circular reference detected in BOM structure.');
        }

        $bomData = [
            'item_id_fg' => $this->request->getPost('item_id_fg'),
            'revision' => $this->request->getPost('revision'),
            'description' => $this->request->getPost('description'),
            'uom' => $this->request->getPost('uom'),
            'qty_per' => $this->request->getPost('qty_per'),
            'effective_from' => $this->request->getPost('effective_from'),
            'effective_to' => $this->request->getPost('effective_to') ?: null,
            'bom_type' => $this->request->getPost('bom_type'),
            'is_phantom' => $this->request->getPost('is_phantom') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'notes' => $this->request->getPost('notes'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->bomModel->updateBOM($id, $bomData)) {
            // Update components
            $this->bomComponentModel->where('bom_id', $id)->delete();
            $components = $this->request->getPost('components');
            if ($components) {
                foreach ($components as $index => $component) {
                    if (!empty($component['item_id'])) {
                        $componentData = [
                            'bom_id' => $id,
                            'component_item_id' => $component['item_id'],
                            'qty' => $component['qty'],
                            'uom' => $component['uom'],
                            'scrap_pct' => isset($component['scrap_pct']) ? $component['scrap_pct'] : 0,
                            'yield_pct' => isset($component['yield_pct']) ? $component['yield_pct'] : 100,
                            'is_alternate' => isset($component['is_alternate']) ? $component['is_alternate'] : 0,
                            'priority' => isset($component['priority']) ? $component['priority'] : 1,
                            'position' => $index + 1,
                            'reference_designator' => isset($component['reference_designator']) ? $component['reference_designator'] : '',
                            'notes' => isset($component['notes']) ? $component['notes'] : '',
                            'created_by' => session()->get('user_id') ?? 1
                        ];
                        $this->bomComponentModel->createComponent($componentData);
                    }
                }
            }

            // Update operations
            $this->bomOperationModel->where('bom_id', $id)->delete();
            $operations = $this->request->getPost('operations');
            if ($operations) {
                foreach ($operations as $index => $operation) {
                    if (!empty($operation['workcenter_id'])) {
                        $operationData = [
                            'bom_id' => $id,
                            'operation_seq' => $index + 1,
                            'workcenter_id' => $operation['workcenter_id'],
                            'operation_name' => $operation['operation_name'],
                            'setup_time' => isset($operation['setup_time']) ? $operation['setup_time'] : 0,
                            'run_time_per_unit' => isset($operation['run_time_per_unit']) ? $operation['run_time_per_unit'] : 0,
                            'labor_rate' => isset($operation['labor_rate']) ? $operation['labor_rate'] : 0,
                            'machine_rate' => isset($operation['machine_rate']) ? $operation['machine_rate'] : 0,
                            'notes' => isset($operation['notes']) ? $operation['notes'] : '',
                            'created_by' => session()->get('user_id') ?? 1
                        ];
                        $this->bomOperationModel->createOperation($operationData);
                    }
                }
            }

            // Update by-products
            $this->bomByProductModel->where('bom_id', $id)->delete();
            $byProducts = $this->request->getPost('by_products');
            if ($byProducts) {
                foreach ($byProducts as $byProduct) {
                    if (!empty($byProduct['item_id'])) {
                        $byProductData = [
                            'bom_id' => $id,
                            'byprod_item_id' => $byProduct['item_id'],
                            'yield_qty' => $byProduct['yield_qty'],
                            'yield_pct' => isset($byProduct['yield_pct']) ? $byProduct['yield_pct'] : 0,
                            'valuation_method' => isset($byProduct['valuation_method']) ? $byProduct['valuation_method'] : 'standard_cost',
                            'notes' => isset($byProduct['notes']) ? $byProduct['notes'] : '',
                            'created_by' => session()->get('user_id') ?? 1
                        ];
                        $this->bomByProductModel->createByProduct($byProductData);
                    }
                }
            }

            return redirect()->to('/production/boms')->with('success', 'BOM updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update BOM.');
        }
    }

    public function bomApprove($id)
    {
        $bom = $this->bomModel->find($id);
        if (!$bom) {
            return redirect()->to('/production/boms')->with('error', 'BOM not found.');
        }

        if ($bom['status'] !== 'under_review') {
            return redirect()->to('/production/boms')->with('error', 'BOM is not under review.');
        }

        $notes = $this->request->getPost('notes') ?? '';
        
        if ($this->bomModel->approveBOM($id, session()->get('user_id') ?? 1, $notes)) {
            return redirect()->to('/production/boms')->with('success', 'BOM approved and released successfully!');
        } else {
            return redirect()->to('/production/boms')->with('error', 'Failed to approve BOM.');
        }
    }

    public function bomExplode($id)
    {
        $bom = $this->bomModel->find($id);
        if (!$bom) {
            return redirect()->to('/production/boms')->with('error', 'BOM not found.');
        }

        $quantity = $this->request->getPost('quantity') ?? 1;
        $exploded = $this->bomModel->explodeBOM($id, $quantity);

        $data = [
            'title' => 'BOM Explosion - PRODX',
            'bom' => $bom,
            'quantity' => $quantity,
            'exploded' => $exploded
        ];

        return view('production/boms/explode', $data);
    }

    /**
     * Work Orders Management
     */
    public function workOrders()
    {
        try {
            $workOrders = $this->workOrderModel->getWithRelations();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::workOrders list: ' . $e->getMessage());
            $workOrders = [];
        }

        try {
            $boms = $this->bomModel->where('status', 'released')->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::workOrders boms: ' . $e->getMessage());
            $boms = [];
        }

        $data = [
            'title' => 'Work Orders - PRODX',
            'workOrders' => $workOrders,
            'boms' => $boms,
            'warehouses' => $this->warehouseModel->findAll()
        ];

        return view('production/work_orders/index', $data);
    }

    public function workOrderCreate()
    {
        $data = [
            'title' => 'Create Work Order - PRODX',
            'boms' => $this->bomModel->where('status', 'released')->findAll(),
            'warehouses' => $this->warehouseModel->findAll(),
            'workOrderNumber' => $this->workOrderModel->generateWONumber()
        ];

        return view('production/work_orders/create', $data);
    }

    public function workOrderStore()
    {
        $rules = [
            'bom_id' => 'required|integer',
            'order_qty' => 'required|numeric|greater_than[0]',
            'due_date' => 'required|valid_date',
            'warehouse_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bomId = (int) $this->request->getPost('bom_id');
        $bom = $this->bomModel->find($bomId);
        if (!$bom) {
            return redirect()->back()->withInput()->with('error', 'Invalid BOM selected.');
        }

        $uom = $this->request->getPost('uom');
        if ($uom === null || $uom === '') {
            $fgItem = $this->itemModel->find($bom['item_id_fg']);
            $uom = $fgItem['uom'] ?? 'EA';
        }

        $workOrderData = [
            'wo_number' => $this->request->getPost('work_order_number') ?: null,
            'item_id_fg' => (int) $bom['item_id_fg'],
            'bom_id' => $bomId,
            'order_qty' => $this->request->getPost('order_qty'),
            'uom' => $uom,
            'due_date' => $this->request->getPost('due_date'),
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'priority' => $this->request->getPost('priority') ?? 'normal',
            'status' => 'released',
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $workOrderId = $this->workOrderModel->createWorkOrder($workOrderData);

        if ($workOrderId) {
            return redirect()->to(base_url('work-orders'))->with('success', 'Work Order created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create Work Order.');
    }

    public function workOrderView($id)
    {
        $workOrder = $this->workOrderModel->getWithRelations($id);
        if (!$workOrder) {
            return redirect()->to(base_url('work-orders'))->with('error', 'Work Order not found.');
        }

        $data = [
            'title' => 'View Work Order - PRODX',
            'workOrder' => $workOrder,
            'bom' => $this->bomModel->getWithRelations($workOrder['bom_id']),
            'components' => $this->bomComponentModel->getByBOM($workOrder['bom_id']),
            'operations' => $this->bomOperationModel->getByBOM($workOrder['bom_id']),
            'jobCards' => $this->jobCardModel->getByWorkOrder($id)
        ];

        return view('production/work_orders/view', $data);
    }

    public function workOrderStart($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        if (!$workOrder) {
            return redirect()->to(base_url('work-orders'))->with('error', 'Work Order not found.');
        }

        if (! in_array($workOrder['status'], ['released', 'draft'], true)) {
            return redirect()->to(base_url('work-orders'))->with('error', 'Work Order cannot be started from its current status.');
        }

        // Check component availability
        $availability = $this->bomComponentModel->checkComponentAvailability($workOrder['bom_id'], $workOrder['warehouse_id']);
        $shortages = array_filter($availability, function($item) {
            return !$item['is_available'];
        });

        if (!empty($shortages)) {
            $shortageMessage = 'Insufficient stock for: ';
            foreach ($shortages as $shortage) {
                $shortageMessage .= $shortage['item_code'] . ' (Required: ' . $shortage['required_qty'] . ', Available: ' . $shortage['available_qty'] . '), ';
            }
            return redirect()->to(base_url('work-orders'))->with('error', rtrim($shortageMessage, ', '));
        }

        // Start work order
        $updateData = [
            'status' => 'in_progress',
            'actual_start' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->workOrderModel->update($id, $updateData)) {
            // Create job cards for each BOM operation
            $operations = $this->bomOperationModel->getByBOM($workOrder['bom_id']);
            foreach ($operations as $operation) {
                $jobCardData = [
                    'work_order_id' => $id,
                    'operation_id' => $operation['id'],
                    'item_id' => (int) $workOrder['item_id_fg'],
                    'planned_qty' => $workOrder['order_qty'],
                    'setup_time_planned' => $operation['setup_time'],
                    'run_time_planned' => $operation['run_time_per_unit'],
                    'status' => 'released',
                    'workcenter_id' => $operation['workcenter_id'],
                    'created_by' => session()->get('user_id') ?? 1
                ];
                $this->jobCardModel->createJobCard($jobCardData);
            }

            return redirect()->to(base_url('work-orders'))->with('success', 'Work Order started successfully! Job Cards created.');
        }

        return redirect()->to(base_url('work-orders'))->with('error', 'Failed to start Work Order.');
    }

    public function workOrderComplete($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        if (!$workOrder) {
            return redirect()->to(base_url('work-orders'))->with('error', 'Work Order not found.');
        }

        if ($workOrder['status'] !== 'in_progress') {
            return redirect()->to(base_url('work-orders'))->with('error', 'Work Order is not in progress.');
        }

        // Check if all job cards are completed
        $jobCards = $this->jobCardModel->getByWorkOrder($id);
        $incompleteCards = array_filter($jobCards, function ($card) {
            return $card['status'] !== 'completed';
        });

        if (! empty($incompleteCards)) {
            return redirect()->to(base_url('work-orders'))->with('error', 'Cannot complete Work Order. Some Job Cards are still in progress.');
        }

        // Complete work order
        $updateData = [
            'status' => 'completed',
            'actual_end' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->workOrderModel->update($id, $updateData)) {
            return redirect()->to(base_url('work-orders'))->with('success', 'Work Order completed successfully!');
        }

        return redirect()->to(base_url('work-orders'))->with('error', 'Failed to complete Work Order.');
    }

    /**
     * Job Cards Management
     */
    public function jobCards()
    {
        try {
            $jobCards = $this->jobCardModel->getWithRelations();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::jobCards: ' . $e->getMessage());
            $jobCards = [];
        }

        $data = [
            'title' => 'Job Cards - PRODX',
            'jobCards' => $jobCards,
            'workOrders' => $this->workOrderModel->where('status', 'in_progress')->findAll()
        ];

        return view('production/job_cards/index', $data);
    }

    public function jobCardView($id)
    {
        $jobCard = $this->jobCardModel->getWithRelations($id);
        if (!$jobCard) {
            return redirect()->to('/production/job-cards')->with('error', 'Job Card not found.');
        }

        $data = [
            'title' => 'View Job Card - PRODX',
            'jobCard' => $jobCard,
            'workOrder' => $this->workOrderModel->getWithRelations($jobCard['work_order_id'])
        ];

        return view('production/job_cards/view', $data);
    }

    public function jobCardStart($id)
    {
        $jobCard = $this->jobCardModel->find($id);
        if (!$jobCard) {
            return redirect()->to('/production/job-cards')->with('error', 'Job Card not found.');
        }

        if ($jobCard['status'] !== 'released') {
            return redirect()->to('/production/job-cards')->with('error', 'Job Card cannot be started from its current status.');
        }

        $updateData = [
            'status' => 'in_progress',
            'start_time' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->jobCardModel->update($id, $updateData)) {
            return redirect()->to('/production/job-cards')->with('success', 'Job Card started successfully!');
        } else {
            return redirect()->to('/production/job-cards')->with('error', 'Failed to start Job Card.');
        }
    }

    public function jobCardComplete($id)
    {
        $jobCard = $this->jobCardModel->find($id);
        if (!$jobCard) {
            return redirect()->to('/production/job-cards')->with('error', 'Job Card not found.');
        }

        if ($jobCard['status'] !== 'in_progress') {
            return redirect()->to('/production/job-cards')->with('error', 'Job Card is not in progress.');
        }

        $updateData = [
            'status' => 'completed',
            'end_time' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->jobCardModel->update($id, $updateData)) {
            return redirect()->to('/production/job-cards')->with('success', 'Job Card completed successfully!');
        } else {
            return redirect()->to('/production/job-cards')->with('error', 'Failed to complete Job Card.');
        }
    }

    /**
     * Material Requirements Planning (MRP)
     */
    public function mrp()
    {
        $data = [
            'title' => 'Material Requirements Planning - PRODX',
            'workOrders' => $this->workOrderModel->whereIn('status', ['released', 'draft', 'in_progress'])->findAll(),
            'boms' => $this->bomModel->where('status', 'released')->findAll()
        ];

        return view('production/mrp/index', $data);
    }

    public function mrpRun()
    {
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $bomIds = $this->request->getPost('bom_ids');

        if (!$startDate || !$endDate || !$bomIds) {
            return redirect()->back()->with('error', 'Please provide start date, end date, and select BOMs.');
        }

        // Run MRP calculation
        $mrpResults = [];
        foreach ($bomIds as $bomId) {
            $bom = $this->bomModel->find($bomId);
            if ($bom && $bom['status'] === 'released') {
                $exploded = $this->bomModel->explodeBOM($bomId, 1);
                $mrpResults[$bomId] = [
                    'bom' => $bom,
                    'requirements' => $exploded
                ];
            }
        }

        $data = [
            'title' => 'MRP Results - PRODX',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'mrpResults' => $mrpResults
        ];

        return view('production/mrp/results', $data);
    }

    /**
     * Production Reports
     */
    public function reports()
    {
        try {
            $bomStats = $this->bomModel->getBOMStats();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::reports bomStats: ' . $e->getMessage());
            $bomStats = [];
        }
        try {
            $workOrderStats = $this->workOrderModel->getWorkOrderStats();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::reports woStats: ' . $e->getMessage());
            $workOrderStats = [];
        }
        try {
            $productionAnalytics = $this->bomModel->getBOMAnalytics();
        } catch (\Throwable $e) {
            log_message('error', 'ProductionController::reports analytics: ' . $e->getMessage());
            $productionAnalytics = [];
        }

        $data = [
            'title' => 'Production Reports - PRODX',
            'bomStats' => $bomStats,
            'workOrderStats' => $workOrderStats,
            'productionAnalytics' => $productionAnalytics
        ];

        return view('production/reports/index', $data);
    }

    public function exportReport($type)
    {
        switch ($type) {
            case 'bom':
                $data = $this->bomModel->getWithRelations();
                $filename = 'bom_report_' . date('Y-m-d') . '.csv';
                break;
            case 'work_orders':
                $data = $this->workOrderModel->getWithRelations();
                $filename = 'work_orders_report_' . date('Y-m-d') . '.csv';
                break;
            case 'job_cards':
                $data = $this->jobCardModel->getWithRelations();
                $filename = 'job_cards_report_' . date('Y-m-d') . '.csv';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // Add headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            // Add data rows
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit;
    }
}
