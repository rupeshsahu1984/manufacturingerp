<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-building me-3"></i>Department Master</h1>
    <div class="header-actions">
        <a href="<?= base_url('hr/department/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Department
        </a>
        <a href="<?= base_url('hr/export/departments') ?>" class="btn btn-outline-success ms-2">
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
        <form method="GET" action="<?= base_url('hr/departments') ?>">
            <div class="row">
                <div class="col-md-8">
                    <label for="search" class="form-label">Search Departments</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Search by department name...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= (isset($filters['status']) ? $filters['status'] : '') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (isset($filters['status']) ? $filters['status'] : '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
                <p>Total Departments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="stat-card-body">
                <h3><?= $stats['active'] ?></h3>
                <p>Active Departments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="stat-card-body">
                <h3><?= $stats['inactive'] ?></h3>
                <p>Inactive Departments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="stat-card-body">
                <h3><?= count($departments) ?></h3>
                <p>Displayed Results</p>
            </div>
        </div>
    </div>
</div>

<!-- Departments Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Departments List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($departments)): ?>
            <div class="text-center py-4">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No departments found</h5>
                <p class="text-muted">Create your first department to get started.</p>
                <a href="<?= base_url('hr/department/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Department
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $department): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($department['department_name']) ?></strong>
                                </td>
                                <td>
                                    <?php if ($department['description']): ?>
                                        <?= esc(substr($department['description'], 0, 100)) ?>
                                        <?= strlen($department['description']) > 100 ? '...' : '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">No description</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $department['status'] == 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($department['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($department['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('hr/department/edit/' . $department['id']) ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteDepartment(<?= $department['id'] ?>)" title="Delete">
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
function deleteDepartment(departmentId) {
    if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
        window.location.href = '<?= base_url('hr/department/delete/') ?>' + departmentId;
    }
}
</script>
<?= $this->endSection() ?>
