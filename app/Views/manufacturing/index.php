<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manufacturing') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand"><i class="fas fa-industry me-2"></i>Manufacturing</span>
            <a class="nav-link text-white" href="<?= base_url('dashboard') ?>">Dashboard</a>
        </div>
    </nav>
    <div class="container py-4">
        <?php if (! empty($load_error)): ?>
            <div class="alert alert-warning"><?= esc($load_error) ?></div>
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <p class="display-6"><?= is_countable($pendingOrders ?? null) ? count($pendingOrders) : 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Active</h5>
                        <p class="display-6"><?= is_countable($activeProductions ?? null) ? count($activeProductions) : 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <p class="display-6"><?= is_countable($completedProductions ?? null) ? count($completedProductions) : 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
        <a href="<?= base_url('manufacturing/create') ?>" class="btn btn-primary mt-4">Create manufacturing order</a>
        <a href="<?= base_url('production') ?>" class="btn btn-outline-secondary mt-4 ms-2">Production module</a>
    </div>
</body>
</html>
