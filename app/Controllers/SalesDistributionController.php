<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Quotation;
use App\Models\DispatchNote;
use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Models\CustomerPayment;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Lead;
use App\Models\CustomerCommunication;
use App\Models\CustomerNote;

class SalesDistributionController extends BaseController
{
    protected $customerModel;
    protected $salesOrderModel;
    protected $quotationModel;
    protected $dispatchModel;
    protected $invoiceModel;
    protected $returnModel;
    protected $paymentModel;
    protected $distributorModel;
    protected $productModel;
    protected $stockModel;
    protected $leadModel;
    protected $communicationModel;
    protected $noteModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
        $this->salesOrderModel = new SalesOrder();
        $this->quotationModel = new Quotation();
        $this->dispatchModel = new DispatchNote();
        $this->invoiceModel = new Invoice();
        $this->returnModel = new SalesReturn();
        $this->paymentModel = new CustomerPayment();
        $this->distributorModel = new Distributor();
        $this->productModel = new Product();
        $this->stockModel = new Stock();
        $this->leadModel = new Lead();
        $this->communicationModel = new CustomerCommunication();
        $this->noteModel = new CustomerNote();
    }

    /**
     * Route missing CRM methods to a working hub page (links to standalone modules).
     */
    public function _remap($method, ...$params)
    {
        $direct = ['index', 'orderCreate', 'orderStore', 'apiGetProductStock', 'apiCheckStockAvailability'];
        if (in_array($method, $direct, true) && method_exists($this, $method)) {
            return $this->{$method}(...$params);
        }
        if (str_starts_with((string) $method, 'api') && method_exists($this, $method)) {
            return $this->{$method}(...$params);
        }

        return $this->salesModuleFallback($method, $params);
    }

    /**
     * Sales & Distribution Dashboard
     */
    public function index()
    {
        $defaults = [
            'title' => 'Sales & Distribution Dashboard - PRODX',
            'totalCustomers' => 0,
            'totalLeads' => 0,
            'pendingQuotations' => 0,
            'pendingOrders' => 0,
            'pendingDispatch' => 0,
            'pendingInvoices' => 0,
            'outstandingPayments' => 0,
            'monthlySales' => [],
            'topCustomers' => [],
            'topProducts' => [],
            'recentActivities' => [],
        ];

        try {
            $defaults['totalCustomers'] = $this->customerModel->countAll();
            $defaults['totalLeads'] = $this->leadModel->countAll();
            $defaults['pendingQuotations'] = $this->quotationModel->where('status', 'pending')->countAllResults();
            $defaults['pendingOrders'] = $this->salesOrderModel->where('status', 'pending')->countAllResults();
            $defaults['pendingDispatch'] = $this->dispatchModel->where('status', 'pending')->countAllResults();
            $defaults['pendingInvoices'] = $this->invoiceModel->where('status', 'pending')->countAllResults();
            $defaults['outstandingPayments'] = $this->paymentModel->where('status', 'pending')->countAllResults();
            $defaults['monthlySales'] = method_exists($this->salesOrderModel, 'getMonthlySales')
                ? $this->salesOrderModel->getMonthlySales() : [];
            $defaults['topCustomers'] = method_exists($this->customerModel, 'getTopCustomers')
                ? $this->customerModel->getTopCustomers() : [];
            $defaults['topProducts'] = method_exists($this->productModel, 'getTopSellingProducts')
                ? $this->productModel->getTopSellingProducts() : [];
            $defaults['recentActivities'] = $this->getRecentActivities();
        } catch (\Throwable $e) {
            log_message('error', 'SalesDistributionController::index: ' . $e->getMessage());
            $defaults['dashboard_error'] = 'Some dashboard metrics could not be loaded (check related database tables).';
        }

        return view('sales_distribution/dashboard', $defaults);
    }

    private function getRecentActivities()
    {
        $activities = [];

        try {
            $recentOrders = $this->salesOrderModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
            foreach ($recentOrders as $order) {
                $cust = $order['customer_name'] ?? ('#' . ($order['customer_id'] ?? ''));
                $activities[] = [
                    'type' => 'order',
                    'description' => 'New order ' . ($order['order_number'] ?? '') . ' from ' . $cust,
                    'time' => $order['created_at'] ?? '',
                    'status' => $order['status'] ?? '',
                ];
            }
        } catch (\Throwable $e) {
            log_message('error', 'getRecentActivities orders: ' . $e->getMessage());
        }

        try {
            $recentPayments = $this->paymentModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
            foreach ($recentPayments as $payment) {
                $cust = $payment['customer_name'] ?? ('#' . ($payment['customer_id'] ?? ''));
                $activities[] = [
                    'type' => 'payment',
                    'description' => 'Payment of ₹' . number_format((float) ($payment['amount'] ?? 0)) . ' from ' . $cust,
                    'time' => $payment['created_at'] ?? '',
                    'status' => $payment['status'] ?? '',
                ];
            }
        } catch (\Throwable $e) {
            log_message('error', 'getRecentActivities payments: ' . $e->getMessage());
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Create Sales Order with Stock Check
     */
    public function orderCreate()
    {
        $data = [
            'title' => 'Create Sales Order - PRODX',
            'customers' => $this->customerModel->findAll(),
            'products' => $this->productModel->where('material_type', 'finished_goods')->findAll(),
            'warehouses' => $this->stockModel->getWarehouses()
        ];

        return view('sales_distribution/order_create', $data);
    }

    /**
     * Store Sales Order with Stock Validation
     */
    public function orderStore()
    {
        $rules = [
            'customer_id' => 'required|integer',
            'order_date' => 'required|valid_date',
            'delivery_date' => 'required|valid_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|greater_than[0]',
            'items.*.unit_price' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');

        // Check stock availability
        $stockIssues = [];
        for ($i = 0; $i < count($items); $i++) {
            if (!empty($items[$i])) {
                $availableStock = $this->stockModel->getAvailableStock($items[$i]);
                if ($availableStock < $quantities[$i]) {
                    $product = $this->productModel->find($items[$i]);
                    $stockIssues[] = $product['product_name'] . ' - Required: ' . $quantities[$i] . ', Available: ' . $availableStock;
                }
            }
        }

        if (!empty($stockIssues)) {
            return redirect()->back()->withInput()->with('stock_issues', $stockIssues);
        }

        // Create sales order
        $orderData = [
            'order_number' => $this->generateOrderNumber(),
            'customer_id' => $this->request->getPost('customer_id'),
            'order_date' => $this->request->getPost('order_date'),
            'delivery_date' => $this->request->getPost('delivery_date'),
            'status' => 'pending',
            'created_by' => session()->get('user_id') ?? 1
        ];

        try {
            $orderId = $this->salesOrderModel->insert($orderData);
            
            if ($orderId) {
                // Create order items
                for ($i = 0; $i < count($items); $i++) {
                    if (!empty($items[$i])) {
                        $itemData = [
                            'sales_order_id' => $orderId,
                            'product_id' => $items[$i],
                            'quantity' => $quantities[$i],
                            'unit_price' => $this->request->getPost('unit_prices')[$i],
                            'line_total' => $quantities[$i] * $this->request->getPost('unit_prices')[$i],
                            'available_stock' => $this->stockModel->getAvailableStock($items[$i])
                        ];
                        
                        $this->salesOrderModel->createOrderItem($itemData);
                    }
                }

                return redirect()->to('sales-distribution/orders')->with('success', 'Sales order created successfully!');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create sales order.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $prefix = 'SO';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = $this->salesOrderModel->where('order_number LIKE', $prefix . $year . $month . '%')
                                          ->orderBy('order_number', 'DESC')
                                          ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder['order_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * API: Get product stock information
     */
    public function apiGetProductStock($productId)
    {
        $stockInfo = $this->stockModel->getItemStock($productId);
        $product = $this->productModel->find($productId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'product' => $product,
                'stock' => $stockInfo
            ]
        ]);
    }

    /**
     * API: Check stock availability for multiple products
     */
    public function apiCheckStockAvailability()
    {
        $items = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');
        
        $stockCheck = [];
        $allAvailable = true;
        
        for ($i = 0; $i < count($items); $i++) {
            if (!empty($items[$i])) {
                $availableStock = $this->stockModel->getAvailableStock($items[$i]);
                $required = $quantities[$i];
                $available = $availableStock >= $required;
                
                $stockCheck[] = [
                    'product_id' => $items[$i],
                    'required' => $required,
                    'available' => $availableStock,
                    'sufficient' => $available,
                    'shortage' => max(0, $required - $availableStock)
                ];
                
                if (!$available) {
                    $allAvailable = false;
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'all_available' => $allAvailable,
            'stock_check' => $stockCheck
        ]);
    }

    /**
     * Placeholder for grouped /sales/* CRM routes not yet implemented as full CRUD.
     */
    private function salesModuleFallback(string $method, array $params)
    {
        if (str_starts_with($method, 'api')) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'message' => 'Endpoint reserved; use standalone pages or extend SalesDistributionController.',
            ]);
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            return redirect()->back()->with(
                'info',
                'Use the main menu entries (Customer, Sales Orders, Quotations, Invoices, etc.) for this action.'
            );
        }

        $links = [
            ['Customer master', base_url('customer')],
            ['Sales orders', base_url('sales-orders')],
            ['Quotations', base_url('quotation')],
            ['Invoices', base_url('invoice')],
            ['Sales invoice (alias)', base_url('sales-invoice')],
            ['Dispatch', base_url('dispatch')],
            ['Returns', base_url('sales-return')],
            ['Customer payments', base_url('customer-payment')],
            ['Sales & CRM dashboard', base_url('sales')],
        ];

        return view('sales_distribution/hub_stub', [
            'title' => 'Sales — ' . $method,
            'page_title' => str_replace('_', ' ', $method),
            'method' => $method,
            'params' => $params,
            'links' => $links,
        ]);
    }
}
