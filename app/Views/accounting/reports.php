<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header text-center mb-4">
        <h1 class="h2 mb-2">
            <i class="fas fa-chart-bar me-3"></i>
            Financial Reports
        </h1>
        <p class="mb-0">View comprehensive financial reports</p>
    </div>

    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/accounting">Accounting</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>

    <!-- Report Type Selection -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/accounting/reports" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Report Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="profit_loss" <?= ($report_type ?? '') == 'profit_loss' ? 'selected' : '' ?>>Profit & Loss</option>
                        <option value="cash_flow" <?= ($report_type ?? '') == 'cash_flow' ? 'selected' : '' ?>>Cash Flow</option>
                        <option value="balance_sheet" <?= ($report_type ?? '') == 'balance_sheet' ? 'selected' : '' ?>>Balance Sheet</option>
                        <option value="customer_ledger" <?= ($report_type ?? '') == 'customer_ledger' ? 'selected' : '' ?>>Customer Ledger</option>
                        <option value="supplier_ledger" <?= ($report_type ?? '') == 'supplier_ledger' ? 'selected' : '' ?>>Supplier Ledger</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?? date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?? date('Y-m-t') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sync me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profit & Loss Report -->
    <?php if (($report_type ?? 'profit_loss') == 'profit_loss'): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>Profit & Loss Statement
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Total Revenue</th>
                        <td class="text-end">₹<?= number_format($profit_loss['revenue'] ?? 0, 2) ?></td>
                    </tr>
                    <tr>
                        <th>Total Expenses</th>
                        <td class="text-end">₹<?= number_format($profit_loss['expenses'] ?? 0, 2) ?></td>
                    </tr>
                    <tr>
                        <th>Gross Profit</th>
                        <td class="text-end"><strong>₹<?= number_format($profit_loss['gross_profit'] ?? 0, 2) ?></strong></td>
                    </tr>
                    <tr class="table-success">
                        <th>Net Profit</th>
                        <td class="text-end"><strong>₹<?= number_format($profit_loss['net_profit'] ?? 0, 2) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Cash Flow Report -->
    <?php if (($report_type ?? '') == 'cash_flow'): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>Cash Flow Statement
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Cash Inflows</th>
                        <td class="text-end text-success">₹<?= number_format($cash_flow['cash_in'] ?? 0, 2) ?></td>
                    </tr>
                    <tr>
                        <th>Cash Outflows</th>
                        <td class="text-end text-danger">₹<?= number_format($cash_flow['cash_out'] ?? 0, 2) ?></td>
                    </tr>
                    <tr class="table-info">
                        <th>Net Cash Flow</th>
                        <td class="text-end"><strong>₹<?= number_format($cash_flow['net_cash_flow'] ?? 0, 2) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Balance Sheet -->
    <?php if (($report_type ?? '') == 'balance_sheet'): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-balance-scale me-2"></i>Balance Sheet
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Total Assets</th>
                        <td class="text-end">₹<?= number_format($balance_sheet['assets'] ?? 0, 2) ?></td>
                    </tr>
                    <tr>
                        <th>Total Liabilities</th>
                        <td class="text-end">₹<?= number_format($balance_sheet['liabilities'] ?? 0, 2) ?></td>
                    </tr>
                    <tr class="table-primary">
                        <th>Equity</th>
                        <td class="text-end"><strong>₹<?= number_format($balance_sheet['equity'] ?? 0, 2) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

