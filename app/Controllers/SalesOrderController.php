<?php

namespace App\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use App\Models\Customer;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class SalesOrderController extends BaseController
{
    protected $salesOrderModel;
    protected $salesOrderItemModel;
    protected $productModel;
    protected $customerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->salesOrderModel = new SalesOrder();
        $this->salesOrderItemModel = new SalesOrderItem();
        $this->productModel = new Product();
        $this->customerModel = new Customer();
    }

    public function index()
    {
        // Get filters from request
        $filters = [
            'search' => $this->request->getGet('search'),
            'customer' => $this->request->getGet('customer'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        // Get customers for filter dropdown
        $customers = $this->customerModel->select('id, customer_name')->findAll();

        $data = [
            'title' => 'Sales Orders',
            'sales_orders' => $this->salesOrderModel->getSalesOrdersWithDetails($filters),
            'customers' => $customers,
            'filters' => $filters
        ];
        
        return view('sales_order/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Sales Order',
            'so_number' => $this->salesOrderModel->generateUniqueSoNumber(),
            'products' => $this->productModel->getSalesMaterials(), // Only finished goods and waste materials
            'customers' => $this->customerModel->findAll()
        ];
        
        return view('sales_order/create_new', $data);
    }

    public function store()
    {
        // Validate input
        $rules = [
            'invoice_no' => 'required',
            'customer_id' => 'required|numeric',
            'order_date' => 'required|valid_date',
            'customer_address' => 'required',
            'customer_mobile' => 'required'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('errors', $errors)->with('error', 'Validation failed: ' . implode(', ', $errors));
        }
        
        // Validate items separately (CodeIgniter doesn't support 'array' as a validation rule)
        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product to the order.');
        }
        
        // Debug: Log received data
        log_message('debug', 'Sales Order Store - Received POST data: ' . json_encode($this->request->getPost()));

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate totals from items first
            $items = $this->request->getPost('items');
            $subtotal = 0;
            $discountTotal = 0;
            $gstAmount = 0;
            
            if (!empty($items) && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        $quantity = floatval($item['quantity']);
                        $unitPrice = floatval($item['unit_price'] ?? 0);
                        $discount = floatval($item['discount'] ?? 0);
                        $lineSubtotal = $quantity * $unitPrice;
                        $lineDiscount = $lineSubtotal * ($discount / 100);
                        $subtotal += $lineSubtotal;
                        $discountTotal += $lineDiscount;
                        // GST calculation can be added here if needed
                    }
                }
            }
            
            $totalAmount = $subtotal - $discountTotal + $gstAmount;
            
            // Create sales order - only use fields that exist in the database table
            $salesOrderData = [
                'so_number' => $this->request->getPost('invoice_no'),
                'customer_id' => intval($this->request->getPost('customer_id')),
                'order_date' => $this->request->getPost('order_date'),
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'created_by' => session()->get('user_id') ?? 1
            ];
            
            // Add optional fields if they exist in the table
            if ($this->request->getPost('delivery_date')) {
                $salesOrderData['delivery_date'] = $this->request->getPost('delivery_date');
            }
            if ($this->request->getPost('delivery_address')) {
                $salesOrderData['delivery_address'] = $this->request->getPost('delivery_address');
            }
            if ($this->request->getPost('payment_terms')) {
                $salesOrderData['payment_terms'] = $this->request->getPost('payment_terms');
            }

            log_message('debug', 'Sales Order Store - Order data: ' . json_encode($salesOrderData));
            
            // Use direct database insert to avoid model validation issues
            $builder = $db->table('sales_orders');
            
            // Insert using query builder directly
            if (!$builder->insert($salesOrderData)) {
                $error = $db->error();
                log_message('error', 'Sales Order Store - Failed to insert order: ' . json_encode($error));
                throw new \Exception('Failed to create sales order: ' . ($error['message'] ?? 'Unknown database error'));
            }
            
            $salesOrderId = $db->insertID();
            
            if (!$salesOrderId) {
                $error = $db->error();
                log_message('error', 'Sales Order Store - Failed to get insert ID: ' . json_encode($error));
                throw new \Exception('Failed to get sales order ID after insert');
            }
            
            log_message('debug', 'Sales Order Store - Order created with ID: ' . $salesOrderId);

            // Create sales order items (items already retrieved above for totals calculation)
            log_message('debug', 'Sales Order Store - Items received: ' . json_encode($items));
            
            if (empty($items) || !is_array($items)) {
                log_message('error', 'Sales Order Store - No items provided');
                throw new \Exception('No items provided in the order. Please add at least one product.');
            }
            
            $validItemsCount = 0;
            
            foreach ($items as $index => $item) {
                if (!empty($item['product_id']) && !empty($item['quantity'])) {
                    // Calculate totals based on actual database table structure
                    $quantity = floatval($item['quantity']);
                    $unitPrice = floatval($item['unit_price'] ?? 0);
                    $discount = floatval($item['discount'] ?? 0);
                    $subtotal = $quantity * $unitPrice;
                    $discountAmount = $subtotal * ($discount / 100);
                    $taxableAmount = $subtotal - $discountAmount;
                    
                    // GST calculation (default 18%)
                    $gstRate = 18.00;
                    $gstAmount = $taxableAmount * ($gstRate / 100);
                    $totalAmount = $taxableAmount + $gstAmount;
                    
                    // Use actual database column names
                    $itemData = [
                        'so_id' => $salesOrderId,
                        'product_id' => intval($item['product_id']),
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'gst_rate' => $gstRate,
                        'gst_amount' => $gstAmount,
                        'total_amount' => $totalAmount
                    ];

                    log_message('debug', "Sales Order Store - Inserting item $index: " . json_encode($itemData));
                    
                    // Use direct database insert to avoid model issues
                    $itemBuilder = $db->table('sales_order_items');
                    $itemBuilder->insert($itemData);
                    $itemInsertId = $db->insertID();
                    
                    if (!$itemInsertId) {
                        $error = $db->error();
                        log_message('error', "Sales Order Store - Failed to insert item $index: " . json_encode($error));
                        throw new \Exception('Failed to insert sales order item #' . ($index + 1) . ': ' . ($error['message'] ?? 'Unknown error'));
                    }
                    $validItemsCount++;
                } else {
                    log_message('debug', "Sales Order Store - Skipping invalid item $index: " . json_encode($item));
                }
            }
            
            if ($validItemsCount === 0) {
                throw new \Exception('No valid items found. Please ensure all products have a quantity greater than 0.');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Sales Order Store - Transaction failed');
                $dbError = $db->error();
                return redirect()->back()->withInput()->with('error', 'Failed to create sales order. Database error: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            log_message('info', "Sales Order Store - Successfully created order ID: $salesOrderId with $validItemsCount items");
            return redirect()->to('sales-order')->with('success', 'Sales order created successfully with ' . $validItemsCount . ' item(s).');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating sales order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $salesOrder = $this->salesOrderModel->getSalesOrderWithItems($id);
        
        if (!$salesOrder) {
            return redirect()->to('sales-order')->with('error', 'Sales order not found.');
        }

        // Calculate order summary
        $orderSummary = $this->calculateOrderSummary($salesOrder['items']);

        $data = [
            'title' => 'Sales Order Details',
            'sales_order' => $salesOrder,
            'order_items' => $salesOrder['items'],
            'order_summary' => $orderSummary
        ];
        
        return view('sales_order/show', $data);
    }

    public function edit($id)
    {
        $salesOrder = $this->salesOrderModel->getSalesOrderWithItems($id);
        
        if (!$salesOrder) {
            return redirect()->to('sales-order')->with('error', 'Sales order not found.');
        }

        $data = [
            'title' => 'Edit Sales Order',
            'sales_order' => $salesOrder,
            'order_items' => $salesOrder['items'],
            'products' => $this->productModel->findAll(),
            'customers' => $this->customerModel->findAll()
        ];
        
        return view('sales_order/edit', $data);
    }

    public function update($id)
    {
        // Validate input
        $rules = [
            'invoice_no' => 'required',
            'customer_id' => 'required|numeric',
            'order_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Validate items separately (CodeIgniter doesn't support 'array' as a validation rule)
        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product to the order.');
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate totals from items first
            $items = $this->request->getPost('items');
            $subtotal = 0;
            $gstAmount = 0;
            
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        $quantity = floatval($item['quantity']);
                        $unitPrice = floatval($item['unit_price'] ?? 0);
                        $lineSubtotal = $quantity * $unitPrice;
                        $subtotal += $lineSubtotal;
                        // GST calculation (default 18%)
                        $gstRate = 18.00;
                        $lineGstAmount = $lineSubtotal * ($gstRate / 100);
                        $gstAmount += $lineGstAmount;
                    }
                }
            }
            
            $totalAmount = $subtotal + $gstAmount;
            
            // Update sales order - include all fields from the form
            $salesOrderData = [
                'so_number' => $this->request->getPost('invoice_no') ?? '',
                'invoice_no' => $this->request->getPost('invoice_no') ?? '',
                'customer_id' => intval($this->request->getPost('customer_id')),
                'order_date' => $this->request->getPost('order_date'),
                'customer_address' => $this->request->getPost('customer_address') ?? '',
                'customer_mobile' => $this->request->getPost('customer_mobile') ?? '',
                'customer_gstn' => $this->request->getPost('customer_gstn') ?? '',
                'transport_amount' => floatval($this->request->getPost('transport_amount') ?? 0),
                'transport_tax' => floatval($this->request->getPost('transport_tax') ?? 0),
                'description' => $this->request->getPost('description') ?? '',
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'total_amount' => $totalAmount,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session()->get('user_id') ?? 1
            ];
            
            // Add optional fields
            if ($this->request->getPost('delivery_date')) {
                $salesOrderData['delivery_date'] = $this->request->getPost('delivery_date');
            }
            if ($this->request->getPost('delivery_address')) {
                $salesOrderData['delivery_address'] = $this->request->getPost('delivery_address');
            }
            if ($this->request->getPost('payment_terms')) {
                $salesOrderData['payment_terms'] = $this->request->getPost('payment_terms');
            }
            if ($this->request->getPost('status')) {
                $salesOrderData['status'] = $this->request->getPost('status');
            }

            $this->salesOrderModel->update($id, $salesOrderData);

            // Delete existing items
            $this->salesOrderItemModel->where('so_id', $id)->delete();

            // Create new sales order items - only use columns that exist in the database
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        $quantity = floatval($item['quantity']);
                        $unitPrice = floatval($item['unit_price'] ?? 0);
                        $lineSubtotal = $quantity * $unitPrice;
                        
                        // GST calculation (default 18%)
                        $gstRate = 18.00;
                        $lineGstAmount = $lineSubtotal * ($gstRate / 100);
                        $lineTotalAmount = $lineSubtotal + $lineGstAmount;
                        
                        $itemData = [
                            'so_id' => $id,
                            'product_id' => intval($item['product_id']),
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'gst_rate' => $gstRate,
                            'gst_amount' => $lineGstAmount,
                            'total_amount' => $lineTotalAmount
                        ];

                        $this->salesOrderItemModel->insert($itemData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update sales order.');
            }

            return redirect()->to('sales-order/show/' . $id)->with('success', 'Sales order updated successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating sales order: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete items first
            $this->salesOrderItemModel->where('so_id', $id)->delete();
            
            // Delete sales order
            $this->salesOrderModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to delete sales order.');
            }

            return redirect()->to('sales-order')->with('success', 'Sales order deleted successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error deleting sales order: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        
        if (!$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status is required']);
        }

        try {
            $this->salesOrderModel->update($id, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $salesOrders = $this->salesOrderModel->getSalesOrdersWithDetails();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_orders_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'SO Number', 'Customer', 'Order Date', 'Status', 'Subtotal', 'Total Amount'
        ]);
        
        // CSV data
        foreach ($salesOrders as $order) {
            fputcsv($output, [
                $order['so_number'],
                $order['customer_name'],
                $order['order_date'],
                $order['status'],
                isset($order['subtotal']) ? $order['subtotal'] : 0,
                isset($order['total_amount']) ? $order['total_amount'] : 0
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function print($id)
    {
        $salesOrder = $this->salesOrderModel->getSalesOrderWithItems($id);
        
        if (!$salesOrder) {
            return redirect()->to('sales-order')->with('error', 'Error: Sales order not found.');
        }

        // Calculate order summary
        $orderSummary = $this->calculateOrderSummary($salesOrder['items']);

        $data = [
            'sales_order' => $salesOrder,
            'order_items' => $salesOrder['items'],
            'order_summary' => $orderSummary
        ];
        
        return view('sales_order/print', $data);
    }

    public function getProducts()
    {
        // Get only finished goods and waste materials for sales
        $products = $this->productModel->getSalesMaterials();
        return $this->response->setJSON($products);
    }

    public function getFinishedGoodsForDropdown()
    {
        // Get only finished goods for dropdown selection
        $products = $this->productModel->getFinishedGoodsForDropdown();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    public function getCustomers()
    {
        $customers = $this->customerModel->select('id, customer_name, address, phone, gst_number')->findAll();
        return $this->response->setJSON($customers);
    }

    private function calculateOrderSummary($items)
    {
        $subtotal = 0;
        $discountTotal = 0;
        $cgstTotal = 0;
        $sgstTotal = 0;
        $igstTotal = 0;

        foreach ($items as $item) {
            $lineSubtotal = $item['unit_price'] * $item['quantity'];
            $lineDiscount = ($lineSubtotal * (isset($item['discount']) ? $item['discount'] : 0)) / 100;
            $taxableAmount = $lineSubtotal - $lineDiscount;

            $subtotal += $lineSubtotal;
            $discountTotal += $lineDiscount;
            $cgstTotal += ($taxableAmount * (isset($item['cgst']) ? $item['cgst'] : 0)) / 100;
            $sgstTotal += ($taxableAmount * (isset($item['sgst']) ? $item['sgst'] : 0)) / 100;
            $igstTotal += ($taxableAmount * (isset($item['igst']) ? $item['igst'] : 0)) / 100;
        }

        $finalTotal = $subtotal - $discountTotal + $cgstTotal + $sgstTotal + $igstTotal;

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'cgst_total' => $cgstTotal,
            'sgst_total' => $sgstTotal,
            'igst_total' => $igstTotal,
            'final_total' => $finalTotal
        ];
    }
}
