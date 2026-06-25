<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sales-order') ?>">Sales Orders</a></li>
                        <li class="breadcrumb-item active">View Sales Order</li>
                    </ol>
                </div>
                <h4 class="page-title">Sales Order Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="header-title">Sales Order #<?= esc($sales_order['so_number']) ?></h4>
                        <div>
                            <a href="<?= base_url('sales-order/edit/' . $sales_order['id']) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url('sales-order/print/' . $sales_order['id']) ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-print"></i> Print
                            </a>
                            <a href="<?= base_url('sales-order') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Order Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice No:</strong></td>
                                    <td><?= esc(isset($sales_order['invoice_no']) ? $sales_order['invoice_no'] : $sales_order['so_number']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Order Date:</strong></td>
                                    <td><?= date('d-m-Y', strtotime($sales_order['order_date'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusColor($sales_order['status']) ?>">
                                            <?= ucfirst(esc($sales_order['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td><?= esc(isset($sales_order['description']) ? $sales_order['description'] : 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Customer Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Customer Name:</strong></td>
                                    <td><?= esc($sales_order['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?= esc(isset($sales_order['customer_address']) ? $sales_order['customer_address'] : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile Number:</strong></td>
                                    <td><?= esc(isset($sales_order['customer_mobile']) ? $sales_order['customer_mobile'] : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>GSTN:</strong></td>
                                    <td><?= esc(isset($sales_order['customer_gstn']) ? $sales_order['customer_gstn'] : 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Discount</th>
                                            <th>Billed Qty</th>
                                            <th>Total</th>
                                            <th>CGST</th>
                                            <th>SGST</th>
                                            <th>IGST</th>
                                            <th>Tax</th>
                                            <th>Ship Qty</th>
                                            <th>Avail Stk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?= esc($item['product_name']) ?></td>
                                            <td><?= esc(isset($item['unit']) ? $item['unit'] : 'Un') ?></td>
                                            <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                                            <td><?= number_format(isset($item['discount']) ? $item['discount'] : 0, 2) ?>%</td>
                                            <td><?= esc($item['quantity']) ?></td>
                                            <td>₹<?= number_format(isset($item['total_amount']) ? $item['total_amount'] : ($item['quantity'] * $item['unit_price']), 2) ?></td>
                                            <td><?= number_format(isset($item['cgst']) ? $item['cgst'] : 0, 2) ?>%</td>
                                            <td><?= number_format(isset($item['sgst']) ? $item['sgst'] : 0, 2) ?>%</td>
                                            <td><?= number_format(isset($item['igst']) ? $item['igst'] : 0, 2) ?>%</td>
                                            <td>₹<?= number_format(isset($item['tax_amount']) ? $item['tax_amount'] : 0, 2) ?></td>
                                            <td><?= esc(isset($item['ship_qty']) ? $item['ship_qty'] : $item['quantity']) ?></td>
                                            <td><?= esc(isset($item['available_stock']) ? $item['available_stock'] : 'N/A') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Transport Details -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Transport Amount</h6>
                                    <h4 class="text-primary">₹<?= number_format(isset($sales_order['transport_amount']) ? $sales_order['transport_amount'] : 0, 2) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Transport Tax</h6>
                                    <h4 class="text-info">₹<?= number_format(isset($sales_order['transport_tax']) ? $sales_order['transport_tax'] : 0, 2) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Description</h6>
                                    <p class="mb-0"><?= esc(isset($sales_order['description']) ? $sales_order['description'] : 'N/A') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Order Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6">Subtotal:</div>
                                        <div class="col-6 text-end">₹<?= number_format($order_summary['subtotal'], 2) ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">Discount Total:</div>
                                        <div class="col-6 text-end">₹<?= number_format($order_summary['discount_total'], 2) ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">CGST Total:</div>
                                        <div class="col-6 text-end">₹<?= number_format($order_summary['cgst_total'], 2) ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">SGST Total:</div>
                                        <div class="col-6 text-end">₹<?= number_format($order_summary['sgst_total'], 2) ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">IGST Total:</div>
                                        <div class="col-6 text-end">₹<?= number_format($order_summary['igst_total'], 2) ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">Transport Amount:</div>
                                        <div class="col-6 text-end">₹<?= number_format(isset($sales_order['transport_amount']) ? $sales_order['transport_amount'] : 0, 2) ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">Transport Tax:</div>
                                        <div class="col-6 text-end">₹<?= number_format(isset($sales_order['transport_tax']) ? $sales_order['transport_tax'] : 0, 2) ?></div>
                                    </div>
                                    <hr>
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>Final Total:</strong></div>
                                        <div class="col-6 text-end"><strong>₹<?= number_format($order_summary['final_total'], 2) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'draft': return 'secondary';
        case 'confirmed': return 'info';
        case 'processing': return 'warning';
        case 'ready': return 'primary';
        case 'dispatched': return 'success';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>

<?= $this->endSection() ?>
