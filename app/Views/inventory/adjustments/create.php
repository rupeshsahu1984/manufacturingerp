<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .mandatory::after {
        content: " *";
        color: red;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="fas fa-adjust me-2"></i>
                Create Stock Adjustment
            </h1>
            <p class="mb-0">Adjust stock quantities for corrections, damage, expiry, or physical count differences</p>
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
                        <li><?= is_array($error) ? implode(', ', $error) : $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Form Card -->
        <div class="form-container">
            <form action="<?= base_url('inventory/adjustments/store') ?>" method="POST" id="adjustmentForm">
                <?= csrf_field() ?>
                
                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="mb-3 text-warning">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                   value="<?= old('reference_number', $reference_number ?? '') ?>" readonly>
                            <div class="form-text">Auto-generated reference number</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="adjustment_date" class="form-label mandatory">Adjustment Date *</label>
                            <input type="date" class="form-control" id="adjustment_date" name="adjustment_date" 
                                   required value="<?= old('adjustment_date', date('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="item_id" class="form-label mandatory">Item *</label>
                            <select class="form-control" id="item_id" name="item_id" required>
                                <option value="">Select Item</option>
                                <?php if (isset($items) && is_array($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= $item['id'] ?>" 
                                                <?= old('item_id') == $item['id'] ? 'selected' : '' ?>
                                                data-uom="<?= esc($item['uom'] ?? $item['unit_of_measure'] ?? '') ?>"
                                                data-cost="<?= esc($item['cost_price'] ?? 0) ?>">
                                            <?= esc($item['item_name']) ?> 
                                            <?= !empty($item['item_code']) ? '(' . esc($item['item_code']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select an item.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="warehouse_id" class="form-label mandatory">Warehouse *</label>
                            <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                <option value="">Select Warehouse</option>
                                <?php if (isset($warehouses) && is_array($warehouses)): ?>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse['id'] ?>" 
                                                <?= old('warehouse_id') == $warehouse['id'] ? 'selected' : '' ?>>
                                            <?= esc($warehouse['warehouse_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select a warehouse.</div>
                        </div>
                    </div>
                </div>

                <!-- Adjustment Details -->
                <div class="form-section">
                    <h5 class="mb-3 text-warning">
                        <i class="fas fa-adjust me-2"></i>Adjustment Details
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="adjustment_type" class="form-label mandatory">Adjustment Type *</label>
                            <select class="form-control" id="adjustment_type" name="adjustment_type" required>
                                <option value="">Select Adjustment Type</option>
                                <?php if (isset($adjustment_types) && is_array($adjustment_types)): ?>
                                    <?php foreach ($adjustment_types as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= old('adjustment_type') == $key ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select an adjustment type.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label mandatory">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   step="0.01" min="0.01" required placeholder="Enter quantity"
                                   value="<?= old('quantity') ?>">
                            <div class="form-text" id="uom-display"></div>
                            <div class="invalid-feedback">Please enter a valid quantity.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unit_cost" class="form-label">Unit Cost</label>
                            <input type="number" class="form-control" id="unit_cost" name="unit_cost" 
                                   step="0.01" min="0" placeholder="0.00"
                                   value="<?= old('unit_cost') ?>">
                            <div class="form-text">Leave blank to use item's default cost price</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="reason" class="form-label mandatory">Reason *</label>
                            <select class="form-control" id="reason" name="reason" required>
                                <option value="">Select Reason</option>
                                <?php if (isset($reasons) && is_array($reasons)): ?>
                                    <?php foreach ($reasons as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= old('reason') == $key ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select a reason.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Enter any additional notes or comments"><?= old('notes') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Current Stock Info -->
                <div class="form-section" id="stock-info-section" style="display: none;">
                    <h5 class="mb-3 text-info">
                        <i class="fas fa-boxes me-2"></i>Current Stock Information
                    </h5>
                    <div class="alert alert-info" id="stock-info-alert">
                        <strong>Available Stock:</strong> <span id="available-stock">0</span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Create Adjustment
                    </button>
                    <a href="<?= base_url('stock-adjustment') ?>" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Auto-fill unit cost from item
        $('#item_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const uom = selectedOption.data('uom');
            const cost = selectedOption.data('cost');
            
            if (uom) {
                $('#uom-display').text('Unit: ' + uom);
            }
            
            if (cost && !$('#unit_cost').val()) {
                $('#unit_cost').val(cost);
            }
            
            // Load current stock if item and warehouse are selected
            loadCurrentStock();
        });

        $('#warehouse_id').on('change', function() {
            loadCurrentStock();
        });

        function loadCurrentStock() {
            const itemId = $('#item_id').val();
            const warehouseId = $('#warehouse_id').val();
            
            if (itemId && warehouseId) {
                $.ajax({
                    url: '<?= base_url('api/stock/get-item-stock') ?>',
                    method: 'GET',
                    data: {
                        item_id: itemId,
                        warehouse_id: warehouseId
                    },
                    success: function(response) {
                        if (response && response.available_stock !== undefined) {
                            $('#available-stock').text(response.available_stock);
                            $('#stock-info-section').show();
                        }
                    },
                    error: function() {
                        // If API doesn't exist, hide the section
                        $('#stock-info-section').hide();
                    }
                });
            } else {
                $('#stock-info-section').hide();
            }
        }

        // Validate decrease adjustments
        $('#adjustmentForm').on('submit', function(e) {
            const adjustmentType = $('#adjustment_type').val();
            const quantity = parseFloat($('#quantity').val());
            const availableStock = parseFloat($('#available-stock').text()) || 0;
            
            if (['decrease', 'damage', 'expiry'].includes(adjustmentType) && quantity > availableStock) {
                e.preventDefault();
                alert('Insufficient stock. Available: ' + availableStock);
                return false;
            }
        });
    });
</script>
<?= $this->endSection() ?>

