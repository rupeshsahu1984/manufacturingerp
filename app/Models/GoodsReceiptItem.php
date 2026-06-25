<?php

namespace App\Models;

use CodeIgniter\Model;

class GoodsReceiptItem extends Model
{
    protected $table = 'goods_receipt_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'grn_id',
        'product_id',
        'purchase_order_item_id',
        'received_qty',
        'accepted_qty',
        'rejected_qty',
        'unit_price',
        'remarks'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'grn_id' => 'required|numeric',
        'product_id' => 'required|numeric',
        'purchase_order_item_id' => 'required|numeric',
        'received_qty' => 'required|numeric|greater_than[0]',
        'accepted_qty' => 'required|numeric|greater_than_equal_to[0]',
        'rejected_qty' => 'required|numeric|greater_than_equal_to[0]',
        'unit_price' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'grn_id' => [
            'required' => 'GRN ID is required',
            'numeric' => 'GRN ID must be a valid number'
        ],
        'product_id' => [
            'required' => 'Product is required',
            'numeric' => 'Product must be a valid number'
        ],
        'purchase_order_item_id' => [
            'required' => 'Purchase Order Item is required',
            'numeric' => 'Purchase Order Item must be a valid number'
        ],
        'received_qty' => [
            'required' => 'Received Quantity is required',
            'numeric' => 'Received Quantity must be a valid number',
            'greater_than' => 'Received Quantity must be greater than 0'
        ],
        'accepted_qty' => [
            'required' => 'Accepted Quantity is required',
            'numeric' => 'Accepted Quantity must be a valid number',
            'greater_than_equal_to' => 'Accepted Quantity must be 0 or greater'
        ],
        'rejected_qty' => [
            'required' => 'Rejected Quantity is required',
            'numeric' => 'Rejected Quantity must be a valid number',
            'greater_than_equal_to' => 'Rejected Quantity must be 0 or greater'
        ],
        'unit_price' => [
            'required' => 'Unit Price is required',
            'numeric' => 'Unit Price must be a valid number',
            'greater_than_equal_to' => 'Unit Price must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getWithProduct($id)
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('gri.*, p.product_code, p.product_name, p.unit, p.specifications');
        $builder->join('products p', 'p.id = gri.product_id', 'left');
        $builder->where('gri.id', $id);
        
        return $builder->get()->getRowArray();
    }

    public function getByGRN($grnId)
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('gri.*, p.product_code, p.product_name, p.unit, p.specifications, 
                         poi.quantity as po_quantity, poi.unit_price as po_unit_price');
        $builder->join('products p', 'p.id = gri.product_id', 'left');
        $builder->join('purchase_order_items poi', 'poi.id = gri.purchase_order_item_id', 'left');
        $builder->where('gri.grn_id', $grnId);
        
        return $builder->get()->getResultArray();
    }

    public function getByProduct($productId, $filters = [])
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('gri.*, gr.grn_number, gr.receipt_date, gr.status, s.supplier_name');
        $builder->join('goods_receipts gr', 'gr.id = gri.grn_id', 'left');
        $builder->join('suppliers s', 's.id = gr.supplier_id', 'left');
        $builder->where('gri.product_id', $productId);
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('gr.status', $filters['status']);
        }
        
        $builder->orderBy('gr.receipt_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getByPurchaseOrderItem($poItemId)
    {
        return $this->where('purchase_order_item_id', $poItemId)->findAll();
    }

    public function getTotalReceivedByProduct($productId, $filters = [])
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('SUM(gri.accepted_qty) as total_received, COUNT(DISTINCT gri.grn_id) as total_grns');
        $builder->join('goods_receipts gr', 'gr.id = gri.grn_id', 'left');
        $builder->where('gri.product_id', $productId);
        $builder->where('gr.status', 'approved');
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        $result = $builder->get()->getRowArray();
        
        return [
            'total_received' => isset($result['total_received']) ? $result['total_received'] : 0,
            'total_grns' => isset($result['total_grns']) ? $result['total_grns'] : 0
        ];
    }

    public function getQualityMetrics($filters = [])
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('p.product_name, p.product_code,
                         SUM(gri.received_qty) as total_received,
                         SUM(gri.accepted_qty) as total_accepted,
                         SUM(gri.rejected_qty) as total_rejected,
                         AVG(gri.unit_price) as avg_unit_price');
        $builder->join('products p', 'p.id = gri.product_id', 'left');
        $builder->join('goods_receipts gr', 'gr.id = gri.grn_id', 'left');
        $builder->where('gr.status', 'approved');
        
        // Apply filters
        if (!empty($filters['supplier_id'])) {
            $builder->where('gr.supplier_id', $filters['supplier_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        $builder->groupBy(['p.id', 'p.product_name', 'p.product_code']);
        $builder->orderBy('total_received', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        // Calculate quality metrics
        foreach ($results as &$result) {
            $total = $result['total_received'];
            $accepted = $result['total_accepted'];
            $rejected = $result['total_rejected'];
            
            $result['acceptance_rate'] = $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
            $result['rejection_rate'] = $total > 0 ? round(($rejected / $total) * 100, 2) : 0;
            $result['quality_score'] = $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
        }
        
        return $results;
    }

    public function getSupplierQualityMetrics($supplierId, $filters = [])
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('p.product_name, p.product_code,
                         SUM(gri.received_qty) as total_received,
                         SUM(gri.accepted_qty) as total_accepted,
                         SUM(gri.rejected_qty) as total_rejected,
                         AVG(gri.unit_price) as avg_unit_price');
        $builder->join('products p', 'p.id = gri.product_id', 'left');
        $builder->join('goods_receipts gr', 'gr.id = gri.grn_id', 'left');
        $builder->where('gr.supplier_id', $supplierId);
        $builder->where('gr.status', 'approved');
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        $builder->groupBy(['p.id', 'p.product_name', 'p.product_code']);
        $builder->orderBy('total_received', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        // Calculate quality metrics
        foreach ($results as &$result) {
            $total = $result['total_received'];
            $accepted = $result['total_accepted'];
            $rejected = $result['total_rejected'];
            
            $result['acceptance_rate'] = $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
            $result['rejection_rate'] = $total > 0 ? round(($rejected / $total) * 100, 2) : 0;
            $result['quality_score'] = $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
        }
        
        return $results;
    }

    public function validateQuantities($grnId)
    {
        $items = $this->getByGRN($grnId);
        $errors = [];
        
        foreach ($items as $item) {
            $received = $item['received_qty'];
            $accepted = $item['accepted_qty'];
            $rejected = $item['rejected_qty'];
            
            if ($accepted + $rejected !== $received) {
                $errors[] = "Product {$item['product_name']}: Accepted + Rejected quantity must equal Received quantity";
            }
            
            if ($accepted < 0 || $rejected < 0) {
                $errors[] = "Product {$item['product_name']}: Quantities cannot be negative";
            }
        }
        
        return $errors;
    }
}
