<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\PurchaseRequisition;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $productModel;
    protected $purchaseRequisitionModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->purchaseRequisitionModel = new PurchaseRequisition();
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in') || !session()->get('user_id')) {
            session()->setFlashdata('error', 'Your session has expired. Please login again.');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Dashboard - PRODX',
            'stats' => $this->getDashboardStats(),
            'recent_activities' => $this->getRecentActivities(),
            'user' => [
                'name' => session()->get('user_name') ? session()->get('user_name') : 'User',
                'role' => session()->get('user_role') ? session()->get('user_role') : 'User',
                'last_visited' => date('Y-m-d H:i:s')
            ]
        ];

        return view('dashboard/index', $data);
    }

    private function getDashboardStats()
    {
        // Get basic statistics
        $stats = [
            'total_orders' => $this->getTotalOrders(),
            'production_units' => $this->getProductionUnits(),
            'revenue' => $this->getRevenue(),
            'active_employees' => $this->getActiveEmployees(),
            'pending_prs' => $this->getPendingPRs(),
            'low_stock_products' => $this->getLowStockProducts(),
            'total_customers' => $this->getTotalCustomers(),
            'total_suppliers' => $this->getTotalSuppliers()
        ];

        return $stats;
    }

    private function getTotalOrders()
    {
        // This would come from sales_orders table
        // For now, return a sample number
        return 156;
    }

    private function getProductionUnits()
    {
        // This would come from work_orders table
        // For now, return a sample number
        return 1250;
    }

    private function getRevenue()
    {
        // This would come from invoices table
        // For now, return a sample number
        return 25000000; // 25M
    }

    private function getActiveEmployees()
    {
        // Get from users table where role is employee and status is active
        return $this->userModel->where('status', 'active')->countAllResults();
    }

    private function getPendingPRs()
    {
        // Get pending purchase requisitions
        return $this->purchaseRequisitionModel->where('status', 'pending')->countAllResults();
    }

    private function getLowStockProducts()
    {
        // Get products with low stock
        return $this->productModel->where('status', 'active')
            ->where('min_stock > 0')
            ->countAllResults();
    }

    private function getTotalCustomers()
    {
        // This would come from customers table
        // For now, return a sample number
        return 45;
    }

    private function getTotalSuppliers()
    {
        // This would come from suppliers table
        // For now, return a sample number
        return 23;
    }

    private function getRecentActivities()
    {
        // This would come from an activities/audit log table
        // For now, return sample activities
        return [
            [
                'description' => 'New Purchase Requisition created',
                'user' => 'John Doe',
                'time' => '2 minutes ago',
                'status' => 'Completed',
                'status_color' => 'success'
            ],
            [
                'description' => 'Sales Order #SO-2025-001 approved',
                'user' => 'Jane Smith',
                'time' => '15 minutes ago',
                'status' => 'Completed',
                'status_color' => 'success'
            ],
            [
                'description' => 'Work Order #WO-2025-005 started',
                'user' => 'Mike Johnson',
                'time' => '1 hour ago',
                'status' => 'In Progress',
                'status_color' => 'warning'
            ],
            [
                'description' => 'Gate Entry #GE-2025-003 completed',
                'user' => 'Sarah Wilson',
                'time' => '2 hours ago',
                'status' => 'Completed',
                'status_color' => 'success'
            ],
            [
                'description' => 'Quality Check failed for Batch #QC-2025-002',
                'user' => 'David Brown',
                'time' => '3 hours ago',
                'status' => 'Failed',
                'status_color' => 'danger'
            ],
            [
                'description' => 'New Employee added to system',
                'user' => 'HR Manager',
                'time' => '4 hours ago',
                'status' => 'Completed',
                'status_color' => 'success'
            ]
        ];
    }
} 
