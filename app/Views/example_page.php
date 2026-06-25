<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-star me-3"></i>Example Page</h1>
    <div class="header-actions">
        <a href="#" class="btn-primary">
            <i class="fas fa-plus"></i>
            Add New Item
        </a>
        <a href="#" class="btn btn-outline-primary">
            <i class="fas fa-download"></i>
            Export Data
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-value">1,234</div>
        <div class="stat-label">Total Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value">987</div>
        <div class="stat-label">Active Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-value">156</div>
        <div class="stat-label">Pending Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-value">23</div>
        <div class="stat-label">Critical Items</div>
    </div>
</div>

<!-- Content Section -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Sample Data Table</h5>
        <div>
            <span class="badge bg-light text-dark">5 items</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr data-selectable>
                    <td><strong>001</strong></td>
                    <td>Sample Item 1</td>
                    <td><span class="badge bg-info">Category A</span></td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-sm btn-view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr data-selectable>
                    <td><strong>002</strong></td>
                    <td>Sample Item 2</td>
                    <td><span class="badge bg-warning">Category B</span></td>
                    <td><span class="status-badge status-inactive">Inactive</span></td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn btn-sm btn-view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Information Section -->
<div class="filters-section">
    <h5><i class="fas fa-info-circle me-2"></i>How to Use This Layout</h5>
    <div class="row">
        <div class="col-md-6">
            <h6>Features Included:</h6>
            <ul>
                <li>Responsive sidebar with navigation</li>
                <li>Top navigation bar</li>
                <li>Stats cards with icons</li>
                <li>Data tables with actions</li>
                <li>Search and filter functionality</li>
                <li>Mobile-friendly design</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h6>CSS Classes Available:</h6>
            <ul>
                <li><code>.header</code> - Page header with title and actions</li>
                <li><code>.stats-grid</code> - Grid layout for statistics</li>
                <li><code>.stat-card</code> - Individual stat cards</li>
                <li><code>.content-card</code> - Content containers</li>
                <li><code>.filters-section</code> - Filter and form sections</li>
                <li><code>.action-buttons</code> - Button groups for actions</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Example page specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Example page loaded successfully!');
        
        // Show a welcome notification
        setTimeout(function() {
            showNotification('Welcome to the example page! This demonstrates the new layout system.', 'info');
        }, 1000);
    });
</script>
<?= $this->endSection() ?>
