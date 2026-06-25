<?php

namespace App\Models;

use CodeIgniter\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'department_name', 'description', 'status', 'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'department_name' => 'required|min_length[3]|max_length[100]|is_unique[departments.department_name,id,{id}]',
        'description' => 'permit_empty|max_length[500]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'department_name' => [
            'required' => 'Department name is required',
            'min_length' => 'Department name must be at least 3 characters long',
            'max_length' => 'Department name cannot exceed 100 characters',
            'is_unique' => 'Department name already exists'
        ]
    ];

    // Get all active departments
    public function getActiveDepartments()
    {
        return $this->where('status', 'active')
                   ->orderBy('department_name', 'ASC')
                   ->findAll();
    }

    // Get department with assigned modules
    public function getDepartmentWithModules($departmentId)
    {
        $department = $this->find($departmentId);
        if (!$department) {
            return null;
        }

        // Get assigned modules for this department
        $moduleAssignmentModel = new ModuleAssignment();
        $department['assigned_modules'] = $moduleAssignmentModel->getModulesByDepartment($departmentId);

        return $department;
    }

    // Get all departments with their module assignments
    public function getAllDepartmentsWithModules()
    {
        $departments = $this->orderBy('department_name', 'ASC')->findAll();
        
        $moduleAssignmentModel = new ModuleAssignment();
        
        foreach ($departments as &$department) {
            $department['assigned_modules'] = $moduleAssignmentModel->getModulesByDepartment($department['id']);
        }

        return $departments;
    }

    // Check if department exists
    public function departmentExists($departmentName, $excludeId = null)
    {
        $query = $this->where('department_name', $departmentName);
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->first() !== null;
    }

    // Get department statistics
    public function getDepartmentStats()
    {
        $total = $this->countAll();
        $active = $this->where('status', 'active')->countAllResults();
        $inactive = $this->where('status', 'inactive')->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive
        ];
    }

    // Get departments with filters
    public function getDepartments($filters = [])
    {
        $builder = $this->builder();
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->like('department_name', $filters['search']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        // Order by
        $builder->orderBy('department_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    // Check if department has employees
    public function hasEmployees($departmentId)
    {
        $db = \Config\Database::connect();
        
        try {
            $result = $db->table('employees')
                        ->where('department_id', $departmentId)
                        ->countAllResults();
            
            return $result > 0;
        } catch (Exception $e) {
            // Table doesn't exist or other error
            return false;
        }
    }

    // Search departments
    public function searchDepartments($searchTerm)
    {
        return $this->like('department_name', $searchTerm)
                   ->where('status', 'active')
                   ->orderBy('department_name', 'ASC')
                   ->findAll();
    }
}
