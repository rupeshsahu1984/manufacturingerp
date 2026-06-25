<?php

namespace App\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Product;
use Exception;

class QuotationController extends BaseController
{
    protected $quotationModel;
    protected $quotationItemModel;
    protected $customerModel;
    protected $productModel;

    public function __construct()
    {
        $this->quotationModel = new Quotation();
        $this->quotationItemModel = new QuotationItem();
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
            'title' => 'Quotations - PRODX',
            'quotations' => $this->quotationModel->getQuotationsWithDetails($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->quotationModel->getQuotationStats(),
            'filters' => $filters
        ];

        return view('quotation/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Quotation - PRODX',
            'customers' => $this->customerModel->getActiveCustomers(),
            'products' => $this->productModel->getSalesMaterials(),
            'quotation_number' => $this->quotationModel->generateUniqueQuotationNumber()
        ];

        return view('quotation/create', $data);
    }

    public function store()
    {
        $rules = [
            'quotation_number' => 'required|max_length[20]|is_unique[quotations.quotation_number]',
            'customer_id' => 'required|integer',
            'quotation_date' => 'required|valid_date',
            'valid_until' => 'required|valid_date',
            'delivery_address' => 'permit_empty|max_length[65535]',
            'payment_terms' => 'permit_empty|max_length[255]',
            'status' => 'required|in_list[draft,sent,accepted,rejected,expired]'
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
            'quotation_number' => $this->request->getPost('quotation_number'),
            'customer_id' => $this->request->getPost('customer_id'),
            'quotation_date' => $this->request->getPost('quotation_date'),
            'valid_until' => $this->request->getPost('valid_until'),
            'delivery_address' => $this->request->getPost('delivery_address'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert quotation
            $quotation_id = $this->quotationModel->insert($data);

            // Insert quotation items
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $itemData = [
                            'quotation_id' => $quotation_id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'line_total' => $item['quantity'] * $item['unit_price'],
                            'description' => isset($item['description']) ? $item['description'] : ''
                        ];
                        $this->quotationItemModel->insert($itemData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to create quotation.');
            }

            return redirect()->to('quotation')->with('success', 'Quotation created successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating quotation: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $quotation = $this->quotationModel->getQuotationWithDetails($id);
        
        if (!$quotation) {
            return redirect()->to('quotation')->with('error', 'Quotation not found.');
        }

        $data = [
            'title' => 'Quotation Details - PRODX',
            'quotation' => $quotation
        ];

        return view('quotation/show', $data);
    }

    public function edit($id)
    {
        $quotation = $this->quotationModel->getQuotationWithDetails($id);
        
        if (!$quotation) {
            return redirect()->to('quotation')->with('error', 'Quotation not found.');
        }

        $data = [
            'title' => 'Edit Quotation - PRODX',
            'quotation' => $quotation,
            'customers' => $this->customerModel->getActiveCustomers(),
            'products' => $this->productModel->getActiveProducts()
        ];

        return view('quotation/edit', $data);
    }

    public function update($id)
    {
        $quotation = $this->quotationModel->find($id);
        
        if (!$quotation) {
            return redirect()->to('quotation')->with('error', 'Quotation not found.');
        }

        $rules = [
            'quotation_number' => "required|max_length[20]|is_unique[quotations.quotation_number,id,$id]",
            'customer_id' => 'required|integer',
            'quotation_date' => 'required|valid_date',
            'valid_until' => 'required|valid_date',
            'delivery_address' => 'permit_empty|max_length[65535]',
            'payment_terms' => 'permit_empty|max_length[255]',
            'status' => 'required|in_list[draft,sent,accepted,rejected,expired]'
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
            'quotation_number' => $this->request->getPost('quotation_number'),
            'customer_id' => $this->request->getPost('customer_id'),
            'quotation_date' => $this->request->getPost('quotation_date'),
            'valid_until' => $this->request->getPost('valid_until'),
            'delivery_address' => $this->request->getPost('delivery_address'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'status' => $this->request->getPost('status')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update quotation
            $this->quotationModel->update($id, $data);

            // Delete existing items and insert new ones
            $this->quotationItemModel->where('quotation_id', $id)->delete();

            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $itemData = [
                            'quotation_id' => $id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'line_total' => $item['quantity'] * $item['unit_price'],
                            'description' => isset($item['description']) ? $item['description'] : ''
                        ];
                        $this->quotationItemModel->insert($itemData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update quotation.');
            }

            return redirect()->to('quotation')->with('success', 'Quotation updated successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating quotation: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $quotation = $this->quotationModel->find($id);
        
        if (!$quotation) {
            return redirect()->to('quotation')->with('error', 'Quotation not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete items first
            $this->quotationItemModel->where('quotation_id', $id)->delete();
            
            // Delete quotation
            $this->quotationModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('quotation')->with('error', 'Failed to delete quotation.');
            }

            return redirect()->to('quotation')->with('success', 'Quotation deleted successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('quotation')->with('error', 'Error deleting quotation: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $quotation = $this->quotationModel->find($id);
        
        if (!$quotation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quotation not found.']);
        }

        $newStatus = $this->request->getPost('status');
        
        if ($this->quotationModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function convertToOrder($id)
    {
        $quotation = $this->quotationModel->getQuotationWithDetails($id);
        
        if (!$quotation) {
            return redirect()->to('quotation')->with('error', 'Quotation not found.');
        }

        if ($quotation['status'] !== 'accepted') {
            return redirect()->to('quotation')->with('error', 'Only accepted quotations can be converted to orders.');
        }

        // Redirect to sales order create with quotation data
        return redirect()->to('sales-order/create?quotation_id=' . $id);
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

        $quotations = $this->quotationModel->getQuotationsWithDetails($filters);
        
        $filename = 'quotations_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Quotation Number', 'Customer', 'Quotation Date', 'Valid Until', 'Subtotal', 'GST', 'Total', 'Status'
        ]);
        
        foreach ($quotations as $quotation) {
            fputcsv($output, [
                $quotation['quotation_number'],
                isset($quotation['customer_name']) ? $quotation['customer_name'] : '',
                $quotation['quotation_date'],
                $quotation['valid_until'],
                $quotation['subtotal'],
                $quotation['gst_amount'],
                $quotation['total_amount'],
                $quotation['status']
            ]);
        }
        
        fclose($output);
        exit;
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
        $quotation = $this->quotationModel->getQuotationWithDetails($id);
        
        if (!$quotation) {
            return redirect()->to('quotation')->with('error', 'Quotation not found.');
        }

        $data = [
            'title' => 'Print Quotation - PRODX',
            'quotation' => $quotation
        ];

        return view('quotation/print', $data);
    }
}
