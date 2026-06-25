<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class PurchaseOrder extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Purchase Order',
            'orders' => [
                ['id' => 1, 'po_number' => 'PO-2025-001', 'supplier' => 'ABC Suppliers', 'total_amount' => 250000, 'status' => 'Active', 'created_date' => '2025-01-15'],
                ['id' => 2, 'po_number' => 'PO-2025-002', 'supplier' => 'XYZ Corporation', 'total_amount' => 180000, 'status' => 'Delivered', 'created_date' => '2025-01-14'],
                ['id' => 3, 'po_number' => 'PO-2025-003', 'supplier' => 'DEF Industries', 'total_amount' => 320000, 'status' => 'Pending', 'created_date' => '2025-01-13'],
            ]
        ];
        
        return view('purchase_order/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Purchase Order'
        ];
        
        return view('purchase_order/create', $data);
    }

    public function store()
    {
        // Handle form submission
        return redirect()->to('purchase-order')->with('success', 'Purchase order created successfully');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Purchase Order',
            'order' => [
                'id' => $id,
                'po_number' => 'PO-2025-001',
                'supplier' => 'ABC Suppliers',
                'total_amount' => 250000,
                'status' => 'Active'
            ]
        ];
        
        return view('purchase_order/edit', $data);
    }

    public function update($id)
    {
        // Handle form submission
        return redirect()->to('purchase-order')->with('success', 'Purchase order updated successfully');
    }

    public function delete($id)
    {
        // Handle deletion
        return redirect()->to('purchase-order')->with('success', 'Purchase order deleted successfully');
    }
} 