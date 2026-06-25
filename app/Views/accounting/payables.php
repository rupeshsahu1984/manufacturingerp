<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-money-check-alt me-3"></i>
            Accounts Payable
        </h1>
        <p class="mb-0">Track outstanding supplier payments</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Payables</li>
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

    <!-- Payables Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Outstanding Payables
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="payablesTable">
                    <thead>
                        <tr>
                            <th>Bill #</th>
                            <th>Supplier</th>
                            <th>Bill Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Days Overdue</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($payables) && is_array($payables) && count($payables) > 0): ?>
                            <?php foreach ($payables as $payable): ?>
                                <?php 
                                $due_date = isset($payable['due_date']) ? strtotime($payable['due_date']) : time();
                                $today = time();
                                $days_overdue = max(0, floor(($today - $due_date) / (60 * 60 * 24)));
                                $is_overdue = $days_overdue > 0;
                                ?>
                                <tr class="<?= $is_overdue ? 'table-danger' : '' ?>">
                                    <td><?= esc($payable['bill_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($payable['supplier_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($payable['bill_date'] ?? 'N/A') ?></td>
                                    <td><?= esc($payable['due_date'] ?? 'N/A') ?></td>
                                    <td>₹<?= number_format($payable['total_amount'] ?? 0, 2) ?></td>
                                    <td><?= $is_overdue ? $days_overdue . ' days' : '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $is_overdue ? 'danger' : 'warning' ?>">
                                            <?= $is_overdue ? 'Overdue' : 'Current' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/purchase-bill/show/<?= $payable['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        <button class="btn btn-sm btn-outline-success" onclick="recordPayment(<?= $payable['id'] ?>)">
                                            Record Payment
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No outstanding payables</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function recordPayment(billId) {
    // Redirect to payment recording page
    window.location.href = `/vendor-payment/create?bill_id=${billId}`;
}
</script>
<?= $this->endSection() ?>

