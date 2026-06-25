<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-calculator me-3"></i>Accounting Dashboard</h1>
    <div class="header-actions">
        <a href="<?= base_url('accounting/invoices') ?>" class="btn btn-primary">
            <i class="fas fa-file-invoice"></i> Manage Invoices
        </a>
        <a href="<?= base_url('accounting/bills') ?>" class="btn btn-outline-primary ms-2">
            <i class="fas fa-receipt"></i> Manage Bills
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

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Financial Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3>₹<?= number_format($total_revenue, 2) ?></h3>
                <p>Total Revenue</p>
                <small>All time</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-danger text-white">
            <div class="stat-card-body">
                <h3>₹<?= number_format($total_expenses, 2) ?></h3>
                <p>Total Expenses</p>
                <small>All time</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="stat-card-body">
                <h3>₹<?= number_format($outstanding_receivables, 2) ?></h3>
                <p>Outstanding Receivables</p>
                <small>To be collected</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="stat-card-body">
                <h3>₹<?= number_format($outstanding_payables, 2) ?></h3>
                <p>Outstanding Payables</p>
                <small>To be paid</small>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Performance -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Monthly Performance</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h4 class="text-success">₹<?= number_format($monthly_stats['revenue'], 2) ?></h4>
                            <small class="text-muted">Revenue</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h4 class="text-danger">₹<?= number_format($monthly_stats['expenses'], 2) ?></h4>
                            <small class="text-muted">Expenses</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h4 class="<?= $monthly_stats['profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                ₹<?= number_format($monthly_stats['profit'], 2) ?>
                            </h4>
                            <small class="text-muted">Net Profit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i>Cash Flow Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                            <h5 class="text-success">Cash In</h5>
                            <h4 class="text-success">₹<?= number_format($monthly_stats['revenue'], 2) ?></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                            <h5 class="text-danger">Cash Out</h5>
                            <h4 class="text-danger">₹<?= number_format($monthly_stats['expenses'], 2) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-file-invoice me-2"></i>Recent Invoices</h5>
                <a href="<?= base_url('accounting/invoices') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_invoices)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_invoices as $invoice): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($invoice['invoice_number']) ?></strong>
                                        </td>
                                        <td><?= esc(isset($invoice['customer_name']) ? $invoice['customer_name'] : 'N/A') ?></td>
                                        <td>₹<?= number_format($invoice['total_amount'], 2) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'secondary';
                                            if ($invoice['status'] == 'paid') $statusClass = 'success';
                                            elseif ($invoice['status'] == 'sent') $statusClass = 'info';
                                            elseif ($invoice['status'] == 'overdue') $statusClass = 'danger';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= ucfirst($invoice['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-file-invoice fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No recent invoices</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-receipt me-2"></i>Recent Bills</h5>
                <a href="<?= base_url('accounting/bills') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_bills)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Supplier</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bills as $bill): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($bill['bill_number']) ?></strong>
                                        </td>
                                        <td><?= esc(isset($bill['supplier_name']) ? $bill['supplier_name'] : 'N/A') ?></td>
                                        <td>₹<?= number_format($bill['total_amount'], 2) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'secondary';
                                            if ($bill['status'] == 'paid') $statusClass = 'success';
                                            elseif ($bill['status'] == 'pending') $statusClass = 'warning';
                                            elseif ($bill['status'] == 'overdue') $statusClass = 'danger';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= ucfirst($bill['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No recent bills</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Customer & Supplier Performance -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-users me-2"></i>Top Customers</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($customer_stats)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Invoices</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer_stats as $customer): ?>
                                    <tr>
                                        <td><?= esc($customer['customer_name']) ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= $customer['invoice_count'] ?></span>
                                        </td>
                                        <td>₹<?= number_format(isset($customer['total_amount']) ? $customer['total_amount'] : 0, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No customer data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-truck me-2"></i>Top Suppliers</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($supplier_stats)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Bills</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($supplier_stats as $supplier): ?>
                                    <tr>
                                        <td><?= esc($supplier['supplier_name']) ?></td>
                                        <td>
                                            <span class="badge bg-warning"><?= $supplier['bill_count'] ?></span>
                                        </td>
                                        <td>₹<?= number_format(isset($supplier['total_amount']) ? $supplier['total_amount'] : 0, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-truck fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No supplier data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('accounting/invoices') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-file-invoice"></i> Manage Invoices
                    </a>
                    <a href="<?= base_url('accounting/bills') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-receipt"></i> Manage Bills
                    </a>
                    <a href="<?= base_url('accounting/receivables') ?>" class="btn btn-outline-info">
                        <i class="fas fa-hand-holding-usd"></i> Accounts Receivable
                    </a>
                    <a href="<?= base_url('accounting/payables') ?>" class="btn btn-outline-warning">
                        <i class="fas fa-credit-card"></i> Accounts Payable
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('accounting/reports') ?>" class="btn btn-outline-success">
                        <i class="fas fa-file-alt"></i> Financial Reports
                    </a>
                    <a href="<?= base_url('accounting/analytics') ?>" class="btn btn-outline-info">
                        <i class="fas fa-chart-line"></i> Analytics Dashboard
                    </a>
                    <a href="<?= base_url('accounting/journal') ?>" class="btn btn-outline-dark">
                        <i class="fas fa-book"></i> General Journal
                    </a>
                    <a href="<?= base_url('accounting/ledger') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> General Ledger
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Health Indicators -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-heartbeat me-2"></i>Financial Health Indicators</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-percentage fa-2x text-primary mb-2"></i>
                            <h6>Profit Margin</h6>
                            <h4 class="text-primary">
                                <?= $total_revenue > 0 ? round((($total_revenue - $total_expenses) / $total_revenue) * 100, 1) : 0 ?>%
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h6>Days Receivable</h6>
                            <h4 class="text-warning">30 days</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                            <h6>Days Payable</h6>
                            <h4 class="text-info">45 days</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-balance-scale fa-2x text-success mb-2"></i>
                            <h6>Current Ratio</h6>
                            <h4 class="text-success">2.5:1</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
