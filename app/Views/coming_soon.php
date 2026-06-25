<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - PRODX ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('public/css/sidebar.css?v=2') ?>" rel="stylesheet">
    <link href="<?= base_url('public/css/style.css') ?>" rel="stylesheet">
    <style>
        .coming-soon {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
        }
        .coming-soon-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .icon-large {
            font-size: 5rem;
            color: #ff6b35;
            margin-bottom: 2rem;
        }
        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 2rem 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ff6b35, #e55a2b);
            width: 75%;
            animation: progress 2s ease-in-out;
        }
        @keyframes progress {
            from { width: 0%; }
            to { width: 75%; }
        }
    </style>
</head>
<body>
    <div class="coming-soon">
        <div class="coming-soon-card">
            <div class="icon-large">
                <i class="fas fa-tools"></i>
            </div>
            
            <h2 class="mb-3">Module Coming Soon!</h2>
            <p class="text-muted mb-4">
                This module is currently under development. Our team is working hard to bring you the best ERP experience.
            </p>
            
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            
            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="border-end">
                        <h4 class="text-warning">75%</h4>
                        <small class="text-muted">Complete</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-end">
                        <h4 class="text-info">2</h4>
                        <small class="text-muted">Weeks Left</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4 class="text-success">Active</h4>
                    <small class="text-muted">Development</small>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>What's Next?</strong> This module will be available soon with full functionality including data management, reporting, and integration with other modules.
            </div>
            
            <div class="d-grid gap-2">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-warning">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
                <a href="<?= base_url('purchase-requisition') ?>" class="btn btn-outline-warning">
                    <i class="fas fa-clipboard-list me-2"></i>Try Purchase Requisition
                </a>
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Expected Release: Q1 2025
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 