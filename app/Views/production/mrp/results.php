<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand">MRP results</span>
            <a class="nav-link text-white" href="<?= base_url('production/mrp') ?>">Back</a>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <p class="text-muted">Period: <?= esc($startDate ?? '') ?> — <?= esc($endDate ?? '') ?></p>
        <?php if (empty($mrpResults)): ?>
            <div class="alert alert-info">No results. Select released BOMs and try again.</div>
        <?php else: ?>
            <?php foreach ($mrpResults as $bomId => $pack): ?>
                <div class="card mb-3">
                    <div class="card-header"><?= esc($pack['bom']['bom_number'] ?? 'BOM ' . $bomId) ?></div>
                    <div class="card-body">
                        <pre class="small mb-0 bg-light p-2 rounded"><?= esc(print_r($pack['requirements'] ?? [], true)) ?></pre>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
