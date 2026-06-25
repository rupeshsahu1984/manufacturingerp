<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
    <style>
        .page-header {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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
            border-left: 5px solid #ffc107;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #ffc107;
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
        
        .adjustment-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .adjustment-increase { background-color: #d4edda; color: #155724; }
        .adjustment-decrease { background-color: #f8d7da; color: #721c24; }
        .adjustment-correction { background-color: #cce7ff; color: #004085; }
        .adjustment-damage { background-color: #fff3cd; color: #856404; }
        .adjustment-expiry { background-color: #e2e3e5; color: #383d41; }
        .adjustment-count { background-color: #d1ecf1; color: #0c5460; }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-adjust me-3"></i>
                Stock Adjustments
            </h1>
            <p class="mb-0">Manage stock adjustments, corrections, and write-offs</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('inventory') ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Inventory Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Stock Adjustments</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?= count($adjustments ?? []) ?>
                    </div>
                    <div class="text-muted">Total Adjustments</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?= isset($stats['pending']) ? $stats['pending'] : 0 ?>
                    </div>
                    <div class="text-muted">Pending Approval</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?= isset($stats['approved_this_month']) ? $stats['approved_this_month'] : 0 ?>
                    </div>
                    <div class="text-muted">Approved This Month</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        ₹<?= number_format(isset($stats['total_pending_value']) ? $stats['total_pending_value'] : 0, 2) ?>
                    </div>
                    <div class="text-muted">Pending Value</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="<?= base_url('stock-adjustment') ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="warehouse_id" class="form-label">Warehouse</label>
                    <select class="form-select" id="warehouse_id" name="warehouse_id">
                        <option value="">All Warehouses</option>
                        <?php if (isset($warehouses) && is_array($warehouses)): ?>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <?php if (is_array($warehouse) && isset($warehouse['id']) && isset($warehouse['warehouse_name'])): ?>
                                    <option value="<?= $warehouse['id'] ?>" <?= (isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : '') == $warehouse['id'] ? 'selected' : '' ?>>
                                        <?= esc($warehouse['warehouse_name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="adjustment_type" class="form-label">Adjustment Type</label>
                    <select class="form-select" id="adjustment_type" name="adjustment_type">
                        <option value="">All Types</option>
                        <option value="increase" <?= (isset($_GET['adjustment_type']) ? $_GET['adjustment_type'] : '') === 'increase' ? 'selected' : '' ?>>Increase</option>
                        <option value="decrease" <?= (isset($_GET['adjustment_type']) ? $_GET['adjustment_type'] : '') === 'decrease' ? 'selected' : '' ?>>Decrease</option>
                        <option value="correction" <?= (isset($_GET['adjustment_type']) ? $_GET['adjustment_type'] : '') === 'correction' ? 'selected' : '' ?>>Correction</option>
                        <option value="damage" <?= (isset($_GET['adjustment_type']) ? $_GET['adjustment_type'] : '') === 'damage' ? 'selected' : '' ?>>Damage</option>
                        <option value="expiry" <?= (isset($_GET['adjustment_type']) ? $_GET['adjustment_type'] : '') === 'expiry' ? 'selected' : '' ?>>Expiry</option>
                        <option value="count" <?= (isset($_GET['adjustment_type']) ? $_GET['adjustment_type'] : '') === 'count' ? 'selected' : '' ?>>Count</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.reload()">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </button>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>
            <div>
                <a href="<?= base_url('inventory/adjustments/create') ?>" class="btn btn-warning">
                    <i class="fas fa-plus me-2"></i>Create Adjustment
                </a>
            </div>
        </div>

        <!-- Adjustments Table -->
        <div class="table-container">
            <table id="adjustmentsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Item</th>
                        <th>Warehouse</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Total Value</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($adjustments) && is_array($adjustments) && !empty($adjustments)): ?>
                        <?php foreach ($adjustments as $adjustment): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($adjustment['reference_number'] ?? 'N/A') ?></strong>
                                </td>
                                <td>
                                    <?= esc($adjustment['item_name'] ?? 'N/A') ?>
                                    <?php if (isset($adjustment['item_code'])): ?>
                                        <br><small class="text-muted"><?= esc($adjustment['item_code']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($adjustment['warehouse_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="adjustment-badge adjustment-<?= esc($adjustment['adjustment_type'] ?? 'adjustment') ?>">
                                        <?= ucwords(str_replace('_', ' ', $adjustment['adjustment_type'] ?? 'Adjustment')) ?>
                                    </span>
                                </td>
                                <td><?= number_format($adjustment['quantity'] ?? 0, 2) ?></td>
                                <td>₹<?= number_format($adjustment['unit_cost'] ?? 0, 2) ?></td>
                                <td>₹<?= number_format(($adjustment['quantity'] ?? 0) * ($adjustment['unit_cost'] ?? 0), 2) ?></td>
                                <td><?= isset($adjustment['transaction_date']) ? date('d/m/Y', strtotime($adjustment['transaction_date'])) : 'N/A' ?></td>
                                <td>
                                    <span class="status-badge status-<?= esc($adjustment['status'] ?? 'pending') ?>">
                                        <?= ucfirst($adjustment['status'] ?? 'Pending') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('inventory/adjustments/view/' . ($adjustment['id'] ?? '')) ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (($adjustment['status'] ?? 'pending') === 'pending'): ?>
                                            <a href="<?= base_url('inventory/adjustments/edit/' . ($adjustment['id'] ?? '')) ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('inventory/adjustments/approve/' . ($adjustment['id'] ?? '')) ?>" class="btn btn-sm btn-outline-success btn-action" title="Approve" onclick="return confirm('Approve this adjustment?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-adjust fa-3x mb-3"></i>
                                <h5>No stock adjustments found</h5>
                                <p>Create a new adjustment to get started</p>
                                <a href="<?= base_url('inventory/adjustments/create') ?>" class="btn btn-warning">
                                    <i class="fas fa-plus me-2"></i>Create Adjustment
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#adjustmentsTable').DataTable({
                pageLength: 25,
                order: [[7, 'desc']], // Sort by date
                responsive: true,
                language: {
                    search: "Search adjustments:",
                    lengthMenu: "Show _MENU_ adjustments per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ adjustments"
                }
            });
        });

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('adjustmentsTable');
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
            link.setAttribute('download', 'stock_adjustments.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
<?= $this->endSection() ?>

