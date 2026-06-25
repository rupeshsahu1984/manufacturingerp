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
            <span class="navbar-brand fw-bold"><i class="fas fa-clipboard-list me-2"></i>Job Cards</span>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="<?= base_url('production') ?>">Production</a>
                <a class="nav-link text-white" href="<?= base_url('work-orders') ?>">Work Orders</a>
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
        <h1 class="h3 mb-3">Job cards</h1>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>JC #</th>
                            <th>Work order</th>
                            <th>Operation</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jobCards)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No job cards. Start a work order to create job cards.</td></tr>
                        <?php else: ?>
                            <?php foreach ($jobCards as $jc): ?>
                                <tr>
                                    <td><?= esc($jc['job_card_number'] ?? '') ?></td>
                                    <td><?= esc($jc['wo_number'] ?? '') ?></td>
                                    <td><?= esc($jc['operation_name'] ?? '') ?></td>
                                    <td><?= esc(($jc['item_code'] ?? '') . ' ' . ($jc['item_name'] ?? '')) ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($jc['status'] ?? '') ?></span></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('production/job-cards/view/' . (int) ($jc['id'] ?? 0)) ?>">View</a>
                                    </td>
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
