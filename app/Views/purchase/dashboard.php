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
        .dashboard-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .dashboard-card .icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        
        .dashboard-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .dashboard-card .label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .quick-action-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 25px;
            margin: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            color: white;
        }
        
        .recent-activity {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.2rem;
        }
        
        .bg-primary { background-color: #007bff !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-info { background-color: #17a2b8 !important; }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-completed { background-color: #d1ecf1; color: #0c5460; }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-0">
                    <i class="fas fa-shopping-cart text-primary me-3"></i>
                    Purchase Management Dashboard
                </h1>
                <p class="text-muted">Monitor and manage all purchase activities</p>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?= $total_suppliers ?></div>
                            <div class="label">Total Suppliers</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?= $pending_prs ?></div>
                            <div class="label">Pending PRs</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?= $pending_pos ?></div>
                            <div class="label">Pending POs</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?= $overdue_invoices ?></div>
                            <div class="label">Overdue Invoices</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h4 class="mb-3">
                <i class="fas fa-bolt text-warning me-2"></i>
                Quick Actions
            </h4>
            <div class="text-center">
                <a href="/purchase/suppliers/create" class="quick-action-btn">
                    <i class="fas fa-plus me-2"></i>Add Supplier
                </a>
                <a href="/purchase/requisitions/create" class="quick-action-btn">
                    <i class="fas fa-clipboard-plus me-2"></i>Create PR
                </a>
                <a href="/purchase/orders/create" class="quick-action-btn">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Create PO
                </a>
                <a href="/purchase/grn/create" class="quick-action-btn">
                    <i class="fas fa-truck me-2"></i>Create GRN
                </a>
                <a href="/purchase/invoices/create" class="quick-action-btn">
                    <i class="fas fa-receipt me-2"></i>Record Invoice
                </a>
                <a href="/purchase/reports" class="quick-action-btn">
                    <i class="fas fa-chart-bar me-2"></i>View Reports
                </a>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Purchase by Category
                    </h5>
                    <canvas id="purchaseCategoryChart" width="400" height="200"></canvas>
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

        <!-- Recent Activities -->
        <div class="recent-activity">
            <h4 class="mb-3">
                <i class="fas fa-history text-info me-2"></i>
                Recent Activities
            </h4>
            
            <div class="activity-item">
                <div class="activity-icon bg-primary">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">Purchase Requisition Approved</div>
                    <div class="text-muted">PR-2024-001 for Raw Materials approved by Manager</div>
                    <small class="text-muted">2 hours ago</small>
                </div>
                <span class="status-badge status-approved">Approved</span>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon bg-success">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">Goods Received</div>
                    <div class="text-muted">GRN-2024-001 created for PO-2024-001</div>
                    <small class="text-muted">4 hours ago</small>
                </div>
                <span class="status-badge status-completed">Completed</span>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon bg-warning">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">Payment Overdue</div>
                    <div class="text-muted">Invoice INV-2024-001 is overdue by 5 days</div>
                    <small class="text-muted">1 day ago</small>
                </div>
                <span class="status-badge status-pending">Overdue</span>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon bg-info">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">New Supplier Added</div>
                    <div class="text-muted">ABC Manufacturing Co. added to supplier database</div>
                    <small class="text-muted">2 days ago</small>
                </div>
                <span class="status-badge status-completed">Added</span>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Purchase by Category Chart
        const categoryCtx = document.getElementById('purchaseCategoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Raw Materials', 'Tools', 'Services', 'Packaging', 'Other'],
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

        // Auto-refresh dashboard every 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
