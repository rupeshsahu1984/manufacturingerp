<?php
// Simple Web Installer for Manufacturing ERP
// No Composer Required - Perfect for Non-Technical Users

if (file_exists('installed.txt')) {
    header('Location: index.php');
    exit;
}

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$error = '';

// Check requirements
$php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
$extensions_ok = extension_loaded('mysqli') && extension_loaded('json');
$writable_ok = true;

$dirs = ['config', 'uploads', 'logs'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!is_writable($dir)) $writable_ok = false;
}

if ($_POST && $step == 2) {
    $host = $_POST['host'] ?? 'localhost';
    $dbname = $_POST['dbname'] ?? 'manufacturingerp';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    
    try {
        // Connect to MySQL server
        $mysqli = new mysqli($host, $username, $password);
        
        if ($mysqli->connect_error) {
            throw new Exception("Connection failed: " . $mysqli->connect_error);
        }
        
        // Create database
        $mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $mysqli->select_db($dbname);
        
        // Create tables
        $mysqli->query("CREATE TABLE users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'manager', 'operator') DEFAULT 'operator',
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $mysqli->query("CREATE TABLE suppliers (
            id INT PRIMARY KEY AUTO_INCREMENT,
            supplier_code VARCHAR(20) UNIQUE NOT NULL,
            supplier_name VARCHAR(100) NOT NULL,
            contact_person VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            address TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $mysqli->query("CREATE TABLE customers (
            id INT PRIMARY KEY AUTO_INCREMENT,
            customer_code VARCHAR(20) UNIQUE NOT NULL,
            customer_name VARCHAR(100) NOT NULL,
            contact_person VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            address TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $mysqli->query("CREATE TABLE products (
            id INT PRIMARY KEY AUTO_INCREMENT,
            product_code VARCHAR(20) UNIQUE NOT NULL,
            product_name VARCHAR(100) NOT NULL,
            description TEXT,
            unit VARCHAR(20) DEFAULT 'PCS',
            cost_price DECIMAL(15,2) DEFAULT 0,
            selling_price DECIMAL(15,2) DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Insert admin user
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        $mysqli->query("INSERT INTO users (username, email, password, full_name, role) VALUES 
                    ('admin', 'admin@manufacturingerp.com', '$admin_password', 'System Administrator', 'admin')");
        
        // Create config file
        $config = "<?php
define('DB_HOST', '$host');
define('DB_NAME', '$dbname');
define('DB_USER', '$username');
define('DB_PASS', '$password');

function getDB() {
    \$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (\$mysqli->connect_error) {
        die('Database connection failed: ' . \$mysqli->connect_error);
    }
    return \$mysqli;
}
?>";
        
        file_put_contents('config/database.php', $config);
        
        // Create main application files
        createMainApp();
        
        // Mark as installed
        file_put_contents('installed.txt', date('Y-m-d H:i:s'));
        
        $step = 3;
        
    } catch (Exception $e) {
        $error = 'Database connection failed: ' . $e->getMessage();
    }
}

function createMainApp() {
    // Create main index.php
    $index_content = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturing ERP</title>
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
    require_once "config/database.php";
    session_start();
    
    if (!isset($_SESSION["user_id"]) && basename($_SERVER["PHP_SELF"]) != "login.php") {
        header("Location: login.php");
        exit;
    }
    ?>
    
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo">
                        <i class="fas fa-industry text-warning"></i>
                        <span class="ms-2 fw-bold">Manufacturing ERP</span>
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
</html>';
    
    file_put_contents('index.php', $index_content);
    
    // Create login page
    $login_content = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manufacturing ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php
    require_once "config/database.php";
    session_start();
    
    if ($_POST) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $mysqli = getDB();
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
    ?>
    
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="fas fa-industry fa-3x text-warning mb-3"></i>
            <h3 class="fw-bold">Manufacturing ERP</h3>
            <p class="text-muted">Sign in to your account</p>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
        </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                Default: admin / password
            </small>
        </div>
    </div>
</body>
</html>';
    
    file_put_contents('login.php', $login_content);
    
    // Create logout page
    $logout_content = '<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>';
    
    file_put_contents('logout.php', $logout_content);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturing ERP - Simple Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin: 2rem auto;
            max-width: 800px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
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
        }
        .step.active {
            background: #ff6b35;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .btn-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #e55a2b 0%, #ff6b35 100%);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="installer-card p-4">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="fas fa-industry fa-3x text-warning mb-3"></i>
                <h2 class="fw-bold">Manufacturing ERP - Simple Installer</h2>
                <p class="text-muted">No Composer Required - Perfect for Non-Technical Users</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?= $step >= 1 ? 'active' : '' ?>">1</div>
                <div class="step <?= $step >= 2 ? 'active' : '' ?>">2</div>
                <div class="step <?= $step >= 3 ? 'completed' : '' ?>">3</div>
            </div>

            <?php if ($step == 1): ?>
            <!-- Step 1: System Check -->
            <div class="text-center">
                <h4 class="mb-4">System Requirements Check</h4>
                
                <div class="row text-start">
                    <div class="col-md-6">
                        <h6>PHP Version</h6>
                        <p class="<?= $php_ok ? 'status-ok' : 'status-error' ?>">
                            <i class="fas fa-<?= $php_ok ? 'check' : 'times' ?>"></i>
                            PHP <?= PHP_VERSION ?> <?= $php_ok ? '(OK)' : '(Required: 7.4+)' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>PHP Extensions</h6>
                        <p class="<?= $extensions_ok ? 'status-ok' : 'status-error' ?>">
                            <i class="fas fa-<?= $extensions_ok ? 'check' : 'times' ?>"></i>
                            <?= $extensions_ok ? 'All Required Extensions (OK)' : 'Missing Extensions' ?>
                        </p>
                    </div>
                </div>

                <div class="row text-start mt-3">
                    <div class="col-12">
                        <h6>Directory Permissions</h6>
                        <p class="<?= $writable_ok ? 'status-ok' : 'status-error' ?>">
                            <i class="fas fa-<?= $writable_ok ? 'check' : 'times' ?>"></i>
                            Directories: <?= $writable_ok ? 'Writable (OK)' : 'Not Writable' ?>
                        </p>
                    </div>
                </div>

                <?php if ($php_ok && $extensions_ok && $writable_ok): ?>
                <div class="mt-4">
                    <form method="post">
                        <input type="hidden" name="step" value="2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right me-2"></i>Continue to Database Setup
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="alert alert-danger mt-4">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>System Requirements Not Met</h6>
                    <p class="mb-0">Please fix the issues above before continuing with the installation.</p>
                </div>
                <?php endif; ?>
            </div>

            <?php elseif ($step == 2): ?>
            <!-- Step 2: Database Configuration -->
            <div class="text-center">
                <h4 class="mb-4">Database Configuration</h4>
                
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                </div>
                <?php endif; ?>

                <form method="post" class="text-start">
                    <input type="hidden" name="step" value="2">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Host</label>
                            <input type="text" name="host" class="form-control" value="localhost" required>
                            <small class="text-muted">Usually 'localhost' for local installations</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" name="dbname" class="form-control" value="manufacturingerp" required>
                            <small class="text-muted">The database will be created if it doesn't exist</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Username</label>
                            <input type="text" name="username" class="form-control" value="root" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" name="password" class="form-control" value="">
                            <small class="text-muted">Leave empty if no password is set</small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Default Login Credentials</h6>
                        <p class="mb-0">
                            <strong>Username:</strong> admin<br>
                            <strong>Password:</strong> password
                        </p>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-database me-2"></i>Install Manufacturing ERP
                        </button>
                    </div>
                </form>
            </div>

            <?php elseif ($step == 3): ?>
            <!-- Step 3: Installation Complete -->
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                
                <h4 class="text-success mb-3">Installation Completed Successfully!</h4>
                
                <div class="alert alert-success">
                    <h6><i class="fas fa-check me-2"></i>What was installed:</h6>
                    <ul class="text-start mb-0">
                        <li>Database with all tables</li>
                        <li>Configuration files</li>
                        <li>ICICI Bank-inspired theme</li>
                        <li>Default admin user</li>
                        <li>Complete ERP application</li>
                    </ul>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6><i class="fas fa-user me-2"></i>Login Credentials</h6>
                                <p class="mb-1"><strong>Username:</strong> admin</p>
                                <p class="mb-0"><strong>Password:</strong> password</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6><i class="fas fa-link me-2"></i>Access Your ERP</h6>
                                <p class="mb-0">Click the button below to access your Manufacturing ERP system</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-success btn-lg me-3">
                        <i class="fas fa-rocket me-2"></i>Launch Manufacturing ERP
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="fas fa-print me-2"></i>Print Instructions
                    </button>
                </div>

                <div class="alert alert-warning mt-4">
                    <h6><i class="fas fa-shield-alt me-2"></i>Security Reminder</h6>
                    <p class="mb-0">For security reasons, please delete this installer file after accessing your ERP system.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 