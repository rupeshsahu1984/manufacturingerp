<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="fas fa-eye me-2"></i>
                Stock Adjustment Details
            </h1>
            <p class="mb-0">View adjustment information</p>
        </div>
    </div>

    <div class="container">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Details Card -->
        <div class="form-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Adjustment Information</h4>
                <div>
                    <?php if (($adjustment['status'] ?? 'pending') === 'pending'): ?>
                        <a href="<?= base_url('inventory/adjustments/edit/' . ($adjustment['id'] ?? '')) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="<?= base_url('inventory/adjustments/approve/' . ($adjustment['id'] ?? '')) ?>" class="btn btn-success" onclick="return confirm('Approve this adjustment?')">
                            <i class="fas fa-check me-2"></i>Approve
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('stock-adjustment') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Reference Number:</th>
                                    <td><strong><?= esc($adjustment['reference_number'] ?? 'N/A') ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Adjustment Date:</th>
                                    <td><?= isset($adjustment['transaction_date']) ? date('d/m/Y', strtotime($adjustment['transaction_date'])) : 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="status-badge status-<?= esc($adjustment['status'] ?? 'pending') ?>">
                                            <?= ucfirst($adjustment['status'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Adjustment Type:</th>
                                    <td>
                                        <span class="adjustment-badge adjustment-<?= esc($adjustment['source_type'] ?? 'adjustment') ?>">
                                            <?= ucwords(str_replace('_', ' ', $adjustment['source_type'] ?? 'Adjustment')) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Item & Warehouse</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Item:</th>
                                    <td><strong><?= esc($item['item_name'] ?? 'N/A') ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Item Code:</th>
                                    <td><?= esc($item['item_code'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Warehouse:</th>
                                    <td><strong><?= esc($warehouse['warehouse_name'] ?? 'N/A') ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td><strong><?= number_format($adjustment['quantity'] ?? 0, 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Financial Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Unit Cost:</th>
                                    <td>₹<?= number_format($adjustment['unit_cost'] ?? 0, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Total Value:</th>
                                    <td><strong>₹<?= number_format(($adjustment['quantity'] ?? 0) * ($adjustment['unit_cost'] ?? 0), 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Additional Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Notes:</th>
                                    <td><?= esc($adjustment['notes'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td><?= isset($adjustment['created_at']) ? date('d/m/Y H:i', strtotime($adjustment['created_at'])) : 'N/A' ?></td>
                                </tr>
                                <?php if (isset($adjustment['approved_at'])): ?>
                                <tr>
                                    <th>Approved At:</th>
                                    <td><?= date('d/m/Y H:i', strtotime($adjustment['approved_at'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        padding: 30px 0;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .adjustment-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .adjustment-increase { background-color: #d4edda; color: #155724; }
    .adjustment-decrease { background-color: #f8d7da; color: #721c24; }
    .adjustment-correction { background-color: #cce7ff; color: #004085; }
    .adjustment-damage { background-color: #fff3cd; color: #856404; }
    .adjustment-expiry { background-color: #e2e3e5; color: #383d41; }
    .adjustment-count { background-color: #d1ecf1; color: #0c5460; }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
</style>
<?= $this->endSection() ?>



