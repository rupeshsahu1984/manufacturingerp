<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Exception;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $productModel;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'category' => $this->request->getGet('category'),
            'status' => $this->request->getGet('status')
        ];

        $data = [
            'title' => 'Supplier Master - PRODX',
            'suppliers' => $this->supplierModel->getSuppliers($filters),
            'stats' => $this->supplierModel->getSupplierStats(),
            'filters' => $filters
        ];

        return view('supplier/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Supplier - PRODX',
            'supplier_code' => $this->supplierModel->generateSupplierCode()
        ];

        return view('supplier/create', $data);
    }

    public function store()
    {
        $rules = [
            'supplier_code' => 'required|is_unique[suppliers.supplier_code]',
            'supplier_name' => 'required|max_length[100]',
            'contact_person' => 'required|max_length[100]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'required|max_length[20]',
            'address' => 'required',
            'city' => 'required|max_length[50]',
            'state' => 'required|max_length[50]',
            'pincode' => 'required|max_length[10]',
            'gst_number' => 'permit_empty|max_length[20]',
            'pan_number' => 'permit_empty|max_length[20]',
            'bank_name' => 'permit_empty|max_length[100]',
            'bank_account' => 'permit_empty|max_length[50]',
            'bank_ifsc' => 'permit_empty|max_length[20]',
            'payment_terms' => 'permit_empty|max_length[100]',
            'credit_limit' => 'permit_empty|numeric',
            'supplier_category' => 'required|in_list[raw_material,packaging,service]',
            'return_policy' => 'permit_empty',
            'credit_terms' => 'permit_empty|max_length[100]',
            'website' => 'permit_empty|valid_url'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'supplier_code' => $this->request->getPost('supplier_code'),
            'supplier_name' => $this->request->getPost('supplier_name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'pincode' => $this->request->getPost('pincode'),
            'gst_number' => $this->request->getPost('gst_number'),
            'pan_number' => $this->request->getPost('pan_number'),
            'bank_name' => $this->request->getPost('bank_name'),
            'bank_account' => $this->request->getPost('bank_account'),
            'bank_ifsc' => $this->request->getPost('bank_ifsc'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'credit_limit' => $this->request->getPost('credit_limit') ?: 0,
            'supplier_category' => $this->request->getPost('supplier_category'),
            'return_policy' => $this->request->getPost('return_policy'),
            'credit_terms' => $this->request->getPost('credit_terms'),
            'website' => $this->request->getPost('website'),
            'status' => $this->request->getPost('status') ?: 'active'
        ];

        try {
            if ($this->supplierModel->insert($data)) {
                return redirect()->to('supplier')->with('success', 'Supplier created successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create supplier');
            }
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating supplier: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $supplier = $this->supplierModel->getSupplierWithHistory($id);
        
        if (!$supplier) {
            return redirect()->to('supplier')->with('error', 'Supplier not found');
        }

        $data = [
            'title' => 'View Supplier - PRODX',
            'supplier' => $supplier,
            'performance' => $this->supplierModel->getSupplierPerformance($id)
        ];

        return view('supplier/show', $data);
    }

    public function edit($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            return redirect()->to('supplier')->with('error', 'Supplier not found');
        }

        $data = [
            'title' => 'Edit Supplier - PRODX',
            'supplier' => $supplier
        ];

        return view('supplier/edit', $data);
    }

    public function update($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            return redirect()->to('supplier')->with('error', 'Supplier not found');
        }

        $rules = [
            'supplier_code' => 'required|is_unique[suppliers.supplier_code,id,' . $id . ']',
            'supplier_name' => 'required|max_length[100]',
            'contact_person' => 'permit_empty|max_length[100]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty',
            'gst_number' => 'permit_empty|max_length[20]',
            'pan_number' => 'permit_empty|max_length[20]',
            'bank_name' => 'permit_empty|max_length[100]',
            'bank_account' => 'permit_empty|max_length[50]',
            'bank_ifsc' => 'permit_empty|max_length[20]',
            'payment_terms' => 'permit_empty|max_length[100]',
            'credit_limit' => 'permit_empty|numeric',
            'supplier_category' => 'required|in_list[raw_material,packaging,service]',
            'return_policy' => 'permit_empty',
            'credit_terms' => 'permit_empty|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'supplier_code' => $this->request->getPost('supplier_code'),
            'supplier_name' => $this->request->getPost('supplier_name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'gst_number' => $this->request->getPost('gst_number'),
            'pan_number' => $this->request->getPost('pan_number'),
            'bank_name' => $this->request->getPost('bank_name'),
            'bank_account' => $this->request->getPost('bank_account'),
            'bank_ifsc' => $this->request->getPost('bank_ifsc'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'credit_limit' => $this->request->getPost('credit_limit') ?: 0,
            'supplier_category' => $this->request->getPost('supplier_category'),
            'return_policy' => $this->request->getPost('return_policy'),
            'credit_terms' => $this->request->getPost('credit_terms')
        ];

        if ($this->supplierModel->update($id, $data)) {
            return redirect()->to('supplier')->with('success', 'Supplier updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update supplier');
    }

    public function delete($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            return redirect()->to('supplier')->with('error', 'Supplier not found');
        }

        // Check if supplier has any related records
        if ($this->supplierModel->hasOutstandingPayments($id)) {
            return redirect()->to('supplier')->with('error', 'Cannot delete supplier with outstanding payments');
        }

        if ($this->supplierModel->delete($id)) {
            return redirect()->to('supplier')->with('success', 'Supplier deleted successfully');
        }

        return redirect()->to('supplier')->with('error', 'Failed to delete supplier');
    }

    public function toggleStatus($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            return $this->response->setJSON(['success' => false, 'message' => 'Supplier not found']);
        }

        $newStatus = $supplier['status'] === 'active' ? 'inactive' : 'active';
        
        if ($this->supplierModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Supplier status updated successfully',
                'new_status' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update supplier status']);
    }

    public function getSuppliersByCategory()
    {
        $category = $this->request->getGet('category');
        
        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category is required']);
        }

        $suppliers = $this->supplierModel->getSuppliersByCategory($category);
        
        return $this->response->setJSON(['success' => true, 'suppliers' => $suppliers]);
    }

    public function getOutstandingPayments()
    {
        $suppliers = $this->supplierModel->getSuppliersWithOutstandingPayments();
        
        $data = [
            'title' => 'Outstanding Payments - PRODX',
            'suppliers' => $suppliers
        ];

        return view('supplier/outstanding_payments', $data);
    }

    public function print($id)
    {
        $supplier = $this->supplierModel->getSupplierWithHistory($id);
        
        if (!$supplier) {
            return redirect()->to('supplier')->with('error', 'Supplier not found');
        }

        $data = [
            'title' => 'Print Supplier Details - PRODX',
            'supplier' => $supplier,
            'performance' => $this->supplierModel->getSupplierPerformance($id)
        ];

        return view('supplier/print', $data);
    }

    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'category' => $this->request->getGet('category'),
            'status' => $this->request->getGet('status')
        ];

        $suppliers = $this->supplierModel->getSuppliers($filters);
        
        // Generate CSV
        $filename = 'suppliers_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Supplier Code',
            'Supplier Name',
            'Contact Person',
            'Email',
            'Phone',
            'GST Number',
            'PAN Number',
            'Category',
            'Credit Limit',
            'Status'
        ]);
        
        // CSV data
        foreach ($suppliers as $supplier) {
            fputcsv($output, [
                $supplier['supplier_code'],
                $supplier['supplier_name'],
                $supplier['contact_person'],
                $supplier['email'],
                $supplier['phone'],
                $supplier['gst_number'],
                $supplier['pan_number'],
                $supplier['supplier_category'],
                $supplier['credit_limit'],
                $supplier['status']
            ]);
        }
        
        fclose($output);
        exit;
    }
} 