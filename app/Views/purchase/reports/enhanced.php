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
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #ff6b35;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ff6b35;
            margin: 10px 0;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .report-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .report-section h5 {
            color: #ff6b35;
            border-bottom: 2px solid #ff6b35;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .metric-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .indicator-excellent { background-color: #28a745; }
        .indicator-good { background-color: #17a2b8; }
        .indicator-average { background-color: #ffc107; }
        .indicator-poor { background-color: #dc3545; }
        
        .performance-score {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .score-excellent { color: #28a745; }
        .score-good { color: #17a2b8; }
        .score-average { color: #ffc107; }
        .score-poor { color: #dc3545; }
        
        .filter-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        .btn-export {
            background: #28a745;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-export:hover {
            background: #218838;
            color: white;
            transform: translateY(-2px);
        }
        
        .trend-indicator {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .trend-up { background-color: #d4edda; color: #155724; }
        .trend-down { background-color: #f8d7da; color: #721c24; }
        .trend-stable { background-color: #e2e3e5; color: #383d41; }
        
        .supplier-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .supplier-card:hover {
            border-color: #ff6b35;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.1);
        }
        
        .nav-pills .nav-link {
            color: #6c757d;
            border-radius: 20px;
            margin: 0 5px;
            padding: 8px 20px;
        }
        
        .nav-pills .nav-link.active {
            background-color: #ff6b35;
            color: white;
        }
        
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-chart-line me-3"></i>
                Enhanced Purchase Reports & Analytics
            </h1>
            <p class="mb-0">Comprehensive insights into procurement performance, supplier analytics, and spend optimization</p>
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
                    <a href="/purchase/reports" class="text-decoration-none">Reports</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Enhanced Analytics</li>
            </ol>
        </nav>

        <!-- Filters -->
        <div class="filter-card">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <label for="supplier_filter" class="form-label">Supplier</label>
                    <select class="form-select" id="supplier_filter">
                        <option value="">All Suppliers</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>"><?= esc($supplier['supplier_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category_filter" class="form-label">Category</label>
                    <select class="form-select" id="category_filter">
                        <option value="">All Categories</option>
                        <option value="raw_material">Raw Material</option>
                        <option value="tools">Tools</option>
                        <option value="services">Services</option>
                        <option value="packaging">Packaging</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>Apply
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                    <div class="stats-number"><?= number_format(isset($total_purchase_value) ? $total_purchase_value : 0, 0) ?></div>
                    <div class="stats-label">Total Purchase Value (₹)</div>
                    <div class="trend-indicator trend-up mt-2">
                        <i class="fas fa-arrow-up me-1"></i>12.5%
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <i class="fas fa-boxes fa-2x text-muted mb-2"></i>
                    <div class="stats-number"><?= isset($total_orders) ? $total_orders : 0 ?></div>
                    <div class="stats-label">Total Purchase Orders</div>
                    <div class="trend-indicator trend-up mt-2">
                        <i class="fas fa-arrow-up me-1"></i>8.3%
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <i class="fas fa-building fa-2x text-muted mb-2"></i>
                    <div class="stats-number"><?= isset($active_suppliers) ? $active_suppliers : 0 ?></div>
                    <div class="stats-label">Active Suppliers</div>
                    <div class="trend-indicator trend-stable mt-2">
                        <i class="fas fa-minus me-1"></i>0.0%
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <i class="fas fa-percentage fa-2x text-muted mb-2"></i>
                    <div class="stats-number"><?= number_format(isset($avg_supplier_rating) ? $avg_supplier_rating : 0, 1) ?></div>
                    <div class="stats-label">Avg Supplier Rating</div>
                    <div class="trend-indicator trend-up mt-2">
                        <i class="fas fa-arrow-up me-1"></i>2.1%
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills justify-content-center mb-4" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                    <i class="fas fa-tachometer-alt me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="supplier-tab" data-bs-toggle="pill" data-bs-target="#supplier" type="button" role="tab">
                    <i class="fas fa-building me-2"></i>Supplier Performance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="spend-tab" data-bs-toggle="pill" data-bs-target="#spend" type="button" role="tab">
                    <i class="fas fa-chart-pie me-2"></i>Spend Analysis
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trends-tab" data-bs-toggle="pill" data-bs-target="#trends" type="button" role="tab">
                    <i class="fas fa-chart-line me-2"></i>Trends & Forecasting
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="quality-tab" data-bs-toggle="pill" data-bs-target="#quality" type="button" role="tab">
                    <i class="fas fa-shield-alt me-2"></i>Quality Metrics
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="chart-container">
                            <h5>Purchase Value Trends (Last 12 Months)</h5>
                            <canvas id="purchaseTrendChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="chart-container">
                            <h5>Purchase by Category</h5>
                            <canvas id="categoryChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                        <div class="report-section">
                            <h5>Recent Purchase Orders</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>PO Number</th>
                                            <th>Supplier</th>
                                            <th>Value</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($recent_orders) && is_array($recent_orders)): ?>
                                            <?php foreach (array_slice($recent_orders, 0, 5) as $order): ?>
                                                <tr>
                                                    <td><?= esc(isset($order['po_number']) ? $order['po_number'] : 'N/A') ?></td>
                                                    <td><?= esc(isset($order['supplier_name']) ? $order['supplier_name'] : 'N/A') ?></td>
                                                    <td>₹<?= number_format(isset($order['total_amount']) ? $order['total_amount'] : 0, 2) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= (isset($order['status']) ? $order['status'] : '') === 'completed' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst(isset($order['status']) ? $order['status'] : 'N/A') ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="report-section">
                            <h5>Pending Approvals</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Number</th>
                                            <th>Amount</th>
                                            <th>Days Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($pending_approvals) && is_array($pending_approvals)): ?>
                                            <?php foreach (array_slice($pending_approvals, 0, 5) as $pending): ?>
                                                <tr>
                                                    <td><?= esc(isset($pending['type']) ? $pending['type'] : 'N/A') ?></td>
                                                    <td><?= esc(isset($pending['number']) ? $pending['number'] : 'N/A') ?></td>
                                                    <td>₹<?= number_format(isset($pending['amount']) ? $pending['amount'] : 0, 2) ?></td>
                                                    <td><?= isset($pending['days_pending']) ? $pending['days_pending'] : 0 ?> days</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Performance Tab -->
            <div class="tab-pane fade" id="supplier" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="chart-container">
                            <h5>Supplier Performance Matrix</h5>
                            <canvas id="supplierPerformanceChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="chart-container">
                            <h5>Top Performers</h5>
                            <div id="topPerformers">
                                <?php if (isset($top_suppliers) && is_array($top_suppliers)): ?>
                                    <?php foreach (array_slice($top_suppliers, 0, 5) as $supplier): ?>
                                        <div class="supplier-card">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?= esc(isset($supplier['supplier_name']) ? $supplier['supplier_name'] : 'N/A') ?></h6>
                                                    <small class="text-muted"><?= esc(isset($supplier['category']) ? $supplier['category'] : 'N/A') ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="performance-score score-<?= (isset($supplier['rating']) ? $supplier['rating'] : 0) >= 4 ? 'excellent' : ((isset($supplier['rating']) ? $supplier['rating'] : 0) >= 3 ? 'good' : 'average') ?>">
                                                        <?= number_format(isset($supplier['rating']) ? $supplier['rating'] : 0, 1) ?>/5.0
                                                    </div>
                                                    <small class="text-muted"><?= number_format(isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0, 1) ?>% on-time</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="report-section">
                    <h5>Supplier Performance Details</h5>
                    <div class="table-responsive">
                        <table class="table table-striped" id="supplierPerformanceTable">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Category</th>
                                    <th>Rating</th>
                                    <th>On-Time Delivery</th>
                                    <th>Quality Score</th>
                                    <th>Total Orders</th>
                                    <th>Total Spend</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($supplier_performance) && is_array($supplier_performance)): ?>
                                    <?php foreach ($supplier_performance as $supplier): ?>
                                        <tr>
                                            <td><?= esc(isset($supplier['supplier_name']) ? $supplier['supplier_name'] : 'N/A') ?></td>
                                            <td><?= ucwords(str_replace('_', ' ', isset($supplier['category']) ? $supplier['category'] : 'N/A')) ?></td>
                                            <td>
                                                <span class="performance-score score-<?= (isset($supplier['rating']) ? $supplier['rating'] : 0) >= 4 ? 'excellent' : ((isset($supplier['rating']) ? $supplier['rating'] : 0) >= 3 ? 'good' : 'average') ?>">
                                                    <?= number_format(isset($supplier['rating']) ? $supplier['rating'] : 0, 1) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format(isset($supplier['on_time_delivery']) ? $supplier['on_time_delivery'] : 0, 1) ?>%</td>
                                            <td><?= number_format(isset($supplier['quality_score']) ? $supplier['quality_score'] : 0, 1) ?>%</td>
                                            <td><?= isset($supplier['total_orders']) ? $supplier['total_orders'] : 0 ?></td>
                                            <td>₹<?= number_format(isset($supplier['total_spend']) ? $supplier['total_spend'] : 0, 2) ?></td>
                                            <td>
                                                <a href="/purchase/suppliers/view/<?= $supplier['id'] ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-export" onclick="exportSupplierReport(<?= $supplier['id'] ?>)">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Spend Analysis Tab -->
            <div class="tab-pane fade" id="spend" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <h5>Spend by Category</h5>
                            <canvas id="spendCategoryChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <h5>Spend by Department</h5>
                            <canvas id="spendDepartmentChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="report-section">
                    <h5>Spend Analysis Details</h5>
                    <div class="table-responsive">
                        <table class="table table-striped" id="spendAnalysisTable">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Department</th>
                                    <th>Total Spend</th>
                                    <th>Orders Count</th>
                                    <th>Avg Order Value</th>
                                    <th>Trend</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($spend_analysis) && is_array($spend_analysis)): ?>
                                    <?php foreach ($spend_analysis as $spend): ?>
                                        <tr>
                                            <td><?= ucwords(str_replace('_', ' ', isset($spend['category']) ? $spend['category'] : 'N/A')) ?></td>
                                            <td><?= esc(isset($spend['department']) ? $spend['department'] : 'N/A') ?></td>
                                            <td>₹<?= number_format(isset($spend['total_spend']) ? $spend['total_spend'] : 0, 2) ?></td>
                                            <td><?= isset($spend['orders_count']) ? $spend['orders_count'] : 0 ?></td>
                                            <td>₹<?= number_format(isset($spend['avg_order_value']) ? $spend['avg_order_value'] : 0, 2) ?></td>
                                            <td>
                                                <span class="trend-indicator trend-<?= (isset($spend['trend']) ? $spend['trend'] : '') === 'up' ? 'up' : ((isset($spend['trend']) ? $spend['trend'] : '') === 'down' ? 'down' : 'stable') ?>">
                                                    <i class="fas fa-arrow-<?= (isset($spend['trend']) ? $spend['trend'] : '') === 'up' ? 'up' : ((isset($spend['trend']) ? $spend['trend'] : '') === 'down' ? 'down' : 'minus') ?> me-1"></i>
                                                    <?= number_format(isset($spend['trend_percentage']) ? $spend['trend_percentage'] : 0, 1) ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-export" onclick="exportSpendReport('<?= isset($spend['category']) ? $spend['category'] : '' ?>')">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Trends & Forecasting Tab -->
            <div class="tab-pane fade" id="trends" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="chart-container">
                            <h5>Price Trends & Forecasting</h5>
                            <canvas id="priceTrendChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="chart-container">
                            <h5>Seasonal Patterns</h5>
                            <canvas id="seasonalChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="report-section">
                    <h5>Price Variance Analysis</h5>
                    <div class="table-responsive">
                        <table class="table table-striped" id="priceVarianceTable">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Current Price</th>
                                    <th>Previous Price</th>
                                    <th>Variance</th>
                                    <th>Variance %</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($price_variance) && is_array($price_variance)): ?>
                                    <?php foreach ($price_variance as $variance): ?>
                                        <tr>
                                            <td><?= esc(isset($variance['item_name']) ? $variance['item_name'] : 'N/A') ?></td>
                                            <td><?= ucwords(str_replace('_', ' ', isset($variance['category']) ? $variance['category'] : 'N/A')) ?></td>
                                            <td>₹<?= number_format(isset($variance['current_price']) ? $variance['current_price'] : 0, 2) ?></td>
                                            <td>₹<?= number_format(isset($variance['previous_price']) ? $variance['previous_price'] : 0, 2) ?></td>
                                            <td>₹<?= number_format(isset($variance['variance']) ? $variance['variance'] : 0, 2) ?></td>
                                            <td><?= number_format(isset($variance['variance_percentage']) ? $variance['variance_percentage'] : 0, 2) ?>%</td>
                                            <td>
                                                <span class="trend-indicator trend-<?= (isset($variance['variance']) ? $variance['variance'] : 0) > 0 ? 'up' : 'down' ?>">
                                                    <i class="fas fa-arrow-<?= (isset($variance['variance']) ? $variance['variance'] : 0) > 0 ? 'up' : 'down' ?> me-1"></i>
                                                    <?= number_format(abs(isset($variance['variance_percentage']) ? $variance['variance_percentage'] : 0), 1) ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quality Metrics Tab -->
            <div class="tab-pane fade" id="quality" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <h5>Quality Score Trends</h5>
                            <canvas id="qualityTrendChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <h5>Rejection Rate by Supplier</h5>
                            <canvas id="rejectionRateChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="report-section">
                    <h5>Quality Metrics Summary</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-number"><?= number_format(isset($overall_quality_score) ? $overall_quality_score : 0, 1) ?>%</div>
                                <div class="stats-label">Overall Quality Score</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-number"><?= number_format(isset($avg_rejection_rate) ? $avg_rejection_rate : 0, 2) ?>%</div>
                                <div class="stats-label">Average Rejection Rate</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-number"><?= isset($total_quality_issues) ? $total_quality_issues : 0 ?></div>
                                <div class="stats-label">Total Quality Issues</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card text-center">
                                <div class="stats-number"><?= number_format(isset($quality_improvement) ? $quality_improvement : 0, 1) ?>%</div>
                                <div class="stats-label">Quality Improvement</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#supplierPerformanceTable').DataTable({
                pageLength: 25,
                order: [[2, 'desc']], // Sort by rating
                responsive: true
            });
            
            $('#spendAnalysisTable').DataTable({
                pageLength: 25,
                order: [[2, 'desc']], // Sort by total spend
                responsive: true
            });
            
            $('#priceVarianceTable').DataTable({
                pageLength: 25,
                order: [[5, 'desc']], // Sort by variance percentage
                responsive: true
            });
            
            // Initialize charts
            initializeCharts();
        });

        function initializeCharts() {
            // Purchase Trend Chart
            const purchaseTrendCtx = document.getElementById('purchaseTrendChart').getContext('2d');
            new Chart(purchaseTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Purchase Value (₹)',
                        data: [1200000, 1350000, 1100000, 1400000, 1600000, 1550000, 1700000, 1650000, 1800000, 1750000, 1900000, 2000000],
                        borderColor: '#ff6b35',
                        backgroundColor: 'rgba(255, 107, 53, 0.1)',
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
                            beginAtZero: true
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Raw Material', 'Tools', 'Services', 'Packaging'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: ['#ff6b35', '#17a2b8', '#28a745', '#ffc107']
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

            // Supplier Performance Chart
            const supplierPerformanceCtx = document.getElementById('supplierPerformanceChart').getContext('2d');
            new Chart(supplierPerformanceCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Suppliers',
                        data: [
                            {x: 95, y: 4.5, r: 20}, // High quality, high delivery
                            {x: 85, y: 4.2, r: 15},
                            {x: 90, y: 3.8, r: 18},
                            {x: 75, y: 4.0, r: 12},
                            {x: 80, y: 3.5, r: 14}
                        ],
                        backgroundColor: 'rgba(255, 107, 53, 0.6)',
                        borderColor: '#ff6b35'
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
                        x: {
                            title: {
                                display: true,
                                text: 'On-Time Delivery (%)'
                            },
                            min: 70,
                            max: 100
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Quality Rating'
                            },
                            min: 3,
                            max: 5
                        }
                    }
                }
            });

            // Spend Category Chart
            const spendCategoryCtx = document.getElementById('spendCategoryChart').getContext('2d');
            new Chart(spendCategoryCtx, {
                type: 'bar',
                data: {
                    labels: ['Raw Material', 'Tools', 'Services', 'Packaging'],
                    datasets: [{
                        label: 'Spend (₹)',
                        data: [4500000, 2500000, 2000000, 1000000],
                        backgroundColor: ['#ff6b35', '#17a2b8', '#28a745', '#ffc107']
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
                            beginAtZero: true
                        }
                    }
                }
            });

            // Spend Department Chart
            const spendDepartmentCtx = document.getElementById('spendDepartmentChart').getContext('2d');
            new Chart(spendDepartmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Production', 'Maintenance', 'Engineering', 'Quality'],
                    datasets: [{
                        label: 'Spend (₹)',
                        data: [6000000, 2000000, 1500000, 500000],
                        backgroundColor: ['#ff6b35', '#17a2b8', '#28a745', '#ffc107']
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
                            beginAtZero: true
                        }
                    }
                }
            });

            // Price Trend Chart
            const priceTrendCtx = document.getElementById('priceTrendChart').getContext('2d');
            new Chart(priceTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Raw Material',
                        data: [100, 105, 102, 108, 110, 112],
                        borderColor: '#ff6b35',
                        tension: 0.4
                    }, {
                        label: 'Tools',
                        data: [200, 198, 202, 205, 208, 210],
                        borderColor: '#17a2b8',
                        tension: 0.4
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
                            beginAtZero: true
                        }
                    }
                }
            });

            // Seasonal Chart
            const seasonalCtx = document.getElementById('seasonalChart').getContext('2d');
            new Chart(seasonalCtx, {
                type: 'radar',
                data: {
                    labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                    datasets: [{
                        label: 'Spend Pattern',
                        data: [80, 90, 85, 95],
                        borderColor: '#ff6b35',
                        backgroundColor: 'rgba(255, 107, 53, 0.2)',
                        pointBackgroundColor: '#ff6b35'
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
                        r: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });

            // Quality Trend Chart
            const qualityTrendCtx = document.getElementById('qualityTrendChart').getContext('2d');
            new Chart(qualityTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Quality Score (%)',
                        data: [92, 94, 91, 95, 96, 97],
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

            // Rejection Rate Chart
            const rejectionRateCtx = document.getElementById('rejectionRateChart').getContext('2d');
            new Chart(rejectionRateCtx, {
                type: 'bar',
                data: {
                    labels: ['Supplier A', 'Supplier B', 'Supplier C', 'Supplier D'],
                    datasets: [{
                        label: 'Rejection Rate (%)',
                        data: [2.5, 1.8, 3.2, 1.5],
                        backgroundColor: ['#ff6b35', '#17a2b8', '#28a745', '#ffc107']
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
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function applyFilters() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const supplier = document.getElementById('supplier_filter').value;
            const category = document.getElementById('category_filter').value;
            
            // Apply filters to charts and tables
            console.log('Applying filters:', { dateFrom, dateTo, supplier, category });
            
            // Refresh data based on filters
            // This would typically make AJAX calls to update the data
        }

        function resetFilters() {
            document.getElementById('date_from').value = '<?= date('Y-m-01') ?>';
            document.getElementById('date_to').value = '<?= date('Y-m-d') ?>';
            document.getElementById('supplier_filter').value = '';
            document.getElementById('category_filter').value = '';
            
            // Reset charts and tables to original data
            console.log('Filters reset');
        }

        function exportSupplierReport(supplierId) {
            window.location.href = `/purchase/reports/export-supplier/${supplierId}`;
        }

        function exportSpendReport(category) {
            window.location.href = `/purchase/reports/export-spend/${category}`;
        }
    </script>
</body>
</html>
