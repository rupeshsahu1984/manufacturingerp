<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Manufacturing ERP</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-section h5 {
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-user-plus me-3"></i>
                Add New Supplier
            </h1>
            <p class="mb-0">Enter supplier information to add them to your database</p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/purchase" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Purchase Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/purchase/suppliers" class="text-decoration-none">Suppliers</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Add New</li>
            </ol>
        </nav>

        <!-- Form Container -->
        <div class="form-container">
            <form id="supplierForm" action="/purchase/suppliers/store" method="POST" enctype="multipart/form-data">
                <!-- Basic Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-info-circle me-2"></i>
                        Basic Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier_code" class="form-label">Supplier Code</label>
                            <input type="text" class="form-control" id="supplier_code" name="supplier_code" 
                                   placeholder="Auto-generated" readonly>
                            <small class="text-muted">Will be auto-generated if left empty</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="supplier_name" class="form-label required-field">Supplier Name</label>
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" 
                                   required placeholder="Enter supplier name">
                            <div class="error-message" id="supplier_name_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label required-field">Contact Person</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                   required placeholder="Enter contact person name">
                            <div class="error-message" id="contact_person_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label required-field">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category ?>">
                                        <?= ucfirst(str_replace('_', ' ', $category)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" id="category_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-phone me-2"></i>
                        Contact Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   required placeholder="Enter email address">
                            <div class="error-message" id="email_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label required-field">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   required placeholder="Enter phone number">
                            <div class="error-message" id="phone_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="alternate_phone" class="form-label">Alternate Phone</label>
                            <input type="tel" class="form-control" id="alternate_phone" name="alternate_phone" 
                                   placeholder="Enter alternate phone (optional)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   placeholder="Enter website URL (optional)">
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Address Information
                    </h5>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" 
                                  placeholder="Enter complete address"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   placeholder="Enter city">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" 
                                   placeholder="Enter state">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" 
                                   placeholder="Enter pincode">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" 
                                   value="India" placeholder="Enter country">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York (EST)</option>
                                <option value="Europe/London">Europe/London (GMT)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tax & Legal Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-file-invoice me-2"></i>
                        Tax & Legal Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gst_number" class="form-label">GST Number</label>
                            <input type="text" class="form-control" id="gst_number" name="gst_number" 
                                   placeholder="Enter GST number (15 characters)" maxlength="15">
                            <small class="text-muted">Format: 22AAAAA0000A1Z5</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_number" class="form-label">PAN Number</label>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" 
                                   placeholder="Enter PAN number (10 characters)" maxlength="10">
                            <small class="text-muted">Format: ABCDE1234F</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tin_number" class="form-label">TIN Number</label>
                            <input type="text" class="form-control" id="tin_number" name="tin_number" 
                                   placeholder="Enter TIN number (optional)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cst_number" class="form-label">CST Number</label>
                            <input type="text" class="form-control" id="cst_number" name="cst_number" 
                                   placeholder="Enter CST number (optional)">
                        </div>
                    </div>
                </div>

                <!-- Business Terms -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-handshake me-2"></i>
                        Business Terms
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_terms" class="form-label">Payment Terms</label>
                            <select class="form-select" id="payment_terms" name="payment_terms">
                                <option value="">Select Payment Terms</option>
                                <option value="Net 30">Net 30</option>
                                <option value="Net 45">Net 45</option>
                                <option value="Net 60">Net 60</option>
                                <option value="Net 90">Net 90</option>
                                <option value="Immediate">Immediate</option>
                                <option value="Advance">Advance</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_terms" class="form-label">Delivery Terms</label>
                            <select class="form-select" id="delivery_terms" name="delivery_terms">
                                <option value="">Select Delivery Terms</option>
                                <option value="FOB">FOB (Free On Board)</option>
                                <option value="CIF">CIF (Cost, Insurance & Freight)</option>
                                <option value="EXW">EXW (Ex Works)</option>
                                <option value="DDP">DDP (Delivered Duty Paid)</option>
                                <option value="FCA">FCA (Free Carrier)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lead_time_days" class="form-label">Lead Time (Days)</label>
                            <input type="number" class="form-control" id="lead_time_days" name="lead_time_days" 
                                   min="0" placeholder="Enter lead time in days">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="credit_limit" class="form-label">Credit Limit (₹)</label>
                            <input type="number" class="form-control" id="credit_limit" name="credit_limit" 
                                   min="0" step="0.01" placeholder="Enter credit limit">
                        </div>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-university me-2"></i>
                        Bank Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                   placeholder="Enter bank name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_account" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="bank_account" name="bank_account" 
                                   placeholder="Enter account number">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ifsc_code" class="form-label">IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" 
                                   placeholder="Enter IFSC code" maxlength="11">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="branch_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control" id="branch_name" name="branch_name" 
                                   placeholder="Enter branch name">
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-plus-circle me-2"></i>
                        Additional Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier_rating" class="form-label">Initial Rating</label>
                            <select class="form-select" id="supplier_rating" name="supplier_rating">
                                <option value="5">⭐⭐⭐⭐⭐ Excellent (5)</option>
                                <option value="4">⭐⭐⭐⭐ Good (4)</option>
                                <option value="3">⭐⭐⭐ Average (3)</option>
                                <option value="2">⭐⭐ Below Average (2)</option>
                                <option value="1">⭐ Poor (1)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="supplier_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="supplier_notes" name="supplier_notes" rows="3" 
                                      placeholder="Enter any additional notes about the supplier"></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="documents" class="form-label">Upload Documents</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Upload contracts, certifications, compliance docs (PDF, DOC, Images)</small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between align-items-center pt-4">
                    <a href="/purchase/suppliers" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-save me-2"></i>Save Supplier
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        document.getElementById('supplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearErrors();
            
            // Validate required fields
            let isValid = true;
            
            const requiredFields = ['supplier_name', 'contact_person', 'email', 'phone', 'category'];
            requiredFields.forEach(field => {
                const value = document.getElementById(field).value.trim();
                if (!value) {
                    showError(field, 'This field is required');
                    isValid = false;
                }
            });
            
            // Validate email format
            const email = document.getElementById('email').value;
            if (email && !isValidEmail(email)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }
            
            // Validate phone format
            const phone = document.getElementById('phone').value;
            if (phone && !isValidPhone(phone)) {
                showError('phone', 'Please enter a valid phone number');
                isValid = false;
            }
            
            // Validate GST number format if provided
            const gstNumber = document.getElementById('gst_number').value;
            if (gstNumber && !isValidGST(gstNumber)) {
                showError('gst_number', 'Please enter a valid GST number (15 characters)');
                isValid = false;
            }
            
            // Validate PAN number format if provided
            const panNumber = document.getElementById('pan_number').value;
            if (panNumber && !isValidPAN(panNumber)) {
                showError('pan_number', 'Please enter a valid PAN number (10 characters)');
                isValid = false;
            }
            
            if (isValid) {
                // Submit form
                this.submit();
            }
        });
        
        // Validation helper functions
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function isValidPhone(phone) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
        }
        
        function isValidGST(gst) {
            return gst.length === 15 && /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(gst);
        }
        
        function isValidPAN(pan) {
            return pan.length === 10 && /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan);
        }
        
        function showError(fieldId, message) {
            const errorDiv = document.getElementById(fieldId + '_error');
            if (errorDiv) {
                errorDiv.textContent = message;
                document.getElementById(fieldId).classList.add('is-invalid');
            }
        }
        
        function clearErrors() {
            const errorDivs = document.querySelectorAll('.error-message');
            errorDivs.forEach(div => div.textContent = '');
            
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => input.classList.remove('is-invalid'));
        }
        
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                document.getElementById('supplierForm').reset();
                clearErrors();
            }
        }
        
        // Auto-generate supplier code
        document.getElementById('supplier_name').addEventListener('blur', function() {
            const supplierCode = document.getElementById('supplier_code');
            if (!supplierCode.value && this.value) {
                const code = 'SUP' + Date.now().toString().slice(-6);
                supplierCode.value = code;
            }
        });
        
        // Format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 5) {
                    value = value;
                } else if (value.length <= 10) {
                    value = value.slice(0, 5) + '-' + value.slice(5);
                } else {
                    value = value.slice(0, 5) + '-' + value.slice(5, 10) + '-' + value.slice(10, 15);
                }
            }
            e.target.value = value;
        });
        
        // Format GST number
        document.getElementById('gst_number').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            e.target.value = value;
        });
        
        // Format PAN number
        document.getElementById('pan_number').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            e.target.value = value;
        });
    </script>
</body>
</html>
