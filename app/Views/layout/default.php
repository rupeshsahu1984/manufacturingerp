<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'ERP System' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main CSS -->
    <link href="<?= base_url('public/css/main.css') ?>?v=<?= time() ?>" rel="stylesheet" type="text/css">
    
    <!-- Force CSS Refresh -->
    <style>
        /* Immediate form fixes */
        .form-control, .form-select {
            font-size: 14px !important;
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
            padding: 8px 12px !important;
            height: 38px !important;
            line-height: 1.4 !important;
            margin-bottom: 0.5rem !important;
        }
        
        .form-label {
            font-weight: 600 !important;
            margin-bottom: 0.25rem !important;
            font-size: 13px !important;
            line-height: 1.3 !important;
        }
        
        .material-row, .waste-material-row {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            border-radius: 8px !important;
            margin-bottom: 1rem !important;
            padding: 1rem !important;
        }
        
        .input-group-text {
            background-color: #e9ecef !important;
            border: 1px solid #ced4da !important;
            font-size: 14px !important;
            padding: 8px 12px !important;
            height: 38px !important;
            line-height: 1.4 !important;
        }
        
        .btn {
            font-size: 14px !important;
            padding: 8px 16px !important;
            border-radius: 4px !important;
            font-weight: 500 !important;
        }
        
        .btn-sm {
            padding: 6px 12px !important;
            font-size: 12px !important;
        }
        
        .table {
            font-size: 14px !important;
        }
        
        .table th {
            font-weight: 600 !important;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
        }
        
        .card {
            border: 1px solid #e9ecef !important;
            border-radius: 8px !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
        
        .card-header {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            font-weight: 600 !important;
        }
        
        .page-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            margin-bottom: 1rem !important;
        }
        
        .breadcrumb {
            font-size: 14px !important;
            margin-bottom: 0 !important;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">" !important;
        }
    </style>
    
    <!-- Additional CSS files -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?= base_url('public/' . $css) ?>?v=<?= time() ?>" rel="stylesheet" type="text/css">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg top-nav">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <div class="navbar-brand d-flex align-items-center">
                <img src="<?= base_url('public/images/logo-text.svg') ?>" alt="ProDX ERP" class="logo-img me-3" onerror="this.src='<?= base_url('public/images/logo.svg') ?>'">
                <span class="brand-text d-none d-md-inline">ProDX ERP System</span>
            </div>
            
            <!-- Navigation Links -->
            <div class="navbar-nav flex-grow-1 justify-content-center">
                <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="fas fa-th"></i> <span class="d-none d-lg-inline">OVERVIEW</span></a>
                <a class="nav-link" href="<?= base_url('purchase-requisition') ?>"><i class="fas fa-building"></i> <span class="d-none d-lg-inline">PROCUREMENT</span></a>
                <a class="nav-link" href="<?= base_url('work-orders') ?>"><i class="fas fa-exchange-alt"></i> <span class="d-none d-lg-inline">MANUFACTURING</span></a>
                <a class="nav-link" href="<?= base_url('sales-orders') ?>"><i class="fas fa-credit-card"></i> <span class="d-none d-lg-inline">SALES & FINANCE</span></a>
                <a class="nav-link" href="<?= base_url('reports') ?>"><i class="fas fa-chart-line"></i> <span class="d-none d-lg-inline">REPORTS</span></a>
                <a class="nav-link" href="<?= base_url('support') ?>"><i class="fas fa-headset"></i> <span class="d-none d-lg-inline">SUPPORT</span></a>
            </div>
            
            <!-- User Info -->
            <div class="login-info d-none d-md-block">
                Last logged in: <?= date('d/m/Y H:i:s') ?> IST
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <?= view('partials/sidebar') ?>

        <!-- Main Content -->
        <div class="main-content">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JavaScript -->
    <script src="<?= base_url('public/js/app.js') ?>"></script>
    
    <!-- Additional JavaScript files -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= base_url('public/' . $js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Page specific JavaScript -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
