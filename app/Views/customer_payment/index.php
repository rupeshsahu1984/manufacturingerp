<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-money-bill-wave me-3"></i>Customer Payments</h1>
    <div class="header-actions">
        <a href="<?= base_url('customer-payment/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Payment
        </a>
        <a href="<?= base_url('customer-payment/export') ?>" class="btn btn-outline-success ms-2">
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
        <form method="GET" action="<?= base_url('customer-payment') ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Payments</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by payment number, customer, invoice...">
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
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="">All Methods</option>
                        <option value="cash" <?= (isset($filters['payment_method']) ? $filters['payment_method'] : '') == 'cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="bank_transfer" <?= (isset($filters['payment_method']) ? $filters['payment_method'] : '') == 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                        <option value="cheque" <?= (isset($filters['payment_method']) ? $filters['payment_method'] : '') == 'cheque' ? 'selected' : '' ?>>Cheque</option>
                        <option value="credit_card" <?= (isset($filters['payment_method']) ? $filters['payment_method'] : '') == 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                        <option value="online" <?= (isset($filters['payment_method']) ? $filters['payment_method'] : '') == 'online' ? 'selected' : '' ?>>Online</option>
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
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #1a202c !important;"><?= isset($stats['total_payments']) ? $stats['total_payments'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Payments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #059669 !important;">₹<?= number_format(isset($stats['total_amount']) ? $stats['total_amount'] : 0, 2) ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Amount</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #0891b2 !important;"><?= isset($stats['cash_payments']) ? $stats['cash_payments'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Cash Payments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-university"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #d97706 !important;"><?= isset($stats['bank_transfer_payments']) ? $stats['bank_transfer_payments'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Bank Transfers</div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Payments List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($payments)): ?>
            <div class="text-center py-4">
                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No payments found</h5>
                <p class="text-muted">Create your first customer payment to get started.</p>
                <a href="<?= base_url('customer-payment/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Payment
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Payment Number</th>
                            <th>Customer</th>
                            <th>Invoice Number</th>
                            <th>Payment Date</th>
                            <th>Payment Amount</th>
                            <th>Payment Method</th>
                            <th>Reference Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($payment['payment_number']) ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= esc(isset($payment['customer_name']) ? $payment['customer_name'] : 'N/A') ?></strong>
                                        <?php if (isset($payment['customer_email'])): ?>
                                            <br>
                                            <small class="text-muted"><?= esc($payment['customer_email']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (isset($payment['invoice_number'])): ?>
                                        <a href="<?= base_url('invoice/show/' . $payment['invoice_id']) ?>" class="text-decoration-none">
                                            <?= esc($payment['invoice_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= isset($payment['payment_date']) ? date('d M Y', strtotime($payment['payment_date'])) : 'N/A' ?></td>
                                <td>
                                    <strong>₹<?= number_format(isset($payment['payment_amount']) ? $payment['payment_amount'] : 0, 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $methodLabels = [
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank Transfer',
                                        'cheque' => 'Cheque',
                                        'credit_card' => 'Credit Card',
                                        'online' => 'Online'
                                    ];
                                    $method = isset($payment['payment_method']) ? $payment['payment_method'] : '';
                                    ?>
                                    <span class="badge bg-secondary">
                                        <?= isset($methodLabels[$method]) ? $methodLabels[$method] : ucfirst(str_replace('_', ' ', $method)) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= esc(isset($payment['reference_number']) ? $payment['reference_number'] : '-') ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('customer-payment/show/' . $payment['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('customer-payment/print/' . $payment['id']) ?>"
                                           class="btn btn-sm btn-outline-info" title="Print Receipt">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php if (isset($payment['status']) && $payment['status'] == 'draft'): ?>
                                            <a href="<?= base_url('customer-payment/edit/' . $payment['id']) ?>"
                                               class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deletePayment(<?= $payment['id'] ?>)" title="Delete">
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
function deletePayment(id) {
    if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
        window.location.href = '<?= base_url('customer-payment/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>

