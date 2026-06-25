<?php
session_start();

// Simple session check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Database connection
try {
    $mysqli = new mysqli('localhost', 'root', '', 'manufacturingerp');

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Get user info
    $user_id = $_SESSION['user_id'];
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
    $mysqli->close();
} catch (mysqli_sql_exception $e) {
    // Log error or show a friendly message
    $user = ['role' => 'User', 'full_name' => 'User'];
} catch (Exception $e) {
    $user = ['role' => 'User', 'full_name' => 'User'];
}

// Get statistics
$stats = [
    'total_orders' => 156,
    'production_units' => 1250,
    'revenue' => 25000000,
    'active_employees' => 8
];

// Get recent activities
$recent_activities = [
    [
        'description' => 'New Purchase Requisition created',
        'user' => 'John Doe',
        'time' => '2 minutes ago',
        'status' => 'Completed',
        'status_color' => 'success'
    ],
    [
        'description' => 'Sales Order #SO-2025-001 approved',
        'user' => 'Jane Smith',
        'time' => '15 minutes ago',
        'status' => 'Completed',
        'status_color' => 'success'
    ],
    [
        'description' => 'Work Order #WO-2025-005 started',
        'user' => 'Mike Johnson',
        'time' => '1 hour ago',
        'status' => 'In Progress',
        'status_color' => 'warning'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PRODX ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%); color: white; padding: 1rem 0; }
        .navbar-warning { background-color: #ff6b35 !important; }
        .navbar-warning .nav-link { color: white !important; font-weight: 600; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .btn-warning { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%); border: none; color: white; }
        .bg-gradient-warning { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%) !important; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo">
                        <i class="fas fa-industry text-warning"></i>
                        <span class="ms-2 fw-bold">PRODX</span>
                        <small class="text-muted ms-2">Smarter Control for Modern Manufacturing</small>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <span class="badge bg-warning me-2"><?= $user['role'] ?? 'User' ?></span>
                        <a href="#" class="btn btn-outline-light btn-sm me-2">Profile</a>
                        <a href="logout.php" class="btn btn-dark btn-sm">LOG OUT</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-warning">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>DASHBOARD
                        </a>
                    </li>
                    
                    <!-- Procurement & Inventory -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-boxes me-1"></i>PROCUREMENT
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="purchase_requisition.php">
                                <i class="fas fa-clipboard-list me-2"></i>Purchase Requisition
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-shopping-cart me-2"></i>Purchase Order
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-truck me-2"></i>Gate Entry
                            </a></li>
                        </ul>
                    </li>

                    <!-- Manufacturing -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cogs me-1"></i>MANUFACTURING
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-list-alt me-2"></i>BOM Management
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-tasks me-2"></i>Work Orders
                            </a></li>
                        </ul>
                    </li>

                    <!-- Sales & Distribution -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-shopping-cart me-1"></i>SALES
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-file-invoice me-2"></i>Sales Orders
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-shipping-fast me-2"></i>Dispatch
                            </a></li>
                        </ul>
                    </li>

                    <!-- Finance & Accounting -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-line me-1"></i>FINANCE
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-money-bill-wave me-2"></i>Receivables
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-credit-card me-2"></i>Payables
                            </a></li>
                        </ul>
                    </li>

                    <!-- HR & Attendance -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users me-1"></i>HRM
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-user-tie me-2"></i>Employee Database
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-money-check me-2"></i>Payroll
                            </a></li>
                        </ul>
                    </li>

                    <!-- Settings -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>SETTINGS
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="company_profile.php">
                                <i class="fas fa-building me-2"></i>Company Profile
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">Welcome back, <?= $user['full_name'] ?? 'User' ?>!</h4>
                                    <p class="mb-0">Here's what's happening in your PRODX ERP system today.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h5 class="mb-0"><?= date('l, F j, Y') ?></h5>
                                    <small>Last updated: <?= date('H:i:s') ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <h4><?= $stats['total_orders'] ?></h4>
                            <small>Total Orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-industry fa-2x mb-2"></i>
                            <h4><?= $stats['production_units'] ?></h4>
                            <small>Production Units</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                            <h4>₹<?= number_format($stats['revenue']) ?></h4>
                            <small>Revenue</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h4><?= $stats['active_employees'] ?></h4>
                            <small>Active Employees</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <!-- Sales Chart -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Sales Trend</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Purchase Chart -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Purchase Trend</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="purchaseChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Production Chart -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-industry me-2"></i>Production Trend</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="productionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities & Quick Actions -->
            <div class="row">
                <!-- Recent Activities -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activities</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Activity</th>
                                            <th>User</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_activities as $activity): ?>
                                        <tr>
                                            <td><?= $activity['description'] ?></td>
                                            <td><?= $activity['user'] ?></td>
                                            <td><?= $activity['time'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $activity['status_color'] ?>">
                                                    <?= $activity['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="purchase_requisition.php" class="btn btn-warning btn-sm">
                                    <i class="fas fa-plus me-2"></i>New Purchase Requisition
                                </a>
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus me-2"></i>New Sales Order
                                </a>
                                <a href="#" class="btn btn-info btn-sm">
                                    <i class="fas fa-plus me-2"></i>New Work Order
                                </a>
                                <a href="#" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>New Gate Entry
                                </a>
                                <a href="#" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-plus me-2"></i>Add Employee
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-server me-2"></i>System Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Database</span>
                                <span class="badge bg-success">Online</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Server</span>
                                <span class="badge bg-success">Online</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Backup</span>
                                <span class="badge bg-warning">Pending</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Updates</span>
                                <span class="badge bg-info">Available</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales (₹)',
                    data: [1200000, 1500000, 1800000, 1600000, 2000000, 2200000],
                    borderColor: '#ff6b35',
                    backgroundColor: 'rgba(255, 107, 53, 0.1)',
                    tension: 0.4
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
                        ticks: {
                            callback: function(value) {
                                return '₹' + (value / 1000000) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // Purchase Chart
        const purchaseCtx = document.getElementById('purchaseChart').getContext('2d');
        new Chart(purchaseCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Purchase (₹)',
                    data: [800000, 950000, 1100000, 1000000, 1200000, 1400000],
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    borderWidth: 1
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
                        ticks: {
                            callback: function(value) {
                                return '₹' + (value / 1000000) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // Production Chart
        const productionCtx = document.getElementById('productionChart').getContext('2d');
        new Chart(productionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html> 