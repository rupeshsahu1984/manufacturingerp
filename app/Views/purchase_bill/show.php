<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('purchase-bill') ?>">Purchase Bills</a></li>
                        <li class="breadcrumb-item active">View Purchase Bill</li>
                    </ol>
                </div>
                <h4 class="page-title">View Purchase Bill</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Purchase Bill Details
                    </h5>
                    <div>
                        <a href="<?= base_url('purchase-bill') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                        <?php if (isset($bill['status']) && $bill['status'] === 'draft'): ?>
                        <a href="<?= base_url('purchase-bill/edit/' . $bill['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <?php endif; ?>
                        <a href="<?= base_url('purchase-bill/print/' . $bill['id']) ?>" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-print me-1"></i>Print
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bill Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Bill Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 40%;">Bill Number:</td>
                                    <td><?= esc($bill['bill_number']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Bill Date:</td>
                                    <td><?= date('d/m/Y', strtotime($bill['bill_date'])) ?></td>
                                </tr>
                                <?php if (!empty($bill['due_date'])): ?>
                                <tr>
                                    <td class="fw-bold">Due Date:</td>
                                    <td><?= date('d/m/Y', strtotime($bill['due_date'])) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($bill['invoice_number'])): ?>
                                <tr>
                                    <td class="fw-bold">Supplier Memo:</td>
                                    <td><?= esc($bill['invoice_number']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <?php
                                        $statusBadges = [
                                            'draft' => 'secondary',
                                            'received' => 'info',
                                            'paid' => 'success',
                                            'overdue' => 'danger',
                                            'cancelled' => 'dark'
                                        ];
                                        $badgeColor = $statusBadges[$bill['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badgeColor ?>"><?= ucfirst($bill['status']) ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Supplier Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 40%;">Supplier Name:</td>
                                    <td><?= esc($bill['supplier_name']) ?></td>
                                </tr>
                                <?php if (!empty($bill['supplier_code'])): ?>
                                <tr>
                                    <td class="fw-bold">Supplier Code:</td>
                                    <td><?= esc($bill['supplier_code']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($bill['contact_person'])): ?>
                                <tr>
                                    <td class="fw-bold">Contact Person:</td>
                                    <td><?= esc($bill['contact_person']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($bill['phone'])): ?>
                                <tr>
                                    <td class="fw-bold">Phone:</td>
                                    <td><?= esc($bill['phone']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($bill['email'])): ?>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td><?= esc($bill['email']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($bill['address'])): ?>
                                <tr>
                                    <td class="fw-bold">Address:</td>
                                    <td><?= esc($bill['address']) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Bill Items -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Bill Items</h6>
                            <?php if (empty($bill['items'])): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>No items found in this purchase bill.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>Product Code</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>CGST %</th>
                                                <th>SGST %</th>
                                                <th>IGST %</th>
                                                <th>GST Amount</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $itemCount = 1;
                                            foreach ($bill['items'] as $item): 
                                            ?>
                                                <tr>
                                                    <td><?= $itemCount++ ?></td>
                                                    <td>
                                                        <strong><?= esc($item['product_name']) ?></strong>
                                                    </td>
                                                    <td><?= esc($item['product_code']) ?></td>
                                                    <td><?= esc($item['unit'] ?? 'PCS') ?></td>
                                                    <td><?= number_format($item['quantity'], 2) ?></td>
                                                    <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                                                    <td><?= number_format($item['cgst_rate'] ?? 0, 2) ?>%</td>
                                                    <td><?= number_format($item['sgst_rate'] ?? 0, 2) ?>%</td>
                                                    <td><?= number_format($item['igst_rate'] ?? 0, 2) ?>%</td>
                                                    <td>₹<?= number_format($item['gst_amount'] ?? 0, 2) ?></td>
                                                    <td><strong>₹<?= number_format($item['total_amount'], 2) ?></strong></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bill Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Bill Summary</h6>
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold">Subtotal:</td>
                                            <td class="text-end">₹<?= number_format($bill['subtotal'] ?? 0, 2) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">GST Amount:</td>
                                            <td class="text-end">₹<?= number_format($bill['gst_amount'] ?? 0, 2) ?></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="fw-bold fs-5">Total Amount:</td>
                                            <td class="text-end fs-5 text-primary fw-bold">₹<?= number_format($bill['total_amount'], 2) ?></td>
                                        </tr>
                                        <?php if (isset($bill['paid_amount']) && $bill['paid_amount'] > 0): ?>
                                        <tr>
                                            <td class="fw-bold">Paid Amount:</td>
                                            <td class="text-end text-success">₹<?= number_format($bill['paid_amount'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Outstanding Amount:</td>
                                            <td class="text-end text-danger">₹<?= number_format($bill['total_amount'] - $bill['paid_amount'], 2) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

