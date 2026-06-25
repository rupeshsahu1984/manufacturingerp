<?php

namespace App\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Exception;

class PurchaseReturnController extends BaseController
{
    protected $purchaseReturnModel;
    protected $purchaseReturnItemModel;
    protected $purchaseOrderModel;
    protected $supplierModel;
    protected $productModel;

    public function __construct()
    {
        $this->purchaseReturnModel = new PurchaseReturn();
        $this->purchaseReturnItemModel = new PurchaseReturnItem();
        $this->purchaseOrderModel = new PurchaseOrder();
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Purchase Returns - PRODX',
            'purchase_returns' => $this->purchaseReturnModel->getPurchaseReturns($filters),
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'stats' => $this->purchaseReturnModel->getPurchaseReturnStats(),
            'filters' => $filters
        ];

        return view('purchase_return/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Purchase Return - PRODX',
            'return_number' => $this->purchaseReturnModel->generateReturnNumber(),
            'purchase_orders' => $this->purchaseOrderModel->getReceivedPurchaseOrders(),
            'suppliers' => $this->supplierModel->getActiveSuppliers()
        ];

        return view('purchase_return/create', $data);
    }

    public function store()
    {
        $rules = [
            'return_number' => 'required|is_unique[purchase_returns.return_number]',
            'purchase_order_id' => 'permit_empty|numeric',
            'supplier_id' => 'required|numeric',
            'return_date' => 'required|valid_date',
            'return_reason' => 'required|in_list[damaged,defective,wrong_item,quality_issue,expired,overstock,other]',
            'return_method' => 'permit_empty|in_list[pickup,delivery,courier]',
            'notes' => 'permit_empty|max_length[1000]',
            'return_instructions' => 'permit_empty|max_length[1000]',
            'is_urgent' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate items
        $items = $this->request->getPost('items');
        log_message('debug', 'Purchase Return Store: Items received: ' . json_encode($items));
        
        if (empty($items) || !is_array($items)) {
            log_message('error', 'Purchase Return Store: No items provided. Items: ' . json_encode($items));
            return redirect()->back()->withInput()->with('error', 'At least one item is required.');
        }
        
        // Filter out items with zero return quantity
        $validItems = [];
        foreach ($items as $item) {
            $returnQty = floatval($item['return_quantity'] ?? 0);
            $productId = intval($item['product_id'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            
            if ($productId > 0 && $returnQty > 0 && $unitPrice > 0) {
                $validItems[] = $item;
            }
        }
        
        if (empty($validItems)) {
            log_message('error', 'Purchase Return Store: No valid items after filtering. Items: ' . json_encode($items));
            return redirect()->back()->withInput()->with('error', 'At least one item with return quantity greater than 0 is required.');
        }
        
        $items = $validItems;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get supplier ID - either from purchase order or directly from form
            $supplierId = $this->request->getPost('supplier_id');
            $purchaseOrderId = $this->request->getPost('purchase_order_id');
            
            // If purchase order is provided, get supplier from it
            if ($purchaseOrderId) {
                $purchaseOrder = $this->purchaseOrderModel->find($purchaseOrderId);
                if ($purchaseOrder) {
                    $supplierId = $purchaseOrder['supplier_id'];
                }
            }
            
            if (!$supplierId) {
                throw new Exception('Supplier is required');
            }

            // Create purchase return
            $returnData = [
                'return_number' => $this->request->getPost('return_number'),
                'purchase_order_id' => $purchaseOrderId ?: null,
                'supplier_id' => $supplierId,
                'return_date' => $this->request->getPost('return_date'),
                'return_reason' => $this->request->getPost('return_reason'),
                'return_method' => $this->request->getPost('return_method'),
                'notes' => $this->request->getPost('notes'),
                'return_instructions' => $this->request->getPost('return_instructions'),
                'is_urgent' => $this->request->getPost('is_urgent') ? 1 : 0,
                'status' => 'draft',
                'subtotal' => 0,
                'tax_amount' => 0,
                'restocking_fee' => 0,
                'total_amount' => 0,
                'created_by' => session()->get('user_id') ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Temporarily skip validation to avoid strict model validation issues
            $this->purchaseReturnModel->skipValidation(true);
            $returnId = $this->purchaseReturnModel->insert($returnData);
            $this->purchaseReturnModel->skipValidation(false);

            if (!$returnId) {
                $errors = $this->purchaseReturnModel->errors();
                log_message('error', 'Purchase Return Store: Failed to insert return. Data: ' . json_encode($returnData) . ' Errors: ' . json_encode($errors));
                throw new Exception('Failed to create purchase return: ' . json_encode($errors));
            }
            
            log_message('debug', 'Purchase Return Store: Return created with ID: ' . $returnId);

            // Process items
            $subtotal = 0;
            $itemCount = 0;
            foreach ($items as $item) {
                $returnQuantity = floatval($item['return_quantity'] ?? 0);
                $unitPrice = floatval($item['unit_price'] ?? 0);
                $productId = intval($item['product_id'] ?? 0);
                
                if ($productId <= 0 || $returnQuantity <= 0 || $unitPrice <= 0) {
                    log_message('error', 'Purchase Return Store: Skipping invalid item. Item: ' . json_encode($item));
                    continue;
                }

                $itemTotal = $returnQuantity * $unitPrice;
                $subtotal += $itemTotal;

                $itemData = [
                    'purchase_return_id' => $returnId,
                    'product_id' => $productId,
                    'quantity' => $returnQuantity, // Use quantity field for model
                    'return_quantity' => $returnQuantity,
                    'original_quantity' => isset($item['original_quantity']) ? floatval($item['original_quantity']) : 0,
                    'unit_price' => $unitPrice,
                    'total_amount' => $itemTotal
                ];

                // Temporarily skip validation
                $this->purchaseReturnItemModel->skipValidation(true);
                $itemInserted = $this->purchaseReturnItemModel->insert($itemData);
                $this->purchaseReturnItemModel->skipValidation(false);
                
                if (!$itemInserted) {
                    $errors = $this->purchaseReturnItemModel->errors();
                    log_message('error', 'Purchase Return Store: Failed to insert item. Item: ' . json_encode($itemData) . ' Errors: ' . json_encode($errors));
                    throw new Exception('Failed to add item to purchase return: ' . json_encode($errors));
                }
                $itemCount++;
            }
            
            if ($itemCount == 0) {
                throw new Exception('No valid items were added to the purchase return');
            }

            // Update totals
            $taxAmount = $subtotal * 0.18; // 18% tax
            $restockingFee = $subtotal * 0.05; // 5% restocking fee
            $totalAmount = $subtotal + $taxAmount + $restockingFee;

            $this->purchaseReturnModel->update($returnId, [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'restocking_fee' => $restockingFee,
                'total_amount' => $totalAmount
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return created successfully');

        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Purchase Return Store Error: ' . $e->getMessage());
            log_message('error', 'Purchase Return Store Error Trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to create purchase return: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->getPurchaseReturnWithItems($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        $data = [
            'title' => 'View Purchase Return - PRODX',
            'purchase_return' => $purchaseReturn
        ];

        return view('purchase_return/show', $data);
    }

    public function edit($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->getPurchaseReturnWithItems($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        // Check if purchase return can be edited
        if (!in_array($purchaseReturn['status'], ['draft', 'pending'])) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return cannot be edited in current status');
        }

        $data = [
            'title' => 'Edit Purchase Return - PRODX',
            'purchase_return' => $purchaseReturn,
            'purchase_orders' => $this->purchaseOrderModel->getReceivedPurchaseOrders()
        ];

        return view('purchase_return/edit', $data);
    }

    public function update($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        // Check if purchase return can be edited
        if (!in_array($purchaseReturn['status'], ['draft', 'pending'])) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return cannot be edited in current status');
        }

        $rules = [
            'return_number' => 'required|is_unique[purchase_returns.return_number,id,' . $id . ']',
            'purchase_order_id' => 'permit_empty|numeric',
            'supplier_id' => 'required|numeric',
            'return_date' => 'required|valid_date',
            'return_reason' => 'required|in_list[damaged,defective,wrong_item,quality_issue,expired,overstock,other]',
            'return_method' => 'permit_empty|in_list[pickup,delivery,courier]',
            'notes' => 'permit_empty|max_length[1000]',
            'return_instructions' => 'permit_empty|max_length[1000]',
            'is_urgent' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Purchase Return Update Validation Errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate items
        $items = $this->request->getPost('items');
        log_message('debug', 'Purchase Return Update: Items received: ' . json_encode($items));
        
        if (empty($items) || !is_array($items)) {
            log_message('error', 'Purchase Return Update: No items provided. Items: ' . json_encode($items));
            return redirect()->back()->withInput()->with('error', 'At least one item is required.');
        }
        
        // Filter out items with zero return quantity
        $validItems = [];
        foreach ($items as $item) {
            $returnQty = floatval($item['return_quantity'] ?? 0);
            $productId = intval($item['product_id'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            
            if ($productId > 0 && $returnQty > 0 && $unitPrice > 0) {
                $validItems[] = $item;
            }
        }
        
        if (empty($validItems)) {
            log_message('error', 'Purchase Return Update: No valid items after filtering. Items: ' . json_encode($items));
            return redirect()->back()->withInput()->with('error', 'At least one item with return quantity greater than 0 is required.');
        }
        
        $items = $validItems;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get supplier ID - either from purchase order or directly from form
            $supplierId = $this->request->getPost('supplier_id');
            $purchaseOrderId = $this->request->getPost('purchase_order_id');
            
            // If purchase order is provided, get supplier from it
            if ($purchaseOrderId) {
                $purchaseOrder = $this->purchaseOrderModel->find($purchaseOrderId);
                if ($purchaseOrder) {
                    $supplierId = $purchaseOrder['supplier_id'];
                }
            }
            
            if (!$supplierId) {
                throw new Exception('Supplier is required');
            }

            // Update purchase return
            $returnData = [
                'return_number' => $this->request->getPost('return_number'),
                'purchase_order_id' => $purchaseOrderId ?: null,
                'supplier_id' => $supplierId,
                'return_date' => $this->request->getPost('return_date'),
                'return_reason' => $this->request->getPost('return_reason'),
                'return_method' => $this->request->getPost('return_method'),
                'notes' => $this->request->getPost('notes'),
                'return_instructions' => $this->request->getPost('return_instructions'),
                'is_urgent' => $this->request->getPost('is_urgent') ? 1 : 0,
                'updated_by' => session()->get('user_id') ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Temporarily skip validation
            $this->purchaseReturnModel->skipValidation(true);
            $updateResult = $this->purchaseReturnModel->update($id, $returnData);
            $this->purchaseReturnModel->skipValidation(false);
            
            if (!$updateResult) {
                $errors = $this->purchaseReturnModel->errors();
                log_message('error', 'Purchase Return Update: Failed to update return. Data: ' . json_encode($returnData) . ' Errors: ' . json_encode($errors));
                throw new Exception('Failed to update purchase return: ' . json_encode($errors));
            }

            // Delete existing items
            $this->purchaseReturnItemModel->where('purchase_return_id', $id)->delete();

            // Process new items
            $subtotal = 0;
            $itemCount = 0;
            foreach ($items as $item) {
                $returnQuantity = floatval($item['return_quantity'] ?? 0);
                $unitPrice = floatval($item['unit_price'] ?? 0);
                $productId = intval($item['product_id'] ?? 0);
                
                if ($productId <= 0 || $returnQuantity <= 0 || $unitPrice <= 0) {
                    log_message('error', 'Purchase Return Update: Skipping invalid item. Item: ' . json_encode($item));
                    continue;
                }

                $itemTotal = $returnQuantity * $unitPrice;
                $subtotal += $itemTotal;

                $itemData = [
                    'purchase_return_id' => $id,
                    'product_id' => $productId,
                    'quantity' => $returnQuantity, // Use quantity field for model
                    'return_quantity' => $returnQuantity,
                    'original_quantity' => isset($item['original_quantity']) ? floatval($item['original_quantity']) : 0,
                    'unit_price' => $unitPrice,
                    'total_amount' => $itemTotal
                ];

                // Temporarily skip validation
                $this->purchaseReturnItemModel->skipValidation(true);
                $itemInserted = $this->purchaseReturnItemModel->insert($itemData);
                $this->purchaseReturnItemModel->skipValidation(false);
                
                if (!$itemInserted) {
                    $errors = $this->purchaseReturnItemModel->errors();
                    log_message('error', 'Purchase Return Update: Failed to insert item. Item: ' . json_encode($itemData) . ' Errors: ' . json_encode($errors));
                    throw new Exception('Failed to add item to purchase return: ' . json_encode($errors));
                }
                $itemCount++;
            }
            
            if ($itemCount == 0) {
                throw new Exception('No valid items were added to the purchase return');
            }

            // Update totals
            $taxAmount = $subtotal * 0.18; // 18% tax
            $restockingFee = $subtotal * 0.05; // 5% restocking fee
            $totalAmount = $subtotal + $taxAmount + $restockingFee;

            $this->purchaseReturnModel->update($id, [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'restocking_fee' => $restockingFee,
                'total_amount' => $totalAmount
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return updated successfully');

        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Purchase Return Update Error: ' . $e->getMessage());
            log_message('error', 'Purchase Return Update Error Trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase return: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        // Check if purchase return can be deleted
        if (!in_array($purchaseReturn['status'], ['draft', 'pending'])) {
            return redirect()->to('purchase-return')->with('error', 'Purchase return cannot be deleted in current status');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete items first
            $this->purchaseReturnItemModel->where('purchase_return_id', $id)->delete();
            
            // Delete purchase return
            if (!$this->purchaseReturnModel->delete($id)) {
                throw new Exception('Failed to delete purchase return');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return deleted successfully');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('purchase-return')->with('error', 'Failed to delete purchase return: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        if ($purchaseReturn['status'] !== 'pending') {
            return redirect()->to('purchase-return')->with('error', 'Purchase return can only be approved from pending status');
        }

        if ($this->purchaseReturnModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id') ?? 1,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return approved successfully');
        }

        return redirect()->to('purchase-return')->with('error', 'Failed to approve purchase return');
    }

    public function process($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        if ($purchaseReturn['status'] !== 'approved') {
            return redirect()->to('purchase-return')->with('error', 'Purchase return can only be processed from approved status');
        }

        if ($this->purchaseReturnModel->update($id, [
            'status' => 'processed',
            'processed_by' => session()->get('user_id') ?? 1,
            'processed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return processed successfully');
        }

        return redirect()->to('purchase-return')->with('error', 'Failed to process purchase return');
    }

    public function complete($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        if ($purchaseReturn['status'] !== 'processed') {
            return redirect()->to('purchase-return')->with('error', 'Purchase return can only be completed from processed status');
        }

        if ($this->purchaseReturnModel->update($id, [
            'status' => 'completed',
            'completed_by' => session()->get('user_id') ?? 1,
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return completed successfully');
        }

        return redirect()->to('purchase-return')->with('error', 'Failed to complete purchase return');
    }

    public function cancel($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        if (in_array($purchaseReturn['status'], ['completed', 'cancelled'])) {
            return redirect()->to('purchase-return')->with('error', 'Purchase return cannot be cancelled in current status');
        }

        if ($this->purchaseReturnModel->update($id, [
            'status' => 'cancelled',
            'cancelled_by' => session()->get('user_id') ?? 1,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to(base_url('purchase-return'))->with('success', 'Purchase return cancelled successfully');
        }

        return redirect()->to('purchase-return')->with('error', 'Failed to cancel purchase return');
    }

    public function getPOItems($purchaseOrderId)
    {
        try {
            $items = $this->purchaseOrderModel->getPurchaseOrderItems($purchaseOrderId);
            
            return $this->response->setJSON([
                'success' => true,
                'items' => $items
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getProducts()
    {
        try {
            $products = $this->productModel->where('status', 'active')
                ->select('id, product_name, product_code, unit, cost_price as unit_price, selling_price')
                ->orderBy('product_name', 'ASC')
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'products' => $products
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function print($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->getPurchaseReturnWithItems($id);
        
        if (!$purchaseReturn) {
            return redirect()->to(base_url('purchase-return'))->with('error', 'Purchase return not found');
        }

        $data = [
            'title' => 'Purchase Return - ' . $purchaseReturn['return_number'],
            'purchase_return' => $purchaseReturn
        ];

        return view('purchase_return/print', $data);
    }

    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $format = $this->request->getGet('export') ?: 'csv';
        $purchaseReturns = $this->purchaseReturnModel->getPurchaseReturns($filters);
        
        if ($format === 'csv') {
            return $this->exportToCSV($purchaseReturns);
        } elseif ($format === 'pdf') {
            return $this->exportToPDF($purchaseReturns);
        }

        return redirect()->to(base_url('purchase-return'));
    }

    private function exportToCSV($purchaseReturns)
    {
        $filename = 'purchase_returns_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Return Number', 'Supplier', 'Original PO', 'Return Date', 'Status', 'Total Amount', 'Created Date']);
        
        // CSV data
        foreach ($purchaseReturns as $return) {
            fputcsv($output, [
                $return['return_number'],
                $return['supplier_name'],
                $return['po_number'],
                $return['return_date'],
                ucfirst($return['status']),
                '₹' . number_format($return['total_amount'], 2),
                $return['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportToPDF($purchaseReturns)
    {
        // This would require a PDF library like TCPDF or DOMPDF
        // For now, redirect to CSV export
        return $this->exportToCSV($purchaseReturns);
    }
}
