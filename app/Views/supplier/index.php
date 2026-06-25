<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
           
<!-- Header -->
<div class="header">
    <div>
        <h1><?= $title ?></h1>
        <p class="text-muted mb-0">Manage supplier information and performance</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('supplier/export') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-download me-2"></i>Export
        </a>
        <a href="<?= base_url('supplier/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Supplier
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-truck"></i>
        </div>
        <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
        <div class="stat-label">Total Suppliers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value"><?= isset($stats['active']) ? $stats['active'] : 0 ?></div>
        <div class="stat-label">Active Suppliers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-value"><?= isset($stats['raw_material']) ? $stats['raw_material'] : 0 ?></div>
        <div class="stat-label">Raw Material</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-cube"></i>
        </div>
        <div class="stat-value"><?= isset($stats['packaging']) ? $stats['packaging'] : 0 ?></div>
        <div class="stat-label">Packaging</div>
    </div>
</div>

<!-- Filters -->
<div class="filters-section">
    <h5><i class="fas fa-filter me-2"></i>Filters</h5>
    <form method="GET" action="<?= base_url('supplier') ?>" data-validate>
        <div class="filter-row">
            <div class="form-group">
                <label class="form-label">Search</label>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Search suppliers..." value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" data-target="#suppliersTable">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="raw_material" <?= (isset($filters['category']) ? $filters['category'] : '') === 'raw_material' ? 'selected' : '' ?>>Raw Material</option>
                    <option value="packaging" <?= (isset($filters['category']) ? $filters['category'] : '') === 'packaging' ? 'selected' : '' ?>>Packaging</option>
                    <option value="service" <?= (isset($filters['category']) ? $filters['category'] : '') === 'service' ? 'selected' : '' ?>>Service</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= base_url('supplier') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Suppliers Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All Suppliers</h5>
        <div>
            <a href="<?= base_url('supplier/outstanding-payments') ?>" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-exclamation-triangle me-2"></i>Outstanding Payments
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table" id="suppliersTable">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Category</th>
                    <th>GST Number</th>
                    <th>Credit Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($suppliers)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h5>No Suppliers Found</h5>
                            <p>Create your first supplier to get started.</p>
                            <a href="<?= base_url('supplier/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Supplier
                            </a>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($suppliers as $supplier): ?>
                <tr data-selectable>
                    <td>
                        <strong><?= $supplier['supplier_code'] ?></strong>
                    </td>
                    <td>
                        <div>
                            <strong><?= $supplier['supplier_name'] ?></strong>
                            <?php if ($supplier['email']): ?>
                            <br><small class="text-muted"><?= $supplier['email'] ?></small>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?= $supplier['contact_person'] ?: '-' ?>
                        <?php if ($supplier['phone']): ?>
                        <br><small class="text-muted"><?= $supplier['phone'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $supplier['supplier_category'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $supplier['supplier_category'])) ?>
                        </span>
                    </td>
                    <td>
                        <?= $supplier['gst_number'] ?: '-' ?>
                    </td>
                    <td>
                        <?= $supplier['credit_limit'] ? '₹' . number_format($supplier['credit_limit']) : '-' ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $supplier['status'] ?>">
                            <?= ucfirst($supplier['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?= base_url('supplier/show/' . $supplier['id']) ?>" class="btn btn-sm btn-view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('supplier/edit/' . $supplier['id']) ?>" class="btn btn-sm btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-toggle" 
                                    onclick="toggleStatus(<?= $supplier['id'] ?>, 'supplier')" 
                                    title="Toggle Status">
                                <i class="fas fa-toggle-on"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-delete" 
                                    onclick="deleteItem(<?= $supplier['id'] ?>, 'supplier')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Supplier specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Supplier page loaded successfully!');
});
</script>
<?= $this->endSection() ?> 