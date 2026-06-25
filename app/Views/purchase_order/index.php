<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Purchase Orders</h1>
        <p class="text-muted mb-0">Manage purchase orders and track procurement</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-order/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Purchase Order
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('purchase-order') ?>" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="PO number, supplier...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="draft" <?= (isset($filters['status']) ? $filters['status'] : '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="pending" <?= (isset($filters['status']) ? $filters['status'] : '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= (isset($filters['status']) ? $filters['status'] : '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="ordered" <?= (isset($filters['status']) ? $filters['status'] : '') === 'ordered' ? 'selected' : '' ?>>Ordered</option>
                    <option value="received" <?= (isset($filters['status']) ? $filters['status'] : '') === 'received' ? 'selected' : '' ?>>Received</option>
                    <option value="cancelled" <?= (isset($filters['status']) ? $filters['status'] : '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="supplier" class="form-label">Supplier</label>
                <select class="form-control" id="supplier" name="supplier_id">
                    <option value="">All Suppliers</option>
                    <?php foreach (isset($suppliers) ? $suppliers : [] as $supplier): ?>
                        <option value="<?= $supplier['id'] ?>" <?= (isset($filters['supplier_id']) ? $filters['supplier_id'] : '') == $supplier['id'] ? 'selected' : '' ?>>
                            <?= esc($supplier['supplier_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?= isset($stats['total']) ? $stats['total'] : 0 ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?= isset($stats['pending']) ? $stats['pending'] : 0 ?></h3>
                <p>Pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= isset($stats['received']) ? $stats['received'] : 0 ?></h3>
                <p>Received</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-content">
                <h3>₹<?= number_format(isset($stats['total_amount']) ? $stats['total_amount'] : 0) ?></h3>
                <p>Total Value</p>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Orders Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Purchase Orders
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportData('csv')">
                <i class="fas fa-download me-1"></i>Export CSV
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportData('pdf')">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($purchase_orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Purchase Orders Found</h5>
                <p class="text-muted">Create your first purchase order to get started.</p>
                <a href="<?= base_url('purchase-order/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Purchase Order
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Order Date</th>
                            <th>Expected Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchase_orders as $po): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($po['po_number']) ?></strong>
                                    <?php if ($po['is_urgent']): ?>
                                        <span class="badge bg-danger ms-1">Urgent</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= esc($po['supplier_name']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= esc($po['contact_person']) ?></small>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($po['order_date'])) ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($po['expected_date'])) ?>
                                    <?php if (strtotime($po['expected_date']) < time() && $po['status'] !== 'received'): ?>
                                        <span class="badge bg-warning ms-1">Overdue</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>₹<?= number_format($po['total_amount'] ?? 0, 2) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= isset($po['item_count']) ? $po['item_count'] : 0 ?> items</small>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'ordered' => 'primary',
                                        'received' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$po['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($po['status']) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('purchase-order/show/' . $po['id']) ?>" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($po['status'], ['draft', 'pending'])): ?>
                                            <a href="<?= base_url('purchase-order/edit/' . $po['id']) ?>" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($po['status'] === 'ordered'): ?>
                                            <a href="<?= base_url('purchase-order/receive/' . $po['id']) ?>" 
                                               class="btn btn-outline-success" title="Receive">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (in_array($po['status'], ['draft', 'pending'])): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deletePurchaseOrder(<?= $po['id'] ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function deletePurchaseOrder(id) {
    if (confirm('Are you sure you want to delete this purchase order? This action cannot be undone.')) {
        // Get CSRF token from meta tag or form
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="<?= csrf_token() ?>"]')?.value || '';
        
        fetch(`<?= base_url('purchase-order/delete') ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, might be redirect or error
                throw new Error('Invalid response format');
            }
        })
        .then(data => {
            if (data.success) {
                // Show success message
                if (data.message) {
                    alert(data.message);
                }
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete purchase order'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the purchase order. Please try again.');
        });
    }
}

function exportData(format) {
    // Get current filter parameters
    const currentUrl = new URL(window.location);
    const searchParams = new URLSearchParams();
    
    // Add export format
    searchParams.set('export', format);
    
    // Copy existing filter parameters
    const filters = ['search', 'status', 'supplier_id', 'date_from', 'date_to'];
    filters.forEach(filter => {
        const value = currentUrl.searchParams.get(filter);
        if (value) {
            searchParams.set(filter, value);
        }
    });
    
    // Redirect to export route with all parameters
    window.location.href = '<?= base_url("purchase-order/export") ?>?' + searchParams.toString();
}
</script>
<?= $this->endSection() ?>
