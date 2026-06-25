<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-industry me-2"></i>PRODX
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= base_url('dashboard') ?>">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="<?= base_url('inventory') ?>">
                    <i class="fas fa-boxes me-1"></i>Inventory
                </a>
                <a class="nav-link" href="<?= base_url('purchase') ?>">
                    <i class="fas fa-shopping-cart me-1"></i>Purchase
                </a>
                <a class="nav-link active" href="<?= base_url('production') ?>">
                    <i class="fas fa-cogs me-1"></i>Production
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-dark">
                            <i class="fas fa-cogs me-2 text-primary"></i>Production Dashboard
                        </h1>
                        <p class="text-muted mb-0">Manage your manufacturing operations and production planning</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('production/boms/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create BOM
                        </a>
                        <a href="<?= base_url('work-orders/create') ?>" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Create Work Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-list-alt text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Total BOMs</h5>
                                <h2 class="mb-0 text-dark"><?= number_format($totalBOMs) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Active BOMs</h5>
                                <h2 class="mb-0 text-dark"><?= number_format($activeBOMs) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Pending Work Orders</h5>
                                <h2 class="mb-0 text-dark"><?= number_format($pendingWorkOrders) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-spinner text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">In Progress</h5>
                                <h2 class="mb-0 text-dark"><?= number_format($inProgressWorkOrders) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="<?= base_url('production/boms') ?>" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center h-100 hover-shadow">
                                        <i class="fas fa-list-alt fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-1">BOM Management</h6>
                                        <small class="text-muted">Create and manage Bill of Materials</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url('work-orders') ?>" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center h-100 hover-shadow">
                                        <i class="fas fa-clipboard-list fa-2x text-success mb-2"></i>
                                        <h6 class="mb-1">Work Orders</h6>
                                        <small class="text-muted">Manage production work orders</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url('production/job-cards') ?>" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center h-100 hover-shadow">
                                        <i class="fas fa-id-card fa-2x text-info mb-2"></i>
                                        <h6 class="mb-1">Job Cards</h6>
                                        <small class="text-muted">Track production operations</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url('production/mrp') ?>" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center h-100 hover-shadow">
                                        <i class="fas fa-calculator fa-2x text-warning mb-2"></i>
                                        <h6 class="mb-1">MRP</h6>
                                        <small class="text-muted">Material Requirements Planning</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Charts -->
        <div class="row">
            <!-- Recent BOMs -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-list-alt me-2 text-primary"></i>Recent BOMs
                            </h5>
                            <a href="<?= base_url('production/boms') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentBOMs)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>BOM Number</th>
                                            <th>Item</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentBOMs as $bom): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url('production/boms/view/' . $bom['id']) ?>" class="text-decoration-none">
                                                        <strong><?= $bom['bom_number'] ?></strong>
                                                    </a>
                                                </td>
                                                <td><?= isset($bom['item_code']) ? $bom['item_code'] : 'N/A' ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $bom['status'] === 'released' ? 'success' : ($bom['status'] === 'draft' ? 'secondary' : 'warning') ?>">
                                                        <?= ucfirst($bom['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($bom['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No BOMs created yet</p>
                                <a href="<?= base_url('production/boms/create') ?>" class="btn btn-sm btn-primary mt-2">Create First BOM</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Work Orders -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-clipboard-list me-2 text-success"></i>Recent Work Orders
                            </h5>
                            <a href="<?= base_url('work-orders') ?>" class="btn btn-sm btn-outline-success">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentWorkOrders)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>WO Number</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentWorkOrders as $wo): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url('work-orders/view/' . $wo['id']) ?>" class="text-decoration-none">
                                                        <strong><?= $wo['work_order_number'] ?></strong>
                                                    </a>
                                                </td>
                                                <td><?= number_format($wo['order_qty']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $wo['status'] === 'completed' ? 'success' : ($wo['status'] === 'pending' ? 'warning' : 'info') ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $wo['status'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($wo['due_date'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No work orders created yet</p>
                                <a href="<?= base_url('work-orders/create') ?>" class="btn btn-sm btn-success mt-2">Create First Work Order</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Analytics Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-chart-line me-2 text-info"></i>Production Analytics
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="productionChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Production Analytics Chart
        const ctx = document.getElementById('productionChart');
        if (ctx) {
            const productionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($productionAnalytics, 'date')) ?>,
                    datasets: [{
                        label: 'BOM Count',
                        data: <?= json_encode(array_column($productionAnalytics, 'bom_count')) ?>,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Average Cost',
                        data: <?= json_encode(array_column($productionAnalytics, 'avg_cost')) ?>,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'BOM Count'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Average Cost'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Production Performance Trends'
                        }
                    }
                }
            });
        }

        // Add hover effect to quick action cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.hover-shadow');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>
