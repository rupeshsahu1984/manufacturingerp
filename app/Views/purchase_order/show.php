<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Purchase Order Details</h1>
        <p class="text-muted mb-0">View purchase order information</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-order') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Purchase Orders
        </a>
        <button type="button" class="btn btn-primary" onclick="printPurchaseOrder()">
            <i class="fas fa-print me-2"></i>Print
        </button>
    </div>
</div>

<!-- Purchase Order Information -->
<div class="row">
    <div class="col-md-8">
        <!-- Basic Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="h5">
                    <i class="fas fa-info-circle me-2"></i>Purchase Order Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">PO Number</label>
                            <p class="form-control-plaintext"><?= esc($purchase_order['po_number']) ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-bold">Order Date</label>
                            <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($purchase_order['order_date'])) ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-bold">Expected Delivery</label>
                            <p class="form-control-plaintext">
                                <?= date('d/m/Y', strtotime($purchase_order['expected_date'])) ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary"><?= ucfirst($purchase_order['status']) ?></span>
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-bold">Supplier</label>
                            <p class="form-control-plaintext"><?= esc($purchase_order['supplier_name']) ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-bold">Total Amount</label>
                            <p class="form-control-plaintext">₹<?= number_format($purchase_order['total_amount'], 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="h5">
                    <i class="fas fa-list me-2"></i>Order Items
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($purchase_order['items'])): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No items found in this purchase order.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchase_order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($item['product_name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($item['product_code']) ?></small>
                                        </td>
                                        <td><?= number_format($item['quantity']) ?></td>
                                        <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                                        <td><strong>₹<?= number_format($item['total_amount'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Order Summary Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="h5">
                    <i class="fas fa-calculator me-2"></i>Order Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="summary-item">
                    <span>Subtotal:</span>
                    <span>₹<?= number_format($purchase_order['subtotal'], 2) ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (18%):</span>
                    <span>₹<?= number_format($purchase_order['tax_amount'], 2) ?></span>
                </div>
                <div class="summary-item total">
                    <span>Total:</span>
                    <span>₹<?= number_format($purchase_order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="h5">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="printPurchaseOrder()">
                        <i class="fas fa-print me-2"></i>Print PO
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function printPurchaseOrder() {
    window.open('<?= base_url('purchase-order/print/' . $purchase_order['id']) ?>', '_blank');
}

function downloadPDF() {
    window.open('<?= base_url('purchase-order/pdf/' . $purchase_order['id']) ?>', '_blank');
}
</script>

<style>
.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #dee2e6;
}

.summary-item.total {
    font-weight: bold;
    font-size: 1.1em;
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
</style>
<?= $this->endSection() ?>
