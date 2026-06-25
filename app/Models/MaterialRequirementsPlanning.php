<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequirementsPlanning extends Model
{
    protected $table = 'material_requirements_planning';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'mrp_run_id',
        'item_id',
        'period_start',
        'period_end',
        'gross_requirement',
        'scheduled_receipts',
        'projected_on_hand',
        'net_requirement',
        'planned_order_receipt',
        'planned_order_release',
        'safety_stock',
        'reorder_point',
        'lead_time',
        'lot_size',
        'lot_sizing_rule',
        'order_policy',
        'supplier_id',
        'warehouse_id',
        'priority',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'mrp_run_id' => 'required|integer',
        'item_id' => 'required|integer',
        'period_start' => 'required|valid_date',
        'period_end' => 'required|valid_date',
        'gross_requirement' => 'required|numeric|greater_than_equal_to[0]',
        'scheduled_receipts' => 'required|numeric|greater_than_equal_to[0]',
        'projected_on_hand' => 'required|numeric|greater_than_equal_to[0]',
        'net_requirement' => 'required|numeric|greater_than_equal_to[0]',
        'lead_time' => 'required|integer|greater_than_equal_to[0]',
        'lot_sizing_rule' => 'required|in_list[lfl,eoq,min_max,period_order,lot_for_lot]',
        'order_policy' => 'required|in_list[make_to_order,make_to_stock,assemble_to_order,engineer_to_order]'
    ];

    protected $validationMessages = [
        'mrp_run_id' => [
            'required' => 'MRP run ID is required',
            'integer' => 'Invalid MRP run ID'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Invalid item ID'
        ],
        'gross_requirement' => [
            'required' => 'Gross requirement is required',
            'numeric' => 'Gross requirement must be a number',
            'greater_than_equal_to' => 'Gross requirement must be 0 or greater'
        ],
        'lead_time' => [
            'required' => 'Lead time is required',
            'integer' => 'Lead time must be a number',
            'greater_than_equal_to' => 'Lead time must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function mrpRun()
    {
        return $this->belongsTo('App\Models\MRPRun', 'mrp_run_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('material_requirements_planning.*, items.item_code, items.item_name, items.unit_of_measurement, suppliers.supplier_name, warehouses.warehouse_name, mrp_runs.run_number, mrp_runs.run_date')
                        ->join('items', 'items.id = material_requirements_planning.item_id')
                        ->join('suppliers', 'suppliers.id = material_requirements_planning.supplier_id', 'left')
                        ->join('warehouses', 'warehouses.id = material_requirements_planning.warehouse_id', 'left')
                        ->join('mrp_runs', 'mrp_runs.id = material_requirements_planning.mrp_run_id');

        if ($id) {
            return $builder->where('material_requirements_planning.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByMRPRun($mrpRunId)
    {
        return $this->select('material_requirements_planning.*, items.item_code, items.item_name, items.unit_of_measurement')
                    ->join('items', 'items.id = material_requirements_planning.item_id')
                    ->where('mrp_run_id', $mrpRunId)
                    ->orderBy('items.item_code', 'ASC')
                    ->findAll();
    }

    public function getByItem($itemId, $mrpRunId = null)
    {
        $builder = $this->select('material_requirements_planning.*, mrp_runs.run_number, mrp_runs.run_date')
                        ->join('mrp_runs', 'mrp_runs.id = material_requirements_planning.mrp_run_id')
                        ->where('item_id', $itemId);
        
        if ($mrpRunId) {
            $builder->where('mrp_run_id', $mrpRunId);
        }

        return $builder->orderBy('mrp_runs.run_date', 'DESC')->findAll();
    }

    public function getByPeriod($startDate, $endDate, $mrpRunId = null)
    {
        $builder = $this->where('period_start >=', $startDate)
                        ->where('period_end <=', $endDate);
        
        if ($mrpRunId) {
            $builder->where('mrp_run_id', $mrpRunId);
        }

        return $builder->orderBy('period_start', 'ASC')->findAll();
    }

    public function getNetRequirements($mrpRunId)
    {
        return $this->select('material_requirements_planning.*, items.item_code, items.item_name, items.unit_of_measurement')
                    ->join('items', 'items.id = material_requirements_planning.item_id')
                    ->where('mrp_run_id', $mrpRunId)
                    ->where('net_requirement >', 0)
                    ->orderBy('net_requirement', 'DESC')
                    ->findAll();
    }

    public function getPlannedOrders($mrpRunId)
    {
        return $this->select('material_requirements_planning.*, items.item_code, items.item_name, items.unit_of_measurement')
                    ->join('items', 'items.id = material_requirements_planning.item_id')
                    ->where('mrp_run_id', $mrpRunId)
                    ->where('planned_order_receipt >', 0)
                    ->orderBy('planned_order_release', 'ASC')
                    ->findAll();
    }

    public function createMRPRecord($data)
    {
        $mrpData = [
            'mrp_run_id' => $data['mrp_run_id'],
            'item_id' => $data['item_id'],
            'period_start' => $data['period_start'],
            'period_end' => $data['period_end'],
            'gross_requirement' => isset($data['gross_requirement']) ? $data['gross_requirement'] : 0,
            'scheduled_receipts' => isset($data['scheduled_receipts']) ? $data['scheduled_receipts'] : 0,
            'projected_on_hand' => isset($data['projected_on_hand']) ? $data['projected_on_hand'] : 0,
            'net_requirement' => isset($data['net_requirement']) ? $data['net_requirement'] : 0,
            'planned_order_receipt' => isset($data['planned_order_receipt']) ? $data['planned_order_receipt'] : 0,
            'planned_order_release' => isset($data['planned_order_release']) ? $data['planned_order_release'] : null,
            'safety_stock' => isset($data['safety_stock']) ? $data['safety_stock'] : 0,
            'reorder_point' => isset($data['reorder_point']) ? $data['reorder_point'] : 0,
            'lead_time' => $data['lead_time'],
            'lot_size' => isset($data['lot_size']) ? $data['lot_size'] : 0,
            'lot_sizing_rule' => $data['lot_sizing_rule'],
            'order_policy' => $data['order_policy'],
            'supplier_id' => isset($data['supplier_id']) ? $data['supplier_id'] : null,
            'warehouse_id' => isset($data['warehouse_id']) ? $data['warehouse_id'] : null,
            'priority' => isset($data['priority']) ? $data['priority'] : 'normal',
            'status' => isset($data['status']) ? $data['status'] : 'planned',
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : session()->get('user_id')
        ];

        return $this->insert($mrpData);
    }

    public function updateMRPRecord($id, $data)
    {
        $mrp = $this->find($id);
        if (!$mrp) {
            return false;
        }

        $updateData = [
            'gross_requirement' => isset($data['gross_requirement']) ? $data['gross_requirement'] : $mrp['gross_requirement'],
            'scheduled_receipts' => isset($data['scheduled_receipts']) ? $data['scheduled_receipts'] : $mrp['scheduled_receipts'],
            'projected_on_hand' => isset($data['projected_on_hand']) ? $data['projected_on_hand'] : $mrp['projected_on_hand'],
            'net_requirement' => isset($data['net_requirement']) ? $data['net_requirement'] : $mrp['net_requirement'],
            'planned_order_receipt' => isset($data['planned_order_receipt']) ? $data['planned_order_receipt'] : $mrp['planned_order_receipt'],
            'planned_order_release' => isset($data['planned_order_release']) ? $data['planned_order_release'] : $mrp['planned_order_release'],
            'status' => isset($data['status']) ? $data['status'] : $mrp['status'],
            'notes' => isset($data['notes']) ? $data['notes'] : $mrp['notes']
        ];

        return $this->update($id, $updateData);
    }

    public function calculateNetRequirements($itemId, $startDate, $endDate, $mrpRunId)
    {
        $item = model('Item')->find($itemId);
        if (!$item) {
            return false;
        }

        $periods = $this->generatePeriods($startDate, $endDate);
        $mrpRecords = [];

        foreach ($periods as $period) {
            $grossRequirement = $this->calculateGrossRequirement($itemId, $period['start'], $period['end']);
            $scheduledReceipts = $this->getScheduledReceipts($itemId, $period['start'], $period['end']);
            $projectedOnHand = $this->calculateProjectedOnHand($itemId, $period['start'], $mrpRunId);
            $netRequirement = $this->calculateNetRequirement($grossRequirement, $scheduledReceipts, $projectedOnHand, $item['safety_stock']);

            $mrpRecord = [
                'mrp_run_id' => $mrpRunId,
                'item_id' => $itemId,
                'period_start' => $period['start'],
                'period_end' => $period['end'],
                'gross_requirement' => $grossRequirement,
                'scheduled_receipts' => $scheduledReceipts,
                'projected_on_hand' => $projectedOnHand,
                'net_requirement' => $netRequirement,
                'safety_stock' => $item['safety_stock'],
                'reorder_point' => $item['reorder_point'],
                'lead_time' => $item['lead_time'],
                'lot_size' => $item['lot_size'],
                'lot_sizing_rule' => isset($item['lot_sizing_rule']) ? $item['lot_sizing_rule'] : 'lot_for_lot',
                'order_policy' => isset($item['order_policy']) ? $item['order_policy'] : 'make_to_stock',
                'warehouse_id' => isset($item['preferred_warehouse_id']) ? $item['preferred_warehouse_id'] : null,
                'priority' => $this->determinePriority($netRequirement, $item['safety_stock'])
            ];

            $mrpRecords[] = $mrpRecord;
        }

        return $mrpRecords;
    }

    private function generatePeriods($startDate, $endDate)
    {
        $periods = [];
        $currentDate = new \DateTime($startDate);
        $endDateTime = new \DateTime($endDate);

        while ($currentDate <= $endDateTime) {
            $periodStart = $currentDate->format('Y-m-d');
            $currentDate->add(new \DateInterval('P1D'));
            $periodEnd = $currentDate->format('Y-m-d');

            $periods[] = [
                'start' => $periodStart,
                'end' => $periodEnd
            ];
        }

        return $periods;
    }

    private function calculateGrossRequirement($itemId, $startDate, $endDate)
    {
        $grossRequirement = 0;

        // Sales orders demand
        $salesOrders = model('SalesOrder')->getByDateRange($startDate, $endDate);
        foreach ($salesOrders as $so) {
            $soItems = model('SalesOrderItem')->getBySalesOrder($so['id']);
            foreach ($soItems as $soItem) {
                if ($soItem['item_id'] == $itemId) {
                    $grossRequirement += $soItem['quantity'];
                }
            }
        }

        // Customer orders demand
        $customerOrders = model('CustomerOrder')->getByDateRange($startDate, $endDate);
        foreach ($customerOrders as $co) {
            $coItems = model('CustomerOrderItem')->getByCustomerOrder($co['id']);
            foreach ($coItems as $coItem) {
                if ($coItem['item_id'] == $itemId) {
                    $grossRequirement += $coItem['quantity'];
                }
            }
        }

        // Dependent demand from work orders
        $workOrders = model('WorkOrder')->getByDateRange($startDate, $endDate);
        foreach ($workOrders as $wo) {
            $bom = model('BillOfMaterials')->find($wo['bom_id']);
            if ($bom) {
                $components = model('BOMComponent')->getByBOM($wo['bom_id']);
                foreach ($components as $component) {
                    if ($component['component_item_id'] == $itemId) {
                        $requiredQty = model('BOMComponent')->calculateRequiredQuantity(
                            $component['qty'] * $wo['order_qty'],
                            $component['scrap_pct'],
                            $component['yield_pct']
                        );
                        $grossRequirement += $requiredQty;
                    }
                }
            }
        }

        return $grossRequirement;
    }

    private function getScheduledReceipts($itemId, $startDate, $endDate)
    {
        $scheduledReceipts = 0;

        // Purchase orders
        $purchaseOrders = model('PurchaseOrder')->getByDateRange($startDate, $endDate);
        foreach ($purchaseOrders as $po) {
            $poItems = model('PurchaseOrderItem')->getByPurchaseOrder($po['id']);
            foreach ($poItems as $poItem) {
                if ($poItem['item_id'] == $itemId) {
                    $scheduledReceipts += $poItem['quantity'];
                }
            }
        }

        // Work orders
        $workOrders = model('WorkOrder')->getByDateRange($startDate, $endDate);
        foreach ($workOrders as $wo) {
            if ($wo['item_id_fg'] == $itemId) {
                $scheduledReceipts += $wo['order_qty'];
            }
        }

        return $scheduledReceipts;
    }

    private function calculateProjectedOnHand($itemId, $asOfDate, $mrpRunId)
    {
        // Get current on-hand stock
        $currentStock = model('CurrentStock')->getTotalStockBalance($itemId);

        // Get previous MRP records for this run
        $previousMRP = $this->where('mrp_run_id', $mrpRunId)
                            ->where('item_id', $itemId)
                            ->where('period_end <', $asOfDate)
                            ->orderBy('period_end', 'DESC')
                            ->first();

        if ($previousMRP) {
            return $previousMRP['projected_on_hand'];
        }

        return $currentStock;
    }

    private function calculateNetRequirement($grossRequirement, $scheduledReceipts, $projectedOnHand, $safetyStock)
    {
        $availableStock = $projectedOnHand + $scheduledReceipts;
        $netRequirement = $grossRequirement - $availableStock + $safetyStock;

        return max(0, $netRequirement);
    }

    private function determinePriority($netRequirement, $safetyStock)
    {
        if ($netRequirement > $safetyStock * 2) {
            return 'critical';
        } elseif ($netRequirement > $safetyStock) {
            return 'high';
        } elseif ($netRequirement > 0) {
            return 'normal';
        } else {
            return 'low';
        }
    }

    public function generatePlannedOrders($mrpRunId)
    {
        $mrpRecords = $this->getNetRequirements($mrpRunId);
        $plannedOrders = [];

        foreach ($mrpRecords as $mrp) {
            if ($mrp['net_requirement'] > 0) {
                $lotSize = $this->calculateLotSize($mrp['net_requirement'], $mrp['lot_size'], $mrp['lot_sizing_rule']);
                $plannedOrderReceipt = $lotSize;
                $plannedOrderRelease = $this->calculatePlannedOrderRelease($mrp['period_start'], $mrp['lead_time']);

                $updateData = [
                    'planned_order_receipt' => $plannedOrderReceipt,
                    'planned_order_release' => $plannedOrderRelease,
                    'status' => 'planned'
                ];

                $this->update($mrp['id'], $updateData);

                $plannedOrders[] = [
                    'item_id' => $mrp['item_id'],
                    'item_code' => $mrp['item_code'],
                    'item_name' => $mrp['item_name'],
                    'quantity' => $plannedOrderReceipt,
                    'release_date' => $plannedOrderRelease,
                    'receipt_date' => $mrp['period_start'],
                    'order_type' => $mrp['order_policy'] == 'make_to_stock' ? 'work_order' : 'purchase_order'
                ];
            }
        }

        return $plannedOrders;
    }

    private function calculateLotSize($netRequirement, $lotSize, $lotSizingRule)
    {
        switch ($lotSizingRule) {
            case 'lot_for_lot':
                return $netRequirement;
            case 'fixed_lot':
                return $lotSize > 0 ? ceil($netRequirement / $lotSize) * $lotSize : $netRequirement;
            case 'economic_order_quantity':
                return $this->calculateEOQ($netRequirement);
            case 'min_max':
                return max($lotSize, $netRequirement);
            case 'period_order':
                return $this->calculatePeriodOrderQuantity($netRequirement);
            default:
                return $netRequirement;
        }
    }

    private function calculateEOQ($demand)
    {
        // Simplified EOQ calculation - can be enhanced with setup cost and holding cost
        $setupCost = 100; // Default setup cost
        $holdingCost = 0.2; // Default holding cost (20% of item cost)
        
        if ($holdingCost <= 0) {
            return $demand;
        }

        $eoq = sqrt((2 * $demand * $setupCost) / $holdingCost);
        return ceil($eoq);
    }

    private function calculatePeriodOrderQuantity($demand)
    {
        // Simplified period order quantity - can be enhanced with demand variability
        return ceil($demand * 1.1); // Add 10% buffer
    }

    private function calculatePlannedOrderRelease($receiptDate, $leadTime)
    {
        $releaseDate = new \DateTime($receiptDate);
        $releaseDate->sub(new \DateInterval("P{$leadTime}D"));
        return $releaseDate->format('Y-m-d');
    }

    public function getMRPStats($mrpRunId)
    {
        $stats = [
            'total_items' => 0,
            'items_with_net_requirements' => 0,
            'total_net_requirements' => 0,
            'total_planned_orders' => 0,
            'critical_priority' => 0,
            'high_priority' => 0,
            'normal_priority' => 0,
            'low_priority' => 0
        ];

        $mrpRecords = $this->getByMRPRun($mrpRunId);

        foreach ($mrpRecords as $mrp) {
            $stats['total_items']++;
            $stats['total_net_requirements'] += $mrp['net_requirement'];

            if ($mrp['net_requirement'] > 0) {
                $stats['items_with_net_requirements']++;
                $stats['total_planned_orders'] += $mrp['planned_order_receipt'];
            }

            switch ($mrp['priority']) {
                case 'critical':
                    $stats['critical_priority']++;
                    break;
                case 'high':
                    $stats['high_priority']++;
                    break;
                case 'normal':
                    $stats['normal_priority']++;
                    break;
                case 'low':
                    $stats['low_priority']++;
                    break;
            }
        }

        return $stats;
    }

    public function getMRPAnalytics($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(created_at) as date, COUNT(*) as mrp_count, SUM(net_requirement) as total_net_requirements, SUM(planned_order_receipt) as total_planned_orders')
                        ->groupBy('DATE(created_at)');
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    public function getLotSizingRules()
    {
        return [
            'lot_for_lot' => 'Lot for Lot',
            'fixed_lot' => 'Fixed Lot Size',
            'economic_order_quantity' => 'Economic Order Quantity',
            'min_max' => 'Min/Max',
            'period_order' => 'Period Order Quantity'
        ];
    }

    public function getOrderPolicies()
    {
        return [
            'make_to_order' => 'Make to Order',
            'make_to_stock' => 'Make to Stock',
            'assemble_to_order' => 'Assemble to Order',
            'engineer_to_order' => 'Engineer to Order'
        ];
    }

    public function getMRPPriorities()
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'critical' => 'Critical'
        ];
    }

    public function exportMRPReport($mrpRunId, $format = 'csv')
    {
        $mrpRecords = $this->getByMRPRun($mrpRunId);
        
        if ($format == 'csv') {
            return $this->exportToCSV($mrpRecords);
        } elseif ($format == 'excel') {
            return $this->exportToExcel($mrpRecords);
        }
        
        return $mrpRecords;
    }

    private function exportToCSV($mrpRecords)
    {
        $csv = "Item Code,Item Name,Period Start,Period End,Gross Requirement,Scheduled Receipts,Projected On Hand,Net Requirement,Planned Order Receipt,Planned Order Release,Priority,Status\n";
        
        foreach ($mrpRecords as $record) {
            $csv .= "{$record['item_code']},{$record['item_name']},{$record['period_start']},{$record['period_end']},{$record['gross_requirement']},{$record['scheduled_receipts']},{$record['projected_on_hand']},{$record['net_requirement']},{$record['planned_order_receipt']},{$record['planned_order_release']},{$record['priority']},{$record['status']}\n";
        }
        
        return $csv;
    }

    private function exportToExcel($mrpRecords)
    {
        // Implementation for Excel export
        // This would typically use a library like PhpSpreadsheet
        return $mrpRecords;
    }
}
