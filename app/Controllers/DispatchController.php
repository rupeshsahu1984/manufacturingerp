<?php

namespace App\Controllers;

use App\Models\DispatchNote;
use App\Models\DispatchItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use Exception;

class DispatchController extends BaseController
{
    protected $dispatchNoteModel;
    protected $dispatchItemModel;
    protected $salesOrderModel;
    protected $customerModel;
    protected $productModel;

    public function __construct()
    {
        $this->dispatchNoteModel = new DispatchNote();
        $this->dispatchItemModel = new DispatchItem();
        $this->salesOrderModel = new SalesOrder();
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
            'title' => 'Dispatch Notes - PRODX',
            'dispatch_notes' => $this->dispatchNoteModel->getDispatchNotesWithDetails($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->dispatchNoteModel->getDispatchNoteStats(),
            'filters' => $filters
        ];

        return view('dispatch/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Dispatch Note - PRODX',
            'customers' => $this->customerModel->getActiveCustomers(),
            'sales_orders' => $this->salesOrderModel->getReadyForDispatch(),
            'dn_number' => $this->dispatchNoteModel->generateUniqueDnNumber()
        ];

        return view('dispatch/create', $data);
    }

    public function store()
    {
        $rules = [
            'dn_number' => 'required|max_length[20]|is_unique[dispatch_notes.dn_number]',
            'so_id' => 'required|integer',
            'dispatch_date' => 'required|valid_date',
            'delivery_address' => 'permit_empty|max_length[65535]',
            'transport_mode' => 'permit_empty|max_length[100]',
            'vehicle_number' => 'permit_empty|max_length[50]',
            'driver_name' => 'permit_empty|max_length[100]',
            'driver_phone' => 'permit_empty|max_length[20]',
            'status' => 'required|in_list[draft,dispatched,delivered,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'dn_number' => $this->request->getPost('dn_number'),
            'so_id' => $this->request->getPost('so_id'),
            'dispatch_date' => $this->request->getPost('dispatch_date'),
            'delivery_address' => $this->request->getPost('delivery_address'),
            'transport_mode' => $this->request->getPost('transport_mode'),
            'vehicle_number' => $this->request->getPost('vehicle_number'),
            'driver_name' => $this->request->getPost('driver_name'),
            'driver_phone' => $this->request->getPost('driver_phone'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert dispatch note
            $dn_id = $this->dispatchNoteModel->insert($data);

            // Insert dispatch items from sales order
            $so_id = $this->request->getPost('so_id');
            $salesOrderItems = $this->salesOrderModel->getSalesOrderItems($so_id);
            
            foreach ($salesOrderItems as $item) {
                $itemData = [
                    'dn_id' => $dn_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'description' => isset($item['description']) ? $item['description'] : ''
                ];
                $this->dispatchItemModel->insert($itemData);
            }

            // Update sales order status to dispatched
            $this->salesOrderModel->update($so_id, ['status' => 'dispatched']);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to create dispatch note.');
            }

            return redirect()->to('dispatch')->with('success', 'Dispatch note created successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating dispatch note: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $dispatchNote = $this->dispatchNoteModel->getDispatchNoteWithDetails($id);
        
        if (!$dispatchNote) {
            return redirect()->to('dispatch')->with('error', 'Dispatch note not found.');
        }

        $data = [
            'title' => 'Dispatch Note Details - PRODX',
            'dispatch_note' => $dispatchNote
        ];

        return view('dispatch/show', $data);
    }

    public function edit($id)
    {
        $dispatchNote = $this->dispatchNoteModel->getDispatchNoteWithDetails($id);
        
        if (!$dispatchNote) {
            return redirect()->to('dispatch')->with('error', 'Dispatch note not found.');
        }

        $data = [
            'title' => 'Edit Dispatch Note - PRODX',
            'dispatch_note' => $dispatchNote,
            'customers' => $this->customerModel->getActiveCustomers(),
            'sales_orders' => $this->salesOrderModel->getReadyForDispatch()
        ];

        return view('dispatch/edit', $data);
    }

    public function update($id)
    {
        $dispatchNote = $this->dispatchNoteModel->find($id);
        
        if (!$dispatchNote) {
            return redirect()->to('dispatch')->with('error', 'Dispatch note not found.');
        }

        $rules = [
            'dn_number' => "required|max_length[20]|is_unique[dispatch_notes.dn_number,id,$id]",
            'so_id' => 'required|integer',
            'dispatch_date' => 'required|valid_date',
            'delivery_address' => 'permit_empty|max_length[65535]',
            'transport_mode' => 'permit_empty|max_length[100]',
            'vehicle_number' => 'permit_empty|max_length[50]',
            'driver_name' => 'permit_empty|max_length[100]',
            'driver_phone' => 'permit_empty|max_length[20]',
            'status' => 'required|in_list[draft,dispatched,delivered,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'dn_number' => $this->request->getPost('dn_number'),
            'so_id' => $this->request->getPost('so_id'),
            'dispatch_date' => $this->request->getPost('dispatch_date'),
            'delivery_address' => $this->request->getPost('delivery_address'),
            'transport_mode' => $this->request->getPost('transport_mode'),
            'vehicle_number' => $this->request->getPost('vehicle_number'),
            'driver_name' => $this->request->getPost('driver_name'),
            'driver_phone' => $this->request->getPost('driver_phone'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('user_id') ?? 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update dispatch note
            $this->dispatchNoteModel->update($id, $data);

            // Update sales order status based on dispatch status
            $newStatus = $this->request->getPost('status');
            if ($newStatus == 'delivered') {
                $this->salesOrderModel->update($data['so_id'], ['status' => 'delivered']);
            } elseif ($newStatus == 'dispatched') {
                $this->salesOrderModel->update($data['so_id'], ['status' => 'dispatched']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update dispatch note.');
            }

            return redirect()->to('dispatch')->with('success', 'Dispatch note updated successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating dispatch note: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $dispatchNote = $this->dispatchNoteModel->find($id);
        
        if (!$dispatchNote) {
            return redirect()->to('dispatch')->with('error', 'Dispatch note not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete dispatch items first
            $this->dispatchItemModel->where('dn_id', $id)->delete();
            
            // Delete dispatch note
            $this->dispatchNoteModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('dispatch')->with('error', 'Failed to delete dispatch note.');
            }

            return redirect()->to('dispatch')->with('success', 'Dispatch note deleted successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('dispatch')->with('error', 'Error deleting dispatch note: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $dispatchNote = $this->dispatchNoteModel->find($id);
        
        if (!$dispatchNote) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dispatch note not found.']);
        }

        $newStatus = $this->request->getPost('status');
        
        if ($this->dispatchNoteModel->update($id, ['status' => $newStatus])) {
            // Update sales order status accordingly
            if ($newStatus == 'delivered') {
                $this->salesOrderModel->update($dispatchNote['so_id'], ['status' => 'delivered']);
            } elseif ($newStatus == 'dispatched') {
                $this->salesOrderModel->update($dispatchNote['so_id'], ['status' => 'dispatched']);
            }
            
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

        $dispatchNotes = $this->dispatchNoteModel->getDispatchNotesWithDetails($filters);
        
        $filename = 'dispatch_notes_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'DN Number', 'SO Number', 'Customer', 'Dispatch Date', 'Status', 'Transport Mode', 'Driver'
        ]);
        
        foreach ($dispatchNotes as $note) {
            fputcsv($output, [
                $note['dn_number'],
                isset($note['so_number']) ? $note['so_number'] : '',
                isset($note['customer_name']) ? $note['customer_name'] : '',
                $note['dispatch_date'],
                $note['status'],
                isset($note['transport_mode']) ? $note['transport_mode'] : '',
                isset($note['driver_name']) ? $note['driver_name'] : ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function print($id)
    {
        $dispatchNote = $this->dispatchNoteModel->getDispatchNoteWithDetails($id);
        
        if (!$dispatchNote) {
            return redirect()->to('dispatch')->with('error', 'Dispatch note not found.');
        }

        $data = [
            'title' => 'Print Dispatch Note - PRODX',
            'dispatch_note' => $dispatchNote
        ];

        return view('dispatch/print', $data);
    }

    public function getSalesOrders()
    {
        $customer_id = $this->request->getGet('customer_id');
        $salesOrders = $this->salesOrderModel->getReadyForDispatch($customer_id);
        return $this->response->setJSON(['success' => true, 'sales_orders' => $salesOrders]);
    }
}
