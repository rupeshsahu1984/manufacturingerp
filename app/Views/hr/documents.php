<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-file-alt me-3"></i>
            Document Management
        </h1>
        <p class="mb-0">Manage employee documents and files</p>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-upload me-2"></i>Upload Document
                    </button>
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
                <div>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search documents...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Employee Documents
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Document Type</th>
                            <th>File Name</th>
                            <th>Upload Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>ID Proof</td>
                            <td>aadhar_card.pdf</td>
                            <td>2024-01-15</td>
                            <td><span class="badge bg-success">Verified</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Download</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>Resume</td>
                            <td>resume_jane.pdf</td>
                            <td>2024-01-14</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">View</button>
                                <button class="btn btn-sm btn-outline-secondary me-1">Download</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
