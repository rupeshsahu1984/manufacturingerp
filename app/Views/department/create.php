<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-plus me-3"></i>Create New Department</h1>
    <div class="header-actions">
        <a href="<?= base_url('department') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Departments
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
        <h5><i class="fas fa-building me-2"></i>Department Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= base_url('department/store') ?>">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="department_name" class="form-label">Department Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="department_name" name="department_name" 
                       value="<?= old('department_name') ?>" required>
                <div class="form-text">Enter a unique name for this department</div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" 
                          placeholder="Enter department description..."><?= old('description') ?></textarea>
                <div class="form-text">Optional description for this department</div>
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <div class="form-text">Set the initial status for this department</div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('department') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Department
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any client-side validation or functionality here
});
</script>
<?= $this->endSection() ?>
