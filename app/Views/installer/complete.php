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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            background: #28a745;
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
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 3rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
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
        .countdown {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .next-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h5 {
            color: #495057;
            margin-bottom: 15px;
        }
        .next-steps ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin-bottom: 8px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="installer-container">
            <div class="installer-header">
                <h1><i class="fas fa-check-circle"></i> Installation Complete!</h1>
                <p class="mb-0">Your Manufacturing ERP system is ready to use</p>
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
                    <div class="step completed">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>

                <h2 class="text-center mb-4">Congratulations!</h2>
                
                <p class="text-center text-muted mb-4">
                    Your Manufacturing ERP system has been successfully installed and configured. 
                    You can now start managing your manufacturing operations efficiently.
                </p>

                <div class="next-steps">
                    <h5><i class="fas fa-list-check"></i> What's Next?</h5>
                    <ul>
                        <li>Log in with your Super Admin credentials</li>
                        <li>Set up your departments and employees</li>
                        <li>Configure module permissions for users</li>
                        <li>Add your suppliers and customers</li>
                        <li>Create your product catalog</li>
                        <li>Set up production settings and BOMs</li>
                        <li>Start managing your manufacturing operations</li>
                    </ul>
                </div>

                <div class="text-center mt-4">
                    <div class="countdown" id="countdown">5</div>
                    <p class="text-muted">Redirecting to login page in <span id="countdownText">5</span> seconds...</p>
                    
                    <a href="<?= base_url('login') ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        For support and documentation, please refer to the help section in your dashboard.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown timer
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const countdownTextElement = document.getElementById('countdownText');
        
        const timer = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            countdownTextElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '<?= base_url('login') ?>';
            }
        }, 1000);
        
        // Stop countdown if user clicks the button
        document.querySelector('.btn-success').addEventListener('click', function() {
            clearInterval(timer);
        });
    </script>
</body>
</html>
