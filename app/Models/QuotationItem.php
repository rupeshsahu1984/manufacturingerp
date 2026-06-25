<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationItem extends Model
{
    protected $table = 'quotation_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'quotation_id', 'product_id', 'quantity', 'unit_price', 'discount_percent',
        'total_amount', 'notes', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'quotation_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than_equal_to[0]',
        'discount_percent' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
    ];

    protected $validationMessages = [
        'quotation_id' => [
            'required' => 'Quotation ID is required',
            'integer' => 'Invalid quotation ID'
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
        ],
        'discount_percent' => [
            'numeric' => 'Discount must be a number',
            'greater_than_equal_to' => 'Discount cannot be negative',
            'less_than_equal_to' => 'Discount cannot exceed 100%'
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
            $discountPercent = isset($data['data']['discount_percent']) ? $data['data']['discount_percent'] : 0;
            
            $subtotal = $quantity * $unitPrice;
            $discountAmount = $subtotal * ($discountPercent / 100);
            $totalAmount = $subtotal - $discountAmount;
            
            $data['data']['total_amount'] = $totalAmount;
        }
        return $data;
    }

    /**
     * Get items by quotation
     */
    public function getByQuotation($quotationId)
    {
        return $this->db->table('quotation_items qi')
                       ->select('qi.*, p.product_name, p.product_code, p.description, p.unit_of_measure')
                       ->join('products p', 'p.id = qi.product_id', 'left')
                       ->where('qi.quotation_id', $quotationId)
                       ->orderBy('qi.id', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Create quotation item
     */
    public function createItem($data)
    {
        return $this->insert($data);
    }

    /**
     * Update quotation item
     */
    public function updateItem($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Delete quotation item
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
        return $this->db->table('quotation_items qi')
                       ->select('qi.*, p.product_name, p.product_code, p.description, p.unit_of_measure')
                       ->join('products p', 'p.id = qi.product_id', 'left')
                       ->where('qi.id', $id)
                       ->get()
                       ->getRowArray();
    }

    /**
     * Calculate item total
     */
    public function calculateItemTotal($quantity, $unitPrice, $discountPercent = 0)
    {
        $subtotal = $quantity * $unitPrice;
        $discountAmount = $subtotal * ($discountPercent / 100);
        return $subtotal - $discountAmount;
    }

    /**
     * Get quotation items summary
     */
    public function getQuotationSummary($quotationId)
    {
        $items = $this->getByQuotation($quotationId);
        
        $summary = [
            'total_items' => count($items),
            'total_quantity' => 0,
            'subtotal' => 0,
            'total_discount' => 0,
            'total_amount' => 0
        ];
        
        foreach ($items as $item) {
            $summary['total_quantity'] += $item['quantity'];
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $summary['subtotal'] += $itemSubtotal;
            $summary['total_discount'] += $itemSubtotal * ($item['discount_percent'] / 100);
            $summary['total_amount'] += $item['total_amount'];
        }
        
        return $summary;
    }

    /**
     * Check product availability
     */
    public function checkProductAvailability($productId, $quantity)
    {
        $stockModel = new Stock();
        $availableStock = $stockModel->getAvailableStock($productId);
        return $availableStock >= $quantity;
    }

    /**
     * Get items by product
     */
    public function getItemsByProduct($productId)
    {
        return $this->db->table('quotation_items qi')
                       ->select('qi.*, q.quotation_number, q.quotation_date, c.customer_name')
                       ->join('quotations q', 'q.id = qi.quotation_id', 'left')
                       ->join('customers c', 'c.id = q.customer_id', 'left')
                       ->where('qi.product_id', $productId)
                       ->orderBy('q.quotation_date', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get product usage in quotations
     */
    public function getProductUsage($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('quotation_items qi')
                           ->select('qi.product_id, SUM(qi.quantity) as total_quantity, SUM(qi.total_amount) as total_value, COUNT(DISTINCT qi.quotation_id) as quotation_count')
                           ->join('quotations q', 'q.id = qi.quotation_id', 'left')
                           ->where('qi.product_id', $productId);

        if ($startDate) {
            $builder->where('q.quotation_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('q.quotation_date <=', $endDate);
        }

        return $builder->groupBy('qi.product_id')->get()->getRowArray();
    }

    /**
     * Export quotation items to CSV
     */
    public function exportToCSV($quotationId)
    {
        $items = $this->getByQuotation($quotationId);
        
        $filename = 'quotation_items_' . $quotationId . '_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = WRITEPATH . 'uploads/' . $filename;
        
        $fp = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($fp, [
            'Product Code', 'Product Name', 'Description', 'Quantity', 'Unit Price',
            'Discount %', 'Total Amount', 'Unit of Measure', 'Notes'
        ]);
        
        // Write data
        foreach ($items as $item) {
            fputcsv($fp, [
                $item['product_code'],
                $item['product_name'],
                $item['description'],
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percent'],
                $item['total_amount'],
                $item['unit_of_measure'],
                $item['notes']
            ]);
        }
        
        fclose($fp);
        
        return $filepath;
    }
}
