<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Supplier Details</h1>
        <p class="text-muted mb-0">View supplier information</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('supplier') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
        </a>
        <a href="<?= base_url('supplier/edit/' . $supplier['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Supplier
        </a>
    </div>
</div>

<!-- Supplier Information Card -->
<div class="form-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-info-circle me-2"></i>Supplier Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Basic Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Supplier Name</label>
                    <p class="form-control-plaintext"><?= esc($supplier['supplier_name']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Supplier Code</label>
                    <p class="form-control-plaintext"><?= esc($supplier['supplier_code']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Category</label>
                    <p class="form-control-plaintext">
                        <?php
                        $categories = [
                            'raw_material' => 'Raw Material',
                            'packaging' => 'Packaging',
                            'service' => 'Service'
                        ];
                        echo esc($categories[$supplier['supplier_category']] ?? $supplier['supplier_category']);
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">GST Number</label>
                    <p class="form-control-plaintext"><?= esc($supplier['gst_number'] ?: 'Not provided') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">PAN Number</label>
                    <p class="form-control-plaintext"><?= esc($supplier['pan_number'] ?: 'Not provided') ?></p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Contact Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Contact Person</label>
                    <p class="form-control-plaintext"><?= esc($supplier['contact_person']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Phone Number</label>
                    <p class="form-control-plaintext"><?= esc($supplier['phone']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Email Address</label>
                    <p class="form-control-plaintext">
                        <?php if ($supplier['email']): ?>
                            <a href="mailto:<?= esc($supplier['email']) ?>"><?= esc($supplier['email']) ?></a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Website</label>
                    <p class="form-control-plaintext">
                        <?php if ($supplier['website']): ?>
                            <a href="<?= esc($supplier['website']) ?>" target="_blank"><?= esc($supplier['website']) ?></a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Address Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Address Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Address</label>
                    <p class="form-control-plaintext"><?= nl2br(esc($supplier['address'])) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">City</label>
                    <p class="form-control-plaintext"><?= esc($supplier['city']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">State</label>
                    <p class="form-control-plaintext"><?= esc($supplier['state']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Pincode</label>
                    <p class="form-control-plaintext"><?= esc($supplier['pincode']) ?></p>
                </div>
            </div>

            <!-- Bank Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Bank Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Bank Name</label>
                    <p class="form-control-plaintext"><?= esc($supplier['bank_name'] ?: 'Not provided') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Account Number</label>
                    <p class="form-control-plaintext"><?= esc($supplier['bank_account'] ?: 'Not provided') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">IFSC Code</label>
                    <p class="form-control-plaintext"><?= esc($supplier['bank_ifsc'] ?: 'Not provided') ?></p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Business Terms -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Business Terms</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Payment Terms</label>
                    <p class="form-control-plaintext">
                        <?php
                        $paymentTerms = [
                            'immediate' => 'Immediate',
                            '7_days' => '7 Days',
                            '15_days' => '15 Days',
                            '30_days' => '30 Days',
                            '45_days' => '45 Days',
                            '60_days' => '60 Days'
                        ];
                        echo esc($paymentTerms[$supplier['payment_terms']] ?? ($supplier['payment_terms'] ?: 'Not specified'));
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Credit Limit</label>
                    <p class="form-control-plaintext">
                        <?php if ($supplier['credit_limit']): ?>
                            ₹<?= number_format($supplier['credit_limit'], 2) ?>
                        <?php else: ?>
                            Not specified
                        <?php endif; ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Credit Terms</label>
                    <p class="form-control-plaintext"><?= esc($supplier['credit_terms'] ?: 'Not specified') ?></p>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Additional Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Return Policy</label>
                    <p class="form-control-plaintext">
                        <?php if ($supplier['return_policy']): ?>
                            <?= nl2br(esc($supplier['return_policy'])) ?>
                        <?php else: ?>
                            Not specified
                        <?php endif; ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Status</label>
                    <p class="form-control-plaintext">
                        <span class="badge <?= $supplier['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst(esc($supplier['status'])) ?>
                        </span>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Created Date</label>
                    <p class="form-control-plaintext"><?= date('F j, Y', strtotime($supplier['created_at'])) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext"><?= date('F j, Y', strtotime($supplier['updated_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($performance) && $performance): ?>
<!-- Performance Card -->
<div class="form-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-chart-line me-2"></i>Performance Overview
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="text-center">
                    <h4 class="text-primary"><?= isset($performance['total_orders']) ? $performance['total_orders'] : 0 ?></h4>
                    <p class="text-muted">Total Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h4 class="text-success">₹<?= number_format(isset($performance['total_amount']) ? $performance['total_amount'] : 0, 2) ?></h4>
                    <p class="text-muted">Total Amount</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h4 class="text-info"><?= isset($performance['on_time_delivery']) ? $performance['on_time_delivery'] : 0 ?>%</h4>
                    <p class="text-muted">On-Time Delivery</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h4 class="text-warning"><?= isset($performance['quality_rating']) ? $performance['quality_rating'] : 0 ?>/5</h4>
                    <p class="text-muted">Quality Rating</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
