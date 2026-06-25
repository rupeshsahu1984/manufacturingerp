<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-calendar-alt me-3"></i>
            Leave Management
        </h1>
        <p class="mb-0">Manage employee leave requests and approvals</p>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-plus me-2"></i>Apply Leave
                    </button>
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
                <div>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search leave requests...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Requests List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Leave Requests
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>Sick Leave</td>
                            <td>2024-01-20</td>
                            <td>2024-01-22</td>
                            <td>3 days</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1">Approve</button>
                                <button class="btn btn-sm btn-outline-danger me-1">Reject</button>
                                <button class="btn btn-sm btn-outline-primary">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>Annual Leave</td>
                            <td>2024-01-25</td>
                            <td>2024-01-30</td>
                            <td>6 days</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
