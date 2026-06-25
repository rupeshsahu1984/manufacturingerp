<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-chart-pie me-3"></i>
            Financial Analytics
        </h1>
        <p class="mb-0">Advanced financial analysis and insights</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Analytics</li>
        </ol>
    </nav>

    <!-- Profit Margins -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-percentage me-2"></i>Profit Margins
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="text-success">₹<?= number_format($profit_margins['revenue'] ?? 0, 2) ?></h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Total Expenses</h6>
                    <h3 class="text-danger">₹<?= number_format($profit_margins['expenses'] ?? 0, 2) ?></h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Profit</h6>
                    <h3 class="text-primary">₹<?= number_format($profit_margins['profit'] ?? 0, 2) ?></h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Margin %</h6>
                    <h3 class="text-info"><?= number_format($profit_margins['margin_percentage'] ?? 0, 2) ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trends -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-line me-2"></i>Revenue Trends (Last 12 Months)
            </h5>
        </div>
        <div class="card-body">
            <canvas id="revenueTrendsChart" height="100"></canvas>
        </div>
    </div>

    <!-- Customer Performance -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>Top 10 Customers
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Invoices</th>
                            <th>Total Revenue</th>
                            <th>Avg Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($customer_performance) && count($customer_performance) > 0): ?>
                            <?php foreach ($customer_performance as $customer): ?>
                                <tr>
                                    <td><?= esc($customer['customer_name']) ?></td>
                                    <td><?= esc($customer['total_invoices']) ?></td>
                                    <td>₹<?= number_format($customer['total_revenue'], 2) ?></td>
                                    <td>₹<?= number_format($customer['avg_order_value'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueTrendsChart').getContext('2d');
const revenueTrends = <?= json_encode($revenue_trends ?? []) ?>;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: revenueTrends.map(item => item.month),
        datasets: [{
            label: 'Revenue',
            data: revenueTrends.map(item => item.revenue),
            borderColor: 'rgb(75, 192, 192)',
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
</script>
<?= $this->endSection() ?>

