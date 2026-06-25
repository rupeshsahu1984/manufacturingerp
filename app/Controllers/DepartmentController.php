<?php

namespace App\Controllers;

use App\Models\Department;
use Exception;

class DepartmentController extends BaseController
{
    protected $departmentModel;

    public function __construct()
    {
        $this->departmentModel = new Department();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Department Master - PRODX',
            'departments' => $this->departmentModel->getDepartments($filters),
            'stats' => $this->departmentModel->getDepartmentStats(),
            'filters' => $filters
        ];

        return view('department/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Department - PRODX'
        ];

        return view('department/create', $data);
    }

    public function store()
    {
        $rules = [
            'department_name' => 'required|min_length[3]|max_length[100]|is_unique[departments.department_name]',
            'description' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'department_name' => $this->request->getPost('department_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        if ($this->departmentModel->insert($data)) {
            return redirect()->to('department')->with('success', 'Department created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create department.');
        }
    }

    public function show($id)
    {
        $department = $this->departmentModel->getDepartmentWithModules($id);
        
        if (!$department) {
            return redirect()->to('department')->with('error', 'Department not found.');
        }

        $data = [
            'title' => 'Department Details - PRODX',
            'department' => $department
        ];

        return view('department/show', $data);
    }

    public function edit($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return redirect()->to('department')->with('error', 'Department not found.');
        }

        $data = [
            'title' => 'Edit Department - PRODX',
            'department' => $department
        ];

        return view('department/edit', $data);
    }

    public function update($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return redirect()->to('department')->with('error', 'Department not found.');
        }

        $rules = [
            'department_name' => "required|min_length[3]|max_length[100]|is_unique[departments.department_name,id,$id]",
            'description' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'department_name' => $this->request->getPost('department_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->departmentModel->update($id, $data)) {
            return redirect()->to('department')->with('success', 'Department updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update department.');
        }
    }

    public function delete($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return redirect()->to('department')->with('error', 'Department not found.');
        }

        // Check if department has employees
        if ($this->departmentModel->hasEmployees($id)) {
            return redirect()->to('department')->with('error', 'Cannot delete department with employees.');
        }

        if ($this->departmentModel->delete($id)) {
            return redirect()->to('department')->with('success', 'Department deleted successfully.');
        } else {
            return redirect()->to('department')->with('error', 'Failed to delete department.');
        }
    }

    public function toggleStatus($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return $this->response->setJSON(['success' => false, 'message' => 'Department not found.']);
        }

        $newStatus = $department['status'] === 'active' ? 'inactive' : 'active';
        
        if ($this->departmentModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function getDepartments()
    {
        $departments = $this->departmentModel->getActiveDepartments();
        
        return $this->response->setJSON(['success' => true, 'departments' => $departments]);
    }

    public function searchDepartments()
    {
        $search = $this->request->getGet('search');
        
        if (!$search) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search term required.']);
        }

        $departments = $this->departmentModel->searchDepartments($search);
        
        return $this->response->setJSON(['success' => true, 'departments' => $departments]);
    }
}
