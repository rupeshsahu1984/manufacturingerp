<?php

namespace App\Models;

use CodeIgniter\Model;

class StockTransfer extends Model
{
    protected $table = 'stock_transfers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'transfer_code',
        'transfer_date',
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'notes',
        'created_by',
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
        'from_warehouse_id' => 'required|integer|is_not_unique[warehouses.id]',
        'to_warehouse_id' => 'required|integer|is_not_unique[warehouses.id]|differs[from_warehouse_id]',
        'transfer_date' => 'required|valid_date',
        'status' => 'required|in_list[pending,in_transit,completed,cancelled]'
    ];

    protected $validationMessages = [
        'from_warehouse_id' => [
            'required' => 'Source warehouse is required',
            'differs' => 'Source and destination warehouses must be different'
        ],
        'to_warehouse_id' => [
            'required' => 'Destination warehouse is required',
            'differs' => 'Source and destination warehouses must be different'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['generateTransferNumber', 'setDefaults'];
    protected $beforeUpdate = ['updateTotals'];

    /**
     * Generate unique transfer number
     */
    protected function generateTransferNumber(array $data)
    {
        if (!isset($data['data']['transfer_code']) || empty($data['data']['transfer_code'])) {
            $data['data']['transfer_code'] = $this->generateUniqueCode();
        }
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'pending';
        }
        
        if (!isset($data['data']['transfer_date'])) {
            $data['data']['transfer_date'] = date('Y-m-d');
        }
        
        if (!isset($data['data']['created_by'])) {
            $data['data']['created_by'] = session()->get('user_id') ?? 1;
        }
        
        return $data;
    }

    /**
     * Update totals when items change (if needed)
     */
    protected function updateTotals(array $data)
    {
        // Totals are calculated separately, not stored in transfers table
        return $data;
    }

    /**
     * Generate unique transfer code
     */
    public function generateUniqueCode()
    {
        $prefix = 'ST';
        $year = date('Y');
        $month = date('m');
        
        // Get last code for this month
        $lastCode = $this->select('transfer_code')
            ->like('transfer_code', $prefix . $year . $month)
            ->orderBy('transfer_code', 'DESC')
            ->first();
        
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode['transfer_code'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get transfer with full details
     */
    public function getTransferWithDetails($transferId = null)
    {
        $builder = $this->select('stock_transfers.*, 
                                 source_warehouse.warehouse_name as source_warehouse_name,
                                 dest_warehouse.warehouse_name as destination_warehouse_name,
                                 creator.username as created_by_name')
            ->join('warehouses as source_warehouse', 'source_warehouse.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses as dest_warehouse', 'dest_warehouse.id = stock_transfers.to_warehouse_id', 'left')
            ->join('users as creator', 'creator.id = stock_transfers.created_by', 'left');
        
        if ($transferId) {
            $builder->where('stock_transfers.id', $transferId);
            return $builder->first();
        }
        
        return $builder->orderBy('stock_transfers.transfer_date', 'DESC')->findAll();
    }

    /**
     * Get transfers by status
     */
    public function getTransfersByStatus($status)
    {
        $builder = $this->select('stock_transfers.*, 
                                 source_warehouse.warehouse_name as source_warehouse_name,
                                 dest_warehouse.warehouse_name as destination_warehouse_name,
                                 creator.username as created_by_name')
            ->join('warehouses as source_warehouse', 'source_warehouse.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses as dest_warehouse', 'dest_warehouse.id = stock_transfers.to_warehouse_id', 'left')
            ->join('users as creator', 'creator.id = stock_transfers.created_by', 'left')
            ->where('stock_transfers.status', $status);
        
        return $builder->orderBy('stock_transfers.transfer_date', 'DESC')->findAll();
    }

    /**
     * Get pending transfers
     */
    public function getPendingTransfers()
    {
        return $this->whereIn('status', ['requested', 'approved'])
            ->orderBy('priority', 'DESC')
            ->orderBy('expected_delivery_date', 'ASC')
            ->findAll();
    }

    /**
     * Get transfers by warehouse
     */
    public function getTransfersByWarehouse($warehouseId, $direction = 'both')
    {
        // Build query directly instead of calling getTransferWithDetails() which returns array
        $builder = $this->select('stock_transfers.*, 
                                 source_warehouse.warehouse_name as source_warehouse_name,
                                 dest_warehouse.warehouse_name as destination_warehouse_name,
                                 creator.username as created_by_name')
            ->join('warehouses as source_warehouse', 'source_warehouse.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses as dest_warehouse', 'dest_warehouse.id = stock_transfers.to_warehouse_id', 'left')
            ->join('users as creator', 'creator.id = stock_transfers.created_by', 'left');
        
        if ($direction === 'outgoing') {
            $builder->where('stock_transfers.from_warehouse_id', $warehouseId);
        } elseif ($direction === 'incoming') {
            $builder->where('stock_transfers.to_warehouse_id', $warehouseId);
        } else {
            $builder->groupStart()
                ->where('stock_transfers.from_warehouse_id', $warehouseId)
                ->orWhere('stock_transfers.to_warehouse_id', $warehouseId)
                ->groupEnd();
        }
        
        return $builder->orderBy('stock_transfers.transfer_date', 'DESC')->findAll();
    }

    /**
     * Approve transfer
     */
    public function approveTransfer($transferId, $approvedBy)
    {
        $transfer = $this->find($transferId);
        if (!$transfer || $transfer['status'] !== 'requested') {
            return false;
        }
        
        // Check if items are available in source warehouse
        if (!$this->checkItemAvailability($transferId)) {
            return false;
        }
        
        return $this->update($transferId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Start transfer (mark as in transit)
     */
    public function startTransfer($transferId)
    {
        $transfer = $this->find($transferId);
        if (!$transfer || !in_array($transfer['status'], ['pending', 'draft'])) {
            return false;
        }
        
        // Deduct stock from source warehouse
        if (!$this->deductSourceStock($transferId)) {
            return false;
        }
        
        return $this->update($transferId, [
            'status' => 'in_transit',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Complete transfer (mark as received)
     */
    public function completeTransfer($transferId)
    {
        $transfer = $this->find($transferId);
        if (!$transfer || $transfer['status'] !== 'in_transit') {
            return false;
        }
        
        // Add stock to destination warehouse
        if (!$this->addDestinationStock($transferId)) {
            return false;
        }
        
        return $this->update($transferId, [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Check item availability in source warehouse
     */
    private function checkItemAvailability($transferId)
    {
        $transfer = $this->find($transferId);
        if (!$transfer) {
            return false;
        }
        
        $transferItemsModel = new StockTransferItem();
        $items = $transferItemsModel->where('transfer_id', $transferId)->findAll();
        
        $stockModel = new Stock();
        $fromWarehouseId = $transfer['from_warehouse_id'];
        
        foreach ($items as $item) {
            $availableStock = $stockModel->getItemStock($item['item_id'], $fromWarehouseId);
            if ($availableStock['available_stock'] < $item['quantity']) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Deduct stock from source warehouse
     */
    private function deductSourceStock($transferId)
    {
        $transfer = $this->find($transferId);
        if (!$transfer) {
            return false;
        }
        
        $transferItemsModel = new StockTransferItem();
        $items = $transferItemsModel->where('transfer_id', $transferId)->findAll();
        
        $stockModel = new Stock();
        $fromWarehouseId = $transfer['from_warehouse_id'];
        $transferCode = $transfer['transfer_code'] ?? 'ST-' . $transferId;
        
        foreach ($items as $item) {
            if (!$stockModel->removeStock($item['item_id'], $fromWarehouseId, $item['quantity'], 'available', $transferCode)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Add stock to destination warehouse
     */
    private function addDestinationStock($transferId)
    {
        $transfer = $this->find($transferId);
        if (!$transfer) {
            return false;
        }
        
        $transferItemsModel = new StockTransferItem();
        $items = $transferItemsModel->where('transfer_id', $transferId)->findAll();
        
        $stockModel = new Stock();
        $toWarehouseId = $transfer['to_warehouse_id'];
        $transferCode = $transfer['transfer_code'] ?? 'ST-' . $transferId;
        
        foreach ($items as $item) {
            $stockData = [
                'item_id' => $item['item_id'],
                'warehouse_id' => $toWarehouseId,
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_price'] ?? 0,
                'total_cost' => ($item['quantity'] * ($item['unit_price'] ?? 0)),
                'status' => 'available',
                'transaction_type' => 'in',
                'transaction_date' => date('Y-m-d'),
                'source_document' => $transferCode,
                'source_type' => 'transfer',
                'notes' => 'Stock transfer #' . $transferCode
            ];
            
            $db = \Config\Database::connect();
            if (!$db->table('stock')->insert($stockData)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Calculate transfer totals
     */
    public function calculateTransferTotals($transferId)
    {
        $transferItemsModel = new StockTransferItem();
        $items = $transferItemsModel->where('transfer_id', $transferId)->findAll();
        
        $totalItems = 0;
        $totalValue = 0;
        
        foreach ($items as $item) {
            $totalItems += $item['quantity'];
            $totalValue += $item['quantity'] * ($item['unit_price'] ?? 0);
        }
        
        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue
        ];
    }

    /**
     * Get transfer statistics
     */
    public function getTransferStats()
    {
        $stats = [
            'total_transfers' => $this->countAll(),
            'pending_transfers' => $this->where('status', 'pending')->countAllResults(),
            'in_transit' => $this->where('status', 'in_transit')->countAllResults(),
            'completed_today' => $this->where('status', 'completed')
                ->where('updated_at >=', date('Y-m-d') . ' 00:00:00')
                ->countAllResults(),
            'overdue_transfers' => $this->where('status', 'in_transit')
                ->where('transfer_date <', date('Y-m-d'))
                ->countAllResults()
        ];
        
        return $stats;
    }

    /**
     * Search transfers
     */
    public function searchTransfers($search, $filters = [])
    {
        // Build query directly instead of calling getTransferWithDetails() which returns array
        $builder = $this->select('stock_transfers.*, 
                                 source_warehouse.warehouse_name as source_warehouse_name,
                                 dest_warehouse.warehouse_name as destination_warehouse_name,
                                 creator.username as created_by_name')
            ->join('warehouses as source_warehouse', 'source_warehouse.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses as dest_warehouse', 'dest_warehouse.id = stock_transfers.to_warehouse_id', 'left')
            ->join('users as creator', 'creator.id = stock_transfers.created_by', 'left');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('stock_transfers.transfer_code', $search)
                ->orLike('source_warehouse.warehouse_name', $search)
                ->orLike('dest_warehouse.warehouse_name', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['status'])) {
            $builder->where('stock_transfers.status', $filters['status']);
        }
        
        if (!empty($filters['source_warehouse'])) {
            $builder->where('stock_transfers.from_warehouse_id', $filters['source_warehouse']);
        }
        
        if (!empty($filters['destination_warehouse'])) {
            $builder->where('stock_transfers.to_warehouse_id', $filters['destination_warehouse']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('stock_transfers.transfer_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('stock_transfers.transfer_date <=', $filters['date_to']);
        }
        
        return $builder->orderBy('stock_transfers.transfer_date', 'DESC')->findAll();
    }

    /**
     * Export transfers to CSV
     */
    public function exportToCSV($filters = [])
    {
        $transfers = $this->searchTransfers('', $filters);
        
        $csv = "Transfer Code,Transfer Date,Source Warehouse,Destination Warehouse,Created By,Status,Notes\n";
        
        foreach ($transfers as $transfer) {
            $notes = isset($transfer['notes']) ? $transfer['notes'] : 'N/A';
            $createdBy = isset($transfer['created_by_name']) ? $transfer['created_by_name'] : 'N/A';
            $csv .= "{$transfer['transfer_code']},{$transfer['transfer_date']},{$transfer['source_warehouse_name']},{$transfer['destination_warehouse_name']},{$createdBy},{$transfer['status']},{$notes}\n";
        }
        
        return $csv;
    }
    
    /**
     * Get transfer items (delegate to StockTransferItem model)
     */
    public function getTransferItems($transferId)
    {
        $transferItemModel = new StockTransferItem();
        return $transferItemModel->where('transfer_id', $transferId)->findAll();
    }
}
