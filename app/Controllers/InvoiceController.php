<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use Exception;

class InvoiceController extends BaseController
{
    protected $invoiceModel;
    protected $invoiceItemModel;
    protected $salesOrderModel;
    protected $customerModel;
    protected $productModel;

    public function __construct()
    {
        $this->invoiceModel = new Invoice();
        $this->invoiceItemModel = new InvoiceItem();
        $this->salesOrderModel = new SalesOrder();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'customer' => $this->request->getGet('customer'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Invoices - PRODX',
            'invoices' => $this->invoiceModel->getInvoicesWithDetails($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->invoiceModel->getInvoiceStats(),
            'filters' => $filters
        ];

        return view('invoice/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Invoice - PRODX',
            'customers' => $this->customerModel->getActiveCustomers(),
            'sales_orders' => $this->salesOrderModel->getConfirmedSalesOrders(),
            'products' => $this->productModel->getSalesMaterials(),
            'invoice_number' => $this->invoiceModel->generateUniqueInvoiceNumber()
        ];

        return view('invoice/create', $data);
    }

    public function store()
    {
        $rules = [
            'invoice_number' => 'required|max_length[20]|is_unique[invoices.invoice_number]',
            'customer_id' => 'required|integer',
            'so_id' => 'permit_empty|integer',
            'invoice_date' => 'required|valid_date',
            'due_date' => 'permit_empty|valid_date',
            'status' => 'required|in_list[draft,sent,paid,overdue,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate items separately (CodeIgniter doesn't support 'array' as a validation rule)
        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product to the invoice.');
        }

        // Calculate totals from items
        $subtotal = 0;
        $gst_amount = 0;
        $total_amount = 0;

        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                    $line_total = floatval($item['quantity']) * floatval($item['unit_price']);
                    $subtotal += $line_total;
                    $gst_amount += ($line_total * 0.18); // 18% GST
                }
            }
        }
        $total_amount = $subtotal + $gst_amount;
        
        // Log for debugging
        log_message('debug', 'Invoice Store - Items received: ' . json_encode($items));
        log_message('debug', 'Invoice Store - Calculated totals - Subtotal: ' . $subtotal . ', GST: ' . $gst_amount . ', Total: ' . $total_amount);

        $data = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'so_id' => $this->request->getPost('so_id') ?: null,
            'customer_id' => $this->request->getPost('customer_id'),
            'invoice_date' => $this->request->getPost('invoice_date'),
            'due_date' => $this->request->getPost('due_date'),
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'paid_amount' => 0,
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            log_message('debug', 'Invoice Store - Invoice data: ' . json_encode($data));
            
            // Insert invoice using direct database insert to avoid model validation issues
            $db->table('invoices')->insert($data);
            $invoice_id = $db->insertID();
            
            if (!$invoice_id) {
                $error = $db->error();
                log_message('error', 'Invoice Store - Failed to insert invoice: ' . json_encode($error));
                throw new \Exception('Failed to create invoice: ' . ($error['message'] ?? 'Unknown database error'));
            }
            
            log_message('debug', 'Invoice Store - Invoice created with ID: ' . $invoice_id);

            // Insert invoice items - use actual database column names
            $validItemsCount = 0;
            if ($items && is_array($items)) {
                foreach ($items as $index => $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $quantity = floatval($item['quantity']);
                        $unitPrice = floatval($item['unit_price']);
                        $lineSubtotal = $quantity * $unitPrice;
                        
                        // GST calculation (default 18%)
                        $gstRate = 18.00;
                        $lineGstAmount = $lineSubtotal * ($gstRate / 100);
                        $lineTotalAmount = $lineSubtotal + $lineGstAmount;
                        
                        $itemData = [
                            'invoice_id' => $invoice_id,
                            'product_id' => intval($item['product_id']),
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'gst_rate' => $gstRate,
                            'gst_amount' => $lineGstAmount,
                            'total_amount' => $lineTotalAmount
                        ];
                        
                        log_message('debug', "Invoice Store - Inserting item $index: " . json_encode($itemData));
                        
                        // Use direct database insert
                        if (!$db->table('invoice_items')->insert($itemData)) {
                            $error = $db->error();
                            log_message('error', "Invoice Store - Failed to insert item $index: " . json_encode($error));
                            throw new \Exception('Failed to insert invoice item #' . ($index + 1) . ': ' . ($error['message'] ?? 'Unknown error'));
                        }
                        
                        $validItemsCount++;
                    }
                }
            }
            
            if ($validItemsCount == 0) {
                throw new \Exception('No valid items provided. Please add at least one product with quantity and price.');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Invoice Store - Transaction failed: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Failed to create invoice: ' . ($error['message'] ?? 'Transaction failed'));
            }

            log_message('debug', 'Invoice Store - Invoice created successfully with ' . $validItemsCount . ' item(s)');
            return redirect()->to('invoice')->with('success', 'Invoice created successfully with ' . $validItemsCount . ' item(s).');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Invoice Store - Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
        
        if (!$invoice) {
            return redirect()->to('invoice')->with('error', 'Invoice not found.');
        }

        $data = [
            'title' => 'Invoice Details - PRODX',
            'invoice' => $invoice
        ];

        return view('invoice/show', $data);
    }

    public function edit($id)
    {
        $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
        
        if (!$invoice) {
            return redirect()->to('invoice')->with('error', 'Invoice not found.');
        }

        $data = [
            'title' => 'Edit Invoice - PRODX',
            'invoice' => $invoice,
            'customers' => $this->customerModel->getActiveCustomers(),
            'sales_orders' => $this->salesOrderModel->getConfirmedSalesOrders(),
            'products' => $this->productModel->getSalesMaterials() // Use same method as create
        ];

        return view('invoice/edit', $data);
    }

    public function update($id)
    {
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            return redirect()->to('invoice')->with('error', 'Invoice not found.');
        }

        $rules = [
            'invoice_number' => "required|max_length[20]|is_unique[invoices.invoice_number,id,$id]",
            'customer_id' => 'required|integer',
            'so_id' => 'permit_empty|integer',
            'invoice_date' => 'required|valid_date',
            'due_date' => 'permit_empty|valid_date',
            'status' => 'required|in_list[draft,sent,paid,overdue,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate items separately
        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product to the invoice.');
        }

        // Calculate totals from items
        $subtotal = 0;
        $gst_amount = 0;
        $total_amount = 0;

        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                    $line_total = floatval($item['quantity']) * floatval($item['unit_price']);
                    $subtotal += $line_total;
                    $gst_amount += ($line_total * 0.18); // 18% GST
                }
            }
        }
        $total_amount = $subtotal + $gst_amount;

        $data = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'so_id' => $this->request->getPost('so_id') ?: null,
            'customer_id' => intval($this->request->getPost('customer_id')),
            'invoice_date' => $this->request->getPost('invoice_date'),
            'due_date' => $this->request->getPost('due_date') ?: null,
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'status' => $this->request->getPost('status')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            log_message('debug', 'Invoice Update - Invoice data: ' . json_encode($data));
            
            // Update invoice using direct database update to avoid model validation issues
            if (!$db->table('invoices')->where('id', $id)->update($data)) {
                $error = $db->error();
                log_message('error', 'Invoice Update - Failed to update invoice: ' . json_encode($error));
                throw new \Exception('Failed to update invoice: ' . ($error['message'] ?? 'Unknown database error'));
            }

            // Delete existing items
            $db->table('invoice_items')->where('invoice_id', $id)->delete();

            // Insert updated items - use actual database column names
            $validItemsCount = 0;
            if ($items && is_array($items)) {
                foreach ($items as $index => $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $quantity = floatval($item['quantity']);
                        $unitPrice = floatval($item['unit_price']);
                        $lineSubtotal = $quantity * $unitPrice;
                        
                        // GST calculation (default 18%)
                        $gstRate = 18.00;
                        $lineGstAmount = $lineSubtotal * ($gstRate / 100);
                        $lineTotalAmount = $lineSubtotal + $lineGstAmount;
                        
                        $itemData = [
                            'invoice_id' => $id,
                            'product_id' => intval($item['product_id']),
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'gst_rate' => $gstRate,
                            'gst_amount' => $lineGstAmount,
                            'total_amount' => $lineTotalAmount
                        ];
                        
                        log_message('debug', "Invoice Update - Inserting item $index: " . json_encode($itemData));
                        
                        // Use direct database insert
                        if (!$db->table('invoice_items')->insert($itemData)) {
                            $error = $db->error();
                            log_message('error', "Invoice Update - Failed to insert item $index: " . json_encode($error));
                            throw new \Exception('Failed to insert invoice item #' . ($index + 1) . ': ' . ($error['message'] ?? 'Unknown error'));
                        }
                        
                        $validItemsCount++;
                    }
                }
            }
            
            if ($validItemsCount == 0) {
                throw new \Exception('No valid items provided. Please add at least one product with quantity and price.');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Invoice Update - Transaction failed: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Failed to update invoice: ' . ($error['message'] ?? 'Transaction failed'));
            }

            log_message('debug', 'Invoice Update - Invoice updated successfully with ' . $validItemsCount . ' item(s)');
            return redirect()->to('invoice')->with('success', 'Invoice updated successfully with ' . $validItemsCount . ' item(s).');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Invoice Update - Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            return redirect()->to('invoice')->with('error', 'Invoice not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete invoice items first
            $this->invoiceItemModel->where('invoice_id', $id)->delete();
            
            // Delete invoice
            $this->invoiceModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('invoice')->with('error', 'Failed to delete invoice.');
            }

            return redirect()->to('invoice')->with('success', 'Invoice deleted successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('invoice')->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice not found.']);
        }

        $newStatus = $this->request->getPost('status');
        
        if ($this->invoiceModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function recordPayment($id)
    {
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            return redirect()->to('invoice')->with('error', 'Invoice not found.');
        }

        $payment_amount = $this->request->getPost('payment_amount');
        $payment_date = $this->request->getPost('payment_date');
        $payment_method = $this->request->getPost('payment_method');

        if (!$payment_amount || !$payment_date) {
            return redirect()->back()->with('error', 'Payment amount and date are required.');
        }

        $new_paid_amount = $invoice['paid_amount'] + $payment_amount;
        $new_status = ($new_paid_amount >= $invoice['total_amount']) ? 'paid' : 'partial';

        $data = [
            'paid_amount' => $new_paid_amount,
            'status' => $new_status,
            'updated_by' => session()->get('user_id') ?? 1
        ];

        if ($this->invoiceModel->update($id, $data)) {
            return redirect()->to('invoice')->with('success', 'Payment recorded successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to record payment.');
        }
    }

    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'customer' => $this->request->getGet('customer'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $invoices = $this->invoiceModel->getInvoicesWithDetails($filters);
        
        $filename = 'invoices_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Invoice Number', 'Customer', 'Invoice Date', 'Due Date', 'Subtotal', 'GST', 'Total', 'Paid', 'Status'
        ]);
        
        foreach ($invoices as $invoice) {
            fputcsv($output, [
                $invoice['invoice_number'],
                isset($invoice['customer_name']) ? $invoice['customer_name'] : '',
                $invoice['invoice_date'],
                isset($invoice['due_date']) ? $invoice['due_date'] : '',
                $invoice['subtotal'],
                $invoice['gst_amount'],
                $invoice['total_amount'],
                $invoice['paid_amount'],
                $invoice['status']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getSalesOrderItems($so_id)
    {
        $items = $this->salesOrderModel->getSalesOrderItems($so_id);
        return $this->response->setJSON(['success' => true, 'items' => $items]);
    }

    public function getProducts()
    {
        // Get both produced materials (finished_goods) and waste materials
        $products = $this->productModel->getSalesMaterials();
        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    public function getCustomers()
    {
        $customers = $this->customerModel->getActiveCustomers();
        return $this->response->setJSON(['success' => true, 'customers' => $customers]);
    }

    public function printInvoice($id)
    {
        $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
        
        if (!$invoice) {
            return redirect()->to('invoice')->with('error', 'Invoice not found.');
        }

        $data = [
            'title' => 'Print Invoice - PRODX',
            'invoice' => $invoice
        ];

        return view('invoice/print_invoice', $data);
    }
}
