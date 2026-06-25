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
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .action-buttons .btn {
            margin: 2px;
            border-radius: 8px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-closed { background-color: #e2e3e5; color: #383d41; }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .priority-low { background-color: #d1ecf1; color: #0c5460; }
        .priority-normal { background-color: #d4edda; color: #155724; }
        .priority-high { background-color: #fff3cd; color: #856404; }
        .priority-urgent { background-color: #f8d7da; color: #721c24; }
        
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .requisition-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .requisition-card:hover {
            transform: translateY(-5px);
        }
        
        .department-badge {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-clipboard-list me-3"></i>
                Purchase Requisitions
            </h1>
            <p class="mb-0">Manage and track all purchase requisitions</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Purchase Requisitions</li>
            </ol>
        </nav>

        <!-- Search and Filters -->
        <div class="search-box">
            <div class="row">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search requisitions...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="priorityFilter" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="departmentFilter" class="form-select">
                        <option value="">All Departments</option>
                        <option value="Production">Production</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Stores">Stores</option>
                        <option value="Quality">Quality</option>
                        <option value="Engineering">Engineering</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="/purchase/requisitions/create" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Create PR
                    </a>
                </div>
            </div>
        </div>

        <!-- Requisitions Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Requisition List
                </h4>
                <div>
                    <button class="btn btn-outline-success btn-sm" onclick="exportToCSV()">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="printRequisitions()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="requisitionsTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>PR Number</th>
                            <th>Date</th>
                            <th>Department</th>
                            <th>Requested By</th>
                            <th>Priority</th>
                            <th>Items</th>
                            <th>Total Value</th>
                            <th>Required Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requisitions)): ?>
                            <?php foreach ($requisitions as $requisition): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($requisition['pr_number']) ?></strong>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($requisition['created_at'])) ?></td>
                                    <td>
                                        <span class="department-badge">
                                            <?= esc($requisition['department']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($requisition['requested_by']) ?></td>
                                    <td>
                                        <span class="priority-badge priority-<?= $requisition['priority'] ?>">
                                            <?= ucfirst($requisition['priority']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= isset($requisition['item_count']) ? $requisition['item_count'] : 0 ?> items
                                        </span>
                                    </td>
                                    <td>
                                        <strong>₹<?= number_format(isset($requisition['total_value']) ? $requisition['total_value'] : 0, 2) ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                        $requiredDate = isset($requisition['required_date']) ? $requisition['required_date'] : null;
                                        if ($requiredDate) {
                                            $date = new DateTime($requiredDate);
                                            $today = new DateTime();
                                            $diff = $today->diff($date);
                                            $daysLeft = $diff->invert ? -$diff->days : $diff->days;
                                            
                                            if ($daysLeft < 0) {
                                                echo '<span class="text-danger">' . date('d/m/Y', strtotime($requiredDate)) . '</span>';
                                                echo '<br><small class="text-danger">Overdue by ' . abs($daysLeft) . ' days</small>';
                                            } elseif ($daysLeft <= 3) {
                                                echo '<span class="text-warning">' . date('d/m/Y', strtotime($requiredDate)) . '</span>';
                                                echo '<br><small class="text-warning">Due in ' . $daysLeft . ' days</small>';
                                            } else {
                                                echo '<span class="text-success">' . date('d/m/Y', strtotime($requiredDate)) . '</span>';
                                                echo '<br><small class="text-success">Due in ' . $daysLeft . ' days</small>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">Not specified</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $requisition['status'] ?>">
                                            <?= ucfirst($requisition['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/purchase/requisitions/view/<?= $requisition['id'] ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($requisition['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="approveRequisition(<?= $requisition['id'] ?>)" 
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="rejectRequisition(<?= $requisition['id'] ?>)" 
                                                        title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($requisition['status'] === 'approved'): ?>
                                                <a href="/purchase/orders/create?pr_id=<?= $requisition['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Create PO">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="/purchase/requisitions/edit/<?= $requisition['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="printRequisition(<?= $requisition['id'] ?>)" 
                                                    title="Print">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                    <p>No purchase requisitions found. <a href="/purchase/requisitions/create">Create your first PR</a></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div class="modal fade" id="rejectionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle text-danger me-2"></i>
                            Reject Requisition
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="rejectionForm">
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                          rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmRejection()">
                            <i class="fas fa-times me-2"></i>Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        let currentRequisitionId = null;
        
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#requisitionsTable').DataTable({
                pageLength: 25,
                order: [[1, 'desc']], // Sort by date descending
                responsive: true,
                language: {
                    search: "Search requisitions:",
                    lengthMenu: "Show _MENU_ requisitions per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ requisitions"
                }
            });

            // Search functionality
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Status filter
            $('#statusFilter').on('change', function() {
                const status = $(this).val();
                if (status) {
                    table.column(8).search(status).draw();
                } else {
                    table.column(8).search('').draw();
                }
            });

            // Priority filter
            $('#priorityFilter').on('change', function() {
                const priority = $(this).val();
                if (priority) {
                    table.column(4).search(priority).draw();
                } else {
                    table.column(4).search('').draw();
                }
            });

            // Department filter
            $('#departmentFilter').on('change', function() {
                const department = $(this).val();
                if (department) {
                    table.column(2).search(department).draw();
                } else {
                    table.column(2).search('').draw();
                }
            });
        });

        // Approve requisition
        function approveRequisition(requisitionId) {
            if (confirm('Are you sure you want to approve this requisition?')) {
                fetch(`/purchase/requisitions/approve/${requisitionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to approve requisition: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to approve requisition');
                });
            }
        }

        // Reject requisition
        function rejectRequisition(requisitionId) {
            currentRequisitionId = requisitionId;
            new bootstrap.Modal(document.getElementById('rejectionModal')).show();
        }

        // Confirm rejection
        function confirmRejection() {
            const reason = document.getElementById('rejection_reason').value.trim();
            
            if (!reason) {
                alert('Please provide a rejection reason');
                return;
            }

            fetch(`/purchase/requisitions/reject/${currentRequisitionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rejection_reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('rejectionModal')).hide();
                    document.getElementById('rejection_reason').value = '';
                    location.reload();
                } else {
                    alert('Failed to reject requisition: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to reject requisition');
            });
        }

        // Print requisition
        function printRequisition(requisitionId) {
            window.open(`/purchase/requisitions/print/${requisitionId}`, '_blank');
        }

        // Export to CSV
        function exportToCSV() {
            const table = $('#requisitionsTable').DataTable();
            const data = table.data().toArray();
            
            let csv = 'PR Number,Date,Department,Requested By,Priority,Items,Total Value,Required Date,Status\n';
            
            data.forEach(row => {
                const rowData = [
                    row[0].split('<')[0].trim(), // PR Number
                    row[1], // Date
                    row[2].split('>')[1].split('<')[0], // Department (extract from badge)
                    row[3], // Requested By
                    row[4].split('>')[1].split('<')[0], // Priority (extract from badge)
                    row[5].split('>')[1].split('<')[0], // Items (extract from badge)
                    row[6].split('>')[1].split('<')[0], // Total Value (extract from strong)
                    row[7].split('<')[0].trim(), // Required Date (extract first part)
                    row[8].split('>')[1].split('<')[0]  // Status (extract from badge)
                ];
                csv += rowData.join(',') + '\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'purchase_requisitions_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Print requisitions
        function printRequisitions() {
            window.print();
        }
    </script>
</body>
</html>
