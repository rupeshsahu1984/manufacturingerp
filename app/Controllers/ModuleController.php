<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ModuleController extends BaseController
{
    protected $moduleName;
    protected $moduleTitle;
    protected $moduleIcon;
    protected $sampleData;

    public function __construct()
    {
        // Get module name from the current request
        $request = service('request');
        $uri = $request->getUri();
        $segments = $uri->getSegments();
        $this->moduleName = isset($segments[0]) ? $segments[0] : 'module';
        
        $this->moduleTitle = $this->getModuleTitle();
        $this->moduleIcon = $this->getModuleIcon();
        $this->sampleData = $this->getSampleData();
    }

    protected function getModuleTitle()
    {
        $titles = [
            'purchase-order' => 'Purchase Order',
            'gate-entry' => 'Gate Entry',
            'supplier-master' => 'Supplier Master',
            'bom' => 'BOM Management',
            'work-orders' => 'Work Orders',
            'production-tracking' => 'Production Tracking',
            'quality-control' => 'Quality Control',
            'sales-orders' => 'Sales Orders',
            'finance' => 'Finance',
            'hrm' => 'HR Management',
            'reception' => 'Reception'
        ];
        
        return isset($titles[$this->moduleName]) ? $titles[$this->moduleName] : ucfirst(str_replace('-', ' ', $this->moduleName));
    }

    protected function getModuleIcon()
    {
        $icons = [
            'purchase-order' => 'fas fa-shopping-cart',
            'gate-entry' => 'fas fa-truck',
            'supplier-master' => 'fas fa-users',
            'bom' => 'fas fa-list-alt',
            'work-orders' => 'fas fa-tasks',
            'production-tracking' => 'fas fa-chart-line',
            'quality-control' => 'fas fa-check-circle',
            'sales-orders' => 'fas fa-chart-bar',
            'finance' => 'fas fa-money-bill-wave',
            'hrm' => 'fas fa-users',
            'reception' => 'fas fa-user-tie'
        ];
        
        return isset($icons[$this->moduleName]) ? $icons[$this->moduleName] : 'fas fa-cog';
    }

    protected function getSampleData()
    {
        $data = [
            'purchase-order' => [
                ['id' => 1, 'po_number' => 'PO-2025-001', 'supplier' => 'ABC Suppliers', 'total_amount' => 250000, 'status' => 'Active', 'created_date' => '2025-01-15'],
                ['id' => 2, 'po_number' => 'PO-2025-002', 'supplier' => 'XYZ Corporation', 'total_amount' => 180000, 'status' => 'Delivered', 'created_date' => '2025-01-14'],
                ['id' => 3, 'po_number' => 'PO-2025-003', 'supplier' => 'DEF Industries', 'total_amount' => 320000, 'status' => 'Pending', 'created_date' => '2025-01-13'],
            ],
            'gate-entry' => [
                ['id' => 1, 'vehicle_number' => 'MH-12-AB-1234', 'driver_name' => 'John Doe', 'purpose' => 'Material Delivery', 'status' => 'In', 'entry_time' => '2025-01-15 09:30'],
                ['id' => 2, 'vehicle_number' => 'MH-12-CD-5678', 'driver_name' => 'Jane Smith', 'purpose' => 'Finished Goods', 'status' => 'Out', 'entry_time' => '2025-01-15 14:20'],
                ['id' => 3, 'vehicle_number' => 'MH-12-EF-9012', 'driver_name' => 'Mike Johnson', 'purpose' => 'Raw Materials', 'status' => 'In', 'entry_time' => '2025-01-15 11:15'],
            ],
            'supplier-master' => [
                ['id' => 1, 'name' => 'ABC Suppliers', 'contact_person' => 'Raj Kumar', 'phone' => '+91-9876543210', 'email' => 'raj@abcsuppliers.com', 'rating' => 4.5, 'status' => 'Active'],
                ['id' => 2, 'name' => 'XYZ Corporation', 'contact_person' => 'Priya Singh', 'phone' => '+91-9876543211', 'email' => 'priya@xyzcorp.com', 'rating' => 4.2, 'status' => 'Active'],
                ['id' => 3, 'name' => 'DEF Industries', 'contact_person' => 'Amit Patel', 'phone' => '+91-9876543212', 'email' => 'amit@defindustries.com', 'rating' => 3.8, 'status' => 'Inactive'],
            ],
            'bom' => [
                ['id' => 1, 'product_name' => 'Product A', 'version' => '1.0', 'components' => 15, 'total_cost' => 12500, 'status' => 'Active', 'created_date' => '2025-01-15'],
                ['id' => 2, 'product_name' => 'Product B', 'version' => '2.1', 'components' => 22, 'total_cost' => 18750, 'status' => 'Active', 'created_date' => '2025-01-14'],
                ['id' => 3, 'product_name' => 'Product C', 'version' => '1.5', 'components' => 18, 'total_cost' => 15600, 'status' => 'Draft', 'created_date' => '2025-01-13'],
            ],
            'work-orders' => [
                ['id' => 1, 'wo_number' => 'WO-2025-001', 'product' => 'Product A', 'quantity' => 100, 'status' => 'In Progress', 'completion' => 75, 'due_date' => '2025-01-20'],
                ['id' => 2, 'wo_number' => 'WO-2025-002', 'product' => 'Product B', 'quantity' => 50, 'status' => 'Completed', 'completion' => 100, 'due_date' => '2025-01-18'],
                ['id' => 3, 'wo_number' => 'WO-2025-003', 'product' => 'Product C', 'quantity' => 75, 'status' => 'Pending', 'completion' => 0, 'due_date' => '2025-01-25'],
            ],
            'production-tracking' => [
                ['id' => 1, 'line' => 'Line A', 'product' => 'Product A', 'target' => 100, 'produced' => 85, 'efficiency' => 85, 'status' => 'Running'],
                ['id' => 2, 'line' => 'Line B', 'product' => 'Product B', 'target' => 80, 'produced' => 78, 'efficiency' => 97.5, 'status' => 'Running'],
                ['id' => 3, 'line' => 'Line C', 'product' => 'Product C', 'target' => 120, 'produced' => 95, 'efficiency' => 79.2, 'status' => 'Maintenance'],
            ],
            'quality-control' => [
                ['id' => 1, 'batch_number' => 'BATCH-001', 'product' => 'Product A', 'inspected' => 100, 'passed' => 98, 'failed' => 2, 'pass_rate' => 98],
                ['id' => 2, 'batch_number' => 'BATCH-002', 'product' => 'Product B', 'inspected' => 75, 'passed' => 73, 'failed' => 2, 'pass_rate' => 97.3],
                ['id' => 3, 'batch_number' => 'BATCH-003', 'product' => 'Product C', 'inspected' => 50, 'passed' => 48, 'failed' => 2, 'pass_rate' => 96],
            ],
            'sales-orders' => [
                ['id' => 1, 'so_number' => 'SO-2025-001', 'customer' => 'Customer A', 'total_amount' => 450000, 'status' => 'Pending', 'delivery_date' => '2025-01-25'],
                ['id' => 2, 'so_number' => 'SO-2025-002', 'customer' => 'Customer B', 'total_amount' => 320000, 'status' => 'Shipped', 'delivery_date' => '2025-01-22'],
                ['id' => 3, 'so_number' => 'SO-2025-003', 'customer' => 'Customer C', 'total_amount' => 280000, 'status' => 'Delivered', 'delivery_date' => '2025-01-20'],
            ],
            'finance' => [
                ['id' => 1, 'transaction_id' => 'TXN-001', 'type' => 'Revenue', 'amount' => 1250000, 'category' => 'Sales', 'date' => '2025-01-15'],
                ['id' => 2, 'transaction_id' => 'TXN-002', 'type' => 'Expense', 'amount' => 850000, 'category' => 'Operations', 'date' => '2025-01-14'],
                ['id' => 3, 'transaction_id' => 'TXN-003', 'type' => 'Revenue', 'amount' => 980000, 'category' => 'Sales', 'date' => '2025-01-13'],
            ],
            'hrm' => [
                ['id' => 1, 'employee_id' => 'EMP-001', 'name' => 'John Doe', 'department' => 'Production', 'position' => 'Operator', 'status' => 'Active', 'join_date' => '2023-01-15'],
                ['id' => 2, 'employee_id' => 'EMP-002', 'name' => 'Jane Smith', 'department' => 'Quality', 'position' => 'Inspector', 'status' => 'Active', 'join_date' => '2023-03-20'],
                ['id' => 3, 'employee_id' => 'EMP-003', 'name' => 'Mike Johnson', 'department' => 'Maintenance', 'position' => 'Technician', 'status' => 'Active', 'join_date' => '2023-06-10'],
            ],
            'reception' => [
                ['id' => 1, 'visitor_name' => 'Raj Kumar', 'company' => 'ABC Corp', 'purpose' => 'Business Meeting', 'contact_person' => 'John Doe', 'status' => 'In', 'entry_time' => '2025-01-15 10:30'],
                ['id' => 2, 'visitor_name' => 'Priya Singh', 'company' => 'XYZ Ltd', 'purpose' => 'Site Visit', 'contact_person' => 'Jane Smith', 'status' => 'Out', 'entry_time' => '2025-01-15 14:15'],
                ['id' => 3, 'visitor_name' => 'Amit Patel', 'company' => 'DEF Industries', 'purpose' => 'Delivery', 'contact_person' => 'Mike Johnson', 'status' => 'In', 'entry_time' => '2025-01-15 11:45'],
            ]
        ];
        
        return isset($data[$this->moduleName]) ? $data[$this->moduleName] : [];
    }

    public function index()
    {
        $data = [
            'title' => $this->moduleTitle,
            'moduleName' => $this->moduleName,
            'moduleIcon' => $this->moduleIcon,
            'items' => $this->sampleData
        ];
        
        return view('modules/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create ' . $this->moduleTitle,
            'moduleName' => $this->moduleName,
            'moduleIcon' => $this->moduleIcon
        ];
        
        return view('modules/create', $data);
    }

    public function store()
    {
        // Handle form submission
        return redirect()->to($this->moduleName)->with('success', $this->moduleTitle . ' created successfully');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit ' . $this->moduleTitle,
            'moduleName' => $this->moduleName,
            'moduleIcon' => $this->moduleIcon,
            'item' => $this->sampleData[0] ?? []
        ];
        
        return view('modules/edit', $data);
    }

    public function update($id)
    {
        // Handle form submission
        return redirect()->to($this->moduleName)->with('success', $this->moduleTitle . ' updated successfully');
    }

    public function delete($id)
    {
        // Handle deletion
        return redirect()->to($this->moduleName)->with('success', $this->moduleTitle . ' deleted successfully');
    }
} 