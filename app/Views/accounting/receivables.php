<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-hand-holding-usd me-3"></i>
            Accounts Receivable
        </h1>
        <p class="mb-0">Track outstanding customer payments</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Receivables</li>
        </ol>
    </nav>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Outstanding</h6>
                    <h3 class="card-title text-primary">₹<?= isset($stats['total']) ? number_format($stats['total'], 2) : '0.00' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Current</h6>
                    <h3 class="card-title text-success">₹<?= isset($stats['current']) ? number_format($stats['current'], 2) : '0.00' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Overdue</h6>
                    <h3 class="card-title text-danger">₹<?= isset($stats['overdue']) ? number_format($stats['overdue'], 2) : '0.00' ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Receivables Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Outstanding Receivables
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="receivablesTable">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Days Overdue</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($receivables) && is_array($receivables) && count($receivables) > 0): ?>
                            <?php foreach ($receivables as $receivable): ?>
                                <?php 
                                $due_date = isset($receivable['due_date']) ? strtotime($receivable['due_date']) : time();
                                $today = time();
                                $days_overdue = max(0, floor(($today - $due_date) / (60 * 60 * 24)));
                                $is_overdue = $days_overdue > 0;
                                ?>
                                <tr class="<?= $is_overdue ? 'table-danger' : '' ?>">
                                    <td><?= esc($receivable['invoice_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($receivable['customer_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($receivable['invoice_date'] ?? 'N/A') ?></td>
                                    <td><?= esc($receivable['due_date'] ?? 'N/A') ?></td>
                                    <td>₹<?= number_format($receivable['total_amount'] ?? 0, 2) ?></td>
                                    <td><?= $is_overdue ? $days_overdue . ' days' : '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $is_overdue ? 'danger' : 'warning' ?>">
                                            <?= $is_overdue ? 'Overdue' : 'Current' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/invoice/show/<?= $receivable['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        <button class="btn btn-sm btn-outline-success" onclick="recordPayment(<?= $receivable['id'] ?>)">
                                            Record Payment
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No outstanding receivables</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function recordPayment(invoiceId) {
    // Redirect to payment recording page
    window.location.href = `/invoice/record-payment/${invoiceId}`;
}
</script>
<?= $this->endSection() ?>

