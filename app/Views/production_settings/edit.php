<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-cogs me-3"></i>Edit Production Setting</h1>
    <div class="header-actions">
        <a href="<?= base_url('production-settings') ?>" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="<?= base_url('production-settings/show/' . $bom['id']) ?>" class="btn btn-info">
            <i class="fas fa-eye"></i> View Details
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Edit Form -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-edit me-2"></i>Edit Production Setting: <?= $bom['bom_number'] ?></h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('production-settings/update/' . $bom['id']) ?>" method="POST" id="productionSettingForm" data-validate>
            <?= csrf_field() ?>
            
            <!-- Basic Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="finished_product_id" class="form-label">
                            <i class="fas fa-box text-primary"></i> Finished Product *
                        </label>
                        <select class="form-select" id="finished_product_id" name="finished_product_id" required>
                            <option value="">Select Finished Product</option>
                            <?php foreach (isset($finishedProducts) ? $finishedProducts : [] as $product): ?>
                                <option value="<?= $product['id'] ?>" 
                                        data-unit="<?= $product['unit'] ?>"
                                        data-price="<?= $product['unit_price'] ?>"
                                        <?= $product['id'] == $bom['finished_product_id'] ? 'selected' : '' ?>>
                                    <?= $product['product_code'] ?> - <?= $product['product_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Select the final product to be manufactured</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="version" class="form-label">
                            <i class="fas fa-code-branch text-primary"></i> Version
                        </label>
                        <input type="text" class="form-control" id="version" name="version" 
                               value="<?= $bom['version'] ?>" placeholder="1.0">
                        <div class="form-text">BOM version number</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">
                    <i class="fas fa-align-left text-primary"></i> Description
                </label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="Enter detailed description of the production process..."><?= $bom['description'] ?></textarea>
                <div class="form-text">Detailed description of the production process and requirements</div>
            </div>

            <!-- Materials Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list text-primary"></i> Materials & Components
                    </h6>
                </div>
                <div class="card-body">
                    <div id="materialsContainer">
                        <!-- Material rows will be populated with existing data -->
                        <?php if (!empty($bom['items'])): ?>
                            <?php foreach ($bom['items'] as $index => $item): ?>
                                <div class="material-row border rounded p-3 mb-3" data-index="<?= $index ?>">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Material *</label>
                                            <select class="form-select material-select" name="materials[<?= $index ?>][material_id]" required>
                                                <option value="">Select Material</option>
                                                <?php if (!empty($materials)): ?>
                                                    <?php foreach ($materials as $material): ?>
                                                        <option value="<?= $material['id'] ?>" 
                                                                data-unit="<?= $material['unit'] ?>"
                                                                data-price="<?= $material['unit_price'] ?>"
                                                                <?= $material['id'] == $item['material_id'] ? 'selected' : '' ?>>
                                                            <?= $material['product_code'] ?> - <?= $material['product_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No materials available</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity *</label>
                                            <input type="number" class="form-control material-qty" name="materials[<?= $index ?>][quantity]" 
                                                   step="0.01" min="0" required value="<?= $item['quantity'] ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit</label>
                                            <input type="text" class="form-control material-unit" readonly value="<?= isset($item['unit']) ? $item['unit'] : '' ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Waste %</label>
                                            <input type="number" class="form-control material-waste" name="materials[<?= $index ?>][waste_percentage]" 
                                                   step="0.1" min="0" max="100" value="<?= isset($item['waste_percentage']) ? $item['waste_percentage'] : 0 ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Cost</label>
                                            <input type="text" class="form-control material-cost" readonly value="₹<?= number_format(isset($item['total_cost']) ? $item['total_cost'] : 0, 2) ?>">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-material" title="Remove Material">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary" id="addMaterialBtn">
                            <i class="fas fa-plus"></i> Add Material
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator text-primary"></i> Cost Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary" id="totalMaterials"><?= count(isset($bom['items']) ? $bom['items'] : []) ?></h4>
                                <small class="text-muted">Total Materials</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success" id="totalCost">₹<?= number_format(isset($bom['total_cost']) ? $bom['total_cost'] : 0, 2) ?></h4>
                                <small class="text-muted">Total Cost</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning" id="totalWaste">
                                    <?php 
                                    $avgWaste = 0;
                                    if (!empty($bom['items'])) {
                                        $totalWaste = 0;
                                        foreach ($bom['items'] as $item) {
                                            $totalWaste += isset($item['waste_percentage']) ? $item['waste_percentage'] : 0;
                                        }
                                        $avgWaste = $totalWaste / count($bom['items']);
                                    }
                                    echo number_format($avgWaste, 1) . '%';
                                    ?>
                                </h4>
                                <small class="text-muted">Avg Waste %</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info" id="totalQuantity">
                                    <?php 
                                    $totalQty = 0;
                                    if (!empty($bom['items'])) {
                                        foreach ($bom['items'] as $item) {
                                            $totalQty += isset($item['quantity']) ? $item['quantity'] : 0;
                                        }
                                    }
                                    echo number_format($totalQty, 2);
                                    ?>
                                </h4>
                                <small class="text-muted">Total Qty</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-secondary" onclick="history.back()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Production Setting
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Debug materials data
console.log('Materials data:', <?= json_encode(isset($materials) ? $materials : []) ?>);

// Material row template for new rows
function createMaterialRow(index) {
    return `
        <div class="material-row border rounded p-3 mb-3" data-index="${index}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Material *</label>
                    <select class="form-select material-select" name="materials[${index}][material_id]" required>
                        <option value="">Select Material</option>
                        <?php foreach (isset($materials) ? $materials : [] as $material): ?>
                            <option value="<?= $material['id'] ?>" 
                                    data-unit="<?= $material['unit'] ?>"
                                    data-price="<?= $material['unit_price'] ?>">
                                <?= $material['product_code'] ?> - <?= $material['product_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity *</label>
                    <input type="number" class="form-control material-qty" name="materials[${index}][quantity]" 
                           step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit</label>
                    <input type="text" class="form-control material-unit" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Waste %</label>
                    <input type="number" class="form-control material-waste" name="materials[${index}][waste_percentage]" 
                           step="0.1" min="0" max="100" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cost</label>
                    <input type="text" class="form-control material-cost" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-material" title="Remove Material">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

let materialIndex = <?= count(isset($bom['items']) ? $bom['items'] : []) ?>;

document.addEventListener('DOMContentLoaded', function() {
    const addMaterialBtn = document.getElementById('addMaterialBtn');
    const materialsContainer = document.getElementById('materialsContainer');

    // Add material button click
    addMaterialBtn.addEventListener('click', function() {
        addMaterialRow();
    });

    // Remove material button click
    materialsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            const materialRow = e.target.closest('.material-row');
            if (document.querySelectorAll('.material-row').length > 1) {
                materialRow.remove();
                updateSummary();
            } else {
                showNotification('At least one material is required.', 'warning');
            }
        }
    });

    // Material selection change
    materialsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('material-select')) {
            const row = e.target.closest('.material-row');
            const selectedOption = e.target.selectedOptions[0];
            const unit = selectedOption.dataset.unit || '';
            const price = parseFloat(selectedOption.dataset.price) || 0;
            
            row.querySelector('.material-unit').value = unit;
            updateMaterialCost(row);
        }
    });

    // Quantity or waste change
    materialsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('material-qty') || e.target.classList.contains('material-waste')) {
            const row = e.target.closest('.material-row');
            updateMaterialCost(row);
        }
    });

    function addMaterialRow() {
        materialIndex++;
        const newRow = createMaterialRow(materialIndex);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newRow;
        const materialRow = tempDiv.firstElementChild;
        materialsContainer.appendChild(materialRow);
    }

    function updateMaterialCost(row) {
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('.material-qty');
        const costInput = row.querySelector('.material-cost');
        
        if (select.value && qtyInput.value) {
            const selectedOption = select.selectedOptions[0];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const qty = parseFloat(qtyInput.value) || 0;
            const cost = price * qty;
            costInput.value = '₹' + cost.toFixed(2);
        } else {
            costInput.value = '';
        }
        
        updateSummary();
    }

    function updateSummary() {
        const materials = document.querySelectorAll('.material-row');
        let totalMaterials = materials.length;
        let totalCost = 0;
        let totalWaste = 0;
        let totalQuantity = 0;
        let validMaterials = 0;

        materials.forEach(row => {
            const qtyInput = row.querySelector('.material-qty');
            const wasteInput = row.querySelector('.material-waste');
            const costInput = row.querySelector('.material-cost');
            
            if (qtyInput.value) {
                validMaterials++;
                totalQuantity += parseFloat(qtyInput.value) || 0;
                totalWaste += parseFloat(wasteInput.value) || 0;
                
                const costText = costInput.value.replace('₹', '');
                totalCost += parseFloat(costText) || 0;
            }
        });

        document.getElementById('totalMaterials').textContent = totalMaterials;
        document.getElementById('totalCost').textContent = '₹' + totalCost.toFixed(2);
        document.getElementById('totalQuantity').textContent = totalQuantity.toFixed(2);
        
        const avgWaste = validMaterials > 0 ? totalWaste / validMaterials : 0;
        document.getElementById('totalWaste').textContent = avgWaste.toFixed(1) + '%';
    }

    // Initialize existing material costs
    document.querySelectorAll('.material-row').forEach(row => {
        updateMaterialCost(row);
    });
});
</script>
<?= $this->endSection() ?> 