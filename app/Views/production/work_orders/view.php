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
            <span class="navbar-brand fw-bold">WO <?= esc($workOrder['wo_number'] ?? '') ?></span>
            <a class="nav-link text-white" href="<?= base_url('work-orders') ?>">All work orders</a>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header">Details</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9"><span class="badge bg-secondary"><?= esc($workOrder['status'] ?? '') ?></span></dd>
                            <dt class="col-sm-3">Item</dt>
                            <dd class="col-sm-9"><?= esc(($workOrder['item_code'] ?? '') . ' — ' . ($workOrder['item_name'] ?? '')) ?></dd>
                            <dt class="col-sm-3">Quantity</dt>
                            <dd class="col-sm-9"><?= esc($workOrder['order_qty'] ?? '') ?> <?= esc($workOrder['uom'] ?? '') ?></dd>
                            <dt class="col-sm-3">Due</dt>
                            <dd class="col-sm-9"><?= esc($workOrder['due_date'] ?? '') ?></dd>
                            <dt class="col-sm-3">Warehouse</dt>
                            <dd class="col-sm-9"><?= esc($workOrder['warehouse_name'] ?? '') ?></dd>
                            <dt class="col-sm-3">Notes</dt>
                            <dd class="col-sm-9"><?= nl2br(esc($workOrder['notes'] ?? '')) ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="card shadow-sm mb-3">
                    <div class="card-header">Job cards</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>Op</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php if (empty($jobCards)): ?>
                                    <tr><td colspan="2" class="text-muted px-3 py-2">No job cards (start the work order to generate).</td></tr>
                                <?php else: ?>
                                    <?php foreach ($jobCards as $jc): ?>
                                        <tr>
                                            <td><?= esc($jc['operation_name'] ?? $jc['operation_id'] ?? '') ?></td>
                                            <td><?= esc($jc['status'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">Actions</div>
                    <div class="card-body d-grid gap-2">
                        <?php if (in_array($workOrder['status'] ?? '', ['released', 'draft'], true)): ?>
                            <form action="<?= base_url('work-orders/start/' . (int) $workOrder['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success w-100">Start work order</button>
                            </form>
                        <?php endif; ?>
                        <?php if (($workOrder['status'] ?? '') === 'in_progress'): ?>
                            <form action="<?= base_url('work-orders/complete/' . (int) $workOrder['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-warning w-100">Complete work order</button>
                            </form>
                        <?php endif; ?>
                        <a href="<?= base_url('production/job-cards') ?>" class="btn btn-outline-secondary">Open job cards (full)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
