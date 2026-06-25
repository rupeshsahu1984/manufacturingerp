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
        
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        
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
        
        .supplier-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .supplier-card:hover {
            transform: translateY(-5px);
        }
        
        .category-badge {
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
                <i class="fas fa-users me-3"></i>
                Supplier Master
            </h1>
            <p class="mb-0">Manage all supplier information and performance tracking</p>
        </div>

        <!-- Search and Filters -->
        <div class="search-box">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search suppliers...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        <option value="raw_material">Raw Material</option>
                        <option value="tools">Tools</option>
                        <option value="services">Services</option>
                        <option value="packaging">Packaging</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="/purchase/suppliers/create" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Supplier
                    </a>
                </div>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Supplier List
                </h4>
                <div>
                    <button class="btn btn-outline-success btn-sm" onclick="exportToCSV()">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="printSuppliers()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="suppliersTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Supplier Code</th>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Category</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($suppliers)): ?>
                            <?php foreach ($suppliers as $supplier): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($supplier['supplier_code']) ?></strong>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($supplier['supplier_name']) ?></div>
                                        <small class="text-muted"><?= esc($supplier['city']) ?>, <?= esc($supplier['state']) ?></small>
                                    </td>
                                    <td><?= esc($supplier['contact_person']) ?></td>
                                    <td>
                                        <a href="mailto:<?= esc($supplier['email']) ?>" class="text-decoration-none">
                                            <?= esc($supplier['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?= esc($supplier['phone']) ?>" class="text-decoration-none">
                                            <?= esc($supplier['phone']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="category-badge">
                                            <?= ucfirst(str_replace('_', ' ', $supplier['category'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= (isset($supplier['rating']) ? $supplier['rating'] : 0) ? 'text-warning' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2"><?= number_format(isset($supplier['rating']) ? $supplier['rating'] : 0, 1) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $supplier['status'] ?>">
                                            <?= ucfirst($supplier['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/purchase/suppliers/edit/<?= $supplier['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/purchase/suppliers/view/<?= $supplier['id'] ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="viewPerformance(<?= $supplier['id'] ?>)" 
                                                    title="Performance">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                            <?php if ($supplier['status'] === 'active'): ?>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="deactivateSupplier(<?= $supplier['id'] ?>)" 
                                                        title="Deactivate">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="activateSupplier(<?= $supplier['id'] ?>)" 
                                                        title="Activate">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No suppliers found. <a href="/purchase/suppliers/create">Add your first supplier</a></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Modal -->
        <div class="modal fade" id="performanceModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Supplier Performance
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="performanceContent">
                            <!-- Performance data will be loaded here -->
                        </div>
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
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#suppliersTable').DataTable({
                pageLength: 25,
                order: [[1, 'asc']],
                responsive: true,
                language: {
                    search: "Search suppliers:",
                    lengthMenu: "Show _MENU_ suppliers per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ suppliers"
                }
            });

            // Search functionality
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Category filter
            $('#categoryFilter').on('change', function() {
                const category = $(this).val();
                if (category) {
                    table.column(5).search(category).draw();
                } else {
                    table.column(5).search('').draw();
                }
            });

            // Status filter
            $('#statusFilter').on('change', function() {
                const status = $(this).val();
                if (status) {
                    table.column(7).search(status).draw();
                } else {
                    table.column(7).search('').draw();
                }
            });
        });

        // View supplier performance
        function viewPerformance(supplierId) {
            // Load performance data via AJAX
            fetch(`/purchase/suppliers/performance/${supplierId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('performanceContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>On-Time Delivery</h6>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" style="width: ${data.on_time_delivery}%">
                                        ${data.on_time_delivery}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Quality Score</h6>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-info" style="width: ${data.quality_score}%">
                                        ${data.quality_score}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Price Competitiveness</h6>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-warning" style="width: ${data.price_competitiveness}%">
                                        ${data.price_competitiveness}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Overall Rating</h6>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-primary" style="width: ${data.overall_rating}%">
                                        ${data.overall_rating}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('performanceModal')).show();
                })
                .catch(error => {
                    console.error('Error loading performance data:', error);
                    alert('Failed to load performance data');
                });
        }

        // Activate supplier
        function activateSupplier(supplierId) {
            if (confirm('Are you sure you want to activate this supplier?')) {
                fetch(`/purchase/suppliers/activate/${supplierId}`, {
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
                        alert('Failed to activate supplier');
                    }
                });
            }
        }

        // Deactivate supplier
        function deactivateSupplier(supplierId) {
            if (confirm('Are you sure you want to deactivate this supplier?')) {
                fetch(`/purchase/suppliers/deactivate/${supplierId}`, {
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
                        alert('Failed to deactivate supplier');
                    }
                });
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = $('#suppliersTable').DataTable();
            const data = table.data().toArray();
            
            let csv = 'Supplier Code,Name,Contact Person,Email,Phone,Category,Rating,Status\n';
            
            data.forEach(row => {
                const rowData = [
                    row[0], // Supplier Code
                    row[1].split('<')[0].trim(), // Name (remove HTML)
                    row[2], // Contact Person
                    row[3].split('>')[1].split('<')[0], // Email (extract from link)
                    row[4].split('>')[1].split('<')[0], // Phone (extract from link)
                    row[5].split('>')[1].split('<')[0], // Category (extract from badge)
                    row[6].split('>')[1].split('<')[0], // Rating
                    row[7].split('>')[1].split('<')[0]  // Status (extract from badge)
                ];
                csv += rowData.join(',') + '\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'suppliers_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Print suppliers
        function printSuppliers() {
            window.print();
        }
    </script>
</body>
</html>
