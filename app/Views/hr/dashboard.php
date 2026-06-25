<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-users me-3"></i>HR Management Dashboard</h1>
    <div class="header-actions">
        <a href="<?= base_url('hr/employees') ?>" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Manage Employees
        </a>
        <a href="<?= base_url('hr/departments') ?>" class="btn btn-outline-primary ms-2">
            <i class="fas fa-building"></i> Manage Departments
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

<!-- Key Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="stat-card-body">
                <h3><?= $total_employees ?></h3>
                <p>Total Employees</p>
                <small>Across all departments</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3><?= $active_employees ?></h3>
                <p>Active Employees</p>
                <small>Currently employed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="stat-card-body">
                <h3><?= $departments ?></h3>
                <p>Departments</p>
                <small>Organizational units</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="stat-card-body">
                <h3><?= $total_employees > 0 ? round(($active_employees / $total_employees) * 100, 1) : 0 ?>%</h3>
                <p>Retention Rate</p>
                <small>Employee retention</small>
            </div>
        </div>
    </div>
</div>

<!-- Department Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i>Employees by Department</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($department_stats)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Employees</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($department_stats as $dept): ?>
                                    <tr>
                                        <td><?= esc($dept['department_name']) ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= $dept['employee_count'] ?></span>
                                        </td>
                                        <td>
                                            <?= $total_employees > 0 ? round(($dept['employee_count'] / $total_employees) * 100, 1) : 0 ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-chart-pie fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No department data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2"></i>Employee Status Distribution</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($employee_stats)): ?>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <h4 class="text-success"><?= isset($employee_stats['active']) ? $employee_stats['active'] : 0 ?></h4>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <h4 class="text-warning"><?= isset($employee_stats['inactive']) ? $employee_stats['inactive'] : 0 ?></h4>
                                <small class="text-muted">Inactive</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <h4 class="text-danger"><?= isset($employee_stats['terminated']) ? $employee_stats['terminated'] : 0 ?></h4>
                                <small class="text-muted">Terminated</small>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No employee status data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Employees -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-user-clock me-2"></i>Recent Employees</h5>
                <a href="<?= base_url('hr/employees') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_employees)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Join Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_employees as $employee): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($employee['employee_id']) ?></strong>
                                        </td>
                                        <td><?= esc($employee['full_name']) ?></td>
                                        <td><?= esc(isset($employee['department_name']) ? $employee['department_name'] : 'N/A') ?></td>
                                        <td><?= esc($employee['position']) ?></td>
                                        <td><?= date('d M Y', strtotime($employee['join_date'])) ?></td>
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
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No employees found</h5>
                        <p class="text-muted">Add your first employee to get started.</p>
                        <a href="<?= base_url('hr/employee/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Employee
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('hr/employee/create') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus"></i> Add New Employee
                    </a>
                    <a href="<?= base_url('hr/department/create') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-building"></i> Create Department
                    </a>
                    <a href="<?= base_url('hr/reports') ?>" class="btn btn-outline-info">
                        <i class="fas fa-file-alt"></i> Generate Reports
                    </a>
                    <a href="<?= base_url('hr/analytics') ?>" class="btn btn-outline-success">
                        <i class="fas fa-chart-line"></i> View Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>HR Overview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <i class="fas fa-male fa-2x text-primary mb-2"></i>
                            <h6>Male Employees</h6>
                            <h4 class="text-primary"><?= isset($employee_stats['male']) ? $employee_stats['male'] : 0 ?></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <i class="fas fa-female fa-2x text-pink mb-2"></i>
                            <h6>Female Employees</h6>
                            <h4 class="text-pink"><?= isset($employee_stats['female']) ? $employee_stats['female'] : 0 ?></h4>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <i class="fas fa-graduation-cap fa-2x text-info mb-2"></i>
                            <h6>Average Experience</h6>
                            <h4 class="text-info"><?= isset($employee_stats['avg_experience']) ? $employee_stats['avg_experience'] : 0 ?> years</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <h6>Average Salary</h6>
                            <h4 class="text-success">₹<?= number_format(isset($employee_stats['avg_salary']) ? $employee_stats['avg_salary'] : 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-pink {
    color: #e83e8c !important;
}
</style>
<?= $this->endSection() ?>
