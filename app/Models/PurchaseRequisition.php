<?php
namespace App\Models;
use CodeIgniter\Model;

class PurchaseRequisition extends Model
{
    protected $table = 'purchase_requisitions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'pr_number', 'requested_by', 'department', 'priority', 'status',
        'required_date', 'remarks', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'pr_number' => 'required|min_length[3]|max_length[20]|is_unique[purchase_requisitions.pr_number,id,{id}]',
        'requested_by' => 'required|integer',
        'department' => 'permit_empty|max_length[50]',
        'priority' => 'required|in_list[low,medium,high,urgent]',
        'status' => 'required|in_list[draft,pending,approved,rejected,ordered]',
        'required_date' => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'pr_number' => [
            'required' => 'PR Number is required',
            'min_length' => 'PR Number must be at least 3 characters',
            'max_length' => 'PR Number cannot exceed 20 characters',
            'is_unique' => 'PR Number already exists'
        ],
        'requested_by' => [
            'required' => 'Requested by is required',
            'integer' => 'Invalid user ID'
        ],
        'priority' => [
            'required' => 'Priority is required',
            'in_list' => 'Invalid priority level'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Invalid status'
        ],
        'required_date' => [
            'required' => 'Required date is required',
            'valid_date' => 'Invalid date format'
        ]
    ];

    public function getPurchaseRequisitions($filters = [])
    {
        $builder = $this->db->table('purchase_requisitions pr');
        $builder->select('pr.*, u.full_name as requested_by_name');
        $builder->join('users u', 'u.id = pr.requested_by', 'left');
        
        if (!empty($filters['status'])) {
            $builder->where('pr.status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $builder->where('pr.priority', $filters['priority']);
        }
        if (!empty($filters['department'])) {
            $builder->where('pr.department', $filters['department']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('pr.created_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('pr.created_at <=', $filters['date_to']);
        }
        
        $builder->orderBy('pr.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /** List or single row for purchase hub controllers */
    public function getWithRelations($id = null)
    {
        if ($id !== null) {
            return $this->find($id);
        }

        return $this->getPurchaseRequisitions();
    }

    public function getPurchaseRequisitionWithItems($id)
    {
        // Get PR details
        $pr = $this->find($id);
        if (!$pr) {
            return null;
        }

        // Get PR items
        $builder = $this->db->table('purchase_requisition_items pri');
        $builder->select('pri.*, p.product_name, p.product_code, p.unit');
        $builder->join('products p', 'p.id = pri.product_id', 'left');
        $builder->where('pri.pr_id', $id);
        $items = $builder->get()->getResultArray();

        $pr['items'] = $items;
        return $pr;
    }

    public function generatePRNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $builder = $this->db->table('purchase_requisitions');
        $builder->where('YEAR(created_at)', $year);
        $builder->where('MONTH(created_at)', $month);
        $count = $builder->countAllResults();
        
        return 'PR' . $year . $month . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getPRStats()
    {
        $builder = $this->db->table('purchase_requisitions');
        
        $stats = [
            'total' => $builder->countAllResults(),
            'draft' => $builder->where('status', 'draft')->countAllResults(),
            'pending' => $builder->where('status', 'pending')->countAllResults(),
            'approved' => $builder->where('status', 'approved')->countAllResults(),
            'rejected' => $builder->where('status', 'rejected')->countAllResults(),
            'ordered' => $builder->where('status', 'ordered')->countAllResults()
        ];
        
        return $stats;
    }
} 