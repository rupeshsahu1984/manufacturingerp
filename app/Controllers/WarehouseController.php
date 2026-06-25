<?php

namespace App\Controllers;

use App\Models\Warehouse;
use App\Models\Employee;
use Exception;

class WarehouseController extends BaseController
{
    protected $warehouseModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->warehouseModel = new Warehouse();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Warehouse Master - PRODX',
            'warehouses' => $this->warehouseModel->getWarehouses($filters),
            'stats' => $this->warehouseModel->getWarehouseStats(),
            'filters' => $filters
        ];

        return view('warehouse/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Warehouse - PRODX',
            'managers' => $this->employeeModel->getActiveEmployees()
        ];

        return view('warehouse/create', $data);
    }

    public function store()
    {
        $rules = [
            'warehouse_code' => 'required|min_length[3]|max_length[20]|is_unique[warehouses.warehouse_code]',
            'warehouse_name' => 'required|min_length[3]|max_length[100]',
            'location' => 'required|max_length[500]',
            'manager_id' => 'permit_empty|integer',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'warehouse_code' => $this->request->getPost('warehouse_code'),
            'warehouse_name' => $this->request->getPost('warehouse_name'),
            'location' => $this->request->getPost('location'),
            'manager_id' => $this->request->getPost('manager_id') ?: null,
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        if ($this->warehouseModel->insert($data)) {
            return redirect()->to('warehouse')->with('success', 'Warehouse created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create warehouse.');
        }
    }

    public function show($id)
    {
        $warehouse = $this->warehouseModel->getWarehouseWithDetails($id);
        
        if (!$warehouse) {
            return redirect()->to('warehouse')->with('error', 'Warehouse not found.');
        }

        $data = [
            'title' => 'Warehouse Details - PRODX',
            'warehouse' => $warehouse
        ];

        return view('warehouse/show', $data);
    }

    public function edit($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse) {
            return redirect()->to('warehouse')->with('error', 'Warehouse not found.');
        }

        $data = [
            'title' => 'Edit Warehouse - PRODX',
            'warehouse' => $warehouse,
            'managers' => $this->employeeModel->getActiveEmployees()
        ];

        return view('warehouse/edit', $data);
    }

    public function update($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse) {
            return redirect()->to('warehouse')->with('error', 'Warehouse not found.');
        }

        $rules = [
            'warehouse_code' => "required|min_length[3]|max_length[20]|is_unique[warehouses.warehouse_code,id,$id]",
            'warehouse_name' => 'required|min_length[3]|max_length[100]',
            'location' => 'required|max_length[500]',
            'manager_id' => 'permit_empty|integer',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'warehouse_code' => $this->request->getPost('warehouse_code'),
            'warehouse_name' => $this->request->getPost('warehouse_name'),
            'location' => $this->request->getPost('location'),
            'manager_id' => $this->request->getPost('manager_id') ?: null,
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->warehouseModel->update($id, $data)) {
            return redirect()->to('warehouse')->with('success', 'Warehouse updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update warehouse.');
        }
    }

    public function delete($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse) {
            return redirect()->to('warehouse')->with('error', 'Warehouse not found.');
        }

        // Check if warehouse has stock
        if ($this->warehouseModel->hasStock($id)) {
            return redirect()->to('warehouse')->with('error', 'Cannot delete warehouse with stock.');
        }

        if ($this->warehouseModel->delete($id)) {
            return redirect()->to('warehouse')->with('success', 'Warehouse deleted successfully.');
        } else {
            return redirect()->to('warehouse')->with('error', 'Failed to delete warehouse.');
        }
    }

    public function toggleStatus($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse) {
            return $this->response->setJSON(['success' => false, 'message' => 'Warehouse not found.']);
        }

        $newStatus = $warehouse['status'] === 'active' ? 'inactive' : 'active';
        
        if ($this->warehouseModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function export()
    {
        $warehouses = $this->warehouseModel->getWarehouses([]);
        
        $filename = 'warehouses_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Warehouse Code', 'Warehouse Name', 'Location', 'Manager', 'Status', 'Created At'
        ]);
        
        foreach ($warehouses as $warehouse) {
            fputcsv($output, [
                $warehouse['warehouse_code'],
                $warehouse['warehouse_name'],
                $warehouse['location'],
                isset($warehouse['manager_name']) ? $warehouse['manager_name'] : '',
                $warehouse['status'],
                $warehouse['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getWarehouses()
    {
        $warehouses = $this->warehouseModel->getActiveWarehouses();
        
        return $this->response->setJSON(['success' => true, 'warehouses' => $warehouses]);
    }

    public function searchWarehouses()
    {
        $search = $this->request->getGet('search');
        
        if (!$search) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search term required.']);
        }

        $warehouses = $this->warehouseModel->searchWarehouses($search);
        
        return $this->response->setJSON(['success' => true, 'warehouses' => $warehouses]);
    }
}
