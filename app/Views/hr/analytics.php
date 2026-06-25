<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-chart-line me-3"></i>HR Analytics</h1>
    <div class="header-actions">
        <a href="<?= base_url('hr') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> HR Home</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="stat-card-body">
                <h3><?= (int) ($employee_stats['total'] ?? 0) ?></h3>
                <p>Total employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3><?= (int) ($employee_stats['active'] ?? 0) ?></h3>
                <p>Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="stat-card-body">
                <h3><?= esc(number_format((float) ($employee_stats['avg_salary'] ?? 0), 2)) ?></h3>
                <p>Avg. salary</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-secondary text-white">
            <div class="stat-card-body">
                <h3><?= (int) ($department_stats['total'] ?? 0) ?></h3>
                <p>Departments</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header"><h5 class="mb-0">Headcount by department</h5></div>
            <div class="card-body">
                <?php if (empty($department_breakdown)): ?>
                    <p class="text-muted mb-0">No data yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Department</th><th>Active staff</th></tr></thead>
                            <tbody>
                                <?php foreach ($department_breakdown as $row): ?>
                                    <tr>
                                        <td><?= esc($row['department_name'] ?? '—') ?></td>
                                        <td><?= (int) ($row['employee_count'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header"><h5 class="mb-0">Recent hires</h5></div>
            <div class="card-body">
                <?php if (empty($recent_employees)): ?>
                    <p class="text-muted mb-0">No recent employees.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recent_employees as $e): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?= esc(trim(($e['first_name'] ?? '') . ' ' . ($e['last_name'] ?? ''))) ?></span>
                                <small class="text-muted"><?= esc($e['employee_code'] ?? '') ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
