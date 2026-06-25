<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-money-check-alt me-3"></i>Payment Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('customer-payment') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="<?= base_url('customer-payment/edit/' . $payment['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <button onclick="window.print()" class="btn btn-info">
            <i class="fas fa-print"></i> Print
        </button>
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

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-receipt me-2"></i>Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Payment Number</h6>
                        <p class="fw-bold"><?= esc($payment['payment_number']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Payment Date</h6>
                        <p class="fw-bold"><?= date('d M Y', strtotime($payment['payment_date'])) ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Customer</h6>
                        <p class="fw-bold"><?= esc($payment['customer_name'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Invoice</h6>
                        <p class="fw-bold">
                            <?php if (!empty($payment['invoice_number'])): ?>
                                <a href="<?= base_url('invoice/show/' . $payment['invoice_id']) ?>" class="text-decoration-none">
                                    <?= esc($payment['invoice_number']) ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Payment Amount</h6>
                        <p class="fw-bold text-success fs-5">₹<?= number_format($payment['payment_amount'], 2) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Payment Method</h6>
                        <p class="fw-bold">
                            <span class="badge bg-primary"><?= ucfirst(esc($payment['payment_method'])) ?></span>
                        </p>
                    </div>
                </div>

                <?php if (!empty($payment['reference_number'])): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted">Reference Number</h6>
                        <p class="fw-bold"><?= esc($payment['reference_number']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($payment['notes'])): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted">Notes</h6>
                        <p class="fw-bold"><?= esc($payment['notes']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Created By</h6>
                        <p class="fw-bold"><?= esc($payment['created_by_name'] ?? 'System') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Created At</h6>
                        <p class="fw-bold"><?= date('d M Y H:i', strtotime($payment['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Actions -->
        <div class="content-card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-cogs me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="<?= base_url('customer-payment/edit/' . $payment['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Payment
                    </a>
                    <a href="<?= base_url('customer-payment/print/' . $payment['id']) ?>" class="btn btn-info" target="_blank">
                        <i class="fas fa-print"></i> Print Receipt
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment?</p>
                <p class="text-danger">
                    <strong>Payment Number:</strong> <?= esc($payment['payment_number']) ?><br>
                    <strong>Amount:</strong> ₹<?= number_format($payment['payment_amount'], 2) ?><br>
                    <strong>Customer:</strong> <?= esc($payment['customer_name'] ?? 'N/A') ?>
                </p>
                <p class="text-muted">This action cannot be undone and will update the corresponding invoice if applicable.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?= base_url('customer-payment/delete/' . $payment['id']) ?>" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
@media print {
    .header-actions, .btn, .modal {
        display: none !important;
    }
    .content-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
<?= $this->endSection() ?>
