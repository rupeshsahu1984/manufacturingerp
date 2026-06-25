<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-industry me-2"></i>PRODX
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= base_url('production') ?>">
                    <i class="fas fa-cogs me-1"></i>Production
                </a>
                <a class="nav-link" href="<?= base_url('inventory') ?>">
                    <i class="fas fa-boxes me-1"></i>Inventory
                </a>
                <a class="nav-link" href="<?= base_url('purchase') ?>">
                    <i class="fas fa-shopping-cart me-1"></i>Purchase
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-dark">
                            <i class="fas fa-list-alt me-2 text-primary"></i>Bill of Materials
                        </h1>
                        <p class="text-muted mb-0">Manage your product structures and manufacturing recipes</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('production/boms/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create BOM
                        </a>
                        <a href="<?= base_url('production') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-list-alt text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Total BOMs</h5>
                                <h2 class="mb-0 text-dark"><?= count($boms) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Released</h5>
                                <h2 class="mb-0 text-dark"><?= count(array_filter($boms, function($bom) { return $bom['status'] === 'released'; })) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Under Review</h5>
                                <h2 class="mb-0 text-dark"><?= count(array_filter($boms, function($bom) { return $bom['status'] === 'under_review'; })) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-edit text-secondary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Draft</h5>
                                <h2 class="mb-0 text-dark"><?= count(array_filter($boms, function($bom) { return $bom['status'] === 'draft'; })) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">All Statuses</option>
                            <?php foreach ($bomStatuses as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="typeFilter" class="form-label">BOM Type</label>
                        <select class="form-select" id="typeFilter" name="type">
                            <option value="">All Types</option>
                            <?php foreach ($bomTypes as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="itemFilter" class="form-label">Finished Good</label>
                        <select class="form-select select2" id="itemFilter" name="item_id">
                            <option value="">All Items</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= $item['item_code'] ?> - <?= $item['item_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- BOM List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-list me-2 text-primary"></i>BOM List
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="exportToCSV()">
                            <i class="fas fa-download me-1"></i>Export CSV
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bomTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>BOM Number</th>
                                <th>Finished Good</th>
                                <th>Revision</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Qty Per</th>
                                <th>Effective From</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($boms as $bom): ?>
                                <tr>
                                    <td>
                                        <strong><?= $bom['bom_number'] ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold"><?= isset($bom['item_code']) ? $bom['item_code'] : 'N/A' ?></div>
                                            <small class="text-muted"><?= isset($bom['item_name']) ? $bom['item_name'] : 'N/A' ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $bom['revision'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= ucfirst($bom['bom_type']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'secondary';
                                        switch($bom['status']) {
                                            case 'released':
                                                $statusClass = 'success';
                                                break;
                                            case 'under_review':
                                                $statusClass = 'warning';
                                                break;
                                            case 'draft':
                                                $statusClass = 'secondary';
                                                break;
                                            case 'obsolete':
                                                $statusClass = 'danger';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', $bom['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= $bom['qty_per'] ?></strong>
                                        <small class="text-muted"><?= $bom['uom'] ?></small>
                                    </td>
                                    <td>
                                        <?= date('M d, Y', strtotime($bom['effective_from'])) ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= isset($bom['created_by_name']) ? $bom['created_by_name'] : 'N/A' ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= base_url('production/boms/view/' . $bom['id']) ?>" 
                                               class="btn btn-outline-primary" 
                                               title="View BOM">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($bom['status'] === 'draft'): ?>
                                                <a href="<?= base_url('production/boms/edit/' . $bom['id']) ?>" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit BOM">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($bom['status'] === 'under_review'): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-success" 
                                                        title="Approve BOM"
                                                        onclick="approveBOM(<?= $bom['id'] ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" 
                                                    class="btn btn-outline-info" 
                                                    title="Explode BOM"
                                                    onclick="explodeBOM(<?= $bom['id'] ?>)">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve BOM Modal -->
    <div class="modal fade" id="approveBomModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve BOM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveBomForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="approvalNotes" class="form-label">Approval Notes</label>
                            <textarea class="form-control" id="approvalNotes" name="notes" rows="3" placeholder="Enter any approval notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Approve BOM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Explode BOM Modal -->
    <div class="modal fade" id="explodeBomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">BOM Explosion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="explodeBomForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="explodeQuantity" class="form-label">Quantity to Explode</label>
                            <input type="number" class="form-control" id="explodeQuantity" name="quantity" value="1" min="1" step="0.01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-expand-arrows-alt me-2"></i>Explode BOM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let currentBomId = null;
        let bomTable;

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Initialize DataTable
            bomTable = $('#bomTable').DataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });

            // Filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });
        });

        function applyFilters() {
            const status = $('#statusFilter').val();
            const type = $('#typeFilter').val();
            const itemId = $('#itemFilter').val();

            bomTable.draw();

            // Custom filtering
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const rowStatus = data[4].toLowerCase();
                const rowType = data[3].toLowerCase();
                const rowItemId = data[1]; // This would need to be adjusted based on actual data structure

                if (status && !rowStatus.includes(status.toLowerCase())) return false;
                if (type && !rowType.includes(type.toLowerCase())) return false;
                // Item ID filtering would need more complex logic

                return true;
            });

            bomTable.draw();
        }

        function refreshTable() {
            location.reload();
        }

        function exportToCSV() {
            const table = document.getElementById('bomTable');
            const rows = Array.from(table.querySelectorAll('tr'));
            
            let csv = [];
            rows.forEach(row => {
                const cols = Array.from(row.querySelectorAll('td, th'));
                const rowData = cols.map(col => {
                    // Remove HTML tags and get text content
                    const text = col.textContent || col.innerText || '';
                    return `"${text.replace(/"/g, '""')}"`;
                });
                csv.push(rowData.join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'bom_list_' + new Date().toISOString().slice(0, 10) + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function approveBOM(bomId) {
            currentBomId = bomId;
            $('#approveBomModal').modal('show');
        }

        function explodeBOM(bomId) {
            currentBomId = bomId;
            $('#explodeBomModal').modal('show');
        }

        // Approve BOM form submission
        $('#approveBomForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('notes', $('#approvalNotes').val());
            
            fetch(`<?= base_url('production/boms/approve/') ?>${currentBomId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                $('#approveBomModal').modal('hide');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error approving BOM');
            });
        });

        // Explode BOM form submission
        $('#explodeBomForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('quantity', $('#explodeQuantity').val());
            
            // Redirect to explosion view
            window.location.href = `<?= base_url('production/boms/explode/') ?>${currentBomId}`;
        });
    </script>
</body>
</html>
