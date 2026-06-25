<?php

namespace App\Models;

use CodeIgniter\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category_name',
        'description',
        'category_type',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'category_name' => 'required|min_length[2]|max_length[100]|is_unique[categories.category_name,id,{id}]',
        'category_type' => 'required|in_list[raw_material,packaging,finished_goods,waste]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'category_name' => [
            'required' => 'Category name is required',
            'min_length' => 'Category name must be at least 2 characters long',
            'max_length' => 'Category name cannot exceed 100 characters',
            'is_unique' => 'Category name already exists'
        ],
        'category_type' => [
            'required' => 'Category type is required',
            'in_list' => 'Invalid category type'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Invalid status'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get categories with optional filters
     */
    public function getCategories($filters = [])
    {
        $builder = $this->builder();
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->like('category_name', $filters['search']);
        }
        
        if (!empty($filters['category_type'])) {
            $builder->where('category_type', $filters['category_type']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        // Order by
        $builder->orderBy('category_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'by_type' => [
                'raw_material' => $this->where('category_type', 'raw_material')->countAllResults(),
                'packaging' => $this->where('category_type', 'packaging')->countAllResults(),
                'finished_goods' => $this->where('category_type', 'finished_goods')->countAllResults(),
                'waste' => $this->where('category_type', 'waste')->countAllResults()
            ]
        ];
        
        return $stats;
    }

    /**
     * Get active categories for dropdown
     */
    public function getActiveCategories()
    {
        return $this->where('status', 'active')
                   ->orderBy('category_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get category types for dropdown
     */
    public function getCategoryTypes()
    {
        return [
            'raw_material' => 'Raw Material',
            'packaging' => 'Packaging',
            'finished_goods' => 'Finished Goods',
            'waste' => 'Waste'
        ];
    }

    /**
     * Check if category is used in products
     */
    public function isCategoryUsed($categoryId)
    {
        $db = \Config\Database::connect();
        $result = $db->table('products')
                    ->where('category_id', $categoryId)
                    ->countAllResults();
        
        return $result > 0;
    }

    /**
     * Get category with product count
     */
    public function getCategoryWithProductCount($categoryId)
    {
        $db = \Config\Database::connect();
        $category = $this->find($categoryId);
        
        if ($category) {
            $category['product_count'] = $db->table('products')
                                          ->where('category_id', $categoryId)
                                          ->countAllResults();
        }
        
        return $category;
    }

    /**
     * Get categories with product counts
     */
    public function getCategoriesWithProductCounts()
    {
        $db = \Config\Database::connect();
        $categories = $this->findAll();
        
        foreach ($categories as &$category) {
            $category['product_count'] = $db->table('products')
                                          ->where('category_id', $category['id'])
                                          ->countAllResults();
        }
        
        return $categories;
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($categoryId)
    {
        $category = $this->find($categoryId);
        
        if (!$category) {
            return false;
        }
        
        $newStatus = ($category['status'] === 'active') ? 'inactive' : 'active';
        
        return $this->update($categoryId, ['status' => $newStatus]);
    }

    /**
     * Get category name by ID
     */
    public function getCategoryName($categoryId)
    {
        $category = $this->find($categoryId);
        return $category ? $category['category_name'] : '';
    }

    /**
     * Get category type name
     */
    public function getCategoryTypeName($type)
    {
        $types = $this->getCategoryTypes();
        return isset($types[$type]) ? $types[$type] : $type;
    }

    /**
     * Get categories for specific material types
     */
    public function getCategoriesForMaterialType($materialType)
    {
        $categoryTypeMap = [
            'raw_material' => 'raw_material',
            'packaging' => 'packaging',
            'finished_goods' => 'finished_goods',
            'waste' => 'waste'
        ];
        
        $categoryType = isset($categoryTypeMap[$materialType]) ? $categoryTypeMap[$materialType] : 'raw_material';
        
        return $this->where('category_type', $categoryType)
                   ->where('status', 'active')
                   ->orderBy('category_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get category usage count
     */
    public function getCategoryUsageCount($categoryId)
    {
        $db = \Config\Database::connect();
        $count = 0;
        
        // Check products table
        try {
            $count += $db->table('products')->where('category_id', $categoryId)->countAllResults();
        } catch (\Throwable $e) {
            // Table doesn't exist
        }
        
        // Check suppliers table
        try {
            if ($db->fieldExists('supplier_category', 'suppliers')) {
                $count += $db->table('suppliers')->where('supplier_category', $categoryId)->countAllResults();
            }
        } catch (\Throwable $e) {
            // Table doesn't exist
        }
        
        // Check customers table
        try {
            if ($db->fieldExists('customer_category', 'customers')) {
                $count += $db->table('customers')->where('customer_category', $categoryId)->countAllResults();
            }
        } catch (\Throwable $e) {
            // Table doesn't exist
        }
        
        return $count;
    }

    /**
     * Check if category is in use
     */
    public function isCategoryInUse($categoryId)
    {
        return $this->getCategoryUsageCount($categoryId) > 0;
    }

    /**
     * Get categories by type for dropdown
     */
    public function getCategoriesByType($type)
    {
        return $this->where('category_type', $type)
                   ->where('status', 'active')
                   ->orderBy('category_name', 'ASC')
                   ->findAll();
    }
} 
