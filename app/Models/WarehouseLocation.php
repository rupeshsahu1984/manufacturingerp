<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseLocation extends Model
{
    protected $table = 'warehouse_locations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'warehouse_id',
        'location_code',
        'location_name',
        'location_type',
        'parent_location_id',
        'rack_number',
        'bin_number',
        'shelf_number',
        'capacity',
        'capacity_unit',
        'current_utilization',
        'status',
        'description',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'warehouse_id' => 'required|integer',
        'location_code' => 'required|is_unique[warehouse_locations.location_code,id,{id}]',
        'location_name' => 'required|min_length[2]',
        'location_type' => 'required|in_list[area,rack,bin,shelf,zone]',
        'status' => 'required|in_list[active,inactive,maintenance]'
    ];

    protected $validationMessages = [
        'warehouse_id' => [
            'required' => 'Warehouse is required',
            'integer' => 'Invalid warehouse ID'
        ],
        'location_code' => [
            'required' => 'Location code is required',
            'is_unique' => 'Location code must be unique'
        ],
        'location_name' => [
            'required' => 'Location name is required',
            'min_length' => 'Location name must be at least 2 characters'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function parentLocation()
    {
        return $this->belongsTo('App\Models\WarehouseLocation', 'parent_location_id', 'id');
    }

    public function subLocations()
    {
        return $this->hasMany('App\Models\WarehouseLocation', 'parent_location_id', 'id');
    }

    public function currentStock()
    {
        return $this->hasMany('App\Models\CurrentStock', 'location_id', 'id');
    }

    public function stockMovements()
    {
        return $this->hasMany('App\Models\StockMovement', 'location_id', 'id');
    }

    // Methods
    public function getWithRelations($id = null)
    {
        $builder = $this->select('warehouse_locations.*, warehouses.warehouse_name, warehouses.warehouse_code, parent.location_name as parent_location_name')
                        ->join('warehouses', 'warehouses.id = warehouse_locations.warehouse_id')
                        ->join('warehouse_locations parent', 'parent.id = warehouse_locations.parent_location_id', 'left');

        if ($id) {
            return $builder->where('warehouse_locations.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->orderBy('location_type', 'ASC')
                    ->orderBy('location_name', 'ASC')
                    ->findAll();
    }

    public function getByType($warehouseId, $type)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->where('location_type', $type)
                    ->orderBy('location_name', 'ASC')
                    ->findAll();
    }

    public function getHierarchy($warehouseId)
    {
        $locations = $this->where('warehouse_id', $warehouseId)
                          ->where('parent_location_id IS NULL')
                          ->findAll();
        
        $hierarchy = [];

        foreach ($locations as $location) {
            $location['sub_locations'] = $this->getSubLocations($location['id']);
            $hierarchy[] = $location;
        }

        return $hierarchy;
    }

    public function getSubLocations($parentId)
    {
        $subLocations = $this->where('parent_location_id', $parentId)->findAll();
        
        foreach ($subLocations as &$subLocation) {
            $subLocation['sub_locations'] = $this->getSubLocations($subLocation['id']);
        }

        return $subLocations;
    }

    public function getRacksByWarehouse($warehouseId)
    {
        return $this->where('warehouse_id', $warehouseId)
                    ->where('location_type', 'rack')
                    ->orderBy('rack_number', 'ASC')
                    ->findAll();
    }

    public function getBinsByRack($rackId)
    {
        return $this->where('parent_location_id', $rackId)
                    ->where('location_type', 'bin')
                    ->orderBy('bin_number', 'ASC')
                    ->findAll();
    }

    public function getShelvesByRack($rackId)
    {
        return $this->where('parent_location_id', $rackId)
                    ->where('location_type', 'shelf')
                    ->orderBy('shelf_number', 'ASC')
                    ->findAll();
    }

    public function getLocationStats($locationId)
    {
        $stats = [
            'total_items' => 0,
            'total_value' => 0,
            'utilization_percentage' => 0,
            'low_stock_items' => 0
        ];

        // Get current stock statistics
        $currentStock = model('CurrentStock')->where('location_id', $locationId)->findAll();
        
        foreach ($currentStock as $stock) {
            $stats['total_items']++;
            $stats['total_value'] += ($stock['quantity'] * $stock['unit_cost']);
            
            if ($stock['quantity'] <= $stock['reorder_level']) {
                $stats['low_stock_items']++;
            }
        }

        // Calculate utilization percentage
        $location = $this->find($locationId);
        if ($location && $location['capacity'] > 0) {
            $stats['utilization_percentage'] = ($stats['total_items'] / $location['capacity']) * 100;
        }

        return $stats;
    }

    public function getLocationUtilization($locationId)
    {
        $location = $this->find($locationId);
        if (!$location) {
            return 0;
        }

        $currentStock = model('CurrentStock')->where('location_id', $locationId)->countAllResults();
        
        if ($location['capacity'] > 0) {
            return ($currentStock / $location['capacity']) * 100;
        }

        return 0;
    }

    public function generateLocationCode($warehouseId, $type)
    {
        $warehouse = model('Warehouse')->find($warehouseId);
        if (!$warehouse) {
            return false;
        }

        $prefix = strtoupper(substr($type, 0, 1));
        $warehouseCode = $warehouse['warehouse_code'];
        
        $lastLocation = $this->where('warehouse_id', $warehouseId)
                             ->where('location_type', $type)
                             ->where('location_code LIKE', $warehouseCode . $prefix . '%')
                             ->orderBy('location_code', 'DESC')
                             ->first();

        if ($lastLocation) {
            $lastNumber = intval(substr($lastLocation['location_code'], -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $warehouseCode . $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function updateUtilization($locationId)
    {
        $utilization = $this->getLocationUtilization($locationId);
        return $this->update($locationId, ['current_utilization' => $utilization]);
    }

    public function getLocationTypes()
    {
        return [
            'area' => 'Area',
            'rack' => 'Rack',
            'bin' => 'Bin',
            'shelf' => 'Shelf',
            'zone' => 'Zone'
        ];
    }

    public function getLocationStatuses()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Under Maintenance'
        ];
    }

    public function getAvailableLocations($warehouseId, $type = null)
    {
        $builder = $this->where('warehouse_id', $warehouseId)
                        ->where('status', 'active');

        if ($type) {
            $builder->where('location_type', $type);
        }

        return $builder->where('current_utilization < capacity OR capacity IS NULL')
                      ->orderBy('location_name', 'ASC')
                      ->findAll();
    }

    public function getFullLocations($warehouseId, $type = null)
    {
        $builder = $this->where('warehouse_id', $warehouseId)
                        ->where('status', 'active');

        if ($type) {
            $builder->where('location_type', $type);
        }

        return $builder->where('current_utilization >= capacity')
                      ->where('capacity IS NOT NULL')
                      ->orderBy('location_name', 'ASC')
                      ->findAll();
    }
}
