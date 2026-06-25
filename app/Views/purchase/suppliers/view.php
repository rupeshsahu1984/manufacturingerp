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
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid #ff6b35;
        }
        
        .performance-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .performance-score {
            font-size: 3rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .score-excellent { color: #28a745; }
        .score-good { color: #17a2b8; }
        .score-average { color: #ffc107; }
        .score-poor { color: #dc3545; }
        
        .rating-stars {
            color: #ffc107;
            font-size: 1.5rem;
            margin: 10px 0;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        .status-blacklisted { background-color: #343a40; color: white; }
        
        .category-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .category-raw_material { background-color: #e3f2fd; color: #1565c0; }
        .category-tools { background-color: #f3e5f5; color: #7b1fa2; }
        .category-services { background-color: #e8f5e8; color: #2e7d32; }
        .category-packaging { background-color: #fff3e0; color: #f57c00; }
        .category-other { background-color: #fce4ec; color: #c2185b; }
        
        .metric-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ff6b35;
        }
        
        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6b35;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
        }
        
        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-building me-3"></i>
                        Supplier Details
                    </h1>
                    <p class="mb-0"><?= esc(isset($supplier['supplier_name']) ? $supplier['supplier_name'] : 'N/A') ?></p>
                </div>
                <div class="text-end">
                    <span class="status-badge status-<?= strtolower(isset($supplier['status']) ? $supplier['status'] : 'active') ?>">
                        <?= ucfirst(isset($supplier['status']) ? $supplier['status'] : 'Active') ?>
                    </span>
                    <br>
                    <span class="category-badge category-<?= strtolower(isset($supplier['category']) ? $supplier['category'] : 'other') ?>">
                        <?= ucwords(str_replace('_', ' ', isset($supplier['category']) ? $supplier['category'] : 'Other')) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/purchase/suppliers" class="text-decoration-none">Suppliers</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Supplier Details</li>
            </ol>
        </nav>

        <!-- Action Buttons -->
        <div class="text-center mb-4">
            <div class="action-buttons">
                <a href="/purchase/suppliers" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
                <a href="/purchase/suppliers/edit/<?= $supplier['id'] ?>" class="btn btn-outline-warning btn-action">
                    <i class="fas fa-edit me-2"></i>Edit Supplier
                </a>
                <a href="/purchase/orders/create?supplier_id=<?= $supplier['id'] ?>" class="btn btn-outline-primary btn-action">
                    <i class="fas fa-plus me-2"></i>Create PO
                </a>
                <button type="button" class="btn btn-outline-info btn-action" onclick="printSupplier()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <?php if ((isset($supplier['status']) ? $supplier['status'] : '') === 'active'): ?>
                    <button type="button" class="btn btn-outline-danger btn-action" onclick="deactivateSupplier(<?= $supplier['id'] ?>)">
                        <i class="fas fa-ban me-2"></i>Deactivate
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-success btn-action" onclick="activateSupplier(<?= $supplier['id'] ?>)">
                        <i class="fas fa-check me-2"></i>Activate
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="performance-card">
                    <h5>Overall Rating</h5>
                    <div class="rating-stars">
                        <?php 
                        $rating = isset($supplier['overall_rating']) ? $supplier['overall_rating'] : 0;
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <div class="performance-score score-<?= $rating >= 4 ? 'excellent' : ($rating >= 3 ? 'good' : ($rating >= 2 ? 'average' : 'poor')) ?>">
                        <?= number_format($rating, 1) ?>/5.0
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="performance-card">
                    <h5>On-Time Delivery</h5>
                    <div class="performance-score score-<?= (isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0) >= 90 ? 'excellent' : ((isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0) >= 80 ? 'good' : ((isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0) >= 70 ? 'average' : 'poor')) ?>">
                        <?= number_format(isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0, 1) ?>%
                    </div>
                    <small class="text-muted">Last 12 months</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="performance-card">
                    <h5>Quality Score</h5>
                    <div class="performance-score score-<?= (isset($supplier['quality_score']) ? $supplier['quality_score'] : 0) >= 95 ? 'excellent' : ((isset($supplier['quality_score']) ? $supplier['quality_score'] : 0) >= 85 ? 'good' : ((isset($supplier['quality_score']) ? $supplier['quality_score'] : 0) >= 75 ? 'average' : 'poor')) ?>">
                        <?= number_format(isset($supplier['quality_score']) ? $supplier['quality_score'] : 0, 1) ?>%
                    </div>
                    <small class="text-muted">Based on QC results</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="performance-card">
                    <h5>Price Competitiveness</h5>
                    <div class="performance-score score-<?= (isset($supplier['price_score']) ? $supplier['price_score'] : 0) >= 8 ? 'excellent' : ((isset($supplier['price_score']) ? $supplier['price_score'] : 0) >= 6 ? 'good' : ((isset($supplier['price_score']) ? $supplier['price_score'] : 0) >= 4 ? 'average' : 'poor')) ?>">
                        <?= number_format(isset($supplier['price_score']) ? $supplier['price_score'] : 0, 1) ?>/10
                    </div>
                    <small class="text-muted">Market comparison</small>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Basic Information
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Supplier Code:</strong></div>
                        <div class="col-8"><?= esc(isset($supplier['supplier_code']) ? $supplier['supplier_code'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Supplier Name:</strong></div>
                        <div class="col-8"><?= esc(isset($supplier['supplier_name']) ? $supplier['supplier_name'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Contact Person:</strong></div>
                        <div class="col-8"><?= esc(isset($supplier['contact_person']) ? $supplier['contact_person'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Category:</strong></div>
                        <div class="col-8">
                            <span class="category-badge category-<?= strtolower(isset($supplier['category']) ? $supplier['category'] : 'other') ?>">
                                <?= ucwords(str_replace('_', ' ', isset($supplier['category']) ? $supplier['category'] : 'Other')) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Email:</strong></div>
                        <div class="col-8"><?= esc(isset($supplier['email']) ? $supplier['email'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Phone:</strong></div>
                        <div class="col-8"><?= esc(isset($supplier['phone']) ? $supplier['phone'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Status:</strong></div>
                        <div class="col-8">
                            <span class="status-badge status-<?= strtolower(isset($supplier['status']) ? $supplier['status'] : 'active') ?>">
                                <?= ucfirst(isset($supplier['status']) ? $supplier['status'] : 'Active') ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Rating:</strong></div>
                        <div class="col-8"><?= number_format(isset($supplier['overall_rating']) ? $supplier['overall_rating'] : 0, 1) ?>/5.0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-chart-line me-2"></i>
                Performance Metrics
            </h5>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value"><?= isset($supplier['total_orders']) ? $supplier['total_orders'] : 0 ?></div>
                        <div class="metric-label">Total Orders</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value">₹<?= number_format(isset($supplier['total_spend']) ? $supplier['total_spend'] : 0, 0) ?></div>
                        <div class="metric-label">Total Spend</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value"><?= number_format(isset($supplier['rejection_rate']) ? $supplier['rejection_rate'] : 0, 1) ?>%</div>
                        <div class="metric-label">Rejection Rate</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value"><?= isset($supplier['avg_delivery_time']) ? $supplier['avg_delivery_time'] : 0 ?> days</div>
                        <div class="metric-label">Avg Delivery Time</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-pie me-2"></i>
                        Delivery Performance (Last 12 Months)
                    </h5>
                    <canvas id="deliveryChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-bar me-2"></i>
                        Quality Trends
                    </h5>
                    <canvas id="qualityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Delivery Performance Chart
        const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
        new Chart(deliveryCtx, {
            type: 'doughnut',
            data: {
                labels: ['On Time', 'Late', 'Very Late'],
                datasets: [{
                    data: [
                        <?= isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 80 ?>,
                        <?= 100 - (isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 80) ?>,
                        0
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
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

        // Quality Trends Chart
        const qualityCtx = document.getElementById('qualityChart').getContext('2d');
        new Chart(qualityCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Quality Score (%)',
                    data: [95, 92, 88, 94, 96, <?= isset($supplier['quality_score']) ? $supplier['quality_score'] : 90 ?>],
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        function printSupplier() {
            window.print();
        }

        function activateSupplier(supplierId) {
            if (confirm('Are you sure you want to activate this supplier?')) {
                window.location.href = `/purchase/suppliers/activate/${supplierId}`;
            }
        }

        function deactivateSupplier(supplierId) {
            if (confirm('Are you sure you want to deactivate this supplier?')) {
                window.location.href = `/purchase/suppliers/deactivate/${supplierId}`;
            }
        }
    </script>
</body>
</html>
