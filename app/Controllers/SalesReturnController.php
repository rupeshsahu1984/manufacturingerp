<?php

namespace App\Controllers;

use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Exception;

class SalesReturnController extends BaseController
{
    protected $salesReturnModel;
    protected $salesReturnItemModel;
    protected $invoiceModel;
    protected $customerModel;
    protected $productModel;

    public function __construct()
    {
        $this->salesReturnModel = new SalesReturn();
        $this->salesReturnItemModel = new SalesReturnItem();
        $this->invoiceModel = new Invoice();
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
            'title' => 'Sales Returns - PRODX',
            'sales_returns' => $this->salesReturnModel->getSalesReturnsWithDetails($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->salesReturnModel->getSalesReturnStats(),
            'filters' => $filters
        ];

        return view('sales_return/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Sales Return - PRODX',
            'customers' => $this->customerModel->getActiveCustomers(),
            'invoices' => $this->invoiceModel->getDeliveredInvoices(),
            'products' => $this->productModel->getSalesMaterials(),
            'return_number' => $this->salesReturnModel->generateUniqueReturnNumber()
        ];

        return view('sales_return/create', $data);
    }

    public function store()
    {
        $rules = [
            'return_number' => 'required|max_length[20]|is_unique[sales_returns.return_number]',
            'invoice_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'return_date' => 'required|valid_date',
            'return_reason' => 'required|max_length[500]',
            'status' => 'required|in_list[draft,submitted,approved,processed,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Calculate totals from items
        $items = $this->request->getPost('items');
        $subtotal = 0;
        $gst_amount = 0;
        $total_amount = 0;

        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                    $line_total = $item['quantity'] * $item['unit_price'];
                    $subtotal += $line_total;
                    $gst_amount += ($line_total * 0.18); // 18% GST
                }
            }
        }
        $total_amount = $subtotal + $gst_amount;

        $data = [
            'return_number' => $this->request->getPost('return_number'),
            'invoice_id' => $this->request->getPost('invoice_id'),
            'customer_id' => $this->request->getPost('customer_id'),
            'return_date' => $this->request->getPost('return_date'),
            'return_reason' => $this->request->getPost('return_reason'),
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert sales return
            $return_id = $this->salesReturnModel->insert($data);

            // Insert sales return items
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $itemData = [
                            'return_id' => $return_id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'line_total' => $item['quantity'] * $item['unit_price'],
                            'return_reason' => isset($item['return_reason']) ? $item['return_reason'] : ''
                        ];
                        $this->salesReturnItemModel->insert($itemData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to create sales return.');
            }

            return redirect()->to('sales-return')->with('success', 'Sales return created successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating sales return: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $salesReturn = $this->salesReturnModel->getSalesReturnWithDetails($id);
        
        if (!$salesReturn) {
            return redirect()->to('sales-return')->with('error', 'Sales return not found.');
        }

        $data = [
            'title' => 'Sales Return Details - PRODX',
            'sales_return' => $salesReturn
        ];

        return view('sales_return/show', $data);
    }

    public function edit($id)
    {
        $salesReturn = $this->salesReturnModel->getSalesReturnWithDetails($id);
        
        if (!$salesReturn) {
            return redirect()->to('sales-return')->with('error', 'Sales return not found.');
        }

        $data = [
            'title' => 'Edit Sales Return - PRODX',
            'sales_return' => $salesReturn,
            'customers' => $this->customerModel->getActiveCustomers(),
            'invoices' => $this->invoiceModel->getDeliveredInvoices(),
            'products' => $this->productModel->getActiveProducts()
        ];

        return view('sales_return/edit', $data);
    }

    public function update($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        
        if (!$salesReturn) {
            return redirect()->to('sales-return')->with('error', 'Sales return not found.');
        }

        $rules = [
            'return_number' => "required|max_length[20]|is_unique[sales_returns.return_number,id,$id]",
            'invoice_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'return_date' => 'required|valid_date',
            'return_reason' => 'required|max_length[500]',
            'status' => 'required|in_list[draft,submitted,approved,processed,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Calculate totals from items
        $items = $this->request->getPost('items');
        $subtotal = 0;
        $gst_amount = 0;
        $total_amount = 0;

        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                    $line_total = $item['quantity'] * $item['unit_price'];
                    $subtotal += $line_total;
                    $gst_amount += ($line_total * 0.18); // 18% GST
                }
            }
        }
        $total_amount = $subtotal + $gst_amount;

        $data = [
            'return_number' => $this->request->getPost('return_number'),
            'invoice_id' => $this->request->getPost('invoice_id'),
            'customer_id' => $this->request->getPost('customer_id'),
            'return_date' => $this->request->getPost('return_date'),
            'return_reason' => $this->request->getPost('return_reason'),
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'status' => $this->request->getPost('status')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update sales return
            $this->salesReturnModel->update($id, $data);

            // Delete existing items and insert new ones
            $this->salesReturnItemModel->where('return_id', $id)->delete();

            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $itemData = [
                            'return_id' => $id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'line_total' => $item['quantity'] * $item['unit_price'],
                            'return_reason' => isset($item['return_reason']) ? $item['return_reason'] : ''
                        ];
                        $this->salesReturnItemModel->insert($itemData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update sales return.');
            }

            return redirect()->to('sales-return')->with('success', 'Sales return updated successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating sales return: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        
        if (!$salesReturn) {
            return redirect()->to('sales-return')->with('error', 'Sales return not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete items first
            $this->salesReturnItemModel->where('return_id', $id)->delete();
            
            // Delete sales return
            $this->salesReturnModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('sales-return')->with('error', 'Failed to delete sales return.');
            }

            return redirect()->to('sales-return')->with('success', 'Sales return deleted successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('sales-return')->with('error', 'Error deleting sales return: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        
        if (!$salesReturn) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sales return not found.']);
        }

        $newStatus = $this->request->getPost('status');
        
        if ($this->salesReturnModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
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

        $salesReturns = $this->salesReturnModel->getSalesReturnsWithDetails($filters);
        
        $filename = 'sales_returns_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Return Number', 'Customer', 'Invoice', 'Return Date', 'Reason', 'Subtotal', 'GST', 'Total', 'Status'
        ]);
        
        foreach ($salesReturns as $return) {
            fputcsv($output, [
                $return['return_number'],
                isset($return['customer_name']) ? $return['customer_name'] : '',
                isset($return['invoice_number']) ? $return['invoice_number'] : '',
                $return['return_date'],
                $return['return_reason'],
                $return['subtotal'],
                $return['gst_amount'],
                $return['total_amount'],
                $return['status']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getInvoices()
    {
        $invoices = $this->invoiceModel->getDeliveredInvoices();
        return $this->response->setJSON(['success' => true, 'invoices' => $invoices]);
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

    public function print($id)
    {
        $salesReturn = $this->salesReturnModel->getSalesReturnWithDetails($id);
        
        if (!$salesReturn) {
            return redirect()->to('sales-return')->with('error', 'Sales return not found.');
        }

        $data = [
            'title' => 'Print Sales Return - PRODX',
            'sales_return' => $salesReturn
        ];

        return view('sales_return/print', $data);
    }
}
