<?php

namespace App\Models;

use CodeIgniter\Model;

class GoodsReceipt extends Model
{
    protected $table = 'goods_receipt_notes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'grn_number',
        'po_id',
        'supplier_id',
        'warehouse_id',
        'receipt_date',
        'vehicle_number',
        'driver_name',
        'remarks',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'created_at',
        'updated_at',
        'approved_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'grn_number' => 'required|min_length[3]|max_length[50]|is_unique[goods_receipt_notes.grn_number,id,{id}]',
        'po_id' => 'required|numeric',
        'supplier_id' => 'required|numeric',
        'warehouse_id' => 'required|numeric',
        'receipt_date' => 'required|valid_date',
        'status' => 'required|in_list[draft,received,approved,rejected]'
    ];

    protected $validationMessages = [
        'grn_number' => [
            'required' => 'GRN Number is required',
            'min_length' => 'GRN Number must be at least 3 characters long',
            'max_length' => 'GRN Number cannot exceed 50 characters',
            'is_unique' => 'GRN Number must be unique'
        ],
        'po_id' => [
            'required' => 'Purchase Order is required',
            'numeric' => 'Purchase Order must be a valid number'
        ],
        'supplier_id' => [
            'required' => 'Supplier is required',
            'numeric' => 'Supplier must be a valid number'
        ],
        'warehouse_id' => [
            'required' => 'Warehouse is required',
            'numeric' => 'Warehouse must be a valid number'
        ],
        'receipt_date' => [
            'required' => 'Receipt Date is required',
            'valid_date' => 'Receipt Date must be a valid date'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: draft, received, approved, rejected'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getWithRelations($id = null)
    {
        if ($id === null) {
            return $this->getAllWithRelations();
        }

        $builder = $this->db->table('goods_receipt_notes gr');
        $builder->select('gr.*, po.po_number, s.supplier_name, s.supplier_code, w.warehouse_name');
        $builder->join('purchase_orders po', 'po.id = gr.po_id', 'left');
        $builder->join('suppliers s', 's.id = gr.supplier_id', 'left');
        $builder->join('warehouses w', 'w.id = gr.warehouse_id', 'left');
        $builder->where('gr.id', $id);

        $result = $builder->get()->getRowArray();

        if ($result) {
            $result['items'] = $this->getItems($id);
        }

        return $result;
    }

    public function getAllWithRelations($filters = [])
    {
        $builder = $this->db->table('goods_receipt_notes gr');
        $builder->select('gr.*, po.po_number, s.supplier_name, s.supplier_code, w.warehouse_name');
        $builder->join('purchase_orders po', 'po.id = gr.po_id', 'left');
        $builder->join('suppliers s', 's.id = gr.supplier_id', 'left');
        $builder->join('warehouses w', 'w.id = gr.warehouse_id', 'left');
        
        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('gr.status', $filters['status']);
        }
        
        if (!empty($filters['supplier_id'])) {
            $builder->where('gr.supplier_id', $filters['supplier_id']);
        }
        
        if (!empty($filters['warehouse_id'])) {
            $builder->where('gr.warehouse_id', $filters['warehouse_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart();
            $builder->like('gr.grn_number', $search);
            $builder->orLike('po.po_number', $search);
            $builder->orLike('s.supplier_name', $search);
            $builder->orLike('s.supplier_code', $search);
            $builder->groupEnd();
        }
        
        $builder->orderBy('gr.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getItems($grnId)
    {
        $builder = $this->db->table('goods_receipt_items gri');
        $builder->select('gri.*, p.product_code, p.product_name, p.unit, poi.quantity as po_quantity, poi.unit_price as po_unit_price');
        $builder->join('products p', 'p.id = gri.product_id', 'left');
        $builder->join('purchase_order_items poi', 'poi.id = gri.purchase_order_item_id', 'left');
        $builder->where('gri.grn_id', $grnId);
        
        return $builder->get()->getResultArray();
    }

    public function getByPurchaseOrder($poId)
    {
        return $this->where('po_id', $poId)->findAll();
    }

    public function getApprovedForDebitNote()
    {
        return $this->whereIn('status', ['approved', 'verified', 'received'])
            ->orderBy('receipt_date', 'DESC')
            ->findAll();
    }

    public function getBySupplier($supplierId, $filters = [])
    {
        $builder = $this->db->table('goods_receipt_notes gr');
        $builder->select('gr.*, po.po_number, w.warehouse_name');
        $builder->join('purchase_orders po', 'po.id = gr.po_id', 'left');
        $builder->join('warehouses w', 'w.id = gr.warehouse_id', 'left');
        $builder->where('gr.supplier_id', $supplierId);
        
        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('gr.status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        $builder->orderBy('gr.receipt_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getStats($filters = [])
    {
        $builder = $this->db->table('goods_receipt_notes gr');
        
        // Apply filters
        if (!empty($filters['supplier_id'])) {
            $builder->where('gr.supplier_id', $filters['supplier_id']);
        }
        
        if (!empty($filters['warehouse_id'])) {
            $builder->where('gr.warehouse_id', $filters['warehouse_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        $stats = [
            'total' => $builder->countAllResults(),
            'draft' => $builder->where('gr.status', 'draft')->countAllResults(),
            'received' => $builder->where('gr.status', 'received')->countAllResults(),
            'approved' => $builder->where('gr.status', 'approved')->countAllResults(),
            'rejected' => $builder->where('gr.status', 'rejected')->countAllResults()
        ];
        
        return $stats;
    }

    public function getMonthlyStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $builder = $this->db->table('goods_receipt_notes gr');
        $builder->select('MONTH(gr.receipt_date) as month, COUNT(*) as count, gr.status');
        $builder->where('YEAR(gr.receipt_date)', $year);
        $builder->groupBy(['MONTH(gr.receipt_date)', 'gr.status']);
        $builder->orderBy('month', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Initialize monthly data
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = [
                'month' => $i,
                'month_name' => date('F', mktime(0, 0, 0, $i, 1)),
                'draft' => 0,
                'received' => 0,
                'approved' => 0,
                'rejected' => 0,
                'total' => 0
            ];
        }
        
        // Fill in actual data
        foreach ($results as $result) {
            $month = $result['month'];
            $status = $result['status'];
            $count = $result['count'];
            
            if (isset($monthlyData[$month])) {
                $monthlyData[$month][$status] = $count;
                $monthlyData[$month]['total'] += $count;
            }
        }
        
        return array_values($monthlyData);
    }

    public function getSupplierPerformance($filters = [])
    {
        $builder = $this->db->table('goods_receipt_notes gr');
        $builder->select('s.supplier_name, s.supplier_code, COUNT(gr.id) as total_grns, 
                         SUM(CASE WHEN gr.status = "approved" THEN 1 ELSE 0 END) as approved_grns,
                         AVG(CASE WHEN gr.status = "approved" THEN 1 ELSE 0 END) * 100 as approval_rate');
        $builder->join('suppliers s', 's.id = gr.supplier_id', 'left');
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $builder->where('gr.receipt_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('gr.receipt_date <=', $filters['date_to']);
        }
        
        $builder->groupBy(['s.id', 's.supplier_name', 's.supplier_code']);
        $builder->orderBy('total_grns', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
