<?php

namespace App\Controllers;

use App\Models\Customer;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerController extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
    }

    /**
     * Display list of customers
     */
    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'sales_zone' => $this->request->getGet('sales_zone'),
            'sales_region' => $this->request->getGet('sales_region'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Customer Master - PRODX',
            'customers' => $this->customerModel->getCustomers($filters),
            'stats' => $this->customerModel->getCustomerStats(),
            'filters' => $filters
        ];

        return view('customer/index', $data);
    }

    /**
     * Show customer creation form
     */
    public function create()
    {
        log_message('debug', '=== CUSTOMER CREATE METHOD CALLED ===');
        $data = [
            'title' => 'Create Customer - PRODX'
        ];

        return view('customer/create', $data);
    }

    /**
     * Store new customer
     */
    public function store()
    {
        // Always log when method is called
        log_message('debug', '=== CUSTOMER STORE METHOD CALLED ===');
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'GET data: ' . json_encode($this->request->getGet()));
        log_message('debug', 'Headers: ' . json_encode($this->request->getHeaders()));

        $rules = [
            'customer_name' => 'required|min_length[3]|max_length[255]',
            'contact_person' => 'required|min_length[2]|max_length[255]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'email' => 'permit_empty|valid_email',
            'address' => 'required|min_length[5]',
            'city' => 'required|min_length[2]|max_length[100]',
            'state' => 'required|min_length[2]|max_length[100]',
            'pincode' => 'required|min_length[6]|max_length[10]',
            'credit_limit' => 'permit_empty|numeric',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', '=== VALIDATION FAILED ===');
            log_message('debug', 'Validation errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        
        // Generate customer code
        $data['customer_code'] = $this->customerModel->generateCustomerCode($data['customer_name']);
        $data['created_by'] = session()->get('user_id') ?? 1;

        log_message('debug', '=== DATA PREPARED FOR INSERT ===');
        log_message('debug', 'Data to insert: ' . json_encode($data));

        try {
            log_message('debug', '=== ATTEMPTING DATABASE INSERT ===');
            $result = $this->customerModel->insert($data);
            log_message('debug', '=== INSERT SUCCESSFUL ===');
            log_message('debug', 'Insert result: ' . json_encode($result));
            return redirect()->to('customer')->with('success', 'Customer created successfully!');
        } catch (\Exception $e) {
            log_message('error', '=== INSERT FAILED ===');
            log_message('error', 'Customer insert error: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    /**
     * Show customer details
     */
    public function show($id = null)
    {
        $customer = $this->customerModel->getCustomerWithHistory($id);
        
        if (!$customer) {
            return redirect()->to('customer')->with('error', 'Customer not found!');
        }

        $data = [
            'title' => 'Customer Details - PRODX',
            'customer' => $customer,
            'performance' => $this->customerModel->getCustomerPerformance($id)
        ];

        return view('customer/show', $data);
    }

    /**
     * Show customer edit form
     */
    public function edit($id = null)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return redirect()->to('customer')->with('error', 'Customer not found!');
        }

        $data = [
            'title' => 'Edit Customer - PRODX',
            'customer' => $customer
        ];

        return view('customer/edit', $data);
    }

    /**
     * Update customer
     */
    public function update($id = null)
    {
        // Always log when method is called
        log_message('debug', '=== CUSTOMER UPDATE METHOD CALLED ===');
        log_message('debug', 'Customer ID: ' . $id);
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'GET data: ' . json_encode($this->request->getGet()));
        log_message('debug', 'Headers: ' . json_encode($this->request->getHeaders()));

        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            if ($this->request->getPost('debug')) {
                log_message('debug', 'Customer not found for ID: ' . $id);
            }
            return redirect()->to('customer')->with('error', 'Customer not found!');
        }

        $rules = [
            'customer_name' => 'required|min_length[3]|max_length[255]',
            'contact_person' => 'required|min_length[2]|max_length[255]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'email' => 'permit_empty|valid_email',
            'address' => 'required|min_length[5]',
            'city' => 'required|min_length[2]|max_length[100]',
            'state' => 'required|min_length[2]|max_length[100]',
            'pincode' => 'required|min_length[6]|max_length[10]',
            'credit_limit' => 'permit_empty|numeric',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', '=== VALIDATION FAILED ===');
            log_message('debug', 'Validation errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        log_message('debug', '=== DATA PREPARED FOR UPDATE ===');
        log_message('debug', 'Data to update: ' . json_encode($data));

        try {
            log_message('debug', '=== ATTEMPTING DATABASE UPDATE ===');
            $result = $this->customerModel->update($id, $data);
            log_message('debug', '=== UPDATE SUCCESSFUL ===');
            log_message('debug', 'Update result: ' . json_encode($result));
            return redirect()->to('customer')->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            log_message('error', '=== UPDATE FAILED ===');
            log_message('error', 'Customer update error: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    /**
     * Delete customer
     */
    public function delete($id = null)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return redirect()->to('customer')->with('error', 'Customer not found!');
        }

        // Check if customer has any related records
        $hasOutstandingPayments = $this->customerModel->hasOutstandingPayments($id);
        
        if ($hasOutstandingPayments) {
            return redirect()->to('customer')->with('error', 'Cannot delete customer with outstanding payments!');
        }

        try {
            $this->customerModel->delete($id);
            return redirect()->to('customer')->with('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->to('customer')->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    /**
     * Toggle customer status
     */
    public function toggleStatus($id = null)
    {
        if ($this->request->isAJAX()) {
            $customer = $this->customerModel->find($id);
            
            if (!$customer) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
            }

            $newStatus = $customer['status'] === 'active' ? 'inactive' : 'active';
            
            try {
                $this->customerModel->update($id, ['status' => $newStatus]);
                return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error updating status']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    /**
     * Get customers by sales zone
     */
    public function getCustomersByZone()
    {
        $zone = $this->request->getGet('zone');
        
        if (!$zone) {
            return $this->response->setJSON(['success' => false, 'message' => 'Zone parameter required']);
        }

        $customers = $this->customerModel->getCustomersByZone($zone);
        return $this->response->setJSON(['success' => true, 'customers' => $customers]);
    }

    /**
     * Get customers by sales region
     */
    public function getCustomersByRegion()
    {
        $region = $this->request->getGet('region');
        
        if (!$region) {
            return $this->response->setJSON(['success' => false, 'message' => 'Region parameter required']);
        }

        $customers = $this->customerModel->getCustomersByRegion($region);
        return $this->response->setJSON(['success' => true, 'customers' => $customers]);
    }

    /**
     * Get outstanding payments
     */
    public function getOutstandingPayments()
    {
        $customers = $this->customerModel->getCustomersWithOutstandingPayments();
        
        $data = [
            'title' => 'Outstanding Payments - PRODX',
            'customers' => $customers
        ];

        return view('customer/outstanding_payments', $data);
    }

    /**
     * Print customer details
     */
    public function print($id = null)
    {
        $customer = $this->customerModel->getCustomerWithHistory($id);
        
        if (!$customer) {
            return redirect()->to('customer')->with('error', 'Customer not found!');
        }

        $data = [
            'customer' => $customer,
            'performance' => $this->customerModel->getCustomerPerformance($id)
        ];

        return view('customer/print', $data);
    }

    /**
     * Export customers
     */
    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'sales_zone' => $this->request->getGet('sales_zone'),
            'sales_region' => $this->request->getGet('sales_region')
        ];

        $customers = $this->customerModel->getCustomers($filters);

        // Set headers for CSV download
        $filename = 'customers_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'Customer Code',
            'Customer Name',
            'Contact Person',
            'Phone',
            'Email',
            'Address',
            'City',
            'State',
            'Pincode',
            'GST Number',
            'PAN Number',
            'Credit Limit',
            'Payment Terms',
            'Sales Zone',
            'Sales Region',
            'Status',
            'Created At'
        ]);

        // CSV data
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['customer_code'],
                $customer['customer_name'],
                $customer['contact_person'],
                $customer['phone'],
                $customer['email'],
                $customer['address'],
                $customer['city'],
                $customer['state'],
                $customer['pincode'],
                $customer['gst_number'],
                $customer['pan_number'],
                $customer['credit_limit'],
                $customer['payment_terms'],
                $customer['sales_zone'],
                $customer['sales_region'],
                $customer['status'],
                $customer['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Get customer performance report
     */
    public function performanceReport($id = null)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return redirect()->to('customer')->with('error', 'Customer not found!');
        }

        $performance = $this->customerModel->getCustomerPerformance($id);
        $summary = $this->customerModel->getCustomerSummary($id);

        $data = [
            'title' => 'Customer Performance Report - PRODX',
            'customer' => $customer,
            'performance' => $performance,
            'summary' => $summary
        ];

        return view('customer/performance_report', $data);
    }

    /**
     * Get sales zones for dropdown
     */
    public function getSalesZones()
    {
        $zones = $this->customerModel->getSalesZones();
        return $this->response->setJSON(['success' => true, 'zones' => $zones]);
    }

    /**
     * Get sales regions for dropdown
     */
    public function getSalesRegions()
    {
        $regions = $this->customerModel->getSalesRegions();
        return $this->response->setJSON(['success' => true, 'regions' => $regions]);
    }

    /**
     * Get customer for AJAX requests
     */
    public function getCustomer($id = null)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer not found']);
        }

        return $this->response->setJSON(['success' => true, 'customer' => $customer]);
    }

    /**
     * Search customers for AJAX requests
     */
    public function searchCustomers()
    {
        $search = $this->request->getGet('search');
        
        if (!$search) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search term required']);
        }

        $customers = $this->customerModel->getCustomers(['search' => $search]);
        return $this->response->setJSON(['success' => true, 'customers' => $customers]);
    }
} 