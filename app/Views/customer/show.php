<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Customer Details</h1>
        <p class="text-muted mb-0">View customer information</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('customer') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Customers
        </a>
        <a href="<?= base_url('customer/edit/' . $customer['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Customer
        </a>
    </div>
</div>

<!-- Customer Information Card -->
<div class="form-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-info-circle me-2"></i>Customer Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Basic Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Customer Name</label>
                    <p class="form-control-plaintext"><?= esc($customer['customer_name']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Customer Code</label>
                    <p class="form-control-plaintext"><?= esc($customer['customer_code']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Contact Person</label>
                    <p class="form-control-plaintext"><?= esc($customer['contact_person']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Phone Number</label>
                    <p class="form-control-plaintext"><?= esc($customer['phone']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Email Address</label>
                    <p class="form-control-plaintext">
                        <?php if ($customer['email']): ?>
                            <a href="mailto:<?= esc($customer['email']) ?>"><?= esc($customer['email']) ?></a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Website</label>
                    <p class="form-control-plaintext">
                        <?php if ($customer['website']): ?>
                            <a href="<?= esc($customer['website']) ?>" target="_blank"><?= esc($customer['website']) ?></a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Address Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Address Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Address</label>
                    <p class="form-control-plaintext"><?= nl2br(esc($customer['address'])) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">City</label>
                    <p class="form-control-plaintext"><?= esc($customer['city']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">State</label>
                    <p class="form-control-plaintext"><?= esc($customer['state']) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Pincode</label>
                    <p class="form-control-plaintext"><?= esc($customer['pincode']) ?></p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Business Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Business Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">GST Number</label>
                    <p class="form-control-plaintext"><?= esc($customer['gst_number'] ?: 'Not provided') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">PAN Number</label>
                    <p class="form-control-plaintext"><?= esc($customer['pan_number'] ?: 'Not provided') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Sales Zone</label>
                    <p class="form-control-plaintext"><?= esc($customer['sales_zone'] ?: 'Not specified') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Sales Region</label>
                    <p class="form-control-plaintext"><?= esc($customer['sales_region'] ?: 'Not specified') ?></p>
                </div>
            </div>

            <!-- Sales & Credit Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Sales & Credit Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Credit Limit</label>
                    <p class="form-control-plaintext">
                        <?php if ($customer['credit_limit']): ?>
                            ₹<?= number_format($customer['credit_limit'], 2) ?>
                        <?php else: ?>
                            Not specified
                        <?php endif; ?>
                    </p>
                </div>

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
                            '60_days' => '60 Days',
                            '90_days' => '90 Days'
                        ];
                        echo esc($paymentTerms[$customer['payment_terms']] ?? ($customer['payment_terms'] ?: 'Not specified'));
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Return Policy</label>
                    <p class="form-control-plaintext">
                        <?php
                        $returnPolicies = [
                            'no_returns' => 'No Returns',
                            '7_days' => '7 Days',
                            '15_days' => '15 Days',
                            '30_days' => '30 Days',
                            'custom' => 'Custom'
                        ];
                        echo esc($returnPolicies[$customer['return_policy']] ?? ($customer['return_policy'] ?: 'Not specified'));
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Debit Note Configuration</label>
                    <p class="form-control-plaintext">
                        <?php
                        $debitNoteConfigs = [
                            'allowed' => 'Allowed',
                            'restricted' => 'Restricted',
                            'not_allowed' => 'Not Allowed'
                        ];
                        echo esc($debitNoteConfigs[$customer['debit_note_config']] ?? ($customer['debit_note_config'] ?: 'Not specified'));
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Status</label>
                    <p class="form-control-plaintext">
                        <span class="badge <?= $customer['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst(esc($customer['status'])) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Additional Information -->
            <div class="col-md-6">
                <h6 class="mb-3 text-primary">Additional Information</h6>
                
                <div class="form-group">
                    <label class="form-label fw-bold">Created Date</label>
                    <p class="form-control-plaintext"><?= date('F j, Y', strtotime($customer['created_at'])) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext"><?= date('F j, Y', strtotime($customer['updated_at'])) ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Created By</label>
                    <p class="form-control-plaintext"><?= esc(isset($customer['created_by']) ? $customer['created_by'] : 'System') ?></p>
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
                    <h4 class="text-info"><?= isset($performance['payment_ontime']) ? $performance['payment_ontime'] : 0 ?>%</h4>
                    <p class="text-muted">On-Time Payment</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h4 class="text-warning"><?= isset($performance['credit_utilization']) ? $performance['credit_utilization'] : 0 ?>%</h4>
                    <p class="text-muted">Credit Utilization</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
