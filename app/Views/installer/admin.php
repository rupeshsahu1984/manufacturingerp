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
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 30px;
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
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .btn-installer {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            color: white;
            padding: 12px 30px;
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
        .btn-secondary {
            background: #6c757d;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #5a6268;
            color: white;
        }
        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .password-strength {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            display: none;
        }
        .strength-weak {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .strength-medium {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .strength-strong {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .admin-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .admin-info h5 {
            color: #ff6b35;
            margin-bottom: 15px;
        }
        .admin-info ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .admin-info li {
            margin-bottom: 5px;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="installer-container">
            <div class="installer-header">
                <h2><i class="fas fa-user-shield"></i> Super Admin Setup</h2>
                <p class="mb-0">Step <?= $step ?> of <?= $total_steps ?></p>
            </div>
            
            <div class="installer-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step completed">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="step completed">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="step completed">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="step completed">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="step active">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <div class="admin-info">
                    <h5><i class="fas fa-info-circle"></i> Super Admin Account</h5>
                    <p class="mb-2">This account will have full access to all system features:</p>
                    <ul>
                        <li>Complete system administration</li>
                        <li>User and department management</li>
                        <li>Module assignments and permissions</li>
                        <li>Company settings and configuration</li>
                        <li>System backup and maintenance</li>
                    </ul>
                </div>

                <form method="POST" action="<?= base_url('installer/admin') ?>" id="adminForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_name" class="form-label">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" class="form-control" id="admin_name" name="admin_name" 
                                       value="<?= old('admin_name') ?>" required>
                                <div class="help-text">Administrator's full name</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email Address *
                                </label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                       value="<?= old('admin_email') ?>" required>
                                <div class="help-text">Will be used for login and notifications</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_username" class="form-label">
                                    <i class="fas fa-user-tag"></i> Username *
                                </label>
                                <input type="text" class="form-control" id="admin_username" name="admin_username" 
                                       value="<?= old('admin_username') ?>" required>
                                <div class="help-text">Unique username for login</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">
                                    <i class="fas fa-lock"></i> Password *
                                </label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                       value="<?= old('admin_password') ?>" required onkeyup="checkPasswordStrength()">
                                <div class="help-text">Minimum 6 characters</div>
                                <div id="passwordStrength" class="password-strength"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock"></i> Confirm Password *
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               value="<?= old('confirm_password') ?>" required onkeyup="checkPasswordMatch()">
                        <div class="help-text">Re-enter your password</div>
                        <div id="passwordMatch" class="password-strength"></div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('installer/company') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        
                        <button type="submit" class="btn btn-installer" id="submitBtn">
                            <i class="fas fa-check"></i> Complete Installation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('admin_password').value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }
            
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthDiv.style.display = 'block';
            
            if (strength <= 2) {
                strengthDiv.className = 'password-strength strength-weak';
                feedback = 'Weak password';
            } else if (strength <= 4) {
                strengthDiv.className = 'password-strength strength-medium';
                feedback = 'Medium strength password';
            } else {
                strengthDiv.className = 'password-strength strength-strong';
                feedback = 'Strong password';
            }
            
            strengthDiv.innerHTML = `<i class="fas fa-shield-alt"></i> ${feedback}`;
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('admin_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchDiv.style.display = 'none';
                return;
            }
            
            matchDiv.style.display = 'block';
            
            if (password === confirmPassword) {
                matchDiv.className = 'password-strength strength-strong';
                matchDiv.innerHTML = '<i class="fas fa-check"></i> Passwords match';
            } else {
                matchDiv.className = 'password-strength strength-weak';
                matchDiv.innerHTML = '<i class="fas fa-times"></i> Passwords do not match';
            }
        }
        
        // Form validation
        document.getElementById('adminForm').addEventListener('submit', function(e) {
            const password = document.getElementById('admin_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>
</html>
