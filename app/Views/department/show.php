<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-building me-3"></i>Department Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('department/edit/' . $department['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Department
        </a>
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

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Department Details -->
<div class="row">
    <div class="col-md-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Department Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Department Name</label>
                    <p class="form-control-plaintext"><?= esc($department['department_name']) ?></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext">
                        <?= $department['description'] ? esc($department['description']) : '<em class="text-muted">No description provided</em>' ?>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <p class="form-control-plaintext">
                        <span class="badge bg-<?= $department['status'] == 'active' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($department['status']) ?>
                        </span>
                    </p>
                </div>
                
                <?php if (isset($employee_count)): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Employee Count</label>
                    <p class="form-control-plaintext">
                        <span class="badge bg-info"><?= $employee_count ?> employees</span>
                    </p>
                </div>
                <?php endif; ?>
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
                        <?= date('M d, Y H:i', strtotime($department['created_at'])) ?>
                    </p>
                </div>
                
                <?php if (isset($department['updated_at']) && $department['updated_at']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext">
                        <?= date('M d, Y H:i', strtotime($department['updated_at'])) ?>
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
                    <a href="<?= base_url('department/edit/' . $department['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Department
                    </a>
                    
                    <?php if ($department['status'] == 'active'): ?>
                        <button type="button" class="btn btn-warning" onclick="toggleStatus(<?= $department['id'] ?>, 'inactive')">
                            <i class="fas fa-pause"></i> Deactivate
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $department['id'] ?>, 'active')">
                            <i class="fas fa-play"></i> Activate
                        </button>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-danger" onclick="deleteDepartment(<?= $department['id'] ?>)">
                        <i class="fas fa-trash"></i> Delete Department
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(departmentId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus == 'active' ? 'activate' : 'deactivate') + ' this department?')) {
        // You can implement AJAX call here or redirect to a toggle endpoint
        window.location.href = '<?= base_url('department/toggle-status/') ?>' + departmentId;
    }
}

function deleteDepartment(departmentId) {
    if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
        window.location.href = '<?= base_url('department/delete/') ?>' + departmentId;
    }
}
</script>
<?= $this->endSection() ?>
