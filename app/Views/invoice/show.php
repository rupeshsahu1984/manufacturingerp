<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-file-invoice me-3"></i>Invoice Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('invoice/print/' . $invoice['id']) ?>" class="btn btn-info" target="_blank">
            <i class="fas fa-print"></i> Print
        </a>
        <a href="<?= base_url('invoice/edit/' . $invoice['id']) ?>" class="btn btn-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="<?= base_url('invoice') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="content-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Invoice Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Invoice Number</th>
                                <td>: <strong><?= esc($invoice['invoice_number']) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Invoice Date</th>
                                <td>: <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></td>
                            </tr>
                            <tr>
                                <th>Due Date</th>
                                <td>: <?= $invoice['due_date'] ? date('d M Y', strtotime($invoice['due_date'])) : 'N/A' ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>: 
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
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Customer</th>
                                <td>: <?= esc($invoice['customer_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>: <?= esc($invoice['phone'] ?: 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>: <?= esc($invoice['email'] ?: 'N/A') ?></td>
                            </tr>
                            <?php if (isset($invoice['sales_order_id']) && $invoice['sales_order_id']): ?>
                            <tr>
                                <th>Sales Order</th>
                                <td>: <a href="<?= base_url('sales-order/show/' . $invoice['sales_order_id']) ?>">Link</a></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-list me-2"></i>Invoice Items</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">GST Rate</th>
                            <th class="text-end">GST Amount</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoice['items'] as $item): ?>
                        <tr>
                            <td class="ps-3">
                                <strong><?= esc($item['product_name']) ?></strong><br>
                                <small class="text-muted"><?= esc($item['product_code']) ?></small>
                            </td>
                            <td class="text-center"><?= number_format($item['quantity'], 2) ?></td>
                            <td class="text-end">₹<?= number_format($item['unit_price'], 2) ?></td>
                            <td class="text-end"><?= number_format($item['gst_rate'], 2) ?>%</td>
                            <td class="text-end">₹<?= number_format($item['gst_amount'], 2) ?></td>
                            <td class="text-end pe-3">₹<?= number_format($item['total_amount'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-calculator me-2"></i>Payment Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">₹<?= number_format($invoice['subtotal'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>GST Amount</td>
                        <td class="text-end">₹<?= number_format($invoice['gst_amount'], 2) ?></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Total Amount</strong></td>
                        <td class="text-end"><strong>₹<?= number_format($invoice['total_amount'], 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-success">Paid Amount</td>
                        <td class="text-end text-success">₹<?= number_format($invoice['paid_amount'], 2) ?></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Balance Due</strong></td>
                        <td class="text-end"><strong>₹<?= number_format($invoice['total_amount'] - $invoice['paid_amount'], 2) ?></strong></td>
                    </tr>
                </table>
                
                <?php if ($invoice['status'] != 'paid' && $invoice['status'] != 'cancelled'): ?>
                <div class="d-grid mt-4">
                    <button class="btn btn-success" onclick="recordPayment(<?= $invoice['id'] ?>)">
                        <i class="fas fa-money-bill-wave me-2"></i>Record Payment
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Copy script logic from index if needed -->
<script>
function recordPayment(invoiceId) {
    // This assumes the recordPayment modal and logic are available or can be triggered
    alert('Please use the Invoices list page to record payment for now.');
}
</script>
<?= $this->endSection() ?>
