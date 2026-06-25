<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
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
        
        .stock-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745;
            transition: transform 0.3s ease;
        }
        
        .stock-card:hover {
            transform: translateY(-5px);
        }
        
        .transaction-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .transaction-stock_in { background-color: #d4edda; color: #155724; }
        .transaction-stock_out { background-color: #f8d7da; color: #721c24; }
        .transaction-transfer { background-color: #cce7ff; color: #004085; }
        .transaction-adjustment { background-color: #fff3cd; color: #856404; }
        
        .stock-level-indicator {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .stock-level-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .level-high { background: linear-gradient(90deg, #28a745, #20c997); }
        .level-medium { background: linear-gradient(90deg, #ffc107, #ff9800); }
        .level-low { background: linear-gradient(90deg, #dc3545, #f44336); }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .stock-grid {
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
        
        .refresh-spinning {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        #refreshBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-chart-line me-3"></i>
                Stock Management & Tracking
            </h1>
            <p class="mb-0">Real-time stock monitoring, movement tracking, and inventory analytics</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('inventory') ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Inventory Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Stock Management</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $totalItems = 0;
                        foreach ($stock_movements as $movement) {
                            $totalItems += isset($movement['quantity']) ? $movement['quantity'] : 0;
                        }
                        echo $totalItems;
                        ?>
                    </div>
                    <div class="text-muted">Total Stock Items</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $lowStockCount = 0;
                        foreach ($stock_movements as $movement) {
                            if ((isset($movement['current_stock']) ? $movement['current_stock'] : 0) <= (isset($movement['reorder_level']) ? $movement['reorder_level'] : 0)) $lowStockCount++;
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
                        $totalValue = 0;
                        foreach ($stock_movements as $movement) {
                            $totalValue += (isset($movement['current_stock']) ? $movement['current_stock'] : 0) * (isset($movement['unit_cost']) ? $movement['unit_cost'] : 0);
                        }
                        echo '₹' . number_format($totalValue, 2);
                        ?>
                    </div>
                    <div class="text-muted">Total Stock Value</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card text-center">
                    <div class="stats-number">
                        <?php 
                        $warehouses = [];
                        foreach ($stock_movements as $movement) {
                            $warehouses[] = isset($movement['warehouse_name']) ? $movement['warehouse_name'] : 'Unknown';
                        }
                        echo count(array_unique($warehouses));
                        ?>
                    </div>
                    <div class="text-muted">Active Warehouses</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h5>
            <form method="GET" action="<?= base_url('stock') ?>" class="row g-3" id="stockFilterForm">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Item name, code, barcode...">
                </div>
                <div class="col-md-2">
                    <label for="item_id" class="form-label">Item</label>
                    <select class="form-select" id="item_id" name="item_id">
                        <option value="">All Items</option>
                        <?php if (isset($items) && is_array($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <?php if (is_array($item) && isset($item['id']) && isset($item['item_name'])): ?>
                                    <option value="<?= $item['id'] ?>" <?= (isset($_GET['item_id']) ? $_GET['item_id'] : '') == $item['id'] ? 'selected' : '' ?>>
                                        <?= esc($item['item_name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No items available</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="warehouse_id" class="form-label">Warehouse</label>
                    <select class="form-select" id="warehouse_id" name="warehouse_id">
                        <option value="">All Warehouses</option>
                        <?php if (isset($warehouses) && is_array($warehouses)): ?>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <?php if (is_array($warehouse) && isset($warehouse['id']) && isset($warehouse['warehouse_name'])): ?>
                                    <option value="<?= $warehouse['id'] ?>" <?= (isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : '') == $warehouse['id'] ? 'selected' : '' ?>>
                                        <?= esc($warehouse['warehouse_name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No warehouses available</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="transaction_type" class="form-label">Type</label>
                    <select class="form-select" id="transaction_type" name="transaction_type">
                        <option value="">All Types</option>
                        <option value="stock_in" <?= (isset($_GET['transaction_type']) ? $_GET['transaction_type'] : '') === 'stock_in' ? 'selected' : '' ?>>Stock In</option>
                        <option value="stock_out" <?= (isset($_GET['transaction_type']) ? $_GET['transaction_type'] : '') === 'stock_out' ? 'selected' : '' ?>>Stock Out</option>
                        <option value="transfer" <?= (isset($_GET['transaction_type']) ? $_GET['transaction_type'] : '') === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                        <option value="adjustment" <?= (isset($_GET['transaction_type']) ? $_GET['transaction_type'] : '') === 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
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
                <button type="button" class="btn btn-outline-secondary me-2" id="refreshBtn" onclick="refreshStock()">
                    <i class="fas fa-refresh me-2" id="refreshIcon"></i>Refresh
                </button>
                <button type="button" class="btn btn-outline-info me-2" onclick="exportToCSV()">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
                <a href="<?= base_url('stock/scan') ?>" class="btn btn-outline-warning me-2">
                    <i class="fas fa-barcode me-2"></i>Barcode Scan
                </a>
                <a href="<?= base_url('stock/rfid') ?>" class="btn btn-outline-success me-2">
                    <i class="fas fa-rfid me-2"></i>RFID Scan
                </a>
            </div>
            <div>
                <a href="<?= base_url('stock/stock-in') ?>" class="btn btn-success me-2">
                    <i class="fas fa-plus me-2"></i>Stock In
                </a>
                <a href="<?= base_url('stock/stock-out') ?>" class="btn btn-danger">
                    <i class="fas fa-minus me-2"></i>Stock Out
                </a>
            </div>
        </div>

        <!-- Grid View -->
        <div id="grid-view" class="stock-grid">
            <?php if (isset($stock_movements) && is_array($stock_movements)): ?>
                <?php foreach ($stock_movements as $movement): ?>
                    <div class="stock-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?= esc(isset($movement['item_name']) ? $movement['item_name'] : 'N/A') ?></h5>
                                <small class="text-muted"><?= esc(isset($movement['item_code']) ? $movement['item_code'] : 'N/A') ?></small>
                            </div>
                            <div class="text-end">
                                <span class="transaction-badge transaction-<?= strtolower(isset($movement['transaction_type']) ? $movement['transaction_type'] : 'unknown') ?>">
                                    <?= ucwords(str_replace('_', ' ', isset($movement['transaction_type']) ? $movement['transaction_type'] : 'Unknown')) ?>
                                </span>
                                <br>
                                <small class="text-muted"><?= esc(isset($movement['warehouse_name']) ? $movement['warehouse_name'] : 'N/A') ?></small>
                            </div>
                        </div>
                        
                        <!-- Stock Details -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Current Stock</small>
                                <strong><?= isset($movement['current_stock']) ? $movement['current_stock'] : 0 ?> <?= esc(isset($movement['uom']) ? $movement['uom'] : '') ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Quantity</small>
                                <strong><?= isset($movement['quantity']) ? $movement['quantity'] : 0 ?> <?= esc(isset($movement['uom']) ? $movement['uom'] : '') ?></strong>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Unit Cost</small>
                                <strong>₹<?= number_format(isset($movement['unit_cost']) ? $movement['unit_cost'] : 0, 2) ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Total Value</small>
                                <strong>₹<?= number_format((isset($movement['quantity']) ? $movement['quantity'] : 0) * (isset($movement['unit_cost']) ? $movement['unit_cost'] : 0), 2) ?></strong>
                            </div>
                        </div>
                        
                        <!-- Stock Level Indicator -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Stock Level</small>
                                <small class="text-muted">
                                    <?= isset($movement['current_stock']) ? $movement['current_stock'] : 0 ?>/<?= isset($movement['max_stock']) ? $movement['max_stock'] : 0 ?> 
                                    <?= esc(isset($movement['uom']) ? $movement['uom'] : '') ?>
                                </small>
                            </div>
                            <div class="stock-level-indicator">
                                <?php 
                                $stockLevel = 0;
                                if ((isset($movement['max_stock']) ? $movement['max_stock'] : 0) > 0) {
                                    $stockLevel = ((isset($movement['current_stock']) ? $movement['current_stock'] : 0) / $movement['max_stock']) * 100;
                                }
                                
                                $levelClass = 'level-high';
                                if ($stockLevel <= 30) $levelClass = 'level-low';
                                elseif ($stockLevel <= 70) $levelClass = 'level-medium';
                                ?>
                                <div class="stock-level-fill <?= $levelClass ?>" style="width: <?= min($stockLevel, 100) ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="<?= base_url('stock/view/' . $movement['id']) ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= base_url('stock/edit/' . (isset($movement['id']) && !empty($movement['id']) ? intval($movement['id']) : '0')) ?>" 
                                   class="btn btn-sm btn-outline-warning btn-action" 
                                   title="Edit Stock Record #<?= isset($movement['id']) ? $movement['id'] : 'N/A' ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= base_url('stock/history/' . (isset($movement['item_id']) ? $movement['item_id'] : '')) ?>" class="btn btn-sm btn-outline-secondary btn-action" title="History">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                            <div>
                                <a href="<?= base_url('stock/quick-stock-in?item_id=' . (isset($movement['item_id']) ? $movement['item_id'] : '') . (isset($movement['warehouse_id']) ? '&warehouse_id=' . $movement['warehouse_id'] : '')) ?>" class="btn btn-sm btn-outline-success btn-action" title="Quick Stock In (+1)" onclick="return confirm('Add 1 unit to stock?')">
                                    <i class="fas fa-plus"></i> Stock In
                                </a>
                                <a href="<?= base_url('stock/quick-stock-out?item_id=' . (isset($movement['item_id']) ? $movement['item_id'] : '') . (isset($movement['warehouse_id']) ? '&warehouse_id=' . $movement['warehouse_id'] : '')) ?>" class="btn btn-sm btn-outline-danger btn-action" title="Quick Stock Out (-1)" onclick="return confirm('Remove 1 unit from stock?')">
                                    <i class="fas fa-minus"></i> Stock Out
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h5>No stock movements found</h5>
                    <p>Start by adding stock or performing stock operations</p>
                    <a href="<?= base_url('stock/stock-in') ?>" class="btn btn-success me-2">
                        <i class="fas fa-plus me-2"></i>Stock In
                    </a>
                    <a href="<?= base_url('stock/stock-out') ?>" class="btn btn-danger">
                        <i class="fas fa-minus me-2"></i>Stock Out
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Table View (Hidden by default) -->
        <div id="table-view" class="table-container" style="display: none;">
            <table id="stockTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Warehouse</th>
                        <th>Current Stock</th>
                        <th>Transaction Type</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Total Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($stock_movements) && is_array($stock_movements)): ?>
                        <?php foreach ($stock_movements as $movement): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(isset($movement['item_code']) ? $movement['item_code'] : 'N/A') ?></strong>
                                    <?php if (!empty($movement['barcode'])): ?>
                                        <br><small class="text-muted"><?= esc($movement['barcode']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc(isset($movement['item_name']) ? $movement['item_name'] : 'N/A') ?></td>
                                <td><?= esc(isset($movement['warehouse_name']) ? $movement['warehouse_name'] : 'N/A') ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="stock-level-indicator me-2" style="width: 80px;">
                                            <?php 
                                            $stockLevel = 0;
                                            if ((isset($movement['max_stock']) ? $movement['max_stock'] : 0) > 0) {
                                                $stockLevel = ((isset($movement['current_stock']) ? $movement['current_stock'] : 0) / $movement['max_stock']) * 100;
                                            }
                                            
                                            $levelClass = 'level-high';
                                            if ($stockLevel <= 30) $levelClass = 'level-low';
                                            elseif ($stockLevel <= 70) $levelClass = 'level-medium';
                                            ?>
                                            <div class="stock-level-fill <?= $levelClass ?>" style="width: <?= min($stockLevel, 100) ?>%"></div>
                                        </div>
                                        <small><?= isset($movement['current_stock']) ? $movement['current_stock'] : 0 ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="transaction-badge transaction-<?= strtolower(isset($movement['transaction_type']) ? $movement['transaction_type'] : 'unknown') ?>">
                                        <?= ucwords(str_replace('_', ' ', isset($movement['transaction_type']) ? $movement['transaction_type'] : 'Unknown')) ?>
                                    </span>
                                </td>
                                <td><?= isset($movement['quantity']) ? $movement['quantity'] : 0 ?> <?= esc(isset($movement['uom']) ? $movement['uom'] : '') ?></td>
                                <td>₹<?= number_format(isset($movement['unit_cost']) ? $movement['unit_cost'] : 0, 2) ?></td>
                                <td>₹<?= number_format((isset($movement['quantity']) ? $movement['quantity'] : 0) * (isset($movement['unit_cost']) ? $movement['unit_cost'] : 0), 2) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('stock/view/' . $movement['id']) ?>" class="btn btn-sm btn-outline-info btn-action" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('stock/edit/' . (isset($movement['id']) && !empty($movement['id']) ? intval($movement['id']) : '0')) ?>" 
                                           class="btn btn-sm btn-outline-warning btn-action" 
                                           title="Edit Stock Record #<?= isset($movement['id']) ? $movement['id'] : 'N/A' ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('stock/history/' . (isset($movement['item_id']) ? $movement['item_id'] : '')) ?>" class="btn btn-sm btn-outline-secondary btn-action" title="History">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="<?= base_url('stock/quick-stock-in?item_id=' . (isset($movement['item_id']) ? $movement['item_id'] : '') . (isset($movement['warehouse_id']) ? '&warehouse_id=' . $movement['warehouse_id'] : '')) ?>" class="btn btn-sm btn-outline-success btn-action" title="Quick Stock In (+1)" onclick="return confirm('Add 1 unit to stock?')">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <a href="<?= base_url('stock/quick-stock-out?item_id=' . (isset($movement['item_id']) ? $movement['item_id'] : '') . (isset($movement['warehouse_id']) ? '&warehouse_id=' . $movement['warehouse_id'] : '')) ?>" class="btn btn-sm btn-outline-danger btn-action" title="Quick Stock Out (-1)" onclick="return confirm('Remove 1 unit from stock?')">
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable for table view
            $('#stockTable').DataTable({
                pageLength: 25,
                order: [[3, 'desc']], // Sort by current stock
                responsive: true,
                language: {
                    search: "Search stock:",
                    lengthMenu: "Show _MENU_ items per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ items"
                }
            });
            
            // Add keyboard shortcut for refresh (Ctrl+R or F5)
            $(document).keydown(function(e) {
                // Prevent default F5 behavior and use our refresh function
                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                    e.preventDefault();
                    refreshStock();
                }
            });
        });
        
        // Refresh stock data while preserving filters
        function refreshStock() {
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');
            
            // Disable button and show spinning icon
            refreshBtn.disabled = true;
            refreshIcon.classList.add('refresh-spinning');
            
            // Get current URL parameters to preserve filters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Determine the correct base path (works for both /stock and /inventory/stock)
            let basePath = window.location.pathname;
            // If path ends with /stock, use it; otherwise try to extract it
            if (!basePath.endsWith('/stock') && !basePath.endsWith('stock')) {
                // Fallback: use /stock as default
                basePath = '/stock';
            }
            
            const currentUrl = basePath + (urlParams.toString() ? '?' + urlParams.toString() : '');
            
            // Reload the page with current filters
            setTimeout(function() {
                window.location.href = currentUrl;
            }, 300); // Small delay to show the spinning animation
        }

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
            const table = document.getElementById('stockTable');
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
                    if (index < 8) { // Exclude actions column
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
            link.setAttribute('download', 'stock_movements.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

<?= $this->endSection() ?>
