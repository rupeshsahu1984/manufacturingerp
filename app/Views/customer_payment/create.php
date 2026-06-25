<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-money-check-alt me-3"></i>Record Customer Payment</h1>
    <div class="header-actions">
        <a href="<?= base_url('customer-payment') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
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

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle me-2"></i>Payment Details</h5>
            </div>
            <div class="card-body">
                <form id="paymentForm" action="<?= base_url('customer-payment/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Number</label>
                            <input type="text" class="form-control" name="payment_number" value="<?= esc($payment_number) ?>" readonly>
                            <div class="form-text text-muted">Auto-generated number</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-danger">* Payment Date</label>
                            <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-danger">* Customer</label>
                            <select class="form-select select2" id="customerSelect" name="customer_id" required>
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>"><?= esc($customer['customer_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Related Invoice (Optional)</label>
                            <select class="form-select" id="invoiceSelect" name="invoice_id">
                                <option value="">Select Invoice</option>
                                <?php if (!empty($invoices)): ?>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <option value="<?= $invoice['id'] ?>" data-customer-id="<?= $invoice['customer_id'] ?>" data-balance="<?= $invoice['total_amount'] - $invoice['paid_amount'] ?>">
                                            <?= esc($invoice['invoice_number']) ?> (Bal: ₹<?= number_format($invoice['total_amount'] - $invoice['paid_amount'], 2) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-danger">* Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="paymentAmount" name="payment_amount" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-danger">* Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="">-- Choose Method --</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reference Number</label>
                        <input type="text" class="form-control" name="reference_number" placeholder="Transaction ID, Cheque No, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Any additional information..."></textarea>
                    </div>

                    <div class="text-end mt-4">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('paymentDate').value = today;

    const customerSelect = document.getElementById('customerSelect');
    const invoiceSelect = document.getElementById('invoiceSelect');
    const paymentAmount = document.getElementById('paymentAmount');

    // Filter invoices by customer
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        if (!customerId) {
            resetInvoices();
            return;
        }

        // Fetch invoices via AJAX
        fetch(`<?= base_url('customer-payment/get-invoices') ?>?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateInvoices(data.invoices);
                }
            })
            .catch(error => console.error('Error fetching invoices:', error));
    });

    // Auto-fill amount from selected invoice
    invoiceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const balance = selectedOption.getAttribute('data-balance');
            if (balance) {
                paymentAmount.value = balance;
            }
        }
    });

    function populateInvoices(invoices) {
        invoiceSelect.innerHTML = '<option value="">Select Invoice</option>';
        invoices.forEach(inv => {
            const balance = inv.total_amount - inv.paid_amount;
            const option = document.createElement('option');
            option.value = inv.id;
            option.dataset.balance = balance;
            option.textContent = `${inv.invoice_number} (Bal: ₹${parseFloat(balance).toLocaleString(undefined, {minimumFractionDigits: 2})})`;
            invoiceSelect.appendChild(option);
        });
    }

    function resetInvoices() {
        invoiceSelect.innerHTML = '<option value="">Select Invoice</option>';
    }

    // Form submission safety
    document.getElementById('paymentForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
});
</script>

<style>
.content-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}
.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #edf2f7;
    padding: 1.25rem;
    border-radius: 10px 10px 0 0 !important;
}
.card-header h5 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}
.card-body {
    padding: 1.5rem;
}
.header {
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}
</style>

<?= $this->endSection() ?>
