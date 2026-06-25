<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-cogs me-3"></i>Production Settings</h1>
    <div class="header-actions">
        <a href="<?= base_url('production-settings/create') ?>" class="btn-primary">
            <i class="fas fa-plus"></i>
            Create Production Settings
        </a>
        <a href="<?= base_url('production-settings/export') ?>" class="btn btn-outline-primary">
            <i class="fas fa-download"></i>
            Export
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
        <div class="stat-label">Total Production Settings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value"><?= isset($stats['active']) ? $stats['active'] : 0 ?></div>
        <div class="stat-label">Active Settings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-edit"></i>
        </div>
        <div class="stat-value"><?= isset($stats['draft']) ? $stats['draft'] : 0 ?></div>
        <div class="stat-label">Draft Settings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-value"><?= count($finished_products ?? []) ?></div>
        <div class="stat-label">Finished Products</div>
    </div>
</div>

<!-- Material Management Section -->
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-boxes me-2"></i>Material Management</h5>
        <div>
            <a href="<?= base_url('product/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Add Material
            </a>
            <a href="<?= base_url('product') ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-list me-2"></i>View All Materials
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="quick-stat">
                    <div class="quick-stat-icon blue">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="quick-stat-content">
                        <div class="quick-stat-value"><?= isset($material_stats['total']) ? $material_stats['total'] : 0 ?></div>
                        <div class="quick-stat-label">Total Materials</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="quick-stat">
                    <div class="quick-stat-icon green">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="quick-stat-content">
                        <div class="quick-stat-value"><?= isset($material_stats['raw_materials']) ? $material_stats['raw_materials'] : 0 ?></div>
                        <div class="quick-stat-label">Raw Materials</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="quick-stat">
                    <div class="quick-stat-icon orange">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="quick-stat-content">
                        <div class="quick-stat-value"><?= isset($material_stats['packaging']) ? $material_stats['packaging'] : 0 ?></div>
                        <div class="quick-stat-label">Packaging</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="quick-stat">
                    <div class="quick-stat-icon red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="quick-stat-content">
                        <div class="quick-stat-value"><?= isset($material_stats['low_stock']) ? $material_stats['low_stock'] : 0 ?></div>
                        <div class="quick-stat-label">Low Stock Items</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Materials Table -->
        <div class="mt-4">
            <h6 class="mb-3"><i class="fas fa-clock me-2"></i>Recent Materials</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Material Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_materials)): ?>
                            <?php foreach ($recent_materials as $material): ?>
                                <tr>
                                    <td>
                                        <strong><?= $material['product_name'] ?></strong>
                                        <br><small class="text-muted"><?= $material['product_code'] ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $material['material_type'])) ?></span>
                                    </td>
                                    <td><?= isset($material['category_name']) ? $material['category_name'] : 'N/A' ?></td>
                                    <td>
                                        <?php if (isset($material['current_stock'])): ?>
                                            <span class="<?= $material['current_stock'] <= (isset($material['reorder_level']) ? $material['reorder_level'] : 0) ? 'text-danger' : 'text-success' ?>">
                                                <?= isset($material['current_stock']) ? $material['current_stock'] : 0 ?> <?= $material['unit'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $material['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                                            <?= ucfirst($material['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= base_url('product/edit/' . $material['id']) ?>" class="btn btn-sm btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('product/show/' . $material['id']) ?>" class="btn btn-sm btn-view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-3">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No materials found</p>
                                    <a href="<?= base_url('product/create') ?>" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus me-2"></i>Add First Material
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-section">
    <h5><i class="fas fa-filter me-2"></i>Filters</h5>
    <form method="GET" action="<?= base_url('production-settings') ?>" data-validate>
        <div class="filter-row">
            <div class="form-group">
                <label class="form-label">Search</label>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Search production settings..." value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" data-target="#productionSettingsTable">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Finished Product</label>
                <select name="finished_product_id" class="form-control">
                    <option value="">All Products</option>
                    <?php foreach ($finished_products as $product): ?>
                        <option value="<?= $product['id'] ?>" <?= (isset($filters['finished_product_id']) ? $filters['finished_product_id'] : '') == $product['id'] ? 'selected' : '' ?>><?= $product['product_name'] ?> (<?= $product['product_code'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="draft" <?= (isset($filters['status']) ? $filters['status'] : '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= base_url('production-settings') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Production Settings Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Production Settings List</h5>
        <div>
            <span class="badge bg-light text-dark"><?= count($boms ?? []) ?> settings</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="productionSettingsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Version</th>
                    <th>Materials</th>
                    <th>Waste %</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($boms)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No production settings found</p>
                            <a href="<?= base_url('production-settings/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Setting
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($boms as $bom): ?>
                        <tr data-selectable>
                            <td>
                                <div>
                                    <strong><?= isset($bom['finished_product_name']) ? $bom['finished_product_name'] : 'N/A' ?></strong>
                                    <br><small class="text-muted"><?= isset($bom['finished_product_code']) ? $bom['finished_product_code'] : '' ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $bom['version'] ?></span>
                            </td>
                            <td>
                                <?php 
                                $itemCount = count(isset($bom['items']) ? $bom['items'] : []);
                                echo $itemCount . ' material' . ($itemCount != 1 ? 's' : '');
                                ?>
                            </td>
                            <td>
                                <?php 
                                $totalWastePercentage = 0;
                                if (!empty($bom['items'])) {
                                    foreach ($bom['items'] as $item) {
                                        $totalWastePercentage += isset($item['waste_percentage']) ? $item['waste_percentage'] : 0;
                                    }
                                    $avgWastePercentage = $totalWastePercentage / count($bom['items']);
                                }
                                ?>
                                <span class="waste-percentage"><?= number_format($avgWastePercentage, 1) ?>%</span>
                            </td>
                            <td>
                                <span class="text-success">₹<?= number_format(isset($bom['total_cost']) ? $bom['total_cost'] : 0, 2) ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?= $bom['status'] === 'active' ? 'status-active' : ($bom['status'] === 'draft' ? 'status-draft' : 'status-inactive') ?>">
                                    <?= ucfirst($bom['status']) ?>
                                </span>
                            </td>
                            <td><?= isset($bom['created_by_name']) ? $bom['created_by_name'] : 'System' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('production-settings/show/' . $bom['id']) ?>" class="btn btn-sm btn-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('production-settings/edit/' . $bom['id']) ?>" class="btn btn-sm btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-toggle" title="Toggle Status" onclick="toggleStatus(<?= $bom['id'] ?>, 'production-settings')">
                                        <i class="fas fa-toggle-on"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-delete" title="Delete" onclick="deleteItem(<?= $bom['id'] ?>, 'production-settings')">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
<script>
    // Production settings specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Production Settings page loaded successfully!');
    });
</script>
<?= $this->endSection() ?> 