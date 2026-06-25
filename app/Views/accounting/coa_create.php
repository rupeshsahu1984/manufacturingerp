<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="header">
    <h1><i class="fas fa-plus me-2"></i>Create account</h1>
    <a href="<?= base_url('accounting/coa') ?>" class="btn btn-outline-secondary btn-sm">Back to COA</a>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted">Chart of accounts creation is stubbed. Wire this form to your <code>chart_of_accounts</code> table when ready.</p>
        <form action="<?= base_url('accounting/coa/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Account code</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="asset">Asset</option>
                    <option value="liability">Liability</option>
                    <option value="equity">Equity</option>
                    <option value="revenue">Revenue</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save (stub)</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
