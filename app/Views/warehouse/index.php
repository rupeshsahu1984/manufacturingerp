<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-warehouse me-3"></i>Warehouse Master</h1>
    <div class="header-actions">
        <a href="<?= base_url('warehouse/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Warehouse
        </a>
        <a href="<?= base_url('warehouse/export') ?>" class="btn btn-outline-success ms-2">
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
        <form method="GET" action="<?= base_url('warehouse') ?>">
            <div class="row">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search Warehouses</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by code, name, or location...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="stat-card-body">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Warehouses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3><?= $stats['active'] ?></h3>
                <p>Active Warehouses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="stat-card-body">
                <h3><?= $stats['inactive'] ?></h3>
                <p>Inactive Warehouses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="stat-card-body">
                <h3><?= count($warehouses ?? []) ?></h3>
                <p>Displayed Results</p>
            </div>
        </div>
    </div>
</div>

<!-- Warehouses Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Warehouses List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($warehouses)): ?>
            <div class="text-center py-4">
                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No warehouses found</h5>
                <p class="text-muted">Create your first warehouse to get started.</p>
                <a href="<?= base_url('warehouse/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Warehouse
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Warehouse Code</th>
                            <th>Warehouse Name</th>
                            <th>Location</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($warehouse['warehouse_code']) ?></strong>
                                </td>
                                <td><?= esc($warehouse['warehouse_name']) ?></td>
                                <td><?= esc($warehouse['location']) ?></td>
                                <td>
                                    <?php if ($warehouse['first_name'] && $warehouse['last_name']): ?>
                                        <?= esc($warehouse['first_name'] . ' ' . $warehouse['last_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $warehouse['status'] == 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($warehouse['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($warehouse['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('warehouse/show/' . $warehouse['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('warehouse/edit/' . $warehouse['id']) ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteWarehouse(<?= $warehouse['id'] ?>)" title="Delete">
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
function deleteWarehouse(warehouseId) {
    if (confirm('Are you sure you want to delete this warehouse? This action cannot be undone.')) {
        window.location.href = '<?= base_url('warehouse/delete/') ?>' + warehouseId;
    }
}
</script>
<?= $this->endSection() ?>
