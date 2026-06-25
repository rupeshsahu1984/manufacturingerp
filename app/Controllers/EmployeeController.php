<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Exception;

class EmployeeController extends BaseController
{
    protected $employeeModel;
    protected $departmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();
        $this->departmentModel = new Department();
        $this->userModel = new User();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'department' => $this->request->getGet('department'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Employee Master - PRODX',
            'employees' => $this->employeeModel->getEmployees($filters),
            'departments' => $this->departmentModel->getActiveDepartments(),
            'stats' => $this->employeeModel->getEmployeeStats(),
            'filters' => $filters
        ];

        return view('employee/index', $data);
    }

    public function create()
    {
        // Redirect to new HR module
        return redirect()->to('hr/employee/create');
    }

    public function store()
    {
        // Redirect to new HR module
        return redirect()->to('hr/employee/store');
    }

    public function show($id)
    {
        $employee = $this->employeeModel->getEmployeeWithDetails($id);
        
        if (!$employee) {
            return redirect()->to('employee')->with('error', 'Employee not found.');
        }

        $data = [
            'title' => 'Employee Details - PRODX',
            'employee' => $employee
        ];

        return view('employee/show', $data);
    }

    public function edit($id)
    {
        $employee = $this->employeeModel->find($id);
        
        if (!$employee) {
            return redirect()->to('employee')->with('error', 'Employee not found.');
        }

        $data = [
            'title' => 'Edit Employee - PRODX',
            'employee' => $employee,
            'departments' => $this->departmentModel->getActiveDepartments(),
            'users' => $this->userModel->getAllUsersWithRoles()
        ];

        return view('employee/edit', $data);
    }

    public function update($id)
    {
        $employee = $this->employeeModel->find($id);
        
        if (!$employee) {
            return redirect()->to('employee')->with('error', 'Employee not found.');
        }

        $rules = [
            'employee_code' => "required|min_length[3]|max_length[20]|is_unique[employees.employee_code,id,$id]",
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|max_length[100]|is_unique[employees.email,id,$id]",
            'phone' => 'required|max_length[20]',
            'department_id' => 'required|integer',
            'designation' => 'required|max_length[100]',
            'joining_date' => 'required|valid_date',
            'salary' => 'required|numeric',
            'status' => 'required|in_list[active,inactive,terminated]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'employee_code' => $this->request->getPost('employee_code'),
            'user_id' => $this->request->getPost('user_id') ?: null,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'department_id' => $this->request->getPost('department_id'),
            'designation' => $this->request->getPost('designation'),
            'joining_date' => $this->request->getPost('joining_date'),
            'salary' => $this->request->getPost('salary'),
            'allowances' => $this->request->getPost('allowances') ?: 0,
            'deductions' => $this->request->getPost('deductions') ?: 0,
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->employeeModel->update($id, $data)) {
            return redirect()->to('employee')->with('success', 'Employee updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update employee.');
        }
    }

    public function delete($id)
    {
        $employee = $this->employeeModel->find($id);
        
        if (!$employee) {
            return redirect()->to('employee')->with('error', 'Employee not found.');
        }

        if ($this->employeeModel->delete($id)) {
            return redirect()->to('employee')->with('success', 'Employee deleted successfully.');
        } else {
            return redirect()->to('employee')->with('error', 'Failed to delete employee.');
        }
    }

    public function toggleStatus($id)
    {
        $employee = $this->employeeModel->find($id);
        
        if (!$employee) {
            return $this->response->setJSON(['success' => false, 'message' => 'Employee not found.']);
        }

        $newStatus = $employee['status'] === 'active' ? 'inactive' : 'active';
        
        if ($this->employeeModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function export()
    {
        $employees = $this->employeeModel->getEmployees([]);
        
        $filename = 'employees_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Employee Code', 'Name', 'Email', 'Phone', 'Department', 
            'Designation', 'Joining Date', 'Salary', 'Status'
        ]);
        
        foreach ($employees as $employee) {
            fputcsv($output, [
                $employee['employee_code'],
                $employee['first_name'] . ' ' . $employee['last_name'],
                $employee['email'],
                $employee['phone'],
                $employee['department_name'] ?? '',
                $employee['designation'],
                $employee['joining_date'],
                $employee['salary'],
                $employee['status']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getEmployeesByDepartment()
    {
        $departmentId = $this->request->getGet('department_id');
        
        if (!$departmentId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Department ID required.']);
        }

        $employees = $this->employeeModel->getEmployeesByDepartment($departmentId);
        
        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }

    public function searchEmployees()
    {
        $search = $this->request->getGet('search');
        
        if (!$search) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search term required.']);
        }

        $employees = $this->employeeModel->searchEmployees($search);
        
        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }
}