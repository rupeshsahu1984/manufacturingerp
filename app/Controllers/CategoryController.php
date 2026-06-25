<?php

namespace App\Controllers;

use App\Models\Category;
use Exception;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'type' => $this->request->getGet('type'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Category Master - PRODX',
            'categories' => $this->categoryModel->getCategories($filters),
            'stats' => $this->categoryModel->getCategoryStats(),
            'filters' => $filters
        ];

        return view('category/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Category - PRODX'
        ];

        return view('category/create', $data);
    }

    public function store()
    {
        $rules = [
            'category_name' => 'required|max_length[100]|is_unique[categories.category_name]',
            'category_type' => 'required|in_list[material,supplier,customer,product,service]',
            'description' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_name' => $this->request->getPost('category_name'),
            'category_type' => $this->request->getPost('category_type'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->categoryModel->insert($data)) {
            return redirect()->to('category')->with('success', 'Category created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create category');
    }

    public function show($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('category')->with('error', 'Category not found');
        }

        $data = [
            'title' => 'View Category - PRODX',
            'category' => $category,
            'usage_count' => $this->categoryModel->getCategoryUsageCount($id)
        ];

        return view('category/show', $data);
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('category')->with('error', 'Category not found');
        }

        $data = [
            'title' => 'Edit Category - PRODX',
            'category' => $category
        ];

        return view('category/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('category')->with('error', 'Category not found');
        }

        $rules = [
            'category_name' => 'required|max_length[100]|is_unique[categories.category_name,id,' . $id . ']',
            'category_type' => 'required|in_list[material,supplier,customer,product,service]',
            'description' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_name' => $this->request->getPost('category_name'),
            'category_type' => $this->request->getPost('category_type'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id') ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('category')->with('success', 'Category updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update category');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('category')->with('error', 'Category not found');
        }

        // Check if category is being used
        if ($this->categoryModel->isCategoryInUse($id)) {
            return redirect()->to('category')->with('error', 'Cannot delete category as it is being used');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to('category')->with('success', 'Category deleted successfully');
        }

        return redirect()->to('category')->with('error', 'Failed to delete category');
    }

    public function toggleStatus($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('category')->with('error', 'Category not found');
        }

        $newStatus = $category['status'] === 'active' ? 'inactive' : 'active';
        
        if ($this->categoryModel->update($id, ['status' => $newStatus])) {
            return redirect()->to('category')->with('success', 'Category status updated successfully');
        }

        return redirect()->to('category')->with('error', 'Failed to update category status');
    }

    public function getCategoriesByType()
    {
        $type = $this->request->getGet('type');
        
        if (!$type) {
            return $this->response->setJSON(['success' => false, 'message' => 'Type parameter is required']);
        }

        $categories = $this->categoryModel->where('category_type', $type)
            ->where('status', 'active')
            ->orderBy('category_name', 'ASC')
            ->findAll();

        return $this->response->setJSON(['success' => true, 'categories' => $categories]);
    }

    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'type' => $this->request->getGet('type'),
            'status' => $this->request->getGet('status')
        ];

        $categories = $this->categoryModel->getCategories($filters);
        
        // Generate CSV
        $filename = 'categories_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['ID', 'Category Name', 'Type', 'Description', 'Status', 'Created At']);
        
        // CSV data
        foreach ($categories as $category) {
            fputcsv($output, [
                $category['id'],
                $category['category_name'],
                $category['category_type'],
                $category['description'],
                $category['status'],
                $category['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
