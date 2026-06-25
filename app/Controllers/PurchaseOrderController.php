<?php

namespace App\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Exception;

class PurchaseOrderController extends BaseController
{
    protected $purchaseOrderModel;
    protected $purchaseOrderItemModel;
    protected $supplierModel;
    protected $productModel;

    public function __construct()
    {
        $this->purchaseOrderModel = new PurchaseOrder();
        $this->purchaseOrderItemModel = new PurchaseOrderItem();
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
            'title' => 'Purchase Orders - PRODX',
            'purchase_orders' => $this->purchaseOrderModel->getPurchaseOrders($filters),
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'stats' => $this->purchaseOrderModel->getPurchaseOrderStats(),
            'filters' => $filters
        ];

        return view('purchase_order/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Purchase Order - PRODX',
            'po_number' => $this->purchaseOrderModel->generatePONumber(),
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'products' => $this->productModel->getActiveProducts()
        ];

        return view('purchase_order/create', $data);
    }

    public function store()
    {
        $rules = [
            'po_number' => 'required',
            'supplier_id' => 'required|numeric',
            'order_date' => 'required|valid_date',
            'expected_date' => 'required|valid_date',
            'payment_terms' => 'permit_empty|in_list[immediate,7_days,15_days,30_days,45_days,60_days]',
            'delivery_address' => 'permit_empty|max_length[500]',
            'notes' => 'permit_empty|max_length[1000]',
            'terms_conditions' => 'permit_empty|max_length[1000]',
            'is_urgent' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('purchase-order/create')->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate items
        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items)) {
            return redirect()->to('purchase-order/create')->withInput()->with('error', 'At least one item is required.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Handle PO Number uniqueness
            $poNumber = $this->request->getPost('po_number');
            if ($this->purchaseOrderModel->where('po_number', $poNumber)->countAllResults() > 0) {
                // PO Number taken, generate new one
                $poNumber = $this->purchaseOrderModel->generatePONumber();
            }

            // Get user ID from session
            $userId = session()->get('user_id');
            if (!$userId) {
                throw new Exception('User session not found. Please login again.');
            }

            // Create purchase order - only include fields that exist in allowedFields
            $poData = [
                'po_number' => $poNumber,
                'supplier_id' => (int)$this->request->getPost('supplier_id'),
                'order_date' => $this->request->getPost('order_date'),
                'expected_date' => $this->request->getPost('expected_date'),
                'delivery_address' => $this->request->getPost('delivery_address') ?: null,
                'terms_conditions' => $this->request->getPost('terms_conditions') ?: null,
                'status' => 'draft',
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'created_by' => (int)$userId
            ];

            // Add optional fields only if they exist in model's allowedFields
            $paymentTerms = $this->request->getPost('payment_terms');
            if ($paymentTerms) {
                $poData['payment_terms'] = $paymentTerms;
            }

            $notes = $this->request->getPost('notes');
            if ($notes) {
                $poData['notes'] = $notes;
            }

            $isUrgent = $this->request->getPost('is_urgent');
            if ($isUrgent) {
                $poData['is_urgent'] = 1;
            }

            // Skip validation for insert to avoid issues with is_unique on new records
            $this->purchaseOrderModel->skipValidation(true);
            
            $poId = $this->purchaseOrderModel->insert($poData);

            if (!$poId) {
                $errors = $this->purchaseOrderModel->errors();
                $dbError = $db->error();
                $errorMsg = 'Failed to create purchase order. ';
                if ($errors) {
                    $errorMsg .= 'Validation errors: ' . implode(', ', $errors);
                }
                if ($dbError['code'] != 0) {
                    $errorMsg .= ' Database error: ' . $dbError['message'];
                }
                if (!$errors && $dbError['code'] == 0) {
                    $errorMsg .= ' Unknown error. Check database connection and table structure.';
                }
                throw new Exception($errorMsg);
            }

            // Process items
            $subtotal = 0;
            foreach ($items as $item) {
                if (empty($item['product_id']) || empty($item['quantity']) || empty($item['unit_price'])) {
                    continue;
                }

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemTotal;

                $itemData = [
                    'po_id' => (int)$poId, // Database column name
                    'product_id' => (int)$item['product_id'],
                    'quantity' => (float)$item['quantity'],
                    'unit_price' => (float)$item['unit_price'],
                    'total_amount' => (float)$itemTotal
                ];

                // Skip validation for items
                $this->purchaseOrderItemModel->skipValidation(true);
                
                if (!$this->purchaseOrderItemModel->insert($itemData)) {
                    $itemErrors = $this->purchaseOrderItemModel->errors();
                    $dbError = $db->error();
                    $errorMsg = 'Failed to add item to purchase order. ';
                    if ($itemErrors) {
                        $errorMsg .= 'Errors: ' . implode(', ', $itemErrors);
                    }
                    if ($dbError['code'] != 0) {
                        $errorMsg .= ' DB: ' . $dbError['message'];
                    }
                    throw new Exception($errorMsg);
                }
            }

            // Update totals
            $taxAmount = $subtotal * 0.18; // 18% tax
            $discountAmount = 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $this->purchaseOrderModel->update($poId, [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            return redirect()->to('purchase-order')->with('success', 'Purchase order created successfully');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('purchase-order/create')->withInput()->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        $data = [
            'title' => 'View Purchase Order - PRODX',
            'purchase_order' => $purchaseOrder
        ];

        return view('purchase_order/show', $data);
    }

    public function edit($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        // Check if purchase order can be edited
        if (!in_array($purchaseOrder['status'], ['draft', 'pending'])) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order cannot be edited in current status');
        }

        $data = [
            'title' => 'Edit Purchase Order - PRODX',
            'purchase_order' => $purchaseOrder,
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'products' => $this->productModel->getActiveProducts()
        ];

        return view('purchase_order/edit', $data);
    }

    public function update($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        // Check if purchase order can be edited
        if (!in_array($purchaseOrder['status'], ['draft', 'pending'])) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order cannot be edited in current status');
        }

        $rules = [
            'po_number' => 'required|is_unique[purchase_orders.po_number,id,' . $id . ']',
            'supplier_id' => 'required|numeric',
            'order_date' => 'required|valid_date',
            'expected_date' => 'required|valid_date',
            'payment_terms' => 'permit_empty|in_list[immediate,7_days,15_days,30_days,45_days,60_days]',
            'delivery_address' => 'permit_empty|max_length[500]',
            'notes' => 'permit_empty|max_length[1000]',
            'terms_conditions' => 'permit_empty|max_length[1000]',
            'is_urgent' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate items
        $items = $this->request->getPost('items');
        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'At least one item is required.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update purchase order
            $poData = [
                'po_number' => $this->request->getPost('po_number'),
                'supplier_id' => $this->request->getPost('supplier_id'),
                'order_date' => $this->request->getPost('order_date'),
                'expected_date' => $this->request->getPost('expected_date'),
                'payment_terms' => $this->request->getPost('payment_terms'),
                'delivery_address' => $this->request->getPost('delivery_address'),
                'notes' => $this->request->getPost('notes'),
                'terms_conditions' => $this->request->getPost('terms_conditions'),
                'is_urgent' => $this->request->getPost('is_urgent') ? 1 : 0,
                'updated_by' => session()->get('user_id') ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->purchaseOrderModel->update($id, $poData)) {
                throw new Exception('Failed to update purchase order');
            }

            // Delete existing items
            $this->purchaseOrderItemModel->where('po_id', $id)->delete();

            // Process new items
            $subtotal = 0;
            foreach ($items as $item) {
                if (empty($item['product_id']) || empty($item['quantity']) || empty($item['unit_price'])) {
                    continue;
                }

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemTotal;

                $itemData = [
                    'po_id' => $id, // Database column name
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $itemTotal,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if (!$this->purchaseOrderItemModel->insert($itemData)) {
                    throw new Exception('Failed to add item to purchase order');
                }
            }

            // Update totals
            $taxAmount = $subtotal * 0.18; // 18% tax
            $discountAmount = 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $this->purchaseOrderModel->update($id, [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            return redirect()->to('purchase-order')->with('success', 'Purchase order updated successfully');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        // Check if this is an AJAX request
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
        
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        
        if (!$purchaseOrder) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ]);
            }
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        // Check if purchase order can be deleted
        if (!in_array($purchaseOrder['status'], ['draft', 'pending'])) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Purchase order cannot be deleted in current status'
                ]);
            }
            return redirect()->to('purchase-order')->with('error', 'Purchase order cannot be deleted in current status');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete items first
            $this->purchaseOrderItemModel->where('po_id', $id)->delete();
            
            // Delete purchase order
            if (!$this->purchaseOrderModel->delete($id)) {
                throw new Exception('Failed to delete purchase order');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Purchase order deleted successfully'
                ]);
            }

            return redirect()->to('purchase-order')->with('success', 'Purchase order deleted successfully');

        } catch (Exception $e) {
            $db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete purchase order: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->to('purchase-order')->with('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        if ($purchaseOrder['status'] !== 'pending') {
            return redirect()->to('purchase-order')->with('error', 'Purchase order can only be approved from pending status');
        }

        if ($this->purchaseOrderModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id') ?? 1,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to('purchase-order')->with('success', 'Purchase order approved successfully');
        }

        return redirect()->to('purchase-order')->with('error', 'Failed to approve purchase order');
    }

    public function order($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        if (!in_array($purchaseOrder['status'], ['approved', 'pending'])) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order can only be ordered from approved or pending status');
        }

        if ($this->purchaseOrderModel->update($id, [
            'status' => 'ordered',
            'ordered_by' => session()->get('user_id') ?? 1,
            'ordered_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to('purchase-order')->with('success', 'Purchase order sent to supplier successfully');
        }

        return redirect()->to('purchase-order')->with('error', 'Failed to send purchase order');
    }

    public function receive($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        if ($purchaseOrder['status'] !== 'ordered') {
            return redirect()->to('purchase-order')->with('error', 'Purchase order can only be received from ordered status');
        }

        if ($this->purchaseOrderModel->update($id, [
            'status' => 'received',
            'received_by' => session()->get('user_id') ?? 1,
            'received_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to('purchase-order')->with('success', 'Purchase order marked as received successfully');
        }

        return redirect()->to('purchase-order')->with('error', 'Failed to mark purchase order as received');
    }

    public function cancel($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        if (in_array($purchaseOrder['status'], ['received', 'cancelled'])) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order cannot be cancelled in current status');
        }

        if ($this->purchaseOrderModel->update($id, [
            'status' => 'cancelled',
            'cancelled_by' => session()->get('user_id') ?? 1,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->to('purchase-order')->with('success', 'Purchase order cancelled successfully');
        }

        return redirect()->to('purchase-order')->with('error', 'Failed to cancel purchase order');
    }

    public function print($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($id);
        
        if (!$purchaseOrder) {
            return redirect()->to('purchase-order')->with('error', 'Purchase order not found');
        }

        $data = [
            'title' => 'Purchase Order - ' . $purchaseOrder['po_number'],
            'purchase_order' => $purchaseOrder
        ];

        return view('purchase_order/print', $data);
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
        $purchaseOrders = $this->purchaseOrderModel->getPurchaseOrders($filters);
        
        if ($format === 'csv') {
            return $this->exportToCSV($purchaseOrders);
        } elseif ($format === 'pdf') {
            return $this->exportToPDF($purchaseOrders);
        }

        return redirect()->to('purchase-order');
    }

    private function exportToCSV($purchaseOrders)
    {
        $filename = 'purchase_orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8 Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV headers
        fputcsv($output, ['PO Number', 'Supplier', 'Order Date', 'Expected Date', 'Status', 'Total Amount', 'Items', 'Created Date']);
        
        // CSV data
        foreach ($purchaseOrders as $po) {
            fputcsv($output, [
                $po['po_number'] ?? '',
                $po['supplier_name'] ?? '',
                $po['order_date'] ?? '',
                $po['expected_date'] ?? '',
                ucfirst($po['status'] ?? ''),
                number_format($po['total_amount'] ?? 0, 2),
                $po['item_count'] ?? 0,
                $po['created_at'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportToPDF($purchaseOrders)
    {
        // Check if DOMPDF is available
        if (class_exists('\Dompdf\Dompdf')) {
            try {
                // Generate PDF using DOMPDF
                $dompdf = new \Dompdf\Dompdf();
                
                // Prepare data for PDF view
                $data = [
                    'purchase_orders' => $purchaseOrders,
                    'export_date' => date('d/m/Y H:i:s'),
                    'total_count' => count($purchaseOrders),
                    'total_amount' => array_sum(array_column($purchaseOrders, 'total_amount'))
                ];
                
                // Generate HTML from view
                $html = view('purchase_order/pdf_export', $data);
                
                // Load HTML into DOMPDF
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape'); // Landscape for table
                $dompdf->render();
                
                // Generate filename
                $filename = 'Purchase_Orders_' . date('Y-m-d_H-i-s') . '.pdf';
                
                // Stream PDF to browser
                $dompdf->stream($filename, ['Attachment' => true]);
                exit;
                
            } catch (\Exception $e) {
                // Fallback to HTML print view if PDF generation fails
                log_message('error', 'PDF Export Error: ' . $e->getMessage());
                return $this->exportToHTML($purchaseOrders);
            }
        } else {
            // DOMPDF not installed, use HTML print view
            return $this->exportToHTML($purchaseOrders);
        }
    }

    private function exportToHTML($purchaseOrders)
    {
        // Prepare data for HTML view
        $data = [
            'purchase_orders' => $purchaseOrders,
            'export_date' => date('d/m/Y H:i:s'),
            'total_count' => count($purchaseOrders),
            'total_amount' => array_sum(array_column($purchaseOrders, 'total_amount'))
        ];
        
        // Return HTML view that can be printed to PDF by browser
        return view('purchase_order/pdf_export', $data);
    }
}
