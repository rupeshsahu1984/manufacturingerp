<?= $this->extend('layouts/main') ?>\n\n<?= $this->section('content') ?>
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
                <div id="alert-container"></div>
                
                <form data-validate id="companyProfileForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Logo Upload Section -->
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>Company Logo</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="logo-preview mb-3" id="logoPreview">
                                        <?php if (isset($profile['logo_path']) && !empty($profile['logo_path'])): ?>
                                            <img src="<?= base_url($profile['logo_path']) ?>" alt="Company Logo" id="currentLogo">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">No logo uploaded</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="text-muted">Max size: 2MB, Formats: JPG, PNG, GIF</small>
                                    </div>
                                    
                                    <?php if (isset($profile['logo_path']) && !empty($profile['logo_path'])): ?>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteLogo()">
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
                                            <input type="text" class="form-control" name="company_name" value="<?= isset($profile['company_name']) ? htmlspecialchars($profile['company_name']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">GST Number *</label>
                                            <input type="text" class="form-control" name="gst_number" value="<?= isset($profile['gst_number']) ? htmlspecialchars($profile['gst_number']) : '' ?>" placeholder="22AAAAA0000A1Z5" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Registration Number *</label>
                                            <input type="text" class="form-control" name="registration_number" value="<?= isset($profile['registration_number']) ? htmlspecialchars($profile['registration_number']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" name="phone" value="<?= isset($profile['phone']) ? htmlspecialchars($profile['phone']) : '' ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email *</label>
                                            <input type="email" class="form-control" name="email" value="<?= isset($profile['email']) ? htmlspecialchars($profile['email']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" class="form-control" name="website" value="<?= isset($profile['website']) ? htmlspecialchars($profile['website']) : '' ?>" placeholder="https://www.example.com">
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
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    
<?= $this->endSection() ?> 