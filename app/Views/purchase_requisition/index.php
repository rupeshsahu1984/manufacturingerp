<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Purchase Requisitions</h1>
        <p class="text-muted mb-0">Manage purchase requests and approvals</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-requisition/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Requisition
        </a>
    </div>
</div>

<!-- Requisitions Table -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All Requisitions</h5>
    </div>
    <div class="table-responsive">
        <table class="table" id="requisitionsTable">
            <thead>
                <tr>
                    <th>PR Number</th>
                    <th>Department</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Requested By</th>
                    <th>Required Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requisitions as $requisition): ?>
                <tr data-selectable>
                    <td><?= $requisition['pr_number'] ?></td>
                    <td><?= $requisition['department'] ?></td>
                    <td>
                        <span class="badge bg-<?= $requisition['priority'] === 'urgent' ? 'danger' : ($requisition['priority'] === 'high' ? 'warning' : 'info') ?>">
                            <?= ucfirst($requisition['priority']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?= strtolower($requisition['status']) ?>">
                            <?= ucfirst($requisition['status']) ?>
                        </span>
                    </td>
                    <td><?= $requisition['requested_by_name'] ?? $requisition['requested_by'] ?></td>
                    <td><?= date('M d, Y', strtotime($requisition['required_date'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?= base_url('purchase-requisition/edit/' . $requisition['id']) ?>" 
                               class="btn btn-sm btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-delete" 
                                    onclick="deleteItem(<?= $requisition['id'] ?>, 'purchase-requisition')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?> 