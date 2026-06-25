<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class VendorPaymentController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Vendor Payment Management',
            'page_title' => 'Vendor Payment Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Vendor Payment', 'url' => '']
            ]
        ];
        
        return view('vendor_payment/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Create Vendor Payment',
            'page_title' => 'Create Vendor Payment',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Vendor Payment', 'url' => base_url('vendor-payment')],
                ['title' => 'Create', 'url' => '']
            ]
        ];
        
        return view('vendor_payment/create', $data);
    }
    
    public function store()
    {
        // Handle form submission
        return redirect()->to('vendor-payment')->with('success', 'Vendor payment created successfully.');
    }
    
    public function show($id)
    {
        $data = [
            'title' => 'View Vendor Payment',
            'page_title' => 'View Vendor Payment',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Vendor Payment', 'url' => base_url('vendor-payment')],
                ['title' => 'View', 'url' => '']
            ]
        ];
        
        return view('vendor_payment/show', $data);
    }
    
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Vendor Payment',
            'page_title' => 'Edit Vendor Payment',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Vendor Payment', 'url' => base_url('vendor-payment')],
                ['title' => 'Edit', 'url' => '']
            ]
        ];
        
        return view('vendor_payment/edit', $data);
    }
    
    public function update($id)
    {
        // Handle form submission
        return redirect()->to('vendor-payment')->with('success', 'Vendor payment updated successfully.');
    }
    
    public function delete($id)
    {
        // Handle deletion
        return redirect()->to('vendor-payment')->with('success', 'Vendor payment deleted successfully.');
    }
    
    public function export()
    {
        // Handle export
        return redirect()->to('vendor-payment')->with('success', 'Data exported successfully.');
    }
}
