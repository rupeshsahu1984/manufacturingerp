<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Manufacturing ERP</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-pending { background-color: #cce7ff; color: #004085; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-in_transit { background-color: #fff3cd; color: #856404; }
        .status-received { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .priority-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .priority-low { background-color: #e8f5e8; color: #2e7d32; }
        .priority-normal { background-color: #e3f2fd; color: #1565c0; }
        .priority-high { background-color: #fff8e1; color: #f57f17; }
        .priority-urgent { background-color: #ffebee; color: #c62828; }
        
        .route-visualization {
            background: #f8f9fa;
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
        }
        
        .warehouse-box {
            background: white;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            display: inline-block;
            min-width: 200px;
        }
        
        .route-arrow {
            font-size: 2rem;
            color: #28a745;
            margin: 0 20px;
        }
        
        .item-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            transition: all 0.3s ease;
        }
        
        .item-card:hover {
            background: white;
            border-color: #28a745;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #28a745;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 60px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #28a745;
            border: 3px solid white;
        }
        
        .timeline-content {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .timeline-date {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .timeline-title {
            font-weight: 600;
            color: #28a745;
            margin-bottom: 5px;
        }
        
        .timeline-description {
            color: #495057;
            margin-bottom: 0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .summary-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .summary-number {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }
        
        .summary-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
        .progress-tracker {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .progress-step:last-child {
            margin-bottom: 0;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .step-completed {
            background: #28a745;
            color: white;
        }
        
        .step-current {
            background: #ffc107;
            color: white;
        }
        
        .step-pending {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .step-info {
            flex: 1;
        }
        
        .step-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .step-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-exchange-alt me-3"></i>
                        Stock Transfer Details
                    </h1>
                    <p class="mb-0">Transfer #<?= esc(isset($transfer['transfer_number']) ? $transfer['transfer_number'] : 'N/A') ?></p>
                </div>
                <div class="text-end">
                    <span class="status-badge status-<?= strtolower(isset($transfer['status']) ? $transfer['status'] : 'draft') ?>">
                        <?= ucwords(str_replace('_', ' ', isset($transfer['status']) ? $transfer['status'] : 'Draft')) ?>
                    </span>
                    <br>
                    <span class="priority-badge priority-<?= strtolower(isset($transfer['priority']) ? $transfer['priority'] : 'normal') ?>">
                        <?= ucfirst(isset($transfer['priority']) ? $transfer['priority'] : 'Normal') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/inventory" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Inventory Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/inventory/transfers" class="text-decoration-none">Stock Transfers</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Transfer Details</li>
            </ol>
        </nav>

        <!-- Action Buttons -->
        <div class="text-center mb-4">
            <div class="action-buttons">
                <a href="/inventory/transfers" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
                <a href="/inventory/transfers/edit/<?= $transfer['id'] ?>" class="btn btn-outline-warning btn-action">
                    <i class="fas fa-edit me-2"></i>Edit Transfer
                </a>
                <a href="/inventory/transfers/items/<?= $transfer['id'] ?>" class="btn btn-outline-info btn-action">
                    <i class="fas fa-boxes me-2"></i>View Items
                </a>
                <button type="button" class="btn btn-outline-success btn-action" onclick="printTransfer()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <?php if ((isset($transfer['status']) ? $transfer['status'] : '') === 'pending'): ?>
                    <button type="button" class="btn btn-success btn-action" onclick="approveTransfer(<?= $transfer['id'] ?>)">
                        <i class="fas fa-check me-2"></i>Approve
                    </button>
                    <button type="button" class="btn btn-danger btn-action" onclick="rejectTransfer(<?= $transfer['id'] ?>)">
                        <i class="fas fa-times me-2"></i>Reject
                    </button>
                <?php elseif ((isset($transfer['status']) ? $transfer['status'] : '') === 'approved'): ?>
                    <button type="button" class="btn btn-warning btn-action" onclick="startTransfer(<?= $transfer['id'] ?>)">
                        <i class="fas fa-truck me-2"></i>Start Transfer
                    </button>
                <?php elseif ((isset($transfer['status']) ? $transfer['status'] : '') === 'in_transit'): ?>
                    <button type="button" class="btn btn-info btn-action" onclick="completeTransfer(<?= $transfer['id'] ?>)">
                        <i class="fas fa-check-double me-2"></i>Complete Transfer
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="progress-tracker">
            <h5 class="mb-3">
                <i class="fas fa-route me-2"></i>
                Transfer Progress
            </h5>
            
            <div class="progress-step">
                <div class="step-icon <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['draft', 'pending', 'approved', 'in_transit', 'received']) ? 'step-completed' : 'step-pending' ?>">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="step-info">
                    <div class="step-title">Transfer Created</div>
                    <div class="step-description">Transfer request submitted</div>
                </div>
            </div>
            
            <div class="progress-step">
                <div class="step-icon <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['approved', 'in_transit', 'received']) ? 'step-completed' : ((isset($transfer['status']) ? $transfer['status'] : '') === 'pending' ? 'step-current' : 'step-pending') ?>">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="step-info">
                    <div class="step-title">Approval</div>
                    <div class="step-description">Waiting for approval</div>
                </div>
            </div>
            
            <div class="progress-step">
                <div class="step-icon <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['in_transit', 'received']) ? 'step-completed' : ((isset($transfer['status']) ? $transfer['status'] : '') === 'approved' ? 'step-current' : 'step-pending') ?>">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="step-info">
                    <div class="step-title">In Transit</div>
                    <div class="step-description">Items being transported</div>
                </div>
            </div>
            
            <div class="progress-step">
                <div class="step-icon <?= (isset($transfer['status']) ? $transfer['status'] : '') === 'received' ? 'step-completed' : ((isset($transfer['status']) ? $transfer['status'] : '') === 'in_transit' ? 'step-current' : 'step-pending') ?>">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="step-info">
                    <div class="step-title">Received</div>
                    <div class="step-description">Items received at destination</div>
                </div>
            </div>
        </div>

        <!-- Transfer Summary -->
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number"><?= isset($transfer['total_items']) ? $transfer['total_items'] : 0 ?></div>
                <div class="summary-label">Total Items</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">₹<?= number_format(isset($transfer['total_value']) ? $transfer['total_value'] : 0, 2) ?></div>
                <div class="summary-label">Total Value</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?= isset($transfer['transfer_date']) ? $transfer['transfer_date'] : 'N/A' ?></div>
                <div class="summary-label">Transfer Date</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?= isset($transfer['expected_delivery_date']) ? $transfer['expected_delivery_date'] : 'N/A' ?></div>
                <div class="summary-label">Expected Delivery</div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Transfer Information
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Transfer Number:</strong></div>
                        <div class="col-8"><?= esc(isset($transfer['transfer_number']) ? $transfer['transfer_number'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Transfer Date:</strong></div>
                        <div class="col-8"><?= isset($transfer['transfer_date']) ? $transfer['transfer_date'] : 'N/A' ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Priority:</strong></div>
                        <div class="col-8">
                            <span class="priority-badge priority-<?= strtolower(isset($transfer['priority']) ? $transfer['priority'] : 'normal') ?>">
                                <?= ucfirst(isset($transfer['priority']) ? $transfer['priority'] : 'Normal') ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Status:</strong></div>
                        <div class="col-8">
                            <span class="status-badge status-<?= strtolower(isset($transfer['status']) ? $transfer['status'] : 'draft') ?>">
                                <?= ucwords(str_replace('_', ' ', isset($transfer['status']) ? $transfer['status'] : 'Draft')) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Requested By:</strong></div>
                        <div class="col-8"><?= esc(isset($transfer['requested_by_name']) ? $transfer['requested_by_name'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Approved By:</strong></div>
                        <div class="col-8"><?= esc(isset($transfer['approved_by_name']) ? $transfer['approved_by_name'] : 'N/A') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Expected Delivery:</strong></div>
                        <div class="col-8"><?= isset($transfer['expected_delivery_date']) ? $transfer['expected_delivery_date'] : 'N/A' ?></div>
                    </div>
                    <?php if (!empty($transfer['actual_delivery_date'])): ?>
                        <div class="row mb-3">
                            <div class="col-4"><strong>Actual Delivery:</strong></div>
                            <div class="col-8"><?= $transfer['actual_delivery_date'] ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Route Information -->
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-route me-2"></i>
                Transfer Route
            </h5>
            
            <div class="route-visualization">
                <div class="warehouse-box">
                    <i class="fas fa-warehouse fa-2x text-primary mb-2"></i>
                    <h6>Source Warehouse</h6>
                    <strong><?= esc(isset($transfer['source_warehouse_name']) ? $transfer['source_warehouse_name'] : 'N/A') ?></strong>
                    <br><small class="text-muted"><?= esc(isset($transfer['source_warehouse_type']) ? $transfer['source_warehouse_type'] : 'N/A') ?></small>
                </div>
                
                <i class="fas fa-arrow-right route-arrow"></i>
                
                <div class="warehouse-box">
                    <i class="fas fa-warehouse fa-2x text-success mb-2"></i>
                    <h6>Destination Warehouse</h6>
                    <strong><?= esc(isset($transfer['destination_warehouse_name']) ? $transfer['destination_warehouse_name'] : 'N/A') ?></strong>
                    <br><small class="text-muted"><?= esc(isset($transfer['destination_warehouse_type']) ? $transfer['destination_warehouse_type'] : 'N/A') ?></small>
                </div>
            </div>
        </div>

        <!-- Transport Information -->
        <?php if (!empty($transfer['transport_mode']) || !empty($transfer['carrier'])): ?>
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-truck me-2"></i>
                    Transport Details
                </h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <strong>Transport Mode:</strong><br>
                        <?= ucfirst(isset($transfer['transport_mode']) ? $transfer['transport_mode'] : 'N/A') ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Carrier:</strong><br>
                        <?= esc(isset($transfer['carrier']) ? $transfer['carrier'] : 'N/A') ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Tracking Number:</strong><br>
                        <?= esc(isset($transfer['tracking_number']) ? $transfer['tracking_number'] : 'N/A') ?>
                    </div>
                </div>
                
                <?php if (!empty($transfer['special_instructions'])): ?>
                    <div class="mt-3">
                        <strong>Special Instructions:</strong><br>
                        <?= esc($transfer['special_instructions']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Transfer Items -->
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-boxes me-2"></i>
                Transfer Items
            </h5>
            
            <?php if (isset($transfer_items) && is_array($transfer_items) && count($transfer_items) > 0): ?>
                <?php foreach ($transfer_items as $item): ?>
                    <div class="item-card">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong><?= esc(isset($item['item_name']) ? $item['item_name'] : 'N/A') ?></strong>
                                <br><small class="text-muted"><?= esc(isset($item['item_code']) ? $item['item_code'] : 'N/A') ?></small>
                            </div>
                            <div class="col-md-2">
                                <strong>Quantity:</strong><br>
                                <?= isset($item['quantity']) ? $item['quantity'] : 0 ?> <?= esc(isset($item['uom']) ? $item['uom'] : '') ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Unit Cost:</strong><br>
                                ₹<?= number_format(isset($item['unit_cost']) ? $item['unit_cost'] : 0, 2) ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Total Cost:</strong><br>
                                ₹<?= number_format(isset($item['total_cost']) ? $item['total_cost'] : 0, 2) ?>
                            </div>
                            <div class="col-md-3">
                                <?php if (!empty($item['batch_number'])): ?>
                                    <strong>Batch:</strong> <?= esc($item['batch_number']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($item['expiry_date'])): ?>
                                    <strong>Expiry:</strong> <?= $item['expiry_date'] ?><br>
                                <?php endif; ?>
                                <?php if (!empty($item['notes'])): ?>
                                    <strong>Notes:</strong> <?= esc($item['notes']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <h6>No items found</h6>
                    <p>Items for this transfer have not been added yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Transfer Timeline -->
        <div class="info-card">
            <h5 class="mb-3">
                <i class="fas fa-history me-2"></i>
                Transfer Timeline
            </h5>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date"><?= isset($transfer['transfer_date']) ? $transfer['transfer_date'] : 'N/A' ?></div>
                        <div class="timeline-title">Transfer Created</div>
                        <div class="timeline-description">
                            Transfer request created by <?= esc(isset($transfer['requested_by_name']) ? $transfer['requested_by_name'] : 'N/A') ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($transfer['approved_by_name'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date"><?= isset($transfer['approved_date']) ? $transfer['approved_date'] : 'N/A' ?></div>
                            <div class="timeline-title">Transfer Approved</div>
                            <div class="timeline-description">
                                Transfer approved by <?= esc(isset($transfer['approved_by_name']) ? $transfer['approved_by_name'] : 'N/A') ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ((isset($transfer['status']) ? $transfer['status'] : '') === 'in_transit' || (isset($transfer['status']) ? $transfer['status'] : '') === 'received'): ?>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date"><?= isset($transfer['started_date']) ? $transfer['started_date'] : 'N/A' ?></div>
                            <div class="timeline-title">Transfer Started</div>
                            <div class="timeline-description">
                                Items picked up and transfer initiated
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ((isset($transfer['status']) ? $transfer['status'] : '') === 'received'): ?>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date"><?= isset($transfer['actual_delivery_date']) ? $transfer['actual_delivery_date'] : 'N/A' ?></div>
                            <div class="timeline-title">Transfer Completed</div>
                            <div class="timeline-description">
                                Items received at destination warehouse
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notes and Comments -->
        <?php if (!empty($transfer['notes'])): ?>
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-sticky-note me-2"></i>
                    Notes & Comments
                </h5>
                
                <div class="bg-light p-3 rounded">
                    <?= esc($transfer['notes']) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function printTransfer() {
            window.print();
        }

        function approveTransfer(transferId) {
            if (confirm('Are you sure you want to approve this transfer?')) {
                window.location.href = `/inventory/transfers/approve/${transferId}`;
            }
        }

        function rejectTransfer(transferId) {
            if (confirm('Are you sure you want to reject this transfer?')) {
                window.location.href = `/inventory/transfers/reject/${transferId}`;
            }
        }

        function startTransfer(transferId) {
            if (confirm('Are you sure you want to start this transfer?')) {
                window.location.href = `/inventory/transfers/start/${transferId}`;
            }
        }

        function completeTransfer(transferId) {
            if (confirm('Are you sure you want to complete this transfer?')) {
                window.location.href = `/inventory/transfers/complete/${transferId}`;
            }
        }
    </script>
</body>
</html>
