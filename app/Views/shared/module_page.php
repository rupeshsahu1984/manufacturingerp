<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1"><?= esc($page_title ?? $title ?? 'Module') ?></h1>
                    <p class="text-muted mb-0"><?= esc($message ?? 'This module page is available.') ?></p>
                </div>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <?php if (! empty($summary) && is_array($summary)): ?>
        <div class="row g-3">
            <?php foreach ($summary as $label => $value): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small"><?= esc((string) $label) ?></div>
                            <div class="h4 mb-0"><?= esc((string) $value) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
