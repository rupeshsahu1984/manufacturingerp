<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Create') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('manufacturing') ?>">← Manufacturing</a>
        </div>
    </nav>
    <div class="container py-4" style="max-width:640px">
        <h1 class="h4 mb-3">Create manufacturing order</h1>
        <p class="text-muted small">Use Production → Work Orders for full workflow, or submit below when your <code>manufacturing_orders</code> table is ready.</p>
        <form action="<?= base_url('manufacturing/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">BOM</label>
                <select name="bom_id" class="form-select" required>
                    <?php foreach ($boms ?? [] as $bom): ?>
                        <option value="<?= (int) ($bom['id'] ?? 0) ?>"><?= esc($bom['bom_number'] ?? $bom['id'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" step="0.01" name="production_quantity" class="form-control" required value="1">
            </div>
            <div class="mb-3">
                <label class="form-label">Planned start</label>
                <input type="date" name="planned_start_date" class="form-control" required value="<?= esc(date('Y-m-d')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Planned completion</label>
                <input type="date" name="planned_completion_date" class="form-control" required value="<?= esc(date('Y-m-d', strtotime('+7 days'))) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
