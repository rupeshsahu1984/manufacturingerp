<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="fas fa-arrow-circle-up me-2"></i>Stock Out
            </h1>
            <p class="mb-0">Remove stock from inventory</p>
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
            <form action="<?= base_url('stock/stock-out') ?>" method="POST" id="stockOutForm">
                <?= csrf_field() ?>
                
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
                                            data-code="<?= esc($item['item_code'] ?? $item['item_name']) ?>">
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
                                   step="0.01" min="0.01" max="<?= isset($available_stock) ? $available_stock : '' ?>" 
                                   required placeholder="Enter quantity">
                            <div class="form-text" id="uom-display"></div>
                            <?php if (isset($available_stock) && $available_stock > 0): ?>
                                <div class="form-text text-info">
                                    <i class="fas fa-info-circle me-1"></i>Available Stock: <strong><?= number_format($available_stock, 2) ?></strong>
                                </div>
                            <?php endif; ?>
                            <div class="invalid-feedback">Please enter a valid quantity.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="available_stock_display" class="form-label">Available Stock</label>
                            <input type="text" class="form-control" id="available_stock_display" 
                                   value="<?= isset($available_stock) ? number_format($available_stock, 2) : '0.00' ?>" 
                                   readonly style="background-color: #f8f9fa;">
                        </div>
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
                                    <option value="<?= $key ?>"><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a source type.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="source_document" class="form-label mandatory">Source Document *</label>
                            <input type="text" class="form-control" id="source_document" name="source_document" 
                                   required placeholder="e.g., SO-001, DO-123">
                            <div class="invalid-feedback">Please enter source document reference.</div>
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
                                  placeholder="Enter any additional notes or remarks..."></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="<?= base_url('stock') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-2"></i>Remove Stock
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
    const warehouseSelect = document.getElementById('warehouse_id');
    const uomDisplay = document.getElementById('uom-display');
    const quantityInput = document.getElementById('quantity');
    const availableStockDisplay = document.getElementById('available_stock_display');
    
    // Function to fetch available stock
    function fetchAvailableStock() {
        const itemId = itemSelect.value;
        const warehouseId = warehouseSelect.value;
        
        if (itemId && warehouseId) {
            // You can add AJAX call here to fetch real-time stock
            // For now, we'll use the initial value
        }
    }
    
    // Update UOM display when item is selected
    itemSelect.addEventListener('change', function() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const uom = selectedOption.dataset.uom || '';
        
        if (uom) {
            uomDisplay.textContent = 'Unit of Measure: ' + uom;
        } else {
            uomDisplay.textContent = '';
        }
        
        fetchAvailableStock();
    });
    
    // Update available stock when warehouse changes
    warehouseSelect.addEventListener('change', function() {
        fetchAvailableStock();
    });
    
    // Trigger change if item is pre-selected
    if (itemSelect.value) {
        itemSelect.dispatchEvent(new Event('change'));
    }
    
    // Form validation
    const form = document.getElementById('stockOutForm');
    form.addEventListener('submit', function(e) {
        const quantity = parseFloat(quantityInput.value) || 0;
        const availableStock = parseFloat(availableStockDisplay.value.replace(/,/g, '')) || 0;
        
        if (quantity > availableStock) {
            e.preventDefault();
            e.stopPropagation();
            alert('Quantity cannot exceed available stock. Available: ' + availableStock.toFixed(2));
            quantityInput.focus();
            return false;
        }
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    
    // Set max attribute dynamically
    if (availableStockDisplay && availableStockDisplay.value) {
        const maxStock = parseFloat(availableStockDisplay.value.replace(/,/g, '')) || 0;
        if (quantityInput) {
            quantityInput.setAttribute('max', maxStock);
        }
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.page-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    padding: 10px 30px;
    font-weight: 600;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
<?= $this->endSection() ?>

