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
        
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-ordered { background-color: #cce7ff; color: #004085; }
        .status-received { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .priority-low { background-color: #d1ecf1; color: #0c5460; }
        .priority-normal { background-color: #d4edda; color: #155724; }
        .priority-high { background-color: #fff3cd; color: #856404; }
        .priority-urgent { background-color: #f8d7da; color: #721c24; }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-file-invoice me-3"></i>
                Purchase Orders
            </h1>
            <p class="mb-0">Manage and track all purchase orders</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($total_orders) ? $total_orders : 0 ?></div>
                    <div class="text-muted">Total Orders</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($pending_orders) ? $pending_orders : 0 ?></div>
                    <div class="text-muted">Pending Approval</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($approved_orders) ? $approved_orders : 0 ?></div>
                    <div class="text-muted">Approved</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">₹<?= number_format(isset($total_value) ? $total_value : 0) ?></div>
                    <div class="text-muted">Total Value</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="/purchase/orders" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="PO number, supplier...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="draft" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="pending" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="ordered" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'ordered' ? 'selected' : '' ?>>Ordered</option>
                        <option value="received" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'received' ? 'selected' : '' ?>>Received</option>
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
                <a href="/purchase/orders" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </a>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>
            <div>
                <a href="/purchase/orders/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Purchase Order
                </a>
            </div>
        </div>

        <!-- Purchase Orders Table -->
        <div class="table-container">
            <table id="purchaseOrdersTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Order Date</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($purchase_orders) && is_array($purchase_orders)): ?>
                        <?php foreach ($purchase_orders as $order): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($order['po_number']) ? $order['po_number'] : 'N/A') ?></strong>
                                </td>
                                <td><?= esc(isset($order['supplier_name']) ? $order['supplier_name'] : 'N/A') ?></td>
                                <td><?= isset($order['order_date']) ? $order['order_date'] : 'N/A' ?></td>
                                <td>
                                    <?php 
                                    $dueDate = isset($order['due_date']) ? $order['due_date'] : null;
                                    if ($dueDate) {
                                        $dueDateObj = new DateTime($dueDate);
                                        $today = new DateTime();
                                        $isOverdue = $dueDateObj < $today;
                                        echo '<span class="' . ($isOverdue ? 'text-danger fw-bold' : '') . '">' . $dueDate . '</span>';
                                        if ($isOverdue) echo ' <i class="fas fa-exclamation-triangle text-warning"></i>';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>₹<?= number_format(isset($order['total_amount']) ? $order['total_amount'] : 0, 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(isset($order['status']) ? $order['status'] : 'draft') ?>">
                                        <?= ucfirst(isset($order['status']) ? $order['status'] : 'Draft') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-badge priority-<?= strtolower(isset($order['priority']) ? $order['priority'] : 'normal') ?>">
                                        <?= ucfirst(isset($order['priority']) ? $order['priority'] : 'Normal') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/purchase/orders/view/<?= $order['id'] ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ((isset($order['status']) ? $order['status'] : '') === 'pending'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success btn-action" title="Approve" onclick="approveOrder(<?= $order['id'] ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (in_array(isset($order['status']) ? $order['status'] : '', ['draft', 'pending'])): ?>
                                            <a href="/purchase/orders/edit/<?= $order['id'] ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/purchase/orders/print/<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No purchase orders found
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
            $('#purchaseOrdersTable').DataTable({
                pageLength: 25,
                order: [[2, 'desc']], // Sort by order date descending
                responsive: true,
                language: {
                    search: "Search orders:",
                    lengthMenu: "Show _MENU_ orders per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ orders"
                }
            });
        });

        // Approve purchase order
        function approveOrder(orderId) {
            if (confirm('Are you sure you want to approve this purchase order?')) {
                fetch(`/purchase/orders/approve/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Purchase order approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to approve order'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error occurred while approving the order');
                });
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('purchaseOrdersTable');
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
                    if (index < 7) { // Exclude actions column
                        let text = td.textContent.trim();
                        // Remove status badges and priority badges
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
            link.setAttribute('download', 'purchase_orders.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
