<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-question-circle me-3"></i>Help & Documentation</h1>
    <div class="header-actions">
        <a href="<?= base_url('help/video-tutorials') ?>" class="btn btn-primary">
            <i class="fas fa-play"></i> Video Tutorials
        </a>
        <a href="<?= base_url('help/contact-support') ?>" class="btn btn-outline-info ms-2">
            <i class="fas fa-headset"></i> Contact Support
        </a>
    </div>
</div>

<!-- Quick Search -->
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-search me-2"></i>Quick Search</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" id="helpSearch" placeholder="Search help topics...">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" onclick="searchHelp()">
                    <i class="fas fa-search"></i> Search Help
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Help Categories -->
<div class="row">
    <!-- Master Settings -->
    <div class="col-md-6 mb-4">
        <div class="content-card h-100">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-cogs me-2"></i>Master Settings</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('help/categories') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-tags text-primary me-2"></i>
                            <strong>Material Categories</strong>
                            <br><small class="text-muted">Create and manage material categories</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/suppliers') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-truck text-success me-2"></i>
                            <strong>Supplier Management</strong>
                            <br><small class="text-muted">Manage suppliers and vendor information</small>
                        </div>
                        <span class="badge bg-success rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/customers') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-users text-info me-2"></i>
                            <strong>Customer Management</strong>
                            <br><small class="text-muted">Manage customer information and relationships</small>
                        </div>
                        <span class="badge bg-info rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/materials') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-box text-warning me-2"></i>
                            <strong>Material Master</strong>
                            <br><small class="text-muted">Create and manage materials and products</small>
                        </div>
                        <span class="badge bg-warning rounded-pill">Guide</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Production & Manufacturing -->
    <div class="col-md-6 mb-4">
        <div class="content-card h-100">
            <div class="card-header bg-success text-white">
                <h5><i class="fas fa-industry me-2"></i>Production & Manufacturing</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('help/production-settings') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-cogs text-success me-2"></i>
                            <strong>Production Settings</strong>
                            <br><small class="text-muted">Configure BOMs and production processes</small>
                        </div>
                        <span class="badge bg-success rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/manufacturing') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-industry text-primary me-2"></i>
                            <strong>Manufacturing Orders</strong>
                            <br><small class="text-muted">Create and manage production orders</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/bom-management') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-list-alt text-warning me-2"></i>
                            <strong>BOM Management</strong>
                            <br><small class="text-muted">Manage Bill of Materials</small>
                        </div>
                        <span class="badge bg-warning rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/waste-management') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-recycle text-danger me-2"></i>
                            <strong>Waste Management</strong>
                            <br><small class="text-muted">Track and manage waste materials</small>
                        </div>
                        <span class="badge bg-danger rounded-pill">Guide</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Procurement -->
    <div class="col-md-6 mb-4">
        <div class="content-card h-100">
            <div class="card-header bg-warning text-dark">
                <h5><i class="fas fa-shopping-cart me-2"></i>Procurement</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('help/purchase-requisitions') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-clipboard-list text-warning me-2"></i>
                            <strong>Purchase Requisitions</strong>
                            <br><small class="text-muted">Create and manage purchase requests</small>
                        </div>
                        <span class="badge bg-warning rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/purchase-orders') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-invoice text-primary me-2"></i>
                            <strong>Purchase Orders</strong>
                            <br><small class="text-muted">Manage purchase orders and supplier orders</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/purchase-bills') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-receipt text-success me-2"></i>
                            <strong>Purchase Bills</strong>
                            <br><small class="text-muted">Process supplier invoices and payments</small>
                        </div>
                        <span class="badge bg-success rounded-pill">Guide</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory & Stock -->
    <div class="col-md-6 mb-4">
        <div class="content-card h-100">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-warehouse me-2"></i>Inventory & Stock</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('help/stock-management') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-boxes text-info me-2"></i>
                            <strong>Stock Management</strong>
                            <br><small class="text-muted">Monitor and manage inventory levels</small>
                        </div>
                        <span class="badge bg-info rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/stock-transactions') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exchange-alt text-primary me-2"></i>
                            <strong>Stock Transactions</strong>
                            <br><small class="text-muted">Track stock movements and transactions</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">Guide</span>
                    </a>
                    <a href="<?= base_url('help/low-stock-alerts') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                            <strong>Low Stock Alerts</strong>
                            <br><small class="text-muted">Configure and manage stock alerts</small>
                        </div>
                        <span class="badge bg-danger rounded-pill">Guide</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Start Guides -->
<div class="content-card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-rocket me-2"></i>Quick Start Guides</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-3x text-primary mb-3"></i>
                        <h6>Getting Started</h6>
                        <p class="text-muted">Learn the basics of the ERP system</p>
                        <a href="<?= base_url('help/getting-started') ?>" class="btn btn-sm btn-primary">View Guide</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-video fa-3x text-success mb-3"></i>
                        <h6>Video Tutorials</h6>
                        <p class="text-muted">Step-by-step video guides</p>
                        <a href="<?= base_url('help/video-tutorials') ?>" class="btn btn-sm btn-success">Watch Videos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-3x text-warning mb-3"></i>
                        <h6>User Manual</h6>
                        <p class="text-muted">Complete system documentation</p>
                        <a href="<?= base_url('help/user-manual') ?>" class="btn btn-sm btn-warning">Read Manual</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Support Section -->
<div class="content-card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-headset me-2"></i>Need More Help?</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-envelope text-primary"></i> Contact Support</h6>
                <p>Get help from our support team</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope me-2"></i> Email: support@prodx.com</li>
                    <li><i class="fas fa-phone me-2"></i> Phone: +1-800-PRODX</li>
                    <li><i class="fas fa-clock me-2"></i> Hours: Mon-Fri 9AM-6PM</li>
                </ul>
                <a href="<?= base_url('help/contact-support') ?>" class="btn btn-primary">Contact Support</a>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-question-circle text-success"></i> FAQ</h6>
                <p>Find answers to common questions</p>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('help/faq-general') ?>">General Questions</a></li>
                    <li><a href="<?= base_url('help/faq-technical') ?>">Technical Issues</a></li>
                    <li><a href="<?= base_url('help/faq-features') ?>">Feature Questions</a></li>
                </ul>
                <a href="<?= base_url('help/faq') ?>" class="btn btn-success">View All FAQs</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function searchHelp() {
    const searchTerm = document.getElementById('helpSearch').value;
    if (searchTerm.trim()) {
        window.location.href = '<?= base_url('help/search') ?>?q=' + encodeURIComponent(searchTerm);
    }
}

// Enter key search
document.getElementById('helpSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchHelp();
    }
});
</script>
<?= $this->endSection() ?>
