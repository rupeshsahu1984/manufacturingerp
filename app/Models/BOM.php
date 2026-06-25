<?php

namespace App\Models;

use CodeIgniter\Model;

class BOM extends Model
{
    protected $table = 'bom';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'finished_product_id',
        'description',
        'version',
        'total_cost',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'finished_product_id' => 'required|integer',
        'description' => 'permit_empty|max_length[500]',
        'version' => 'required|max_length[20]',
        'total_cost' => 'permit_empty|numeric',
        'status' => 'required|in_list[active,inactive,draft]'
    ];

    protected $validationMessages = [
        'finished_product_id' => [
            'required' => 'Finished product is required',
            'integer' => 'Finished product must be a valid selection'
        ],
        'description' => [
            'max_length' => 'Description cannot exceed 500 characters'
        ],
        'version' => [
            'required' => 'Version is required',
            'max_length' => 'Version cannot exceed 20 characters'
        ],
        'total_cost' => [
            'numeric' => 'Total cost must be a number'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be active, inactive, or draft'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get BOM with items
     */
    public function getBOMWithItems($bomId)
    {
        $bom = $this->find($bomId);
        
        if (!$bom) {
            return null;
        }

        // Get BOM items
        $bomItemModel = new \App\Models\BOMItem();
        $bom['items'] = $bomItemModel->getBOMItems($bomId);

        // Get finished product details
        $productModel = new \App\Models\Product();
        $bom['finished_product'] = $productModel->find($bom['finished_product_id']);

        return $bom;
    }

    /**
     * Get BOM by finished product
     */
    public function getBOMByFinishedProduct($finishedProductId)
    {
        return $this->where('finished_product_id', $finishedProductId)
            ->where('status', 'active')
            ->orderBy('version', 'DESC')
            ->first();
    }

    /**
     * Get BOM with items by finished product
     */
    public function getBOMWithItemsByFinishedProduct($finishedProductId)
    {
        $bom = $this->getBOMByFinishedProduct($finishedProductId);
        
        if (!$bom) {
            return null;
        }

        return $this->getBOMWithItems($bom['id']);
    }

    /**
     * Get all BOMs with filters
     */
    public function getBOMs($filters = [])
    {
        $builder = $this->select('bom.*, p.product_name as finished_product_name, p.product_code as finished_product_code, u.full_name as created_by_name')
            ->join('products p', 'bom.finished_product_id = p.id', 'left')
            ->join('users u', 'bom.created_by = u.id', 'left');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('p.product_name', $search)
                ->orLike('p.product_code', $search)
                ->orLike('bom.description', $search)
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('bom.status', $filters['status']);
        }

        if (!empty($filters['finished_product_id'])) {
            $builder->where('bom.finished_product_id', $filters['finished_product_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('bom.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('bom.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('bom.created_at', 'DESC')->findAll();
    }

    /**
     * Get BOM statistics
     */
    public function getBOMStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'draft' => $this->where('status', 'draft')->countAllResults()
        ];

        // Count by finished product
        $products = $this->select('p.product_name, COUNT(*) as count')
            ->join('products p', 'bom.finished_product_id = p.id', 'left')
            ->groupBy('bom.finished_product_id')
            ->findAll();
        
        $stats['products'] = $products;

        return $stats;
    }

    /**
     * Create BOM with items
     */
    public function createBOMWithItems($data, $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert BOM
            $bomId = $this->insert($data);

            if (!$bomId) {
                throw new \Exception('Failed to create BOM');
            }

            // Insert BOM items
            $bomItemModel = new \App\Models\BOMItem();
            foreach ($items as $item) {
                $item['bom_id'] = $bomId;
                $bomItemModel->insert($item);
            }

            // Calculate and update total cost
            $totalCost = $this->calculateBOMCost($bomId);
            $this->update($bomId, ['total_cost' => $totalCost]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $bomId;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Update BOM with items
     */
    public function updateBOMWithItems($bomId, $data, $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update BOM
            $this->update($bomId, $data);

            // Delete existing items
            $bomItemModel = new \App\Models\BOMItem();
            $bomItemModel->deleteItemsByBOMId($bomId);

            // Insert new items
            foreach ($items as $item) {
                $item['bom_id'] = $bomId;
                $bomItemModel->insert($item);
            }

            // Calculate and update total cost
            $totalCost = $this->calculateBOMCost($bomId);
            $this->update($bomId, ['total_cost' => $totalCost]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Calculate BOM cost
     */
    public function calculateBOMCost($bomId)
    {
        $bomItemModel = new \App\Models\BOMItem();
        $items = $bomItemModel->getBOMItems($bomId);

        $totalCost = 0;
        foreach ($items as $item) {
            $totalCost += $item['total_cost'];
        }

        return $totalCost;
    }

    /**
     * Calculate finished product cost
     */
    public function calculateFinishedProductCost($finishedProductId)
    {
        $bom = $this->getBOMByFinishedProduct($finishedProductId);
        
        if (!$bom) {
            return 0;
        }

        return $bom['total_cost'];
    }

    /**
     * Get material requirements for production
     */
    public function getMaterialRequirements($finishedProductId, $quantity)
    {
        $bom = $this->getBOMWithItemsByFinishedProduct($finishedProductId);
        
        if (!$bom) {
            return [];
        }

        $requirements = [];
        foreach ($bom['items'] as $item) {
            $requiredQuantity = $item['quantity_required'] * $quantity;
            
            $requirements[] = [
                'material_id' => $item['material_id'],
                'material_name' => $item['material_name'],
                'material_code' => $item['material_code'],
                'quantity_required' => $requiredQuantity,
                'unit' => $item['unit'],
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $requiredQuantity * $item['unit_cost']
            ];
        }

        return $requirements;
    }

    /**
     * Check material availability for production
     */
    public function checkMaterialAvailability($finishedProductId, $quantity)
    {
        $requirements = $this->getMaterialRequirements($finishedProductId, $quantity);
        $stockModel = new \App\Models\Stock();
        
        $availability = [];
        $canProduce = true;

        foreach ($requirements as $requirement) {
            $currentStock = $stockModel->getCurrentStock($requirement['material_id']);
            $available = $currentStock >= $requirement['quantity_required'];
            
            $availability[] = [
                'material_id' => $requirement['material_id'],
                'material_name' => $requirement['material_name'],
                'required_quantity' => $requirement['quantity_required'],
                'available_quantity' => $currentStock,
                'available' => $available,
                'shortage' => max(0, $requirement['quantity_required'] - $currentStock)
            ];

            if (!$available) {
                $canProduce = false;
            }
        }

        return [
            'can_produce' => $canProduce,
            'availability' => $availability
        ];
    }

    /**
     * Get BOM version history
     */
    public function getBOMVersionHistory($finishedProductId)
    {
        return $this->select('bom.*, p.product_name, u.full_name as created_by_name')
            ->join('products p', 'bom.finished_product_id = p.id', 'left')
            ->join('users u', 'bom.created_by = u.id', 'left')
            ->where('bom.finished_product_id', $finishedProductId)
            ->orderBy('bom.version', 'DESC')
            ->findAll();
    }

    /**
     * Generate new version number
     */
    public function generateVersionNumber($finishedProductId)
    {
        $lastBOM = $this->where('finished_product_id', $finishedProductId)
            ->orderBy('version', 'DESC')
            ->first();

        if (!$lastBOM) {
            return '1.0';
        }

        $versionParts = explode('.', $lastBOM['version']);
        $major = intval($versionParts[0]);
        $minor = intval(isset($versionParts[1]) ? $versionParts[1] : 0);

        return ($major + 1) . '.0';
    }

    /**
     * Copy BOM to new version
     */
    public function copyBOMToNewVersion($bomId, $newVersion)
    {
        $originalBOM = $this->getBOMWithItems($bomId);
        
        if (!$originalBOM) {
            return false;
        }

        $newBOMData = [
            'finished_product_id' => $originalBOM['finished_product_id'],
            'description' => $originalBOM['description'] . ' (Copy)',
            'version' => $newVersion,
            'status' => 'draft',
            'created_by' => session()->get('user_id') ?? 1
        ];

        $newItems = [];
        foreach ($originalBOM['items'] as $item) {
            unset($item['id'], $item['bom_id']);
            $newItems[] = $item;
        }

        return $this->createBOMWithItems($newBOMData, $newItems);
    }

    /**
     * Get BOM summary
     */
    public function getBOMSummary($bomId)
    {
        $bom = $this->getBOMWithItems($bomId);
        
        if (!$bom) {
            return null;
        }

        $totalItems = count($bom['items'] ?? []);
        $totalCost = $bom['total_cost'];

        return [
            'bom' => $bom,
            'total_items' => $totalItems,
            'total_cost' => $totalCost,
            'average_item_cost' => $totalItems > 0 ? $totalCost / $totalItems : 0
        ];
    }

    /**
     * Validate BOM completeness
     */
    public function validateBOM($bomId)
    {
        $bom = $this->getBOMWithItems($bomId);
        
        if (!$bom) {
            return ['valid' => false, 'message' => 'BOM not found'];
        }

        if (empty($bom['items'])) {
            return ['valid' => false, 'message' => 'BOM has no items'];
        }

        foreach ($bom['items'] as $item) {
            if ($item['quantity_required'] <= 0) {
                return ['valid' => false, 'message' => 'Invalid quantity for item: ' . $item['material_name']];
            }
        }

        return ['valid' => true, 'message' => 'BOM is valid'];
    }
} 