<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MaintenanceController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Maintenance Management',
            'page_title' => 'Maintenance Management',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Maintenance', 'url' => '']
            ]
        ];
        
        return view('maintenance/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Create Maintenance Record',
            'page_title' => 'Create Maintenance Record',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Maintenance', 'url' => base_url('maintenance')],
                ['title' => 'Create', 'url' => '']
            ]
        ];
        
        return view('maintenance/create', $data);
    }
    
    public function store()
    {
        // Handle form submission
        return redirect()->to('maintenance')->with('success', 'Maintenance record created successfully.');
    }
    
    public function show($id)
    {
        $data = [
            'title' => 'View Maintenance Record',
            'page_title' => 'View Maintenance Record',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Maintenance', 'url' => base_url('maintenance')],
                ['title' => 'View', 'url' => '']
            ]
        ];
        
        return view('maintenance/show', $data);
    }
    
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Maintenance Record',
            'page_title' => 'Edit Maintenance Record',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')],
                ['title' => 'Maintenance', 'url' => base_url('maintenance')],
                ['title' => 'Edit', 'url' => '']
            ]
        ];
        
        return view('maintenance/edit', $data);
    }
    
    public function update($id)
    {
        // Handle form submission
        return redirect()->to('maintenance')->with('success', 'Maintenance record updated successfully.');
    }
    
    public function delete($id)
    {
        // Handle deletion
        return redirect()->to('maintenance')->with('success', 'Maintenance record deleted successfully.');
    }
    
    public function export()
    {
        // Handle export
        return redirect()->to('maintenance')->with('success', 'Data exported successfully.');
    }
}
