<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Purchase Returns</h1>
        <p class="text-muted mb-0">Manage purchase returns and track refunds</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-return/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Purchase Return
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('purchase-return') ?>" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Return number, supplier...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="draft" <?= (isset($filters['status']) ? $filters['status'] : '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="pending" <?= (isset($filters['status']) ? $filters['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= (isset($filters['status']) ? $filters['status'] : '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="processed" <?= (isset($filters['status']) ? $filters['status'] : '') === 'processed' ? 'selected' : '' ?>>Processed</option>
                    <option value="completed" <?= (isset($filters['status']) ? $filters['status'] : '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= (isset($filters['status']) ? $filters['status'] : '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="supplier" class="form-label">Supplier</label>
                <select class="form-control" id="supplier" name="supplier_id">
                    <option value="">All Suppliers</option>
                    <?php foreach (isset($suppliers) ? $suppliers : [] as $supplier): ?>
                        <option value="<?= $supplier['id'] ?>" <?= (isset($filters['supplier_id']) ? $filters['supplier_id'] : '') == $supplier['id'] ? 'selected' : '' ?>>
                            <?= esc($supplier['supplier_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-undo"></i>
            </div>
            <div class="stat-content">
                <h3><?= isset($stats['total']) ? $stats['total'] : 0 ?></h3>
                <p>Total Returns</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?= isset($stats['pending']) ? $stats['pending'] : 0 ?></h3>
                <p>Pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= isset($stats['completed']) ? $stats['completed'] : 0 ?></h3>
                <p>Completed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-content">
                <h3>₹<?= number_format(isset($stats['total_amount']) ? $stats['total_amount'] : 0) ?></h3>
                <p>Total Value</p>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Returns Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Purchase Returns
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportData('csv')">
                <i class="fas fa-download me-1"></i>Export CSV
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportData('pdf')">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($purchase_returns)): ?>
            <div class="text-center py-5">
                <i class="fas fa-undo fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Purchase Returns Found</h5>
                <p class="text-muted">Create your first purchase return to get started.</p>
                <a href="<?= base_url('purchase-return/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Purchase Return
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Return Number</th>
                            <th>Supplier</th>
                            <th>Original PO</th>
                            <th>Return Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchase_returns as $return): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($return['return_number']) ?></strong>
                                    <?php if ($return['is_urgent']): ?>
                                        <span class="badge bg-danger ms-1">Urgent</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= esc($return['supplier_name']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= esc($return['contact_person']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= base_url('purchase-order/show/' . $return['purchase_order_id']) ?>" 
                                       class="text-decoration-none">
                                        <?= esc($return['po_number']) ?>
                                    </a>
                                </td>
                                <td><?= date('d/m/Y', strtotime($return['return_date'])) ?></td>
                                <td>
                                    <strong>₹<?= number_format($return['total_amount'], 2) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $return['item_count'] ?> items</small>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'processed' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$return['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($return['status']) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('purchase-return/show/' . $return['id']) ?>" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($return['status'], ['draft', 'pending'])): ?>
                                            <a href="<?= base_url('purchase-return/edit/' . $return['id']) ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($return['status'] === 'approved'): ?>
                                            <a href="<?= base_url('purchase-return/process/' . $return['id']) ?>" 
                                               class="btn btn-outline-success" title="Process">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (in_array($return['status'], ['draft', 'pending'])): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deletePurchaseReturn(<?= $return['id'] ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function deletePurchaseReturn(id) {
    if (confirm('Are you sure you want to delete this purchase return? This action cannot be undone.')) {
        fetch(`<?= base_url('purchase-return/delete') ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the purchase return.');
        });
    }
}

function exportData(format) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('export', format);
    window.location.href = currentUrl.toString();
}
</script>
<?= $this->endSection() ?>
