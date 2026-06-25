<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PRODX ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #f15a2b;
            --brand-dark: #c83f18;
            --ink: #17212f;
            --muted: #667085;
            --line: #d9e0ea;
            --surface: #ffffff;
            --soft: #f5f7fb;
            --panel: #101828;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 18%, rgba(241, 90, 43, 0.14), transparent 28%),
                linear-gradient(135deg, #eef2f7 0%, #f7f9fc 48%, #eef3f8 100%);
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 18px;
        }

        .login-card {
            width: min(1180px, 100%);
            min-height: 720px;
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            overflow: hidden;
            border: 1px solid rgba(16, 24, 40, 0.08);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.16);
        }

        .brand-panel {
            position: relative;
            padding: 52px;
            color: #fff;
            background:
                linear-gradient(rgba(16, 24, 40, 0.88), rgba(16, 24, 40, 0.88)),
                url("https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1200&q=80") center/cover;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 72px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: var(--brand);
            box-shadow: 0 12px 28px rgba(241, 90, 43, 0.36);
        }

        .brand-panel h1 {
            max-width: 500px;
            margin: 0 0 18px;
            font-size: 42px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand-panel p {
            max-width: 520px;
            margin: 0;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.7;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 52px;
        }

        .metric {
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
        }

        .metric strong {
            display: block;
            margin-bottom: 4px;
            font-size: 22px;
        }

        .metric span {
            color: rgba(255, 255, 255, 0.72);
            font-size: 13px;
        }

        .form-panel {
            padding: 42px 48px;
            background: linear-gradient(180deg, #fff 0%, #fbfcfe 100%);
        }

        .login-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 24px;
        }

        .login-header h2 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .login-header p {
            margin: 0;
            color: var(--muted);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border: 1px solid #c7f0d8;
            border-radius: 999px;
            color: #067647;
            background: #ecfdf3;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 22px;
        }

        .role-option {
            min-height: 88px;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 10px;
            text-align: left;
            cursor: pointer;
            background: #fff;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .role-option:hover,
        .role-option.selected {
            border-color: var(--brand);
            box-shadow: 0 10px 28px rgba(241, 90, 43, 0.14);
            transform: translateY(-1px);
        }

        .role-option.selected {
            background: #fff7f3;
        }

        .role-option i {
            color: var(--brand);
            font-size: 18px;
            margin-bottom: 8px;
        }

        .role-name {
            font-size: 13px;
            font-weight: 800;
        }

        .role-desc {
            margin-top: 2px;
            color: var(--muted);
            font-size: 11px;
        }

        .form-label {
            font-weight: 700;
            color: #263244;
        }

        .input-group-text,
        .form-control {
            min-height: 50px;
            border-color: var(--line);
        }

        .input-group-text {
            width: 50px;
            justify-content: center;
            color: var(--muted);
            background: var(--soft);
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.2rem rgba(241, 90, 43, 0.13);
        }

        .btn-login {
            min-height: 52px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            font-weight: 800;
            box-shadow: 0 14px 28px rgba(241, 90, 43, 0.24);
        }

        .credential-panel {
            margin-top: 24px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
        }

        .credential-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .credential-header h6 {
            margin: 0;
            font-weight: 800;
        }

        .credential-header span {
            color: var(--muted);
            font-size: 12px;
        }

        .credential-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .credential-btn {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 11px 12px;
            text-align: left;
            color: var(--ink);
            background: var(--soft);
            transition: border-color 0.18s ease, background 0.18s ease;
        }

        .credential-btn:hover {
            border-color: var(--brand);
            background: #fff7f3;
        }

        .credential-btn strong,
        .credential-btn span {
            display: block;
        }

        .credential-btn strong {
            font-size: 13px;
        }

        .credential-btn span {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
        }

        .login-footer {
            margin-top: 18px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 980px) {
            .login-card {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                display: none;
            }
        }

        @media (max-width: 620px) {
            .login-shell {
                padding: 0;
            }

            .login-card {
                min-height: 100vh;
                border-radius: 0;
            }

            .form-panel {
                padding: 28px 18px;
            }

            .login-header {
                display: block;
            }

            .status-pill {
                margin-top: 14px;
            }

            .role-grid,
            .credential-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
<?php
    $credentials = [
        ['role' => 'super_admin', 'label' => 'Super Admin', 'desc' => 'Full access', 'icon' => 'fa-crown', 'username' => 'admin', 'password' => 'Admin@2026'],
        ['role' => 'purchase', 'label' => 'Purchase', 'desc' => 'Procurement', 'icon' => 'fa-cart-shopping', 'username' => 'purchase', 'password' => 'Purchase@2026'],
        ['role' => 'sales', 'label' => 'Sales', 'desc' => 'Orders and dispatch', 'icon' => 'fa-chart-line', 'username' => 'sales', 'password' => 'Sales@2026'],
        ['role' => 'production', 'label' => 'Production', 'desc' => 'Manufacturing', 'icon' => 'fa-gears', 'username' => 'production', 'password' => 'Production@2026'],
        ['role' => 'finance', 'label' => 'Finance', 'desc' => 'Accounting', 'icon' => 'fa-file-invoice-dollar', 'username' => 'finance', 'password' => 'Finance@2026'],
        ['role' => 'gate_entry', 'label' => 'Gate Entry', 'desc' => 'Logistics', 'icon' => 'fa-truck-ramp-box', 'username' => 'gate_entry', 'password' => 'Gate@2026'],
        ['role' => 'hrm', 'label' => 'HRM', 'desc' => 'People ops', 'icon' => 'fa-users', 'username' => 'hrm', 'password' => 'Hrm@2026'],
        ['role' => 'reception', 'label' => 'Reception', 'desc' => 'Front desk', 'icon' => 'fa-id-card', 'username' => 'reception', 'password' => 'Reception@2026'],
    ];
?>
    <main class="login-shell">
        <section class="login-card">
            <aside class="brand-panel">
                <div class="brand-mark">
                    <span class="brand-icon"><i class="fas fa-industry"></i></span>
                    <span>PRODX ERP</span>
                </div>
                <h1>Manufacturing operations, finance, and teams in one control center.</h1>
                <p>Track procurement, production, inventory, sales, HR, accounting, and reporting from a single role-aware ERP workspace.</p>

                <div class="metric-grid">
                    <div class="metric">
                        <strong>8</strong>
                        <span>Core business modules</span>
                    </div>
                    <div class="metric">
                        <strong>24/7</strong>
                        <span>Plant-ready access</span>
                    </div>
                    <div class="metric">
                        <strong>Live</strong>
                        <span>Inventory and sales views</span>
                    </div>
                    <div class="metric">
                        <strong>Secure</strong>
                        <span>Role based sign in</span>
                    </div>
                </div>
            </aside>

            <section class="form-panel">
                <div class="login-header">
                    <div>
                        <h2>Sign in</h2>
                        <p>Use a role below or enter your assigned ERP credentials.</p>
                    </div>
                    <div class="status-pill"><i class="fas fa-circle-check"></i> ERP Online</div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-triangle-exclamation me-2"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-circle-check me-2"></i>
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('login') ?>" id="loginForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <div class="role-grid">
                            <?php foreach ($credentials as $credential): ?>
                                <button type="button"
                                        class="role-option"
                                        data-role="<?= esc($credential['role']) ?>"
                                        data-username="<?= esc($credential['username']) ?>"
                                        data-password="<?= esc($credential['password']) ?>">
                                    <i class="fas <?= esc($credential['icon']) ?>"></i>
                                    <div class="role-name"><?= esc($credential['label']) ?></div>
                                    <div class="role-desc"><?= esc($credential['desc']) ?></div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="role" id="selectedRole" value="super_admin" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required autocomplete="username">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required autocomplete="current-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <small class="text-muted">PRODX Manufacturing ERP</small>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-right-to-bracket me-2"></i>Access Dashboard
                    </button>
                </form>

                <div class="credential-panel">
                    <div class="credential-header">
                        <h6><i class="fas fa-key me-2"></i>Quick Login Credentials</h6>
                        <span>Click any account to fill the form</span>
                    </div>
                    <div class="credential-grid">
                        <?php foreach ($credentials as $credential): ?>
                            <button type="button"
                                    class="credential-btn"
                                    data-role="<?= esc($credential['role']) ?>"
                                    data-username="<?= esc($credential['username']) ?>"
                                    data-password="<?= esc($credential['password']) ?>">
                                <strong><?= esc($credential['label']) ?></strong>
                                <span><?= esc($credential['username']) ?> / <?= esc($credential['password']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="login-footer">
                    &copy; <?= date('Y') ?> PRODX ERP. Enterprise Manufacturing Solutions.
                </div>
            </section>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const selectedRoleInput = document.getElementById('selectedRole');
        const roleButtons = document.querySelectorAll('.role-option');

        function selectCredential(button) {
            const role = button.dataset.role;
            selectedRoleInput.value = role;
            usernameInput.value = button.dataset.username || '';
            passwordInput.value = button.dataset.password || '';

            roleButtons.forEach((roleButton) => {
                roleButton.classList.toggle('selected', roleButton.dataset.role === role);
            });

            usernameInput.dispatchEvent(new Event('input', { bubbles: true }));
            passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        document.querySelectorAll('.role-option, .credential-btn').forEach((button) => {
            button.addEventListener('click', () => selectCredential(button));
        });

        document.getElementById('togglePassword').addEventListener('click', function () {
            const icon = this.querySelector('i');
            const visible = passwordInput.type === 'text';
            passwordInput.type = visible ? 'password' : 'text';
            icon.classList.toggle('fa-eye', visible);
            icon.classList.toggle('fa-eye-slash', !visible);
            this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });

        document.getElementById('loginForm').addEventListener('submit', function (event) {
            if (!selectedRoleInput.value) {
                event.preventDefault();
                alert('Please select your role before logging in.');
            }
        });

        selectCredential(document.querySelector('.role-option[data-role="super_admin"]'));
    </script>
</body>
</html>
