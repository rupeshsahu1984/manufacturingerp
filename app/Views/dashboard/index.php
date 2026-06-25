<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Professional Dashboard Header -->
<div class="dashboard-header">
    <div class="dashboard-title">
        <h1>Dashboard</h1>
        <p class="dashboard-subtitle">Welcome back! Here's what's happening with your business today.</p>
    </div>
    <div class="dashboard-actions">
        <button class="btn btn-outline-primary btn-sm">
            <i class="fas fa-download"></i> Export
        </button>
        <button class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Order
        </button>
            </div>
        </div>

<!-- Main Navigation Menu -->
<div class="dashboard-nav">
    <div class="nav-tabs">
        <a href="#overview" class="nav-tab active" data-tab="overview">
            <i class="fas fa-chart-pie"></i> Overview
        </a>
        <a href="#production" class="nav-tab" data-tab="production">
            <i class="fas fa-industry"></i> Production
        </a>
        <a href="#sales" class="nav-tab" data-tab="sales">
            <i class="fas fa-chart-line"></i> Sales
        </a>
        <a href="#inventory" class="nav-tab" data-tab="inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="#finance" class="nav-tab" data-tab="finance">
            <i class="fas fa-calculator"></i> Finance
        </a>
        </div>
    </div>

<!-- Key Metrics Row - Compact -->
<div class="metrics-row-compact">
    <div class="metric-card-small">
        <div class="metric-icon-small">
            <i class="fas fa-industry"></i>
        </div>
        <div class="metric-content-small">
            <div class="metric-value-small">1,247</div>
            <div class="metric-label-small">Production</div>
            <div class="metric-change-small positive">+12.5%</div>
        </div>
    </div>

    <div class="metric-card-small">
        <div class="metric-icon-small">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="metric-content-small">
            <div class="metric-value-small">₹12.5M</div>
            <div class="metric-label-small">Revenue</div>
            <div class="metric-change-small positive">+8.3%</div>
        </div>
    </div>

    <div class="metric-card-small">
        <div class="metric-icon-small">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="metric-content-small">
            <div class="metric-value-small">94.2%</div>
            <div class="metric-label-small">Efficiency</div>
            <div class="metric-change-small positive">+2.1%</div>
    </div>
</div>

    <div class="metric-card-small">
        <div class="metric-icon-small">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="metric-content-small">
            <div class="metric-value-small">98.7%</div>
            <div class="metric-label-small">Quality</div>
            <div class="metric-change-small positive">+1.3%</div>
        </div>
        </div>

    <div class="metric-card-small">
        <div class="metric-icon-small">
                <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="metric-content-small">
            <div class="metric-value-small">156</div>
            <div class="metric-label-small">Orders</div>
            <div class="metric-change-small positive">+15.2%</div>
        </div>
        </div>

    <div class="metric-card-small">
        <div class="metric-icon-small">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="metric-content-small">
            <div class="metric-value-small">1,247</div>
            <div class="metric-label-small">Items</div>
            <div class="metric-change-small negative">-2.1%</div>
        </div>
            </div>
        </div>

<!-- Main Dashboard Content - Compact -->
<div class="dashboard-content-compact">
    <!-- Charts Row -->
    <div class="charts-row">
        <!-- Production Chart -->
        <div class="chart-card-compact">
            <div class="chart-header-compact">
                <h4>Production Overview</h4>
                <select class="form-select form-select-xs">
                    <option>6M</option>
                    <option>1Y</option>
                </select>
            </div>
            <div class="chart-body-compact">
                <canvas id="productionChart" width="300" height="150"></canvas>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="chart-card-compact">
            <div class="chart-header-compact">
                <h4>Sales Performance</h4>
                <div class="chart-legend-compact">
                    <span class="legend-dot" style="background: #1e40af;"></span>
                    <span class="legend-dot" style="background: #3b82f6;"></span>
        </div>
            </div>
            <div class="chart-body-compact">
                <canvas id="salesChart" width="300" height="150"></canvas>
            </div>
        </div>
        </div>

    <!-- Data Tables Row -->
    <div class="tables-row">
        <!-- Recent Orders -->
        <div class="data-card-compact">
            <div class="data-header-compact">
                <h4>Recent Orders</h4>
                <a href="<?= base_url('sales-orders') ?>" class="btn btn-xs btn-outline-primary">View All</a>
            </div>
            <div class="data-body-compact">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#SO-001</td>
                            <td>ABC Corp</td>
                            <td>₹45K</td>
                            <td><span class="badge badge-success">Done</span></td>
                        </tr>
                        <tr>
                            <td>#SO-002</td>
                            <td>XYZ Ltd</td>
                            <td>₹32K</td>
                            <td><span class="badge badge-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <td>#SO-003</td>
                            <td>Tech Sol</td>
                            <td>₹67K</td>
                            <td><span class="badge badge-info">Process</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="data-card-compact">
            <div class="data-header-compact">
                <h4>Inventory</h4>
                <a href="<?= base_url('inventory') ?>" class="btn btn-xs btn-outline-primary">Manage</a>
            </div>
            <div class="data-body-compact">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Steel Rods</td>
                            <td>45</td>
                            <td><span class="badge badge-success">OK</span></td>
                        </tr>
                        <tr>
                            <td>Aluminum</td>
                            <td>8</td>
                            <td><span class="badge badge-warning">Low</span></td>
                        </tr>
                        <tr>
                            <td>Copper Wire</td>
                            <td>120</td>
                            <td><span class="badge badge-success">OK</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="data-card-compact">
            <div class="data-header-compact">
                <h4>Quick Actions</h4>
            </div>
            <div class="data-body-compact">
                <div class="quick-actions-compact">
                    <a href="<?= base_url('purchase-requisition') ?>" class="quick-btn">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Purchase</span>
                    </a>
                    <a href="<?= base_url('sales-orders') ?>" class="quick-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Sales</span>
                    </a>
                    <a href="<?= base_url('production') ?>" class="quick-btn">
                        <i class="fas fa-cogs"></i>
                        <span>Production</span>
                    </a>
                    <a href="<?= base_url('inventory') ?>" class="quick-btn">
                        <i class="fas fa-boxes"></i>
                        <span>Inventory</span>
                    </a>
            </div>
            </div>
        </div>
        </div>
</div>

<!-- Recent Activities -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-clock me-2"></i>Recent Activities</h5>
    </div>
    <div class="card-body">
        <div class="activity-list">
            <?php foreach ($recent_activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-circle text-<?= $activity['status_color'] ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-description"><?= $activity['description'] ?></div>
                        <div class="activity-meta">
                            <span class="activity-user"><?= $activity['user'] ?></span>
                            <span class="activity-time"><?= $activity['time'] ?></span>
                        </div>
                    </div>
                    <div class="activity-status">
                        <span class="badge badge-<?= $activity['status_color'] ?>"><?= $activity['status'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Chart.js for Data Visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Dashboard Navigation Tabs
document.addEventListener('DOMContentLoaded', function() {
    const navTabs = document.querySelectorAll('.nav-tab');
    
    navTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            navTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Here you can add logic to show different content based on tab
            const tabName = this.getAttribute('data-tab');
            console.log('Switched to tab:', tabName);
        });
    });
});

// Production Chart
const productionCtx = document.getElementById('productionChart').getContext('2d');
const productionChart = new Chart(productionCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Production Units',
            data: [1200, 1350, 1100, 1450, 1300, 1247],
            backgroundColor: '#3b82f6',
            borderColor: '#1e40af',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#e2e8f0'
                },
                ticks: {
                    color: '#718096'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#718096'
                }
            }
        }
    }
});

// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
            data: [8500000, 9200000, 7800000, 10500000, 9800000, 12500000],
            borderColor: '#1e40af',
            backgroundColor: 'rgba(30, 64, 175, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }, {
            label: 'Orders',
            data: [45, 52, 38, 67, 58, 78],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                grid: {
                    color: '#e2e8f0'
                },
                ticks: {
                    color: '#718096',
                    callback: function(value) {
                        return '₹' + (value / 1000000).toFixed(1) + 'M';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    color: '#718096'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#718096'
                }
            }
        }
    }
});
</script>

<?= $this->endSection() ?> 