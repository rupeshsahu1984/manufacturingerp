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
        .connection-status {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            display: none;
        }
        .connection-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .connection-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="installer-container">
            <div class="installer-header">
                <h2><i class="fas fa-database"></i> Database Configuration</h2>
                <p class="mb-0">Step <?= $step ?> of <?= $total_steps ?></p>
            </div>
            
            <div class="installer-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step completed">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="step active">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="step">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="step">
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

                <div class="connection-status" id="connectionStatus"></div>

                <form method="POST" action="<?= base_url('installer/database') ?>" id="databaseForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hostname" class="form-label">
                                    <i class="fas fa-server"></i> Database Host
                                </label>
                                <input type="text" class="form-control" id="hostname" name="hostname" 
                                       value="<?= old('hostname', 'localhost') ?>" required>
                                <div class="help-text">Usually 'localhost' or '127.0.0.1'</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="port" class="form-label">
                                    <i class="fas fa-network-wired"></i> Port
                                </label>
                                <input type="number" class="form-control" id="port" name="port" 
                                       value="<?= old('port', '3306') ?>" required>
                                <div class="help-text">Default MySQL port is 3306</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Database Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= old('username', 'root') ?>" required>
                                <div class="help-text">Database user with create privileges</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Database Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       value="<?= old('password') ?>" required>
                                <div class="help-text">Password for the database user</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="database" class="form-label">
                            <i class="fas fa-database"></i> Database Name
                        </label>
                        <input type="text" class="form-control" id="database" name="database" 
                               value="<?= old('database', 'manufacturingerp') ?>" required>
                        <div class="help-text">Database will be created if it doesn't exist</div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('installer') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        
                        <div>
                            <button type="button" class="btn btn-info me-2" onclick="testConnection()">
                                <i class="fas fa-plug"></i> Test Connection
                            </button>
                            
                            <button type="submit" class="btn btn-installer">
                                <i class="fas fa-arrow-right"></i> Continue
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function testConnection() {
            const formData = new FormData(document.getElementById('databaseForm'));
            const statusDiv = document.getElementById('connectionStatus');
            
            statusDiv.style.display = 'block';
            statusDiv.className = 'connection-status';
            statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing connection...';
            
            fetch('<?= base_url('installer/testDatabase') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.className = 'connection-status connection-success';
                    statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                } else {
                    statusDiv.className = 'connection-status connection-error';
                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> ' + data.message;
                }
            })
            .catch(error => {
                statusDiv.className = 'connection-status connection-error';
                statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Connection test failed: ' + error.message;
            });
        }
    </script>
</body>
</html>
