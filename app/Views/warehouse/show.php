<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-warehouse me-3"></i>Warehouse Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('warehouse/edit/' . $warehouse['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Warehouse
        </a>
        <a href="<?= base_url('warehouse') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Warehouses
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

<!-- Warehouse Details -->
<div class="row">
    <div class="col-md-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Warehouse Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Warehouse Code</label>
                            <p class="form-control-plaintext"><?= esc($warehouse['warehouse_code']) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Warehouse Name</label>
                            <p class="form-control-plaintext"><?= esc($warehouse['warehouse_name']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Location</label>
                    <p class="form-control-plaintext"><?= esc($warehouse['location']) ?></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Manager</label>
                            <p class="form-control-plaintext">
                                <?php if ($warehouse['first_name'] && $warehouse['last_name']): ?>
                                    <?= esc($warehouse['first_name'] . ' ' . $warehouse['last_name']) ?>
                                    <br><small class="text-muted">Email: <?= esc($warehouse['email']) ?></small>
                                    <br><small class="text-muted">Phone: <?= esc($warehouse['phone']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Not assigned</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?= $warehouse['status'] == 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($warehouse['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-clock me-2"></i>Timestamps</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Created At</label>
                    <p class="form-control-plaintext">
                        <?= date('M d, Y H:i', strtotime($warehouse['created_at'])) ?>
                    </p>
                </div>
                
                <?php if (isset($warehouse['updated_at']) && $warehouse['updated_at']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="form-control-plaintext">
                        <?= date('M d, Y H:i', strtotime($warehouse['updated_at'])) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="content-card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-tools me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('warehouse/edit/' . $warehouse['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Warehouse
                    </a>
                    
                    <?php if ($warehouse['status'] == 'active'): ?>
                        <button type="button" class="btn btn-warning" onclick="toggleStatus(<?= $warehouse['id'] ?>, 'inactive')">
                            <i class="fas fa-pause"></i> Deactivate
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $warehouse['id'] ?>, 'active')">
                            <i class="fas fa-play"></i> Activate
                        </button>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-danger" onclick="deleteWarehouse(<?= $warehouse['id'] ?>)">
                        <i class="fas fa-trash"></i> Delete Warehouse
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(warehouseId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus == 'active' ? 'activate' : 'deactivate') + ' this warehouse?')) {
        // You can implement AJAX call here or redirect to a toggle endpoint
        window.location.href = '<?= base_url('warehouse/toggle-status/') ?>' + warehouseId;
    }
}

function deleteWarehouse(warehouseId) {
    if (confirm('Are you sure you want to delete this warehouse? This action cannot be undone.')) {
        window.location.href = '<?= base_url('warehouse/delete/') ?>' + warehouseId;
    }
}
</script>
<?= $this->endSection() ?>
