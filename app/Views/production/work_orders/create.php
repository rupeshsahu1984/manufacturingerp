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
            <span class="navbar-brand fw-bold"><i class="fas fa-plus me-2"></i>Create Work Order</span>
            <a class="nav-link text-white" href="<?= base_url('work-orders') ?>">Back to list</a>
        </div>
    </nav>
    <div class="container py-4" style="max-width: 720px;">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <div><?= esc(is_array($err) ? implode(' ', $err) : $err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('work-orders/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Work order # (optional)</label>
                        <input type="text" name="work_order_number" class="form-control" value="<?= esc(old('work_order_number', $workOrderNumber ?? '')) ?>" placeholder="Auto-generated if empty">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">BOM <span class="text-danger">*</span></label>
                        <select name="bom_id" class="form-select" required>
                            <option value="">— Select released BOM —</option>
                            <?php foreach ($boms as $bom): ?>
                                <option value="<?= (int) $bom['id'] ?>" <?= old('bom_id') == $bom['id'] ? 'selected' : '' ?>>
                                    <?= esc($bom['bom_number'] ?? $bom['id']) ?> — rev <?= esc($bom['revision'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="0.0001" name="order_qty" class="form-control" required value="<?= esc(old('order_qty', '1')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UOM</label>
                        <input type="text" name="uom" class="form-control" value="<?= esc(old('uom')) ?>" placeholder="Leave blank to use item default">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" required value="<?= esc(old('due_date', date('Y-m-d', strtotime('+7 days')))) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select" required>
                            <option value="">— Select —</option>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= (int) $wh['id'] ?>" <?= old('warehouse_id') == $wh['id'] ? 'selected' : '' ?>>
                                    <?= esc($wh['warehouse_name'] ?? $wh['id']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <?php foreach (['low', 'normal', 'high', 'urgent', 'critical'] as $p): ?>
                                <option value="<?= $p ?>" <?= old('priority', 'normal') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"><?= esc(old('notes')) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= base_url('work-orders') ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
