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
            --brand: #f05a22;
            --brand-soft: #fff3ed;
            --ink: #111827;
            --muted: #667085;
            --line: #d7dde7;
            --panel: #182230;
            --page: #eef2f6;
            --white: #fff;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            background: var(--page);
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            padding: 0;
        }

        .login-frame {
            width: 100%;
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(360px, 42vw) minmax(0, 1fr);
            overflow: hidden;
            background: linear-gradient(90deg, #111827 0%, #111827 42%, #f8fafc 42%, #f8fafc 100%);
        }

        .system-panel {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(36px, 5vw, 72px);
            color: #fff;
            background:
                linear-gradient(180deg, rgba(29, 41, 57, .94) 0%, rgba(17, 24, 39, .98) 100%),
                repeating-linear-gradient(90deg, rgba(255,255,255,.04) 0 1px, transparent 1px 72px),
                repeating-linear-gradient(0deg, rgba(255,255,255,.035) 0 1px, transparent 1px 72px);
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 56px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: var(--brand);
        }

        .brand-title {
            font-size: 17px;
            font-weight: 800;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.62);
            font-size: 12px;
        }

        .system-panel h1 {
            margin: 0 0 12px;
            max-width: 520px;
            font-size: clamp(30px, 3vw, 48px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: 0;
        }

        .system-panel p {
            margin: 0;
            max-width: 520px;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.65;
        }

        .module-list {
            display: grid;
            gap: 10px;
            width: min(520px, 100%);
            margin-top: 42px;
        }

        .module-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.84);
            font-size: 13px;
        }

        .module-item i {
            width: 18px;
            color: #ffb088;
            text-align: center;
        }

        .login-panel {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(28px, 5vw, 76px);
            background: #f8fafc;
        }

        .login-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            width: min(760px, 100%);
            margin: 0 auto 26px;
        }

        .login-top h2 {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .login-top p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .environment-badge {
            padding: 7px 10px;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            color: #067647;
            background: #f0fdf4;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        #loginForm,
        .credential-panel,
        .login-footer,
        .alert {
            width: min(760px, 100%);
            margin-left: auto;
            margin-right: auto;
        }

        .role-option,
        .credential-btn {
            border: 1px solid var(--line);
            background: #fff;
            text-align: left;
            cursor: pointer;
            transition: border-color .16s ease, background .16s ease, box-shadow .16s ease;
        }

        .role-option {
            min-height: 74px;
            padding: 10px;
            border-radius: 10px;
        }

        .role-option:hover,
        .role-option.selected,
        .credential-btn:hover {
            border-color: var(--brand);
            background: var(--brand-soft);
        }

        .role-option.selected {
            box-shadow: 0 0 0 2px rgba(240, 90, 34, .12);
        }

        .role-option i {
            color: var(--brand);
            font-size: 16px;
            margin-bottom: 6px;
        }

        .role-name {
            font-size: 12px;
            font-weight: 800;
        }

        .role-desc {
            color: var(--muted);
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .form-label {
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 700;
        }

        .input-group-text,
        .form-control {
            min-height: 46px;
            border-color: var(--line);
        }

        .input-group-text {
            width: 46px;
            justify-content: center;
            background: #f8fafc;
            color: var(--muted);
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem rgba(240, 90, 34, .12);
        }

        .btn-login {
            min-height: 48px;
            border: 0;
            border-radius: 9px;
            background: var(--brand);
            color: #fff;
            font-weight: 800;
        }

        .btn-login:hover {
            background: #d94d1b;
            color: #fff;
        }

        .credential-panel {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
        }

        .credential-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .credential-heading h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
        }

        .credential-heading span {
            color: var(--muted);
            font-size: 12px;
        }

        .credential-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .credential-btn {
            padding: 10px 12px;
            border-radius: 9px;
        }

        .credential-btn strong,
        .credential-btn span {
            display: block;
        }

        .credential-btn strong {
            font-size: 12px;
        }

        .credential-btn span {
            margin-top: 2px;
            color: var(--muted);
            font-size: 12px;
        }

        .login-footer {
            margin-top: 16px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 960px) {
            .login-frame { grid-template-columns: 1fr; }
            .system-panel { display: none; }
            .login-panel { background: #fff; }
        }

        @media (max-width: 640px) {
            .login-panel { padding: 26px 18px; }
            .login-top { display: block; }
            .environment-badge { display: inline-flex; margin-top: 12px; }
            .role-grid, .credential-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
<?php
    $credentials = [
        ['role' => 'super_admin', 'label' => 'Super Admin', 'desc' => 'Full access', 'icon' => 'fa-crown', 'username' => 'admin', 'password' => 'Admin@2026'],
        ['role' => 'purchase', 'label' => 'Purchase', 'desc' => 'Procurement', 'icon' => 'fa-cart-shopping', 'username' => 'purchase', 'password' => 'Purchase@2026'],
        ['role' => 'sales', 'label' => 'Sales', 'desc' => 'Orders', 'icon' => 'fa-chart-line', 'username' => 'sales', 'password' => 'Sales@2026'],
        ['role' => 'production', 'label' => 'Production', 'desc' => 'Shop floor', 'icon' => 'fa-gears', 'username' => 'production', 'password' => 'Production@2026'],
        ['role' => 'finance', 'label' => 'Finance', 'desc' => 'Accounts', 'icon' => 'fa-file-invoice-dollar', 'username' => 'finance', 'password' => 'Finance@2026'],
        ['role' => 'gate_entry', 'label' => 'Gate Entry', 'desc' => 'Logistics', 'icon' => 'fa-truck-ramp-box', 'username' => 'gate_entry', 'password' => 'Gate@2026'],
        ['role' => 'hrm', 'label' => 'HRM', 'desc' => 'People', 'icon' => 'fa-users', 'username' => 'hrm', 'password' => 'Hrm@2026'],
        ['role' => 'reception', 'label' => 'Reception', 'desc' => 'Front desk', 'icon' => 'fa-id-card', 'username' => 'reception', 'password' => 'Reception@2026'],
    ];
?>
    <main class="login-shell">
        <section class="login-frame">
            <aside class="system-panel">
                <div class="brand-row">
                    <div class="brand-mark"><i class="fas fa-industry"></i></div>
                    <div>
                        <div class="brand-title">PRODX ERP</div>
                        <div class="brand-subtitle">Manufacturing operations suite</div>
                    </div>
                </div>

                <h1>Secure access for your factory operations.</h1>
                <p>Manage procurement, inventory, production, sales, finance, HR, and reporting from one role-based workspace.</p>

                <div class="module-list">
                    <div class="module-item"><i class="fas fa-boxes-stacked"></i> Inventory and warehouse control</div>
                    <div class="module-item"><i class="fas fa-cart-shopping"></i> Purchase bills, orders, and vendors</div>
                    <div class="module-item"><i class="fas fa-gears"></i> Production planning and job tracking</div>
                    <div class="module-item"><i class="fas fa-chart-column"></i> Operational reports and analytics</div>
                </div>
            </aside>

            <section class="login-panel">
                <div class="login-top">
                    <div>
                        <h2>Sign in to PRODX</h2>
                        <p>Select a role to auto-fill the login fields.</p>
                    </div>
                    <div class="environment-badge"><i class="fas fa-circle-check me-1"></i> Online</div>
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
                        <small class="text-muted">Role based access</small>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-right-to-bracket me-2"></i>Access Dashboard
                    </button>
                </form>

                <div class="credential-panel">
                    <div class="credential-heading">
                        <h3><i class="fas fa-key me-2"></i>Quick credentials</h3>
                        <span>Click to fill</span>
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
