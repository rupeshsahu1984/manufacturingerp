<?php

namespace App\Models;

use CodeIgniter\Model;

class DispatchNote extends Model
{
    protected $table = 'dispatch_notes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'dispatch_number', 'so_id', 'dispatch_date', 'vehicle_number',
        'driver_name', 'driver_phone', 'courier_company', 'lr_awb_number',
        'delivery_address', 'status', 'notes', 'created_by', 'updated_by',
        'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'so_id' => 'required|integer',
        'dispatch_date' => 'required|valid_date',
        'vehicle_number' => 'required|min_length[3]|max_length[20]',
        'driver_name' => 'required|min_length[2]|max_length[100]',
        'status' => 'required|in_list[draft,dispatched,delivered,cancelled]'
    ];

    protected $validationMessages = [
        'order_id' => [
            'required' => 'Sales order is required',
            'integer' => 'Please select a valid sales order'
        ],
        'dispatch_date' => [
            'required' => 'Dispatch date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'vehicle_number' => [
            'required' => 'Vehicle number is required',
            'min_length' => 'Vehicle number must be at least 3 characters',
            'max_length' => 'Vehicle number cannot exceed 20 characters'
        ],
        'driver_name' => [
            'required' => 'Driver name is required',
            'min_length' => 'Driver name must be at least 2 characters',
            'max_length' => 'Driver name cannot exceed 100 characters'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Please select a valid status'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateDispatchNumber'];
    protected $beforeUpdate = [];

    /**
     * Generate unique dispatch number
     */
    protected function generateDispatchNumber(array $data)
    {
        if (!isset($data['data']['dispatch_number']) || empty($data['data']['dispatch_number'])) {
            $data['data']['dispatch_number'] = $this->generateUniqueCode();
        }
        return $data;
    }

    /**
     * Generate unique dispatch number
     */
    public function generateUniqueCode()
    {
        $prefix = 'DN';
        $year = date('Y');
        $month = date('m');
        $numberField = $this->db->fieldExists('dn_number', $this->table) ? 'dn_number' : 'dispatch_number';
        
        // Get the last dispatch number for this month
        $lastDispatch = $this->where($numberField . ' LIKE', $prefix . $year . $month . '%')
                           ->orderBy($numberField, 'DESC')
                           ->first();
        
        if ($lastDispatch) {
            $lastNumber = intval(substr($lastDispatch[$numberField], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function generateUniqueDnNumber()
    {
        return $this->generateUniqueCode();
    }

    /**
     * Get dispatch notes with details
     */
    public function getDispatchNotesWithDetails($filters = [])
    {
        $builder = $this->db->table('dispatch_notes dn')
                           ->select('dn.*, so.so_number, c.customer_name, c.contact_person, c.phone')
                           ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                           ->join('customers c', 'c.id = so.customer_id', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('dn.status', $filters['status']);
        }
        if (!empty($filters['order'])) {
            $builder->where('dn.so_id', $filters['order']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('dn.dispatch_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('dn.dispatch_date <=', $filters['date_to']);
        }

        return $builder->orderBy('dn.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get dispatch note with details
     */
    public function getDispatchById($id)
    {
        $numberField = $this->db->fieldExists('dn_number', $this->table) ? 'dn.dn_number' : 'dn.dispatch_number';

        return $this->db->table('dispatch_notes dn')
                       ->select("dn.*, {$numberField} as dispatch_number, so.so_number, so.order_date, so.delivery_date, c.customer_name, c.contact_person, c.phone, c.address, c.city, c.state, c.pincode", false)
                       ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->where('dn.id', $id)
                       ->get()
                       ->getRowArray();
    }

    public function getDispatchNoteWithDetails($id)
    {
        return $this->getDispatchById($id);
    }

    /**
     * Create dispatch note
     */
    public function createDispatch($data)
    {
        return $this->insert($data);
    }

    /**
     * Update dispatch note
     */
    public function updateDispatch($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Update dispatch status
     */
    public function updateStatus($id, $status, $userId = null)
    {
        $data = ['status' => $status];
        if ($userId) {
            $data['updated_by'] = $userId;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Get ready orders for dispatch
     */
    public function getReadyOrders()
    {
        return $this->db->table('sales_orders so')
                       ->select('so.*, c.customer_name')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->where('so.status', 'confirmed')
                       ->where('so.id NOT IN (SELECT so_id FROM dispatch_notes WHERE status != "cancelled")')
                       ->orderBy('so.delivery_date', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get delivered dispatches
     */
    public function getDeliveredDispatches()
    {
        return $this->db->table('dispatch_notes dn')
                       ->select('dn.*, so.so_number, c.customer_name')
                       ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->where('dn.status', 'delivered')
                       ->where('dn.id NOT IN (SELECT dispatch_id FROM invoices WHERE status != "cancelled")')
                       ->orderBy('dn.dispatch_date', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get dispatch statistics
     */
    public function getDispatchStats()
    {
        $stats = [
            'total_dispatches' => $this->countAll(),
            'draft_dispatches' => $this->where('status', 'draft')->countAllResults(),
            'dispatched' => $this->where('status', 'dispatched')->countAllResults(),
            'delivered' => $this->where('status', 'delivered')->countAllResults(),
            'cancelled' => $this->where('status', 'cancelled')->countAllResults()
        ];

        // Calculate delivery rate
        $totalDispatches = $stats['total_dispatches'];
        if ($totalDispatches > 0) {
            $stats['delivery_rate'] = round(($stats['delivered'] / $totalDispatches) * 100, 2);
        } else {
            $stats['delivery_rate'] = 0;
        }

        return $stats;
    }

    /**
     * Alias for getDispatchStats
     */
    public function getDispatchNoteStats()
    {
        return $this->getDispatchStats();
    }

    /**
     * Get dispatches by status
     */
    public function getDispatchesByStatus()
    {
        return $this->select('status, COUNT(*) as count')
                   ->groupBy('status')
                   ->get()
                   ->getResultArray();
    }

    /**
     * Get dispatches by vehicle
     */
    public function getDispatchesByVehicle()
    {
        return $this->select('vehicle_number, COUNT(*) as dispatch_count')
                   ->groupBy('vehicle_number')
                   ->orderBy('dispatch_count', 'DESC')
                   ->get()
                   ->getResultArray();
    }

    /**
     * Get dispatches by driver
     */
    public function getDispatchesByDriver()
    {
        return $this->select('driver_name, COUNT(*) as dispatch_count')
                   ->groupBy('driver_name')
                   ->orderBy('dispatch_count', 'DESC')
                   ->get()
                   ->getResultArray();
    }

    /**
     * Get recent dispatches
     */
    public function getRecentDispatches($limit = 10)
    {
        return $this->db->table('dispatch_notes dn')
                       ->select('dn.*, so.so_number, c.customer_name')
                       ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->orderBy('dn.created_at', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get pending deliveries
     */
    public function getPendingDeliveries()
    {
        return $this->db->table('dispatch_notes dn')
                       ->select('dn.*, so.so_number, so.delivery_date, c.customer_name, c.contact_person, c.phone')
                       ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->where('dn.status', 'dispatched')
                       ->where('so.delivery_date <=', date('Y-m-d', strtotime('+3 days')))
                       ->orderBy('so.delivery_date', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get overdue deliveries
     */
    public function getOverdueDeliveries()
    {
        return $this->db->table('dispatch_notes dn')
                       ->select('dn.*, so.so_number, so.delivery_date, c.customer_name, c.contact_person, c.phone')
                       ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->where('dn.status', 'dispatched')
                       ->where('so.delivery_date <', date('Y-m-d'))
                       ->orderBy('so.delivery_date', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Search dispatch notes
     */
    public function searchDispatchNotes($searchTerm)
    {
        return $this->db->table('dispatch_notes dn')
                       ->select('dn.*, so.so_number, c.customer_name')
                       ->join('sales_orders so', 'so.id = dn.so_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->groupStart()
                       ->like('dn.dispatch_number', $searchTerm)
                       ->orLike('so.so_number', $searchTerm)
                       ->orLike('c.customer_name', $searchTerm)
                       ->orLike('dn.vehicle_number', $searchTerm)
                       ->orLike('dn.driver_name', $searchTerm)
                       ->groupEnd()
                       ->orderBy('dn.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Export dispatch notes to CSV
     */
    public function exportToCSV($filters = [])
    {
        $dispatches = $this->getDispatchWithDetails($filters);
        
        $filename = 'dispatch_notes_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = WRITEPATH . 'uploads/' . $filename;
        
        $fp = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($fp, [
            'Dispatch Number', 'Sales Order Number', 'Customer Name', 'Contact Person', 'Phone',
            'Dispatch Date', 'Vehicle Number', 'Driver Name', 'Driver Phone', 'Courier Company',
            'LR/AWB Number', 'Status', 'Notes', 'Created Date'
        ]);
        
        // Write data
        foreach ($dispatches as $dispatch) {
            fputcsv($fp, [
                $dispatch['dispatch_number'],
                $dispatch['so_number'],
                $dispatch['customer_name'],
                $dispatch['contact_person'],
                $dispatch['phone'],
                $dispatch['dispatch_date'],
                $dispatch['vehicle_number'],
                $dispatch['driver_name'],
                $dispatch['driver_phone'],
                $dispatch['courier_company'],
                $dispatch['lr_awb_number'],
                $dispatch['status'],
                $dispatch['notes'],
                $dispatch['created_at']
            ]);
        }
        
        fclose($fp);
        
        return $filepath;
    }

    /**
     * Get dispatch items
     */
    public function getDispatchItems($dispatchId)
    {
        return model('DispatchItem')->getByDispatch($dispatchId);
    }

    /**
     * Calculate dispatch totals
     */
    public function calculateTotals($dispatchId)
    {
        $items = $this->getDispatchItems($dispatchId);
        
        $totalQuantity = 0;
        $totalAmount = 0;
        
        foreach ($items as $item) {
            $totalQuantity += $item['quantity'];
            $totalAmount += $item['total_amount'];
        }
        
        return [
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Update dispatch totals
     */
    public function updateTotals($dispatchId)
    {
        $totals = $this->calculateTotals($dispatchId);
        return $this->update($dispatchId, $totals);
    }
}
