<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Sales & Distribution Dashboard</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sales & Distribution</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('sales/customers/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Customer
                </a>
            </div>
        </div>
    </div>

    <?php if (! empty($dashboard_error)): ?>
        <div class="alert alert-warning"><?= esc($dashboard_error) ?></div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($totalCustomers) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Total Customers</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-success">
                            <i class="fas fa-lightbulb"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($totalLeads) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Total Leads</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-warning">
                            <i class="fas fa-file-invoice"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($pendingQuotations) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Pending Quotations</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-warning" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-info">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($pendingOrders) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Pending Orders</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-info" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-secondary">
                            <i class="fas fa-truck"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($pendingDispatch) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Pending Dispatch</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-secondary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-danger">
                            <i class="fas fa-receipt"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($pendingInvoices) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Pending Invoices</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-dark">
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?= number_format($outstandingPayments) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Outstanding Payments</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-dark" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        <div class="dash-count">
                            <h3>₹<?= number_format(isset($monthlySales['total']) ? $monthlySales['total'] : 0) ?></h3>
                        </div>
                    </div>
                    <p class="text-muted">Monthly Sales</p>
                    <div class="progress progress-sm mt-3">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/customers/create') ?>" class="btn btn-outline-primary btn-block mb-3">
                                <i class="fas fa-user-plus"></i> Add Customer
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/leads/create') ?>" class="btn btn-outline-success btn-block mb-3">
                                <i class="fas fa-lightbulb"></i> Add Lead
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/quotations/create') ?>" class="btn btn-outline-warning btn-block mb-3">
                                <i class="fas fa-file-invoice"></i> Create Quotation
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/orders/create') ?>" class="btn btn-outline-info btn-block mb-3">
                                <i class="fas fa-shopping-cart"></i> Create Order
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/dispatch/create') ?>" class="btn btn-outline-secondary btn-block mb-3">
                                <i class="fas fa-truck"></i> Create Dispatch
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/invoices/create') ?>" class="btn btn-outline-danger btn-block mb-3">
                                <i class="fas fa-receipt"></i> Create Invoice
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/payments/create') ?>" class="btn btn-outline-dark btn-block mb-3">
                                <i class="fas fa-money-bill-wave"></i> Record Payment
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?= base_url('sales/reports') ?>" class="btn btn-outline-primary btn-block mb-3">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row">
        <!-- Monthly Sales Chart -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Monthly Sales Trend</h4>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Top Customers</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($topCustomers)): ?>
                        <?php foreach (array_slice($topCustomers, 0, 5) as $customer): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-primary">
                                        <?= strtoupper(substr($customer['customer_name'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= $customer['customer_name'] ?></h6>
                                    <small class="text-muted">₹<?= number_format(isset($customer['total_sales']) ? $customer['total_sales'] : 0) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No customer data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activities</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivities)): ?>
                        <div class="activity-feed">
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <?php if ($activity['type'] === 'order'): ?>
                                            <i class="fas fa-shopping-cart text-info"></i>
                                        <?php elseif ($activity['type'] === 'payment'): ?>
                                            <i class="fas fa-money-bill-wave text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-circle text-primary"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="activity-content">
                                        <p class="mb-0"><?= $activity['description'] ?></p>
                                        <small class="text-muted"><?= date('M d, Y H:i', strtotime($activity['time'])) ?></small>
                                    </div>
                                    <div class="activity-status">
                                        <span class="badge badge-<?= $activity['status'] === 'completed' ? 'success' : ($activity['status'] === 'pending' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst($activity['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No recent activities</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Navigation -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sales & Distribution Modules</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5>Customer Master</h5>
                                <p>Manage customers, leads, and CRM</p>
                                <a href="<?= base_url('sales/customers') ?>" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-success">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <h5>Quotations</h5>
                                <p>Create and manage price quotes</p>
                                <a href="<?= base_url('sales/quotations') ?>" class="btn btn-sm btn-success">View</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-info">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h5>Sales Orders</h5>
                                <p>Process customer orders</p>
                                <a href="<?= base_url('sales/orders') ?>" class="btn btn-sm btn-info">View</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-warning">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <h5>Dispatch</h5>
                                <p>Manage deliveries and shipments</p>
                                <a href="<?= base_url('sales/dispatch') ?>" class="btn btn-sm btn-warning">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-danger">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h5>Invoices</h5>
                                <p>Generate and track invoices</p>
                                <a href="<?= base_url('sales/invoices') ?>" class="btn btn-sm btn-danger">View</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-secondary">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <h5>Returns</h5>
                                <p>Handle sales returns</p>
                                <a href="<?= base_url('sales/returns') ?>" class="btn btn-sm btn-secondary">View</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-dark">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <h5>Payments</h5>
                                <p>Track customer payments</p>
                                <a href="<?= base_url('sales/payments') ?>" class="btn btn-sm btn-dark">View</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card">
                                <div class="module-icon bg-primary">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h5>Reports</h5>
                                <p>Sales analytics and reports</p>
                                <a href="<?= base_url('sales/reports') ?>" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.module-card {
    text-align: center;
    padding: 20px;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.module-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.module-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 24px;
}

.activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #e3e6f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fc;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.activity-content {
    flex-grow: 1;
}

.activity-status {
    margin-left: 15px;
}
</style>

<script>
// Monthly Sales Chart
const monthlySalesData = <?= json_encode(isset($monthlySales) ? $monthlySales : []) ?>;
const ctx = document.getElementById('monthlySalesChart').getContext('2d');

if (monthlySalesData && monthlySalesData.labels) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlySalesData.labels,
            datasets: [{
                label: 'Sales (₹)',
                data: monthlySalesData.data,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
</script>
<?= $this->endSection() ?>
