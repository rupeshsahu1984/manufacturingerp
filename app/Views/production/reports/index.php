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
            <span class="navbar-brand"><i class="fas fa-chart-bar me-2"></i>Production reports</span>
            <a class="nav-link text-white" href="<?= base_url('production') ?>">Production</a>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <h1 class="h3 mb-4">Production reports</h1>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">BOM statistics</h5>
                        <p class="card-text small text-muted"><?= empty($bomStats) ? 'No data or tables not ready.' : 'Summary loaded.' ?></p>
                        <?php if (! empty($bomStats) && is_array($bomStats)): ?>
                            <ul class="small mb-0">
                                <?php foreach ($bomStats as $k => $v): ?>
                                    <?php if (! is_array($v)): ?>
                                        <li><strong><?= esc((string) $k) ?>:</strong> <?= esc((string) $v) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-outline-primary mt-2" href="<?= base_url('production/reports/export/bom') ?>">Export BOM CSV</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Work order statistics</h5>
                        <?php if (! empty($workOrderStats) && is_array($workOrderStats)): ?>
                            <ul class="small mb-0">
                                <?php foreach ($workOrderStats as $k => $v): ?>
                                    <?php if (! is_array($v)): ?>
                                        <li><strong><?= esc((string) $k) ?>:</strong> <?= esc((string) $v) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="small text-muted mb-0">No data.</p>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-outline-primary mt-2" href="<?= base_url('production/reports/export/work_orders') ?>">Export WO CSV</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Analytics</h5>
                        <p class="small text-muted"><?= empty($productionAnalytics) ? 'No analytics data.' : 'Data available (see server logs for structure).' ?></p>
                        <a class="btn btn-sm btn-outline-primary mt-2" href="<?= base_url('production/reports/export/job_cards') ?>">Export job cards CSV</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
