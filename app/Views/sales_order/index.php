<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-chart-bar me-3"></i>Sales Orders</h1>
    <div class="header-actions">
        <a href="<?= base_url('sales-order/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Sales Order
        </a>
        <a href="<?= base_url('sales-order/export') ?>" class="btn btn-outline-success ms-2">
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
        <form method="GET" action="<?= base_url('sales-order') ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Orders</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by SO number, customer...">
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
                        <option value="confirmed" <?= (isset($filters['status']) ? $filters['status'] : '') == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="processing" <?= (isset($filters['status']) ? $filters['status'] : '') == 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="ready" <?= (isset($filters['status']) ? $filters['status'] : '') == 'ready' ? 'selected' : '' ?>>Ready</option>
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
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #1a202c !important;"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #059669 !important;"><?= isset($stats['confirmed']) ? $stats['confirmed'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Confirmed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-sync-alt"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #d97706 !important;"><?= isset($stats['processing']) ? $stats['processing'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Processing</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #0891b2 !important;">₹<?= number_format(isset($stats['total_value']) ? $stats['total_value'] : 0, 2) ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Value</div>
        </div>
    </div>
</div>

<!-- Sales Orders Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Sales Orders List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($sales_orders)): ?>
            <div class="text-center py-4">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No sales orders found</h5>
                <p class="text-muted">Create your first sales order to get started.</p>
                <a href="<?= base_url('sales-order/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Sales Order
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SO Number</th>
                            <th>Customer</th>
                            <th>Order Date</th>
                            <th>Delivery Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_orders as $order): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($order['so_number']) ?></strong>
                                </td>
                                <td><?= esc(isset($order['customer_name']) ? $order['customer_name'] : 'N/A') ?></td>
                                <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                                <td>
                                    <?= $order['delivery_date'] ? date('d M Y', strtotime($order['delivery_date'])) : 'Not set' ?>
                                </td>
                                <td>
                                    <strong>₹<?= number_format($order['total_amount'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if ($order['status'] == 'confirmed') $statusClass = 'success';
                                    elseif ($order['status'] == 'processing') $statusClass = 'warning';
                                    elseif ($order['status'] == 'ready') $statusClass = 'info';
                                    elseif ($order['status'] == 'dispatched') $statusClass = 'primary';
                                    elseif ($order['status'] == 'delivered') $statusClass = 'success';
                                    elseif ($order['status'] == 'cancelled') $statusClass = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('sales-order/show/' . $order['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('sales-order/edit/' . $order['id']) ?>"
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('sales-order/print/' . $order['id']) ?>"
                                           class="btn btn-sm btn-outline-info" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="deleteSalesOrder(<?= $order['id'] ?>)" title="Delete">
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
function deleteSalesOrder(orderId) {
    if (confirm('Are you sure you want to delete this sales order? This action cannot be undone.')) {
        window.location.href = '<?= base_url('sales-order/delete/') ?>' + orderId;
    }
}
</script>
<?= $this->endSection() ?>
