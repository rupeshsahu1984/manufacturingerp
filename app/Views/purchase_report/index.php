<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?><?= isset($title) ? $title : 'Purchase Reports' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title"><?= isset($title) ? $title : 'Purchase Reports & Analytics' ?></h4>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= isset($summary_stats['total_requisitions']) ? $summary_stats['total_requisitions'] : 0 ?></h4>
                    <small>Total PRs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= isset($summary_stats['pending_requisitions']) ? $summary_stats['pending_requisitions'] : 0 ?></h4>
                    <small>Pending PRs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= isset($summary_stats['total_orders']) ? $summary_stats['total_orders'] : 0 ?></h4>
                    <small>Total POs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?= isset($summary_stats['total_receipts']) ? $summary_stats['total_receipts'] : 0 ?></h4>
                    <small>Total GRNs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4><?= isset($summary_stats['total_invoices']) ? $summary_stats['total_invoices'] : 0 ?></h4>
                    <small>Total Invoices</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4><?= isset($summary_stats['overdue_invoices']) ? $summary_stats['overdue_invoices'] : 0 ?></h4>
                    <small>Overdue</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="<?= base_url('purchase-report/pending-orders') ?>" class="btn btn-warning btn-lg w-100 mb-2">
                                <i class="fas fa-clock me-2"></i>Pending Orders
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('purchase-report/supplier-history') ?>" class="btn btn-info btn-lg w-100 mb-2">
                                <i class="fas fa-history me-2"></i>Supplier History
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('purchase-report/price-trends') ?>" class="btn btn-success btn-lg w-100 mb-2">
                                <i class="fas fa-chart-line me-2"></i>Price Trends
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('purchase-report/quality-metrics') ?>" class="btn btn-primary btn-lg w-100 mb-2">
                                <i class="fas fa-award me-2"></i>Quality Metrics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row">
        <!-- Monthly Trends Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Suppliers Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Suppliers by Volume</h5>
                </div>
                <div class="card-body">
                    <canvas id="topSuppliersChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products and Cost Analysis -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Products by Purchase Value</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Total Value</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($top_products['by_value']) && !empty($top_products['by_value'])): ?>
                                    <?php foreach (array_slice($top_products['by_value'], 0, 5) as $product): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                                            <td>₹<?= number_format($product['total_value'], 2) ?></td>
                                            <td><?= number_format($product['total_quantity']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('purchase-report/cost-analysis') ?>" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="fas fa-calculator me-2"></i>Detailed Cost Analysis
                    </a>
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary">₹<?= number_format(isset($summary_stats['total_orders']) ? $summary_stats['total_orders'] : 0) ?></h3>
                            <small>Total Purchase Value</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">₹<?= number_format(isset($summary_stats['total_invoices']) ? $summary_stats['total_invoices'] : 0) ?></h3>
                            <small>Total Invoice Value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Export Reports</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('purchase-report/export') ?>" method="GET" class="row">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Report</option>
                                <option value="purchase_orders">Purchase Orders</option>
                                <option value="supplier_performance">Supplier Performance</option>
                                <option value="price_trends">Price Trends</option>
                                <option value="quality_metrics">Quality Metrics</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    const monthlyData = <?= json_encode(isset($monthly_trends) ? $monthly_trends : []) ?>;
    
    if (monthlyData.purchase_orders && monthlyData.purchase_orders.length > 0) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.purchase_orders.map(item => item.month_name),
                datasets: [{
                    label: 'Purchase Orders',
                    data: monthlyData.purchase_orders.map(item => item.total),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Goods Receipts',
                    data: monthlyData.goods_receipts ? monthlyData.goods_receipts.map(item => item.total) : [],
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Top Suppliers Chart
    const suppliersCtx = document.getElementById('topSuppliersChart').getContext('2d');
    const suppliersData = <?= json_encode(isset($top_suppliers) ? $top_suppliers : []) ?>;
    
    if (suppliersData.by_volume && suppliersData.by_volume.length > 0) {
        new Chart(suppliersCtx, {
            type: 'doughnut',
            data: {
                labels: suppliersData.by_volume.slice(0, 5).map(item => item.supplier_name),
                datasets: [{
                    data: suppliersData.by_volume.slice(0, 5).map(item => item.total_orders),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
