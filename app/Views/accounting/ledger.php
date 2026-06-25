<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-book-open me-3"></i>
            General Ledger
        </h1>
        <p class="mb-0">View ledger entries by account</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Ledger</li>
        </ol>
    </nav>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/accounting/ledger" class="row g-3">
                <div class="col-md-4">
                    <label for="account" class="form-label">Account</label>
                    <select class="form-select" id="account" name="account">
                        <option value="all">All Accounts</option>
                        <?php if (isset($accounts)): ?>
                            <?php foreach ($accounts as $acc): ?>
                                <option value="<?= $acc['code'] ?>" <?= ($selected_account ?? 'all') == $acc['code'] ? 'selected' : '' ?>>
                                    <?= $acc['code'] ?> - <?= $acc['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sync me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Entries Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Ledger Entries
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="ledgerTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($ledger_entries) && is_array($ledger_entries) && count($ledger_entries) > 0): ?>
                            <?php 
                            $running_balance = 0;
                            foreach ($ledger_entries as $entry): 
                                $running_balance += $entry['balance'];
                            ?>
                                <tr>
                                    <td><?= esc($entry['date']) ?></td>
                                    <td><?= esc($entry['description']) ?></td>
                                    <td class="text-end text-success">
                                        <?= $entry['debit'] > 0 ? '₹' . number_format($entry['debit'], 2) : '-' ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        <?= $entry['credit'] > 0 ? '₹' . number_format($entry['credit'], 2) : '-' ?>
                                    </td>
                                    <td class="text-end">
                                        <strong>₹<?= number_format($running_balance, 2) ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No ledger entries found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

