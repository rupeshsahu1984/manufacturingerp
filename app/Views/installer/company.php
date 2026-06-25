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
            max-width: 700px;
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
        .logo-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .logo-upload:hover {
            border-color: #ff6b35;
            background: #fff5f2;
        }
        .logo-upload.dragover {
            border-color: #ff6b35;
            background: #fff5f2;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            margin: 10px auto;
            display: none;
        }
        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .section-title {
            color: #ff6b35;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="installer-container">
            <div class="installer-header">
                <h2><i class="fas fa-building"></i> Company Setup</h2>
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
                    <div class="step active">
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

                <form method="POST" action="<?= base_url('installer/company') ?>" enctype="multipart/form-data">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i> Company Information
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">
                                    <i class="fas fa-building"></i> Company Name *
                                </label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?= old('company_name') ?>" required>
                                <div class="help-text">Your company's official name</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email Address *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email') ?>" required>
                                <div class="help-text">Primary contact email</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number *
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone') ?>" required>
                                <div class="help-text">Primary contact phone</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="website" class="form-label">
                                    <i class="fas fa-globe"></i> Website
                                </label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?= old('website') ?>" placeholder="https://www.yourcompany.com">
                                <div class="help-text">Optional company website</div>
                            </div>
                        </div>
                    </div>

                    <h4 class="section-title">
                        <i class="fas fa-map-marker-alt"></i> Address Information
                    </h4>

                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-pin"></i> Street Address *
                        </label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= old('address') ?>" required>
                        <div class="help-text">Complete street address</div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">
                                    <i class="fas fa-city"></i> City *
                                </label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= old('city') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="state" class="form-label">
                                    <i class="fas fa-map"></i> State/Province *
                                </label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?= old('state') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">
                                    <i class="fas fa-mail-bulk"></i> Postal Code *
                                </label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       value="<?= old('postal_code') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label">
                            <i class="fas fa-flag"></i> Country *
                        </label>
                        <select class="form-control" id="country" name="country" required>
                            <option value="">Select Country</option>
                            <option value="India" <?= old('country') == 'India' ? 'selected' : '' ?>>India</option>
                            <option value="United States" <?= old('country') == 'United States' ? 'selected' : '' ?>>United States</option>
                            <option value="United Kingdom" <?= old('country') == 'United Kingdom' ? 'selected' : '' ?>>United Kingdom</option>
                            <option value="Canada" <?= old('country') == 'Canada' ? 'selected' : '' ?>>Canada</option>
                            <option value="Australia" <?= old('country') == 'Australia' ? 'selected' : '' ?>>Australia</option>
                            <option value="Germany" <?= old('country') == 'Germany' ? 'selected' : '' ?>>Germany</option>
                            <option value="France" <?= old('country') == 'France' ? 'selected' : '' ?>>France</option>
                            <option value="Japan" <?= old('country') == 'Japan' ? 'selected' : '' ?>>Japan</option>
                            <option value="China" <?= old('country') == 'China' ? 'selected' : '' ?>>China</option>
                            <option value="Other" <?= old('country') == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <h4 class="section-title">
                        <i class="fas fa-image"></i> Company Logo
                    </h4>

                    <div class="mb-3">
                        <div class="logo-upload" onclick="document.getElementById('logo').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5>Upload Company Logo</h5>
                            <p class="text-muted">Click to select or drag and drop your company logo</p>
                            <p class="text-muted small">Recommended: PNG, JPG, or GIF (Max 2MB)</p>
                            <img id="logoPreview" class="logo-preview" alt="Logo Preview">
                        </div>
                        <input type="file" id="logo" name="logo" accept="image/*" style="display: none;" onchange="previewLogo(this)">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('installer/install') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        
                        <button type="submit" class="btn btn-installer">
                            <i class="fas fa-arrow-right"></i> Continue to Admin Setup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewLogo(input) {
            const preview = document.getElementById('logoPreview');
            const uploadArea = document.querySelector('.logo-upload');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    uploadArea.style.borderColor = '#28a745';
                    uploadArea.style.background = '#f8fff9';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Drag and drop functionality
        const uploadArea = document.querySelector('.logo-upload');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('logo').files = files;
                previewLogo(document.getElementById('logo'));
            }
        });
    </script>
</body>
</html>
