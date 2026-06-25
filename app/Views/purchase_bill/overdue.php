<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Overdue Purchase Bills</h1>
        <p class="text-muted mb-0">Bills that are past their due date</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-bill') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Bills
        </a>
        <a href="<?= base_url('purchase-bill/export') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-download me-2"></i>Export
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-value"><?= count($bills ?? []) ?></div>
        <div class="stat-label">Overdue Bills</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div class="stat-value">₹<?= number_format(array_sum(array_map(function($bill) { return $bill['total_amount'] - $bill['paid_amount']; }, $bills ?? []))) ?></div>
        <div class="stat-label">Total Outstanding</div>
    </div>
</div>

<!-- Overdue Bills Table -->
<div class="content-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
            Overdue Bills List
        </h5>
        <div class="d-flex gap-2">
            <span class="text-danger">
                <strong>Total Outstanding: ₹<?= number_format(array_sum(array_map(function($bill) { return $bill['total_amount'] - $bill['paid_amount']; }, $bills ?? []))) ?></strong>
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr data-selectable>
                    <th>Bill Number</th>
                    <th>Supplier</th>
                    <th>Bill Date</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                    <th>Invoice Number</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bills)): ?>
                <tr data-selectable>
                    <td colspan="11" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <h5>No Overdue Bills</h5>
                            <p>All bills are up to date!</p>
                            <a href="<?= base_url('purchase-bill') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to All Bills
                            </a>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($bills as $bill): ?>
                <?php 
                    $outstanding = $bill['total_amount'] - $bill['paid_amount'];
                    $dueDate = $bill['due_date'] ? strtotime($bill['due_date']) : null;
                    $daysOverdue = $dueDate ? max(0, floor((time() - $dueDate) / 86400)) : 0;
                ?>
                <tr data-selectable>
                    <td>
                        <strong><?= esc($bill['bill_number']) ?></strong>
                    </td>
                    <td>
                        <div>
                            <strong><?= esc($bill['supplier_name']) ?></strong>
                            <br><small class="text-muted"><?= esc($bill['supplier_code'] ?? '') ?></small>
                        </div>
                    </td>
                    <td>
                        <?= $bill['bill_date'] ? date('d M Y', strtotime($bill['bill_date'])) : '-' ?>
                    </td>
                    <td>
                        <?php if ($bill['due_date']): ?>
                            <span class="text-danger">
                                <strong><?= date('d M Y', strtotime($bill['due_date'])) ?></strong>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($daysOverdue > 0): ?>
                            <span class="badge bg-danger">
                                <?= $daysOverdue ?> day<?= $daysOverdue > 1 ? 's' : '' ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning">Due Today</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= esc($bill['invoice_number'] ?: '-') ?>
                    </td>
                    <td>
                        <strong>₹<?= number_format($bill['total_amount'], 2) ?></strong>
                    </td>
                    <td>
                        ₹<?= number_format($bill['paid_amount'], 2) ?>
                    </td>
                    <td>
                        <span class="text-danger">
                            <strong>₹<?= number_format($outstanding, 2) ?></strong>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?= esc($bill['status']) ?>">
                            <?= ucfirst(esc($bill['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?= base_url('purchase-bill/show/' . $bill['id']) ?>" 
                               class="btn btn-view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('purchase-bill/edit/' . $bill['id']) ?>" 
                               class="btn btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-success" 
                                    onclick="recordPayment(<?= $bill['id'] ?>, <?= $outstanding ?>)" 
                                    title="Record Payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                            <a href="<?= base_url('purchase-bill/print/' . $bill['id']) ?>" 
                               class="btn btn-outline-info" title="Print">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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
            <div class="modal-body">
                <form data-validate id="paymentForm">
                    <input type="hidden" id="paymentBillId">
                    <div class="mb-3">
                        <label class="form-label">Payment Amount</label>
                        <input type="number" id="paymentAmount" class="form-control" step="0.01" required>
                        <div class="form-text">Enter the payment amount</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentBillId = null;

function recordPayment(billId, maxAmount) {
    currentBillId = billId;
    document.getElementById('paymentBillId').value = billId;
    document.getElementById('paymentAmount').value = maxAmount;
    document.getElementById('paymentAmount').max = maxAmount;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

function submitPayment() {
    const billId = document.getElementById('paymentBillId').value;
    const amount = parseFloat(document.getElementById('paymentAmount').value);
    
    if (!amount || amount <= 0) {
        alert('Please enter a valid payment amount');
        return;
    }
    
    fetch('<?= base_url('purchase-bill/record-payment') ?>/' + billId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ amount: amount })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            location.reload();
        } else {
            alert('Failed to record payment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to record payment');
    });
}
</script>

<?= $this->endSection() ?>


