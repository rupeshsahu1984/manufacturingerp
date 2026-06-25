<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-tags me-3"></i>Material Categories</h1>
    <div class="header-actions">
        <a href="<?= base_url('category/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Category
        </a>
        <a href="<?= base_url('help/categories') ?>" class="btn btn-outline-info ms-2" target="_blank">
            <i class="fas fa-question-circle"></i> Help Guide
        </a>
    </div>
</div>

<!-- Help Alert -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Need Help?</strong> 
    <a href="<?= base_url('help/categories') ?>" target="_blank" class="alert-link">View Category Management Guide</a> 
    or 
    <a href="<?= base_url('help/video-categories') ?>" target="_blank" class="alert-link">Watch Video Tutorial</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2"></i>Search & Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('category') ?>">
            <div class="row">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Categories</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by category name...">
                </div>
                <div class="col-md-3">
                    <label for="category_type" class="form-label">Category Type</label>
                    <select class="form-select" id="category_type" name="category_type">
                        <option value="">All Types</option>
                        <option value="raw_material" <?= (isset($filters['category_type']) ? $filters['category_type'] : '') == 'raw_material' ? 'selected' : '' ?>>Raw Material</option>
                        <option value="packaging" <?= (isset($filters['category_type']) ? $filters['category_type'] : '') == 'packaging' ? 'selected' : '' ?>>Packaging</option>
                        <option value="finished_goods" <?= (isset($filters['category_type']) ? $filters['category_type'] : '') == 'finished_goods' ? 'selected' : '' ?>>Finished Goods</option>
                        <option value="waste" <?= (isset($filters['category_type']) ? $filters['category_type'] : '') == 'waste' ? 'selected' : '' ?>>Waste</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="stat-card-body">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3><?= $stats['active'] ?></h3>
                <p>Active Categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="stat-card-body">
                <h3><?= $stats['by_type']['raw_material'] ?></h3>
                <p>Raw Material Categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="stat-card-body">
                <h3><?= $stats['by_type']['finished_goods'] ?></h3>
                <p>Finished Goods Categories</p>
            </div>
        </div>
    </div>
</div>

<!-- Categories List -->
<div class="content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list me-2"></i>Material Categories</h5>
        <div>
            <a href="<?= base_url('help/category-types') ?>" class="btn btn-sm btn-outline-info" target="_blank">
                <i class="fas fa-question-circle"></i> Category Types Guide
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No categories found</h5>
                <p class="text-muted">Create your first material category to get started.</p>
                <a href="<?= base_url('category/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Category
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Category Name</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($category['category_name']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getCategoryTypeColor($category['category_type']) ?>">
                                        <?= getCategoryTypeName($category['category_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= esc(isset($category['description']) ? $category['description'] : 'No description') ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= isset($category['product_count']) ? $category['product_count'] : 0 ?> products
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $category['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($category['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('category/show/' . $category['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('category/edit/' . $category['id']) ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Edit Category">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ((isset($category['product_count']) ? $category['product_count'] : 0) == 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteCategory(<?= $category['id'] ?>)" title="Delete Category">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Help Section -->
<div class="content-card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-lightbulb me-2"></i>Quick Help</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle text-primary"></i> Category Types</h6>
                <ul class="list-unstyled">
                    <li><strong>Raw Material:</strong> Basic materials used in production</li>
                    <li><strong>Packaging:</strong> Materials used for packaging finished goods</li>
                    <li><strong>Finished Goods:</strong> Completed products ready for sale</li>
                    <li><strong>Waste:</strong> Materials generated during production</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-exclamation-triangle text-warning"></i> Important Notes</h6>
                <ul class="list-unstyled">
                    <li>• Categories with products cannot be deleted</li>
                    <li>• Category names must be unique</li>
                    <li>• Inactive categories won't appear in dropdowns</li>
                    <li>• Category type determines where it appears in the system</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        window.location.href = '<?= base_url('category/delete/') ?>' + categoryId;
    }
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
<?= $this->endSection() ?>

<?php
function getCategoryTypeColor($type) {
    $colors = [
        'raw_material' => 'primary',
        'packaging' => 'info',
        'finished_goods' => 'success',
        'waste' => 'warning'
    ];
    return isset($colors[$type]) ? $colors[$type] : 'secondary';
}

function getCategoryTypeName($type) {
    $names = [
        'raw_material' => 'Raw Material',
        'packaging' => 'Packaging',
        'finished_goods' => 'Finished Goods',
        'waste' => 'Waste'
    ];
    return isset($names[$type]) ? $names[$type] : $type;
}
?>
