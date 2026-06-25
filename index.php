<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>PRODX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: "Segoe UI", sans-serif; }
        .header { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%); color: white; padding: 1rem 0; }
        .navbar-warning { background-color: #ff6b35 !important; }
        .navbar-warning .nav-link { color: white !important; font-weight: 600; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .card-header { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%); color: white; border: none; }
        .btn-warning { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%); border: none; color: white; }
        .bg-gradient-warning { background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%) !important; }
        .bg-gradient-primary { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important; }
    </style>
</head>
<body>
    <?php
    // Redirect to the new CodeIgniter dashboard
    header("Location: public/");
    exit;
    ?>
    
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo">
                        <i class="fas fa-industry text-warning"></i>
                        <span class="ms-2 fw-bold">PRODX</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="#" class="btn btn-outline-light btn-sm me-2">About Us</a>
                        <a href="#" class="btn btn-warning btn-sm me-2">CUSTOMER CARE</a>
                        <a href="logout.php" class="btn btn-dark btn-sm">LOG OUT</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-warning">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt me-1"></i>OVERVIEW</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-boxes me-1"></i>INVENTORY
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="suppliers.php">Suppliers</a></li>
                            <li><a class="dropdown-item" href="products.php">Products</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-shopping-cart me-1"></i>SALES
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="customers.php">Customers</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Left Sidebar -->
                <div class="col-lg-3">
                    <!-- User Profile Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">MONA S</h6>
                                    <small class="text-muted">Administrator</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning btn-sm">PERSONAL DETAILS</button>
                                <button class="btn btn-outline-warning btn-sm">GENERATE CARD PIN</button>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                Last visited 29/08/2024 15:39:48 IST
                            </small>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-star text-warning me-2"></i>QUICK LINKS</h6>
                            <button class="btn btn-warning btn-sm">CUSTOMIZE</button>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><a href="#" class="text-decoration-none text-dark"><i class="fas fa-chevron-right text-warning me-2"></i>Bank Account e-statement</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-dark"><i class="fas fa-chevron-right text-warning me-2"></i>Credit Card e-statement</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-dark"><i class="fas fa-chevron-right text-warning me-2"></i>Cheque Book Request</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-dark"><i class="fas fa-chevron-right text-warning me-2"></i>Forex & Travel Cards</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-dark"><i class="fas fa-chevron-right text-warning me-2"></i>PPF Account</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-dark"><i class="fas fa-chevron-right text-warning me-2"></i>Invest in Mutual Funds</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-lg-9">
                    <!-- Promotional Banner -->
                    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <strong>Life cover in 3 simple steps</strong><br>
                            <small>Buy ICICI Pru iProtect Smart term plan online to get 5% discount. Also get tax benefits.</small>
                        </div>
                        <i class="fas fa-arrow-right text-warning"></i>
                    </div>

                    <!-- Account Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-2">Current Account</h6>
                                    <select class="form-select form-select-sm mb-3" style="width: 200px;">
                                        <option>(INR) - MONA</option>
                                    </select>
                                    <a href="#" class="text-primary">View All Accounts</a>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h4 class="text-success fw-bold mb-2">₹ 83,796.52</h4>
                                    <small class="text-muted">Account Balance</small>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-3 col-6 mb-3">
                                    <button class="btn btn-outline-warning w-100">
                                        <i class="fas fa-exchange-alt mb-2"></i><br>
                                        <small>Fund Transfer</small>
                                    </button>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <button class="btn btn-outline-warning w-100">
                                        <i class="fas fa-file-alt mb-2"></i><br>
                                        <small>Statement</small>
                                    </button>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <button class="btn btn-outline-warning w-100">
                                        <i class="fas fa-receipt mb-2"></i><br>
                                        <small>Bill Payments</small>
                                    </button>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <button class="btn btn-outline-warning w-100">
                                        <i class="fas fa-user mb-2"></i><br>
                                        <small>Balance Details</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Cards -->
                    <div class="row">
                        <!-- CIBIL Score Card -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>MY CIBIL</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="text-warning fw-bold mb-3">751 - 801</h3>
                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 75%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted mb-3">
                                        <span>300</span>
                                        <span>900</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-download me-1"></i>Download Report
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-plus me-1"></i>Credit Health
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- iWish Card -->
                        <div class="col-lg-4 mb-4">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-star me-2"></i>IWISH - THE FLEXIBLE RECURRING DEPOSIT</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small mb-3">Put aside any amount anytime with iWish Flexible RD and earn interest rate like FD/RD</p>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small>Target Amount:</small><br>
                                            <strong>₹50,000</strong>
                                        </div>
                                        <div class="col-6">
                                            <small>Tenure:</small><br>
                                            <strong>12 months</strong>
                                        </div>
                                    </div>
                                    <button class="btn btn-light btn-sm w-100">CREATE NOW</button>
                                </div>
                            </div>
                        </div>

                        <!-- Pre-qualified Offers -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-percentage me-2"></i>PRE-QUALIFIED OFFERS</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="bg-gradient-primary text-white p-3 rounded">
                                                <small>Get Personal loan on Credit Card up to Rs. 5,00,000</small>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="bg-gradient-warning text-white p-3 rounded">
                                                <small>PERSONAL LOAN upto Rs. 14,05,000 for 5 years. Disbursal in 3 seconds</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>