<?php

namespace App\Controllers;

use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\Customer;
use Exception;

class CustomerPaymentController extends BaseController
{
    protected $customerPaymentModel;
    protected $invoiceModel;
    protected $customerModel;

    public function __construct()
    {
        $this->customerPaymentModel = new CustomerPayment();
        $this->invoiceModel = new Invoice();
        $this->customerModel = new Customer();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'customer' => $this->request->getGet('customer'),
            'payment_method' => $this->request->getGet('payment_method'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Customer Payments - PRODX',
            'payments' => $this->customerPaymentModel->getPaymentsWithDetails($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->customerPaymentModel->getPaymentStats(),
            'filters' => $filters
        ];

        return view('customer_payment/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Customer Payment - PRODX',
            'customers' => $this->customerModel->getActiveCustomers(),
            'invoices' => $this->invoiceModel->getOutstandingInvoices(),
            'payment_number' => $this->customerPaymentModel->generateUniquePaymentNumber()
        ];

        return view('customer_payment/create', $data);
    }

    public function store()
    {
        $rules = [
            'payment_number' => 'required|max_length[20]|is_unique[customer_payments.payment_number]',
            'customer_id' => 'required|integer',
            'invoice_id' => 'permit_empty|integer',
            'payment_date' => 'required|valid_date',
            'payment_amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[cash,bank_transfer,cheque,credit_card,online]',
            'reference_number' => 'permit_empty|max_length[50]',
            'notes' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'payment_number' => $this->request->getPost('payment_number'),
            'customer_id' => $this->request->getPost('customer_id'),
            'invoice_id' => $this->request->getPost('invoice_id') ?: null,
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_amount' => $this->request->getPost('payment_amount'),
            'payment_method' => $this->request->getPost('payment_method'),
            'reference_number' => $this->request->getPost('reference_number'),
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id') ?? 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert payment
            $payment_id = $this->customerPaymentModel->insert($data);

            // Update invoice paid amount if invoice is specified
            if (!empty($data['invoice_id'])) {
                $invoice = $this->invoiceModel->find($data['invoice_id']);
                if ($invoice) {
                    $new_paid_amount = $invoice['paid_amount'] + $data['payment_amount'];
                    $this->invoiceModel->update($data['invoice_id'], [
                        'paid_amount' => $new_paid_amount,
                        'status' => ($new_paid_amount >= $invoice['total_amount']) ? 'paid' : 'partial'
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to create payment.');
            }

            return redirect()->to('customer-payment')->with('success', 'Payment recorded successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $payment = $this->customerPaymentModel->getPaymentWithDetails($id);
        
        if (!$payment) {
            return redirect()->to('customer-payment')->with('error', 'Payment not found.');
        }

        $data = [
            'title' => 'Payment Details - PRODX',
            'payment' => $payment
        ];

        return view('customer_payment/show', $data);
    }

    public function edit($id)
    {
        $payment = $this->customerPaymentModel->getPaymentWithDetails($id);
        
        if (!$payment) {
            return redirect()->to('customer-payment')->with('error', 'Payment not found.');
        }

        $data = [
            'title' => 'Edit Payment - PRODX',
            'payment' => $payment,
            'customers' => $this->customerModel->getActiveCustomers(),
            'invoices' => $this->invoiceModel->getOutstandingInvoices()
        ];

        return view('customer_payment/edit', $data);
    }

    public function update($id)
    {
        $payment = $this->customerPaymentModel->find($id);
        
        if (!$payment) {
            return redirect()->to('customer-payment')->with('error', 'Payment not found.');
        }

        $rules = [
            'payment_number' => "required|max_length[20]|is_unique[customer_payments.payment_number,id,$id]",
            'customer_id' => 'required|integer',
            'invoice_id' => 'permit_empty|integer',
            'payment_date' => 'required|valid_date',
            'payment_amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[cash,bank_transfer,cheque,credit_card,online]',
            'reference_number' => 'permit_empty|max_length[50]',
            'notes' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $old_amount = $payment['payment_amount'];
        $old_invoice_id = $payment['invoice_id'];

        $data = [
            'payment_number' => $this->request->getPost('payment_number'),
            'customer_id' => $this->request->getPost('customer_id'),
            'invoice_id' => $this->request->getPost('invoice_id') ?: null,
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_amount' => $this->request->getPost('payment_amount'),
            'payment_method' => $this->request->getPost('payment_method'),
            'reference_number' => $this->request->getPost('reference_number'),
            'notes' => $this->request->getPost('notes')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update payment
            $this->customerPaymentModel->update($id, $data);

            // Revert old invoice paid amount
            if ($old_invoice_id) {
                $old_invoice = $this->invoiceModel->find($old_invoice_id);
                if ($old_invoice) {
                    $reverted_paid_amount = $old_invoice['paid_amount'] - $old_amount;
                    $this->invoiceModel->update($old_invoice_id, [
                        'paid_amount' => max(0, $reverted_paid_amount),
                        'status' => ($reverted_paid_amount <= 0) ? 'unpaid' : 'partial'
                    ]);
                }
            }

            // Update new invoice paid amount
            if (!empty($data['invoice_id'])) {
                $new_invoice = $this->invoiceModel->find($data['invoice_id']);
                if ($new_invoice) {
                    $new_paid_amount = $new_invoice['paid_amount'] + $data['payment_amount'];
                    $this->invoiceModel->update($data['invoice_id'], [
                        'paid_amount' => $new_paid_amount,
                        'status' => ($new_paid_amount >= $new_invoice['total_amount']) ? 'paid' : 'partial'
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update payment.');
            }

            return redirect()->to('customer-payment')->with('success', 'Payment updated successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating payment: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $payment = $this->customerPaymentModel->find($id);
        
        if (!$payment) {
            return redirect()->to('customer-payment')->with('error', 'Payment not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Revert invoice paid amount
            if ($payment['invoice_id']) {
                $invoice = $this->invoiceModel->find($payment['invoice_id']);
                if ($invoice) {
                    $reverted_paid_amount = $invoice['paid_amount'] - $payment['payment_amount'];
                    $this->invoiceModel->update($payment['invoice_id'], [
                        'paid_amount' => max(0, $reverted_paid_amount),
                        'status' => ($reverted_paid_amount <= 0) ? 'unpaid' : 'partial'
                    ]);
                }
            }
            
            // Delete payment
            $this->customerPaymentModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('customer-payment')->with('error', 'Failed to delete payment.');
            }

            return redirect()->to('customer-payment')->with('success', 'Payment deleted successfully.');

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('customer-payment')->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'customer' => $this->request->getGet('customer'),
            'payment_method' => $this->request->getGet('payment_method'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $payments = $this->customerPaymentModel->getPaymentsWithDetails($filters);
        
        $filename = 'customer_payments_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Payment Number', 'Customer', 'Invoice', 'Payment Date', 'Amount', 'Method', 'Reference', 'Notes'
        ]);
        
        foreach ($payments as $payment) {
            fputcsv($output, [
                $payment['payment_number'],
                isset($payment['customer_name']) ? $payment['customer_name'] : '',
                isset($payment['invoice_number']) ? $payment['invoice_number'] : '',
                $payment['payment_date'],
                $payment['payment_amount'],
                $payment['payment_method'],
                isset($payment['reference_number']) ? $payment['reference_number'] : '',
                isset($payment['notes']) ? $payment['notes'] : ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getInvoices()
    {
        $customer_id = $this->request->getGet('customer_id');
        $invoices = $this->invoiceModel->getOutstandingInvoicesByCustomer($customer_id);
        return $this->response->setJSON(['success' => true, 'invoices' => $invoices]);
    }

    public function getCustomers()
    {
        $customers = $this->customerModel->getActiveCustomers();
        return $this->response->setJSON(['success' => true, 'customers' => $customers]);
    }

    public function print($id)
    {
        $payment = $this->customerPaymentModel->getPaymentWithDetails($id);
        
        if (!$payment) {
            return redirect()->to('customer-payment')->with('error', 'Payment not found.');
        }

        $data = [
            'title' => 'Print Payment Receipt - PRODX',
            'payment' => $payment
        ];

        return view('customer_payment/print', $data);
    }
}
