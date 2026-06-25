<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-users me-3"></i>HR — Employees</h1>
    <div class="header-actions">
        <a href="<?= base_url('hr/employee/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Employee
        </a>
        <a href="<?= base_url('hr/export/employees') ?>" class="btn btn-outline-success ms-2">
            <i class="fas fa-download"></i> Export
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

<!-- Filters -->
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2"></i>Search & Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('hr/employees') ?>">
            <div class="row">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Employees</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by name, code, email...">
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= (isset($filters['department']) ? $filters['department'] : '') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="terminated" <?= (isset($filters['status']) ? $filters['status'] : '') == 'terminated' ? 'selected' : '' ?>>Terminated</option>
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
                <p>Total Employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3><?= $stats['active'] ?></h3>
                <p>Active Employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="stat-card-body">
                <h3><?= $stats['inactive'] ?></h3>
                <p>Inactive Employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-danger text-white">
            <div class="stat-card-body">
                <h3><?= $stats['terminated'] ?></h3>
                <p>Terminated Employees</p>
            </div>
        </div>
    </div>
</div>

<!-- Employees Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Employees List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($employees)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No employees found</h5>
                <p class="text-muted">Create your first employee to get started.</p>
                <a href="<?= base_url('hr/employee/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Employee
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($employee['employee_code']) ?></strong>
                                </td>
                                <td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                                <td><?= esc($employee['email']) ?></td>
                                <td><?= esc($employee['phone']) ?></td>
                                <td><?= esc(isset($employee['department_name']) ? $employee['department_name'] : 'Not assigned') ?></td>
                                <td><?= esc($employee['designation']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if ($employee['status'] == 'active') $statusClass = 'success';
                                    elseif ($employee['status'] == 'inactive') $statusClass = 'warning';
                                    elseif ($employee['status'] == 'terminated') $statusClass = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= ucfirst($employee['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('hr/employee/edit/' . $employee['id']) ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteEmployee(<?= $employee['id'] ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<script>
function deleteEmployee(employeeId) {
    if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
        window.location.href = '<?= base_url('hr/employee/delete/') ?>' + employeeId;
    }
}
</script>
<?= $this->endSection() ?>
