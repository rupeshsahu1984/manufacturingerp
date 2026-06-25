<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-file-invoice-dollar me-3"></i>
            Purchase Bills
        </h1>
        <p class="mb-0">Manage supplier bills</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Bills</li>
        </ol>
    </nav>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Bills</h6>
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
            <a href="/purchase-bill/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Bill
            </a>
        </div>
        <div>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <a href="/accounting/bills/export" class="btn btn-outline-success">
                <i class="fas fa-download me-2"></i>Export
            </a>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Bill List
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="billsTable">
                    <thead>
                        <tr>
                            <th>Bill #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($bills) && is_array($bills) && count($bills) > 0): ?>
                            <?php foreach ($bills as $bill): ?>
                                <tr>
                                    <td><?= esc($bill['bill_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($bill['supplier_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($bill['bill_date'] ?? 'N/A') ?></td>
                                    <td>₹<?= number_format($bill['total_amount'] ?? 0, 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($bill['status'] ?? 'pending') == 'paid' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($bill['status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/purchase-bill/show/<?= $bill['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="/purchase-bill/edit/<?= $bill['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No bills found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

