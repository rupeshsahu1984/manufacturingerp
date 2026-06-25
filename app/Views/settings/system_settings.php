<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sliders-h me-2"></i><?= esc($title ?? 'System Settings') ?>
        </h1>
        <a href="<?= base_url('settings/company') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-building me-1"></i>Edit company profile
        </a>
    </div>

    <?php if (! empty(session()->getFlashdata('success'))): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty(session()->getFlashdata('error'))): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Regional &amp; display</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Company</dt>
                        <dd class="col-sm-8"><?= esc($company['company_name'] ?? '—') ?></dd>
                        <dt class="col-sm-4">Legal name</dt>
                        <dd class="col-sm-8"><?= esc($company['legal_name'] ?? '—') ?></dd>
                        <dt class="col-sm-4">Currency</dt>
                        <dd class="col-sm-8"><?= esc($company['currency'] ?? 'INR') ?></dd>
                        <dt class="col-sm-4">Timezone</dt>
                        <dd class="col-sm-8"><?= esc($company['timezone'] ?? 'Asia/Kolkata') ?></dd>
                        <dt class="col-sm-4">Date / time format</dt>
                        <dd class="col-sm-8"><?= esc($company['date_format'] ?? 'd/m/Y') ?> &middot; <?= esc($company['time_format'] ?? 'H:i') ?></dd>
                        <dt class="col-sm-4">Fiscal year</dt>
                        <dd class="col-sm-8"><?= esc($company['fiscal_year_start'] ?? '') ?> → <?= esc($company['fiscal_year_end'] ?? '') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Branding</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Logo and favicon are managed from company profile.</p>
                    <?php if (! empty($company['logo_path'])): ?>
                        <p class="mb-1"><strong>Logo:</strong> <?= esc($company['logo_path']) ?></p>
                    <?php endif; ?>
                    <?php if (! empty($company['favicon_path'])): ?>
                        <p class="mb-0"><strong>Favicon:</strong> <?= esc($company['favicon_path']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
