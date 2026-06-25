<?php

namespace App\Controllers;

use App\Models\GateEntry;
use App\Models\GateEntryItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\PurchaseBill;
use App\Models\SalesOrder;
use App\Models\Inventory;
use App\Models\Vehicle;
use App\Models\Driver;

class GateEntryController extends BaseController
{
    protected $gateEntryModel;
    protected $gateEntryItemModel;
    protected $productModel;
    protected $supplierModel;
    protected $customerModel;
    protected $purchaseBillModel;
    protected $salesOrderModel;
    protected $inventoryModel;
    protected $vehicleModel;
    protected $driverModel;

    public function __construct()
    {
        $this->gateEntryModel = new GateEntry();
        $this->gateEntryItemModel = new GateEntryItem();
        $this->productModel = new Product();
        $this->supplierModel = new Supplier();
        $this->customerModel = new Customer();
        $this->purchaseBillModel = new PurchaseBill();
        $this->salesOrderModel = new SalesOrder();
        $this->inventoryModel = new Inventory();
        $this->vehicleModel = new Vehicle();
        $this->driverModel = new Driver();
    }

    public function index()
    {
        $data = [
            'title' => 'Gate Entry Management - PRODX',
            'gate_entries' => $this->gateEntryModel->getGateEntriesWithDetails(),
            'stats' => $this->getGateEntryStats()
        ];

        return view('gate_entry/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Gate Entry - PRODX',
            'entry_number' => $this->gateEntryModel->generateEntryNumber(),
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'customers' => $this->customerModel->getActiveCustomers(),
            'products' => $this->productModel->getActiveProducts(),
            'vehicles' => $this->vehicleModel->getActiveVehicles(),
            'drivers' => $this->driverModel->getActiveDrivers(),
            'purchase_bills' => $this->purchaseBillModel->getPendingBills(),
            'sales_orders' => $this->salesOrderModel->getReadyForDispatch()
        ];

        return view('gate_entry/create', $data);
    }

    public function store()
    {
        $rules = [
            'entry_type' => 'required|in_list[in,out]',
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'purpose' => 'required|max_length[255]',
            'items' => 'required|array|min_length[1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create gate entry
            $entryData = [
                'entry_number' => $this->request->getPost('entry_number'),
                'entry_type' => $this->request->getPost('entry_type'),
                'vehicle_id' => $this->request->getPost('vehicle_id'),
                'driver_id' => $this->request->getPost('driver_id'),
                'supplier_id' => $this->request->getPost('supplier_id') ?: null,
                'customer_id' => $this->request->getPost('customer_id') ?: null,
                'purchase_bill_id' => $this->request->getPost('purchase_bill_id') ?: null,
                'sales_order_id' => $this->request->getPost('sales_order_id') ?: null,
                'purpose' => $this->request->getPost('purpose'),
                'remarks' => $this->request->getPost('remarks'),
                'entry_time' => date('Y-m-d H:i:s'),
                'status' => 'active',
                'created_by' => session()->get('user_id')
            ];

            $entryId = $this->gateEntryModel->insert($entryData);

            // Process items
            $items = $this->request->getPost('items');
            foreach ($items as $item) {
                $itemData = [
                    'gate_entry_id' => $entryId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'batch_number' => $item['batch_number'] ?: null,
                    'expiry_date' => $item['expiry_date'] ?: null,
                    'quality_status' => isset($item['quality_status']) ? $item['quality_status'] : 'pending',
                    'remarks' => $item['remarks'] ?: null
                ];

                $this->gateEntryItemModel->insert($itemData);

                // Update inventory based on entry type
                $this->updateInventory($entryData['entry_type'], $item['product_id'], $item['quantity'], $item['batch_number']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to create gate entry. Please try again.');
            }

            return redirect()->to('gate-entry')->with('success', 'Gate entry created successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating gate entry: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $gateEntry = $this->gateEntryModel->getGateEntryWithDetails($id);
        
        if (!$gateEntry) {
            return redirect()->to('gate-entry')->with('error', 'Gate entry not found.');
        }

        $data = [
            'title' => 'Gate Entry Details - PRODX',
            'gate_entry' => $gateEntry
        ];

        return view('gate_entry/show', $data);
    }

    public function edit($id)
    {
        $gateEntry = $this->gateEntryModel->find($id);
        
        if (!$gateEntry) {
            return redirect()->to('gate-entry')->with('error', 'Gate entry not found.');
        }

        $data = [
            'title' => 'Edit Gate Entry - PRODX',
            'gate_entry' => $gateEntry,
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'customers' => $this->customerModel->getActiveCustomers(),
            'products' => $this->productModel->getActiveProducts(),
            'vehicles' => $this->vehicleModel->getActiveVehicles(),
            'drivers' => $this->driverModel->getActiveDrivers(),
            'purchase_bills' => $this->purchaseBillModel->getPendingBills(),
            'sales_orders' => $this->salesOrderModel->getReadyForDispatch()
        ];

        return view('gate_entry/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'entry_type' => 'required|in_list[in,out]',
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'purpose' => 'required|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update gate entry
            $entryData = [
                'entry_type' => $this->request->getPost('entry_type'),
                'vehicle_id' => $this->request->getPost('vehicle_id'),
                'driver_id' => $this->request->getPost('driver_id'),
                'supplier_id' => $this->request->getPost('supplier_id') ?: null,
                'customer_id' => $this->request->getPost('customer_id') ?: null,
                'purchase_bill_id' => $this->request->getPost('purchase_bill_id') ?: null,
                'sales_order_id' => $this->request->getPost('sales_order_id') ?: null,
                'purpose' => $this->request->getPost('purpose'),
                'remarks' => $this->request->getPost('remarks'),
                'updated_by' => session()->get('user_id')
            ];

            $this->gateEntryModel->update($id, $entryData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update gate entry. Please try again.');
            }

            return redirect()->to('gate-entry')->with('success', 'Gate entry updated successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating gate entry: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $gateEntry = $this->gateEntryModel->find($id);
        
        if (!$gateEntry) {
            return redirect()->to('gate-entry')->with('error', 'Gate entry not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Reverse inventory changes
            $items = $this->gateEntryItemModel->where('gate_entry_id', $id)->findAll();
            foreach ($items as $item) {
                $reverseType = $gateEntry['entry_type'] === 'in' ? 'out' : 'in';
                $this->updateInventory($reverseType, $item['product_id'], $item['quantity'], $item['batch_number']);
            }

            // Delete items and entry
            $this->gateEntryItemModel->where('gate_entry_id', $id)->delete();
            $this->gateEntryModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to delete gate entry. Please try again.');
            }

            return redirect()->to('gate-entry')->with('success', 'Gate entry deleted successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error deleting gate entry: ' . $e->getMessage());
        }
    }

    public function getProducts()
    {
        $products = $this->productModel->getActiveProducts();
        return $this->response->setJSON($products);
    }

    public function getPurchaseBillItems($billId)
    {
        $items = $this->purchaseBillModel->getBillItems($billId);
        return $this->response->setJSON($items);
    }

    public function getSalesOrderItems($orderId)
    {
        $items = $this->salesOrderModel->getOrderItems($orderId);
        return $this->response->setJSON($items);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $validStatuses = ['active', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        $this->gateEntryModel->update($id, ['status' => $status]);
        return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function export()
    {
        $gateEntries = $this->gateEntryModel->getGateEntriesWithDetails();
        
        // Generate CSV/Excel export
        $filename = 'gate_entries_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Entry Number', 'Type', 'Vehicle', 'Driver', 'Purpose', 'Entry Time', 'Status']);
        
        foreach ($gateEntries as $entry) {
            fputcsv($output, [
                $entry['entry_number'],
                ucfirst($entry['entry_type']),
                $entry['vehicle_number'],
                $entry['driver_name'],
                $entry['purpose'],
                $entry['entry_time'],
                ucfirst($entry['status'])
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function print($id)
    {
        $gateEntry = $this->gateEntryModel->getGateEntryWithDetails($id);
        
        if (!$gateEntry) {
            return redirect()->to('gate-entry')->with('error', 'Gate entry not found.');
        }

        $data = [
            'title' => 'Gate Entry Print - PRODX',
            'gate_entry' => $gateEntry
        ];

        return view('gate_entry/print', $data);
    }

    private function updateInventory($type, $productId, $quantity, $batchNumber = null)
    {
        $inventory = $this->inventoryModel->where('product_id', $productId)
                                        ->where('batch_number', $batchNumber)
                                        ->first();

        if ($type === 'in') {
            // Add to inventory
            if ($inventory) {
                $this->inventoryModel->update($inventory['id'], [
                    'quantity' => $inventory['quantity'] + $quantity,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->inventoryModel->insert([
                    'product_id' => $productId,
                    'batch_number' => $batchNumber,
                    'quantity' => $quantity,
                    'warehouse_id' => 1, // Default warehouse
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            // Remove from inventory
            if ($inventory && $inventory['quantity'] >= $quantity) {
                $this->inventoryModel->update($inventory['id'], [
                    'quantity' => $inventory['quantity'] - $quantity,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    private function getGateEntryStats()
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'today_in' => $this->gateEntryModel->where('entry_type', 'in')
                                              ->where('DATE(entry_time)', $today)
                                              ->countAllResults(),
            'today_out' => $this->gateEntryModel->where('entry_type', 'out')
                                               ->where('DATE(entry_time)', $today)
                                               ->countAllResults(),
            'month_total' => $this->gateEntryModel->where('DATE_FORMAT(entry_time, "%Y-%m")', $thisMonth)
                                                 ->countAllResults(),
            'pending' => $this->gateEntryModel->where('status', 'active')
                                             ->countAllResults()
        ];
    }
}
