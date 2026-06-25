<?php

namespace App\Models;

use CodeIgniter\Model;

class StockTransferItem extends Model
{
    protected $table = 'stock_transfer_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'transfer_id',
        'item_id',
        'quantity',
        'unit_price',
        'notes'
    ];

    // Dates
    protected $useTimestamps = false; // Table doesn't have created_at/updated_at

    // Validation
    protected $validationRules = [
        'transfer_id' => 'required|integer|is_not_unique[stock_transfers.id]',
        'item_id' => 'required|integer|is_not_unique[items.id]',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'permit_empty|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'transfer_id' => [
            'required' => 'Transfer ID is required',
            'integer' => 'Transfer ID must be a number',
            'is_not_unique' => 'Selected transfer does not exist'
        ],
        'item_id' => [
            'required' => 'Item is required',
            'integer' => 'Item ID must be a number',
            'is_not_unique' => 'Selected item does not exist'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['calculateTotalCost'];
    protected $beforeUpdate = ['calculateTotalCost'];

    /**
     * Calculate total cost (not needed as table doesn't have total_cost column)
     */
    protected function calculateTotalCost(array $data)
    {
        // Table doesn't have total_cost column, so no calculation needed
        return $data;
    }

    /**
     * Get transfer items with details
     */
    public function getTransferItems($transferId)
    {
        return $this->select('stock_transfer_items.*, 
                             items.item_name,
                             items.item_code,
                             items.unit_of_measure as uom,
                             source_warehouse.warehouse_name as source_warehouse_name,
                             dest_warehouse.warehouse_name as destination_warehouse_name')
            ->join('items', 'items.id = stock_transfer_items.item_id', 'left')
            ->join('stock_transfers', 'stock_transfers.id = stock_transfer_items.transfer_id', 'left')
            ->join('warehouses as source_warehouse', 'source_warehouse.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses as dest_warehouse', 'dest_warehouse.id = stock_transfers.to_warehouse_id', 'left')
            ->where('stock_transfer_items.transfer_id', $transferId)
            ->orderBy('stock_transfer_items.id', 'ASC')
            ->findAll();
    }

    /**
     * Get item transfer history
     */
    public function getItemTransferHistory($itemId, $warehouseId = null)
    {
        $builder = $this->select('stock_transfer_items.*, 
                                 stock_transfers.transfer_code,
                                 stock_transfers.transfer_date,
                                 stock_transfers.status as transfer_status,
                                 source_warehouse.warehouse_name as source_warehouse_name,
                                 dest_warehouse.warehouse_name as destination_warehouse_name')
            ->join('stock_transfers', 'stock_transfers.id = stock_transfer_items.transfer_id', 'left')
            ->join('warehouses as source_warehouse', 'source_warehouse.id = stock_transfers.from_warehouse_id', 'left')
            ->join('warehouses as dest_warehouse', 'dest_warehouse.id = stock_transfers.to_warehouse_id', 'left')
            ->where('stock_transfer_items.item_id', $itemId)
            ->orderBy('stock_transfers.transfer_date', 'DESC');
        
        if ($warehouseId) {
            $builder->groupStart()
                ->where('stock_transfers.from_warehouse_id', $warehouseId)
                ->orWhere('stock_transfers.to_warehouse_id', $warehouseId)
                ->groupEnd();
        }
        
        return $builder->findAll();
    }

    /**
     * Validate item availability for transfer
     */
    public function validateItemAvailability($itemId, $sourceWarehouseId, $quantity)
    {
        $stockModel = new Stock();
        $availableStock = $stockModel->getItemStock($itemId, $sourceWarehouseId);
        
        return isset($availableStock['available_stock']) && $availableStock['available_stock'] >= $quantity;
    }

    /**
     * Get transfer summary
     */
    public function getTransferSummary($transferId)
    {
        $items = $this->getTransferItems($transferId);
        
        $summary = [
            'total_items' => count($items),
            'total_quantity' => 0,
            'total_value' => 0,
            'items' => $items
        ];
        
        foreach ($items as $item) {
            $summary['total_quantity'] += $item['quantity'];
            $summary['total_value'] += $item['quantity'] * ($item['unit_price'] ?? 0);
        }
        
        return $summary;
    }

    /**
     * Export transfer items to CSV
     */
    public function exportToCSV($transferId)
    {
        $items = $this->getTransferItems($transferId);
        
        $csv = "Item Code,Item Name,UOM,Source Warehouse,Destination Warehouse,Quantity,Unit Cost,Total Cost,Batch Number,Expiry Date,Notes\n";
        
        foreach ($items as $item) {
            $batchNumber = isset($item['batch_number']) ? $item['batch_number'] : 'N/A';
            $expiryDate = isset($item['expiry_date']) ? $item['expiry_date'] : 'N/A';
            $notes = isset($item['notes']) ? $item['notes'] : 'N/A';
            
            $csv .= "{$item['item_code']},{$item['item_name']},{$item['uom']},{$item['source_warehouse_name']},{$item['destination_warehouse_name']},{$item['quantity']},{$item['unit_cost']},{$item['total_cost']},{$batchNumber},{$expiryDate},{$notes}\n";
        }
        
        return $csv;
    }
}
