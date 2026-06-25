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
        .progress {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
            margin: 20px 0;
        }
        .progress-bar {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        .install-step {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .install-step:last-child {
            border-bottom: none;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #6c757d;
        }
        .step-icon.completed {
            background: #28a745;
            color: white;
        }
        .step-icon.processing {
            background: #ff6b35;
            color: white;
            animation: pulse 1.5s infinite;
        }
        .step-icon.error {
            background: #dc3545;
            color: white;
        }
        .step-content {
            flex: 1;
        }
        .step-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .step-description {
            font-size: 14px;
            color: #6c757d;
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
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .spinner {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="installer-container">
            <div class="installer-header">
                <h2><i class="fas fa-cogs"></i> System Installation</h2>
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
                    <div class="step active">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="step">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="step">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <h3 class="text-center mb-4">Installing System Components</h3>
                
                <div class="progress">
                    <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                </div>

                <div class="install-steps">
                    <div class="install-step" id="step1">
                        <div class="step-icon" id="icon1">
                            <i class="fas fa-spinner spinner"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Updating Database Configuration</div>
                            <div class="step-description">Configuring database connection settings...</div>
                        </div>
                    </div>

                    <div class="install-step" id="step2">
                        <div class="step-icon" id="icon2">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Creating Database Tables</div>
                            <div class="step-description">Setting up all required database tables...</div>
                        </div>
                    </div>

                    <div class="install-step" id="step3">
                        <div class="step-icon" id="icon3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Initializing System Settings</div>
                            <div class="step-description">Setting up default system configurations...</div>
                        </div>
                    </div>

                    <div class="install-step" id="step4">
                        <div class="step-icon" id="icon4">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Creating Upload Directories</div>
                            <div class="step-description">Setting up file upload directories...</div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div id="statusMessage" class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Installation in progress...
                    </div>
                    
                    <a href="<?= base_url('installer/company') ?>" class="btn btn-installer" id="continueBtn" style="display: none;">
                        <i class="fas fa-arrow-right"></i> Continue to Company Setup
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulate installation process
        const steps = [
            { id: 1, title: 'Updating Database Configuration', duration: 2000 },
            { id: 2, title: 'Creating Database Tables', duration: 3000 },
            { id: 3, title: 'Initializing System Settings', duration: 2000 },
            { id: 4, title: 'Creating Upload Directories', duration: 1500 }
        ];

        let currentStep = 0;
        const progressBar = document.getElementById('progressBar');
        const statusMessage = document.getElementById('statusMessage');
        const continueBtn = document.getElementById('continueBtn');

        function updateProgress(step, status) {
            const stepElement = document.getElementById(`step${step}`);
            const iconElement = document.getElementById(`icon${step}`);
            
            // Update icon
            iconElement.className = 'step-icon ' + status;
            
            if (status === 'processing') {
                iconElement.innerHTML = '<i class="fas fa-spinner spinner"></i>';
            } else if (status === 'completed') {
                iconElement.innerHTML = '<i class="fas fa-check"></i>';
            } else if (status === 'error') {
                iconElement.innerHTML = '<i class="fas fa-times"></i>';
            }
            
            // Update progress bar
            const progress = ((step - 1) / steps.length) * 100;
            progressBar.style.width = progress + '%';
        }

        function processStep(stepIndex) {
            if (stepIndex >= steps.length) {
                // All steps completed
                progressBar.style.width = '100%';
                statusMessage.className = 'alert alert-success';
                statusMessage.innerHTML = '<i class="fas fa-check-circle"></i> Installation completed successfully!';
                continueBtn.style.display = 'inline-block';
                return;
            }

            const step = steps[stepIndex];
            currentStep = stepIndex + 1;
            
            // Start processing current step
            updateProgress(currentStep, 'processing');
            statusMessage.innerHTML = `<i class="fas fa-cog fa-spin"></i> ${step.title}...`;
            
            // Simulate processing time
            setTimeout(() => {
                // Mark step as completed
                updateProgress(currentStep, 'completed');
                
                // Process next step
                setTimeout(() => {
                    processStep(stepIndex + 1);
                }, 500);
            }, step.duration);
        }

        // Start installation process
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                processStep(0);
            }, 1000);
        });
    </script>
</body>
</html>
