<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-undo me-3"></i>Sales Returns</h1>
    <div class="header-actions">
        <a href="<?= base_url('sales-return/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Sales Return
        </a>
        <a href="<?= base_url('sales-return/export') ?>" class="btn btn-outline-success ms-2">
            <i class="fas fa-download"></i> Export
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2"></i>Search & Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('sales-return') ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Returns</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by return number, customer, invoice...">
                </div>
                <div class="col-md-2">
                    <label for="customer" class="form-label">Customer</label>
                    <select class="form-select" id="customer" name="customer">
                        <option value="">All Customers</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" <?= (isset($filters['customer']) ? $filters['customer'] : '') == $customer['id'] ? 'selected' : '' ?>>
                                <?= esc($customer['customer_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="draft" <?= (isset($filters['status']) ? $filters['status'] : '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="submitted" <?= (isset($filters['status']) ? $filters['status'] : '') == 'submitted' ? 'selected' : '' ?>>Submitted</option>
                        <option value="approved" <?= (isset($filters['status']) ? $filters['status'] : '') == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="processed" <?= (isset($filters['status']) ? $filters['status'] : '') == 'processed' ? 'selected' : '' ?>>Processed</option>
                        <option value="cancelled" <?= (isset($filters['status']) ? $filters['status'] : '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
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
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-undo"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #1a202c !important;"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Returns</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #d97706 !important;"><?= isset($stats['pending']) ? $stats['pending'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #059669 !important;"><?= isset($stats['approved']) ? $stats['approved'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Approved</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #0891b2 !important;">₹<?= number_format(isset($stats['total_amount']) ? $stats['total_amount'] : 0, 2) ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Amount</div>
        </div>
    </div>
</div>

<!-- Sales Returns Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Sales Returns List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($sales_returns)): ?>
            <div class="text-center py-4">
                <i class="fas fa-undo fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No sales returns found</h5>
                <p class="text-muted">Create your first sales return to get started.</p>
                <a href="<?= base_url('sales-return/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Sales Return
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Return Number</th>
                            <th>Customer</th>
                            <th>Invoice Number</th>
                            <th>Return Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_returns as $return): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($return['return_number']) ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= esc(isset($return['customer_name']) ? $return['customer_name'] : 'N/A') ?></strong>
                                        <?php if (isset($return['customer_email'])): ?>
                                            <br>
                                            <small class="text-muted"><?= esc($return['customer_email']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (isset($return['invoice_number'])): ?>
                                        <a href="<?= base_url('invoice/show/' . $return['invoice_id']) ?>" class="text-decoration-none">
                                            <?= esc($return['invoice_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= isset($return['return_date']) ? date('d M Y', strtotime($return['return_date'])) : 'N/A' ?></td>
                                <td>
                                    <strong>₹<?= number_format(isset($return['total_amount']) ? $return['total_amount'] : 0, 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if (isset($return['status'])) {
                                        if ($return['status'] == 'approved') $statusClass = 'success';
                                        elseif ($return['status'] == 'submitted') $statusClass = 'warning';
                                        elseif ($return['status'] == 'processed') $statusClass = 'info';
                                        elseif ($return['status'] == 'cancelled') $statusClass = 'danger';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= isset($return['status']) ? ucfirst($return['status']) : 'N/A' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('sales-return/show/' . $return['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (isset($return['status']) && in_array($return['status'], ['draft', 'submitted'])): ?>
                                            <a href="<?= base_url('sales-return/edit/' . $return['id']) ?>"
                                               class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('sales-return/print/' . $return['id']) ?>"
                                           class="btn btn-sm btn-outline-info" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php if (isset($return['status']) && in_array($return['status'], ['draft', 'submitted'])): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteSalesReturn(<?= $return['id'] ?>)" title="Delete">
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
        <?php endif; ?>
    </div>
</div>

<script>
function deleteSalesReturn(id) {
    if (confirm('Are you sure you want to delete this sales return? This action cannot be undone.')) {
        window.location.href = '<?= base_url('sales-return/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>

