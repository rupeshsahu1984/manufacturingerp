<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-file-invoice me-3"></i>Invoices</h1>
    <div class="header-actions">
        <a href="<?= base_url('invoice/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Invoice
        </a>
        <a href="<?= base_url('invoice/export') ?>" class="btn btn-outline-success ms-2">
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
        <form method="GET" action="<?= base_url('invoice') ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Invoices</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by invoice number, customer...">
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
                        <option value="sent" <?= (isset($filters['status']) ? $filters['status'] : '') == 'sent' ? 'selected' : '' ?>>Sent</option>
                        <option value="paid" <?= (isset($filters['status']) ? $filters['status'] : '') == 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="overdue" <?= (isset($filters['status']) ? $filters['status'] : '') == 'overdue' ? 'selected' : '' ?>>Overdue</option>
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
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #1a202c !important;"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Total Invoices</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #059669 !important;"><?= isset($stats['paid']) ? $stats['paid'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Paid</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value" style="color: #dc2626 !important;"><?= isset($stats['overdue']) ? $stats['overdue'] : 0 ?></div>
            <div class="stat-label" style="color: #4a5568 !important;">Overdue</div>
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

<!-- Invoices Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Invoices List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($invoices)): ?>
            <div class="text-center py-4">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No invoices found</h5>
                <p class="text-muted">Create your first invoice to get started.</p>
                <a href="<?= base_url('invoice/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Invoice
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Customer</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($invoice['invoice_number']) ?></strong>
                                </td>
                                <td><?= esc(isset($invoice['customer_name']) ? $invoice['customer_name'] : 'N/A') ?></td>
                                <td><?= date('d M Y', strtotime($invoice['invoice_date'])) ?></td>
                                <td>
                                    <?= $invoice['due_date'] ? date('d M Y', strtotime($invoice['due_date'])) : 'Not set' ?>
                                </td>
                                <td>
                                    <strong>₹<?= number_format($invoice['total_amount'], 2) ?></strong>
                                </td>
                                <td>
                                    <span class="<?= $invoice['paid_amount'] >= $invoice['total_amount'] ? 'text-success' : 'text-warning' ?>">
                                        ₹<?= number_format($invoice['paid_amount'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if ($invoice['status'] == 'paid') $statusClass = 'success';
                                    elseif ($invoice['status'] == 'sent') $statusClass = 'info';
                                    elseif ($invoice['status'] == 'overdue') $statusClass = 'danger';
                                    elseif ($invoice['status'] == 'cancelled') $statusClass = 'dark';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= ucfirst($invoice['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('invoice/show/' . $invoice['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('invoice/edit/' . $invoice['id']) ?>"
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('invoice/print/' . $invoice['id']) ?>"
                                           class="btn btn-sm btn-outline-info" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php if ($invoice['status'] != 'paid' && $invoice['status'] != 'cancelled'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="recordPayment(<?= $invoice['id'] ?>)" title="Record Payment">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="deleteInvoice(<?= $invoice['id'] ?>)" title="Delete">
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount</label>
                        <input type="number" class="form-control" id="payment_amount" name="payment_amount" 
                               step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteInvoice(invoiceId) {
    if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        window.location.href = '<?= base_url('invoice/delete/') ?>' + invoiceId;
    }
}

function recordPayment(invoiceId) {
    document.getElementById('paymentForm').action = '<?= base_url('invoice/record-payment/') ?>' + invoiceId;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}
</script>
<?= $this->endSection() ?>
