<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Sales') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="page-header mb-4">
        <h3 class="page-title"><?= esc($page_title ?? 'Sales module') ?></h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('sales') ?>">Sales</a></li>
            <li class="breadcrumb-item active"><?= esc($page_title ?? '') ?></li>
        </ul>
    </div>

    <div class="alert alert-info">
        <strong>Working modules:</strong> use the links below for full CRUD. The grouped <code>/sales/…</code> hub for
        <code><?= esc($method ?? '') ?></code> is a navigation bridge until all screens are built here.
    </div>

    <div class="row g-3">
        <?php foreach ($links ?? [] as $pair): ?>
            <?php if (count($pair) >= 2): ?>
                <div class="col-md-4">
                    <a href="<?= esc($pair[1]) ?>" class="btn btn-outline-primary w-100 py-3"><?= esc($pair[0]) ?></a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
