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
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
        
        .filters-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .warehouse-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745;
            transition: transform 0.3s ease;
        }
        
        .warehouse-card:hover {
            transform: translateY(-5px);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        .status-maintenance { background-color: #fff3cd; color: #856404; }
        .status-closed { background-color: #e9ecef; color: #495057; }
        
        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .type-head_office { background-color: #e3f2fd; color: #1565c0; }
        .type-factory { background-color: #f3e5f5; color: #7b1fa2; }
        .type-branch { background-color: #e8f5e8; color: #2e7d32; }
        .type-distribution_center { background-color: #fff8e1; color: #f57f17; }
        .type-retail_store { background-color: #fce4ec; color: #c2185b; }
        
        .capacity-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .capacity-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .warehouse-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .view-toggle {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .btn-view-toggle {
            background: white;
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 8px 16px;
            border-radius: 8px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .btn-view-toggle.active {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-warehouse me-3"></i>
                Multi-Warehouse Management
            </h1>
            <p class="mb-0">Manage multiple warehouses, locations, and capacity tracking</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/inventory" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Inventory Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Warehouses</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= count($warehouses) ?></div>
                    <div class="text-muted">Total Warehouses</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $activeCount = 0;
                        foreach ($warehouses as $warehouse) {
                            if ((isset($warehouse['status']) ? $warehouse['status'] : '') === 'active') $activeCount++;
                        }
                        echo $activeCount;
                        ?>
                    </div>
                    <div class="text-muted">Active Warehouses</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $totalCapacity = 0;
                        $usedCapacity = 0;
                        foreach ($warehouses as $warehouse) {
                            $totalCapacity += isset($warehouse['capacity_total']) ? $warehouse['capacity_total'] : 0;
                            $usedCapacity += isset($warehouse['capacity_used']) ? $warehouse['capacity_used'] : 0;
                        }
                        echo $totalCapacity > 0 ? round(($usedCapacity / $totalCapacity) * 100, 1) : 0;
                        ?>%
                    </div>
                    <div class="text-muted">Overall Utilization</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $maintenanceCount = 0;
                        foreach ($warehouses as $warehouse) {
                            if ((isset($warehouse['status']) ? $warehouse['status'] : '') === 'maintenance') $maintenanceCount++;
                        }
                        echo $maintenanceCount;
                        ?>
                    </div>
                    <div class="text-muted">Under Maintenance</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="/inventory/warehouses" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Warehouse name, code...">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <?php foreach ($warehouse_types as $key => $value): ?>
                            <option value="<?= $key ?>" <?= (isset($_GET['type']) ? $_GET['type'] : '') === $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="maintenance" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        <option value="closed" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" 
                           value="<?= isset($_GET['city']) ? $_GET['city'] : '' ?>" placeholder="City name">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle text-center">
            <button type="button" class="btn-view-toggle active" onclick="switchView('grid')">
                <i class="fas fa-th-large me-2"></i>Grid View
            </button>
            <button type="button" class="btn-view-toggle" onclick="switchView('table')">
                <i class="fas fa-table me-2"></i>Table View
            </button>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="/inventory/warehouses" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </a>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
                <a href="/inventory/warehouses/map" class="btn btn-outline-warning me-2">
                    <i class="fas fa-map me-2"></i>Warehouse Map
                </a>
            </div>
            <div>
                <a href="/inventory/warehouses/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Warehouse
                </a>
            </div>
        </div>

        <!-- Grid View -->
        <div id="grid-view" class="warehouse-grid">
            <?php if (isset($warehouses) && is_array($warehouses)): ?>
                <?php foreach ($warehouses as $warehouse): ?>
                    <div class="warehouse-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?= esc(isset($warehouse['warehouse_name']) ? $warehouse['warehouse_name'] : 'N/A') ?></h5>
                                <small class="text-muted"><?= esc(isset($warehouse['warehouse_code']) ? $warehouse['warehouse_code'] : 'N/A') ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge status-<?= strtolower(isset($warehouse['status']) ? $warehouse['status'] : 'inactive') ?>">
                                    <?= ucfirst(isset($warehouse['status']) ? $warehouse['status'] : 'Inactive') ?>
                                </span>
                                <br>
                                <span class="type-badge type-<?= strtolower(isset($warehouse['warehouse_type']) ? $warehouse['warehouse_type'] : 'unknown') ?>">
                                    <?= ucwords(str_replace('_', ' ', isset($warehouse['warehouse_type']) ? $warehouse['warehouse_type'] : 'Unknown')) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Capacity Utilization</small>
                                <small class="text-muted">
                                    <?= isset($warehouse['capacity_used']) ? $warehouse['capacity_used'] : 0 ?>/<?= isset($warehouse['capacity_total']) ? $warehouse['capacity_total'] : 0 ?> 
                                    <?= isset($warehouse['capacity_unit']) ? $warehouse['capacity_unit'] : '' ?>
                                </small>
                            </div>
                            <div class="capacity-bar">
                                <?php 
                                $utilization = 0;
                                if ((isset($warehouse['capacity_total']) ? $warehouse['capacity_total'] : 0) > 0) {
                                    $utilization = ((isset($warehouse['capacity_used']) ? $warehouse['capacity_used'] : 0) / $warehouse['capacity_total']) * 100;
                                }
                                ?>
                                <div class="capacity-fill" style="width: <?= min($utilization, 100) ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Location</small>
                                <strong><?= esc(isset($warehouse['city']) ? $warehouse['city'] : 'N/A') ?>, <?= esc(isset($warehouse['state']) ? $warehouse['state'] : 'N/A') ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Contact</small>
                                <strong><?= esc(isset($warehouse['contact_person']) ? $warehouse['contact_person'] : 'N/A') ?></strong>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="/inventory/warehouses/view/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-info btn-action">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/inventory/warehouses/edit/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-warning btn-action">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/inventory/warehouses/locations/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            </div>
                            <div>
                                <a href="/inventory/warehouses/stock/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-success btn-action">
                                    <i class="fas fa-boxes"></i> Stock
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-warehouse fa-3x mb-3"></i>
                    <h5>No warehouses found</h5>
                    <p>Start by adding your first warehouse</p>
                    <a href="/inventory/warehouses/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Warehouse
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Table View (Hidden by default) -->
        <div id="table-view" class="table-container" style="display: none;">
            <table id="warehousesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Warehouse Code</th>
                        <th>Warehouse Name</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Utilization</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($warehouses) && is_array($warehouses)): ?>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($warehouse['warehouse_code']) ? $warehouse['warehouse_code'] : 'N/A') ?></strong>
                                </td>
                                <td><?= esc(isset($warehouse['warehouse_name']) ? $warehouse['warehouse_name'] : 'N/A') ?></td>
                                <td>
                                    <span class="type-badge type-<?= strtolower(isset($warehouse['warehouse_type']) ? $warehouse['warehouse_type'] : 'unknown') ?>">
                                        <?= ucwords(str_replace('_', ' ', isset($warehouse['warehouse_type']) ? $warehouse['warehouse_type'] : 'Unknown')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= esc(isset($warehouse['city']) ? $warehouse['city'] : 'N/A') ?>, <?= esc(isset($warehouse['state']) ? $warehouse['state'] : 'N/A') ?>
                                </td>
                                <td>
                                    <?= isset($warehouse['capacity_used']) ? $warehouse['capacity_used'] : 0 ?>/<?= isset($warehouse['capacity_total']) ? $warehouse['capacity_total'] : 0 ?> 
                                    <?= isset($warehouse['capacity_unit']) ? $warehouse['capacity_unit'] : '' ?>
                                </td>
                                <td>
                                    <?php 
                                    $utilization = 0;
                                    if ((isset($warehouse['capacity_total']) ? $warehouse['capacity_total'] : 0) > 0) {
                                        $utilization = ((isset($warehouse['capacity_used']) ? $warehouse['capacity_used'] : 0) / $warehouse['capacity_total']) * 100;
                                    }
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="capacity-bar me-2" style="width: 100px;">
                                            <div class="capacity-fill" style="width: <?= min($utilization, 100) ?>%"></div>
                                        </div>
                                        <small><?= round($utilization, 1) ?>%</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(isset($warehouse['status']) ? $warehouse['status'] : 'inactive') ?>">
                                        <?= ucfirst(isset($warehouse['status']) ? $warehouse['status'] : 'Inactive') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/inventory/warehouses/view/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/inventory/warehouses/edit/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/inventory/warehouses/locations/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Locations">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                        <a href="/inventory/warehouses/stock/<?= $warehouse['id'] ?>" class="btn btn-sm btn-outline-success btn-action" title="Stock">
                                            <i class="fas fa-boxes"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable for table view
            $('#warehousesTable').DataTable({
                pageLength: 25,
                order: [[1, 'asc']], // Sort by warehouse name
                responsive: true,
                language: {
                    search: "Search warehouses:",
                    lengthMenu: "Show _MENU_ warehouses per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ warehouses"
                }
            });
        });

        // Switch between grid and table views
        function switchView(viewType) {
            const gridView = document.getElementById('grid-view');
            const tableView = document.getElementById('table-view');
            const buttons = document.querySelectorAll('.btn-view-toggle');
            
            if (viewType === 'grid') {
                gridView.style.display = 'grid';
                tableView.style.display = 'none';
                buttons[0].classList.add('active');
                buttons[1].classList.remove('active');
            } else {
                gridView.style.display = 'none';
                tableView.style.display = 'block';
                buttons[0].classList.remove('active');
                buttons[1].classList.add('active');
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('warehousesTable');
            let csv = [];
            
            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Get data rows
            table.querySelectorAll('tbody tr').forEach(row => {
                const rowData = [];
                row.querySelectorAll('td').forEach((td, index) => {
                    if (index < 7) { // Exclude actions column
                        let text = td.textContent.trim();
                        // Remove badges and capacity bars
                        text = text.replace(/[^\w\s%,.-]/g, '');
                        rowData.push(`"${text}"`);
                    }
                });
                csv.push(rowData.join(','));
            });
            
            // Download CSV
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'warehouses.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
