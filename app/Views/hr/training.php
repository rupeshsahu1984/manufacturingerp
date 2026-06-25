<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-graduation-cap me-3"></i>
            Training Management
        </h1>
        <p class="mb-0">Manage employee training and development programs</p>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-plus me-2"></i>Create Training
                    </button>
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
                <div>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search training...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Training Programs
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Training Name</th>
                            <th>Instructor</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Safety Training</td>
                            <td>Safety Officer</td>
                            <td>2024-01-20</td>
                            <td>2024-01-22</td>
                            <td>25</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Software Training</td>
                            <td>IT Manager</td>
                            <td>2024-01-25</td>
                            <td>2024-01-27</td>
                            <td>15</td>
                            <td><span class="badge bg-warning">In Progress</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
