<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Create New Customer</h1>
        <p class="text-muted mb-0">Add a new customer to the system</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('customer') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Customers
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="form-card">
    <div class="card-header">
        <h5><i class="fas fa-user-plus me-2"></i>Customer Information</h5>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form data-validate action="<?= base_url('customer/store') ?>" method="POST" id="customerForm">
            <?= csrf_field() ?>
            <input type="hidden" name="debug" value="1">
            
            <!-- Basic Information -->
            <div class="form-section">
                <h6 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>Basic Information
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name" class="form-label">Customer Name *</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                   placeholder="Enter customer company name" required>
                            <div class="form-text">Enter the full business name</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_code" class="form-label">Customer Code</label>
                            <div class="customer-code-display" id="customer_code_display">
                                <i class="fas fa-spinner fa-spin"></i> Auto-generating...
                            </div>
                            <input type="hidden" id="customer_code" name="customer_code">
                            <div class="form-text">Auto-generated based on customer name</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_person" class="form-label">Contact Person *</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                   placeholder="Enter contact person name" required>
                            <div class="form-text">Primary contact person name</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   placeholder="Enter phone number" required>
                            <div class="form-text">Primary contact number</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Enter email address">
                            <div class="form-text">Business email address</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   placeholder="https://www.example.com">
                            <div class="form-text">Company website URL</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section">
                <h6 class="section-title">
                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                </h6>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="address" class="form-label">Complete Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Enter complete address" required></textarea>
                            <div class="form-text">Full business address</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city" class="form-label">City *</label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   placeholder="Enter city name" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="state" class="form-label">State *</label>
                            <input type="text" class="form-control" id="state" name="state" 
                                   placeholder="Enter state name" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pincode" class="form-label">Pincode *</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" 
                                   placeholder="Enter pincode" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="form-section">
                <h6 class="section-title">
                    <i class="fas fa-building me-2"></i>Business Information
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gst_number" class="form-label">GST Number</label>
                            <input type="text" class="form-control" id="gst_number" name="gst_number" 
                                   placeholder="22AAAAA0000A1Z5">
                            <div class="form-text">15-digit GST registration number</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pan_number" class="form-label">PAN Number</label>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" 
                                   placeholder="ABCDE1234F">
                            <div class="form-text">10-digit PAN number</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales & Credit Information -->
            <div class="form-section">
                <h6 class="section-title">
                    <i class="fas fa-credit-card me-2"></i>Sales & Credit Information
                </h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="credit_limit" class="form-label">Credit Limit</label>
                            <input type="number" class="form-control" id="credit_limit" name="credit_limit" 
                                   placeholder="Enter credit limit amount" min="0" step="0.01">
                            <div class="form-text">Maximum credit amount allowed</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="payment_terms" class="form-label">Payment Terms</label>
                            <select class="form-control" id="payment_terms" name="payment_terms">
                                <option value="">Select Payment Terms</option>
                                <option value="immediate">Immediate</option>
                                <option value="7_days">7 Days</option>
                                <option value="15_days">15 Days</option>
                                <option value="30_days">30 Days</option>
                                <option value="45_days">45 Days</option>
                                <option value="60_days">60 Days</option>
                                <option value="90_days">90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="return_policy" class="form-label">Return Policy</label>
                            <select class="form-control" id="return_policy" name="return_policy">
                                <option value="">Select Return Policy</option>
                                <option value="no_returns">No Returns</option>
                                <option value="7_days">7 Days</option>
                                <option value="15_days">15 Days</option>
                                <option value="30_days">30 Days</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="debit_note_config" class="form-label">Debit Note Configuration</label>
                            <select class="form-control" id="debit_note_config" name="debit_note_config">
                                <option value="">Select Configuration</option>
                                <option value="allowed">Allowed</option>
                                <option value="restricted">Restricted</option>
                                <option value="not_allowed">Not Allowed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Zone & Region -->
            <div class="form-section">
                <h6 class="section-title">
                    <i class="fas fa-globe me-2"></i>Sales Zone & Region
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sales_zone" class="form-label">Sales Zone</label>
                            <input type="text" class="form-control" id="sales_zone" name="sales_zone" 
                                   placeholder="Enter sales zone">
                            <div class="form-text">Geographic sales zone</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sales_region" class="form-label">Sales Region</label>
                            <input type="text" class="form-control" id="sales_region" name="sales_region" 
                                   placeholder="Enter sales region">
                            <div class="form-text">Sales region or territory</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('customer') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Customer
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('customerForm');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        console.log('Form submission started');
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                console.log('Required field empty:', field.name);
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            console.log('Form validation failed');
        } else {
            console.log('Form validation passed, submitting...');
        }
    });
    
    // Remove validation styling on input
    form.querySelectorAll('input, select, textarea').forEach(function(field) {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        e.target.value = value;
    });
    
    // Pincode formatting
    const pincodeInput = document.getElementById('pincode');
    pincodeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
    });
    
    // GST number formatting
    const gstInput = document.getElementById('gst_number');
    gstInput.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        e.target.value = value;
    });
    
    // PAN number formatting
    const panInput = document.getElementById('pan_number');
    panInput.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        e.target.value = value;
    });
    
    // Auto-generate customer code when customer name changes
    const customerNameInput = document.getElementById('customer_name');
    const customerCodeDisplay = document.getElementById('customer_code_display');
    const customerCodeInput = document.getElementById('customer_code');
    
    customerNameInput.addEventListener('input', function() {
        const name = this.value.trim();
        if (name.length >= 3) {
            // Generate a simple code based on name
            const code = 'CUST' + new Date().getFullYear().toString().substr(-2) + 
                        new Date().getMonth().toString().padStart(2, '0') + 
                        name.substring(0, 3).toUpperCase() + 
                        Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            
            customerCodeDisplay.innerHTML = '<i class="fas fa-check text-success"></i> ' + code;
            customerCodeInput.value = code;
        } else {
            customerCodeDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Auto-generating...';
            customerCodeInput.value = '';
        }
    });
});
</script>
<?= $this->endSection() ?> 