<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-plus me-3"></i>Create New Warehouse</h1>
    <div class="header-actions">
        <a href="<?= base_url('warehouse') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Warehouses
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
        <h5><i class="fas fa-warehouse me-2"></i>Warehouse Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= base_url('warehouse/store') ?>">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="warehouse_code" class="form-label">Warehouse Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="warehouse_code" name="warehouse_code" 
                               value="<?= old('warehouse_code') ?>" required>
                        <div class="form-text">Enter a unique code for this warehouse</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="warehouse_name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="warehouse_name" name="warehouse_name" 
                               value="<?= old('warehouse_name') ?>" required>
                        <div class="form-text">Enter the name of the warehouse</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                <textarea class="form-control" id="location" name="location" rows="3" 
                          placeholder="Enter warehouse location details..." required><?= old('location') ?></textarea>
                <div class="form-text">Enter the complete address or location details</div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Warehouse Manager</label>
                        <select class="form-select" id="manager_id" name="manager_id">
                            <option value="">Select Manager (Optional)</option>
                            <?php foreach ($managers as $manager): ?>
                                <option value="<?= $manager['id'] ?>" <?= old('manager_id') == $manager['id'] ? 'selected' : '' ?>>
                                    <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?> 
                                    (<?= esc($manager['employee_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Assign a manager to this warehouse (optional)</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <div class="form-text">Set the initial status for this warehouse</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('warehouse') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Warehouse
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate warehouse code if needed
    const warehouseCodeInput = document.getElementById('warehouse_code');
    const warehouseNameInput = document.getElementById('warehouse_name');
    
    // Add any client-side validation or functionality here
});
</script>
<?= $this->endSection() ?>
