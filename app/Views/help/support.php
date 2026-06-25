<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'Support') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <h1 class="h3 mb-3"><?= esc($page_title ?? 'Support') ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs ?? [] as $bc): ?>
                <?php if (! empty($bc['url'])): ?>
                    <li class="breadcrumb-item"><a href="<?= esc($bc['url']) ?>"><?= esc($bc['title']) ?></a></li>
                <?php else: ?>
                    <li class="breadcrumb-item active"><?= esc($bc['title']) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-2">For technical issues with ProDX ERP:</p>
            <ul>
                <li>Review <a href="<?= base_url('help') ?>">Help Center</a></li>
                <li>Contact your system administrator</li>
                <li>Check application logs under <code>writable/logs/</code></li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
