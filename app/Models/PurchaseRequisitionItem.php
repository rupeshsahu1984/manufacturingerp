<?php
namespace App\Models;
use CodeIgniter\Model;

class PurchaseRequisitionItem extends Model
{
    protected $table = 'purchase_requisition_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'pr_id', 'product_id', 'quantity', 'unit_price', 'total_amount', 'remarks'
    ];
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'pr_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|decimal',
        'unit_price' => 'permit_empty|decimal',
        'total_amount' => 'permit_empty|decimal',
        'remarks' => 'permit_empty|max_length[500]'
    ];
    
    protected $validationMessages = [
        'pr_id' => [
            'required' => 'PR ID is required',
            'integer' => 'Invalid PR ID'
        ],
        'product_id' => [
            'required' => 'Product is required',
            'integer' => 'Invalid product ID'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'decimal' => 'Quantity must be a valid number'
        ],
        'unit_price' => [
            'decimal' => 'Unit price must be a valid number'
        ],
        'total_amount' => [
            'decimal' => 'Total amount must be a valid number'
        ],
        'remarks' => [
            'max_length' => 'Remarks cannot exceed 500 characters'
        ]
    ];

    public function getItemsByPRId($prId)
    {
        $builder = $this->db->table('purchase_requisition_items pri');
        $builder->select('pri.*, p.product_name, p.product_code, p.unit, p.hsn_code');
        $builder->join('products p', 'p.id = pri.product_id', 'left');
        $builder->where('pri.pr_id', $prId);
        return $builder->get()->getResultArray();
    }

    public function addItems($prId, $items)
    {
        $data = [];
        foreach ($items as $item) {
            // Calculate total if not present
            $quantity = isset($item['quantity']) ? floatval($item['quantity']) : 0;
            $unitPrice = isset($item['unit_price']) ? floatval($item['unit_price']) : 0;
            $totalAmount = $quantity * $unitPrice;

            // Prepare clean data array matching allowedFields
            $data[] = [
                'pr_id'        => $prId,
                'product_id'   => isset($item['product_id']) ? intval($item['product_id']) : null,
                'quantity'     => $quantity,
                'unit_price'   => $unitPrice,
                'total_amount' => $totalAmount,
                'remarks'      => isset($item['remarks']) ? $item['remarks'] : null
            ];
        }
        
        // Log the prepared data for debugging
        log_message('error', 'Preparing to insertBatch items: ' . json_encode($data));
        
        return $this->insertBatch($data);
    }

    public function updateItems($prId, $items)
    {
        // Delete existing items
        $this->where('pr_id', $prId)->delete();
        
        // Add new items
        return $this->addItems($prId, $items);
    }

    public function deleteItemsByPRId($prId)
    {
        return $this->where('pr_id', $prId)->delete();
    }

    public function calculateTotal($prId)
    {
        $builder = $this->db->table('purchase_requisition_items');
        $builder->selectSum('total_amount');
        $builder->where('pr_id', $prId);
        $result = $builder->get()->getRow();
        
        return $result ? $result->total_amount : 0;
    }
} 