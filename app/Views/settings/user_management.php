<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users-cog me-2"></i><?= esc($title ?? 'User Management') ?>
        </h1>
    </div>

    <?php if (! empty(session()->getFlashdata('success'))): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty(session()->getFlashdata('error'))): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty($settings_error ?? '')): ?>
        <div class="alert alert-warning"><?= esc($settings_error) ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
            <span class="text-muted small">Use forms below to add users (requires matching <code>users</code> table columns).</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No users loaded.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= (int) ($u['id'] ?? 0) ?></td>
                                    <td><?= esc($u['username'] ?? '') ?></td>
                                    <td><?= esc($u['full_name'] ?? '') ?></td>
                                    <td><?= esc($u['email'] ?? '') ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($u['role'] ?? '') ?></span></td>
                                    <td><?= esc($u['status'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (! empty($departments)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Departments</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($departments as $d): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= esc($d['department_name'] ?? $d['name'] ?? 'Department') ?></span>
                            <?php if (isset($d['id'])): ?>
                                <span class="text-muted small">#<?= (int) $d['id'] ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
