<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold"><i class="fas fa-tasks me-2"></i>PRODX — Work Orders</span>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="<?= base_url('dashboard') ?>">Dashboard</a>
                <a class="nav-link text-white" href="<?= base_url('production') ?>">Production</a>
                <a class="nav-link text-white active" href="<?= base_url('work-orders') ?>">Work Orders</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Work Orders</h1>
                <p class="text-muted mb-0">Plan and track manufacturing work orders</p>
            </div>
            <a href="<?= base_url('work-orders/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Create</a>
        </div>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>WO #</th>
                            <th>Item</th>
                            <th>BOM</th>
                            <th>Qty</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($workOrders)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No work orders yet. Create one or check database connectivity.</td></tr>
                        <?php else: ?>
                            <?php foreach ($workOrders as $wo): ?>
                                <tr>
                                    <td><?= esc($wo['wo_number'] ?? '') ?></td>
                                    <td><?= esc(($wo['item_code'] ?? '') . ' — ' . ($wo['item_name'] ?? '')) ?></td>
                                    <td><?= esc($wo['bom_number'] ?? '') ?> <?= isset($wo['revision']) ? esc('rev ' . $wo['revision']) : '' ?></td>
                                    <td><?= esc($wo['order_qty'] ?? '') ?> <?= esc($wo['uom'] ?? '') ?></td>
                                    <td><?= esc($wo['due_date'] ?? '') ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($wo['status'] ?? '') ?></span></td>
                                    <td><a class="btn btn-sm btn-outline-primary" href="<?= base_url('work-orders/view/' . (int) ($wo['id'] ?? 0)) ?>">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
