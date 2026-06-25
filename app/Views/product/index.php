<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Products & Materials</h1>
        <p class="text-muted mb-0">Manage products, materials and inventory</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('product/export') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-download me-2"></i>Export
        </a>
        <a href="<?= base_url('product/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Product
        </a>
    </div>
</div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
                <div class="stat-label">Total Materials</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?= isset($stats['active']) ? $stats['active'] : 0 ?></div>
                <div class="stat-label">Active Materials</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="stat-value"><?= isset($stats['raw_material']) ? $stats['raw_material'] : 0 ?></div>
                <div class="stat-label">Raw Materials</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-value"><?= isset($stats['packaging']) ? $stats['packaging'] : 0 ?></div>
                <div class="stat-label">Packaging</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-industry"></i>
                </div>
                <div class="stat-value"><?= isset($stats['finished_goods']) ? $stats['finished_goods'] : 0 ?></div>
                <div class="stat-label">Finished Goods</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-recycle"></i>
                </div>
                <div class="stat-value"><?= isset($stats['waste']) ? $stats['waste'] : 0 ?></div>
                <div class="stat-label">Waste Materials</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <h5><i class="fas fa-filter me-2"></i>Filters</h5>
            <form data-validate method="GET" action="<?= base_url('product') ?>">
                <div class="filter-row">
                    <div class="form-group">
                        <label class="form-label">Search</label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="search-input" class="form-control" placeholder="Search materials..." value="<?= isset($filters['search']) ? $filters['search'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Material Type</label>
                        <select name="material_type" class="form-control">
                            <option value="">All Types</option>
                            <option value="raw_material" <?= (isset($filters['material_type']) ? $filters['material_type'] : '') === 'raw_material' ? 'selected' : '' ?>>Raw Material</option>
                            <option value="packaging" <?= (isset($filters['material_type']) ? $filters['material_type'] : '') === 'packaging' ? 'selected' : '' ?>>Packaging</option>
                            <option value="finished_goods" <?= (isset($filters['material_type']) ? $filters['material_type'] : '') === 'finished_goods' ? 'selected' : '' ?>>Finished Goods</option>
                            <option value="waste" <?= (isset($filters['material_type']) ? $filters['material_type'] : '') === 'waste' ? 'selected' : '' ?>>Waste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            <!-- Categories will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Recyclable</label>
                        <select name="is_recyclable" class="form-control">
                            <option value="">All</option>
                            <option value="1" <?= (isset($filters['is_recyclable']) ? $filters['is_recyclable'] : '') === '1' ? 'selected' : '' ?>>Recyclable</option>
                            <option value="0" <?= (isset($filters['is_recyclable']) ? $filters['is_recyclable'] : '') === '0' ? 'selected' : '' ?>>Non-Recyclable</option>
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
                            <a href="<?= base_url('product') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-list me-2"></i>Material List</h5>
                <div>
                    <span class="badge bg-light text-dark"><?= count($products) ?> materials</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr data-selectable>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Material Type</th>
                            <th>Unit</th>
                            <th>Unit Price</th>
                            <th>HSN Code</th>
                            <th>GST Rate</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr data-selectable>
                                <td colspan="14" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No materials found</p>
                                    <a href="<?= base_url('product/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Material
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr data-selectable>
                                    <td>
                                        <strong><?= $product['product_code'] ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= $product['product_name'] ?></strong>
                                            <?php if ($product['description']): ?>
                                                <br><small class="text-muted"><?= $product['description'] ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= isset($product['category_name']) ? $product['category_name'] : '-' ?></td>
                                    <td>
                                        <span class="material-type-badge material-type-<?= $product['material_type'] ?>">
                                            <?= ucwords(str_replace('_', ' ', $product['material_type'])) ?>
                                        </span>
                                    </td>
                                    <td><?= $product['unit'] ?></td>
                                    <td>
                                        <?php if ($product['unit_price'] > 0): ?>
                                            <span class="text-success">₹<?= number_format($product['unit_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= isset($product['hsn_code']) ? $product['hsn_code'] : '-' ?></td>
                                    <td><?= isset($product['gst_rate']) ? $product['gst_rate'] : '-' ?>%</td>
                                    <td><?= isset($product['cgst_rate']) ? $product['cgst_rate'] : '-' ?>%</td>
                                    <td><?= isset($product['sgst_rate']) ? $product['sgst_rate'] : '-' ?>%</td>
                                    <td><?= isset($product['igst_rate']) ? $product['igst_rate'] : '-' ?>%</td>
                                    <td>
                                        <?php if ($product['reorder_level'] > 0): ?>
                                            <span class="text-warning"><?= $product['reorder_level'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $product['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                                            <?= ucfirst($product['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= base_url('product/show/' . $product['id']) ?>" class="btn btn-sm btn-view" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('product/edit/' . $product['id']) ?>" class="btn btn-sm btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-toggle" title="Toggle Status" onclick="toggleStatus(<?= $product['id'] ?>)">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                            <a href="<?= base_url('product/delete/' . $product['id']) ?>" class="btn btn-sm btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this material?')">
                                                <i class="fas fa-trash"></i>
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
    </div>
    </div>

    
    
<?= $this->endSection() ?> 