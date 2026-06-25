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
            <span class="navbar-brand">MRP</span>
            <a class="nav-link text-white" href="<?= base_url('production') ?>">Production</a>
        </div>
    </nav>
    <div class="container py-4">
        <h1 class="h3 mb-3">Material requirements planning</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('production/mrp/run') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Start date</label>
                            <input type="date" name="start_date" class="form-control" required value="<?= esc(date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End date</label>
                            <input type="date" name="end_date" class="form-control" required value="<?= esc(date('Y-m-d', strtotime('+30 days'))) ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">BOMs</label>
                            <select name="bom_ids[]" class="form-select" multiple size="8" required>
                                <?php foreach ($boms as $bom): ?>
                                    <option value="<?= (int) $bom['id'] ?>"><?= esc($bom['bom_number'] ?? $bom['id']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Run MRP</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
