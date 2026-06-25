<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Manufacturing ERP</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .filters-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-partial { background-color: #cce7ff; color: #004085; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
        .status-cancelled { background-color: #e9ecef; color: #495057; }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .overdue-warning {
            color: #dc3545;
            font-weight: bold;
        }
        
        .payment-status {
            font-size: 0.8rem;
            padding: 2px 6px;
            border-radius: 8px;
        }
        
        .payment-pending { background-color: #fff3cd; color: #856404; }
        .payment-partial { background-color: #cce7ff; color: #004085; }
        .payment-paid { background-color: #d4edda; color: #155724; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-receipt me-3"></i>
                Supplier Invoices
            </h1>
            <p class="mb-0">Manage and track supplier invoices and payments</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Supplier Invoices</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($total_invoices) ? $total_invoices : 0 ?></div>
                    <div class="text-muted">Total Invoices</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">₹<?= number_format(isset($total_amount) ? $total_amount : 0) ?></div>
                    <div class="text-muted">Total Amount</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">₹<?= number_format(isset($pending_amount) ? $pending_amount : 0) ?></div>
                    <div class="text-muted">Pending Payment</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($overdue_invoices) ? $overdue_invoices : 0 ?></div>
                    <div class="text-muted">Overdue</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="/purchase/invoices" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Invoice number, PO number...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="partial" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                        <option value="paid" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="overdue" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        <option value="cancelled" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="supplier" class="form-label">Supplier</label>
                    <select class="form-select" id="supplier" name="supplier">
                        <option value="">All Suppliers</option>
                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>" <?= (isset($_GET['supplier']) ? $_GET['supplier'] : '') == $supplier['id'] ? 'selected' : '' ?>>
                                    <?= esc($supplier['supplier_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="/purchase/invoices" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </a>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
                <a href="/purchase/invoices/overdue" class="btn btn-outline-warning me-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>Overdue Invoices
                </a>
            </div>
            <div>
                <a href="/purchase/invoices/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Record Invoice
                </a>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="table-container">
            <table id="invoicesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Invoice Amount</th>
                        <th>Paid Amount</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($invoices) && is_array($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($invoice['invoice_number']) ? $invoice['invoice_number'] : 'N/A') ?></strong>
                                </td>
                                <td>
                                    <a href="/purchase/orders/view/<?= isset($invoice['po_id']) ? $invoice['po_id'] : '' ?>" class="text-decoration-none">
                                        <?= esc(isset($invoice['po_number']) ? $invoice['po_number'] : 'N/A') ?>
                                    </a>
                                </td>
                                <td><?= esc(isset($invoice['supplier_name']) ? $invoice['supplier_name'] : 'N/A') ?></td>
                                <td><?= isset($invoice['invoice_date']) ? $invoice['invoice_date'] : 'N/A' ?></td>
                                <td>
                                    <?php 
                                    $dueDate = isset($invoice['due_date']) ? $invoice['due_date'] : null;
                                    if ($dueDate) {
                                        $dueDateObj = new DateTime($dueDate);
                                        $today = new DateTime();
                                        $isOverdue = $dueDateObj < $today;
                                        echo '<span class="' . ($isOverdue ? 'overdue-warning' : '') . '">' . $dueDate . '</span>';
                                        if ($isOverdue) echo ' <i class="fas fa-exclamation-triangle text-warning"></i>';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>₹<?= number_format(isset($invoice['invoice_amount']) ? $invoice['invoice_amount'] : 0, 2) ?></td>
                                <td>₹<?= number_format(isset($invoice['paid_amount']) ? $invoice['paid_amount'] : 0, 2) ?></td>
                                <td>
                                    <?php 
                                    $balance = (isset($invoice['invoice_amount']) ? $invoice['invoice_amount'] : 0) - (isset($invoice['paid_amount']) ? $invoice['paid_amount'] : 0);
                                    $balanceClass = $balance > 0 ? 'text-danger' : 'text-success';
                                    ?>
                                    <span class="<?= $balanceClass ?> fw-bold">
                                        ₹<?= number_format($balance, 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(isset($invoice['status']) ? $invoice['status'] : 'pending') ?>">
                                        <?= ucfirst(isset($invoice['status']) ? $invoice['status'] : 'Pending') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/purchase/invoices/view/<?= $invoice['id'] ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ((isset($invoice['status']) ? $invoice['status'] : '') === 'pending'): ?>
                                            <a href="/purchase/invoices/edit/<?= $invoice['id'] ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ((isset($invoice['status']) ? $invoice['status'] : '') !== 'paid'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success btn-action" title="Record Payment" onclick="recordPayment(<?= $invoice['id'] ?>)">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="/purchase/invoices/print/<?= $invoice['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-receipt fa-2x mb-2"></i><br>
                                No supplier invoices found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#invoicesTable').DataTable({
                pageLength: 25,
                order: [[3, 'desc']], // Sort by invoice date descending
                responsive: true,
                language: {
                    search: "Search invoices:",
                    lengthMenu: "Show _MENU_ invoices per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ invoices"
                }
            });
        });

        // Record payment
        function recordPayment(invoiceId) {
            if (confirm('Do you want to record a payment for this invoice?')) {
                // Redirect to payment form
                window.location.href = `/purchase/invoices/payment/${invoiceId}`;
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('invoicesTable');
            let csv = [];
            
            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Get data rows
            table.querySelectorAll('tbody tr').forEach(row => {
                const rowData = [];
                row.querySelectorAll('td').forEach((td, index) => {
                    if (index < 9) { // Exclude actions column
                        let text = td.textContent.trim();
                        // Remove badges and icons
                        text = text.replace(/[^\w\s₹,.-]/g, '');
                        rowData.push(`"${text}"`);
                    }
                });
                csv.push(rowData.join(','));
            });
            
            // Download CSV
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'supplier_invoices.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
