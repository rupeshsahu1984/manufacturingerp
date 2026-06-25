<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PRODX ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="<?= base_url('public/css/sidebar.css?v=2') ?>" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #ff6b35;
            --secondary-orange: #e55a2b;
            --light-orange: #fff5f2;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --text-gray: #666;
        }

        body {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: flex;
        }
        
        .login-left {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            color: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            flex: 1;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .login-left-content {
            position: relative;
            z-index: 2;
        }
        
        .company-logo {
            font-size: 2.5rem;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .company-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .company-tagline {
            font-size: 1rem;
            opacity: 0.9;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .erp-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .erp-features li {
            padding: 6px 0;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }
        
        .erp-features li i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
        }
        
        .login-right {
            padding: 40px 35px;
            background: var(--white);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            color: var(--text-gray);
            font-size: 0.95rem;
        }
        
        .role-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .role-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light-gray);
        }
        
        .role-option:hover {
            border-color: var(--primary-orange);
            background-color: var(--light-orange);
            transform: translateY(-2px);
        }
        
        .role-option.selected {
            border-color: var(--primary-orange);
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }
        
        .role-option i {
            font-size: 1.3rem;
            margin-bottom: 6px;
            display: block;
        }
        
        .role-option .role-name {
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 3px;
        }
        
        .role-option .role-desc {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e9ecef;
            background: var(--light-gray);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }
        
        .demo-credentials {
            background: var(--light-orange);
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
        }
        
        .demo-credentials h6 {
            color: var(--dark-gray);
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .credential-item:last-child {
            border-bottom: none;
        }
        
        .credential-label {
            font-weight: 600;
            color: var(--text-gray);
            font-size: 0.8rem;
        }
        
        .credential-value {
            background: var(--primary-orange);
            color: white;
            padding: 3px 6px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            
            .login-right {
                padding: 30px 25px;
            }
            
            .login-card {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Left Side - ERP Info -->
            <div class="login-left">
                <div class="login-left-content">
                    <div class="company-logo">
                        <i class="fas fa-industry"></i>
                    </div>
                    <div class="company-name">PRODX ERP</div>
                    <div class="company-tagline">Enterprise Resource Planning for Modern Manufacturing</div>
                    
                    <div class="mt-3">
                        <h6 class="mb-2">Key Features:</h6>
                        <ul class="erp-features">
                            <li><i class="fas fa-chart-line"></i> Real-time Analytics</li>
                            <li><i class="fas fa-cogs"></i> Production Planning</li>
                            <li><i class="fas fa-shopping-cart"></i> Procurement Management</li>
                            <li><i class="fas fa-truck"></i> Inventory & Logistics</li>
                            <li><i class="fas fa-users"></i> Human Resources</li>
                            <li><i class="fas fa-money-bill-wave"></i> Financial Management</li>
                            <li><i class="fas fa-shield-alt"></i> Quality Control</li>
                            <li><i class="fas fa-mobile-alt"></i> Mobile Ready</li>
                        </ul>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <small style="opacity: 0.8;">
                            Trusted by 500+ Manufacturing Companies
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="login-right">
                <div class="login-header">
                    <h2 class="login-title">Welcome Back</h2>
                    <p class="login-subtitle">Sign in to access your PRODX ERP dashboard</p>
                </div>
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('login') ?>" id="loginForm">
                    <!-- User Role Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Your Role</label>
                        <div class="role-selector">
                            <div class="role-option" data-role="super_admin">
                                <i class="fas fa-crown"></i>
                                <div class="role-name">Super Admin</div>
                                <div class="role-desc">Full Access</div>
                            </div>
                            <div class="role-option" data-role="purchase">
                                <i class="fas fa-shopping-cart"></i>
                                <div class="role-name">Purchase</div>
                                <div class="role-desc">Procurement</div>
                            </div>
                            <div class="role-option" data-role="sales">
                                <i class="fas fa-chart-line"></i>
                                <div class="role-name">Sales</div>
                                <div class="role-desc">Sales & Distribution</div>
                            </div>
                            <div class="role-option" data-role="production">
                                <i class="fas fa-cogs"></i>
                                <div class="role-name">Production</div>
                                <div class="role-desc">Manufacturing</div>
                            </div>
                            <div class="role-option" data-role="finance">
                                <i class="fas fa-money-bill-wave"></i>
                                <div class="role-name">Finance</div>
                                <div class="role-desc">Accounting</div>
                            </div>
                            <div class="role-option" data-role="gate_entry">
                                <i class="fas fa-truck"></i>
                                <div class="role-name">Gate Entry</div>
                                <div class="role-desc">Logistics</div>
                            </div>
                            <div class="role-option" data-role="hrm">
                                <i class="fas fa-users"></i>
                                <div class="role-name">HRM</div>
                                <div class="role-desc">Human Resources</div>
                            </div>
                            <div class="role-option" data-role="reception">
                                <i class="fas fa-user-tie"></i>
                                <div class="role-name">Reception</div>
                                <div class="role-desc">Visitor Management</div>
                            </div>
                        </div>
                        <input type="hidden" name="role" id="selectedRole" required>
                    </div>

                    <!-- Username -->
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

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In to PRODX
                        </button>
                    </div>
                </form>

                <!-- Demo Credentials -->
                <div class="demo-credentials">
                    <h6><i class="fas fa-info-circle me-2"></i>Demo Credentials</h6>
                    <div class="credential-item">
                        <span class="credential-label">Super Admin:</span>
                        <span class="credential-value">admin / admin123</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Purchase:</span>
                        <span class="credential-value">purchase / purchase123</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Sales:</span>
                        <span class="credential-value">sales / sales123</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Production:</span>
                        <span class="credential-value">production / production123</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        © 2025 PRODX ERP. All rights reserved. | Enterprise Manufacturing Solutions
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Role selection
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.role-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Set the hidden input value
                document.getElementById('selectedRole').value = this.dataset.role;
            });
        });

        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const selectedRole = document.getElementById('selectedRole').value;
            
            if (!selectedRole) {
                e.preventDefault();
                alert('Please select your role before logging in.');
                return false;
            }
        });

        // Auto-select first role
        document.querySelector('.role-option').classList.add('selected');
        document.getElementById('selectedRole').value = 'super_admin';
    </script>
</body>
</html> 