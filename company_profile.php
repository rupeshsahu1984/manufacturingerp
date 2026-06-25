<?php
require_once "config/database.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$mysqli = getDB();
$profile = null;

// Get existing company profile
$stmt = $mysqli->prepare("SELECT * FROM company_profile LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
}

// Handle form submission
if ($_POST) {
    $company_name = $_POST['company_name'] ?? '';
    $gst_number = $_POST['gst_number'] ?? '';
    $registration_number = $_POST['registration_number'] ?? '';
    $about_company = $_POST['about_company'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    
    // Handle logo upload
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['logo']['type'];
        
        if (in_array($file_type, $allowed_types) && $_FILES['logo']['size'] <= 2 * 1024 * 1024) {
            $upload_dir = 'uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = 'company_logo_' . time() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $filepath)) {
                $logo_path = $filepath;
                
                // Delete old logo if exists
                if ($profile && !empty($profile['logo_path']) && file_exists($profile['logo_path'])) {
                    unlink($profile['logo_path']);
                }
            }
        }
    }
    
    if ($profile) {
        // Update existing profile
        $stmt = $mysqli->prepare("UPDATE company_profile SET 
            company_name = ?, gst_number = ?, registration_number = ?, about_company = ?, 
            address = ?, phone = ?, email = ?, website = ?" . 
            ($logo_path ? ", logo_path = ?" : "") . " WHERE id = ?");
        
        if ($logo_path) {
            $stmt->bind_param("sssssssssi", $company_name, $gst_number, $registration_number, 
                $about_company, $address, $phone, $email, $website, $logo_path, $profile['id']);
        } else {
            $stmt->bind_param("ssssssssi", $company_name, $gst_number, $registration_number, 
                $about_company, $address, $phone, $email, $website, $profile['id']);
        }
    } else {
        // Create new profile
        $stmt = $mysqli->prepare("INSERT INTO company_profile 
            (company_name, gst_number, registration_number, about_company, address, phone, email, website, logo_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $company_name, $gst_number, $registration_number, 
            $about_company, $address, $phone, $email, $website, $logo_path);
    }
    
    if ($stmt->execute()) {
        $success_message = "Company profile updated successfully!";
        // Refresh profile data
        $stmt = $mysqli->prepare("SELECT * FROM company_profile LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $profile = $result->fetch_assoc();
        }
    } else {
        $error_message = "Failed to update company profile.";
    }
}

// Handle logo deletion
if (isset($_POST['delete_logo']) && $profile && !empty($profile['logo_path'])) {
    if (file_exists($profile['logo_path'])) {
        unlink($profile['logo_path']);
    }
    
    $stmt = $mysqli->prepare("UPDATE company_profile SET logo_path = NULL WHERE id = ?");
    $stmt->bind_param("i", $profile['id']);
    if ($stmt->execute()) {
        $profile['logo_path'] = null;
        $success_message = "Logo deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODX - Company Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin: 2rem auto;
            max-width: 900px;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            background: #f8f9fa;
        }
        .logo-preview img {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
        }
        .form-control:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #e55a2b 0%, #ff6b35 100%);
        }
        .alert {
            border-radius: 10px;
        }
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .header {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
            color: white;
            padding: 1rem 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo">
                        <i class="fas fa-industry text-warning"></i>
                        <span class="ms-2 fw-bold">PRODX</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="index.php" class="btn btn-outline-light btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <a href="logout.php" class="btn btn-dark btn-sm">LOG OUT</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-card">
            <div class="card-header text-center py-4">
                <h3 class="mb-0">
                    <i class="fas fa-building me-2"></i>
                    Company Profile Management
                </h3>
                <p class="mb-0 mt-2">PRODX - Smarter Control for Modern Manufacturing</p>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Logo Upload Section -->
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>Company Logo</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="logo-preview mb-3">
                                        <?php if (isset($profile['logo_path']) && !empty($profile['logo_path'])): ?>
                                            <img src="<?= $profile['logo_path'] ?>" alt="Company Logo">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">No logo uploaded</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <input type="file" class="form-control" name="logo" accept="image/*">
                                        <small class="text-muted">Max size: 2MB, Formats: JPG, PNG, GIF</small>
                                    </div>
                                    
                                    <?php if (isset($profile['logo_path']) && !empty($profile['logo_path'])): ?>
                                        <button type="submit" name="delete_logo" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete the company logo?')">
                                            <i class="fas fa-trash me-1"></i>Delete Logo
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Company Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Company Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company Name *</label>
                                            <input type="text" class="form-control" name="company_name" 
                                                   value="<?= isset($profile['company_name']) ? htmlspecialchars($profile['company_name']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">GST Number *</label>
                                            <input type="text" class="form-control" name="gst_number" 
                                                   value="<?= isset($profile['gst_number']) ? htmlspecialchars($profile['gst_number']) : '' ?>" 
                                                   placeholder="22AAAAA0000A1Z5" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Registration Number *</label>
                                            <input type="text" class="form-control" name="registration_number" 
                                                   value="<?= isset($profile['registration_number']) ? htmlspecialchars($profile['registration_number']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" name="phone" 
                                                   value="<?= isset($profile['phone']) ? htmlspecialchars($profile['phone']) : '' ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email *</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?= isset($profile['email']) ? htmlspecialchars($profile['email']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" class="form-control" name="website" 
                                                   value="<?= isset($profile['website']) ? htmlspecialchars($profile['website']) : '' ?>" 
                                                   placeholder="https://www.example.com">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Company Address *</label>
                                        <textarea class="form-control" name="address" rows="3" required><?= isset($profile['address']) ? htmlspecialchars($profile['address']) : '' ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">About Company *</label>
                                        <textarea class="form-control" name="about_company" rows="4" required><?= isset($profile['about_company']) ? htmlspecialchars($profile['about_company']) : '' ?></textarea>
                                        <small class="text-muted">Describe your company's mission, vision, and key information</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Save Company Profile
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logo preview functionality
        document.querySelector('input[name="logo"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.querySelector('.logo-preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" style="max-width: 100%; max-height: 150px; object-fit: contain;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 