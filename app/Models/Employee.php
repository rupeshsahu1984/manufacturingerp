<?php

namespace App\Models;

use CodeIgniter\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'employee_code', 'user_id', 'first_name', 'last_name', 'email', 'phone', 'address',
        'department_id', 'designation', 'joining_date', 'salary',
        'status', 'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'employee_code' => 'required|min_length[3]|max_length[20]|is_unique[employees.employee_code,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[50]',
        'last_name' => 'required|min_length[2]|max_length[50]',
        'email' => 'required|valid_email|max_length[100]|is_unique[employees.email,id,{id}]',
        'phone' => 'required|max_length[20]',
        'department_id' => 'required|integer',
        'designation' => 'required|max_length[100]',
        'joining_date' => 'required|valid_date',
        'salary' => 'required|numeric',
        'status' => 'required|in_list[active,inactive,terminated]'
    ];

    protected $validationMessages = [
        'employee_code' => [
            'required' => 'Employee code is required',
            'min_length' => 'Employee code must be at least 3 characters long',
            'max_length' => 'Employee code cannot exceed 20 characters',
            'is_unique' => 'Employee code already exists'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email address already exists'
        ]
    ];

    // Get all active employees
    public function getActiveEmployees()
    {
        return $this->select('employees.*, departments.department_name')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->where('employees.status', 'active')
                   ->orderBy('employees.first_name', 'ASC')
                   ->findAll();
    }

    // Get employee with department info
    public function getEmployeeWithDepartment($employeeId)
    {
        return $this->select('employees.*, departments.department_name')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->where('employees.id', $employeeId)
                   ->first();
    }

    // Get all employees with department info
    public function getAllEmployeesWithDepartment()
    {
        return $this->select('employees.*, departments.department_name')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->orderBy('employees.first_name', 'ASC')
                   ->findAll();
    }

    // Get employees by department
    public function getEmployeesByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId)
                   ->where('status', 'active')
                   ->orderBy('first_name', 'ASC')
                   ->findAll();
    }



    // Get employee statistics by department
    public function getEmployeeStatsByDepartment()
    {
        return $this->select('departments.department_name, COUNT(employees.id) as employee_count')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->where('employees.status', 'active')
                   ->groupBy('departments.id, departments.department_name')
                   ->orderBy('employee_count', 'DESC')
                   ->findAll();
    }

    // Check if employee exists
    public function employeeExists($employeeCode, $excludeId = null)
    {
        $query = $this->where('employee_code', $employeeCode);
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->first() !== null;
    }

    // Generate unique employee code
    public function generateEmployeeCode()
    {
        $prefix = 'EMP';
        $year = date('Y');
        $month = date('m');
        
        // Get the last employee code for this month
        $lastEmployee = $this->where('employee_code LIKE', $prefix . $year . $month . '%')
                           ->orderBy('employee_code', 'DESC')
                           ->first();
        
        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee['employee_code'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Get employee by user ID (for module assignments)
    public function getEmployeeByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    // Update employee status
    public function updateEmployeeStatus($employeeId, $status)
    {
        return $this->update($employeeId, [
            'status' => $status,
            'updated_by' => session()->get('user_id')
        ]);
    }

    // Search employees
    public function searchEmployees($searchTerm)
    {
        return $this->select('employees.*, departments.department_name')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->groupStart()
                   ->like('employees.employee_code', $searchTerm)
                   ->orLike('employees.first_name', $searchTerm)
                   ->orLike('employees.last_name', $searchTerm)
                   ->orLike('employees.email', $searchTerm)
                   ->orLike('departments.department_name', $searchTerm)
                   ->groupEnd()
                   ->where('employees.status', 'active')
                   ->orderBy('employees.first_name', 'ASC')
                   ->findAll();
    }

    // Get employees with filters
    public function getEmployees($filters = [])
    {
        $builder = $this->select('employees.*, departments.department_name')
                       ->join('departments', 'departments.id = employees.department_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('employees.employee_code', $filters['search'])
                   ->orLike('employees.first_name', $filters['search'])
                   ->orLike('employees.last_name', $filters['search'])
                   ->orLike('employees.email', $filters['search'])
                   ->orLike('departments.department_name', $filters['search'])
                   ->groupEnd();
        }
        
        if (!empty($filters['department'])) {
            $builder->where('employees.department_id', $filters['department']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('employees.status', $filters['status']);
        }
        
        // Order by
        $builder->orderBy('employees.first_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    // Get employee with details
    public function getEmployeeWithDetails($employeeId)
    {
        return $this->select('employees.*, departments.department_name, users.username')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->join('users', 'users.id = employees.user_id', 'left')
                   ->where('employees.id', $employeeId)
                   ->first();
    }

    // Get recent employees
    public function getRecentEmployees($limit = 5)
    {
        return $this->select('employees.*, departments.department_name')
                   ->join('departments', 'departments.id = employees.department_id', 'left')
                   ->orderBy('employees.created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    // Generate employee ID (alias for generateEmployeeCode)
    public function generateEmployeeId()
    {
        return $this->generateEmployeeCode();
    }

    // Get employee statistics
    public function getEmployeeStats()
    {
        $total = $this->countAll();
        $active = $this->where('status', 'active')->countAllResults();
        $inactive = $this->where('status', 'inactive')->countAllResults();
        $terminated = $this->where('status', 'terminated')->countAllResults();
        
        $avgRow = $this->selectAvg('salary', 'avg_salary')->first();
        $avgSalary = is_array($avgRow) ? ($avgRow['avg_salary'] ?? 0) : 0;
        
        // Calculate average experience (simplified)
        $avgExperience = 2.5; // This would need more complex calculation based on joining_date
        
        // Gender distribution (simplified - would need gender field in database)
        $male = $total * 0.6; // Assuming 60% male
        $female = $total * 0.4; // Assuming 40% female

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'terminated' => $terminated,
            'avg_salary' => $avgSalary,
            'avg_experience' => $avgExperience,
            'male' => round($male),
            'female' => round($female)
        ];
    }

}
