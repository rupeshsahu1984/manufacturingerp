<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Purchase Bills</h1>
        <p class="text-muted mb-0">Manage purchase bills and payments</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-bill/overdue') ?>" class="btn btn-outline-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>Overdue Bills
        </a>
        <a href="<?= base_url('purchase-bill/export') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-download me-2"></i>Export
        </a>
        <a href="<?= base_url('purchase-bill/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Bill
        </a>
    </div>
</div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
                <div class="stat-label">Total Bills</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?= isset($stats['paid']) ? $stats['paid'] : 0 ?></div>
                <div class="stat-label">Paid Bills</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?= isset($stats['received']) ? $stats['received'] : 0 ?></div>
                <div class="stat-label">Pending Payment</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-value"><?= isset($stats['overdue']) ? $stats['overdue'] : 0 ?></div>
                <div class="stat-label">Overdue Bills</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-value">₹<?= number_format(isset($stats['outstanding_amount']) ? $stats['outstanding_amount'] : 0) ?></div>
                <div class="stat-label">Outstanding Amount</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form data-validate method="GET" action="<?= base_url('purchase-bill') ?>">
                <div class="filter-row align-items-end">
                    <div class="filter-field">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search bills..." value="<?= isset($filters['search']) ? esc($filters['search']) : '' ?>">
                    </div>
                    <div class="filter-field">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">All Suppliers</option>
                            <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>" <?= (isset($filters['supplier_id']) ? $filters['supplier_id'] : '') == $supplier['id'] ? 'selected' : '' ?>><?= $supplier['supplier_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="draft" <?= (isset($filters['status']) ? $filters['status'] : '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="received" <?= (isset($filters['status']) ? $filters['status'] : '') === 'received' ? 'selected' : '' ?>>Received</option>
                            <option value="paid" <?= (isset($filters['status']) ? $filters['status'] : '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="overdue" <?= (isset($filters['status']) ? $filters['status'] : '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
                    </div>
                    <div class="filter-field">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
                    </div>
                    <div class="filter-actions">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                            <a href="<?= base_url('purchase-bill') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bills Table -->
        <div class="content-card">
            <div class="card-header">
                <h5 class="h5">All Purchase Bills</h5>
                <div class="d-flex gap-2">
                    <span class="text-muted">Total Outstanding: ₹<?= number_format(isset($stats['outstanding_amount']) ? $stats['outstanding_amount'] : 0) ?></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr data-selectable>
                            <th>Bill Number</th>
                            <th>Supplier</th>
                            <th>Bill Date</th>
                            <th>Due Date</th>
                            <th>Invoice Number</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bills)): ?>
                        <tr data-selectable>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <h5>No Purchase Bills Found</h5>
                                    <p>Create your first purchase bill to get started.</p>
                                    <a href="<?= base_url('purchase-bill/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Create Bill
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($bills as $bill): ?>
                        <tr data-selectable>
                            <td>
                                <strong><?= $bill['bill_number'] ?></strong>
                            </td>
                            <td>
                                <div>
                                    <strong><?= $bill['supplier_name'] ?></strong>
                                    <br><small class="text-muted"><?= $bill['supplier_code'] ?></small>
                                </div>
                            </td>
                            <td>
                                <?= date('d M Y', strtotime($bill['bill_date'])) ?>
                            </td>
                            <td>
                                <?= $bill['due_date'] ? date('d M Y', strtotime($bill['due_date'])) : '-' ?>
                            </td>
                            <td>
                                <?= $bill['invoice_number'] ?: '-' ?>
                            </td>
                            <td>
                                <strong>₹<?= number_format($bill['total_amount']) ?></strong>
                            </td>
                            <td>
                                ₹<?= number_format($bill['paid_amount']) ?>
                            </td>
                            <td>
                                <?php $outstanding = $bill['total_amount'] - $bill['paid_amount']; ?>
                                <span class="<?= $outstanding > 0 ? 'text-danger' : 'text-success' ?>">
                                    ₹<?= number_format($outstanding) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $bill['status'] ?>">
                                    <?= ucfirst($bill['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('purchase-bill/show/' . $bill['id']) ?>" 
                                       class="btn btn-view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('purchase-bill/edit/' . $bill['id']) ?>" 
                                       class="btn btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('purchase-bill/download/' . $bill['id']) ?>" 
                                       class="btn btn-outline-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-toggle" 
                                            onclick="recordPayment(<?= $bill['id'] ?>, <?= $bill['total_amount'] - $bill['paid_amount'] ?>)" 
                                            title="Record Payment">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <button type="button" class="btn btn-delete" 
                                            onclick="deleteItem(<?= $bill['id'] ?>, 'Bill')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form data-validate id="paymentForm">
                        <div class="mb-3">
                            <label class="form-label">Payment Amount</label>
                            <input type="number" id="paymentAmount" class="form-control" step="0.01" required>
                            <div class="form-text">Enter the payment amount</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    
    
<?= $this->endSection() ?> 
