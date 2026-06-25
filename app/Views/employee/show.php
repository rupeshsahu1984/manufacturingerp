<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-user me-3"></i>Employee Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('employee/edit/' . $employee['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Employee
        </a>
        <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Employees
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

<!-- Employee Details -->
<div class="row">
    <div class="col-md-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Employee Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Employee Code</label>
                            <p class="form-control-plaintext"><?= esc($employee['employee_code']) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="form-control-plaintext"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext"><?= esc($employee['email']) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p class="form-control-plaintext"><?= esc($employee['phone']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <p class="form-control-plaintext">
                        <?= $employee['address'] ? esc($employee['address']) : '<em class="text-muted">No address provided</em>' ?>
                    </p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Department</label>
                            <p class="form-control-plaintext"><?= esc(isset($employee['department_name']) ? $employee['department_name'] : 'Not assigned') ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Designation</label>
                            <p class="form-control-plaintext"><?= esc($employee['designation']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Joining Date</label>
                            <p class="form-control-plaintext"><?= date('M d, Y', strtotime($employee['joining_date'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Basic Salary</label>
                            <p class="form-control-plaintext">₹<?= number_format($employee['salary'], 2) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <?php
                                $statusClass = 'secondary';
                                if ($employee['status'] == 'active') $statusClass = 'success';
                                elseif ($employee['status'] == 'inactive') $statusClass = 'warning';
                                elseif ($employee['status'] == 'terminated') $statusClass = 'danger';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst($employee['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Allowances</label>
                            <p class="form-control-plaintext">₹<?= number_format(isset($employee['allowances']) ? $employee['allowances'] : 0, 2) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deductions</label>
                            <p class="form-control-plaintext">₹<?= number_format(isset($employee['deductions']) ? $employee['deductions'] : 0, 2) ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if ($employee['username']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Linked User Account</label>
                    <p class="form-control-plaintext"><?= esc($employee['username']) ?></p>
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
                        <?= date('M d, Y H:i', strtotime($employee['created_at'])) ?>
                    </p>
                </div>
                
                <?php if (isset($employee['updated_at']) && $employee['updated_at']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext">
                        <?= date('M d, Y H:i', strtotime($employee['updated_at'])) ?>
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
                    <a href="<?= base_url('employee/edit/' . $employee['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Employee
                    </a>
                    
                    <?php if ($employee['status'] == 'active'): ?>
                        <button type="button" class="btn btn-warning" onclick="toggleStatus(<?= $employee['id'] ?>, 'inactive')">
                            <i class="fas fa-pause"></i> Deactivate
                        </button>
                    <?php elseif ($employee['status'] == 'inactive'): ?>
                        <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $employee['id'] ?>, 'active')">
                            <i class="fas fa-play"></i> Activate
                        </button>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-danger" onclick="deleteEmployee(<?= $employee['id'] ?>)">
                        <i class="fas fa-trash"></i> Delete Employee
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(employeeId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus == 'active' ? 'activate' : 'deactivate') + ' this employee?')) {
        // You can implement AJAX call here or redirect to a toggle endpoint
        window.location.href = '<?= base_url('employee/toggle-status/') ?>' + employeeId;
    }
}

function deleteEmployee(employeeId) {
    if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
        window.location.href = '<?= base_url('employee/delete/') ?>' + employeeId;
    }
}
</script>
<?= $this->endSection() ?>
