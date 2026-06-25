<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-file-invoice me-3"></i>
            Invoice Management
        </h1>
        <p class="mb-0">Manage customer invoices</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Invoices</li>
        </ol>
    </nav>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Invoices</h6>
                    <h3 class="card-title"><?= isset($stats['total']) ? $stats['total'] : 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Amount</h6>
                    <h3 class="card-title">₹<?= isset($stats['amount']) ? number_format($stats['amount'], 2) : '0.00' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Paid</h6>
                    <h3 class="card-title"><?= isset($stats['paid']) ? $stats['paid'] : 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Pending</h6>
                    <h3 class="card-title"><?= isset($stats['pending']) ? $stats['pending'] : 0 ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="/invoice/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Invoice
            </a>
        </div>
        <div>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <a href="/accounting/invoices/export" class="btn btn-outline-success">
                <i class="fas fa-download me-2"></i>Export
            </a>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Invoice List
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="invoicesTable">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($invoices) && is_array($invoices) && count($invoices) > 0): ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td><?= esc($invoice['invoice_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($invoice['customer_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($invoice['invoice_date'] ?? 'N/A') ?></td>
                                    <td>₹<?= number_format($invoice['total_amount'] ?? 0, 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($invoice['status'] ?? 'pending') == 'paid' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($invoice['status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/invoice/show/<?= $invoice['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="/invoice/edit/<?= $invoice['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No invoices found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

