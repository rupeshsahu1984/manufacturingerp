<?php

namespace App\Controllers;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Supplier;
use App\Models\Product;

class DebitNoteController extends BaseController
{
    public function index()
    {
        $debitNote = new DebitNote();
        $supplier = new Supplier();
        
        $data = [
            'title' => 'Debit Notes',
            'debit_notes' => $debitNote->getAllWithRelations(),
            'suppliers' => $supplier->getAllActive(),
            'stats' => $this->getDebitNoteStats()
        ];
        
        return view('purchase/debit_notes/index', $data);
    }

    public function create()
    {
        $goodsReceipt = new GoodsReceipt();
        $purchaseReturn = new PurchaseReturn();
        $purchaseOrder = new PurchaseOrder();
        $supplier = new Supplier();
        $product = new Product();
        $items = array_map(static function (array $row): array {
            $row['item_name'] = $row['item_name'] ?? $row['product_name'] ?? '';
            $row['item_code'] = $row['item_code'] ?? $row['product_code'] ?? '';
            return $row;
        }, $product->where('status', 'active')->orderBy('product_name', 'ASC')->findAll());
        
        $data = [
            'title' => 'Create Debit Note',
            'goods_receipts' => $goodsReceipt->getApprovedForDebitNote(),
            'purchase_returns' => $purchaseReturn->getApprovedForDebitNote(),
            'purchase_orders' => $purchaseOrder->getPendingForGRN(),
            'suppliers' => $supplier->getAllActive(),
            'items' => $items,
            'debit_note_number' => $this->generateDebitNoteNumber()
        ];
        
        return view('purchase/debit_notes/create', $data);
    }

    public function store()
    {
        $rules = [
            'debit_note_number' => 'required',
            'supplier_id' => 'required|numeric',
            'debit_note_date' => 'required|valid_date',
            'reason' => 'required',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $debitNote = new DebitNote();
        $debitNoteItem = new DebitNoteItem();
        
        $debitNoteData = [
            'debit_note_number' => $this->request->getPost('debit_note_number'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'debit_note_date' => $this->request->getPost('debit_note_date'),
            'reason' => $this->request->getPost('reason'),
            'subtotal' => $this->request->getPost('subtotal'),
            'tax_amount' => $this->request->getPost('tax_amount'),
            'total_amount' => $this->request->getPost('total_amount'),
            'currency' => $this->request->getPost('currency') ?? 'INR',
            'exchange_rate' => $this->request->getPost('exchange_rate') ?? 1,
            'remarks' => $this->request->getPost('remarks'),
            'status' => 'pending',
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $debitNoteId = $debitNote->insert($debitNoteData);

        if ($debitNoteId) {
            $items = json_decode($this->request->getPost('items'), true);
            
            foreach ($items as $item) {
                $itemData = [
                    'debit_note_id' => $debitNoteId,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => isset($item['tax_rate']) ? $item['tax_rate'] : 0,
                    'tax_amount' => isset($item['tax_amount']) ? $item['tax_amount'] : 0,
                    'line_total' => $item['line_total'],
                    'grn_id' => isset($item['grn_id']) ? $item['grn_id'] : null,
                    'po_id' => isset($item['po_id']) ? $item['po_id'] : null,
                    'return_reason' => isset($item['return_reason']) ? $item['return_reason'] : ''
                ];
                
                $debitNoteItem->insert($itemData);
            }
            
            return redirect()->to('debit-note')->with('success', 'Debit Note created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create Debit Note');
    }

    public function show($id)
    {
        $debitNote = new DebitNote();
        $debitNoteData = $debitNote->getWithRelations($id);
        
        if (!$debitNoteData) {
            return redirect()->to('debit-note')->with('error', 'Debit Note not found');
        }

        $data = [
            'title' => 'View Debit Note',
            'debit_note' => $debitNoteData
        ];
        
        return view('purchase/debit_notes/show', $data);
    }

    public function edit($id)
    {
        $debitNote = new DebitNote();
        $debitNoteData = $debitNote->getWithRelations($id);
        
        if (!$debitNoteData) {
            return redirect()->to('debit-note')->with('error', 'Debit Note not found');
        }

        if ($debitNoteData['status'] !== 'pending' && $debitNoteData['status'] !== 'draft') {
            return redirect()->to('debit-note')->with('error', 'Cannot edit approved debit note');
        }

        $goodsReceipt = new GoodsReceipt();
        $purchaseReturn = new PurchaseReturn();
        $supplier = new Supplier();
        
        $data = [
            'title' => 'Edit Debit Note',
            'debit_note' => $debitNoteData,
            'goods_receipts' => $goodsReceipt->getApprovedForDebitNote(),
            'purchase_returns' => $purchaseReturn->getApprovedForDebitNote(),
            'suppliers' => $supplier->getAllActive()
        ];
        
        return view('purchase/debit_notes/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'debit_note_number' => 'required',
            'supplier_id' => 'required|numeric',
            'debit_note_date' => 'required|valid_date',
            'reason' => 'required',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $debitNote = new DebitNote();
        $debitNoteItem = new DebitNoteItem();
        
        $debitNoteData = [
            'debit_note_number' => $this->request->getPost('debit_note_number'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'debit_note_date' => $this->request->getPost('debit_note_date'),
            'reason' => $this->request->getPost('reason'),
            'subtotal' => $this->request->getPost('subtotal'),
            'tax_amount' => $this->request->getPost('tax_amount'),
            'total_amount' => $this->request->getPost('total_amount'),
            'currency' => $this->request->getPost('currency') ?? 'INR',
            'exchange_rate' => $this->request->getPost('exchange_rate') ?? 1,
            'remarks' => $this->request->getPost('remarks'),
            'updated_by' => session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($debitNote->update($id, $debitNoteData)) {
            // Delete existing items and recreate
            $debitNoteItem->where('debit_note_id', $id)->delete();
            
            $items = json_decode($this->request->getPost('items'), true);
            
            foreach ($items as $item) {
                $itemData = [
                    'debit_note_id' => $id,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => isset($item['tax_rate']) ? $item['tax_rate'] : 0,
                    'tax_amount' => isset($item['tax_amount']) ? $item['tax_amount'] : 0,
                    'line_total' => $item['line_total'],
                    'grn_id' => isset($item['grn_id']) ? $item['grn_id'] : null,
                    'po_id' => isset($item['po_id']) ? $item['po_id'] : null,
                    'return_reason' => isset($item['return_reason']) ? $item['return_reason'] : ''
                ];
                
                $debitNoteItem->insert($itemData);
            }
            
            return redirect()->to('debit-note')->with('success', 'Debit Note updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update Debit Note');
    }

    public function delete($id)
    {
        $debitNote = new DebitNote();
        $debitNoteData = $debitNote->find($id);
        
        if (!$debitNoteData) {
            return redirect()->to('debit-note')->with('error', 'Debit Note not found');
        }

        if ($debitNoteData['status'] !== 'pending' && $debitNoteData['status'] !== 'draft') {
            return redirect()->to('debit-note')->with('error', 'Cannot delete approved debit note');
        }

        if ($debitNote->delete($id)) {
            return redirect()->to('debit-note')->with('success', 'Debit Note deleted successfully!');
        }

        return redirect()->to('debit-note')->with('error', 'Failed to delete Debit Note');
    }

    public function approve($id)
    {
        $debitNote = new DebitNote();
        $debitNoteData = $debitNote->find($id);
        
        if (!$debitNoteData) {
            return redirect()->to('debit-note')->with('error', 'Debit Note not found');
        }

        if ($debitNoteData['status'] !== 'pending') {
            return redirect()->to('debit-note')->with('error', 'Debit Note is not in pending status');
        }

        $updateData = [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($debitNote->update($id, $updateData)) {
            return redirect()->to('debit-note')->with('success', 'Debit Note approved successfully!');
        }

        return redirect()->to('debit-note')->with('error', 'Failed to approve Debit Note');
    }

    public function getDebitNoteStats()
    {
        $debitNote = new DebitNote();
        
        return [
            'total' => $debitNote->countAllResults(),
            'pending' => $debitNote->where('status', 'pending')->countAllResults(),
            'approved' => $debitNote->where('status', 'approved')->countAllResults(),
            'rejected' => $debitNote->where('status', 'rejected')->countAllResults()
        ];
    }

    private function generateDebitNoteNumber()
    {
        $debitNote = new DebitNote();
        $year = date('Y');
        $month = date('m');
        
        $count = $debitNote->where('YEAR(created_at)', $year)
                           ->where('MONTH(created_at)', $month)
                           ->countAllResults();
        
        return 'DN' . $year . $month . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getReturnReasons()
    {
        $reasons = [
            'damaged_goods' => 'Damaged Goods',
            'wrong_specification' => 'Wrong Specification',
            'quality_issues' => 'Quality Issues',
            'late_delivery' => 'Late Delivery',
            'over_supply' => 'Over Supply',
            'expired_goods' => 'Expired Goods',
            'packaging_issues' => 'Packaging Issues',
            'other' => 'Other'
        ];
        
        return $this->response->setJSON($reasons);
    }

    public function getSupplierDebitNotes($supplierId)
    {
        $debitNote = new DebitNote();
        $debitNotes = $debitNote->getBySupplier($supplierId);
        
        return $this->response->setJSON($debitNotes);
    }

    public function print($id)
    {
        $debitNote = new DebitNote();
        $debitNoteData = $debitNote->getWithRelations($id);

        if (!$debitNoteData) {
            return redirect()->to('debit-note')->with('error', 'Debit Note not found');
        }

        return view('shared/module_page', [
            'title' => 'Print Debit Note',
            'message' => 'Debit note print page is available.',
            'summary' => [
                'Debit Note ID' => $id,
                'Status' => $debitNoteData['status'] ?? 'N/A',
            ],
        ]);
    }

    public function getDebitNoteAnalytics($filters = [])
    {
        $debitNote = new DebitNote();
        $analytics = $debitNote->getAnalytics($filters);
        
        return $this->response->setJSON($analytics);
    }

    public function createFromReturn($returnId)
    {
        $purchaseReturn = new PurchaseReturn();
        $return = $purchaseReturn->getWithRelations($returnId);
        
        if (!$return) {
            return redirect()->to('debit-note')->with('error', 'Purchase Return not found');
        }

        if ($return['status'] !== 'approved') {
            return redirect()->to('debit-note')->with('error', 'Purchase Return must be approved to create Debit Note');
        }

        $debitNote = new DebitNote();
        $debitNoteItem = new DebitNoteItem();
        
        $debitNoteData = [
            'debit_note_number' => $this->generateDebitNoteNumber(),
            'supplier_id' => $return['supplier_id'],
            'debit_note_date' => date('Y-m-d'),
            'reason' => 'Purchase Return - ' . $return['return_reason'],
            'subtotal' => $return['subtotal'],
            'tax_amount' => $return['tax_amount'],
            'total_amount' => $return['total_amount'],
            'currency' => 'INR',
            'exchange_rate' => 1,
            'remarks' => 'Auto-generated from Purchase Return: ' . $return['return_number'],
            'status' => 'pending',
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $debitNoteId = $debitNote->insert($debitNoteData);

        if ($debitNoteId) {
            foreach ($return['items'] as $item) {
                $itemData = [
                    'debit_note_id' => $debitNoteId,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => isset($item['tax_rate']) ? $item['tax_rate'] : 0,
                    'tax_amount' => isset($item['tax_amount']) ? $item['tax_amount'] : 0,
                    'line_total' => $item['line_total'],
                    'grn_id' => isset($item['grn_id']) ? $item['grn_id'] : null,
                    'po_id' => isset($item['po_id']) ? $item['po_id'] : null,
                    'return_reason' => isset($item['return_reason']) ? $item['return_reason'] : ''
                ];
                
                $debitNoteItem->insert($itemData);
            }
            
            // Update return status
            $purchaseReturn->update($returnId, ['debit_note_id' => $debitNoteId]);
            
            return redirect()->to('debit-note/show/' . $debitNoteId)->with('success', 'Debit Note created from Purchase Return successfully!');
        }

        return redirect()->to('debit-note')->with('error', 'Failed to create Debit Note from Purchase Return');
    }
}
