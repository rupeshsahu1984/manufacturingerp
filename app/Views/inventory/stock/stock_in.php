<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="fas fa-<?= isset($is_edit) && $is_edit ? 'edit' : 'arrow-circle-down' ?> me-2"></i>
                <?= isset($is_edit) && $is_edit ? 'Edit Stock' : 'Stock In' ?>
            </h1>
            <p class="mb-0"><?= isset($is_edit) && $is_edit ? 'Update stock information' : 'Add stock to inventory' ?></p>
        </div>
    </div>

    <div class="container">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Form Card -->
        <div class="form-container">
            <form action="<?= isset($is_edit) && $is_edit ? base_url('stock/update/' . $stock['id']) : base_url('stock/stock-in') ?>" method="POST" id="stockInForm">
                <?= csrf_field() ?>
                <?php if (isset($is_edit) && $is_edit): ?>
                    <input type="hidden" name="stock_id" value="<?= $stock['id'] ?>">
                <?php endif; ?>
                
                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="item_id" class="form-label mandatory">Item *</label>
                            <select class="form-control" id="item_id" name="item_id" required>
                                <option value="">Select Item</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            <?= (isset($selected_item_id) && $selected_item_id == $item['id']) ? 'selected' : '' ?>
                                            data-uom="<?= esc($item['uom'] ?? $item['unit_of_measure'] ?? '') ?>"
                                            data-code="<?= esc($item['item_code'] ?? $item['item_name']) ?>"
                                            data-cost="<?= esc($item['cost_price'] ?? 0) ?>">
                                        <?= esc($item['item_name']) ?> 
                                        <?= !empty($item['item_code']) ? '(' . esc($item['item_code']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select an item.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="warehouse_id" class="form-label mandatory">Warehouse *</label>
                            <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                <option value="">Select Warehouse</option>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <option value="<?= $warehouse['id'] ?>" 
                                            <?= (isset($selected_warehouse_id) && $selected_warehouse_id == $warehouse['id']) ? 'selected' : '' ?>>
                                        <?= esc($warehouse['warehouse_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a warehouse.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label mandatory">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   step="0.01" min="0.01" required placeholder="Enter quantity"
                                   value="<?= isset($stock) && isset($stock['quantity']) ? number_format($stock['quantity'], 2, '.', '') : (old('quantity') ?? '') ?>">
                            <div class="form-text" id="uom-display"></div>
                            <div class="invalid-feedback">Please enter a valid quantity.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="unit_cost" class="form-label">Unit Cost</label>
                            <input type="number" class="form-control" id="unit_cost" name="unit_cost" 
                                   step="0.01" min="0" placeholder="0.00"
                                   value="<?= isset($stock) && isset($stock['unit_cost']) ? number_format($stock['unit_cost'], 2, '.', '') : (old('unit_cost') ?? '') ?>">
                        </div>
                        
                        <?php if (isset($is_edit) && $is_edit): ?>
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">Transaction Date *</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" 
                                   required value="<?= isset($stock) && isset($stock['transaction_date']) ? date('Y-m-d', strtotime($stock['transaction_date'])) : date('Y-m-d') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="available" <?= (isset($stock) && isset($stock['status']) && $stock['status'] == 'available') ? 'selected' : '' ?>>Available</option>
                                <option value="reserved" <?= (isset($stock) && isset($stock['status']) && $stock['status'] == 'reserved') ? 'selected' : '' ?>>Reserved</option>
                                <option value="damaged" <?= (isset($stock) && isset($stock['status']) && $stock['status'] == 'damaged') ? 'selected' : '' ?>>Damaged</option>
                                <option value="expired" <?= (isset($stock) && isset($stock['status']) && $stock['status'] == 'expired') ? 'selected' : '' ?>>Expired</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Source Information -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary">
                        <i class="fas fa-file-alt me-2"></i>Source Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="source_type" class="form-label mandatory">Source Type *</label>
                            <select class="form-control" id="source_type" name="source_type" required>
                                <option value="">Select Source Type</option>
                                <?php foreach ($source_types as $key => $label): ?>
                                    <option value="<?= $key ?>" 
                                            <?= (isset($form_source_type) && $form_source_type == $key) ? 'selected' : (old('source_type') == $key ? 'selected' : '') ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a source type.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="source_document" class="form-label mandatory">Source Document *</label>
                            <input type="text" class="form-control" id="source_document" name="source_document" 
                                   required placeholder="e.g., GRN-001, PO-123"
                                   value="<?= isset($stock) && isset($stock['source_document']) ? esc($stock['source_document']) : (old('source_document') ?? '') ?>">
                            <div class="invalid-feedback">Please enter source document reference.</div>
                        </div>
                    </div>
                </div>

                <!-- Batch & Location Information -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary">
                        <i class="fas fa-map-marker-alt me-2"></i>Batch & Location Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="batch_number" class="form-label">Batch Number</label>
                            <input type="text" class="form-control" id="batch_number" name="batch_number" 
                                   placeholder="Optional"
                                   value="<?= isset($stock) && isset($stock['batch_number']) ? esc($stock['batch_number']) : (old('batch_number') ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date"
                                   value="<?= isset($stock) && isset($stock['expiry_date']) && $stock['expiry_date'] ? date('Y-m-d', strtotime($stock['expiry_date'])) : (old('expiry_date') ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="manufacturing_date" class="form-label">Manufacturing Date</label>
                            <input type="date" class="form-control" id="manufacturing_date" name="manufacturing_date"
                                   value="<?= isset($stock) && isset($stock['manufacturing_date']) && $stock['manufacturing_date'] ? date('Y-m-d', strtotime($stock['manufacturing_date'])) : (old('manufacturing_date') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   placeholder="e.g., Aisle 1"
                                   value="<?= isset($stock) && isset($stock['location']) ? esc($stock['location']) : (old('location') ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="rack" class="form-label">Rack</label>
                            <input type="text" class="form-control" id="rack" name="rack" 
                                   placeholder="e.g., Rack A"
                                   value="<?= isset($stock) && isset($stock['rack']) ? esc($stock['rack']) : (old('rack') ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="bin" class="form-label">Bin</label>
                            <input type="text" class="form-control" id="bin" name="bin" 
                                   placeholder="e.g., Bin 01"
                                   value="<?= isset($stock) && isset($stock['bin']) ? esc($stock['bin']) : (old('bin') ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary">
                        <i class="fas fa-sticky-note me-2"></i>Additional Information
                    </h5>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Enter any additional notes or remarks..."><?= isset($stock) && isset($stock['notes']) ? esc($stock['notes']) : (old('notes') ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="<?= base_url('stock') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-<?= isset($is_edit) && $is_edit ? 'save' : 'plus' ?> me-2"></i>
                        <?= isset($is_edit) && $is_edit ? 'Update Stock' : 'Add Stock' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemSelect = document.getElementById('item_id');
    const uomDisplay = document.getElementById('uom-display');
    const quantityInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit_cost');
    
    // Update UOM display and unit cost when item is selected
    itemSelect.addEventListener('change', function() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const uom = selectedOption.dataset.uom || '';
        const cost = selectedOption.dataset.cost || '';
        
        if (uom) {
            uomDisplay.textContent = 'Unit of Measure: ' + uom;
        } else {
            uomDisplay.textContent = '';
        }
        
        // Pre-fill unit cost if available and not already set
        if (cost && cost > 0 && (!unitCostInput.value || unitCostInput.value == 0)) {
            unitCostInput.value = parseFloat(cost).toFixed(2);
        }
    });
    
    // Trigger change if item is pre-selected (for auto-fill)
    if (itemSelect.value) {
        itemSelect.dispatchEvent(new Event('change'));
    }
    
    // Form validation
    const form = document.getElementById('stockInForm');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    
    // Set today's date as default for manufacturing date (only if not editing)
    const manufacturingDateInput = document.getElementById('manufacturing_date');
    if (manufacturingDateInput && !manufacturingDateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        manufacturingDateInput.value = today;
    }
    
    // Auto-calculate total cost when quantity or unit cost changes
    if (quantityInput && unitCostInput) {
        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitCost = parseFloat(unitCostInput.value) || 0;
            const totalCost = quantity * unitCost;
            // You can add a total display element if needed
            console.log('Total Cost:', totalCost.toFixed(2));
        }
        
        quantityInput.addEventListener('input', calculateTotal);
        unitCostInput.addEventListener('input', calculateTotal);
        
        // Calculate on page load if values exist
        if (quantityInput.value && unitCostInput.value) {
            calculateTotal();
        }
    }
    
    // Format numbers on blur for better UX
    if (quantityInput) {
        quantityInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
    
    if (unitCostInput) {
        unitCostInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.page-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    margin-bottom: 20px;
}

.form-section {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 25px;
    margin-bottom: 25px;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.mandatory::after {
    content: " *";
    color: #dc3545;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 10px 30px;
    font-weight: 600;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1aa179 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
<?= $this->endSection() ?>

