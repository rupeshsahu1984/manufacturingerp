<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-tools me-3"></i>
            Maintenance Management
        </h1>
        <p class="mb-0">Manage equipment maintenance and repairs</p>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-plus me-2"></i>New Maintenance
                    </button>
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
                <div>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search maintenance...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Maintenance Records
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Maintenance ID</th>
                            <th>Equipment</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MNT001</td>
                            <td>Machine A</td>
                            <td>Preventive</td>
                            <td>2024-01-15</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>MNT002</td>
                            <td>Machine B</td>
                            <td>Repair</td>
                            <td>2024-01-14</td>
                            <td><span class="badge bg-warning">In Progress</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
