<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        border-left: 5px solid #28a745;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        color: #28a745;
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
    
    .transfer-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-left: 5px solid #28a745;
        transition: transform 0.3s ease;
    }
    
    .transfer-card:hover {
        transform: translateY(-5px);
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-pending { background-color: #cce7ff; color: #004085; }
    .status-in_transit { background-color: #fff3cd; color: #856404; }
    .status-completed { background-color: #d4edda; color: #155724; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }
    
    .btn-action {
        padding: 4px 8px;
        font-size: 0.8rem;
        margin: 2px;
    }
    
    .transfer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 20px;
    }
    
    .view-toggle {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 20px;
        text-align: center;
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
        background: #28a745;
        border-color: #28a745;
        color: white;
    }
    
    .transfer-details {
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
    
    .route-info {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px;
        margin: 10px 0;
        text-align: center;
    }
    
    .route-arrow {
        font-size: 1.5rem;
        color: #28a745;
        margin: 0 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center">
        <h1 class="h2 mb-2">
            <i class="fas fa-exchange-alt me-3"></i>
            Stock Transfer Management
        </h1>
        <p class="mb-0">Inter-warehouse transfers with approval workflows and tracking</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('inventory') ?>" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>Inventory Management
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Stock Transfers</li>
        </ol>
    </nav>

    <?php
    // Calculate stats
    $totalTransfers = count($transfers ?? []);
    $pendingCount = 0;
    $inTransitCount = 0;
    $completedCount = 0;
    
    foreach ($transfers ?? [] as $transfer) {
        $status = strtolower($transfer['status'] ?? '');
        if ($status === 'pending') {
            $pendingCount++;
        } elseif ($status === 'in_transit') {
            $inTransitCount++;
        } elseif ($status === 'completed') {
            $completedCount++;
        }
    }
    ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card text-center">
                <div class="stats-number"><?= $totalTransfers ?></div>
                <div class="text-muted">Total Transfers</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card text-center">
                <div class="stats-number"><?= $pendingCount ?></div>
                <div class="text-muted">Pending Approval</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card text-center">
                <div class="stats-number"><?= $inTransitCount ?></div>
                <div class="text-muted">In Transit</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card text-center">
                <div class="stats-number"><?= $completedCount ?></div>
                <div class="text-muted">Completed</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2"></i>
            Filters
        </h5>
        <form method="GET" action="<?= base_url('stock-transfer') ?>" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= esc(request()->getGet('search') ?? '') ?>" placeholder="Transfer number, item...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="pending" <?= (request()->getGet('status') ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_transit" <?= (request()->getGet('status') ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                    <option value="completed" <?= (request()->getGet('status') ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= (request()->getGet('status') ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="source_warehouse" class="form-label">Source</label>
                <select class="form-select" id="source_warehouse" name="source_warehouse">
                    <option value="">All Sources</option>
                    <?php foreach ($warehouses ?? [] as $warehouse): ?>
                        <option value="<?= $warehouse['id'] ?>" <?= (request()->getGet('source_warehouse') ?? '') == $warehouse['id'] ? 'selected' : '' ?>>
                            <?= esc($warehouse['warehouse_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="destination_warehouse" class="form-label">Destination</label>
                <select class="form-select" id="destination_warehouse" name="destination_warehouse">
                    <option value="">All Destinations</option>
                    <?php foreach ($warehouses ?? [] as $warehouse): ?>
                        <option value="<?= $warehouse['id'] ?>" <?= (request()->getGet('destination_warehouse') ?? '') == $warehouse['id'] ? 'selected' : '' ?>>
                            <?= esc($warehouse['warehouse_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
    <div class="view-toggle">
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
            <a href="<?= base_url('stock-transfer') ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-refresh me-2"></i>Refresh
            </a>
            <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                <i class="fas fa-download me-2"></i>Export CSV
            </button>
        </div>
        <div>
            <a href="<?= base_url('stock-transfer/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Transfer
            </a>
        </div>
    </div>

    <!-- Grid View -->
    <div id="grid-view" class="transfer-grid">
        <?php if (!empty($transfers) && is_array($transfers)): ?>
            <?php foreach ($transfers as $transfer): ?>
                <div class="transfer-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1"><?= esc($transfer['transfer_code'] ?? 'N/A') ?></h5>
                            <small class="text-muted"><?= esc($transfer['transfer_date'] ?? 'N/A') ?></small>
                        </div>
                        <div class="text-end">
                            <span class="status-badge status-<?= strtolower($transfer['status'] ?? 'pending') ?>">
                                <?= ucwords(str_replace('_', ' ', $transfer['status'] ?? 'Pending')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Route Information -->
                    <div class="route-info">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <small class="text-muted d-block">From</small>
                                <strong><?= esc($transfer['source_warehouse_name'] ?? 'N/A') ?></strong>
                            </div>
                            <div class="col-2">
                                <i class="fas fa-arrow-right route-arrow"></i>
                            </div>
                            <div class="col-5">
                                <small class="text-muted d-block">To</small>
                                <strong><?= esc($transfer['destination_warehouse_name'] ?? 'N/A') ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transfer Details -->
                    <div class="transfer-details">
                        <div class="detail-item">
                            <span class="detail-label">Created By:</span>
                            <span class="detail-value"><?= esc($transfer['created_by_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value"><?= esc($transfer['transfer_date'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <a href="<?= base_url('stock-transfer/view/' . $transfer['id']) ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('stock-transfer/edit/' . $transfer['id']) ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div>
                            <?php if (($transfer['status'] ?? '') === 'pending'): ?>
                                <a href="<?= base_url('stock-transfer/approve/' . $transfer['id']) ?>" class="btn btn-sm btn-outline-success btn-action" title="Approve">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                <h5>No transfers found</h5>
                <p>Start by creating your first stock transfer</p>
                <a href="<?= base_url('stock-transfer/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Transfer
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Table View (Hidden by default) -->
    <div id="table-view" class="table-container" style="display: none;">
        <table id="transfersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Transfer Code</th>
                    <th>Transfer Date</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transfers) && is_array($transfers)): ?>
                    <?php foreach ($transfers as $transfer): ?>
                        <tr>
                            <td><strong><?= esc($transfer['transfer_code'] ?? 'N/A') ?></strong></td>
                            <td><?= esc($transfer['transfer_date'] ?? 'N/A') ?></td>
                            <td><?= esc($transfer['source_warehouse_name'] ?? 'N/A') ?></td>
                            <td><?= esc($transfer['destination_warehouse_name'] ?? 'N/A') ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($transfer['status'] ?? 'pending') ?>">
                                    <?= ucwords(str_replace('_', ' ', $transfer['status'] ?? 'Pending')) ?>
                                </span>
                            </td>
                            <td><?= esc($transfer['created_by_name'] ?? 'N/A') ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('stock-transfer/view/' . $transfer['id']) ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('stock-transfer/edit/' . $transfer['id']) ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (($transfer['status'] ?? '') === 'pending'): ?>
                                        <a href="<?= base_url('stock-transfer/approve/' . $transfer['id']) ?>" class="btn btn-sm btn-outline-success btn-action" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-exchange-alt fa-3x mb-3 d-block"></i>
                            <h5>No transfers found</h5>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable for table view
        $('#transfersTable').DataTable({
            pageLength: 25,
            order: [[1, 'desc']], // Sort by transfer date
            responsive: true,
            language: {
                search: "Search transfers:",
                lengthMenu: "Show _MENU_ transfers per page",
                info: "Showing _START_ to _END_ of _TOTAL_ transfers"
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
        const table = document.getElementById('transfersTable');
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
        link.setAttribute('download', 'stock_transfers.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
<?= $this->endSection() ?>
