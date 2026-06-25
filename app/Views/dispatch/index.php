<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-truck me-3"></i>Dispatch Notes</h1>
    <div class="header-actions">
        <a href="<?= base_url('dispatch/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Dispatch Note
        </a>
        <a href="<?= base_url('dispatch/export') ?>" class="btn btn-outline-success ms-2">
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
        <form method="GET" action="<?= base_url('dispatch') ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Dispatch Notes</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by DN number, SO number, customer...">
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
                        <option value="dispatched" <?= (isset($filters['status']) ? $filters['status'] : '') == 'dispatched' ? 'selected' : '' ?>>Dispatched</option>
                        <option value="delivered" <?= (isset($filters['status']) ? $filters['status'] : '') == 'delivered' ? 'selected' : '' ?>>Delivered</option>
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
            <i class="fas fa-truck"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #1a202c !important;"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Dispatch Notes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #d97706 !important;"><?= isset($stats['draft']) ? $stats['draft'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Draft</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-shipping-fast"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #0891b2 !important;"><?= isset($stats['dispatched']) ? $stats['dispatched'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Dispatched</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #059669 !important;"><?= isset($stats['delivered']) ? $stats['delivered'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Delivered</div>
        </div>
    </div>
</div>

<!-- Dispatch Notes Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Dispatch Notes List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($dispatch_notes)): ?>
            <div class="text-center py-4">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No dispatch notes found</h5>
                <p class="text-muted">Create your first dispatch note to get started.</p>
                <a href="<?= base_url('dispatch/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Dispatch Note
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>DN Number</th>
                            <th>SO Number</th>
                            <th>Customer</th>
                            <th>Dispatch Date</th>
                            <th>Transport Mode</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dispatch_notes as $note): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($note['dn_number']) ?></strong>
                                </td>
                                <td><?= esc(isset($note['so_number']) ? $note['so_number'] : 'N/A') ?></td>
                                <td><?= esc(isset($note['customer_name']) ? $note['customer_name'] : 'N/A') ?></td>
                                <td><?= date('d M Y', strtotime($note['dispatch_date'])) ?></td>
                                <td><?= esc(isset($note['transport_mode']) ? $note['transport_mode'] : 'Not specified') ?></td>
                                <td><?= esc(isset($note['driver_name']) ? $note['driver_name'] : 'Not specified') ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if ($note['status'] == 'dispatched') $statusClass = 'info';
                                    elseif ($note['status'] == 'delivered') $statusClass = 'success';
                                    elseif ($note['status'] == 'cancelled') $statusClass = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= ucfirst($note['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('dispatch/show/' . $note['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('dispatch/edit/' . $note['id']) ?>"
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('dispatch/print/' . $note['id']) ?>"
                                           class="btn btn-sm btn-outline-info" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="deleteDispatchNote(<?= $note['id'] ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
function deleteDispatchNote(noteId) {
    if (confirm('Are you sure you want to delete this dispatch note? This action cannot be undone.')) {
        window.location.href = '<?= base_url('dispatch/delete/') ?>' + noteId;
    }
}
</script>
<?= $this->endSection() ?>
