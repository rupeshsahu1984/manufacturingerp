<?php

namespace App\Models;

use CodeIgniter\Model;

class GateEntry extends Model
{
    protected $table = 'gate_entries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'entry_number',
        'entry_type',
        'vehicle_id',
        'driver_id',
        'supplier_id',
        'customer_id',
        'purchase_bill_id',
        'sales_order_id',
        'purpose',
        'remarks',
        'entry_time',
        'exit_time',
        'status',
        'created_by',
        'updated_by',
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
        'entry_number' => 'required|max_length[20]|is_unique[gate_entries.entry_number,id,{id}]',
        'entry_type' => 'required|in_list[in,out]',
        'vehicle_id' => 'required|integer',
        'driver_id' => 'required|integer',
        'supplier_id' => 'permit_empty|integer',
        'customer_id' => 'permit_empty|integer',
        'purchase_bill_id' => 'permit_empty|integer',
        'sales_order_id' => 'permit_empty|integer',
        'purpose' => 'required|max_length[255]',
        'remarks' => 'permit_empty|max_length[1000]',
        'entry_time' => 'required|valid_date',
        'exit_time' => 'permit_empty|valid_date',
        'status' => 'required|in_list[active,completed,cancelled]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'entry_number' => [
            'required' => 'Entry number is required.',
            'max_length' => 'Entry number cannot exceed 20 characters.',
            'is_unique' => 'Entry number must be unique.'
        ],
        'entry_type' => [
            'required' => 'Entry type is required.',
            'in_list' => 'Entry type must be either "in" or "out".'
        ],
        'vehicle_id' => [
            'required' => 'Vehicle is required.',
            'integer' => 'Vehicle ID must be a valid integer.'
        ],
        'driver_id' => [
            'required' => 'Driver is required.',
            'integer' => 'Driver ID must be a valid integer.'
        ],
        'purpose' => [
            'required' => 'Purpose is required.',
            'max_length' => 'Purpose cannot exceed 255 characters.'
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list' => 'Status must be active, completed, or cancelled.'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy'];

    protected function setCreatedBy(array $data)
    {
        if (!isset($data['data']['created_by'])) {
            $data['data']['created_by'] = session()->get('user_id');
        }
        return $data;
    }

    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }

    // Helper methods
    public function generateEntryNumber()
    {
        $prefix = 'GE';
        $year = date('Y');
        $month = date('m');
        
        // Get the last entry number for this month
        $lastEntry = $this->where('entry_number LIKE', $prefix . $year . $month . '%')
                         ->orderBy('entry_number', 'DESC')
                         ->first();
        
        if ($lastEntry) {
            $lastNumber = intval(substr($lastEntry['entry_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getGateEntriesWithDetails()
    {
        return $this->select('
                gate_entries.*,
                v.vehicle_number,
                v.vehicle_type,
                d.driver_name,
                d.phone as driver_phone,
                s.supplier_name,
                c.customer_name,
                pb.bill_number as purchase_bill_number,
                so.so_number as sales_order_number,
                u1.name as created_by_name,
                u2.name as updated_by_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->join('purchase_bills pb', 'pb.id = gate_entries.purchase_bill_id', 'left')
            ->join('sales_orders so', 'so.id = gate_entries.sales_order_id', 'left')
            ->join('users u1', 'u1.id = gate_entries.created_by', 'left')
            ->join('users u2', 'u2.id = gate_entries.updated_by', 'left')
            ->orderBy('gate_entries.entry_time', 'DESC')
            ->findAll();
    }

    public function getGateEntryWithDetails($id)
    {
        $entry = $this->select('
                gate_entries.*,
                v.vehicle_number,
                v.vehicle_type,
                v.registration_number,
                d.driver_name,
                d.phone as driver_phone,
                d.license_number,
                s.supplier_name,
                s.supplier_code,
                c.customer_name,
                c.customer_code,
                pb.bill_number as purchase_bill_number,
                pb.bill_date as purchase_bill_date,
                so.so_number as sales_order_number,
                so.order_date as sales_order_date,
                u1.name as created_by_name,
                u2.name as updated_by_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->join('purchase_bills pb', 'pb.id = gate_entries.purchase_bill_id', 'left')
            ->join('sales_orders so', 'so.id = gate_entries.sales_order_id', 'left')
            ->join('users u1', 'u1.id = gate_entries.created_by', 'left')
            ->join('users u2', 'u2.id = gate_entries.updated_by', 'left')
            ->find($id);

        if ($entry) {
            // Get items for this entry
            $gateEntryItemModel = new GateEntryItem();
            $entry['items'] = $gateEntryItemModel->getItemsWithProductDetails($id);
        }

        return $entry;
    }

    public function getGateEntriesByDateRange($startDate, $endDate, $type = null)
    {
        $builder = $this->select('
                gate_entries.*,
                v.vehicle_number,
                d.driver_name,
                s.supplier_name,
                c.customer_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->where('DATE(entry_time) >=', $startDate)
            ->where('DATE(entry_time) <=', $endDate);

        if ($type) {
            $builder->where('entry_type', $type);
        }

        return $builder->orderBy('entry_time', 'DESC')->findAll();
    }

    public function getGateEntryStats($dateRange = 'today')
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        $thisYear = date('Y');

        switch ($dateRange) {
            case 'today':
                $whereClause = "DATE(entry_time) = '$today'";
                break;
            case 'month':
                $whereClause = "DATE_FORMAT(entry_time, '%Y-%m') = '$thisMonth'";
                break;
            case 'year':
                $whereClause = "YEAR(entry_time) = '$thisYear'";
                break;
            default:
                $whereClause = "DATE(entry_time) = '$today'";
        }

        $stats = [
            'total_entries' => $this->where($whereClause)->countAllResults(),
            'in_entries' => $this->where($whereClause)->where('entry_type', 'in')->countAllResults(),
            'out_entries' => $this->where($whereClause)->where('entry_type', 'out')->countAllResults(),
            'active_entries' => $this->where($whereClause)->where('status', 'active')->countAllResults(),
            'completed_entries' => $this->where($whereClause)->where('status', 'completed')->countAllResults()
        ];

        return $stats;
    }

    public function getRecentEntries($limit = 10)
    {
        return $this->select('
                gate_entries.entry_number,
                gate_entries.entry_type,
                gate_entries.purpose,
                gate_entries.entry_time,
                gate_entries.status,
                v.vehicle_number,
                d.driver_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->orderBy('entry_time', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getPendingEntries()
    {
        return $this->select('
                gate_entries.*,
                v.vehicle_number,
                d.driver_name,
                s.supplier_name,
                c.customer_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->where('gate_entries.status', 'active')
            ->orderBy('entry_time', 'ASC')
            ->findAll();
    }

    public function getEntriesByVehicle($vehicleId, $limit = 50)
    {
        return $this->select('
                gate_entries.*,
                d.driver_name,
                s.supplier_name,
                c.customer_name
            ')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->where('gate_entries.vehicle_id', $vehicleId)
            ->orderBy('entry_time', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getEntriesByDriver($driverId, $limit = 50)
    {
        return $this->select('
                gate_entries.*,
                v.vehicle_number,
                s.supplier_name,
                c.customer_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->where('gate_entries.driver_id', $driverId)
            ->orderBy('entry_time', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getEntriesBySupplier($supplierId, $limit = 50)
    {
        return $this->select('
                gate_entries.*,
                v.vehicle_number,
                d.driver_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->where('gate_entries.supplier_id', $supplierId)
            ->orderBy('entry_time', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getEntriesByCustomer($customerId, $limit = 50)
    {
        return $this->select('
                gate_entries.*,
                v.vehicle_number,
                d.driver_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->where('gate_entries.customer_id', $customerId)
            ->orderBy('entry_time', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function searchEntries($searchTerm, $filters = [])
    {
        $builder = $this->select('
                gate_entries.*,
                v.vehicle_number,
                d.driver_name,
                s.supplier_name,
                c.customer_name
            ')
            ->join('vehicles v', 'v.id = gate_entries.vehicle_id', 'left')
            ->join('drivers d', 'd.id = gate_entries.driver_id', 'left')
            ->join('suppliers s', 's.id = gate_entries.supplier_id', 'left')
            ->join('customers c', 'c.id = gate_entries.customer_id', 'left')
            ->groupStart()
                ->like('gate_entries.entry_number', $searchTerm)
                ->orLike('v.vehicle_number', $searchTerm)
                ->orLike('d.driver_name', $searchTerm)
                ->orLike('s.supplier_name', $searchTerm)
                ->orLike('c.customer_name', $searchTerm)
                ->orLike('gate_entries.purpose', $searchTerm)
            ->groupEnd();

        // Apply filters
        if (isset($filters['entry_type'])) {
            $builder->where('gate_entries.entry_type', $filters['entry_type']);
        }
        if (isset($filters['status'])) {
            $builder->where('gate_entries.status', $filters['status']);
        }
        if (isset($filters['start_date'])) {
            $builder->where('DATE(gate_entries.entry_time) >=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $builder->where('DATE(gate_entries.entry_time) <=', $filters['end_date']);
        }

        return $builder->orderBy('gate_entries.entry_time', 'DESC')->findAll();
    }
}
