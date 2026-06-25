<?= $this->extend('layouts/main') ?>\n\n<?= $this->section('content') ?>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header bg-gradient-warning text-white p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-eye me-2"></i>View Purchase Requisition
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?= base_url('purchase-requisition') ?>" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="<?= base_url('purchase-requisition/print/' . $pr['id']) ?>" class="btn btn-light" target="_blank">
                        <i class="fas fa-print me-2"></i>Print
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- PR Details -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>PR Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr data-selectable>
                                <td><strong>PR Number:</strong></td>
                                <td><?= $pr['pr_number'] ?></td>
                            </tr>
                            <tr data-selectable>
                                <td><strong>Department:</strong></td>
                                <td><?= $pr['department'] ?></td>
                            </tr>
                            <tr data-selectable>
                                <td><strong>Requested By:</strong></td>
                                <td><?= $pr['requested_by_name'] ?></td>
                            </tr>
                            <tr data-selectable>
                                <td><strong>Priority:</strong></td>
                                <td>
                                    <?php
                                    $priorityClass = '';
                                    switch ($pr['priority']) {
                                        case 'low': $priorityClass = 'badge bg-secondary'; break;
                                        case 'medium': $priorityClass = 'badge bg-info'; break;
                                        case 'high': $priorityClass = 'badge bg-warning'; break;
                                        case 'urgent': $priorityClass = 'badge bg-danger'; break;
                                    }
                                    ?>
                                    <span class="<?= $priorityClass ?>"><?= ucfirst($pr['priority']) ?></span>
                                </td>
                            </tr>
                            <tr data-selectable>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($pr['status']) {
                                        case 'draft': $statusClass = 'badge bg-secondary'; break;
                                        case 'pending': $statusClass = 'badge bg-warning'; break;
                                        case 'approved': $statusClass = 'badge bg-success'; break;
                                        case 'rejected': $statusClass = 'badge bg-danger'; break;
                                        case 'ordered': $statusClass = 'badge bg-info'; break;
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= ucfirst($pr['status']) ?></span>
                                </td>
                            </tr>
                            <tr data-selectable>
                                <td><strong>Required Date:</strong></td>
                                <td><?= date('d/m/Y', strtotime($pr['required_date'])) ?></td>
                            </tr>
                            <tr data-selectable>
                                <td><strong>Created Date:</strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($pr['created_at'])) ?></td>
                            </tr>
                            <?php if ($pr['remarks']): ?>
                            <tr data-selectable>
                                <td><strong>Remarks:</strong></td>
                                <td><?= nl2br(htmlspecialchars($pr['remarks'])) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Status Management -->
                <?php if (in_array($pr['status'], ['draft', 'pending'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Status Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($pr['status'] == 'draft'): ?>
                                <button type="button" class="btn btn-warning" onclick="updateStatus('pending')">
                                    <i class="fas fa-paper-plane me-2"></i>Submit for Approval
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($pr['status'] == 'pending'): ?>
                                <button type="button" class="btn btn-success" onclick="updateStatus('approved')">
                                    <i class="fas fa-check me-2"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger" onclick="updateStatus('rejected')">
                                    <i class="fas fa-times me-2"></i>Reject
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($pr['status'] == 'draft'): ?>
                                <a href="<?= base_url('purchase-requisition/edit/' . $pr['id']) ?>" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit PR
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($pr['status'] == 'approved'): ?>
                                <a href="<?= base_url('purchase-order/create?pr_id=' . $pr['id']) ?>" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Create Purchase Order
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Items</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pr['items'])): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Items Found</h5>
                                <p class="text-muted">This purchase requisition has no items.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-warning">
                                        <tr data-selectable>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalAmount = 0;
                                        foreach ($pr['items'] as $index => $item): 
                                            $totalAmount += $item['total_amount'];
                                        ?>
                                            <tr data-selectable>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <strong><?= $item['product_code'] ?></strong><br>
                                                    <small class="text-muted"><?= $item['product_name'] ?></small>
                                                </td>
                                                <td><?= number_format($item['quantity'], 2) ?></td>
                                                <td><?= $item['unit'] ?></td>
                                                <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                                                <td><strong>₹<?= number_format($item['total_amount'], 2) ?></strong></td>
                                                <td>
                                                    <?php if ($item['remarks']): ?>
                                                        <small class="text-muted"><?= htmlspecialchars($item['remarks']) ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr data-selectable>
                                            <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                                            <td colspan="2">
                                                <strong class="text-primary">₹<?= number_format($totalAmount, 2) ?></strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    
<?= $this->endSection() ?> 