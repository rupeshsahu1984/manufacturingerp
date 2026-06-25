<?php

namespace App\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;

class GoodsReceiptController extends BaseController
{
    public function index()
    {
        $goodsReceipt = new GoodsReceipt();
        $data = [
            'title' => 'Goods Receipt Notes',
            'goods_receipts' => $goodsReceipt->getAllWithRelations(),
            'stats' => $this->getGRNStats()
        ];
        
        return view('purchase/grn/index', $data);
    }

    public function create()
    {
        $purchaseOrder = new PurchaseOrder();
        $supplier = new Supplier();
        $warehouse = new Warehouse();
        
        $data = [
            'title' => 'Create Goods Receipt Note',
            'purchase_orders' => $purchaseOrder->getPendingForGRN(),
            'suppliers' => $supplier->getAllActive(),
            'warehouses' => $warehouse->getAllActive(),
            'grn_number' => $this->generateGRNNumber()
        ];
        
        return view('purchase/grn/create', $data);
    }

    public function store()
    {
        $rules = [
            'grn_number' => 'required',
            'purchase_order_id' => 'required|numeric',
            'supplier_id' => 'required|numeric',
            'warehouse_id' => 'required|numeric',
            'receipt_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $goodsReceipt = new GoodsReceipt();
        $goodsReceiptItem = new GoodsReceiptItem();
        
        $grnData = [
            'grn_number' => $this->request->getPost('grn_number'),
            'purchase_order_id' => $this->request->getPost('purchase_order_id'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'receipt_date' => $this->request->getPost('receipt_date'),
            'vehicle_number' => $this->request->getPost('vehicle_number'),
            'driver_name' => $this->request->getPost('driver_name'),
            'remarks' => $this->request->getPost('remarks'),
            'status' => 'received',
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $grnId = $goodsReceipt->insert($grnData);

        if ($grnId) {
            $items = json_decode($this->request->getPost('items'), true);
            
            foreach ($items as $item) {
                $itemData = [
                    'grn_id' => $grnId,
                    'product_id' => $item['product_id'],
                    'purchase_order_item_id' => $item['po_item_id'],
                    'received_qty' => $item['received_qty'],
                    'accepted_qty' => $item['accepted_qty'],
                    'rejected_qty' => $item['rejected_qty'],
                    'unit_price' => $item['unit_price'],
                    'remarks' => isset($item['remarks']) ? $item['remarks'] : ''
                ];
                
                $goodsReceiptItem->insert($itemData);
            }

            // Update PO status if all items received
            $this->updatePOStatus($this->request->getPost('purchase_order_id'));
            
            return redirect()->to('goods-receipt')->with('success', 'Goods Receipt Note created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create Goods Receipt Note');
    }

    public function show($id)
    {
        $goodsReceipt = new GoodsReceipt();
        $grn = $goodsReceipt->getWithRelations($id);
        
        if (!$grn) {
            return redirect()->to('goods-receipt')->with('error', 'Goods Receipt Note not found');
        }

        $data = [
            'title' => 'View Goods Receipt Note',
            'grn' => $grn
        ];
        
        return view('purchase/grn/view', $data);
    }

    public function edit($id)
    {
        $goodsReceipt = new GoodsReceipt();
        $grn = $goodsReceipt->getWithRelations($id);
        
        if (!$grn) {
            return redirect()->to('goods-receipt')->with('error', 'Goods Receipt Note not found');
        }

        if ($grn['status'] !== 'draft') {
            return redirect()->to('goods-receipt')->with('error', 'Cannot edit received GRN');
        }

        $purchaseOrder = new PurchaseOrder();
        $supplier = new Supplier();
        $warehouse = new Warehouse();
        
        $data = [
            'title' => 'Edit Goods Receipt Note',
            'grn' => $grn,
            'purchase_orders' => $purchaseOrder->getPendingForGRN(),
            'suppliers' => $supplier->getAllActive(),
            'warehouses' => $warehouse->getAllActive()
        ];
        
        return view('purchase/grn/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'grn_number' => 'required',
            'purchase_order_id' => 'required|numeric',
            'supplier_id' => 'required|numeric',
            'warehouse_id' => 'required|numeric',
            'receipt_date' => 'required|valid_date',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $goodsReceipt = new GoodsReceipt();
        $goodsReceiptItem = new GoodsReceiptItem();
        
        $grnData = [
            'grn_number' => $this->request->getPost('grn_number'),
            'purchase_order_id' => $this->request->getPost('purchase_order_id'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'receipt_date' => $this->request->getPost('receipt_date'),
            'vehicle_number' => $this->request->getPost('vehicle_number'),
            'driver_name' => $this->request->getPost('driver_name'),
            'remarks' => $this->request->getPost('remarks'),
            'updated_by' => session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($goodsReceipt->update($id, $grnData)) {
            // Delete existing items and recreate
            $goodsReceiptItem->where('grn_id', $id)->delete();
            
            $items = json_decode($this->request->getPost('items'), true);
            
            foreach ($items as $item) {
                $itemData = [
                    'grn_id' => $id,
                    'product_id' => $item['product_id'],
                    'purchase_order_item_id' => $item['po_item_id'],
                    'received_qty' => $item['received_qty'],
                    'accepted_qty' => $item['accepted_qty'],
                    'rejected_qty' => $item['rejected_qty'],
                    'unit_price' => $item['unit_price'],
                    'remarks' => isset($item['remarks']) ? $item['remarks'] : ''
                ];
                
                $goodsReceiptItem->insert($itemData);
            }
            
            return redirect()->to('goods-receipt')->with('success', 'Goods Receipt Note updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update Goods Receipt Note');
    }

    public function delete($id)
    {
        $goodsReceipt = new GoodsReceipt();
        $grn = $goodsReceipt->find($id);
        
        if (!$grn) {
            return redirect()->to('goods-receipt')->with('error', 'Goods Receipt Note not found');
        }

        if ($grn['status'] !== 'draft') {
            return redirect()->to('goods-receipt')->with('error', 'Cannot delete received GRN');
        }

        if ($goodsReceipt->delete($id)) {
            return redirect()->to('goods-receipt')->with('success', 'Goods Receipt Note deleted successfully!');
        }

        return redirect()->to('goods-receipt')->with('error', 'Failed to delete Goods Receipt Note');
    }

    public function approve($id)
    {
        $goodsReceipt = new GoodsReceipt();
        $grn = $goodsReceipt->find($id);
        
        if (!$grn) {
            return redirect()->to('goods-receipt')->with('error', 'Goods Receipt Note not found');
        }

        if ($grn['status'] !== 'received') {
            return redirect()->to('goods-receipt')->with('error', 'GRN is not in received status');
        }

        $updateData = [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($goodsReceipt->update($id, $updateData)) {
            // Update inventory
            $this->updateInventory($id);
            
            return redirect()->to('goods-receipt')->with('success', 'Goods Receipt Note approved successfully!');
        }

        return redirect()->to('goods-receipt')->with('error', 'Failed to approve Goods Receipt Note');
    }

    private function generateGRNNumber()
    {
        $goodsReceipt = new GoodsReceipt();
        $year = date('Y');
        $month = date('m');
        
        $count = $goodsReceipt->where('YEAR(created_at)', $year)
                              ->where('MONTH(created_at)', $month)
                              ->countAllResults();
        
        return 'GRN' . $year . $month . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function print($id)
    {
        $goodsReceipt = new GoodsReceipt();
        $grn = $goodsReceipt->getWithRelations($id);

        if (!$grn) {
            return redirect()->to('goods-receipt')->with('error', 'Goods Receipt Note not found');
        }

        return view('shared/module_page', [
            'title' => 'Print Goods Receipt Note',
            'message' => 'Goods receipt print page is available.',
            'summary' => [
                'GRN ID' => $id,
                'Status' => $grn['status'] ?? 'N/A',
            ],
        ]);
    }

    private function getGRNStats()
    {
        $goodsReceipt = new GoodsReceipt();
        
        return [
            'total' => $goodsReceipt->countAllResults(),
            'draft' => $goodsReceipt->where('status', 'draft')->countAllResults(),
            'received' => $goodsReceipt->where('status', 'received')->countAllResults(),
            'approved' => $goodsReceipt->where('status', 'approved')->countAllResults(),
            'rejected' => $goodsReceipt->where('status', 'rejected')->countAllResults()
        ];
    }

    private function updatePOStatus($poId)
    {
        $purchaseOrder = new PurchaseOrder();
        $po = $purchaseOrder->find($poId);
        
        if ($po) {
            // Check if all items are received
            $poItems = $purchaseOrder->getItems($poId);
            $allReceived = true;
            
            foreach ($poItems as $item) {
                if ($item['received_qty'] < $item['quantity']) {
                    $allReceived = false;
                    break;
                }
            }
            
            if ($allReceived) {
                $purchaseOrder->update($poId, ['status' => 'completed']);
            }
        }
    }

    private function updateInventory($grnId)
    {
        $goodsReceiptItem = new GoodsReceiptItem();
        $items = $goodsReceiptItem->where('grn_id', $grnId)->findAll();
        
        foreach ($items as $item) {
            // Update product stock
            $product = new Product();
            $currentStock = $product->getCurrentStock($item['product_id']);
            $newStock = $currentStock + $item['accepted_qty'];
            
            $product->updateStock($item['product_id'], $newStock);
        }
    }

    public function getPOItems($poId)
    {
        $purchaseOrder = new PurchaseOrder();
        $items = $purchaseOrder->getItems($poId);
        
        return $this->response->setJSON($items);
    }
}
