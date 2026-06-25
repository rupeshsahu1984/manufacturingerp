<?= $this->extend('layouts/main') ?>\n\n<?= $this->section('content') ?>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-highlight">
                <div class="title">PRODX ERP</div>
                <div class="description">Complete Manufacturing ERP Solution</div>
                <a href="<?= base_url('dashboard') ?>" class="btn">Dashboard</a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="fas fa-tachometer-alt"></i>
                Main Navigation
            </div>
            <a href="<?= base_url('dashboard') ?>" class="sidebar-link">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="fas fa-cogs"></i>
                Manufacturing Modules
            </div>
            <div class="sidebar-description">
                Access all your manufacturing operations and management tools
            </div>
            <a href="<?= base_url('purchase-requisition') ?>" class="sidebar-link">
                <i class="fas fa-clipboard-list me-2"></i>Purchase Requisition
            </a>
            <a href="<?= base_url('purchase-order') ?>" class="sidebar-link <?= $moduleName === 'purchase-order' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart me-2"></i>Purchase Order
            </a>
            <a href="<?= base_url('gate-entry') ?>" class="sidebar-link <?= $moduleName === 'gate-entry' ? 'active' : '' ?>">
                <i class="fas fa-truck me-2"></i>Gate Entry
            </a>
            <a href="<?= base_url('supplier-master') ?>" class="sidebar-link <?= $moduleName === 'supplier-master' ? 'active' : '' ?>">
                <i class="fas fa-users me-2"></i>Supplier Master
            </a>
            <a href="<?= base_url('bom') ?>" class="sidebar-link <?= $moduleName === 'bom' ? 'active' : '' ?>">
                <i class="fas fa-list-alt me-2"></i>BOM Management
            </a>
            <a href="<?= base_url('work-orders') ?>" class="sidebar-link <?= $moduleName === 'work-orders' ? 'active' : '' ?>">
                <i class="fas fa-tasks me-2"></i>Work Orders
            </a>
            <a href="<?= base_url('production-tracking') ?>" class="sidebar-link <?= $moduleName === 'production-tracking' ? 'active' : '' ?>">
                <i class="fas fa-chart-line me-2"></i>Production Tracking
            </a>
            <a href="<?= base_url('quality-control') ?>" class="sidebar-link <?= $moduleName === 'quality-control' ? 'active' : '' ?>">
                <i class="fas fa-check-circle me-2"></i>Quality Control
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="fas fa-chart-bar"></i>
                Business Operations
            </div>
            <a href="<?= base_url('sales-orders') ?>" class="sidebar-link <?= $moduleName === 'sales-orders' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar me-2"></i>Sales Orders
            </a>
            <a href="<?= base_url('finance') ?>" class="sidebar-link <?= $moduleName === 'finance' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave me-2"></i>Finance
            </a>
            <a href="<?= base_url('accounting') ?>" class="sidebar-link <?= $moduleName === 'accounting' ? 'active' : '' ?>">
                <i class="fas fa-calculator me-2"></i>Accounting
            </a>
            <a href="<?= base_url('hrm') ?>" class="sidebar-link <?= $moduleName === 'hrm' ? 'active' : '' ?>">
                <i class="fas fa-users me-2"></i>HR Management
            </a>
            <a href="<?= base_url('reception') ?>" class="sidebar-link <?= $moduleName === 'reception' ? 'active' : '' ?>">
                <i class="fas fa-user-tie me-2"></i>Reception
            </a>
            <a href="<?= base_url('reports') ?>" class="sidebar-link <?= $moduleName === 'reports' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie me-2"></i>Reports
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="fas fa-star"></i>
                What's New?
            </div>
            <div class="sidebar-highlight">
                <div class="title">Advanced Analytics</div>
                <div class="description">Get real-time insights and predictive analytics for better decision making</div>
                <a href="#" class="btn">Learn More ></a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="header-content">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="h1"><?= $title ?></h1>
                </div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name"><?= session()->get('user_name') ?? 'User' ?></div>
                        <div class="user-role"><?= ucfirst(session()->get('user_role') ?? 'User') ?></div>
                    </div>
                    <div class="user-avatar">
                        <?= substr(session()->get('user_name') ?? 'U', 0, 1) ?>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="btn btn-delete btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            <div class="content-header">
                <div>
                    <h2 class="mb-0"><?= $title ?></h2>
                    <p class="text-muted mb-0">Add new <?= strtolower($title) ?></p>
                </div>
                <a href="<?= base_url($moduleName) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="h5">
                        <i class="<?= $moduleIcon ?> me-2"></i>
                        <?= $title ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form data-validate action="<?= base_url($moduleName . '/store') ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create <?= $title ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    
<?= $this->endSection() ?> 