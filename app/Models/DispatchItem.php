<?php

namespace App\Models;

use CodeIgniter\Model;

class DispatchItem extends Model
{
    protected $table = 'dispatch_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'dispatch_id', 'product_id', 'quantity', 'unit_price', 'total_amount',
        'notes', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'dispatch_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'dispatch_id' => [
            'required' => 'Dispatch ID is required',
            'integer' => 'Invalid dispatch ID'
        ],
        'product_id' => [
            'required' => 'Product is required',
            'integer' => 'Please select a valid product'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'numeric' => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'unit_price' => [
            'required' => 'Unit price is required',
            'numeric' => 'Unit price must be a number',
            'greater_than_equal_to' => 'Unit price must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['calculateTotalAmount'];
    protected $beforeUpdate = ['calculateTotalAmount'];

    /**
     * Calculate total amount for item
     */
    protected function calculateTotalAmount(array $data)
    {
        if (isset($data['data']['quantity']) && isset($data['data']['unit_price'])) {
            $quantity = $data['data']['quantity'];
            $unitPrice = $data['data']['unit_price'];
            $data['data']['total_amount'] = $quantity * $unitPrice;
        }
        return $data;
    }

    /**
     * Get items by dispatch
     */
    public function getByDispatch($dispatchId)
    {
        return $this->db->table('dispatch_items di')
                       ->select('di.*, p.product_name, p.product_code, p.description, p.unit_of_measure')
                       ->join('products p', 'p.id = di.product_id', 'left')
                       ->where('di.dispatch_id', $dispatchId)
                       ->orderBy('di.id', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Create dispatch item
     */
    public function createItem($data)
    {
        return $this->insert($data);
    }

    /**
     * Update dispatch item
     */
    public function updateItem($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Delete dispatch item
     */
    public function deleteItem($id)
    {
        return $this->delete($id);
    }

    /**
     * Get item with product details
     */
    public function getItemWithDetails($id)
    {
        return $this->db->table('dispatch_items di')
                       ->select('di.*, p.product_name, p.product_code, p.description, p.unit_of_measure')
                       ->join('products p', 'p.id = di.product_id', 'left')
                       ->where('di.id', $id)
                       ->get()
                       ->getRowArray();
    }

    /**
     * Calculate item total
     */
    public function calculateItemTotal($quantity, $unitPrice)
    {
        return $quantity * $unitPrice;
    }

    /**
     * Get dispatch items summary
     */
    public function getDispatchSummary($dispatchId)
    {
        $items = $this->getByDispatch($dispatchId);
        
        $summary = [
            'total_items' => count($items),
            'total_quantity' => 0,
            'total_amount' => 0
        ];
        
        foreach ($items as $item) {
            $summary['total_quantity'] += $item['quantity'];
            $summary['total_amount'] += $item['total_amount'];
        }
        
        return $summary;
    }

    /**
     * Get items by product
     */
    public function getItemsByProduct($productId)
    {
        return $this->db->table('dispatch_items di')
                       ->select('di.*, dn.dispatch_number, dn.dispatch_date, c.customer_name')
                       ->join('dispatch_notes dn', 'dn.id = di.dispatch_id', 'left')
                       ->join('sales_orders so', 'so.id = dn.order_id', 'left')
                       ->join('customers c', 'c.id = so.customer_id', 'left')
                       ->where('di.product_id', $productId)
                       ->orderBy('dn.dispatch_date', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get product usage in dispatches
     */
    public function getProductUsage($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('dispatch_items di')
                           ->select('di.product_id, SUM(di.quantity) as total_quantity, SUM(di.total_amount) as total_value, COUNT(DISTINCT di.dispatch_id) as dispatch_count')
                           ->join('dispatch_notes dn', 'dn.id = di.dispatch_id', 'left')
                           ->where('di.product_id', $productId);

        if ($startDate) {
            $builder->where('dn.dispatch_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('dn.dispatch_date <=', $endDate);
        }

        return $builder->groupBy('di.product_id')->get()->getRowArray();
    }

    /**
     * Export dispatch items to CSV
     */
    public function exportToCSV($dispatchId)
    {
        $items = $this->getByDispatch($dispatchId);
        
        $filename = 'dispatch_items_' . $dispatchId . '_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = WRITEPATH . 'uploads/' . $filename;
        
        $fp = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($fp, [
            'Product Code', 'Product Name', 'Description', 'Quantity', 'Unit Price',
            'Total Amount', 'Unit of Measure', 'Notes'
        ]);
        
        // Write data
        foreach ($items as $item) {
            fputcsv($fp, [
                $item['product_code'],
                $item['product_name'],
                $item['description'],
                $item['quantity'],
                $item['unit_price'],
                $item['total_amount'],
                $item['unit_of_measure'],
                $item['notes']
            ]);
        }
        
        fclose($fp);
        
        return $filepath;
    }
}
