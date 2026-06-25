<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Edit Supplier</h1>
        <p class="text-muted mb-0">Update supplier information</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('supplier') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="form-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-edit me-2"></i>Supplier Information
        </h5>
    </div>
    <div class="card-body">
        <form data-validate action="<?= base_url('supplier/update/' . $supplier['id']) ?>" method="POST" id="supplierForm">
            <?= csrf_field() ?>

            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Basic Information</h6>
                    
                    <div class="form-group">
                        <label for="supplier_name" class="form-label">Supplier Name *</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" 
                               value="<?= old('supplier_name', isset($supplier['supplier_name']) ? $supplier['supplier_name'] : '') ?>" 
                               placeholder="Enter supplier company name" required>
                    </div>

                    <div class="form-group">
                        <label for="supplier_code" class="form-label">Supplier Code</label>
                        <input type="text" class="form-control" id="supplier_code" name="supplier_code" 
                               value="<?= old('supplier_code', isset($supplier['supplier_code']) ? $supplier['supplier_code'] : '') ?>" readonly>
                        <div class="form-text">Auto-generated code</div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_category" class="form-label">Category *</label>
                        <select class="form-control" id="supplier_category" name="supplier_category" required>
                            <option value="">Select Category</option>
                            <option value="raw_material" <?= (old('supplier_category', isset($supplier['supplier_category']) ? $supplier['supplier_category'] : '') == 'raw_material') ? 'selected' : '' ?>>Raw Material</option>
                            <option value="packaging" <?= (old('supplier_category', isset($supplier['supplier_category']) ? $supplier['supplier_category'] : '') == 'packaging') ? 'selected' : '' ?>>Packaging</option>
                            <option value="service" <?= (old('supplier_category', isset($supplier['supplier_category']) ? $supplier['supplier_category'] : '') == 'service') ? 'selected' : '' ?>>Service</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gst_number" class="form-label">GST Number</label>
                        <input type="text" class="form-control" id="gst_number" name="gst_number" 
                               value="<?= old('gst_number', isset($supplier['gst_number']) ? $supplier['gst_number'] : '') ?>" 
                               placeholder="22AAAAA0000A1Z5">
                    </div>

                    <div class="form-group">
                        <label for="pan_number" class="form-label">PAN Number</label>
                        <input type="text" class="form-control" id="pan_number" name="pan_number" 
                               value="<?= old('pan_number', isset($supplier['pan_number']) ? $supplier['pan_number'] : '') ?>" 
                               placeholder="ABCDE1234F">
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Contact Information</h6>
                    
                    <div class="form-group">
                        <label for="contact_person" class="form-label">Contact Person *</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" 
                               value="<?= old('contact_person', isset($supplier['contact_person']) ? $supplier['contact_person'] : '') ?>" 
                               placeholder="Enter contact person name" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= old('phone', isset($supplier['phone']) ? $supplier['phone'] : '') ?>" 
                               placeholder="Enter phone number" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email', isset($supplier['email']) ? $supplier['email'] : '') ?>" 
                               placeholder="Enter email address">
                    </div>

                    <div class="form-group">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" class="form-control" id="website" name="website" 
                               value="<?= old('website', isset($supplier['website']) ? $supplier['website'] : '') ?>" 
                               placeholder="https://www.example.com">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <!-- Address Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Address Information</h6>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="3" 
                                  placeholder="Enter complete address" required><?= old('address', isset($supplier['address']) ? $supplier['address'] : '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="city" class="form-label">City *</label>
                        <input type="text" class="form-control" id="city" name="city" 
                               value="<?= old('city', isset($supplier['city']) ? $supplier['city'] : '') ?>" 
                               placeholder="Enter city name" required>
                    </div>

                    <div class="form-group">
                        <label for="state" class="form-label">State *</label>
                        <input type="text" class="form-control" id="state" name="state" 
                               value="<?= old('state', isset($supplier['state']) ? $supplier['state'] : '') ?>" 
                               placeholder="Enter state name" required>
                    </div>

                    <div class="form-group">
                        <label for="pincode" class="form-label">Pincode *</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" 
                               value="<?= old('pincode', isset($supplier['pincode']) ? $supplier['pincode'] : '') ?>" 
                               placeholder="Enter pincode" required>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Bank Information</h6>
                    
                    <div class="form-group">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" 
                               value="<?= old('bank_name', isset($supplier['bank_name']) ? $supplier['bank_name'] : '') ?>" 
                               placeholder="Enter bank name">
                    </div>

                    <div class="form-group">
                        <label for="bank_account" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="bank_account" name="bank_account" 
                               value="<?= old('bank_account', isset($supplier['bank_account']) ? $supplier['bank_account'] : '') ?>" 
                               placeholder="Enter account number">
                    </div>

                    <div class="form-group">
                        <label for="bank_ifsc" class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" id="bank_ifsc" name="bank_ifsc" 
                               value="<?= old('bank_ifsc', isset($supplier['bank_ifsc']) ? $supplier['bank_ifsc'] : '') ?>" 
                               placeholder="Enter IFSC code">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <!-- Business Terms -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Business Terms</h6>
                    
                    <div class="form-group">
                        <label for="payment_terms" class="form-label">Payment Terms</label>
                        <select class="form-control" id="payment_terms" name="payment_terms">
                            <option value="">Select Payment Terms</option>
                            <option value="immediate" <?= (old('payment_terms', isset($supplier['payment_terms']) ? $supplier['payment_terms'] : '') == 'immediate') ? 'selected' : '' ?>>Immediate</option>
                            <option value="7_days" <?= (old('payment_terms', isset($supplier['payment_terms']) ? $supplier['payment_terms'] : '') == '7_days') ? 'selected' : '' ?>>7 Days</option>
                            <option value="15_days" <?= (old('payment_terms', isset($supplier['payment_terms']) ? $supplier['payment_terms'] : '') == '15_days') ? 'selected' : '' ?>>15 Days</option>
                            <option value="30_days" <?= (old('payment_terms', isset($supplier['payment_terms']) ? $supplier['payment_terms'] : '') == '30_days') ? 'selected' : '' ?>>30 Days</option>
                            <option value="45_days" <?= (old('payment_terms', isset($supplier['payment_terms']) ? $supplier['payment_terms'] : '') == '45_days') ? 'selected' : '' ?>>45 Days</option>
                            <option value="60_days" <?= (old('payment_terms', isset($supplier['payment_terms']) ? $supplier['payment_terms'] : '') == '60_days') ? 'selected' : '' ?>>60 Days</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="credit_limit" class="form-label">Credit Limit (₹)</label>
                        <input type="number" class="form-control" id="credit_limit" name="credit_limit" 
                               value="<?= old('credit_limit', isset($supplier['credit_limit']) ? $supplier['credit_limit'] : '') ?>" 
                               placeholder="Enter credit limit amount" step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="credit_terms" class="form-label">Credit Terms</label>
                        <input type="text" class="form-control" id="credit_terms" name="credit_terms" 
                               value="<?= old('credit_terms', isset($supplier['credit_terms']) ? $supplier['credit_terms'] : '') ?>" 
                               placeholder="e.g., Net 30">
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Additional Information</h6>
                    
                    <div class="form-group">
                        <label for="return_policy" class="form-label">Return Policy</label>
                        <textarea class="form-control" id="return_policy" name="return_policy" rows="3" 
                                  placeholder="Describe return policy..."><?= old('return_policy', isset($supplier['return_policy']) ? $supplier['return_policy'] : '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" <?= (old('status', isset($supplier['status']) ? $supplier['status'] : '') == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (old('status', isset($supplier['status']) ? $supplier['status'] : '') == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('supplier') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('supplierForm');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
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
});
</script>
<?= $this->endSection() ?>
