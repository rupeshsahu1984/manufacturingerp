<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-plus me-3"></i>Create New Category</h1>
    <div class="header-actions">
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

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Create Form -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-tag me-2"></i>Category Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= base_url('category/store') ?>">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category_name" name="category_name" 
                               value="<?= old('category_name') ?>" required>
                        <div class="form-text">Enter a unique name for this category</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_type" class="form-label">Category Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_type" name="category_type" required>
                            <option value="">Select Category Type</option>
                            <option value="raw_material" <?= old('category_type') == 'raw_material' ? 'selected' : '' ?>>Raw Material</option>
                            <option value="packaging" <?= old('category_type') == 'packaging' ? 'selected' : '' ?>>Packaging</option>
                            <option value="finished_goods" <?= old('category_type') == 'finished_goods' ? 'selected' : '' ?>>Finished Goods</option>
                            <option value="waste" <?= old('category_type') == 'waste' ? 'selected' : '' ?>>Waste</option>
                        </select>
                        <div class="form-text">Choose the type of materials this category represents</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="Enter category description..."><?= old('description') ?></textarea>
                <div class="form-text">Optional description for this category</div>
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <div class="form-text">Set the initial status for this category</div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('category') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate category code if needed
    const categoryNameInput = document.getElementById('category_name');
    const categoryTypeSelect = document.getElementById('category_type');
    
    // Add any client-side validation or functionality here
});
</script>
<?= $this->endSection() ?>
