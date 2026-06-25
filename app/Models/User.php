<?php
namespace App\Models;
use CodeIgniter\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 'email', 'password', 'full_name', 'role', 'status',
        'last_login', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'full_name' => 'required|min_length[3]|max_length[100]',
        'role' => 'required|in_list[admin,manager,operator,viewer]',
        'status' => 'required|in_list[active,inactive]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'required' => 'Username is required',
            'min_length' => 'Username must be at least 3 characters',
            'max_length' => 'Username cannot exceed 50 characters',
            'is_unique' => 'Username already exists'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already exists'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 6 characters'
        ],
        'full_name' => [
            'required' => 'Full name is required',
            'min_length' => 'Full name must be at least 3 characters',
            'max_length' => 'Full name cannot exceed 100 characters'
        ],
        'role' => [
            'required' => 'Role is required',
            'in_list' => 'Invalid role'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Invalid status'
        ]
    ];

    public function authenticate($username, $password)
    {
        $user = $this->where('username', $username)
            ->where('status', 'active')
            ->first();
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }
        
        return false;
    }

    public function getUsersByRole($role = null)
    {
        $builder = $this->db->table('users');
        
        if ($role) {
            $builder->where('role', $role);
        }
        
        $builder->where('status', 'active');
        $builder->orderBy('full_name', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function getUserStats()
    {
        $builder = $this->db->table('users');
        
        $stats = [
            'total' => $builder->countAllResults(),
            'active' => $builder->where('status', 'active')->countAllResults(),
            'inactive' => $builder->where('status', 'inactive')->countAllResults(),
            'admin' => $builder->where('role', 'admin')->where('status', 'active')->countAllResults(),
            'manager' => $builder->where('role', 'manager')->where('status', 'active')->countAllResults(),
            'operator' => $builder->where('role', 'operator')->where('status', 'active')->countAllResults(),
            'viewer' => $builder->where('role', 'viewer')->where('status', 'active')->countAllResults()
        ];
        
        return $stats;
    }

    public function updatePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashedPassword]);
    }

    public function isUsernameUnique($username, $excludeId = null)
    {
        $builder = $this->db->table('users');
        $builder->where('username', $username);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() === 0;
    }

    public function isEmailUnique($email, $excludeId = null)
    {
        $builder = $this->db->table('users');
        $builder->where('email', $email);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() === 0;
    }

    // Get all users with roles
    public function getAllUsersWithRoles()
    {
        return $this->select('users.*')
                   ->where('status', 'active')
                   ->orderBy('full_name', 'ASC')
                   ->findAll();
    }
} 