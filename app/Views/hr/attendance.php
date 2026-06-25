<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-clock me-3"></i>
            Attendance Management
        </h1>
        <p class="mb-0">Manage employee attendance and time tracking</p>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-plus me-2"></i>Mark Attendance
                    </button>
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
                <div>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search attendance...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Attendance Records
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>2024-01-15</td>
                            <td>09:00 AM</td>
                            <td>06:00 PM</td>
                            <td>9 hours</td>
                            <td><span class="badge bg-success">Present</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>2024-01-15</td>
                            <td>09:30 AM</td>
                            <td>05:30 PM</td>
                            <td>8 hours</td>
                            <td><span class="badge bg-success">Present</span></td>
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
