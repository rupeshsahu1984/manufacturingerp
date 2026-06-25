<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-cogs me-3"></i>Production Setting Details</h1>
    <div class="header-actions">
        <a href="<?= base_url('production-settings') ?>" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="<?= base_url('production-settings/edit/' . $bom['id']) ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <button type="button" class="btn btn-danger" onclick="deleteItem(<?= $bom['id'] ?>, 'production-settings')">
            <i class="fas fa-trash"></i> Delete
        </button>
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

<!-- BOM Details -->
<div class="row">
    <div class="col-md-8">
        <!-- Basic Information -->
        <div class="content-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>BOM Number:</strong> <?= $bom['bom_number'] ?></p>
                        <p><strong>Finished Product:</strong> <?= $bom['finished_product_name'] ?> (<?= $bom['finished_product_code'] ?>)</p>
                        <p><strong>Version:</strong> <?= $bom['version'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            <span class="status-badge <?= $bom['status'] === 'active' ? 'status-active' : ($bom['status'] === 'draft' ? 'status-draft' : 'status-inactive') ?>">
                                <?= ucfirst($bom['status']) ?>
                            </span>
                        </p>
                        <p><strong>Created By:</strong> <?= $bom['created_by_name'] ?></p>
                        <p><strong>Created At:</strong> <?= date('d M Y, H:i', strtotime($bom['created_at'])) ?></p>
                    </div>
                </div>
                <?php if (!empty($bom['description'])): ?>
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($bom['description'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Materials List -->
        <div class="content-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-list me-2"></i>Materials Required</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Waste %</th>
                                <th>Unit Cost</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bom['items'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No materials found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bom['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $item['material_name'] ?></strong>
                                            <br><small class="text-muted"><?= $item['material_code'] ?></small>
                                        </td>
                                        <td><?= number_format($item['quantity'], 2) ?></td>
                                        <td><?= $item['unit'] ?></td>
                                        <td>
                                            <span class="waste-percentage"><?= number_format($item['waste_percentage'], 1) ?>%</span>
                                        </td>
                                        <td>₹<?= number_format($item['unit_cost'], 2) ?></td>
                                        <td><strong>₹<?= number_format($item['total_cost'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Cost Summary -->
        <div class="content-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-calculator me-2"></i>Cost Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="stat-value"><?= count(isset($bom['items']) ? $bom['items'] : []) ?></div>
                            <div class="stat-label">Materials</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="stat-value">₹<?= number_format(isset($bom['total_cost']) ? $bom['total_cost'] : 0, 2) ?></div>
                            <div class="stat-label">Total Cost</div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-value">
                                <?php 
                                $avgWaste = 0;
                                if (!empty($bom['items'])) {
                                    $totalWaste = 0;
                                    foreach ($bom['items'] as $item) {
                                        $totalWaste += isset($item['waste_percentage']) ? $item['waste_percentage'] : 0;
                                    }
                                    $avgWaste = $totalWaste / count($bom['items']);
                                }
                                echo number_format($avgWaste, 1) . '%';
                                ?>
                            </div>
                            <div class="stat-label">Avg Waste</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon red">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <div class="stat-value">
                                <?php 
                                $totalQty = 0;
                                if (!empty($bom['items'])) {
                                    foreach ($bom['items'] as $item) {
                                        $totalQty += isset($item['quantity']) ? $item['quantity'] : 0;
                                    }
                                }
                                echo number_format($totalQty, 2);
                                ?>
                            </div>
                            <div class="stat-label">Total Qty</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-cogs me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('production-settings/edit/' . $bom['id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Production Setting
                    </a>
                    <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $bom['id'] ?>, 'production-settings')">
                        <i class="fas fa-toggle-on"></i> Toggle Status
                    </button>
                    <button type="button" class="btn btn-info" onclick="exportBOM(<?= $bom['id'] ?>)">
                        <i class="fas fa-download"></i> Export BOM
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteItem(<?= $bom['id'] ?>, 'production-settings')">
                        <i class="fas fa-trash"></i> Delete BOM
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function exportBOM(bomId) {
    // Implementation for exporting BOM
    showNotification('Export functionality will be implemented soon.', 'info');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Production Settings Details page loaded successfully!');
});
</script>
<?= $this->endSection() ?> 