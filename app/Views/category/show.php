<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-tag me-3"></i>Category Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('category/edit/' . $category['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Category
        </a>
        <a href="<?= base_url('category') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>
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

<!-- Category Details -->
<div class="row">
    <div class="col-md-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Category Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Name</label>
                            <p class="form-control-plaintext"><?= esc($category['category_name']) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Type</label>
                            <p class="form-control-plaintext">
                                <?php
                                $typeLabels = [
                                    'raw_material' => 'Raw Material',
                                    'packaging' => 'Packaging',
                                    'finished_goods' => 'Finished Goods',
                                    'waste' => 'Waste'
                                ];
                                echo $typeLabels[$category['category_type']] ?? $category['category_type'];
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext">
                        <?= $category['description'] ? esc($category['description']) : '<em class="text-muted">No description provided</em>' ?>
                    </p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?= $category['status'] == 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($category['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usage Count</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info"><?= $usage_count ?> items</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-clock me-2"></i>Timestamps</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Created At</label>
                    <p class="form-control-plaintext">
                        <?= date('M d, Y H:i', strtotime($category['created_at'])) ?>
                    </p>
                </div>
                
                <?php if (isset($category['updated_at']) && $category['updated_at']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext">
                        <?= date('M d, Y H:i', strtotime($category['updated_at'])) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="content-card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-tools me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('category/edit/' . $category['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Category
                    </a>
                    
                    <?php if ($category['status'] == 'active'): ?>
                        <button type="button" class="btn btn-warning" onclick="toggleStatus(<?= $category['id'] ?>, 'inactive')">
                            <i class="fas fa-pause"></i> Deactivate
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $category['id'] ?>, 'active')">
                            <i class="fas fa-play"></i> Activate
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($usage_count == 0): ?>
                        <button type="button" class="btn btn-danger" onclick="deleteCategory(<?= $category['id'] ?>)">
                            <i class="fas fa-trash"></i> Delete Category
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-danger" disabled title="Cannot delete category that is in use">
                            <i class="fas fa-trash"></i> Delete Category
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(categoryId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus == 'active' ? 'activate' : 'deactivate') + ' this category?')) {
        window.location.href = '<?= base_url('category/toggle-status/') ?>' + categoryId;
    }
}

function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        window.location.href = '<?= base_url('category/delete/') ?>' + categoryId;
    }
}
</script>
<?= $this->endSection() ?>
