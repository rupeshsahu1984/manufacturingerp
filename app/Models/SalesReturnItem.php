<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesReturnItem extends Model
{
    protected $table = 'sales_return_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'return_id',
        'product_id',
        'quantity',
        'unit_price',
        'line_total',
        'return_reason',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'return_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than[0]',
        'line_total' => 'required|numeric|greater_than[0]',
        'return_reason' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'return_id' => [
            'required' => 'Return ID is required.',
            'integer' => 'Invalid return ID.'
        ],
        'product_id' => [
            'required' => 'Product is required.',
            'integer' => 'Invalid product selected.'
        ],
        'quantity' => [
            'required' => 'Quantity is required.',
            'numeric' => 'Quantity must be a number.',
            'greater_than' => 'Quantity must be greater than 0.'
        ],
        'unit_price' => [
            'required' => 'Unit price is required.',
            'numeric' => 'Unit price must be a number.',
            'greater_than' => 'Unit price must be greater than 0.'
        ],
        'line_total' => [
            'required' => 'Line total is required.',
            'numeric' => 'Line total must be a number.',
            'greater_than' => 'Line total must be greater than 0.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getItemsByReturnId($return_id)
    {
        $builder = $this->db->table('sales_return_items sri')
            ->select('sri.*, p.product_name, p.product_code as product_sku, p.description as product_description')
            ->join('products p', 'p.id = sri.product_id', 'left')
            ->where('sri.return_id', $return_id)
            ->orderBy('sri.id', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getItemsByProduct($product_id)
    {
        return $this->where('product_id', $product_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getTotalQuantityByProduct($product_id, $start_date = null, $end_date = null)
    {
        $builder = $this->selectSum('quantity')
            ->where('product_id', $product_id);

        if ($start_date) {
            $builder->where('created_at >=', $start_date);
        }

        if ($end_date) {
            $builder->where('created_at <=', $end_date);
        }

        $result = $builder->first();
        return isset($result['quantity']) ? $result['quantity'] : 0;
    }

    public function getTotalAmountByReturn($return_id)
    {
        $result = $this->selectSum('line_total')
            ->where('return_id', $return_id)
            ->first();

        return isset($result['line_total']) ? $result['line_total'] : 0;
    }

    public function deleteItemsByReturnId($return_id)
    {
        return $this->where('return_id', $return_id)->delete();
    }
}
