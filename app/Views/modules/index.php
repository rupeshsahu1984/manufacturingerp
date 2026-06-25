<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1><?= $title ?></h1>
        <p class="text-muted mb-0">Manage <?= strtolower($title) ?> data</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url($moduleName . '/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New <?= $title ?>
        </a>
    </div>
</div>

<!-- Content Card -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All <?= $title ?></h5>
    </div>
    <div class="table-responsive">
        <table class="table" id="dataTable">
            <thead>
                <tr>
                    <?php 
                    if (!empty($items)) {
                        foreach (array_keys($items[0]) as $key) {
                            if ($key !== 'id') {
                                echo '<th>' . ucwords(str_replace('_', ' ', $key)) . '</th>';
                            }
                        }
                        echo '<th>Actions</th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr data-selectable>
                    <?php 
                    foreach ($item as $key => $value) {
                        if ($key !== 'id') {
                            if ($key === 'status') {
                                echo '<td><span class="status-badge status-' . strtolower($value) . '">' . $value . '</span></td>';
                            } elseif (strpos($key, 'amount') !== false || strpos($key, 'cost') !== false) {
                                echo '<td>₹' . number_format($value) . '</td>';
                            } elseif (strpos($key, 'date') !== false || strpos($key, 'time') !== false) {
                                echo '<td>' . date('M d, Y', strtotime($value)) . '</td>';
                            } elseif (strpos($key, 'rate') !== false || strpos($key, 'efficiency') !== false) {
                                echo '<td>' . $value . '%</td>';
                            } else {
                                echo '<td>' . $value . '</td>';
                            }
                        }
                    }
                    ?>
                    <td>
                        <div class="action-buttons">
                            <a href="<?= base_url($moduleName . '/edit/' . $item['id']) ?>" 
                               class="btn btn-sm btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-delete" 
                                    onclick="deleteItem(<?= $item['id'] ?>, '<?= $moduleName ?>')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?> 