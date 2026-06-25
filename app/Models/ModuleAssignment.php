<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleAssignment extends Model
{
    protected $table = 'module_assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'department_id', 'employee_id', 'module_name', 'permission_level', 
        'can_view', 'can_create', 'can_edit', 'can_delete', 'can_export', 
        'can_print', 'status', 'assigned_by', 'assigned_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'module_name' => 'required|max_length[100]',
        'permission_level' => 'required|in_list[view,create,edit,delete,admin]',
        'can_view' => 'required|in_list[0,1]',
        'can_create' => 'required|in_list[0,1]',
        'can_edit' => 'required|in_list[0,1]',
        'can_delete' => 'required|in_list[0,1]',
        'can_export' => 'required|in_list[0,1]',
        'can_print' => 'required|in_list[0,1]',
        'status' => 'required|in_list[active,inactive]'
    ];

    // Get modules assigned to a department
    public function getModulesByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId)
                   ->where('employee_id IS NULL')
                   ->where('status', 'active')
                   ->orderBy('module_name', 'ASC')
                   ->findAll();
    }

    // Get modules assigned to an employee
    public function getModulesByEmployee($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                   ->where('status', 'active')
                   ->orderBy('module_name', 'ASC')
                   ->findAll();
    }

    // Get all module assignments for a department (including employee-specific)
    public function getAllAssignmentsByDepartment($departmentId)
    {
        $assignments = $this->where('department_id', $departmentId)
                           ->where('status', 'active')
                           ->orderBy('module_name', 'ASC')
                           ->findAll();

        // Group by module and include employee assignments
        $grouped = [];
        foreach ($assignments as $assignment) {
            $moduleName = $assignment['module_name'];
            
            if (!isset($grouped[$moduleName])) {
                $grouped[$moduleName] = [
                    'department_assignment' => null,
                    'employee_assignments' => []
                ];
            }

            if ($assignment['employee_id']) {
                $grouped[$moduleName]['employee_assignments'][] = $assignment;
            } else {
                $grouped[$moduleName]['department_assignment'] = $assignment;
            }
        }

        return $grouped;
    }

    // Assign module to department
    public function assignModuleToDepartment($departmentId, $moduleName, $permissions, $assignedBy)
    {
        // Check if assignment already exists
        $existing = $this->where('department_id', $departmentId)
                        ->where('module_name', $moduleName)
                        ->where('employee_id IS NULL')
                        ->first();

        $data = [
            'department_id' => $departmentId,
            'module_name' => $moduleName,
            'permission_level' => $permissions['permission_level'],
            'can_view' => isset($permissions['can_view']) ? $permissions['can_view'] : 0,
            'can_create' => isset($permissions['can_create']) ? $permissions['can_create'] : 0,
            'can_edit' => isset($permissions['can_edit']) ? $permissions['can_edit'] : 0,
            'can_delete' => isset($permissions['can_delete']) ? $permissions['can_delete'] : 0,
            'can_export' => isset($permissions['can_export']) ? $permissions['can_export'] : 0,
            'can_print' => isset($permissions['can_print']) ? $permissions['can_print'] : 0,
            'status' => 'active',
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            // Update existing assignment
            return $this->update($existing['id'], $data);
        } else {
            // Create new assignment
            return $this->insert($data);
        }
    }

    // Assign module to employee
    public function assignModuleToEmployee($employeeId, $moduleName, $permissions, $assignedBy)
    {
        // Check if assignment already exists
        $existing = $this->where('employee_id', $employeeId)
                        ->where('module_name', $moduleName)
                        ->first();

        $data = [
            'employee_id' => $employeeId,
            'module_name' => $moduleName,
            'permission_level' => $permissions['permission_level'],
            'can_view' => isset($permissions['can_view']) ? $permissions['can_view'] : 0,
            'can_create' => isset($permissions['can_create']) ? $permissions['can_create'] : 0,
            'can_edit' => isset($permissions['can_edit']) ? $permissions['can_edit'] : 0,
            'can_delete' => isset($permissions['can_delete']) ? $permissions['can_delete'] : 0,
            'can_export' => isset($permissions['can_export']) ? $permissions['can_export'] : 0,
            'can_print' => isset($permissions['can_print']) ? $permissions['can_print'] : 0,
            'status' => 'active',
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            // Update existing assignment
            return $this->update($existing['id'], $data);
        } else {
            // Create new assignment
            return $this->insert($data);
        }
    }

    // Remove module assignment from department
    public function removeModuleFromDepartment($departmentId, $moduleName)
    {
        return $this->where('department_id', $departmentId)
                   ->where('module_name', $moduleName)
                   ->where('employee_id IS NULL')
                   ->set(['status' => 'inactive'])
                   ->update();
    }

    // Remove module assignment from employee
    public function removeModuleFromEmployee($employeeId, $moduleName)
    {
        return $this->where('employee_id', $employeeId)
                   ->where('module_name', $moduleName)
                   ->set(['status' => 'inactive'])
                   ->update();
    }

    // Check if user has access to module
    public function hasModuleAccess($userId, $moduleName, $permission = 'view')
    {
        // Get user's department and employee info
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return false;
        }

        // Check department-level assignment
        if ($user['department_id']) {
            $deptAssignment = $this->where('department_id', $user['department_id'])
                                 ->where('module_name', $moduleName)
                                 ->where('employee_id IS NULL')
                                 ->where('status', 'active')
                                 ->first();

            if ($deptAssignment && $this->hasPermission($deptAssignment, $permission)) {
                return true;
            }
        }

        // Check employee-specific assignment
        $empAssignment = $this->where('employee_id', $userId)
                             ->where('module_name', $moduleName)
                             ->where('status', 'active')
                             ->first();

        if ($empAssignment && $this->hasPermission($empAssignment, $permission)) {
            return true;
        }

        return false;
    }

    // Check if assignment has specific permission
    private function hasPermission($assignment, $permission)
    {
        switch ($permission) {
            case 'view':
                return $assignment['can_view'] == 1;
            case 'create':
                return $assignment['can_create'] == 1;
            case 'edit':
                return $assignment['can_edit'] == 1;
            case 'delete':
                return $assignment['can_delete'] == 1;
            case 'export':
                return $assignment['can_export'] == 1;
            case 'print':
                return $assignment['can_print'] == 1;
            default:
                return false;
        }
    }

    // Get available modules list
    public function getAvailableModules()
    {
        return [
            'dashboard' => 'Dashboard',
            'master_settings' => 'Master Settings',
            'supplier' => 'Supplier Master',
            'customer' => 'Customer Master',
            'product' => 'Material Master',
            'category' => 'Category Master',
            'production_settings' => 'Production Settings',
            'bom' => 'BOM Management',
            'warehouse' => 'Warehouse Master',
            'department' => 'Department Master',
            'employee' => 'Employee Master',
            'purchase_requisition' => 'Purchase Requisition',
            'purchase_order' => 'Purchase Order',
            'purchase_bills' => 'Purchase Bills',
            'purchase_returns' => 'Purchase Returns',
            'vendor_payments' => 'Vendor Payments',
            'sales_orders' => 'Sales Orders',
            'sales_invoices' => 'Sales Invoices',
            'sales_returns' => 'Sales Returns',
            'customer_payments' => 'Customer Payments',
            'quotations' => 'Quotations',
            'work_orders' => 'Work Orders',
            'production_planning' => 'Production Planning',
            'production_tracking' => 'Production Tracking',
            'quality_control' => 'Quality Control',
            'waste_management' => 'Waste Management',
            'stock' => 'Stock Management',
            'inventory' => 'Inventory',
            'warehouse_operations' => 'Warehouse Operations',
            'stock_transfers' => 'Stock Transfers',
            'attendance' => 'Attendance',
            'salary' => 'Salary',
            'payroll' => 'Payroll',
            'leave_management' => 'Leave Management',
            'documents' => 'Documents',
            'general_ledger' => 'General Ledger',
            'accounts_payable' => 'Accounts Payable',
            'accounts_receivable' => 'Accounts Receivable',
            'bank_reconciliation' => 'Bank Reconciliation',
            'gst_management' => 'GST Management',
            'profit_loss' => 'Profit & Loss',
            'balance_sheet' => 'Balance Sheet',
            'sales_reports' => 'Sales Reports',
            'purchase_reports' => 'Purchase Reports',
            'production_reports' => 'Production Reports',
            'inventory_reports' => 'Inventory Reports',
            'financial_reports' => 'Financial Reports',
            'hr_reports' => 'HR Reports',
            'custom_reports' => 'Custom Reports',
            'gate_entry' => 'Gate Entry',
            'gate_exit' => 'Gate Exit',
            'visitor_management' => 'Visitor Management',
            'company_profile' => 'Company Profile',
            'user_management' => 'User Management',
            'role_management' => 'Role Management',
            'system_settings' => 'System Settings',
            'backup_restore' => 'Backup & Restore',
            'audit_logs' => 'Audit Logs'
        ];
    }
}
