<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'ERP System' ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main CSS -->
    <link href="<?= base_url('public/css/main.css') ?>?v=<?= time() ?>" rel="stylesheet" type="text/css">
    
    <!-- Force CSS Refresh -->
    <style>
        /* Professional form styling */
        .form-control, .form-select {
            font-size: 14px !important;
            background-color: var(--form-bg) !important;
            border: 1px solid var(--border-gray) !important;
            border-radius: 6px !important;
            padding: 10px 14px !important;
            height: 42px !important;
            line-height: 1.4 !important;
            margin-bottom: 0.75rem !important;
            transition: all 0.2s ease !important;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--header-bg) !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.15) !important;
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
            font-weight: 500 !important;
            line-height: 1.4 !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }
        
        .btn-primary {
            background-color: var(--header-bg) !important;
            border-color: var(--border-gray) !important;
            color: #000000 !important;
        }
        
        .btn-primary:hover {
            background-color: var(--header-bg) !important;
            border-color: var(--border-gray) !important;
            color: #000000 !important;
            transform: translateY(-1px) !important;
            box-shadow: var(--shadow-medium) !important;
        }
        
        .btn-sm {
            font-size: 12px !important;
            padding: 6px 12px !important;
            height: 38px !important;
        }
        
        .form-text {
            font-size: 12px !important;
            margin-top: 0.25rem !important;
            line-height: 1.3 !important;
        }
        
        small {
            font-size: 12px !important;
            line-height: 1.3 !important;
        }
        /* Username display in top header */
        .header-username {
            font-weight: 600;
            color: #ff6b35; /* theme primary orange */
            font-size: 13px;
        }
    </style>
    
    <!-- Additional CSS files -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?= base_url('public/' . $css) ?>" rel="stylesheet">
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
            <div class="login-info d-none d-md-flex align-items-center">
                <?php
                    // Try to get username from session; if missing but user_id exists,
                    // fetch it from the database once.
                    $loggedUser = session()->get('username');
                    if (!$loggedUser && session()->get('user_id')) {
                        try {
                            $userModel = new \App\Models\User();
                            $userRow = $userModel->find(session()->get('user_id'));
                            if ($userRow && !empty($userRow['username'])) {
                                $loggedUser = $userRow['username'];
                                // cache into session for later requests
                                session()->set('username', $loggedUser);
                            }
                        } catch (\Throwable $e) {
                            // fail silently in layout; don't break page
                        }
                    }
                ?>
                <?php if (!empty($loggedUser)): ?>
                    <span class="me-3 header-username">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= esc($loggedUser) ?>
                    </span>
                <?php endif; ?>
                <span class="text-muted small">
                    Last logged in: <?= date('d/m/Y H:i:s') ?> IST
                </span>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <?= view('partials/sidebar') ?>

        <!-- Main Content -->
        <div class="main-content">
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
