<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .installer-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .installer-body {
            padding: 40px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            position: relative;
        }
        .step.active {
            background: #ff6b35;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 2px;
            background: #e9ecef;
        }
        .step.completed:not(:last-child)::after {
            background: #28a745;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            align-items: center;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list i {
            color: #28a745;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .btn-installer {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-installer:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
            color: white;
        }
        .system-requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .requirement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }
        .requirement-status {
            font-weight: bold;
        }
        .status-ok {
            color: #28a745;
        }
        .status-error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="installer-container">
            <div class="installer-header">
                <h1><i class="fas fa-cogs"></i> Manufacturing ERP</h1>
                <p class="mb-0">Complete Manufacturing Management Solution</p>
            </div>
            
            <div class="installer-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="step">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="step">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="step">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <h2 class="text-center mb-4">Welcome to Manufacturing ERP Installation</h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="fas fa-star text-warning"></i> Key Features</h4>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle"></i> Complete Manufacturing Management</li>
                            <li><i class="fas fa-check-circle"></i> Inventory & Stock Control</li>
                            <li><i class="fas fa-check-circle"></i> Production Planning & Tracking</li>
                            <li><i class="fas fa-check-circle"></i> Purchase & Sales Management</li>
                            <li><i class="fas fa-check-circle"></i> Financial Accounting</li>
                            <li><i class="fas fa-check-circle"></i> Human Resources</li>
                            <li><i class="fas fa-check-circle"></i> Multi-User Access Control</li>
                            <li><i class="fas fa-check-circle"></i> Comprehensive Reporting</li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h4><i class="fas fa-server text-info"></i> System Requirements</h4>
                        <div class="system-requirements">
                            <?php
                            $requirements = [
                                'PHP Version (>= 7.4)' => version_compare(PHP_VERSION, '7.4.0', '>='),
                                'MySQL Extension' => extension_loaded('mysqli'),
                                'PDO Extension' => extension_loaded('pdo'),
                                'JSON Extension' => extension_loaded('json'),
                                'cURL Extension' => extension_loaded('curl'),
                                'GD Extension' => extension_loaded('gd'),
                                'OpenSSL Extension' => extension_loaded('openssl'),
                                'Writable Uploads Directory' => is_writable(FCPATH . 'uploads') || is_writable(FCPATH),
                                'Writable Writable Directory' => is_writable(WRITEPATH)
                            ];
                            
                            foreach ($requirements as $requirement => $status): ?>
                                <div class="requirement-item">
                                    <span><?= $requirement ?></span>
                                    <span class="requirement-status <?= $status ? 'status-ok' : 'status-error' ?>">
                                        <i class="fas fa-<?= $status ? 'check' : 'times' ?>"></i>
                                        <?= $status ? 'OK' : 'ERROR' ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (array_search(false, $requirements) !== false): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> Some system requirements are not met. Please fix them before proceeding.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted mb-3">
                        This installer will guide you through setting up your Manufacturing ERP system.
                        The process includes database configuration, company setup, and creating your first administrator account.
                    </p>
                    
                    <a href="<?= base_url('installer/database') ?>" class="btn btn-installer btn-lg">
                        <i class="fas fa-arrow-right"></i> Start Installation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
