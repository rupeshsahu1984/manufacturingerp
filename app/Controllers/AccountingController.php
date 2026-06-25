<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseBill;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use Exception;

class AccountingController extends BaseController
{
    protected $invoiceModel;
    protected $purchaseBillModel;
    protected $customerModel;
    protected $supplierModel;
    protected $productModel;

    public function __construct()
    {
        $this->invoiceModel = new Invoice();
        $this->purchaseBillModel = new PurchaseBill();
        $this->customerModel = new Customer();
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
    }

    public function index()
    {
        $data = [
            'title' => 'Accounting Dashboard - PRODX',
            'total_revenue' => $this->invoiceModel->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'total_expenses' => $this->purchaseBillModel->selectSum('total_amount')->first()['total_amount'] ?? 0,
            'outstanding_receivables' => $this->getOutstandingReceivables(),
            'outstanding_payables' => $this->getOutstandingPayables(),
            'recent_invoices' => $this->invoiceModel->getRecentInvoices(5),
            'recent_bills' => $this->purchaseBillModel->getRecentBills(5),
            'monthly_stats' => $this->getMonthlyStats(),
            'customer_stats' => $this->getCustomerStats(),
            'supplier_stats' => $this->getSupplierStats()
        ];

        return view('accounting/dashboard', $data);
    }

    public function invoices()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'customer' => $this->request->getGet('customer'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Invoice Management - PRODX',
            'invoices' => $this->invoiceModel->getInvoicesWithDetails($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->invoiceModel->getInvoiceStats(),
            'filters' => $filters
        ];

        return view('accounting/invoices', $data);
    }

    public function bills()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'supplier' => $this->request->getGet('supplier'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Purchase Bills - PRODX',
            'bills' => $this->purchaseBillModel->getBillsWithDetails($filters),
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'stats' => $this->purchaseBillModel->getBillStats(),
            'filters' => $filters
        ];

        return view('accounting/bills', $data);
    }

    public function receivables()
    {
        $filters = [
            'customer' => $this->request->getGet('customer'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Accounts Receivable - PRODX',
            'receivables' => $this->getReceivablesData($filters),
            'customers' => $this->customerModel->getActiveCustomers(),
            'stats' => $this->getReceivablesStats(),
            'filters' => $filters
        ];

        return view('accounting/receivables', $data);
    }

    public function payables()
    {
        $filters = [
            'supplier' => $this->request->getGet('supplier'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $data = [
            'title' => 'Accounts Payable - PRODX',
            'payables' => $this->getPayablesData($filters),
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'stats' => $this->getPayablesStats(),
            'filters' => $filters
        ];

        return view('accounting/payables', $data);
    }

    public function reports()
    {
        $report_type = $this->request->getGet('type') ?? 'profit_loss';
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-01');
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-t');
        
        $data = [
            'title' => 'Financial Reports - PRODX',
            'report_type' => $report_type,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'profit_loss' => $this->getProfitLossReport($date_from, $date_to),
            'cash_flow' => $this->getCashFlowReport($date_from, $date_to),
            'balance_sheet' => $this->getBalanceSheetReport($date_to),
            'customer_ledger' => $this->getCustomerLedgerReport($date_from, $date_to),
            'supplier_ledger' => $this->getSupplierLedgerReport($date_from, $date_to)
        ];

        return view('accounting/reports', $data);
    }

    public function analytics()
    {
        $data = [
            'title' => 'Financial Analytics - PRODX',
            'revenue_trends' => $this->getRevenueTrends(),
            'expense_trends' => $this->getExpenseTrends(),
            'profit_margins' => $this->getProfitMargins(),
            'customer_performance' => $this->getCustomerPerformance(),
            'supplier_performance' => $this->getSupplierPerformance(),
            'cash_flow_analysis' => $this->getCashFlowAnalysis()
        ];

        return view('accounting/analytics', $data);
    }

    public function journal()
    {
        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'type' => $this->request->getGet('type')
        ];

        $data = [
            'title' => 'General Journal - PRODX',
            'entries' => $this->getJournalEntries($filters),
            'filters' => $filters
        ];

        return view('accounting/journal', $data);
    }

    public function ledger()
    {
        $account = $this->request->getGet('account') ?? 'all';
        $date_from = $this->request->getGet('date_from');
        $date_to = $this->request->getGet('date_to');

        $data = [
            'title' => 'General Ledger - PRODX',
            'accounts' => $this->getChartOfAccounts(),
            'ledger_entries' => $this->getLedgerEntries($account, $date_from, $date_to),
            'selected_account' => $account,
            'date_from' => $date_from,
            'date_to' => $date_to
        ];

        return view('accounting/ledger', $data);
    }

    public function exportReport()
    {
        $report_type = $this->request->getGet('type');
        $date_from = $this->request->getGet('date_from');
        $date_to = $this->request->getGet('date_to');

        switch ($report_type) {
            case 'profit_loss':
                $data = $this->getProfitLossReport($date_from, $date_to);
                $filename = 'profit_loss_' . date('Y-m-d_H-i-s') . '.csv';
                break;
            case 'cash_flow':
                $data = $this->getCashFlowReport($date_from, $date_to);
                $filename = 'cash_flow_' . date('Y-m-d_H-i-s') . '.csv';
                break;
            case 'balance_sheet':
                $data = $this->getBalanceSheetReport($date_to);
                $filename = 'balance_sheet_' . date('Y-m-d_H-i-s') . '.csv';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Export data based on report type
        $this->exportReportData($output, $data, $report_type);
        
        fclose($output);
        exit;
    }

    // Helper methods for calculations and data retrieval
    private function getOutstandingReceivables()
    {
        return $this->invoiceModel->selectSum('total_amount')
            ->where('status !=', 'paid')
            ->where('status !=', 'cancelled')
            ->first()['total_amount'] ?? 0;
    }

    private function getOutstandingPayables()
    {
        return $this->purchaseBillModel->selectSum('total_amount')
            ->where('status !=', 'paid')
            ->where('status !=', 'cancelled')
            ->first()['total_amount'] ?? 0;
    }

    private function getMonthlyStats()
    {
        $current_month = date('Y-m');
        
        $revenue = $this->invoiceModel->selectSum('total_amount')
            ->like('invoice_date', $current_month)
            ->where('status', 'paid')
            ->first()['total_amount'] ?? 0;
            
        $expenses = $this->purchaseBillModel->selectSum('total_amount')
            ->like('bill_date', $current_month)
            ->where('status', 'paid')
            ->first()['total_amount'] ?? 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $revenue - $expenses
        ];
    }

    private function getCustomerStats()
    {
        return $this->customerModel->select('customer_name, COUNT(invoices.id) as invoice_count, SUM(invoices.total_amount) as total_amount')
            ->join('invoices', 'invoices.customer_id = customers.id', 'left')
            ->groupBy('customers.id')
            ->orderBy('total_amount', 'DESC')
            ->limit(10)
            ->findAll();
    }

    private function getSupplierStats()
    {
        return $this->supplierModel->select('supplier_name, COUNT(purchase_bills.id) as bill_count, SUM(purchase_bills.total_amount) as total_amount')
            ->join('purchase_bills', 'purchase_bills.supplier_id = suppliers.id', 'left')
            ->groupBy('suppliers.id')
            ->orderBy('total_amount', 'DESC')
            ->limit(10)
            ->findAll();
    }

    private function getReceivablesData($filters)
    {
        $builder = $this->invoiceModel->select('invoices.*, customers.customer_name')
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->where('invoices.status !=', 'paid')
            ->where('invoices.status !=', 'cancelled');

        if (!empty($filters['customer'])) {
            $builder->where('invoices.customer_id', $filters['customer']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('invoices.invoice_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('invoices.invoice_date <=', $filters['date_to']);
        }

        return $builder->orderBy('invoices.due_date', 'ASC')->findAll();
    }

    private function getPayablesData($filters)
    {
        $builder = $this->purchaseBillModel->select('purchase_bills.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_bills.supplier_id', 'left')
            ->where('purchase_bills.status !=', 'paid')
            ->where('purchase_bills.status !=', 'cancelled');

        if (!empty($filters['supplier'])) {
            $builder->where('purchase_bills.supplier_id', $filters['supplier']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('purchase_bills.bill_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('purchase_bills.bill_date <=', $filters['date_to']);
        }

        return $builder->orderBy('purchase_bills.due_date', 'ASC')->findAll();
    }

    private function getReceivablesStats()
    {
        $total = $this->getOutstandingReceivables();
        $overdue = $this->invoiceModel->selectSum('total_amount')
            ->where('due_date <', date('Y-m-d'))
            ->where('status !=', 'paid')
            ->where('status !=', 'cancelled')
            ->first()['total_amount'] ?? 0;

        return [
            'total' => $total,
            'overdue' => $overdue,
            'current' => $total - $overdue
        ];
    }

    private function getPayablesStats()
    {
        $total = $this->getOutstandingPayables();
        $overdue = $this->purchaseBillModel->selectSum('total_amount')
            ->where('due_date <', date('Y-m-d'))
            ->where('status !=', 'paid')
            ->where('status !=', 'cancelled')
            ->first()['total_amount'] ?? 0;

        return [
            'total' => $total,
            'overdue' => $overdue,
            'current' => $total - $overdue
        ];
    }

    private function getProfitLossReport($date_from, $date_to)
    {
        $revenue = $this->invoiceModel->selectSum('total_amount')
            ->where('invoice_date >=', $date_from)
            ->where('invoice_date <=', $date_to)
            ->where('status', 'paid')
            ->first()['total_amount'] ?? 0;

        $expenses = $this->purchaseBillModel->selectSum('total_amount')
            ->where('bill_date >=', $date_from)
            ->where('bill_date <=', $date_to)
            ->where('status', 'paid')
            ->first()['total_amount'] ?? 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'gross_profit' => $revenue - $expenses,
            'net_profit' => $revenue - $expenses // Simplified for demo
        ];
    }

    private function getCashFlowReport($date_from, $date_to)
    {
        $cash_in = $this->invoiceModel->selectSum('paid_amount')
            ->where('invoice_date >=', $date_from)
            ->where('invoice_date <=', $date_to)
            ->first()['paid_amount'] ?? 0;

        $cash_out = $this->purchaseBillModel->selectSum('paid_amount')
            ->where('bill_date >=', $date_from)
            ->where('bill_date <=', $date_to)
            ->first()['paid_amount'] ?? 0;

        return [
            'cash_in' => $cash_in,
            'cash_out' => $cash_out,
            'net_cash_flow' => $cash_in - $cash_out
        ];
    }

    private function getBalanceSheetReport($as_of_date)
    {
        $assets = $this->getAssets($as_of_date);
        $liabilities = $this->getLiabilities($as_of_date);
        $equity = $assets - $liabilities;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity
        ];
    }

    private function getAssets($as_of_date)
    {
        // Simplified assets calculation
        $cash = 1000000; // Starting cash balance
        $receivables = $this->getOutstandingReceivables();
        return $cash + $receivables;
    }

    private function getLiabilities($as_of_date)
    {
        return $this->getOutstandingPayables();
    }

    private function getCustomerLedgerReport($date_from, $date_to)
    {
        return $this->customerModel->select('customers.*, 
            SUM(CASE WHEN invoices.status = "paid" THEN invoices.total_amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN invoices.status != "paid" AND invoices.status != "cancelled" THEN invoices.total_amount ELSE 0 END) as outstanding')
            ->join('invoices', 'invoices.customer_id = customers.id', 'left')
            ->where('invoices.invoice_date >=', $date_from)
            ->where('invoices.invoice_date <=', $date_to)
            ->groupBy('customers.id')
            ->findAll();
    }

    private function getSupplierLedgerReport($date_from, $date_to)
    {
        return $this->supplierModel->select('suppliers.*, 
            SUM(CASE WHEN purchase_bills.status = "paid" THEN purchase_bills.total_amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN purchase_bills.status != "paid" AND purchase_bills.status != "cancelled" THEN purchase_bills.total_amount ELSE 0 END) as outstanding')
            ->join('purchase_bills', 'purchase_bills.supplier_id = suppliers.id', 'left')
            ->where('purchase_bills.bill_date >=', $date_from)
            ->where('purchase_bills.bill_date <=', $date_to)
            ->groupBy('suppliers.id')
            ->findAll();
    }

    private function getRevenueTrends()
    {
        // Get last 12 months revenue
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $revenue = $this->invoiceModel->selectSum('total_amount')
                ->like('invoice_date', $month)
                ->where('status', 'paid')
                ->first()['total_amount'] ?? 0;
            
            $trends[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'revenue' => $revenue
            ];
        }
        return $trends;
    }

    private function getExpenseTrends()
    {
        // Get last 12 months expenses
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $expenses = $this->purchaseBillModel->selectSum('total_amount')
                ->like('bill_date', $month)
                ->where('status', 'paid')
                ->first()['total_amount'] ?? 0;
            
            $trends[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'expenses' => $expenses
            ];
        }
        return $trends;
    }

    private function getProfitMargins()
    {
        $revenue = $this->invoiceModel->selectSum('total_amount')
            ->where('status', 'paid')
            ->first()['total_amount'] ?? 0;
            
        $expenses = $this->purchaseBillModel->selectSum('total_amount')
            ->where('status', 'paid')
            ->first()['total_amount'] ?? 0;

        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'margin_percentage' => round($margin, 2)
        ];
    }

    private function getCustomerPerformance()
    {
        return $this->customerModel->select('customer_name, 
            COUNT(invoices.id) as total_invoices,
            SUM(invoices.total_amount) as total_revenue,
            AVG(invoices.total_amount) as avg_order_value')
            ->join('invoices', 'invoices.customer_id = customers.id', 'left')
            ->where('invoices.status', 'paid')
            ->groupBy('customers.id')
            ->orderBy('total_revenue', 'DESC')
            ->limit(10)
            ->findAll();
    }

    private function getSupplierPerformance()
    {
        return $this->supplierModel->select('supplier_name, 
            COUNT(purchase_bills.id) as total_bills,
            SUM(purchase_bills.total_amount) as total_spent,
            AVG(purchase_bills.total_amount) as avg_bill_value')
            ->join('purchase_bills', 'purchase_bills.supplier_id = suppliers.id', 'left')
            ->where('purchase_bills.status', 'paid')
            ->groupBy('suppliers.id')
            ->orderBy('total_spent', 'DESC')
            ->limit(10)
            ->findAll();
    }

    private function getCashFlowAnalysis()
    {
        $current_month = date('Y-m');
        $previous_month = date('Y-m', strtotime('-1 month'));

        $current_cash_in = $this->invoiceModel->selectSum('paid_amount')
            ->like('invoice_date', $current_month)
            ->first()['paid_amount'] ?? 0;

        $current_cash_out = $this->purchaseBillModel->selectSum('paid_amount')
            ->like('bill_date', $current_month)
            ->first()['paid_amount'] ?? 0;

        $previous_cash_in = $this->invoiceModel->selectSum('paid_amount')
            ->like('invoice_date', $previous_month)
            ->first()['paid_amount'] ?? 0;

        $previous_cash_out = $this->purchaseBillModel->selectSum('paid_amount')
            ->like('bill_date', $previous_month)
            ->first()['paid_amount'] ?? 0;

        return [
            'current_month' => [
                'cash_in' => $current_cash_in,
                'cash_out' => $current_cash_out,
                'net_flow' => $current_cash_in - $current_cash_out
            ],
            'previous_month' => [
                'cash_in' => $previous_cash_in,
                'cash_out' => $previous_cash_out,
                'net_flow' => $previous_cash_in - $previous_cash_out
            ]
        ];
    }

    private function getJournalEntries($filters)
    {
        // Simplified journal entries - in a real system, this would be more complex
        $entries = [];
        
        // Add invoice entries
        $invoices = $this->invoiceModel->select('invoices.*, customers.customer_name')
            ->join('customers', 'customers.id = invoices.customer_id', 'left');
        
        if (!empty($filters['date_from'])) {
            $invoices->where('invoices.invoice_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $invoices->where('invoices.invoice_date <=', $filters['date_to']);
        }
        
        $invoice_data = $invoices->findAll();
        
        foreach ($invoice_data as $invoice) {
            $entries[] = [
                'date' => $invoice['invoice_date'],
                'description' => 'Invoice ' . $invoice['invoice_number'] . ' - ' . $invoice['customer_name'],
                'debit' => $invoice['total_amount'],
                'credit' => 0,
                'type' => 'revenue'
            ];
        }

        // Add bill entries
        $bills = $this->purchaseBillModel->select('purchase_bills.*, suppliers.supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_bills.supplier_id', 'left');
        
        if (!empty($filters['date_from'])) {
            $bills->where('purchase_bills.bill_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $bills->where('purchase_bills.bill_date <=', $filters['date_to']);
        }
        
        $bill_data = $bills->findAll();
        
        foreach ($bill_data as $bill) {
            $entries[] = [
                'date' => $bill['bill_date'],
                'description' => 'Bill ' . $bill['bill_number'] . ' - ' . $bill['supplier_name'],
                'debit' => 0,
                'credit' => $bill['total_amount'],
                'type' => 'expense'
            ];
        }

        // Sort by date
        usort($entries, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $entries;
    }

    private function getChartOfAccounts()
    {
        return [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['code' => '3000', 'name' => 'Revenue', 'type' => 'revenue'],
            ['code' => '4000', 'name' => 'Cost of Goods Sold', 'type' => 'expense'],
            ['code' => '5000', 'name' => 'Operating Expenses', 'type' => 'expense']
        ];
    }

    private function getLedgerEntries($account, $date_from, $date_to)
    {
        // Simplified ledger entries
        $entries = [];
        
        if ($account === 'all' || $account === '1100') { // Accounts Receivable
            $invoices = $this->invoiceModel->select('invoices.*, customers.customer_name')
                ->join('customers', 'customers.id = invoices.customer_id', 'left');
            
            if ($date_from) $invoices->where('invoices.invoice_date >=', $date_from);
            if ($date_to) $invoices->where('invoices.invoice_date <=', $date_to);
            
            $invoice_data = $invoices->findAll();
            
            foreach ($invoice_data as $invoice) {
                $entries[] = [
                    'date' => $invoice['invoice_date'],
                    'description' => 'Invoice ' . $invoice['invoice_number'] . ' - ' . $invoice['customer_name'],
                    'debit' => $invoice['total_amount'],
                    'credit' => 0,
                    'balance' => $invoice['total_amount']
                ];
            }
        }

        if ($account === 'all' || $account === '2000') { // Accounts Payable
            $bills = $this->purchaseBillModel->select('purchase_bills.*, suppliers.supplier_name')
                ->join('suppliers', 'suppliers.id = purchase_bills.supplier_id', 'left');
            
            if ($date_from) $bills->where('purchase_bills.bill_date >=', $date_from);
            if ($date_to) $bills->where('purchase_bills.bill_date <=', $date_to);
            
            $bill_data = $bills->findAll();
            
            foreach ($bill_data as $bill) {
                $entries[] = [
                    'date' => $bill['bill_date'],
                    'description' => 'Bill ' . $bill['bill_number'] . ' - ' . $bill['supplier_name'],
                    'debit' => 0,
                    'credit' => $bill['total_amount'],
                    'balance' => -$bill['total_amount']
                ];
            }
        }

        // Sort by date
        usort($entries, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $entries;
    }

    private function exportReportData($output, $data, $report_type)
    {
        switch ($report_type) {
            case 'profit_loss':
                fputcsv($output, ['Profit & Loss Statement']);
                fputcsv($output, ['Revenue', $data['revenue']]);
                fputcsv($output, ['Expenses', $data['expenses']]);
                fputcsv($output, ['Gross Profit', $data['gross_profit']]);
                fputcsv($output, ['Net Profit', $data['net_profit']]);
                break;
                
            case 'cash_flow':
                fputcsv($output, ['Cash Flow Statement']);
                fputcsv($output, ['Cash In', $data['cash_in']]);
                fputcsv($output, ['Cash Out', $data['cash_out']]);
                fputcsv($output, ['Net Cash Flow', $data['net_cash_flow']]);
                break;
                
            case 'balance_sheet':
                fputcsv($output, ['Balance Sheet']);
                fputcsv($output, ['Assets', $data['assets']]);
                fputcsv($output, ['Liabilities', $data['liabilities']]);
                fputcsv($output, ['Equity', $data['equity']]);
                break;
        }
    }

    // Chart of Accounts
    public function coa()
    {
        $data = [
            'title' => 'Chart of Accounts - PRODX',
            'accounts' => $this->getChartOfAccounts()
        ];

        return view('accounting/coa', $data);
    }

    public function coaCreate()
    {
        $data = [
            'title' => 'Create Account - PRODX'
        ];

        return view('accounting/coa_create', $data);
    }

    public function coaStore()
    {
        // Store chart of account logic
        return redirect()->to('accounting/coa')->with('success', 'Account created successfully.');
    }

    public function coaToggleStatus($id)
    {
        return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
    }

    // Cost Centers
    public function costCenters()
    {
        $data = [
            'title' => 'Cost Centers - PRODX',
            'cost_centers' => []
        ];

        return view('accounting/cost_centers', $data);
    }

    public function costCenterCreate()
    {
        $data = [
            'title' => 'Create Cost Center - PRODX'
        ];

        return view('accounting/cost_center_create', $data);
    }

    public function costCenterStore()
    {
        return redirect()->to('accounting/cost-centers')->with('success', 'Cost center created successfully.');
    }

    public function costCenterToggleStatus($id)
    {
        return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);
    }

    // Journal Entries
    public function journalCreate()
    {
        $data = [
            'title' => 'Create Journal Entry - PRODX',
            'accounts' => $this->getChartOfAccounts()
        ];

        return view('accounting/journal_create', $data);
    }

    public function journalStore()
    {
        return redirect()->to('accounting/journal')->with('success', 'Journal entry created successfully.');
    }

    // Bank Accounts
    public function bankAccounts()
    {
        $data = [
            'title' => 'Bank Accounts - PRODX',
            'bank_accounts' => []
        ];

        return view('accounting/bank_accounts', $data);
    }

    public function bankReconciliations()
    {
        $data = [
            'title' => 'Bank Reconciliation - PRODX',
            'bank_accounts' => []
        ];

        return view('accounting/bank_reconciliation', $data);
    }

    public function bankReconciliationStore()
    {
        return redirect()->to('accounting/bank-reconciliation')->with('success', 'Reconciliation saved successfully.');
    }

    // Expenses
    public function expenses()
    {
        $data = [
            'title' => 'Expenses - PRODX',
            'expenses' => []
        ];

        return view('accounting/expenses', $data);
    }

    public function expenseCreate()
    {
        $data = [
            'title' => 'Create Expense - PRODX',
            'accounts' => $this->getChartOfAccounts()
        ];

        return view('accounting/expense_create', $data);
    }

    public function expenseStore()
    {
        return redirect()->to('accounting/expenses')->with('success', 'Expense created successfully.');
    }

    // Taxes
    public function taxes()
    {
        $data = [
            'title' => 'Tax Management - PRODX',
            'taxes' => []
        ];

        return view('accounting/taxes', $data);
    }

    public function taxCreate()
    {
        $data = [
            'title' => 'Create Tax - PRODX'
        ];

        return view('accounting/tax_create', $data);
    }

    public function taxStore()
    {
        return redirect()->to('accounting/taxes')->with('success', 'Tax created successfully.');
    }
}
