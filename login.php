<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Database connection
    try {
        $mysqli = new mysqli('localhost', 'root', '', 'manufacturingerp');
        
        if ($mysqli->connect_error) {
            $error = "Database connection failed";
        } else {
            $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = "Invalid username or password";
                }
                $stmt->close();
            } else {
                $error = "Database query failed";
            }
            
            $mysqli->close();
        }
    } catch (mysqli_sql_exception $e) {
        $error = "Database error occurred";
    } catch (Exception $e) {
        $error = "An unexpected error occurred";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PRODX ERP</title>
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            border: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-industry fa-3x mb-3"></i>
                        <h2 class="mb-2">PRODX ERP</h2>
                        <p class="mb-0">Smarter Control for Modern Manufacturing</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Enter your username" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Enter your password" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to PRODX
                                </button>
                            </div>
                        </form>

                        <!-- Demo Credentials -->
                        <div class="mt-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>Demo Credentials
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Super Admin:</small><br>
                                            <code>admin / admin123</code>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Purchase:</small><br>
                                            <code>purchase / purchase123</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                © 2025 PRODX ERP. All rights reserved.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>