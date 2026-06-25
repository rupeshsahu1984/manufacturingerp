<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-clipboard-check me-3"></i>Stock Count</h1>
    <div class="header-actions">
        <a href="<?= base_url('stock-count/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Stock Count
        </a>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Stock Counts</h5>
    </div>
    <div class="card-body">
        <?php if (empty($stock_counts)): ?>
            <div class="text-center py-4">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No stock counts found</h5>
                <a href="<?= base_url('stock-count/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Stock Count
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Count Number</th>
                            <th>Warehouse</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Variance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stock_counts as $count): ?>
                            <tr>
                                <td><strong><?= esc($count['count_number'] ?? 'N/A') ?></strong></td>
                                <td><?= esc($count['warehouse_name'] ?? 'N/A') ?></td>
                                <td><?= isset($count['count_date']) ? date('M d, Y', strtotime($count['count_date'])) : 'N/A' ?></td>
                                <td><?= ucfirst($count['count_type'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-<?= ($count['count_status'] ?? 'draft') == 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($count['count_status'] ?? 'draft') ?>
                                    </span>
                                </td>
                                <td><?= number_format($count['variance_percentage'] ?? 0, 2) ?>%</td>
                                <td>
                                    <a href="<?= base_url('stock-count/view/' . $count['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>






