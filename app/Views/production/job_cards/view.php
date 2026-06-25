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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Job card <?= esc($jobCard['job_card_number'] ?? '') ?></span>
            <a class="nav-link text-white" href="<?= base_url('production/job-cards') ?>">Back</a>
        </div>
    </nav>
    <div class="container py-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><?= esc($jobCard['status'] ?? '') ?></dd>
                    <dt class="col-sm-4">Work order</dt>
                    <dd class="col-sm-8"><?= esc($jobCard['wo_number'] ?? '') ?></dd>
                    <dt class="col-sm-4">Operation</dt>
                    <dd class="col-sm-8"><?= esc($jobCard['operation_name'] ?? '') ?></dd>
                    <dt class="col-sm-4">Planned qty</dt>
                    <dd class="col-sm-8"><?= esc($jobCard['planned_qty'] ?? '') ?></dd>
                </dl>
                <div class="d-flex gap-2 mt-3">
                    <?php if (($jobCard['status'] ?? '') === 'released'): ?>
                        <form action="<?= base_url('production/job-cards/start/' . (int) $jobCard['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-sm">Start</button>
                        </form>
                    <?php endif; ?>
                    <?php if (($jobCard['status'] ?? '') === 'in_progress'): ?>
                        <form action="<?= base_url('production/job-cards/complete/' . (int) $jobCard['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-warning btn-sm">Complete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
