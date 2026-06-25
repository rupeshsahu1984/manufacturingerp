<?php

namespace App\Models;

use CodeIgniter\Model;

class Quotation extends Model
{
    protected $table = 'quotations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'quotation_number',
        'customer_id',
        'quotation_date',
        'valid_until',
        'delivery_address',
        'payment_terms',
        'subtotal',
        'gst_amount',
        'total_amount',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'quotation_number' => 'required|max_length[20]|is_unique[quotations.quotation_number,id,{id}]',
        'customer_id' => 'required|integer',
        'quotation_date' => 'required|valid_date',
        'valid_until' => 'required|valid_date',
        'status' => 'required|in_list[draft,sent,accepted,rejected,expired]'
    ];

    public function getQuotationsWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' q');
        $builder->select('q.*, c.customer_name');
        $builder->join('customers c', 'c.id = q.customer_id', 'left');

        if (is_numeric($filters)) {
            $builder->where('q.id', $filters);
            return $builder->get()->getRowArray();
        }

        if (!empty($filters['search'])) {
            $builder->like('q.quotation_number', $filters['search']);
        }

        if (!empty($filters['customer'])) {
            $builder->where('q.customer_id', $filters['customer']);
        }

        if (!empty($filters['status'])) {
            $builder->where('q.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('q.quotation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('q.quotation_date <=', $filters['date_to']);
        }

        $builder->orderBy('q.quotation_date', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getQuotationStats()
    {
        return [
            'total' => $this->countAll(),
            'draft' => $this->where('status', 'draft')->countAllResults(),
            'sent' => $this->where('status', 'sent')->countAllResults(),
            'accepted' => $this->where('status', 'accepted')->countAllResults(),
            'rejected' => $this->where('status', 'rejected')->countAllResults(),
            'expired' => $this->where('status', 'expired')->countAllResults()
        ];
    }

    public function generateUniqueQuotationNumber()
    {
        $prefix = 'QTN' . date('Ym');
        $lastRecord = $this->like('quotation_number', $prefix, 'after')
                           ->orderBy('quotation_number', 'DESC')
                           ->first();
        
        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord['quotation_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
