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
        
        .item-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745;
            transition: transform 0.3s ease;
        }
        
        .item-card:hover {
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
        .status-discontinued { background-color: #e9ecef; color: #495057; }
        
        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .type-raw_material { background-color: #e3f2fd; color: #1565c0; }
        .type-semi_finished { background-color: #f3e5f5; color: #7b1fa2; }
        .type-finished_goods { background-color: #e8f5e8; color: #2e7d32; }
        .type-consumables { background-color: #fff8e1; color: #f57f17; }
        .type-spare_parts { background-color: #fce4ec; color: #c2185b; }
        .type-packaging { background-color: #e0f2f1; color: #00695c; }
        .type-waste { background-color: #ffebee; color: #c62828; }
        
        .stock-indicator {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .stock-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .stock-high { background: linear-gradient(90deg, #28a745, #20c997); }
        .stock-medium { background: linear-gradient(90deg, #ffc107, #ff9800); }
        .stock-low { background: linear-gradient(90deg, #dc3545, #f44336); }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
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
        
        .barcode-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        .item-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .detail-value {
            color: #333;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-box me-3"></i>
                Item Master Database
            </h1>
            <p class="mb-0">Comprehensive management of all materials, products, and inventory items</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/inventory" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Inventory Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Item Master</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number"><?= count($items ?? []) ?></div>
                    <div class="text-muted">Total Items</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $activeCount = 0;
                        foreach ($items as $item) {
                            if ((isset($item['status']) ? $item['status'] : '') === 'active') $activeCount++;
                        }
                        echo $activeCount;
                        ?>
                    </div>
                    <div class="text-muted">Active Items</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $lowStockCount = 0;
                        foreach ($items as $item) {
                            if ((isset($item['current_stock']) ? $item['current_stock'] : 0) <= (isset($item['reorder_level']) ? $item['reorder_level'] : 0)) $lowStockCount++;
                        }
                        echo $lowStockCount;
                        ?>
                    </div>
                    <div class="text-muted">Low Stock Items</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $categories = [];
                        foreach ($items as $item) {
                            $categories[] = isset($item['category_name']) ? $item['category_name'] : 'Unknown';
                        }
                        echo count(array_unique($categories));
                        ?>
                    </div>
                    <div class="text-muted">Categories</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="/inventory/items" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Item name, code, barcode...">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <?php foreach ($item_types as $key => $value): ?>
                            <option value="<?= $key ?>" <?= (isset($_GET['type']) ? $_GET['type'] : '') === $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        <?php foreach ((is_array($categories ?? null) ? $categories : []) as $category): ?>
                            <?php if (! is_array($category)) {
                                continue;
                            } ?>
                            <option value="<?= esc($category['id'] ?? '') ?>" <?= (isset($_GET['category_id']) ? $_GET['category_id'] : '') == ($category['id'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($category['category_name'] ?? '') ?>
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
                        <option value="discontinued" <?= (isset($_GET['status']) ? $_GET['status'] : '') === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
                    </select>
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
                <a href="/inventory/items" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-refresh me-2"></i>Refresh
                </a>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
                <a href="/inventory/items/barcode-generator" class="btn btn-outline-warning me-2">
                    <i class="fas fa-barcode me-2"></i>Barcode Generator
                </a>
                <a href="/inventory/items/import" class="btn btn-outline-success me-2">
                    <i class="fas fa-upload me-2"></i>Import Items
                </a>
            </div>
            <div>
                <a href="/inventory/items/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Item
                </a>
            </div>
        </div>

        <!-- Grid View -->
        <div id="grid-view" class="item-grid">
            <?php if (isset($items) && is_array($items)): ?>
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?= esc(isset($item['item_name']) ? $item['item_name'] : 'N/A') ?></h5>
                                <small class="text-muted"><?= esc(isset($item['item_code']) ? $item['item_code'] : 'N/A') ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge status-<?= strtolower(isset($item['status']) ? $item['status'] : 'inactive') ?>">
                                    <?= ucfirst(isset($item['status']) ? $item['status'] : 'Inactive') ?>
                                </span>
                                <br>
                                <span class="type-badge type-<?= strtolower(isset($item['item_type']) ? $item['item_type'] : 'unknown') ?>">
                                    <?= ucwords(str_replace('_', ' ', isset($item['item_type']) ? $item['item_type'] : 'Unknown')) ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Barcode Preview -->
                        <?php if (!empty($item['barcode'])): ?>
                            <div class="barcode-preview mb-3">
                                <small class="text-muted d-block mb-1">Barcode</small>
                                <strong><?= esc($item['barcode']) ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Item Details -->
                        <div class="item-details">
                            <div class="detail-item">
                                <span class="detail-label">Category:</span>
                                <span class="detail-value"><?= esc(isset($item['category_name']) ? $item['category_name'] : 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">UOM:</span>
                                <span class="detail-value"><?= esc(isset($item['uom']) ? $item['uom'] : 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Current Stock:</span>
                                <span class="detail-value">
                                    <?= isset($item['current_stock']) ? $item['current_stock'] : 0 ?> <?= esc(isset($item['uom']) ? $item['uom'] : '') ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Reorder Level:</span>
                                <span class="detail-value"><?= isset($item['reorder_level']) ? $item['reorder_level'] : 0 ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Standard Cost:</span>
                                <span class="detail-value">₹<?= number_format(isset($item['standard_cost']) ? $item['standard_cost'] : 0, 2) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Selling Price:</span>
                                <span class="detail-value">₹<?= number_format(isset($item['selling_price']) ? $item['selling_price'] : 0, 2) ?></span>
                            </div>
                        </div>
                        
                        <!-- Stock Level Indicator -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Stock Level</small>
                                <small class="text-muted">
                                    <?= isset($item['current_stock']) ? $item['current_stock'] : 0 ?>/<?= isset($item['max_stock']) ? $item['max_stock'] : 0 ?> 
                                    <?= esc(isset($item['uom']) ? $item['uom'] : '') ?>
                                </small>
                            </div>
                            <div class="stock-indicator">
                                <?php 
                                $stockLevel = 0;
                                if ((isset($item['max_stock']) ? $item['max_stock'] : 0) > 0) {
                                    $stockLevel = ((isset($item['current_stock']) ? $item['current_stock'] : 0) / $item['max_stock']) * 100;
                                }
                                
                                $stockClass = 'stock-high';
                                if ($stockLevel <= 30) $stockClass = 'stock-low';
                                elseif ($stockLevel <= 70) $stockClass = 'stock-medium';
                                ?>
                                <div class="stock-fill <?= $stockClass ?>" style="width: <?= min($stockLevel, 100) ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Additional Info -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">HSN Code</small>
                                <strong><?= esc(isset($item['hsn_code']) ? $item['hsn_code'] : 'N/A') ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Weight</small>
                                <strong>
                                    <?php if (!empty($item['weight'])): ?>
                                        <?= $item['weight'] ?> <?= esc(isset($item['weight_uom']) ? $item['weight_uom'] : '') ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="/inventory/items/view/<?= $item['id'] ?>" class="btn btn-sm btn-outline-info btn-action">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/inventory/items/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning btn-action">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/inventory/items/barcode/<?= $item['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action">
                                    <i class="fas fa-barcode"></i>
                                </a>
                            </div>
                            <div>
                                <a href="/inventory/stock/stock-in?item_id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-success btn-action">
                                    <i class="fas fa-plus"></i> Stock In
                                </a>
                                <a href="/inventory/stock/stock-out?item_id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger btn-action">
                                    <i class="fas fa-minus"></i> Stock Out
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-box fa-3x mb-3"></i>
                    <h5>No items found</h5>
                    <p>Start by adding your first inventory item</p>
                    <a href="/inventory/items/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Table View (Hidden by default) -->
        <div id="table-view" class="table-container" style="display: none;">
            <table id="itemsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>UOM</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Standard Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($items) && is_array($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($item['item_code']) ? $item['item_code'] : 'N/A') ?></strong>
                                    <?php if (!empty($item['barcode'])): ?>
                                        <br><small class="text-muted"><?= esc($item['barcode']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc(isset($item['item_name']) ? $item['item_name'] : 'N/A') ?></td>
                                <td>
                                    <span class="type-badge type-<?= strtolower(isset($item['item_type']) ? $item['item_type'] : 'unknown') ?>">
                                        <?= ucwords(str_replace('_', ' ', isset($item['item_type']) ? $item['item_type'] : 'Unknown')) ?>
                                    </span>
                                </td>
                                <td><?= esc(isset($item['category_name']) ? $item['category_name'] : 'N/A') ?></td>
                                <td><?= esc(isset($item['uom']) ? $item['uom'] : 'N/A') ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="stock-indicator me-2" style="width: 80px;">
                                            <?php 
                                            $stockLevel = 0;
                                            if ((isset($item['max_stock']) ? $item['max_stock'] : 0) > 0) {
                                                $stockLevel = ((isset($item['current_stock']) ? $item['current_stock'] : 0) / $item['max_stock']) * 100;
                                            }
                                            
                                            $stockClass = 'stock-high';
                                            if ($stockLevel <= 30) $stockClass = 'stock-low';
                                            elseif ($stockLevel <= 70) $stockClass = 'stock-medium';
                                            ?>
                                            <div class="stock-fill <?= $stockClass ?>" style="width: <?= min($stockLevel, 100) ?>%"></div>
                                        </div>
                                        <small><?= isset($item['current_stock']) ? $item['current_stock'] : 0 ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="<?= (isset($item['current_stock']) ? $item['current_stock'] : 0) <= (isset($item['reorder_level']) ? $item['reorder_level'] : 0) ? 'text-danger fw-bold' : 'text-success' ?>">
                                        <?= isset($item['reorder_level']) ? $item['reorder_level'] : 0 ?>
                                    </span>
                                </td>
                                <td>₹<?= number_format(isset($item['standard_cost']) ? $item['standard_cost'] : 0, 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower(isset($item['status']) ? $item['status'] : 'inactive') ?>">
                                        <?= ucfirst(isset($item['status']) ? $item['status'] : 'Inactive') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/inventory/items/view/<?= $item['id'] ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/inventory/items/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/inventory/items/barcode/<?= $item['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Barcode">
                                            <i class="fas fa-barcode"></i>
                                        </a>
                                        <a href="/inventory/stock/stock-in?item_id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-success btn-action" title="Stock In">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <a href="/inventory/stock/stock-out?item_id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger btn-action" title="Stock Out">
                                            <i class="fas fa-minus"></i>
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
            $('#itemsTable').DataTable({
                pageLength: 25,
                order: [[1, 'asc']], // Sort by item name
                responsive: true,
                language: {
                    search: "Search items:",
                    lengthMenu: "Show _MENU_ items per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ items"
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
            const table = document.getElementById('itemsTable');
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
                    if (index < 9) { // Exclude actions column
                        let text = td.textContent.trim();
                        // Remove badges and stock indicators
                        text = text.replace(/[^\w\s%,.-₹]/g, '');
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
            link.setAttribute('download', 'items.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
