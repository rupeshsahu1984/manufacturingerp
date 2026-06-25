<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrder extends Model
{
    protected $table = 'work_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'wo_number',
        'item_id_fg',
        'bom_id',
        'routing_id',
        'order_qty',
        'uom',
        'due_date',
        'warehouse_id',
        'priority',
        'planner_code',
        'customer_order_id',
        'sales_order_id',
        'mrp_order_id',
        'schedule_start',
        'schedule_end',
        'actual_start',
        'actual_end',
        'status',
        'completion_qty',
        'scrap_qty',
        'rework_qty',
        'total_material_cost',
        'total_labor_cost',
        'total_overhead_cost',
        'total_cost',
        'backflush_material',
        'backflush_labor',
        'backflush_overhead',
        'qc_required',
        'parent_wo_id',
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
        'wo_number' => 'required|is_unique[work_orders.wo_number,id,{id}]',
        'item_id_fg' => 'required|integer',
        'bom_id' => 'required|integer',
        'order_qty' => 'required|numeric|greater_than[0]',
        'uom' => 'required',
        'due_date' => 'required|valid_date',
        'warehouse_id' => 'required|integer',
        'priority' => 'required|in_list[low,normal,high,urgent,critical]',
        'status' => 'required|in_list[draft,released,in_progress,on_hold,completed,closed,cancelled]'
    ];

    protected $validationMessages = [
        'wo_number' => [
            'required' => 'Work order number is required',
            'is_unique' => 'Work order number must be unique'
        ],
        'item_id_fg' => [
            'required' => 'Finished good item is required',
            'integer' => 'Invalid item ID'
        ],
        'bom_id' => [
            'required' => 'BOM is required',
            'integer' => 'Invalid BOM ID'
        ],
        'order_qty' => [
            'required' => 'Order quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'due_date' => [
            'required' => 'Due date is required',
            'valid_date' => 'Invalid due date'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function finishedGood()
    {
        return $this->belongsTo('App\Models\Item', 'item_id_fg', 'id');
    }

    public function bom()
    {
        return $this->belongsTo('App\Models\BillOfMaterials', 'bom_id', 'id');
    }

    public function routing()
    {
        return $this->belongsTo('App\Models\Routing', 'routing_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function customerOrder()
    {
        return $this->belongsTo('App\Models\CustomerOrder', 'customer_order_id', 'id');
    }

    public function salesOrder()
    {
        return $this->belongsTo('App\Models\SalesOrder', 'sales_order_id', 'id');
    }

    public function parentWO()
    {
        return $this->belongsTo('App\Models\WorkOrder', 'parent_wo_id', 'id');
    }

    public function childWOs()
    {
        return $this->hasMany('App\Models\WorkOrder', 'parent_wo_id', 'id');
    }

    public function operations()
    {
        return $this->hasMany('App\Models\WorkOrderOperation', 'work_order_id', 'id');
    }

    public function materialIssues()
    {
        return $this->hasMany('App\Models\WorkOrderMaterialIssue', 'work_order_id', 'id');
    }

    public function materialReceipts()
    {
        return $this->hasMany('App\Models\WorkOrderMaterialReceipt', 'work_order_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $uomSql = Item::sqlItemsUnitAs('unit_of_measurement');
        $builder = $this->select("work_orders.*, items.item_code, items.item_name, {$uomSql}, bill_of_materials.bom_number, bill_of_materials.revision, warehouses.warehouse_name, users.username as created_by_name", false)
                        ->join('items', 'items.id = work_orders.item_id_fg')
                        ->join('bill_of_materials', 'bill_of_materials.id = work_orders.bom_id')
                        ->join('warehouses', 'warehouses.id = work_orders.warehouse_id')
                        ->join('users', 'users.id = work_orders.created_by', 'left');

        if ($id) {
            return $builder->where('work_orders.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getByPriority($priority)
    {
        return $this->where('priority', $priority)
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('due_date >=', $startDate)
                    ->where('due_date <=', $endDate)
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getOverdueOrders()
    {
        return $this->where('due_date <', date('Y-m-d'))
                    ->whereIn('status', ['released', 'in_progress'])
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getByCustomerOrder($customerOrderId)
    {
        return $this->where('customer_order_id', $customerOrderId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getBySalesOrder($salesOrderId)
    {
        return $this->where('sales_order_id', $salesOrderId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function generateWONumber()
    {
        $prefix = 'WO';
        $year = date('Y');
        $month = date('m');
        
        $lastWO = $this->select('wo_number')
                       ->like('wo_number', "{$prefix}{$year}{$month}")
                       ->orderBy('wo_number', 'DESC')
                       ->first();
        
        if ($lastWO) {
            $lastNumber = intval(substr($lastWO['wo_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $newNumber);
    }

    public function createWorkOrder($data)
    {
        $woData = [
            'wo_number' => isset($data['wo_number']) ? $data['wo_number'] : $this->generateWONumber(),
            'item_id_fg' => $data['item_id_fg'],
            'bom_id' => $data['bom_id'],
            'routing_id' => isset($data['routing_id']) ? $data['routing_id'] : null,
            'order_qty' => $data['order_qty'],
            'uom' => $data['uom'],
            'due_date' => $data['due_date'],
            'warehouse_id' => $data['warehouse_id'],
            'priority' => isset($data['priority']) ? $data['priority'] : 'normal',
            'planner_code' => isset($data['planner_code']) ? $data['planner_code'] : '',
            'customer_order_id' => isset($data['customer_order_id']) ? $data['customer_order_id'] : null,
            'sales_order_id' => isset($data['sales_order_id']) ? $data['sales_order_id'] : null,
            'mrp_order_id' => isset($data['mrp_order_id']) ? $data['mrp_order_id'] : null,
            'schedule_start' => isset($data['schedule_start']) ? $data['schedule_start'] : null,
            'schedule_end' => isset($data['schedule_end']) ? $data['schedule_end'] : null,
            'status' => isset($data['status']) ? $data['status'] : 'draft',
            'backflush_material' => isset($data['backflush_material']) ? $data['backflush_material'] : 0,
            'backflush_labor' => isset($data['backflush_labor']) ? $data['backflush_labor'] : 0,
            'backflush_overhead' => isset($data['backflush_overhead']) ? $data['backflush_overhead'] : 0,
            'qc_required' => isset($data['qc_required']) ? $data['qc_required'] : 0,
            'parent_wo_id' => isset($data['parent_wo_id']) ? $data['parent_wo_id'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'attachments' => isset($data['attachments']) ? $data['attachments'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($woData);
    }

    public function updateWorkOrder($id, $data)
    {
        $wo = $this->find($id);
        if (!$wo) {
            return false;
        }

        // If changing status to released, check component availability
        if (isset($data['status']) && $data['status'] == 'released' && $wo['status'] != 'released') {
            $availability = $this->checkComponentAvailability($id);
            $hasShortages = false;
            
            foreach ($availability as $component) {
                if (!$component['is_available']) {
                    $hasShortages = true;
                    break;
                }
            }
            
            if ($hasShortages) {
                return false; // Cannot release WO with component shortages
            }
        }

        return $this->update($id, $data);
    }

    public function releaseWorkOrder($id)
    {
        $wo = $this->find($id);
        if (!$wo || $wo['status'] != 'draft') {
            return false;
        }

        // Check component availability
        $availability = $this->checkComponentAvailability($id);
        $hasShortages = false;
        
        foreach ($availability as $component) {
            if (!$component['is_available']) {
                $hasShortages = true;
                break;
            }
        }
        
        if ($hasShortages) {
            return false;
        }

        // Reserve components
        $this->reserveComponents($id);

        $updateData = [
            'status' => 'released',
            'schedule_start' => isset($wo['schedule_start']) ? $wo['schedule_start'] : date('Y-m-d H:i:s'),
            'schedule_end' => isset($wo['schedule_end']) ? $wo['schedule_end'] : $this->calculateScheduleEnd($id)
        ];

        return $this->update($id, $updateData);
    }

    public function startWorkOrder($id)
    {
        $wo = $this->find($id);
        if (!$wo || $wo['status'] != 'released') {
            return false;
        }

        $updateData = [
            'status' => 'in_progress',
            'actual_start' => date('Y-m-d H:i:s')
        ];

        return $this->update($id, $updateData);
    }

    public function completeWorkOrder($id, $completionQty, $scrapQty = 0, $reworkQty = 0)
    {
        $wo = $this->find($id);
        if (!$wo || $wo['status'] != 'in_progress') {
            return false;
        }

        $totalQty = $completionQty + $scrapQty + $reworkQty;
        if ($totalQty > $wo['order_qty']) {
            return false; // Cannot complete more than ordered
        }

        $updateData = [
            'status' => 'completed',
            'actual_end' => date('Y-m-d H:i:s'),
            'completion_qty' => $completionQty,
            'scrap_qty' => $scrapQty,
            'rework_qty' => $reworkQty
        ];

        // Update completion quantities
        $this->update($id, $updateData);

        // Process material consumption and production output
        $this->processMaterialConsumption($id, $completionQty);
        $this->processProductionOutput($id, $completionQty, $scrapQty, $reworkQty);

        return true;
    }

    public function checkComponentAvailability($woId)
    {
        $wo = $this->find($woId);
        if (!$wo) {
            return [];
        }

        $bom = model('BillOfMaterials')->find($wo['bom_id']);
        if (!$bom) {
            return [];
        }

        $components = model('BOMComponent')->getByBOM($wo['bom_id']);
        $availability = [];

        foreach ($components as $component) {
            $availableQty = model('CurrentStock')->getStockBalance(
                $component['component_item_id'],
                $wo['warehouse_id']
            );

            $requiredQty = model('BOMComponent')->calculateRequiredQuantity(
                $component['qty'] * $wo['order_qty'],
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

    public function reserveComponents($woId)
    {
        $wo = $this->find($woId);
        if (!$wo) {
            return false;
        }

        $components = model('BOMComponent')->getByBOM($wo['bom_id']);
        
        foreach ($components as $component) {
            $requiredQty = model('BOMComponent')->calculateRequiredQuantity(
                $component['qty'] * $wo['order_qty'],
                $component['scrap_pct'],
                $component['yield_pct']
            );

            // Create material reservation
            model('WorkOrderMaterialReservation')->createReservation([
                'work_order_id' => $woId,
                'item_id' => $component['component_item_id'],
                'required_qty' => $requiredQty,
                'reserved_qty' => 0,
                'warehouse_id' => $wo['warehouse_id']
            ]);
        }

        return true;
    }

    public function processMaterialConsumption($woId, $completionQty)
    {
        $wo = $this->find($woId);
        if (!$wo) {
            return false;
        }

        $components = model('BOMComponent')->getByBOM($wo['bom_id']);
        
        foreach ($components as $component) {
            $consumedQty = model('BOMComponent')->calculateRequiredQuantity(
                $component['qty'] * $completionQty,
                $component['scrap_pct'],
                $component['yield_pct']
            );

            // Create material issue
            model('WorkOrderMaterialIssue')->createIssue([
                'work_order_id' => $woId,
                'item_id' => $component['component_item_id'],
                'quantity' => $consumedQty,
                'warehouse_id' => $wo['warehouse_id'],
                'issue_date' => date('Y-m-d H:i:s')
            ]);

            // Update stock
            model('StockMovement')->createMovement([
                'item_id' => $component['component_item_id'],
                'movement_type' => 'out',
                'quantity' => $consumedQty,
                'warehouse_id' => $wo['warehouse_id'],
                'reference_type' => 'work_order',
                'reference_id' => $woId,
                'reference_number' => $wo['wo_number']
            ]);
        }

        return true;
    }

    public function processProductionOutput($woId, $completionQty, $scrapQty, $reworkQty)
    {
        $wo = $this->find($woId);
        if (!$wo) {
            return false;
        }

        // Process good output
        if ($completionQty > 0) {
            model('StockMovement')->createMovement([
                'item_id' => $wo['item_id_fg'],
                'movement_type' => 'in',
                'quantity' => $completionQty,
                'warehouse_id' => $wo['warehouse_id'],
                'reference_type' => 'work_order',
                'reference_id' => $woId,
                'reference_number' => $wo['wo_number']
            ]);
        }

        // Process scrap
        if ($scrapQty > 0) {
            model('StockMovement')->createMovement([
                'item_id' => $wo['item_id_fg'],
                'movement_type' => 'in',
                'quantity' => $scrapQty,
                'warehouse_id' => $wo['warehouse_id'],
                'reference_type' => 'work_order',
                'reference_id' => $woId,
                'reference_number' => $wo['wo_number'],
                'notes' => 'Scrap from production'
            ]);
        }

        // Process rework
        if ($reworkQty > 0) {
            // Create rework work order
            $reworkWO = $this->createWorkOrder([
                'item_id_fg' => $wo['item_id_fg'],
                'bom_id' => $wo['bom_id'],
                'order_qty' => $reworkQty,
                'uom' => $wo['uom'],
                'due_date' => date('Y-m-d', strtotime('+1 day')),
                'warehouse_id' => $wo['warehouse_id'],
                'priority' => 'high',
                'status' => 'draft',
                'parent_wo_id' => $woId,
                'notes' => 'Rework from WO: ' . $wo['wo_number']
            ]);
        }

        return true;
    }

    public function calculateScheduleEnd($woId)
    {
        $wo = $this->find($woId);
        if (!$wo) {
            return null;
        }

        $bom = model('BillOfMaterials')->find($wo['bom_id']);
        if (!$bom) {
            return null;
        }

        $operations = model('BOMOperation')->getByBOM($wo['bom_id']);
        $totalTime = 0;

        foreach ($operations as $operation) {
            $totalTime += model('BOMOperation')->calculateOperationTime(
                $operation['id'],
                $wo['order_qty']
            );
        }

        $startDate = isset($wo['schedule_start']) ? $wo['schedule_start'] : date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime($startDate) + ($totalTime * 60));

        return $endDate;
    }

    public function getWorkOrderStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('status, priority, COUNT(*) as count, AVG(order_qty) as avg_qty, SUM(total_cost) as total_cost')
                        ->groupBy('status, priority');
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->findAll();
    }

    public function getWorkOrderAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as wo_count, AVG(order_qty) as avg_qty, SUM(total_cost) as total_cost')
                        ->groupBy('DATE(created_at)');
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getWorkOrderStatuses()
    {
        return [
            'draft' => 'Draft',
            'released' => 'Released',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled'
        ];
    }

    public function getWorkOrderPriorities()
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
            'critical' => 'Critical'
        ];
    }

    public function getWorkOrderProgress($woId)
    {
        $wo = $this->find($woId);
        if (!$wo) {
            return 0;
        }

        if ($wo['status'] == 'completed' || $wo['status'] == 'closed') {
            return 100;
        }

        if ($wo['status'] == 'draft' || $wo['status'] == 'released') {
            return 0;
        }

        $totalQty = $wo['order_qty'];
        $completedQty = isset($wo['completion_qty']) ? $wo['completion_qty'] : 0;

        return $totalQty > 0 ? ($completedQty / $totalQty) * 100 : 0;
    }

    public function getWorkOrderEfficiency($woId)
    {
        $wo = $this->find($woId);
        if (!$wo || !$wo['actual_start'] || !$wo['actual_end']) {
            return 0;
        }

        $actualTime = strtotime($wo['actual_end']) - strtotime($wo['actual_start']);
        $plannedTime = 0;

        if ($wo['schedule_start'] && $wo['schedule_end']) {
            $plannedTime = strtotime($wo['schedule_end']) - strtotime($wo['schedule_start']);
        }

        if ($plannedTime <= 0) {
            return 0;
        }

        $efficiency = ($plannedTime / $actualTime) * 100;
        return min(100, max(0, $efficiency));
    }
}
