<?php

namespace App\Models;

use CodeIgniter\Model;

class Warehouse extends Model
{
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'warehouse_code',
        'warehouse_name',
        'warehouse_type',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'contact_person',
        'phone',
        'email',
        'capacity_total',
        'capacity_used',
        'capacity_unit',
        'in_charge_id',
        'status',
        'description',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'warehouse_code' => 'required|min_length[3]|max_length[20]|is_unique[warehouses.warehouse_code,id,{id}]',
        'warehouse_name' => 'required|min_length[3]|max_length[100]',
        'warehouse_type' => 'required|in_list[head_office,factory,branch,distribution_center,retail_store]',
        'address' => 'required|min_length[10]',
        'city' => 'required|min_length[2]',
        'state' => 'required|min_length[2]',
        'pincode' => 'required|min_length[6]|max_length[10]',
        'country' => 'required|min_length[2]',
        'contact_person' => 'required|min_length[3]',
        'phone' => 'required|min_length[10]',
        'email' => 'required|valid_email',
        'capacity_total' => 'required|numeric|greater_than[0]',
        'capacity_unit' => 'required|in_list[sqft,sqm,pallets,units,kg,liters]',
        'in_charge_id' => 'required|integer|is_not_unique[employees.id]',
        'status' => 'required|in_list[active,inactive,maintenance,closed]'
    ];

    protected $validationMessages = [
        'warehouse_code' => [
            'required' => 'Warehouse code is required',
            'min_length' => 'Warehouse code must be at least 3 characters long',
            'max_length' => 'Warehouse code cannot exceed 20 characters',
            'is_unique' => 'Warehouse code must be unique'
        ],
        'warehouse_name' => [
            'required' => 'Warehouse name is required',
            'min_length' => 'Warehouse name must be at least 3 characters long'
        ],
        'capacity_total' => [
            'required' => 'Total capacity is required',
            'numeric' => 'Capacity must be a number',
            'greater_than' => 'Capacity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['generateWarehouseCode'];
    protected $beforeUpdate = ['updateCapacityUsed'];

    /**
     * Generate unique warehouse code
     */
    protected function generateWarehouseCode(array $data)
    {
        if (!isset($data['data']['warehouse_code']) || empty($data['data']['warehouse_code'])) {
            $data['data']['warehouse_code'] = $this->generateUniqueCode();
        }
        return $data;
    }

    /**
     * Update capacity used when stock changes
     */
    protected function updateCapacityUsed(array $data)
    {
        if (isset($data['data']['capacity_used'])) {
            $data['data']['capacity_used'] = $this->calculateCapacityUsed($data['id']);
        }
        return $data;
    }

    /**
     * Generate unique warehouse code
     */
    public function generateUniqueCode()
    {
        $prefix = 'WH';
        $year = date('Y');
        $month = date('m');
        
        // Get last code for this month
        $lastCode = $this->select('warehouse_code')
            ->like('warehouse_code', $prefix . $year . $month)
            ->orderBy('warehouse_code', 'DESC')
            ->first();
        
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode['warehouse_code'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate current capacity used
     */
    public function calculateCapacityUsed($warehouseId)
    {
        // Get total stock volume in this warehouse
        $stockModel = new Stock();
        $totalStock = $stockModel->select('SUM(quantity) as total_qty')
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'available')
            ->first();
        
        return isset($totalStock['total_qty']) ? $totalStock['total_qty'] : 0;
    }

    /**
     * Get warehouse with capacity information
     */
    public function getWarehouseWithCapacity($warehouseId = null)
    {
        $builder = $this->select('warehouses.*, 
                                 TRIM(CONCAT(COALESCE(employees.first_name, \'\'), \' \', COALESCE(employees.last_name, \'\'))) as in_charge_name,
                                 (warehouses.capacity_total - warehouses.capacity_used) as available_capacity,
                                 ROUND((warehouses.capacity_used / warehouses.capacity_total) * 100, 2) as utilization_percentage')
            ->join('employees', 'employees.id = warehouses.in_charge_id', 'left');
        
        if ($warehouseId) {
            $builder->where('warehouses.id', $warehouseId);
            return $builder->first();
        }
        
        return $builder->findAll();
    }

    /**
     * Get active warehouses
     */
    public function getActiveWarehouses()
    {
        return $this->where('status', 'active')
            ->orderBy('warehouse_name', 'ASC')
            ->findAll();
    }

    /**
     * Get warehouses with filters
     */
    public function getWarehouses($filters = [])
    {
        $builder = $this->select('warehouses.*, employees.first_name, employees.last_name')
                        ->join('employees', 'employees.id = warehouses.in_charge_id', 'left');
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('warehouses.warehouse_name', $filters['search'])
                ->orLike('warehouses.warehouse_code', $filters['search'])
                ->orLike('warehouses.city', $filters['search'])
                ->groupEnd();
        }
        
        if (!empty($filters['status'])) {
            $builder->where('warehouses.status', $filters['status']);
        }
        
        return $builder->orderBy('warehouses.warehouse_name', 'ASC')->findAll();
    }

    /**
     * Get warehouse utilization report
     */
    public function getUtilizationReport()
    {
        return $this->select('warehouses.*, 
                             TRIM(CONCAT(COALESCE(employees.first_name, \'\'), \' \', COALESCE(employees.last_name, \'\'))) as in_charge_name,
                             ROUND((warehouses.capacity_used / warehouses.capacity_total) * 100, 2) as utilization_percentage')
            ->join('employees', 'employees.id = warehouses.in_charge_id', 'left')
            ->orderBy('utilization_percentage', 'DESC')
            ->findAll();
    }

    /**
     * Check if warehouse has available capacity
     */
    public function hasAvailableCapacity($warehouseId, $requiredCapacity)
    {
        $warehouse = $this->find($warehouseId);
        if (!$warehouse) {
            return false;
        }
        
        $availableCapacity = $warehouse['capacity_total'] - $warehouse['capacity_used'];
        return $availableCapacity >= $requiredCapacity;
    }

    /**
     * Get warehouses by type
     */
    public function getWarehousesByType($type)
    {
        return $this->where('warehouse_type', $type)
            ->where('status', 'active')
            ->orderBy('warehouse_name', 'ASC')
            ->findAll();
    }

    /**
     * Get warehouse statistics
     */
    public function getWarehouseStats()
    {
        $total = $this->countAll();
        $active = $this->builder()->where('status', 'active')->countAllResults();
        $inactive = $this->builder()->where('status', 'inactive')->countAllResults();
        
        $stats = [
            // Keys for warehouse/index view
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            // Backward compatibility keys
            'total_warehouses' => $total,
            'active_warehouses' => $active,
            'total_capacity' => 0,
            'total_used_capacity' => 0,
            'average_utilization' => 0
        ];
        
        // Get capacity sums using proper query builder methods
        $capacityResult = $this->builder()->selectSum('capacity_total')->get()->getRowArray();
        $usedCapacityResult = $this->builder()->selectSum('capacity_used')->get()->getRowArray();
        
        $stats['total_capacity'] = $capacityResult['capacity_total'] ?? 0;
        $stats['total_used_capacity'] = $usedCapacityResult['capacity_used'] ?? 0;
        
        if ($stats['total_capacity'] > 0) {
            $stats['average_utilization'] = round(($stats['total_used_capacity'] / $stats['total_capacity']) * 100, 2);
        }
        
        return $stats;
    }

    /**
     * Search warehouses
     */
    public function searchWarehouses($search, $filters = [])
    {
        $builder = $this->select('warehouses.*, TRIM(CONCAT(COALESCE(employees.first_name, \'\'), \' \', COALESCE(employees.last_name, \'\'))) as in_charge_name')
            ->join('employees', 'employees.id = warehouses.in_charge_id', 'left');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('warehouse_name', $search)
                ->orLike('warehouse_code', $search)
                ->orLike('city', $search)
                ->orLike('contact_person', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['type'])) {
            $builder->where('warehouse_type', $filters['type']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['city'])) {
            $builder->where('city', $filters['city']);
        }
        
        return $builder->orderBy('warehouse_name', 'ASC')->findAll();
    }

    public function getAllActive()
    {
        return $this->getActiveWarehouses();
    }

    /**
     * Export warehouses to CSV
     */
    public function exportToCSV($filters = [])
    {
        $warehouses = $this->searchWarehouses('', $filters);
        
        $csv = "Warehouse Code,Warehouse Name,Type,Address,City,State,Pincode,Contact Person,Phone,Email,Total Capacity,Used Capacity,Available Capacity,Utilization %,In-Charge,Status\n";
        
        foreach ($warehouses as $warehouse) {
            $availableCapacity = $warehouse['capacity_total'] - $warehouse['capacity_used'];
            $utilization = $warehouse['capacity_total'] > 0 ? round(($warehouse['capacity_used'] / $warehouse['capacity_total']) * 100, 2) : 0;
            
            $csv .= "{$warehouse['warehouse_code']},{$warehouse['warehouse_name']},{$warehouse['warehouse_type']},{$warehouse['address']},{$warehouse['city']},{$warehouse['state']},{$warehouse['pincode']},{$warehouse['contact_person']},{$warehouse['phone']},{$warehouse['email']},{$warehouse['capacity_total']} {$warehouse['capacity_unit']},{$warehouse['capacity_used']} {$warehouse['capacity_unit']},{$availableCapacity} {$warehouse['capacity_unit']},{$utilization}%," . (isset($warehouse['in_charge_name']) ? $warehouse['in_charge_name'] : 'N/A') . ",{$warehouse['status']}\n";
        }
        
        return $csv;
    }
}
