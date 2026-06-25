<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-users me-3"></i>Customer Master</h1>
    <div class="header-actions">
        <a href="<?= base_url('customer/create') ?>" class="btn-primary">
            <i class="fas fa-plus"></i>
            Add Customer
        </a>
        <a href="<?= base_url('customer/export') ?>" class="btn btn-outline-primary">
            <i class="fas fa-download"></i>
            Export
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
        <div class="stat-label">Total Customers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value"><?= isset($stats['active']) ? $stats['active'] : 0 ?></div>
        <div class="stat-label">Active Customers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-value"><?= isset($stats['with_credit_limit']) ? $stats['with_credit_limit'] : 0 ?></div>
        <div class="stat-label">With Credit Limit</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div class="stat-value">₹<?= number_format(isset($stats['total_credit_limit']) ? $stats['total_credit_limit'] : 0, 0) ?></div>
        <div class="stat-label">Total Credit Limit</div>
    </div>
</div>

<!-- Filters -->
<div class="filters-section">
    <h5><i class="fas fa-filter me-2"></i>Filters</h5>
    <form method="GET" action="<?= base_url('customer') ?>" data-validate>
        <div class="filter-row">
            <div class="form-group">
                <label class="form-label">Search</label>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Search customers..." value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" data-target="#customersTable">
                </div>
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
                <label class="form-label">Sales Zone</label>
                <input type="text" name="sales_zone" class="form-control" placeholder="Sales Zone" value="<?= isset($filters['sales_zone']) ? $filters['sales_zone'] : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Sales Region</label>
                <input type="text" name="sales_region" class="form-control" placeholder="Sales Region" value="<?= isset($filters['sales_region']) ? $filters['sales_region'] : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= base_url('customer') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Customers Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Customer List</h5>
        <div>
            <span class="badge bg-light text-dark"><?= count($customers ?? []) ?> customers</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="customersTable">
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Sales Zone</th>
                    <th>Credit Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No customers found</p>
                            <a href="<?= base_url('customer/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Customer
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr data-selectable>
                            <td>
                                <strong><?= $customer['customer_code'] ?></strong>
                            </td>
                            <td>
                                <div>
                                    <strong><?= $customer['customer_name'] ?></strong>
                                    <?php if ($customer['gst_number']): ?>
                                        <br><small class="text-muted">GST: <?= $customer['gst_number'] ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= $customer['contact_person'] ?></td>
                            <td><?= $customer['phone'] ?></td>
                            <td>
                                <?php if ($customer['email']): ?>
                                    <a href="mailto:<?= $customer['email'] ?>"><?= $customer['email'] ?></a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($customer['sales_zone']): ?>
                                    <span class="badge bg-info"><?= $customer['sales_zone'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($customer['credit_limit'] > 0): ?>
                                    <span class="text-success">₹<?= number_format($customer['credit_limit'], 0) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $customer['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                                    <?= ucfirst($customer['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('customer/show/' . $customer['id']) ?>" class="btn btn-sm btn-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('customer/edit/' . $customer['id']) ?>" class="btn btn-sm btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-toggle" title="Toggle Status" onclick="toggleStatus(<?= $customer['id'] ?>, 'customer')">
                                        <i class="fas fa-toggle-on"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-delete" title="Delete" onclick="deleteItem(<?= $customer['id'] ?>, 'customer')">
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
    // Page specific JavaScript can be added here
    document.addEventListener('DOMContentLoaded', function() {
        // Any customer-specific functionality
    });
</script>
<?= $this->endSection() ?> 