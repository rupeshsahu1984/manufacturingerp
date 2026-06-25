<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * Display list of products/materials
     */
    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'material_type' => $this->request->getGet('material_type'),
            'category_id' => $this->request->getGet('category_id'),
            'status' => $this->request->getGet('status'),
            'is_recyclable' => $this->request->getGet('is_recyclable'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Material Master - PRODX',
            'products' => $this->productModel->getProducts($filters),
            'stats' => $this->productModel->getProductStats(),
            'filters' => $filters
        ];

        return view('product/index', $data);
    }

    /**
     * Show product creation form
     */
    public function create()
    {
        $data = [
            'title' => 'Create Material - PRODX',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('product/create', $data);
    }

    /**
     * Store new product/material
     */
    public function store()
    {
        $rules = [
            'product_name' => 'required|min_length[3]|max_length[255]',
            'category_id' => 'required|integer',
            'unit' => 'required|max_length[20]',
            'material_type' => 'required|in_list[raw_material,packaging,finished_goods,waste]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        
        // Generate product code
        $data['product_code'] = $this->productModel->generateProductCode($data['product_name'], $data['material_type']);
        $data['created_by'] = session()->get('user_id') ?? 1;

        try {
            $this->productModel->insert($data);
            return redirect()->to('product')->with('success', 'Material created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating material: ' . $e->getMessage());
        }
    }

    /**
     * Show product details
     */
    public function show($id = null)
    {
        $product = $this->productModel->getProductWithBOM($id);
        
        if (!$product) {
            return redirect()->to('product')->with('error', 'Material not found!');
        }

        $data = [
            'title' => 'Material Details - PRODX',
            'product' => $product
        ];

        return view('product/show', $data);
    }

    /**
     * Show product edit form
     */
    public function edit($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return redirect()->to('product')->with('error', 'Material not found!');
        }

        $data = [
            'title' => 'Edit Material - PRODX',
            'product' => $product,
            'categories' => $this->categoryModel->findAll()
        ];

        return view('product/edit', $data);
    }

    /**
     * Update product
     */
    public function update($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return redirect()->to('product')->with('error', 'Material not found!');
        }

        $rules = [
            'product_name' => 'required|min_length[3]|max_length[255]',
            'category_id' => 'required|integer',
            'unit' => 'required|max_length[20]',
            'material_type' => 'required|in_list[raw_material,packaging,finished_goods,waste]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        try {
            $this->productModel->update($id, $data);
            return redirect()->to('product')->with('success', 'Material updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating material: ' . $e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function delete($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return redirect()->to('product')->with('error', 'Material not found!');
        }

        // Check if product has any related records
        // Add checks for BOM, stock, purchase orders, etc.

        try {
            $this->productModel->delete($id);
            return redirect()->to('product')->with('success', 'Material deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->to('product')->with('error', 'Error deleting material: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product status
     */
    public function toggleStatus($id = null)
    {
        if ($this->request->isAJAX()) {
            $product = $this->productModel->find($id);
            
            if (!$product) {
                return $this->response->setJSON(['success' => false, 'message' => 'Material not found']);
            }

            $newStatus = $product['status'] === 'active' ? 'inactive' : 'active';
            
            try {
                $this->productModel->update($id, ['status' => $newStatus]);
                return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error updating status']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    /**
     * Get products by material type
     */
    public function getProductsByMaterialType()
    {
        $materialType = $this->request->getGet('material_type');
        
        if (!$materialType) {
            return $this->response->setJSON(['success' => false, 'message' => 'Material type parameter required']);
        }

        $products = $this->productModel->getProductsByMaterialType($materialType);
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory()
    {
        $categoryId = $this->request->getGet('category_id');
        
        if (!$categoryId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category ID parameter required']);
        }

        $products = $this->productModel->getProductsByCategory($categoryId);
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Export products
     */
    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'material_type' => $this->request->getGet('material_type'),
            'category_id' => $this->request->getGet('category_id'),
            'status' => $this->request->getGet('status')
        ];

        $products = $this->productModel->getProducts($filters);

        // Set headers for CSV download
        $filename = 'materials_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'Product Code',
            'Product Name',
            'Description',
            'Category',
            'Unit',
            'Unit Price',
            'Reorder Level',
            'Material Type',
            'Waste Percentage',
            'Is Recyclable',
            'Status',
            'Created At'
        ]);

        // CSV data
        foreach ($products as $product) {
            fputcsv($output, [
                $product['product_code'],
                $product['product_name'],
                $product['description'],
                $product['category_name'],
                $product['unit'],
                $product['unit_price'],
                $product['reorder_level'],
                $product['material_type'],
                $product['waste_percentage'],
                $product['is_recyclable'] ? 'Yes' : 'No',
                $product['status'],
                $product['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Search products for AJAX
     */
    public function searchProducts()
    {
        $search = $this->request->getGet('search');
        $materialType = $this->request->getGet('material_type');
        
        if (!$search) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search term required']);
        }

        $products = $this->productModel->searchProducts($search, $materialType);
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get product performance report
     */
    public function performanceReport($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return redirect()->to('product')->with('error', 'Material not found!');
        }

        try {
            $performance = $this->productModel->getProductPerformance($id);
        } catch (\Throwable $e) {
            log_message('error', 'ProductController::performanceReport: ' . $e->getMessage());
            $performance = [];
        }

        $data = [
            'title' => 'Material Performance Report - PRODX',
            'product' => $product,
            'performance' => $performance
        ];

        return view('product/performance_report', $data);
    }

    /**
     * Get products for BOM
     */
    public function getProductsForBOM()
    {
        $products = $this->productModel->getProductsForBOM();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get finished goods for BOM creation
     */
    public function getFinishedGoods()
    {
        $products = $this->productModel->getFinishedGoods();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get finished goods for dropdown selection
     */
    public function getFinishedGoodsForDropdown()
    {
        $products = $this->productModel->getFinishedGoodsForDropdown();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get product stock by ID
     */
    public function getStock($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product ID required']);
        }

        try {
            // For now, return a default stock value
            // You can integrate with your actual stock system later
            $stock = 100; // Default stock value
            
            return $this->response->setJSON([
                'success' => true, 
                'stock' => $stock,
                'message' => 'Stock fetched successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error fetching stock: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get product details for auto-filling
     */
    public function getProductDetails($id)
    {
        $product = $this->productModel->getProductDetailsForAutoFill($id);
        
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
        }
        
        return $this->response->setJSON(['success' => true, 'product' => $product]);
    }

    /**
     * Get waste materials
     */
    public function getWasteMaterials()
    {
        $products = $this->productModel->getWasteMaterials();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get sales materials (produced materials and waste materials)
     */
    public function getSalesMaterials()
    {
        $products = $this->productModel->getSalesMaterials();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get products with stock information
     */
    public function getProductsWithStock()
    {
        $products = $this->productModel->getProductsWithStock();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    /**
     * Get products below reorder level
     */
    public function getProductsBelowReorderLevel()
    {
        $products = $this->productModel->getProductsBelowReorderLevel();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }
} 
