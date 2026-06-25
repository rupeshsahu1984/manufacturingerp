<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\SupplierInvoice;
use App\Models\DebitNote;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;

class PurchaseManagementController extends BaseController
{
    public function index()
    {
        // Dashboard view with summary
        $data = [
            'title' => 'Purchase Management Dashboard',
            'total_suppliers' => model('Supplier')->countAll(),
            'pending_prs' => model('PurchaseRequisition')->where('status', 'pending')->countAllResults(),
            'pending_pos' => model('PurchaseOrder')->where('status', 'pending')->countAllResults(),
            'pending_grns' => model('GoodsReceipt')->where('status', 'pending')->countAllResults(),
            'overdue_invoices' => model('SupplierInvoice')->where('due_date <', date('Y-m-d'))->where('status', 'pending')->countAllResults()
        ];
        
        return view('purchase/dashboard', $data);
    }

    // ==================== SUPPLIER MASTER ====================
    
    public function suppliers()
    {
        $supplierModel = new Supplier();
        $data = [
            'title' => 'Supplier Master',
            'suppliers' => $supplierModel->getWithRelations()
        ];
        
        return view('purchase/suppliers/index', $data);
    }

    public function supplierCreate()
    {
        $data = [
            'title' => 'Add New Supplier',
            'categories' => ['raw_material', 'tools', 'services', 'packaging', 'other']
        ];
        
        return view('purchase/suppliers/create', $data);
    }

    public function supplierStore()
    {
        $supplierModel = new Supplier();
        
        $rules = [
            'supplier_name' => 'required|min_length[3]',
            'contact_person' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'gst_number' => 'permit_empty|min_length[15]',
            'category' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierData = [
            'supplier_code' => $this->request->getPost('supplier_code') ?? $supplierModel->generateSupplierCode(),
            'supplier_name' => $this->request->getPost('supplier_name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'pincode' => $this->request->getPost('pincode'),
            'gst_number' => $this->request->getPost('gst_number'),
            'pan_number' => $this->request->getPost('pan_number'),
            'category' => $this->request->getPost('category'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'delivery_terms' => $this->request->getPost('delivery_terms'),
            'lead_time_days' => $this->request->getPost('lead_time_days'),
            'bank_name' => $this->request->getPost('bank_name'),
            'bank_account' => $this->request->getPost('bank_account'),
            'ifsc_code' => $this->request->getPost('ifsc_code'),
            'status' => 'active'
        ];

        if ($supplierModel->createSupplier($supplierData)) {
            return redirect()->to('/purchase/suppliers')->with('success', 'Supplier created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create supplier.');
        }
    }

    public function supplierEdit($id)
    {
        $supplierModel = new Supplier();
        $supplier = $supplierModel->find($id);
        
        if (!$supplier) {
            return redirect()->to('/purchase/suppliers')->with('error', 'Supplier not found.');
        }

        $data = [
            'title' => 'Edit Supplier',
            'supplier' => $supplier,
            'categories' => ['raw_material', 'tools', 'services', 'packaging', 'other']
        ];
        
        return view('purchase/suppliers/edit', $data);
    }

    public function supplierUpdate($id)
    {
        $supplierModel = new Supplier();
        
        $rules = [
            'supplier_name' => 'required|min_length[3]',
            'contact_person' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierData = [
            'supplier_name' => $this->request->getPost('supplier_name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'pincode' => $this->request->getPost('pincode'),
            'gst_number' => $this->request->getPost('gst_number'),
            'pan_number' => $this->request->getPost('pan_number'),
            'category' => $this->request->getPost('category'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'delivery_terms' => $this->request->getPost('delivery_terms'),
            'lead_time_days' => $this->request->getPost('lead_time_days'),
            'bank_name' => $this->request->getPost('bank_name'),
            'bank_account' => $this->request->getPost('bank_account'),
            'ifsc_code' => $this->request->getPost('ifsc_code')
        ];

        if ($supplierModel->updateSupplier($id, $supplierData)) {
            return redirect()->to('/purchase/suppliers')->with('success', 'Supplier updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update supplier.');
        }
    }

    public function getSupplierPerformance($id)
    {
        return view('shared/module_page', [
            'title' => 'Supplier Performance',
            'page_title' => 'Supplier Performance',
            'message' => 'Supplier performance page is available.',
            'summary' => [
                'Supplier ID' => $id,
            ],
        ]);
    }

    // ==================== PURCHASE REQUISITION ====================
    
    public function purchaseRequisitions()
    {
        $prModel = new PurchaseRequisition();
        $data = [
            'title' => 'Purchase Requisitions',
            'requisitions' => $prModel->getWithRelations()
        ];
        
        return view('purchase/requisitions/index', $data);
    }

    public function requisitionCreate()
    {
        $items = array_map(static function (array $item): array {
            $item['item_code'] = $item['item_code'] ?? $item['product_code'] ?? '';
            $item['item_name'] = $item['item_name'] ?? $item['product_name'] ?? '';
            $item['uom'] = $item['uom'] ?? $item['unit'] ?? $item['unit_of_measure'] ?? 'EA';
            return $item;
        }, model('Item')->findAll());

        $data = [
            'title' => 'Create Purchase Requisition',
            'items' => $items,
            'departments' => ['Production', 'Maintenance', 'Stores', 'Quality', 'Engineering'],
            'priorities' => ['low', 'normal', 'high', 'urgent']
        ];
        
        return view('purchase/requisitions/create', $data);
    }

    public function requisitionStore()
    {
        $prModel = new PurchaseRequisition();
        
        $rules = [
            'department' => 'required',
            'requested_by' => 'required',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');
        $required_dates = $this->request->getPost('required_dates');
        $priorities = $this->request->getPost('priorities');

        $requisitionData = [
            'pr_number' => $prModel->generatePRNumber(),
            'department' => $this->request->getPost('department'),
            'requested_by' => $this->request->getPost('requested_by'),
            'priority' => $this->request->getPost('priority'),
            'reason' => $this->request->getPost('reason'),
            'status' => 'pending',
            'created_by' => session()->get('user_id')
        ];

        $prId = $prModel->createRequisition($requisitionData);
        
        if ($prId) {
            // Create requisition items
            for ($i = 0; $i < count($items); $i++) {
                if (!empty($items[$i])) {
                    $itemData = [
                        'requisition_id' => $prId,
                        'item_id' => $items[$i],
                        'quantity' => $quantities[$i],
                        'required_date' => $required_dates[$i],
                        'priority' => isset($priorities[$i]) ? $priorities[$i] : 'normal'
                    ];
                    model('PurchaseRequisitionItem')->createItem($itemData);
                }
            }
            
            return redirect()->to('/purchase/requisitions')->with('success', 'Purchase Requisition created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create requisition.');
        }
    }

    public function requisitionApprove($id)
    {
        $prModel = new PurchaseRequisition();
        
        if ($prModel->approveRequisition($id, session()->get('user_id'))) {
            return redirect()->back()->with('success', 'Requisition approved successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to approve requisition.');
        }
    }

    public function requisitionReject($id)
    {
        $prModel = new PurchaseRequisition();
        $reason = $this->request->getPost('rejection_reason');
        
        if ($prModel->rejectRequisition($id, session()->get('user_id'), $reason)) {
            return redirect()->back()->with('success', 'Requisition rejected successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to reject requisition.');
        }
    }

    public function requisitionView($id)
    {
        return $this->renderPurchasePlaceholder('View Purchase Requisition', 'purchase/requisitions', $id);
    }

    public function requisitionEdit($id)
    {
        return $this->renderPurchasePlaceholder('Edit Purchase Requisition', 'purchase/requisitions', $id);
    }

    public function requisitionUpdate($id)
    {
        return redirect()->to('/purchase/requisitions')->with('success', 'Purchase requisition page is available.');
    }

    public function requisitionPrint($id)
    {
        return $this->renderPurchasePlaceholder('Print Purchase Requisition', 'purchase/requisitions', $id);
    }

    // ==================== PURCHASE ORDER ====================
    
    public function purchaseOrders()
    {
        $poModel = new PurchaseOrder();
        $data = [
            'title' => 'Purchase Orders',
            'purchase_orders' => $poModel->getWithRelations()
        ];
        
        return view('purchase/orders/index', $data);
    }

    public function orderCreate()
    {
        $data = [
            'title' => 'Create Purchase Order',
            'suppliers' => model('Supplier')->where('status', 'active')->findAll(),
            'approved_requisitions' => model('PurchaseRequisition')->where('status', 'approved')->findAll(),
            'items' => model('Item')->findAll()
        ];
        
        return view('purchase/orders/create', $data);
    }

    public function orderStore()
    {
        $poModel = new PurchaseOrder();
        
        $rules = [
            'supplier_id' => 'required|integer',
            'delivery_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');
        $rates = $this->request->getPost('rates');
        $taxes = $this->request->getPost('taxes');

        $orderData = [
            'po_number' => $poModel->generatePONumber(),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'delivery_date' => $this->request->getPost('delivery_date'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'delivery_terms' => $this->request->getPost('delivery_terms'),
            'status' => 'pending',
            'created_by' => session()->get('user_id')
        ];

        $poId = $poModel->createPurchaseOrder($orderData);
        
        if ($poId) {
            // Create order items
            for ($i = 0; $i < count($items); $i++) {
                if (!empty($items[$i])) {
                    $itemData = [
                        'purchase_order_id' => $poId,
                        'item_id' => $items[$i],
                        'quantity' => $quantities[$i],
                        'unit_rate' => $rates[$i],
                        'tax_amount' => isset($taxes[$i]) ? $taxes[$i] : 0
                    ];
                    model('PurchaseOrderItem')->createItem($itemData);
                }
            }
            
            return redirect()->to('/purchase/orders')->with('success', 'Purchase Order created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create purchase order.');
        }
    }

    public function orderApprove($id)
    {
        $poModel = new PurchaseOrder();
        
        if ($poModel->approvePurchaseOrder($id, session()->get('user_id'))) {
            return redirect()->back()->with('success', 'Purchase Order approved successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to approve purchase order.');
        }
    }

    public function orderView($id)
    {
        return $this->renderPurchasePlaceholder('View Purchase Order', 'purchase/orders', $id);
    }

    public function orderEdit($id)
    {
        return $this->renderPurchasePlaceholder('Edit Purchase Order', 'purchase/orders', $id);
    }

    public function orderUpdate($id)
    {
        return redirect()->to('/purchase/orders')->with('success', 'Purchase order page is available.');
    }

    public function orderPrint($id)
    {
        return $this->renderPurchasePlaceholder('Print Purchase Order', 'purchase/orders', $id);
    }

    // ==================== GOODS RECEIPT NOTE ====================
    
    public function goodsReceipts()
    {
        $grnModel = new GoodsReceipt();
        $data = [
            'title' => 'Goods Receipt Notes',
            'goods_receipts' => $grnModel->getWithRelations()
        ];
        
        return view('purchase/grn/index', $data);
    }

    public function grnCreate()
    {
        $data = [
            'title' => 'Create Goods Receipt Note',
            'purchase_orders' => model('PurchaseOrder')->where('status', 'approved')->findAll(),
            'warehouses' => model('Warehouse')->where('status', 'active')->findAll()
        ];
        
        return view('purchase/grn/create', $data);
    }

    public function grnStore()
    {
        $grnModel = new GoodsReceipt();
        
        $rules = [
            'purchase_order_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'received_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        $received_qty = $this->request->getPost('received_qty');
        $accepted_qty = $this->request->getPost('accepted_qty');
        $rejected_qty = $this->request->getPost('rejected_qty');
        $quality_status = $this->request->getPost('quality_status');

        $grnData = [
            'grn_number' => $grnModel->generateGRNNumber(),
            'purchase_order_id' => $this->request->getPost('purchase_order_id'),
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'received_date' => $this->request->getPost('received_date'),
            'received_by' => session()->get('user_id'),
            'status' => 'pending',
            'notes' => $this->request->getPost('notes')
        ];

        $grnId = $grnModel->createGRN($grnData);
        
        if ($grnId) {
            // Create GRN items
            for ($i = 0; $i < count($items); $i++) {
                if (!empty($items[$i])) {
                    $itemData = [
                        'goods_receipt_id' => $grnId,
                        'item_id' => $items[$i],
                        'received_quantity' => $received_qty[$i],
                        'accepted_quantity' => $accepted_qty[$i],
                        'rejected_quantity' => $rejected_qty[$i],
                        'quality_status' => $quality_status[$i]
                    ];
                    model('GoodsReceiptItem')->createItem($itemData);
                }
            }
            
            return redirect()->to('/purchase/grn')->with('success', 'GRN created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create GRN.');
        }
    }

    public function grnView($id)
    {
        return $this->renderPurchasePlaceholder('View Goods Receipt Note', 'purchase/grn', $id);
    }

    public function grnEdit($id)
    {
        return $this->renderPurchasePlaceholder('Edit Goods Receipt Note', 'purchase/grn', $id);
    }

    public function grnUpdate($id)
    {
        return redirect()->to('/purchase/grn')->with('success', 'GRN page is available.');
    }

    public function grnPrint($id)
    {
        return $this->renderPurchasePlaceholder('Print Goods Receipt Note', 'purchase/grn', $id);
    }

    // ==================== SUPPLIER INVOICES ====================
    
    public function supplierInvoices()
    {
        $invoiceModel = new SupplierInvoice();
        $data = [
            'title' => 'Supplier Invoices',
            'invoices' => $invoiceModel->getWithRelations()
        ];
        
        return view('purchase/invoices/index', $data);
    }

    public function invoiceCreate()
    {
        $data = [
            'title' => 'Create Supplier Invoice',
            'suppliers' => model('Supplier')->where('status', 'active')->findAll(),
            'purchase_orders' => model('PurchaseOrder')->where('status', 'approved')->findAll(),
            'goods_receipts' => model('GoodsReceipt')->where('status', 'completed')->findAll()
        ];
        
        return view('purchase/invoices/create', $data);
    }

    public function invoiceStore()
    {
        $invoiceModel = new SupplierInvoice();
        
        $rules = [
            'supplier_id' => 'required|integer',
            'invoice_number' => 'required',
            'invoice_date' => 'required|valid_date',
            'total_amount' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $invoiceData = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'purchase_order_id' => $this->request->getPost('purchase_order_id'),
            'goods_receipt_id' => $this->request->getPost('goods_receipt_id'),
            'invoice_date' => $this->request->getPost('invoice_date'),
            'due_date' => $this->request->getPost('due_date'),
            'total_amount' => $this->request->getPost('total_amount'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'status' => 'pending'
        ];

        if ($invoiceModel->createInvoice($invoiceData)) {
            return redirect()->to('/purchase/invoices')->with('success', 'Invoice created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create invoice.');
        }
    }

    public function invoiceView($id)
    {
        return $this->renderPurchasePlaceholder('View Supplier Invoice', 'purchase/invoices', $id);
    }

    public function invoiceEdit($id)
    {
        return $this->renderPurchasePlaceholder('Edit Supplier Invoice', 'purchase/invoices', $id);
    }

    public function invoiceUpdate($id)
    {
        return redirect()->to('/purchase/invoices')->with('success', 'Supplier invoice page is available.');
    }

    public function invoicePrint($id)
    {
        return $this->renderPurchasePlaceholder('Print Supplier Invoice', 'purchase/invoices', $id);
    }

    // ==================== DEBIT NOTES ====================
    
    public function debitNotes()
    {
        $debitNoteModel = new DebitNote();
        $data = [
            'title' => 'Debit Notes',
            'debit_notes' => $debitNoteModel->getWithRelations(),
            'suppliers' => model('Supplier')->where('status', 'active')->findAll(),
        ];
        
        return view('purchase/debit_notes/index', $data);
    }

    public function debitNoteCreate()
    {
        $products = model('Product')->where('status', 'active')->findAll();
        $items = array_map(static function (array $row): array {
            $row['item_name'] = $row['item_name'] ?? $row['product_name'] ?? '';
            $row['item_code'] = $row['item_code'] ?? $row['product_code'] ?? '';
            return $row;
        }, $products);

        $data = [
            'title' => 'Create Debit Note',
            'suppliers' => model('Supplier')->where('status', 'active')->findAll(),
            'goods_receipts' => model('GoodsReceipt')->whereIn('status', ['approved', 'verified', 'received'])->findAll(),
            'purchase_orders' => model('PurchaseOrder')->getPendingForGRN(),
            'purchase_returns' => model('PurchaseReturn')->getApprovedForDebitNote(),
            'items' => $items,
            'debit_note_number' => 'DN' . date('YmdHis'),
        ];
        
        return view('purchase/debit_notes/create', $data);
    }

    public function debitNoteStore()
    {
        $debitNoteModel = new DebitNote();
        
        $rules = [
            'supplier_id' => 'required|integer',
            'return_reason' => 'required',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');
        $unit_prices = $this->request->getPost('unit_prices');
        $totals = $this->request->getPost('totals');

        $debitNoteData = [
            'debit_note_number' => $debitNoteModel->generateDebitNoteNumber(),
            'debit_note_date' => $this->request->getPost('debit_note_date'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'goods_receipt_id' => $this->request->getPost('goods_receipt_id'),
            'purchase_order_id' => $this->request->getPost('purchase_order_id'),
            'return_reason' => $this->request->getPost('return_reason'),
            'quality_issue_type' => $this->request->getPost('quality_issue_type'),
            'severity' => $this->request->getPost('severity'),
            'corrective_action' => $this->request->getPost('corrective_action'),
            'return_method' => $this->request->getPost('return_method'),
            'expected_resolution_date' => $this->request->getPost('expected_resolution_date'),
            'priority' => $this->request->getPost('priority'),
            'notes' => $this->request->getPost('notes'),
            'status' => 'pending',
            'created_by' => session()->get('user_id')
        ];

        $debitNoteId = $debitNoteModel->createDebitNote($debitNoteData);
        
        if ($debitNoteId) {
            // Create debit note items
            for ($i = 0; $i < count($items); $i++) {
                if (!empty($items[$i])) {
                    $itemData = [
                        'debit_note_id' => $debitNoteId,
                        'item_id' => $items[$i],
                        'quantity' => $quantities[$i],
                        'unit_price' => $unit_prices[$i],
                        'total_amount' => $totals[$i],
                        'batch_number' => $this->request->getPost('batch_numbers')[$i] ?? '',
                        'notes' => $this->request->getPost('item_notes')[$i] ?? ''
                    ];
                    model('DebitNoteItem')->createItem($itemData);
                }
            }
            
            return redirect()->to('/purchase/debit-notes')->with('success', 'Debit Note created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create debit note.');
        }
    }

    public function debitNoteView($id)
    {
        $debitNoteModel = new DebitNote();
        $debitNote = $debitNoteModel->getWithRelations($id);
        
        if (!$debitNote) {
            return redirect()->to('/purchase/debit-notes')->with('error', 'Debit Note not found.');
        }

        $data = [
            'title' => 'View Debit Note',
            'debit_note' => $debitNote
        ];
        
        return view('purchase/debit_notes/view', $data);
    }

    public function debitNoteEdit($id)
    {
        $debitNoteModel = new DebitNote();
        $debitNote = $debitNoteModel->getWithRelations($id);
        
        if (!$debitNote) {
            return redirect()->to('/purchase/debit-notes')->with('error', 'Debit Note not found.');
        }

        $data = [
            'title' => 'Edit Debit Note',
            'debit_note' => $debitNote,
            'suppliers' => model('Supplier')->where('status', 'active')->findAll(),
            'items' => model('Item')->findAll()
        ];
        
        return view('purchase/debit_notes/edit', $data);
    }

    public function debitNoteUpdate($id)
    {
        $debitNoteModel = new DebitNote();
        
        $rules = [
            'supplier_id' => 'required|integer',
            'return_reason' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $debitNoteData = [
            'debit_note_date' => $this->request->getPost('debit_note_date'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'return_reason' => $this->request->getPost('return_reason'),
            'quality_issue_type' => $this->request->getPost('quality_issue_type'),
            'severity' => $this->request->getPost('severity'),
            'corrective_action' => $this->request->getPost('corrective_action'),
            'return_method' => $this->request->getPost('return_method'),
            'expected_resolution_date' => $this->request->getPost('expected_resolution_date'),
            'priority' => $this->request->getPost('priority'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($debitNoteModel->updateDebitNote($id, $debitNoteData)) {
            return redirect()->to('/purchase/debit-notes')->with('success', 'Debit Note updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update debit note.');
        }
    }

    public function debitNoteApprove($id)
    {
        $debitNoteModel = new DebitNote();
        
        if ($debitNoteModel->approveDebitNote($id, session()->get('user_id'))) {
            return redirect()->back()->with('success', 'Debit Note approved successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to approve debit note.');
        }
    }

    public function debitNoteReject($id)
    {
        $debitNoteModel = new DebitNote();
        $reason = $this->request->getPost('rejection_reason');
        
        if ($debitNoteModel->rejectDebitNote($id, session()->get('user_id'), $reason)) {
            return redirect()->back()->with('success', 'Debit Note rejected successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to reject debit note.');
        }
    }

    public function debitNoteProcess($id)
    {
        $debitNoteModel = new DebitNote();
        
        if ($debitNoteModel->processDebitNote($id, session()->get('user_id'))) {
            return redirect()->back()->with('success', 'Debit Note processed successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to process debit note.');
        }
    }

    public function debitNotePrint($id)
    {
        $debitNoteModel = new DebitNote();
        $debitNote = $debitNoteModel->getWithRelations($id);
        
        if (!$debitNote) {
            return redirect()->to('/purchase/debit-notes')->with('error', 'Debit Note not found.');
        }

        $data = [
            'title' => 'Print Debit Note',
            'debit_note' => $debitNote
        ];
        
        return view('purchase/debit_notes/print', $data);
    }

    // ==================== REPORTS ====================
    
    public function reports()
    {
        $data = [
            'title' => 'Purchase Reports',
            'pending_orders' => $this->safeReportData(static fn () => model('PurchaseOrder')->getPendingOrders()),
            'supplier_performance' => $this->safeReportData(static fn () => model('Supplier')->getPerformanceStats()),
            'spend_analysis' => $this->safeReportData(static fn () => model('PurchaseOrder')->getSpendAnalysis()),
            'overdue_invoices' => $this->safeReportData(static fn () => model('SupplierInvoice')->getOverdueInvoices())
        ];
        
        return view('purchase/reports/index', $data);
    }

    public function exportReport($type)
    {
        switch ($type) {
            case 'pending_orders':
                $data = model('PurchaseOrder')->getPendingOrders();
                return $this->exportToCSV($data, 'pending_orders');
                
            case 'supplier_performance':
                $data = model('Supplier')->getPerformanceStats();
                return $this->exportToCSV($data, 'supplier_performance');
                
            case 'spend_analysis':
                $data = model('PurchaseOrder')->getSpendAnalysis();
                return $this->exportToCSV($data, 'spend_analysis');
                
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }
    }

    private function exportToCSV($data, $filename)
    {
        $csv = '';
        
        if (!empty($data)) {
            // Headers
            $csv .= implode(',', array_keys($data[0])) . "\n";
            
            // Data rows
            foreach ($data as $row) {
                $csv .= implode(',', array_values($row)) . "\n";
            }
        }
        
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
        
        return $this->response->setBody($csv);
    }

    private function renderPurchasePlaceholder(string $title, string $section, int $id)
    {
        return view('shared/module_page', [
            'title' => $title,
            'page_title' => $title,
            'message' => ucfirst(str_replace('/', ' ', $section)) . ' detail page is available.',
            'summary' => [
                'Record ID' => $id,
            ],
        ]);
    }

    private function safeReportData(callable $loader): array
    {
        try {
            return $loader() ?: [];
        } catch (\Throwable $e) {
            log_message('error', 'PurchaseManagementController::reports: ' . $e->getMessage());
            return [];
        }
    }
}
