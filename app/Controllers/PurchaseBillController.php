<?php

namespace App\Controllers;

use App\Models\PurchaseBill;
use App\Models\PurchaseBillItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Exception;

class PurchaseBillController extends BaseController
{
    protected $purchaseBillModel;
    protected $purchaseBillItemModel;
    protected $supplierModel;
    protected $productModel;
    protected $purchaseOrderModel;

    public function __construct()
    {
        $this->purchaseBillModel = new PurchaseBill();
        $this->purchaseBillItemModel = new PurchaseBillItem();
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
        $this->purchaseOrderModel = new PurchaseOrder();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Purchase Bills - PRODX',
            'bills' => $this->purchaseBillModel->getPurchaseBills($filters),
            'stats' => $this->purchaseBillModel->getBillStats(),
            'suppliers' => $this->supplierModel->where('status', 'active')->findAll(),
            'filters' => $filters
        ];

        return view('purchase_bill/index', $data);
    }

    public function create()
    {
        $poId = $this->request->getGet('po_id');
        $supplierId = $this->request->getGet('supplier_id');

        $data = [
            'title' => 'Create Purchase Bill - PRODX',
            'bill_number' => $this->purchaseBillModel->generateBillNumber(),
            'suppliers' => $this->supplierModel->where('status', 'active')->findAll(),
            'products' => $this->productModel->where('status', 'active')->whereIn('material_type', ['raw_material', 'packaging'])->findAll(),
            'purchase_orders' => $this->purchaseOrderModel->where('status', 'confirmed')->findAll()
        ];

        // If PO ID is provided, get PO details
        if ($poId) {
            $po = $this->purchaseOrderModel->find($poId);
            if ($po) {
                $data['selected_po'] = $po;
                $data['selected_supplier'] = $this->supplierModel->find($po['supplier_id']);
            }
        }

        // If supplier ID is provided, get supplier details
        if ($supplierId) {
            $data['selected_supplier'] = $this->supplierModel->find($supplierId);
        }

        return view('purchase_bill/create', $data);
    }

    public function store()
    {
        $rules = [
            'bill_number' => 'required|is_unique[purchase_bills.bill_number]',
            'supplier_id' => 'required|integer',
            'bill_date' => 'required|valid_date',
            'due_date' => 'permit_empty|valid_date',
            'invoice_number' => 'permit_empty|max_length[50]',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = json_decode($this->request->getPost('items'), true);
        
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'At least one item is required');
        }

        // Calculate totals and normalize item data
        $subtotal = 0;
        $gstAmount = 0;
        $totalAmount = 0;
        $normalizedItems = [];

        foreach ($items as $item) {
            // Handle both purchase_price and unit_price
            $unitPrice = isset($item['unit_price']) ? floatval($item['unit_price']) : 
                        (isset($item['purchase_price']) ? floatval($item['purchase_price']) : 0);
            
            if ($unitPrice <= 0) {
                return redirect()->back()->withInput()->with('error', 'Invalid unit price for item');
            }
            
            $quantity = floatval($item['quantity']);
            if ($quantity <= 0) {
                return redirect()->back()->withInput()->with('error', 'Invalid quantity for item');
            }
            
            // Calculate GST - use CGST + SGST if available, otherwise use IGST or default GST rate
            $cgstRate = isset($item['cgst']) ? floatval($item['cgst']) : 0;
            $sgstRate = isset($item['sgst']) ? floatval($item['sgst']) : 0;
            $igstRate = isset($item['igst']) ? floatval($item['igst']) : 0;
            
            // If CGST and SGST are provided, use them; otherwise use IGST or default GST rate
            if ($cgstRate > 0 || $sgstRate > 0) {
                $gstRate = $cgstRate + $sgstRate;
            } elseif ($igstRate > 0) {
                $gstRate = $igstRate;
            } else {
                $gstRate = isset($item['gst_rate']) ? floatval($item['gst_rate']) : 18.00;
            }
            
            $rowSubtotal = $quantity * $unitPrice;
            $rowGstAmount = ($rowSubtotal * $gstRate) / 100;
            $rowTotal = $rowSubtotal + $rowGstAmount;
            
            $subtotal += $rowSubtotal;
            $gstAmount += $rowGstAmount;
            $totalAmount += $rowTotal;
            
            // Normalize item data for database insertion
            $normalizedItems[] = [
                'product_id' => intval($item['product_id']),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'cgst_rate' => $cgstRate,
                'sgst_rate' => $sgstRate,
                'igst_rate' => $igstRate,
                'gst_rate' => $gstRate,
                'gst_amount' => $rowGstAmount,
                'total_amount' => $rowTotal
            ];
        }

        $billData = [
            'bill_number' => $this->request->getPost('bill_number'),
            'po_id' => $this->request->getPost('po_id') ?: null,
            'supplier_id' => $this->request->getPost('supplier_id'),
            'bill_date' => $this->request->getPost('bill_date'),
            'due_date' => $this->request->getPost('due_date') ?: null,
            'invoice_number' => $this->request->getPost('invoice_number'),
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'status' => 'draft',
            'created_by' => session()->get('user_id')
        ];

        try {
            $billId = $this->purchaseBillModel->createBillWithItems($billData, $normalizedItems);
            
            if ($billId) {
                return redirect()->to('purchase-bill')->with('success', 'Purchase bill created successfully');
            }
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create purchase bill: ' . $e->getMessage());
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create purchase bill');
    }

    public function show($id)
    {
        $bill = $this->purchaseBillModel->getPurchaseBillWithItems($id);
        
        if (!$bill) {
            return redirect()->to('purchase-bill')->with('error', 'Purchase bill not found');
        }

        $data = [
            'title' => 'View Purchase Bill - PRODX',
            'bill' => $bill
        ];

        return view('purchase_bill/show', $data);
    }

    public function edit($id)
    {
        $bill = $this->purchaseBillModel->getPurchaseBillWithItems($id);
        
        if (!$bill) {
            return redirect()->to('purchase-bill')->with('error', 'Purchase bill not found');
        }

        if ($bill['status'] !== 'draft') {
            return redirect()->to('purchase-bill')->with('error', 'Only draft bills can be edited');
        }

        $data = [
            'title' => 'Edit Purchase Bill - PRODX',
            'bill' => $bill,
            'suppliers' => $this->supplierModel->where('status', 'active')->findAll(),
            'products' => $this->productModel->where('status', 'active')->whereIn('material_type', ['raw_material', 'packaging'])->findAll()
        ];

        return view('purchase_bill/edit', $data);
    }

    public function update($id)
    {
        $bill = $this->purchaseBillModel->find($id);
        
        if (!$bill) {
            return redirect()->to(base_url('purchase-bill'))->with('error', 'Purchase bill not found');
        }

        if ($bill['status'] !== 'draft') {
            return redirect()->to(base_url('purchase-bill'))->with('error', 'Only draft bills can be edited');
        }

        $rules = [
            'bill_number' => 'required|is_unique[purchase_bills.bill_number,id,' . $id . ']',
            'supplier_id' => 'required|integer',
            'bill_date' => 'required|valid_date',
            'due_date' => 'permit_empty|valid_date',
            'invoice_number' => 'permit_empty|max_length[50]',
            'note' => 'permit_empty|max_length[1000]',
            'items' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            log_message('error', 'Purchase Bill Update Validation Errors: ' . json_encode($errors));
            return redirect()->back()->withInput()->with('errors', $errors)->with('error', 'Validation failed. Please check the form and try again.');
        }

        $itemsJson = $this->request->getPost('items');
        if (empty($itemsJson)) {
            return redirect()->back()->withInput()->with('error', 'At least one item is required');
        }
        
        $items = json_decode($itemsJson, true);
        
        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Invalid items data. Please try again.');
        }

        // Calculate totals and normalize item data
        $subtotal = 0;
        $gstAmount = 0;
        $totalAmount = 0;
        $normalizedItems = [];

        foreach ($items as $item) {
            // Handle both purchase_price and unit_price
            $unitPrice = isset($item['unit_price']) ? floatval($item['unit_price']) : 
                        (isset($item['purchase_price']) ? floatval($item['purchase_price']) : 0);
            
            if ($unitPrice <= 0) {
                return redirect()->back()->withInput()->with('error', 'Invalid unit price for item');
            }
            
            $quantity = floatval($item['quantity']);
            if ($quantity <= 0) {
                return redirect()->back()->withInput()->with('error', 'Invalid quantity for item');
            }
            
            // Calculate GST - use CGST + SGST if available, otherwise use IGST or default GST rate
            $cgstRate = isset($item['cgst']) ? floatval($item['cgst']) : 0;
            $sgstRate = isset($item['sgst']) ? floatval($item['sgst']) : 0;
            $igstRate = isset($item['igst']) ? floatval($item['igst']) : 0;
            
            // If CGST and SGST are provided, use them; otherwise use IGST or default GST rate
            if ($cgstRate > 0 || $sgstRate > 0) {
                $gstRate = $cgstRate + $sgstRate;
            } elseif ($igstRate > 0) {
                $gstRate = $igstRate;
            } else {
                $gstRate = isset($item['gst_rate']) ? floatval($item['gst_rate']) : 18.00;
            }
            
            $rowSubtotal = $quantity * $unitPrice;
            $rowGstAmount = ($rowSubtotal * $gstRate) / 100;
            $rowTotal = $rowSubtotal + $rowGstAmount;
            
            $subtotal += $rowSubtotal;
            $gstAmount += $rowGstAmount;
            $totalAmount += $rowTotal;
            
            // Normalize item data for database insertion
            $normalizedItems[] = [
                'product_id' => intval($item['product_id']),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'cgst_rate' => $cgstRate,
                'sgst_rate' => $sgstRate,
                'igst_rate' => $igstRate,
                'gst_rate' => $gstRate,
                'gst_amount' => $rowGstAmount,
                'total_amount' => $rowTotal
            ];
        }

        $billData = [
            'bill_number' => $this->request->getPost('bill_number'),
            'po_id' => $this->request->getPost('po_id') ?: null,
            'supplier_id' => $this->request->getPost('supplier_id'),
            'bill_date' => $this->request->getPost('bill_date'),
            'due_date' => $this->request->getPost('due_date') ?: null,
            'invoice_number' => $this->request->getPost('invoice_number') ?: null,
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'total_amount' => $totalAmount
        ];
        
        // Add note if the field exists in the database
        $note = $this->request->getPost('note');
        if ($note !== null) {
            $billData['note'] = $note;
        }

        try {
            $result = $this->purchaseBillModel->updateBillWithItems($id, $billData, $normalizedItems);
            if ($result) {
                return redirect()->to(base_url('purchase-bill'))->with('success', 'Purchase bill updated successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update purchase bill. Please try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Purchase Bill Update Error: ' . $e->getMessage());
            log_message('error', 'Purchase Bill Update Trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase bill: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $bill = $this->purchaseBillModel->find($id);
        
        if (!$bill) {
            return redirect()->to('purchase-bill')->with('error', 'Purchase bill not found');
        }

        if ($bill['status'] !== 'draft') {
            return redirect()->to('purchase-bill')->with('error', 'Only draft bills can be deleted');
        }

        if ($this->purchaseBillModel->delete($id)) {
            return redirect()->to('purchase-bill')->with('success', 'Purchase bill deleted successfully');
        }

        return redirect()->to('purchase-bill')->with('error', 'Failed to delete purchase bill');
    }

    public function updateStatus($id)
    {
        $bill = $this->purchaseBillModel->find($id);
        
        if (!$bill) {
            return $this->response->setJSON(['success' => false, 'message' => 'Purchase bill not found']);
        }

        $status = $this->request->getPost('status');
        
        if (!in_array($status, ['draft', 'received', 'paid', 'overdue', 'cancelled'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        if ($this->purchaseBillModel->updateStatus($id, $status)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Bill status updated successfully',
                'new_status' => $status
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update bill status']);
    }

    public function recordPayment($id)
    {
        $bill = $this->purchaseBillModel->find($id);
        
        if (!$bill) {
            return $this->response->setJSON(['success' => false, 'message' => 'Purchase bill not found']);
        }

        if (!$this->purchaseBillModel->canPayBill($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bill cannot be paid']);
        }

        $amount = $this->request->getPost('amount');
        
        if (!is_numeric($amount) || $amount <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid payment amount']);
        }

        if ($this->purchaseBillModel->recordPayment($id, $amount)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Payment recorded successfully'
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to record payment']);
    }

    public function getOverdueBills()
    {
        $bills = $this->purchaseBillModel->getOverdueBills();
        
        $data = [
            'title' => 'Overdue Bills - PRODX',
            'bills' => $bills
        ];

        return view('purchase_bill/overdue', $data);
    }

    public function print($id)
    {
        $bill = $this->purchaseBillModel->getPurchaseBillWithItems($id);
        
        if (!$bill) {
            return redirect()->to('purchase-bill')->with('error', 'Purchase bill not found');
        }

        $data = [
            'title' => 'Print Purchase Bill - PRODX',
            'bill' => $bill
        ];

        return view('purchase_bill/print', $data);
    }

    public function download($id)
    {
        $bill = $this->purchaseBillModel->getPurchaseBillWithItems($id);
        
        if (!$bill) {
            return redirect()->to('purchase-bill')->with('error', 'Purchase bill not found');
        }

        // Check if DOMPDF is available
        if (class_exists('\Dompdf\Dompdf')) {
            try {
                // Generate PDF using DOMPDF
                $dompdf = new \Dompdf\Dompdf();
                
                // Use print view for PDF generation
                $html = view('purchase_bill/print', ['bill' => $bill]);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                $filename = 'Purchase_Bill_' . $bill['bill_number'] . '.pdf';
                
                $dompdf->stream($filename, ['Attachment' => true]);
                exit;
                
            } catch (\Exception $e) {
                // Fallback to print view if PDF generation fails
                log_message('error', 'PDF Download Error: ' . $e->getMessage());
                return redirect()->to('purchase-bill/print/' . $id)
                    ->with('info', 'PDF generation failed. Showing print view instead. You can use your browser\'s print function to save as PDF.');
            }
        } else {
            // DOMPDF not installed, redirect to print view
            // User can use browser's "Print to PDF" feature
            return redirect()->to('purchase-bill/print/' . $id)
                ->with('info', 'PDF library not installed. Use your browser\'s print function (Ctrl+P) and select "Save as PDF" to download.');
        }
    }

    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $bills = $this->purchaseBillModel->getPurchaseBills($filters);
        
        // Generate CSV
        $filename = 'purchase_bills_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Bill Number',
            'Supplier',
            'Bill Date',
            'Due Date',
            'Invoice Number',
            'Total Amount',
            'Paid Amount',
            'Outstanding Amount',
            'Status'
        ]);
        
        // CSV data
        foreach ($bills as $bill) {
            $outstanding = $bill['total_amount'] - $bill['paid_amount'];
            fputcsv($output, [
                $bill['bill_number'],
                $bill['supplier_name'],
                $bill['bill_date'],
                $bill['due_date'],
                $bill['invoice_number'],
                $bill['total_amount'],
                $bill['paid_amount'],
                $outstanding,
                $bill['status']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getProducts()
    {
        $products = $this->productModel->where('status', 'active')
            ->whereIn('material_type', ['raw_material', 'packaging'])
            ->select('products.id, products.product_name, products.product_code, products.unit, 
                     COALESCE(products.unit_price, products.cost_price, 0) as unit_price,
                     products.selling_price, products.cost_price,
                     COALESCE(products.cgst_rate, 0) as cgst_rate, 
                     COALESCE(products.sgst_rate, 0) as sgst_rate, 
                     COALESCE(products.igst_rate, 0) as igst_rate,
                     COALESCE(products.gst_rate, 0) as gst_rate')
            ->findAll();
        
        // Add stock information for each product
        $db = \Config\Database::connect();
        $stockModel = new \App\Models\Stock();
        
        foreach ($products as &$product) {
            // Try to find matching item by product_code
            $item = $db->table('items')
                ->where('item_code', $product['product_code'])
                ->get()
                ->getRowArray();
            
            if ($item) {
                // Get available stock for this item
                $stockInfo = $stockModel->getItemStock($item['id']);
                $product['available_stock'] = $stockInfo['available_stock'] ?? 0;
            } else {
                $product['available_stock'] = 0;
            }
            
            // Add alias for JavaScript compatibility
            $product['purchase_price'] = $product['unit_price'];
        }
        
        return $this->response->setJSON($products);
    }

    public function getPurchaseOrders()
    {
        $supplierId = $this->request->getGet('supplier_id');
        
        if (!$supplierId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Supplier ID is required']);
        }

        $orders = $this->purchaseOrderModel->where('supplier_id', $supplierId)
            ->where('status', 'confirmed')
            ->findAll();
        
        return $this->response->setJSON(['success' => true, 'orders' => $orders]);
    }
} 