<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-file-alt me-3"></i>HR Reports</h1>
    <div class="header-actions">
        <a href="<?= base_url('hr') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> HR Home</a>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('hr/reports') ?>" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label">Report</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="employee" <?= ($report_type ?? '') === 'employee' ? 'selected' : '' ?>>Employees</option>
                    <option value="department" <?= ($report_type ?? '') === 'department' ? 'selected' : '' ?>>Departments</option>
                </select>
            </div>
        </form>
    </div>
</div>

<?php if (($report_type ?? 'employee') === 'employee'): ?>
    <div class="content-card">
        <div class="card-header"><h5 class="mb-0">Employee snapshot</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Code</th><th>Name</th><th>Department</th><th>Designation</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $e): ?>
                            <tr>
                                <td><?= esc($e['employee_code'] ?? '') ?></td>
                                <td><?= esc(trim(($e['first_name'] ?? '') . ' ' . ($e['last_name'] ?? ''))) ?></td>
                                <td><?= esc($e['department_name'] ?? '—') ?></td>
                                <td><?= esc($e['designation'] ?? '') ?></td>
                                <td><?= esc($e['status'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="content-card">
        <div class="card-header"><h5 class="mb-0">Departments</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr><th>Name</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td><?= esc($d['department_name'] ?? '') ?></td>
                                <td><?= esc($d['status'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
