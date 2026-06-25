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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
            border-left: 5px solid #dc3545;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
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
        
        .debit-note-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #dc3545;
            transition: transform 0.3s ease;
        }
        
        .debit-note-card:hover {
            transform: translateY(-5px);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-pending { background-color: #cce7ff; color: #004085; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-processed { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .priority-low { background-color: #e8f5e8; color: #2e7d32; }
        .priority-normal { background-color: #e3f2fd; color: #1565c0; }
        .priority-high { background-color: #fff8e1; color: #f57f17; }
        .priority-urgent { background-color: #ffebee; color: #c62828; }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .debit-note-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }
        
        .view-toggle {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .btn-view-toggle {
            background: white;
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 8px 16px;
            border-radius: 8px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .btn-view-toggle.active {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .debit-note-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .detail-value {
            color: #333;
            font-size: 0.9rem;
        }
        
        .return-reason {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .return-reason-text {
            color: #856404;
            font-weight: 600;
            margin: 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-undo me-3"></i>
                Debit Notes Management
            </h1>
            <p class="mb-0">Supplier returns, quality issues, and debit note processing</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Debit Notes</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= count($debit_notes ?? []) ?></div>
                    <div class="text-muted">Total Debit Notes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $pendingCount = 0;
                        foreach ($debit_notes as $note) {
                            if ((isset($note['status']) ? $note['status'] : '') === 'pending') $pendingCount++;
                        }
                        echo $pendingCount;
                        ?>
                    </div>
                    <div class="text-muted">Pending Approval</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $totalValue = 0;
                        foreach ($debit_notes as $note) {
                            $totalValue += (isset($note['total_amount']) ? $note['total_amount'] : 0);
                        }
                        echo '₹' . number_format($totalValue, 0);
                        ?>
                    </div>
                    <div class="text-muted">Total Value</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $processedCount = 0;
                        foreach ($debit_notes as $note) {
                            if ((isset($note['status']) ? $note['status'] : '') === 'processed') $processedCount++;
                        }
                        echo $processedCount;
                        ?>
                    </div>
                    <div class="text-muted">Processed</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="/purchase/debit-notes" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Debit note number, supplier...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="draft" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="pending" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="processed" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'processed' ? 'selected' : '' ?>>Processed</option>
                        <option value="cancelled" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="supplier" class="form-label">Supplier</label>
                    <select class="form-select" id="supplier" name="supplier">
                        <option value="">All Suppliers</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>" <?= (isset($_GET['supplier']) ? $_GET['supplier'] : '') == $supplier['id'] ? 'selected' : '' ?>>
                                <?= esc($supplier['supplier_name']) ?>
                            </option>
                        <?php endforeach; ?>
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

        <!-- View Toggle -->
        <div class="view-toggle text-center">
            <button type="button" class="btn-view-toggle active" onclick="switchView('grid')">
                <i class="fas fa-th-large me-2"></i>Grid View
            </button>
            <button type="button" class="btn-view-toggle" onclick="switchView('table')">
                <i class="fas fa-table me-2"></i>Table View
            </button>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="/purchase/debit-notes" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </a>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
                <a href="/purchase/debit-notes/reports" class="btn btn-outline-warning me-2">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </div>
            <div>
                <a href="/purchase/debit-notes/create" class="btn btn-danger">
                    <i class="fas fa-plus me-2"></i>Create Debit Note
                </a>
            </div>
        </div>

        <!-- Grid View -->
        <div id="grid-view" class="debit-note-grid">
            <?php if (isset($debit_notes) && is_array($debit_notes) && count($debit_notes) > 0): ?>
                <?php foreach ($debit_notes as $note): ?>
                    <div class="debit-note-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?= esc(isset($note['debit_note_number']) ? $note['debit_note_number'] : 'N/A') ?></h5>
                                <small class="text-muted"><?= isset($note['debit_note_date']) ? $note['debit_note_date'] : 'N/A' ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge status-<?= strtolower(isset($note['status']) ? $note['status'] : 'draft') ?>">
                                    <?= ucwords(str_replace('_', ' ', isset($note['status']) ? $note['status'] : 'Draft')) ?>
                                </span>
                                <br>
                                <span class="priority-badge priority-<?= strtolower(isset($note['priority']) ? $note['priority'] : 'normal') ?>">
                                    <?= ucfirst(isset($note['priority']) ? $note['priority'] : 'Normal') ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Supplier Information -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Supplier</small>
                                <strong><?= esc(isset($note['supplier_name']) ? $note['supplier_name'] : 'N/A') ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Total Amount</small>
                                <strong class="text-danger">₹<?= number_format(isset($note['total_amount']) ? $note['total_amount'] : 0, 2) ?></strong>
                            </div>
                        </div>
                        
                        <!-- Return Reason -->
                        <?php if (!empty($note['return_reason'])): ?>
                            <div class="return-reason">
                                <p class="return-reason-text mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= esc($note['return_reason']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Debit Note Details -->
                        <div class="debit-note-details">
                            <div class="detail-item">
                                <span class="detail-label">Items:</span>
                                <span class="detail-value"><?= isset($note['total_items']) ? $note['total_items'] : 0 ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Created By:</span>
                                <span class="detail-value"><?= esc(isset($note['created_by_name']) ? $note['created_by_name'] : 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">GRN Reference:</span>
                                <span class="detail-value"><?= esc(isset($note['grn_number']) ? $note['grn_number'] : 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">PO Reference:</span>
                                <span class="detail-value"><?= esc(isset($note['po_number']) ? $note['po_number'] : 'N/A') ?></span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="/purchase/debit-notes/view/<?= $note['id'] ?>" class="btn btn-sm btn-outline-info btn-action">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/purchase/debit-notes/edit/<?= $note['id'] ?>" class="btn btn-sm btn-outline-warning btn-action">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/purchase/debit-notes/print/<?= $note['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                            <div>
                                <?php if ((isset($note['status']) ? $note['status'] : '') === 'pending'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success btn-action" onclick="approveDebitNote(<?= $note['id'] ?>)">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="rejectDebitNote(<?= $note['id'] ?>)">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                <?php elseif ((isset($note['status']) ? $note['status'] : '') === 'approved'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-action" onclick="processDebitNote(<?= $note['id'] ?>)">
                                        <i class="fas fa-cog"></i> Process
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-undo fa-3x mb-3"></i>
                    <h5>No debit notes found</h5>
                    <p>Start by creating your first debit note for supplier returns</p>
                    <a href="/purchase/debit-notes/create" class="btn btn-danger">
                        <i class="fas fa-plus me-2"></i>Create Debit Note
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Table View (Hidden by default) -->
        <div id="table-view" class="table-container" style="display: none;">
            <table id="debitNotesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Debit Note No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($debit_notes) && is_array($debit_notes)): ?>
                        <?php foreach ($debit_notes as $note): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($note['debit_note_number']) ? $note['debit_note_number'] : 'N/A') ?></strong>
                                    <br><small class="text-muted"><?= esc(isset($note['created_by_name']) ? $note['created_by_name'] : 'N/A') ?></small>
                                </td>
                                <td><?= isset($note['debit_note_date']) ? $note['debit_note_date'] : 'N/A' ?></td>
                                <td><?= esc(isset($note['supplier_name']) ? $note['supplier_name'] : 'N/A') ?></td>
                                <td class="text-danger">₹<?= number_format(isset($note['total_amount']) ? $note['total_amount'] : 0, 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(isset($note['status']) ? $note['status'] : 'draft') ?>">
                                        <?= ucwords(str_replace('_', ' ', isset($note['status']) ? $note['status'] : 'Draft')) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-badge priority-<?= strtolower(isset($note['priority']) ? $note['priority'] : 'normal') ?>">
                                        <?= ucfirst(isset($note['priority']) ? $note['priority'] : 'Normal') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/purchase/debit-notes/view/<?= $note['id'] ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/purchase/debit-notes/edit/<?= $note['id'] ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ((isset($note['status']) ? $note['status'] : '') === 'pending'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success btn-action" title="Approve" onclick="approveDebitNote(<?= $note['id'] ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
            // Initialize DataTable for table view
            $('#debitNotesTable').DataTable({
                pageLength: 25,
                order: [[1, 'desc']], // Sort by date
                responsive: true,
                language: {
                    search: "Search debit notes:",
                    lengthMenu: "Show _MENU_ debit notes per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ debit notes"
                }
            });
        });

        // Switch between grid and table views
        function switchView(viewType) {
            const gridView = document.getElementById('grid-view');
            const tableView = document.getElementById('table-view');
            const buttons = document.querySelectorAll('.btn-view-toggle');
            
            if (viewType === 'grid') {
                gridView.style.display = 'grid';
                tableView.style.display = 'none';
                buttons[0].classList.add('active');
                buttons[1].classList.remove('active');
            } else {
                gridView.style.display = 'none';
                tableView.style.display = 'block';
                buttons[0].classList.remove('active');
                buttons[1].classList.add('active');
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('debitNotesTable');
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
                    if (index < 6) { // Exclude actions column
                        let text = td.textContent.trim();
                        // Remove badges
                        text = text.replace(/[^\w\s%,.-₹]/g, '');
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
            link.setAttribute('download', 'debit_notes.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Debit Note action functions
        function approveDebitNote(noteId) {
            if (confirm('Are you sure you want to approve this debit note?')) {
                window.location.href = `/purchase/debit-notes/approve/${noteId}`;
            }
        }

        function rejectDebitNote(noteId) {
            if (confirm('Are you sure you want to reject this debit note?')) {
                window.location.href = `/purchase/debit-notes/reject/${noteId}`;
            }
        }

        function processDebitNote(noteId) {
            if (confirm('Are you sure you want to process this debit note?')) {
                window.location.href = `/purchase/debit-notes/process/${noteId}`;
            }
        }
    </script>
</body>
</html>
