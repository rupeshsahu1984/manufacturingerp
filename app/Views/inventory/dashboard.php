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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-left: 5px solid #28a745;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .stats-label {
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
        
        .quick-actions {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .btn-inventory {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            margin: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-inventory:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            color: white;
        }
        
        .alert-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert-low-stock {
            border-left: 5px solid #ffc107;
        }
        
        .alert-expiry {
            border-left: 5px solid #dc3545;
        }
        
        .alert-overstock {
            border-left: 5px solid #17a2b8;
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
        .status-info { background-color: #17a2b8; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-boxes me-3"></i>
                Inventory Management Dashboard
            </h1>
            <p class="mb-0">Complete control over your multi-warehouse inventory operations</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Inventory Management</li>
            </ol>
        </nav>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($warehouse_stats['total_warehouses']) ? $warehouse_stats['total_warehouses'] : 0 ?></div>
                    <div class="stats-label">Total Warehouses</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($item_stats['total_items']) ? $item_stats['total_items'] : 0 ?></div>
                    <div class="stats-label">Total Items</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($stock_stats['total_stock_value']) ? $stock_stats['total_stock_value'] : 0 ?></div>
                    <div class="stats-label">Total Stock Value (₹)</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= isset($transfer_stats['pending_transfers']) ? $transfer_stats['pending_transfers'] : 0 ?></div>
                    <div class="stats-label">Pending Transfers</div>
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
                <a href="/inventory/warehouses/create" class="btn-inventory">
                    <i class="fas fa-warehouse me-2"></i>Add Warehouse
                </a>
                <a href="/inventory/items/create" class="btn-inventory">
                    <i class="fas fa-plus me-2"></i>Add Item
                </a>
                <a href="/inventory/stock/stock-in" class="btn-inventory">
                    <i class="fas fa-arrow-down me-2"></i>Stock In
                </a>
                <a href="/inventory/stock/stock-out" class="btn-inventory">
                    <i class="fas fa-arrow-up me-2"></i>Stock Out
                </a>
                <a href="/inventory/transfers/create" class="btn-inventory">
                    <i class="fas fa-exchange-alt me-2"></i>Create Transfer
                </a>
                <a href="/inventory/stock/scan" class="btn-inventory">
                    <i class="fas fa-barcode me-2"></i>Scan Barcode
                </a>
                <a href="/inventory/stock/rfid" class="btn-inventory">
                    <i class="fas fa-rfid me-2"></i>RFID Scan
                </a>
                <a href="/inventory/reports" class="btn-inventory">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Stock by Category
                    </h5>
                    <canvas id="stockByCategoryChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        Stock Movement Trends
                    </h5>
                    <canvas id="stockMovementChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Warehouse Overview -->
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="fas fa-warehouse text-info me-2"></i>
                Warehouse Capacity & Utilization
            </h5>
            <canvas id="warehouseCapacityChart" width="400" height="200"></canvas>
        </div>

        <!-- Alerts Section -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="alert-card alert-low-stock">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Low Stock Alerts
                    </h6>
                    <div class="list-group list-group-flush">
                        <?php if (isset($low_stock_items) && is_array($low_stock_items) && count($low_stock_items) > 0): ?>
                            <?php foreach (array_slice($low_stock_items, 0, 5) as $item): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc(isset($item['item_name']) ? $item['item_name'] : 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= esc(isset($item['item_code']) ? $item['item_code'] : 'N/A') ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning"><?= isset($item['current_stock']) ? $item['current_stock'] : 0 ?> <?= isset($item['uom']) ? $item['uom'] : '' ?></span><br>
                                        <small class="text-muted">Reorder: <?= isset($item['reorder_level']) ? $item['reorder_level'] : 0 ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted">No low stock items</div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3">
                        <a href="/inventory/items?filter=low_stock" class="btn btn-outline-warning btn-sm">
                            View All Low Stock Items
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="alert-card alert-expiry">
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-clock me-2"></i>
                        Expiry Alerts
                    </h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item text-muted">No items expiring soon</div>
                    </div>
                    <div class="mt-3">
                        <a href="/inventory/stock/expiry-alerts" class="btn btn-outline-danger btn-sm">
                            View Expiry Alerts
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="fas fa-history text-info me-2"></i>
                Recent Stock Activities
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($recent_transfers) && is_array($recent_transfers) && count($recent_transfers) > 0): ?>
                            <?php foreach (array_slice($recent_transfers, 0, 10) as $transfer): ?>
                                <tr>
                                    <td><?= isset($transfer['transfer_date']) ? $transfer['transfer_date'] : 'N/A' ?></td>
                                    <td>
                                        <strong><?= esc(isset($transfer['transfer_number']) ? $transfer['transfer_number'] : 'N/A') ?></strong><br>
                                        <small class="text-muted">Stock Transfer</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Transfer</span>
                                    </td>
                                    <td>
                                        <?= esc(isset($transfer['source_warehouse_name']) ? $transfer['source_warehouse_name'] : 'N/A') ?> →
                                        <?= esc(isset($transfer['destination_warehouse_name']) ? $transfer['destination_warehouse_name'] : 'N/A') ?>
                                    </td>
                                    <td><?= isset($transfer['total_items']) ? $transfer['total_items'] : 0 ?> items</td>
                                    <td><?= esc(isset($transfer['transfer_number']) ? $transfer['transfer_number'] : 'N/A') ?></td>
                                    <td>
                                        <?php 
                                        $status = isset($transfer['status']) ? $transfer['status'] : 'draft';
                                        $statusClass = 'bg-secondary';
                                        if ($status === 'approved') $statusClass = 'bg-success';
                                        elseif ($status === 'in_transit') $statusClass = 'bg-warning';
                                        elseif ($status === 'received') $statusClass = 'bg-info';
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-history fa-2x mb-2"></i><br>
                                    No recent activities
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Advanced Features Section -->
        <div class="row mb-4">
            <div class="col-lg-4">
                <div class="chart-container">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-barcode me-2"></i>
                        Barcode & RFID Integration
                    </h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" onclick="scanBarcode()">
                            <i class="fas fa-barcode me-2"></i>Scan Barcode
                        </button>
                        <button class="btn btn-outline-info" onclick="scanRFID()">
                            <i class="fas fa-rfid me-2"></i>Scan RFID Tag
                        </button>
                        <button class="btn btn-outline-warning" onclick="generateBarcode()">
                            <i class="fas fa-print me-2"></i>Generate Barcode
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="chart-container">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-chart-line me-2"></i>
                        Stock Analytics
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="/inventory/reports/stock-aging" class="btn btn-outline-warning">
                            <i class="fas fa-clock me-2"></i>Stock Aging Report
                        </a>
                        <a href="/inventory/reports/stock-valuation" class="btn btn-outline-info">
                            <i class="fas fa-calculator me-2"></i>Stock Valuation
                        </a>
                        <a href="/inventory/reports/movement-analysis" class="btn btn-outline-success">
                            <i class="fas fa-chart-bar me-2"></i>Movement Analysis
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="chart-container">
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-cog me-2"></i>
                        System Tools
                    </h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-danger" onclick="stockCount()">
                            <i class="fas fa-clipboard-list me-2"></i>Stock Count
                        </button>
                        <button class="btn btn-outline-secondary" onclick="batchTracking()">
                            <i class="fas fa-tags me-2"></i>Batch Tracking
                        </button>
                        <button class="btn btn-outline-dark" onclick="locationManagement()">
                            <i class="fas fa-map-marker-alt me-2"></i>Location Management
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Stock by Category Chart
        const categoryCtx = document.getElementById('stockByCategoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Raw Materials', 'Work in Progress', 'Finished Goods', 'Consumables', 'Spare Parts'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
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

        // Stock Movement Trends Chart
        const movementCtx = document.getElementById('stockMovementChart').getContext('2d');
        new Chart(movementCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Stock In',
                    data: [120, 150, 180, 160, 200, 220],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Stock Out',
                    data: [100, 130, 160, 140, 180, 200],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
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
                        ticks: {
                            callback: function(value) {
                                return value + ' units';
                            }
                        }
                    }
                }
            }
        });

        // Warehouse Capacity Chart
        const capacityCtx = document.getElementById('warehouseCapacityChart').getContext('2d');
        new Chart(capacityCtx, {
            type: 'bar',
            data: {
                labels: ['Main Warehouse', 'Factory Store', 'Distribution Center', 'Retail Store'],
                datasets: [{
                    label: 'Used Capacity',
                    data: [75, 60, 45, 30],
                    backgroundColor: '#28a745'
                }, {
                    label: 'Available Capacity',
                    data: [25, 40, 55, 70],
                    backgroundColor: '#e9ecef'
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
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Function implementations
        function scanBarcode() {
            alert('Barcode scanner activated! Point camera at barcode.');
        }

        function scanRFID() {
            alert('RFID scanner activated! Place RFID tag near reader.');
        }

        function generateBarcode() {
            alert('Barcode generation tool opened!');
        }

        function stockCount() {
            window.location.href = '/inventory/stock/count';
        }

        function batchTracking() {
            window.location.href = '/inventory/stock/batch-tracking';
        }

        function locationManagement() {
            window.location.href = '/inventory/warehouses/locations';
        }

        // Auto-refresh dashboard every 5 minutes
        setInterval(function() {
            console.log('Refreshing inventory dashboard...');
            // Refresh chart data here if needed
        }, 300000);
    </script>
</body>
</html>
