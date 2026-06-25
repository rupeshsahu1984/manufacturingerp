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
        .status-received { background-color: #d4edda; color: #155724; }
        .status-qc_pending { background-color: #cce7ff; color: #004085; }
        .status-qc_passed { background-color: #d1ecf1; color: #0c5460; }
        .status-qc_failed { background-color: #f8d7da; color: #721c24; }
        .status-stocked { background-color: #d4edda; color: #155724; }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .quality-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .quality-passed { background-color: #28a745; }
        .quality-failed { background-color: #dc3545; }
        .quality-pending { background-color: #ffc107; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-truck me-3"></i>
                Goods Receipt Notes (GRN)
            </h1>
            <p class="mb-0">Track and manage incoming goods from suppliers</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Goods Receipt</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($total_grns) ? $total_grns : 0 ?></div>
                    <div class="text-muted">Total GRNs</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($pending_grns) ? $pending_grns : 0 ?></div>
                    <div class="text-muted">Pending Receipt</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($qc_pending) ? $qc_pending : 0 ?></div>
                    <div class="text-muted">QC Pending</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($total_items) ? $total_items : 0 ?></div>
                    <div class="text-muted">Total Items</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="/purchase/grn" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="GRN number, PO number...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="received" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'received' ? 'selected' : '' ?>>Received</option>
                        <option value="qc_pending" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'qc_pending' ? 'selected' : '' ?>>QC Pending</option>
                        <option value="qc_passed" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'qc_passed' ? 'selected' : '' ?>>QC Passed</option>
                        <option value="qc_failed" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'qc_failed' ? 'selected' : '' ?>>QC Failed</option>
                        <option value="stocked" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'stocked' ? 'selected' : '' ?>>Stocked</option>
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
                <a href="/purchase/grn" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </a>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>
            <div>
                <a href="/purchase/grn/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create GRN
                </a>
            </div>
        </div>

        <!-- GRN Table -->
        <div class="table-container">
            <table id="grnTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>GRN Number</th>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Receipt Date</th>
                        <th>Items</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th>QC Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($grns) && is_array($grns)): ?>
                        <?php foreach ($grns as $grn): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($grn['grn_number']) ? $grn['grn_number'] : 'N/A') ?></strong>
                                </td>
                                <td>
                                    <a href="/purchase/orders/view/<?= isset($grn['po_id']) ? $grn['po_id'] : '' ?>" class="text-decoration-none">
                                        <?= esc(isset($grn['po_number']) ? $grn['po_number'] : 'N/A') ?>
                                    </a>
                                </td>
                                <td><?= esc(isset($grn['supplier_name']) ? $grn['supplier_name'] : 'N/A') ?></td>
                                <td><?= isset($grn['receipt_date']) ? $grn['receipt_date'] : 'N/A' ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= isset($grn['item_count']) ? $grn['item_count'] : 0 ?> items
                                    </span>
                                </td>
                                <td>₹<?= number_format(isset($grn['total_value']) ? $grn['total_value'] : 0, 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(isset($grn['status']) ? $grn['status'] : 'pending') ?>">
                                        <?= ucfirst(str_replace('_', ' ', isset($grn['status']) ? $grn['status'] : 'pending')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $qcStatus = isset($grn['qc_status']) ? $grn['qc_status'] : 'pending';
                                    $qcClass = 'quality-' . $qcStatus;
                                    $qcText = ucfirst($qcStatus);
                                    ?>
                                    <span class="quality-indicator <?= $qcClass ?>"></span>
                                    <?= $qcText ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/purchase/grn/view/<?= $grn['id'] ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ((isset($grn['status']) ? $grn['status'] : '') === 'pending'): ?>
                                            <a href="/purchase/grn/edit/<?= $grn['id'] ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ((isset($grn['status']) ? $grn['status'] : '') === 'received'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success btn-action" title="Start QC" onclick="startQC(<?= $grn['id'] ?>)">
                                                <i class="fas fa-clipboard-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="/purchase/grn/print/<?= $grn['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-truck fa-2x mb-2"></i><br>
                                No goods receipt notes found
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
            $('#grnTable').DataTable({
                pageLength: 25,
                order: [[3, 'desc']], // Sort by receipt date descending
                responsive: true,
                language: {
                    search: "Search GRNs:",
                    lengthMenu: "Show _MENU_ GRNs per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ GRNs"
                }
            });
        });

        // Start QC process
        function startQC(grnId) {
            if (confirm('Are you sure you want to start quality control for this GRN?')) {
                // Redirect to QC form
                window.location.href = `/purchase/grn/qc/${grnId}`;
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('grnTable');
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
                    if (index < 8) { // Exclude actions column
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
            link.setAttribute('download', 'goods_receipt_notes.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
