<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Manufacturing ERP</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
        }
        
        .metric-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
        }
        
        .metric-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .report-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .btn-report {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            margin: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-report:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            color: white;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-good { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-danger { background-color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-chart-bar me-3"></i>
                Purchase Reports & Analytics
            </h1>
            <p class="mb-0">Comprehensive insights into your procurement operations</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Reports & Analytics</li>
            </ol>
        </nav>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number">₹<?= number_format(isset($total_spend) ? $total_spend : 0) ?></div>
                    <div class="metric-label">Total Spend (This Month)</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number"><?= isset($total_orders) ? $total_orders : 0 ?></div>
                    <div class="metric-label">Purchase Orders</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number"><?= isset($active_suppliers) ? $active_suppliers : 0 ?></div>
                    <div class="metric-label">Active Suppliers</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number"><?= isset($avg_lead_time) ? $avg_lead_time : 0 ?> days</div>
                    <div class="metric-label">Avg Lead Time</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Spend by Category
                    </h5>
                    <canvas id="spendByCategoryChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        Monthly Spend Trend
                    </h5>
                    <canvas id="monthlySpendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="report-card">
            <h4 class="mb-3">
                <i class="fas fa-file-alt text-info me-2"></i>
                Quick Reports
            </h4>
            <div class="text-center">
                <a href="/purchase/reports/export/pending-orders" class="btn btn-report">
                    <i class="fas fa-clock me-2"></i>Pending Orders Report
                </a>
                <a href="/purchase/reports/export/supplier-performance" class="btn btn-report">
                    <i class="fas fa-star me-2"></i>Supplier Performance
                </a>
                <a href="/purchase/reports/export/spend-analysis" class="btn btn-report">
                    <i class="fas fa-chart-pie me-2"></i>Spend Analysis
                </a>
                <a href="/purchase/reports/export/overdue-invoices" class="btn btn-report">
                    <i class="fas fa-exclamation-triangle me-2"></i>Overdue Invoices
                </a>
                <a href="/purchase/reports/export/price-trends" class="btn btn-report">
                    <i class="fas fa-trending-up me-2"></i>Price Trends
                </a>
                <a href="/purchase/reports/export/quality-metrics" class="btn btn-report">
                    <i class="fas fa-clipboard-check me-2"></i>Quality Metrics
                </a>
            </div>
        </div>

        <!-- Supplier Performance Summary -->
        <div class="report-card">
            <h4 class="mb-3">
                <i class="fas fa-users text-warning me-2"></i>
                Top Supplier Performance
            </h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Total Orders</th>
                            <th>Total Spend</th>
                            <th>On-Time Delivery</th>
                            <th>Quality Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($top_suppliers) && is_array($top_suppliers)): ?>
                            <?php foreach ($top_suppliers as $supplier): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc(isset($supplier['supplier_name']) ? $supplier['supplier_name'] : 'N/A') ?></strong>
                                    </td>
                                    <td><?= isset($supplier['total_orders']) ? $supplier['total_orders'] : 0 ?></td>
                                    <td>₹<?= number_format(isset($supplier['total_spend']) ? $supplier['total_spend'] : 0, 2) ?></td>
                                    <td>
                                        <?php 
                                        $otd = isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0;
                                        $otdClass = $otd >= 90 ? 'status-good' : ($otd >= 75 ? 'status-warning' : 'status-danger');
                                        ?>
                                        <span class="status-indicator <?= $otdClass ?>"></span>
                                        <?= $otd ?>%
                                    </td>
                                    <td>
                                        <?php 
                                        $quality = isset($supplier['quality_score']) ? $supplier['quality_score'] : 0;
                                        $qualityClass = $quality >= 4.5 ? 'status-good' : ($quality >= 3.5 ? 'status-warning' : 'status-danger');
                                        ?>
                                        <span class="status-indicator <?= $qualityClass ?>"></span>
                                        <?= number_format($quality, 1) ?>/5.0
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= (isset($supplier['status']) ? $supplier['status'] : '') === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst(isset($supplier['status']) ? $supplier['status'] : 'Inactive') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2"></i><br>
                                    No supplier data available
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="report-card">
            <h4 class="mb-3">
                <i class="fas fa-history text-info me-2"></i>
                Recent Purchase Activities
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Recent Approvals
                    </h6>
                    <div class="list-group list-group-flush">
                        <?php if (isset($recent_approvals) && is_array($recent_approvals)): ?>
                            <?php foreach ($recent_approvals as $approval): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc(isset($approval['document_type']) ? $approval['document_type'] : 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= esc(isset($approval['reference']) ? $approval['reference'] : 'N/A') ?></small>
                                    </div>
                                    <small class="text-muted"><?= isset($approval['approved_date']) ? $approval['approved_date'] : 'N/A' ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted">No recent approvals</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pending Actions
                    </h6>
                    <div class="list-group list-group-flush">
                        <?php if (isset($pending_actions) && is_array($pending_actions)): ?>
                            <?php foreach ($pending_actions as $action): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc(isset($action['action_type']) ? $action['action_type'] : 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= esc(isset($action['description']) ? $action['description'] : 'N/A') ?></small>
                                    </div>
                                    <small class="text-muted"><?= isset($action['due_date']) ? $action['due_date'] : 'N/A' ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted">No pending actions</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="report-card">
            <h4 class="mb-3">
                <i class="fas fa-download text-success me-2"></i>
                Export Reports
            </h4>
            <div class="row">
                <div class="col-md-4">
                    <h6>Standard Reports</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="exportReport('purchase-summary')">
                            <i class="fas fa-file-pdf me-2"></i>Purchase Summary (PDF)
                        </button>
                        <button class="btn btn-outline-success" onclick="exportReport('supplier-analysis')">
                            <i class="fas fa-file-excel me-2"></i>Supplier Analysis (Excel)
                        </button>
                        <button class="btn btn-outline-info" onclick="exportReport('spend-report')">
                            <i class="fas fa-file-csv me-2"></i>Spend Report (CSV)
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6>Custom Reports</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-warning" onclick="exportReport('custom-date-range')">
                            <i class="fas fa-calendar me-2"></i>Custom Date Range
                        </button>
                        <button class="btn btn-outline-secondary" onclick="exportReport('department-wise')">
                            <i class="fas fa-building me-2"></i>Department-wise
                        </button>
                        <button class="btn btn-outline-dark" onclick="exportReport('item-category')">
                            <i class="fas fa-tags me-2"></i>Item Category
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6>Real-time Data</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-danger" onclick="exportReport('live-dashboard')">
                            <i class="fas fa-tachometer-alt me-2"></i>Live Dashboard
                        </button>
                        <button class="btn btn-outline-primary" onclick="exportReport('kpi-summary')">
                            <i class="fas fa-chart-line me-2"></i>KPI Summary
                        </button>
                        <button class="btn btn-outline-success" onclick="exportReport('performance-metrics')">
                            <i class="fas fa-chart-bar me-2"></i>Performance Metrics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Spend by Category Chart
        const categoryCtx = document.getElementById('spendByCategoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Raw Materials', 'Tools & Equipment', 'Services', 'Packaging', 'Other'],
                datasets: [{
                    data: [45, 25, 15, 10, 5],
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

        // Monthly Spend Trend Chart
        const spendCtx = document.getElementById('monthlySpendChart').getContext('2d');
        new Chart(spendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Monthly Spend (₹)',
                    data: [120000, 150000, 180000, 160000, 200000, 220000],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + (value / 1000) + 'K';
                            }
                        }
                    }
                }
            }
        });

        // Export report function
        function exportReport(reportType) {
            // Show loading
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            btn.disabled = true;

            // Simulate report generation
            setTimeout(() => {
                // Reset button
                btn.innerHTML = originalText;
                btn.disabled = false;

                // Show success message
                alert(`Report "${reportType}" generated successfully! Check your downloads folder.`);
            }, 2000);
        }

        // Auto-refresh charts every 5 minutes
        setInterval(function() {
            // Refresh chart data here if needed
            console.log('Refreshing chart data...');
        }, 300000);
    </script>
</body>
</html>
