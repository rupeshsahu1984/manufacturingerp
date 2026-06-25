<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-exclamation-triangle me-3"></i>Low Stock & Expiry Alerts</h1>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="card-header bg-warning">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Items</h5>
            </div>
            <div class="card-body">
                <?php if (empty($low_stock_items)): ?>
                    <p class="text-muted">No low stock items</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_items as $item): ?>
                                    <tr>
                                        <td><?= esc($item['item_name'] ?? 'N/A') ?></td>
                                        <td><span class="badge bg-danger"><?= $item['current_stock'] ?? 0 ?></span></td>
                                        <td><?= $item['reorder_level'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="card-header bg-danger">
                <h5><i class="fas fa-clock me-2"></i>Expiry Alerts (Next <?= $days ?> Days)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($expiry_alerts)): ?>
                    <p class="text-muted">No items expiring soon</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Batch</th>
                                    <th>Expiry Date</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expiry_alerts as $alert): ?>
                                    <tr>
                                        <td><?= esc($alert['item_name'] ?? 'N/A') ?></td>
                                        <td><?= esc($alert['batch_number'] ?? 'N/A') ?></td>
                                        <td><?= isset($alert['expiry_date']) ? date('M d, Y', strtotime($alert['expiry_date'])) : 'N/A' ?></td>
                                        <td><?= $alert['current_quantity'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>






