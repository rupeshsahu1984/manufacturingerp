<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Exception;

class HRController extends BaseController
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
        $data = [
            'title' => 'HR Management - PRODX',
            'total_employees' => $this->employeeModel->countAll(),
            'active_employees' => $this->employeeModel->where('status', 'active')->countAllResults(),
            'departments' => $this->departmentModel->countAll(),
            'recent_employees' => $this->employeeModel->getRecentEmployees(5),
            'department_stats' => $this->employeeModel->getEmployeeStatsByDepartment(),
            'employee_stats' => $this->employeeModel->getEmployeeStats()
        ];

        return view('hr/dashboard', $data);
    }

    public function employees()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'department' => $this->request->getGet('department'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Employee Management - PRODX',
            'employees' => $this->employeeModel->getEmployees($filters),
            'departments' => $this->departmentModel->getActiveDepartments(),
            'stats' => $this->employeeModel->getEmployeeStats(),
            'filters' => $filters
        ];

        return view('hr/employees', $data);
    }

    public function departments()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Department Management - PRODX',
            'departments' => $this->departmentModel->getDepartments($filters),
            'stats' => $this->departmentModel->getDepartmentStats(),
            'filters' => $filters
        ];

        return view('hr/departments', $data);
    }

    public function createEmployee()
    {
        $data = [
            'title' => 'Add New Employee - PRODX',
            'departments' => $this->departmentModel->getActiveDepartments(),
            'users' => $this->userModel->orderBy('username', 'ASC')->findAll(),
            'suggested_code' => $this->employeeModel->generateEmployeeCode(),
        ];

        return view('hr/create_employee', $data);
    }

    public function storeEmployee()
    {
        $rules = [
            'employee_code' => 'required|min_length[3]|max_length[20]|is_unique[employees.employee_code]',
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[employees.email]',
            'phone' => 'required|max_length[20]',
            'department_id' => 'required|integer',
            'designation' => 'required|max_length[100]',
            'joining_date' => 'required|valid_date',
            'salary' => 'required|numeric',
            'status' => 'required|in_list[active,inactive,terminated]',
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
            'status' => $this->request->getPost('status'),
        ];

        if ($this->employeeModel->insert($data)) {
            return redirect()->to('hr/employees')->with('success', 'Employee added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add employee.');
    }

    public function editEmployee($id)
    {
        $employee = $this->employeeModel->getEmployeeWithDetails($id);

        if (!$employee) {
            return redirect()->to('hr/employees')->with('error', 'Employee not found.');
        }

        $data = [
            'title' => 'Edit Employee - PRODX',
            'employee' => $employee,
            'departments' => $this->departmentModel->getActiveDepartments(),
            'users' => $this->userModel->orderBy('username', 'ASC')->findAll(),
        ];

        return view('hr/edit_employee', $data);
    }

    public function updateEmployee($id)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            return redirect()->to('hr/employees')->with('error', 'Employee not found.');
        }

        $rules = [
            'employee_code' => "required|min_length[3]|max_length[20]|is_unique[employees.employee_code,id,$id]",
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|is_unique[employees.email,id,$id]",
            'phone' => 'required|max_length[20]',
            'department_id' => 'required|integer',
            'designation' => 'required|max_length[100]',
            'joining_date' => 'required|valid_date',
            'salary' => 'required|numeric',
            'status' => 'required|in_list[active,inactive,terminated]',
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
            'status' => $this->request->getPost('status'),
        ];

        if ($this->employeeModel->update($id, $data)) {
            return redirect()->to('hr/employees')->with('success', 'Employee updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update employee.');
    }

    public function deleteEmployee($id)
    {
        $employee = $this->employeeModel->find($id);
        
        if (!$employee) {
            return redirect()->to('hr/employees')->with('error', 'Employee not found.');
        }

        if ($this->employeeModel->delete($id)) {
            return redirect()->to('hr/employees')->with('success', 'Employee deleted successfully.');
        } else {
            return redirect()->to('hr/employees')->with('error', 'Failed to delete employee.');
        }
    }

    public function createDepartment()
    {
        $data = [
            'title' => 'Add New Department - PRODX'
        ];

        return view('hr/create_department', $data);
    }

    public function storeDepartment()
    {
        $rules = [
            'department_name' => 'required|max_length[100]|is_unique[departments.department_name]',
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
            return redirect()->to('hr/departments')->with('success', 'Department added successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add department.');
        }
    }

    public function editDepartment($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return redirect()->to('hr/departments')->with('error', 'Department not found.');
        }

        $data = [
            'title' => 'Edit Department - PRODX',
            'department' => $department
        ];

        return view('hr/edit_department', $data);
    }

    public function updateDepartment($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return redirect()->to('hr/departments')->with('error', 'Department not found.');
        }

        $rules = [
            'department_name' => "required|max_length[100]|is_unique[departments.department_name,id,$id]",
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
            return redirect()->to('hr/departments')->with('success', 'Department updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update department.');
        }
    }

    public function deleteDepartment($id)
    {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            return redirect()->to('hr/departments')->with('error', 'Department not found.');
        }

        // Check if department has employees
        if ($this->departmentModel->hasEmployees($id)) {
            return redirect()->to('hr/departments')->with('error', 'Cannot delete department with employees. Please reassign employees first.');
        }

        if ($this->departmentModel->delete($id)) {
            return redirect()->to('hr/departments')->with('success', 'Department deleted successfully.');
        } else {
            return redirect()->to('hr/departments')->with('error', 'Failed to delete department.');
        }
    }

    public function analytics()
    {
        $data = [
            'title' => 'HR Analytics - PRODX',
            'employee_stats' => $this->employeeModel->getEmployeeStats(),
            'department_stats' => $this->departmentModel->getDepartmentStats(),
            'department_breakdown' => $this->employeeModel->getEmployeeStatsByDepartment(),
            'recent_employees' => $this->employeeModel->getRecentEmployees(10),
        ];

        return view('hr/analytics', $data);
    }

    public function reports()
    {
        $report_type = $this->request->getGet('type') ?? 'employee';

        $data = [
            'title' => 'HR Reports - PRODX',
            'report_type' => $report_type,
            'employees' => $this->employeeModel->getEmployees([]),
            'departments' => $this->departmentModel->getDepartments([]),
        ];

        return view('hr/reports', $data);
    }

    public function exportEmployees()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'department' => $this->request->getGet('department'),
            'status' => $this->request->getGet('status')
        ];

        $employees = $this->employeeModel->getEmployees($filters);
        
        $filename = 'employees_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Employee ID', 'Name', 'Email', 'Phone', 'Department', 'Position', 'Join Date', 'Salary', 'Status'
        ]);
        
        foreach ($employees as $employee) {
            fputcsv($output, [
                $employee['employee_code'] ?? '',
                trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')),
                $employee['email'] ?? '',
                $employee['phone'] ?? '',
                $employee['department_name'] ?? 'N/A',
                $employee['designation'] ?? '',
                $employee['joining_date'] ?? '',
                $employee['salary'] ?? '',
                $employee['status'] ?? '',
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function exportDepartments()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status')
        ];

        $departments = $this->departmentModel->getDepartments($filters);
        
        $filename = 'departments_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Department Name', 'Description', 'Employee Count', 'Status', 'Created Date'
        ]);
        
        foreach ($departments as $department) {
            fputcsv($output, [
                $department['department_name'],
                isset($department['description']) ? $department['description'] : '',
                isset($department['employee_count']) ? $department['employee_count'] : 0,
                $department['status'],
                $department['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getEmployeeDetails($id)
    {
        $employee = $this->employeeModel->getEmployeeWithDetails($id);
        
        if (!$employee) {
            return $this->response->setJSON(['success' => false, 'message' => 'Employee not found.']);
        }

        return $this->response->setJSON(['success' => true, 'employee' => $employee]);
    }

    public function getDepartmentEmployees($department_id)
    {
        $employees = $this->employeeModel->getEmployeesByDepartment($department_id);
        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }

    public function updateEmployeeStatus($id)
    {
        $employee = $this->employeeModel->find($id);
        
        if (!$employee) {
            return $this->response->setJSON(['success' => false, 'message' => 'Employee not found.']);
        }

        $newStatus = $this->request->getPost('status');
        
        if ($this->employeeModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function attendance()
    {
        $data = [
            'title' => 'Attendance Management',
            'page_title' => 'Attendance Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'HR', 'url' => base_url('hr')],
                ['title' => 'Attendance', 'url' => '']
            ]
        ];
        
        return view('hr/attendance', $data);
    }

    public function leaveManagement()
    {
        $data = [
            'title' => 'Leave Management',
            'page_title' => 'Leave Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'HR', 'url' => base_url('hr')],
                ['title' => 'Leave Management', 'url' => '']
            ]
        ];
        
        return view('hr/leave', $data);
    }

    public function salaryManagement()
    {
        $data = [
            'title' => 'Salary Management',
            'page_title' => 'Salary Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'HR', 'url' => base_url('hr')],
                ['title' => 'Salary Management', 'url' => '']
            ]
        ];
        
        return view('hr/salary', $data);
    }

    public function payroll()
    {
        $data = [
            'title' => 'Payroll Management',
            'page_title' => 'Payroll Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'HR', 'url' => base_url('hr')],
                ['title' => 'Payroll', 'url' => '']
            ]
        ];
        
        return view('hr/payroll', $data);
    }

    public function documents()
    {
        $data = [
            'title' => 'Document Management',
            'page_title' => 'Document Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'HR', 'url' => base_url('hr')],
                ['title' => 'Documents', 'url' => '']
            ]
        ];
        
        return view('hr/documents', $data);
    }

    public function training()
    {
        $data = [
            'title' => 'Training Management',
            'page_title' => 'Training Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'HR', 'url' => base_url('hr')],
                ['title' => 'Training', 'url' => '']
            ]
        ];
        
        return view('hr/training', $data);
    }
}
