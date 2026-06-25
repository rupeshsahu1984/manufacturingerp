<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-list me-2"></i>Chart of Accounts</h1>
    <div class="header-actions">
        <a href="<?= base_url('accounting/coa/create') ?>" class="btn btn-primary btn-sm">Add account</a>
        <a href="<?= base_url('accounting') ?>" class="btn btn-outline-secondary btn-sm">Accounting home</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts ?? [] as $row): ?>
                        <tr>
                            <td><?= esc($row['code'] ?? '') ?></td>
                            <td><?= esc($row['name'] ?? '') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($row['type'] ?? '') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
