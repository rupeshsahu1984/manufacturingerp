<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\ModuleAssignment;
use App\Models\Company;
use App\Models\User;
use App\Models\Employee;

class Settings extends BaseController
{
    protected $departmentModel;
    protected $moduleAssignmentModel;
    protected $companyModel;
    protected $userModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->departmentModel = new Department();
        $this->moduleAssignmentModel = new ModuleAssignment();
        $this->companyModel = new Company();
        $this->userModel = new User();
        $this->employeeModel = new Employee();
    }

    // Module Assignment Management
    public function moduleAssignments()
    {
        $role = session()->get('user_role');
        if (! in_array($role, ['super_admin', 'superadmin', 'SuperAdmin'], true)) {
            return redirect()->to('dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        $data = [
            'title' => 'Module Assignments',
            'departments' => [],
            'employees' => [],
            'availableModules' => [],
        ];
        try {
            $data['departments'] = $this->departmentModel->getAllDepartmentsWithModules();
            $data['employees'] = $this->employeeModel->getActiveEmployees();
            $data['availableModules'] = $this->moduleAssignmentModel->getAvailableModules();
        } catch (\Throwable $e) {
            log_message('error', 'Settings::moduleAssignments: ' . $e->getMessage());
            $data['settings_error'] = 'Could not load departments or module metadata. Check database tables.';
        }

        return view('settings/module_assignments', $data);
    }

    // Assign modules to department
    public function assignModulesToDepartment()
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $departmentId = $this->request->getPost('department_id');
        $modules = $this->request->getPost('modules');
        $assignedBy = session()->get('user_id');

        if (!$modules || !is_array($modules)) {
            return redirect()->back()->with('error', 'No modules selected for assignment.');
        }

        $successCount = 0;
        foreach ($modules as $moduleName => $permissions) {
            $result = $this->moduleAssignmentModel->assignModuleToDepartment(
                $departmentId, 
                $moduleName, 
                $permissions, 
                $assignedBy
            );
            
            if ($result) {
                $successCount++;
            }
        }

        if ($successCount > 0) {
            return redirect()->back()->with('success', "Successfully assigned {$successCount} modules to department.");
        } else {
            return redirect()->back()->with('error', 'Failed to assign modules to department.');
        }
    }

    // Assign modules to employee
    public function assignModulesToEmployee()
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $employeeId = $this->request->getPost('employee_id');
        $modules = $this->request->getPost('modules');
        $assignedBy = session()->get('user_id');

        if (!$modules || !is_array($modules)) {
            return redirect()->back()->with('error', 'No modules selected for assignment.');
        }

        $successCount = 0;
        foreach ($modules as $moduleName => $permissions) {
            $result = $this->moduleAssignmentModel->assignModuleToEmployee(
                $employeeId, 
                $moduleName, 
                $permissions, 
                $assignedBy
            );
            
            if ($result) {
                $successCount++;
            }
        }

        if ($successCount > 0) {
            return redirect()->back()->with('success', "Successfully assigned {$successCount} modules to employee.");
        } else {
            return redirect()->back()->with('error', 'Failed to assign modules to employee.');
        }
    }

    // Remove module assignment
    public function removeModuleAssignment()
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $assignmentType = $this->request->getPost('assignment_type');
        $moduleName = $this->request->getPost('module_name');

        if ($assignmentType === 'department') {
            $departmentId = $this->request->getPost('department_id');
            $result = $this->moduleAssignmentModel->removeModuleFromDepartment($departmentId, $moduleName);
        } else {
            $employeeId = $this->request->getPost('employee_id');
            $result = $this->moduleAssignmentModel->removeModuleFromEmployee($employeeId, $moduleName);
        }

        if ($result) {
            return redirect()->back()->with('success', 'Module assignment removed successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to remove module assignment.');
        }
    }

    // Company Settings
    public function companySettings()
    {
        // Check if user is superadmin
        if (session()->get('user_role') !== 'super_admin') {
            return redirect()->to('dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        $data = [
            'title' => 'Company Settings',
            'company' => $this->companyModel->getCompanyProfile(),
            'currencies' => $this->companyModel->getAvailableCurrencies(),
            'timezones' => $this->companyModel->getAvailableTimezones(),
            'dateFormats' => $this->companyModel->getAvailableDateFormats(),
            'timeFormats' => $this->companyModel->getAvailableTimeFormats()
        ];

        return view('settings/company_settings', $data);
    }

    // Update company settings
    public function updateCompanySettings()
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $data = $this->request->getPost();
        $data['updated_by'] = session()->get('user_id');

        // Handle file uploads
        $logo = $this->request->getFile('logo');
        $favicon = $this->request->getFile('favicon');

        if ($logo && $logo->isValid()) {
            $data['logo'] = $logo;
        }

        if ($favicon && $favicon->isValid()) {
            $data['favicon'] = $favicon;
        }

        if ($this->companyModel->companyExists()) {
            $result = $this->companyModel->updateCompanyProfile($data);
        } else {
            $data['created_by'] = session()->get('user_id');
            $result = $this->companyModel->createInitialProfile($data);
        }

        if ($result) {
            return redirect()->back()->with('success', 'Company settings updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update company settings.');
        }
    }

    // Department Management
    public function departments()
    {
        // Check if user is superadmin
        if (session()->get('user_role') !== 'super_admin') {
            return redirect()->to('dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        $data = [
            'title' => 'Department Management',
            'departments' => $this->departmentModel->getAllDepartmentsWithModules(),
            'stats' => $this->departmentModel->getDepartmentStats()
        ];

        return view('settings/departments', $data);
    }

    // Create department
    public function createDepartment()
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $data = [
            'department_name' => $this->request->getPost('department_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id')
        ];

        if ($this->departmentModel->insert($data)) {
            return redirect()->back()->with('success', 'Department created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create department.');
        }
    }

    // Update department
    public function updateDepartment($id)
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $data = [
            'department_name' => $this->request->getPost('department_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id')
        ];

        if ($this->departmentModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Department updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update department.');
        }
    }

    // Delete department
    public function deleteDepartment($id)
    {
        // Check if department has employees
        $employeeCount = $this->employeeModel->where('department_id', $id)->countAllResults();
        
        if ($employeeCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete department. It has assigned employees.');
        }

        if ($this->departmentModel->delete($id)) {
            return redirect()->back()->with('success', 'Department deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete department.');
        }
    }

    // System Settings
    public function systemSettings()
    {
        $role = session()->get('user_role');
        if (! in_array($role, ['super_admin', 'superadmin', 'SuperAdmin'], true)) {
            return redirect()->to('dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        $company = [];
        try {
            $company = $this->companyModel->getCompanySettings();
        } catch (\Throwable $e) {
            log_message('error', 'Settings::systemSettings: ' . $e->getMessage());
        }
        if ($company === [] || $company === null) {
            $company = [
                'company_name' => '',
                'legal_name' => '',
                'logo_path' => '',
                'favicon_path' => '',
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'fiscal_year_start' => '04-01',
                'fiscal_year_end' => '03-31',
            ];
        }

        $data = [
            'title' => 'System Settings',
            'company' => $company,
        ];

        return view('settings/system_settings', $data);
    }

    // User Management
    public function userManagement()
    {
        $role = session()->get('user_role');
        if (! in_array($role, ['super_admin', 'superadmin', 'SuperAdmin'], true)) {
            return redirect()->to('dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        $data = [
            'title' => 'User Management',
            'users' => [],
            'departments' => [],
        ];
        try {
            $data['users'] = $this->userModel->getAllUsersWithRoles();
            $data['departments'] = $this->departmentModel->getActiveDepartments();
        } catch (\Throwable $e) {
            log_message('error', 'Settings::userManagement: ' . $e->getMessage());
            $data['settings_error'] = 'Could not load users or departments.';
        }

        return view('settings/user_management', $data);
    }

    // Create user
    public function createUser()
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'department_id' => $this->request->getPost('department_id'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id')
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->back()->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create user.');
        }
    }

    // Update user
    public function updateUser($id)
    {
        if (!$this->request->isPost()) {
            return redirect()->back();
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'department_id' => $this->request->getPost('department_id'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id')
        ];

        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $data)) {
            return redirect()->back()->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }

    // Delete user
    public function deleteUser($id)
    {
        // Prevent deleting own account
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Cannot delete your own account.');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->back()->with('success', 'User deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }

    // Get module assignments for department/employee (AJAX)
    public function getModuleAssignments()
    {
        $type = $this->request->getGet('type');
        $id = $this->request->getGet('id');

        if ($type === 'department') {
            $assignments = $this->moduleAssignmentModel->getModulesByDepartment($id);
        } else {
            $assignments = $this->moduleAssignmentModel->getModulesByEmployee($id);
        }

        return $this->response->setJSON($assignments);
    }
}
