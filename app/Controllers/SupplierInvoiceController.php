<?php

namespace App\Controllers;

use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\SupplierPayment;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Supplier;

class SupplierInvoiceController extends BaseController
{
    public function index()
    {
        $supplierInvoice = new SupplierInvoice();
        $data = [
            'title' => 'Supplier Invoices',
            'invoices' => $supplierInvoice->getAllWithRelations(),
            'stats' => $this->getInvoiceStats()
        ];
        
        return view('purchase/invoices/index', $data);
    }

    public function create()
    {
        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        $supplier = new Supplier();
        
        $data = [
            'title' => 'Create Supplier Invoice',
            'purchase_orders' => $purchaseOrder->getApprovedForInvoice(),
            'goods_receipts' => $goodsReceipt->getApprovedForInvoice(),
            'suppliers' => $supplier->getAllActive(),
            'invoice_number' => $this->generateInvoiceNumber()
        ];
        
        return view('purchase/invoices/create', $data);
    }

    public function store()
    {
        $rules = [
            'invoice_number' => 'required',
            'supplier_id' => 'required|numeric',
            'invoice_date' => 'required|valid_date',
            'due_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierInvoice = new SupplierInvoice();
        $supplierInvoiceItem = new SupplierInvoiceItem();
        
        $invoiceData = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'invoice_date' => $this->request->getPost('invoice_date'),
            'due_date' => $this->request->getPost('due_date'),
            'subtotal' => $this->request->getPost('subtotal'),
            'tax_amount' => $this->request->getPost('tax_amount'),
            'total_amount' => $this->request->getPost('total_amount'),
            'currency' => $this->request->getPost('currency') ?? 'INR',
            'exchange_rate' => $this->request->getPost('exchange_rate') ?? 1,
            'payment_terms' => $this->request->getPost('payment_terms'),
            'remarks' => $this->request->getPost('remarks'),
            'status' => 'pending',
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $invoiceId = $supplierInvoice->insert($invoiceData);

        if ($invoiceId) {
            $items = json_decode($this->request->getPost('items'), true);
            
            foreach ($items as $item) {
                $itemData = [
                    'invoice_id' => $invoiceId,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => isset($item['tax_rate']) ? $item['tax_rate'] : 0,
                    'tax_amount' => isset($item['tax_amount']) ? $item['tax_amount'] : 0,
                    'line_total' => $item['line_total'],
                    'grn_id' => isset($item['grn_id']) ? $item['grn_id'] : null,
                    'po_id' => isset($item['po_id']) ? $item['po_id'] : null
                ];
                
                $supplierInvoiceItem->insert($itemData);
            }
            
            return redirect()->to('supplier-invoice')->with('success', 'Supplier Invoice created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create Supplier Invoice');
    }

    public function show($id)
    {
        $supplierInvoice = new SupplierInvoice();
        $invoice = $supplierInvoice->getWithRelations($id);
        
        if (!$invoice) {
            return redirect()->to('supplier-invoice')->with('error', 'Supplier Invoice not found');
        }

        $supplierPayment = new SupplierPayment();
        $payments = $supplierPayment->getByInvoice($id);

        $data = [
            'title' => 'View Supplier Invoice',
            'invoice' => $invoice,
            'payments' => $payments
        ];
        
        return view('purchase/invoices/show', $data);
    }

    public function edit($id)
    {
        $supplierInvoice = new SupplierInvoice();
        $invoice = $supplierInvoice->getWithRelations($id);
        
        if (!$invoice) {
            return redirect()->to('supplier-invoice')->with('error', 'Supplier Invoice not found');
        }

        if ($invoice['status'] !== 'pending' && $invoice['status'] !== 'draft') {
            return redirect()->to('supplier-invoice')->with('error', 'Cannot edit approved/paid invoice');
        }

        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        $supplier = new Supplier();
        
        $data = [
            'title' => 'Edit Supplier Invoice',
            'invoice' => $invoice,
            'purchase_orders' => $purchaseOrder->getApprovedForInvoice(),
            'goods_receipts' => $goodsReceipt->getApprovedForInvoice(),
            'suppliers' => $supplier->getAllActive()
        ];
        
        return view('purchase/invoices/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'invoice_number' => 'required',
            'supplier_id' => 'required|numeric',
            'invoice_date' => 'required|valid_date',
            'due_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierInvoice = new SupplierInvoice();
        $supplierInvoiceItem = new SupplierInvoiceItem();
        
        $invoiceData = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'invoice_date' => $this->request->getPost('invoice_date'),
            'due_date' => $this->request->getPost('due_date'),
            'subtotal' => $this->request->getPost('subtotal'),
            'tax_amount' => $this->request->getPost('tax_amount'),
            'total_amount' => $this->request->getPost('total_amount'),
            'currency' => $this->request->getPost('currency') ?? 'INR',
            'exchange_rate' => $this->request->getPost('exchange_rate') ?? 1,
            'payment_terms' => $this->request->getPost('payment_terms'),
            'remarks' => $this->request->getPost('remarks'),
            'updated_by' => session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($supplierInvoice->update($id, $invoiceData)) {
            // Delete existing items and recreate
            $supplierInvoiceItem->where('invoice_id', $id)->delete();
            
            $items = json_decode($this->request->getPost('items'), true);
            
            foreach ($items as $item) {
                $itemData = [
                    'invoice_id' => $id,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => isset($item['tax_rate']) ? $item['tax_rate'] : 0,
                    'tax_amount' => isset($item['tax_amount']) ? $item['tax_amount'] : 0,
                    'line_total' => $item['line_total'],
                    'grn_id' => isset($item['grn_id']) ? $item['grn_id'] : null,
                    'po_id' => isset($item['po_id']) ? $item['po_id'] : null
                ];
                
                $supplierInvoiceItem->insert($itemData);
            }
            
            return redirect()->to('supplier-invoice')->with('success', 'Supplier Invoice updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update Supplier Invoice');
    }

    public function delete($id)
    {
        $supplierInvoice = new SupplierInvoice();
        $invoice = $supplierInvoice->find($id);
        
        if (!$invoice) {
            return redirect()->to('supplier-invoice')->with('error', 'Supplier Invoice not found');
        }

        if ($invoice['status'] !== 'pending' && $invoice['status'] !== 'draft') {
            return redirect()->to('supplier-invoice')->with('error', 'Cannot delete approved/paid invoice');
        }

        if ($supplierInvoice->delete($id)) {
            return redirect()->to('supplier-invoice')->with('success', 'Supplier Invoice deleted successfully!');
        }

        return redirect()->to('supplier-invoice')->with('error', 'Failed to delete Supplier Invoice');
    }

    public function approve($id)
    {
        $supplierInvoice = new SupplierInvoice();
        $invoice = $supplierInvoice->find($id);
        
        if (!$invoice) {
            return redirect()->to('supplier-invoice')->with('error', 'Supplier Invoice not found');
        }

        if ($invoice['status'] !== 'pending') {
            return redirect()->to('supplier-invoice')->with('error', 'Invoice is not in pending status');
        }

        $updateData = [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($supplierInvoice->update($id, $updateData)) {
            return redirect()->to('supplier-invoice')->with('success', 'Supplier Invoice approved successfully!');
        }

        return redirect()->to('supplier-invoice')->with('error', 'Failed to approve Supplier Invoice');
    }

    public function print($id)
    {
        $supplierInvoice = new SupplierInvoice();
        $invoice = $supplierInvoice->getWithRelations($id);

        if (!$invoice) {
            return redirect()->to('supplier-invoice')->with('error', 'Supplier Invoice not found');
        }

        return view('shared/module_page', [
            'title' => 'Print Supplier Invoice',
            'page_title' => 'Print Supplier Invoice',
            'message' => 'Supplier invoice print page is available.',
            'summary' => [
                'Invoice ID' => $id,
                'Invoice Number' => $invoice['invoice_number'] ?? '',
            ],
        ]);
    }

    public function export()
    {
        return redirect()->to('supplier-invoice')->with('success', 'Supplier invoice export page is available.');
    }

    public function recordPayment($id)
    {
        $rules = [
            'payment_date' => 'required|valid_date',
            'payment_amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required',
            'reference_number' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierPayment = new SupplierPayment();
        $supplierInvoice = new SupplierInvoice();
        
        $paymentData = [
            'invoice_id' => $id,
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_amount' => $this->request->getPost('payment_amount'),
            'payment_method' => $this->request->getPost('payment_method'),
            'reference_number' => $this->request->getPost('reference_number'),
            'remarks' => $this->request->getPost('remarks'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($supplierPayment->insert($paymentData)) {
            // Update invoice status
            $this->updateInvoiceStatus($id);
            
            return redirect()->to('supplier-invoice/show/' . $id)->with('success', 'Payment recorded successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to record payment');
    }

    public function getInvoiceStats()
    {
        $supplierInvoice = new SupplierInvoice();
        
        return [
            'total' => $supplierInvoice->countAllResults(),
            'pending' => $supplierInvoice->where('status', 'pending')->countAllResults(),
            'approved' => $supplierInvoice->where('status', 'approved')->countAllResults(),
            'paid' => $supplierInvoice->where('status', 'paid')->countAllResults(),
            'overdue' => $supplierInvoice->getOverdueCount()
        ];
    }

    private function generateInvoiceNumber()
    {
        $supplierInvoice = new SupplierInvoice();
        $year = date('Y');
        $month = date('m');
        
        $count = $supplierInvoice->where('YEAR(created_at)', $year)
                                ->where('MONTH(created_at)', $month)
                                ->countAllResults();
        
        return 'INV' . $year . $month . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    private function updateInvoiceStatus($invoiceId)
    {
        $supplierInvoice = new SupplierInvoice();
        $supplierPayment = new SupplierPayment();
        
        $invoice = $supplierInvoice->find($invoiceId);
        $totalPaid = $supplierPayment->getTotalPaidByInvoice($invoiceId);
        
        if ($totalPaid >= $invoice['total_amount']) {
            $supplierInvoice->update($invoiceId, [
                'status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } elseif ($totalPaid > 0) {
            $supplierInvoice->update($invoiceId, [
                'status' => 'partially_paid',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function getOverdueInvoices()
    {
        $supplierInvoice = new SupplierInvoice();
        $invoices = $supplierInvoice->getOverdueInvoices();
        
        return $this->response->setJSON($invoices);
    }

    public function getPaymentHistory($supplierId)
    {
        $supplierPayment = new SupplierPayment();
        $payments = $supplierPayment->getBySupplier($supplierId);
        
        return $this->response->setJSON($payments);
    }

    public function getInvoiceAnalytics($filters = [])
    {
        $supplierInvoice = new SupplierInvoice();
        $analytics = $supplierInvoice->getAnalytics($filters);
        
        return $this->response->setJSON($analytics);
    }
}
