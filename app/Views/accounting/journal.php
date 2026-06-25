<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-book me-3"></i>
            General Journal
        </h1>
        <p class="mb-0">View all journal entries</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Journal</li>
        </ol>
    </nav>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="/accounting/journal/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Journal Entry
            </a>
        </div>
    </div>

    <!-- Journal Entries Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Journal Entries
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="journalTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($entries) && is_array($entries) && count($entries) > 0): ?>
                            <?php foreach ($entries as $entry): ?>
                                <tr>
                                    <td><?= esc($entry['date']) ?></td>
                                    <td><?= esc($entry['description']) ?></td>
                                    <td class="text-end text-success">
                                        <?= $entry['debit'] > 0 ? '₹' . number_format($entry['debit'], 2) : '-' ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        <?= $entry['credit'] > 0 ? '₹' . number_format($entry['credit'], 2) : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $entry['type'] == 'revenue' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($entry['type']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No journal entries found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

