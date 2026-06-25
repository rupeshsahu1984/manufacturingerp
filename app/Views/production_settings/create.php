<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-cogs me-3"></i>Create Production Setting</h1>
    <div class="header-actions">
        <a href="<?= base_url('production-settings') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
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

<!-- Create Form -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-plus-circle me-2"></i>New Production Setting</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('production-settings/store') ?>" method="POST" id="productionSettingForm" data-validate>
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
                                        data-price="<?= $product['unit_price'] ?>">
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
                        <input type="text" class="form-control" id="version" name="version" value="1.0" placeholder="1.0">
                        <div class="form-text">BOM version number</div>
                    </div>
                </div>
            </div>

            <!-- Production Planning Section -->
            <div class="card mb-4 border-0 bg-light">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Production Planning</h6>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="production_quantity" class="form-label fw-bold">Production Quantity</label>
                            <input type="number" class="form-control" id="production_quantity" name="production_quantity" 
                                   value="1" min="1" step="1" placeholder="1">
                            <div class="form-text">Number of finished products to produce</div>
                        </div>
                        <div class="col-md-3">
                            <label for="batch_size" class="form-label fw-bold">Batch Size</label>
                            <input type="number" class="form-control" id="batch_size" name="batch_size" 
                                   value="1" min="1" step="1" placeholder="1">
                            <div class="form-text">Optimal batch size for production</div>
                        </div>
                        <div class="col-md-3">
                            <label for="production_efficiency" class="form-label fw-bold">Production Efficiency (%)</label>
                            <input type="number" class="form-control" id="production_efficiency" name="production_efficiency" 
                                   value="95" min="1" max="100" step="1" placeholder="95">
                            <div class="form-text">Expected production efficiency</div>
                        </div>
                        <div class="col-md-3">
                            <label for="base_unit" class="form-label fw-bold">Base Unit</label>
                            <select class="form-select" id="base_unit" name="base_unit">
                                <option value="pieces">Pieces</option>
                                <option value="kg">Kilograms (kg)</option>
                                <option value="g">Grams (g)</option>
                                <option value="l">Liters (L)</option>
                                <option value="ml">Milliliters (ml)</option>
                                <option value="m">Meters (m)</option>
                                <option value="cm">Centimeters (cm)</option>
                            </select>
                            <div class="form-text">Unit per finished product</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">
                    <i class="fas fa-align-left text-primary"></i> Description
                </label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="Enter detailed description of the production process..."></textarea>
                <div class="form-text">Detailed description of the production process and requirements</div>
            </div>

            <!-- Materials Section -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i> Raw Materials & Components (Per Finished Product)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Enter the exact quantity of each raw material required to produce ONE finished product. 
                        The system will automatically calculate total requirements based on your production quantity.
                    </div>
                    
                    <div id="materialsContainer">
                        <!-- Material rows will be added here dynamically -->
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary" id="addMaterialBtn">
                            <i class="fas fa-plus"></i> Add Raw Material
                        </button>
                        <button type="button" class="btn btn-outline-info ms-2" id="calculateRequirementsBtn">
                            <i class="fas fa-calculator"></i> Calculate Total Requirements
                        </button>
                        <button type="button" class="btn btn-outline-warning ms-2" id="optimizeWasteBtn">
                            <i class="fas fa-recycle"></i> Optimize Waste
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" id="addSampleBtn">
                            <i class="fas fa-plus-circle"></i> Add Sample Materials
                        </button>
                    </div>
                </div>
            </div>

            <!-- Waste Materials Section -->
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-recycle me-2"></i> Waste Materials Generated (Per Finished Product)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Instructions:</strong> Add waste materials that will be generated during production. These will be automatically 
                        added to stock when manufacturing occurs.
                    </div>
                    
                    <div id="wasteMaterialsContainer">
                        <!-- Waste material rows will be added here dynamically -->
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-warning" id="addWasteMaterialBtn">
                            <i class="fas fa-plus"></i> Add Waste Material
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" id="addSampleWasteBtn">
                            <i class="fas fa-plus-circle"></i> Add Sample Waste Materials
                        </button>
                    </div>
                </div>
            </div>

            <!-- Material Requirements Summary -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i> Production Requirements Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-primary" id="totalRawMaterials">0</h4>
                                <small class="text-muted">Raw Materials</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-warning" id="totalWasteMaterials">0</h4>
                                <small class="text-muted">Waste Materials</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-success" id="totalCost">₹0.00</h4>
                                <small class="text-muted">Total Cost</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-danger" id="totalWaste">0%</h4>
                                <small class="text-muted">Avg Waste %</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-info" id="totalQuantity">0</h4>
                                <small class="text-muted">Total Qty Required</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h4 class="text-secondary" id="totalWasteQuantity">0</h4>
                                <small class="text-muted">Total Waste Generated</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Requirements Table -->
                    <div class="mt-3">
                        <h6 class="text-muted">Detailed Production Requirements:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="requirementsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Material</th>
                                        <th>Required Qty</th>
                                        <th>Waste Qty</th>
                                        <th>Total Qty</th>
                                        <th>Unit Cost</th>
                                        <th>Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody id="requirementsTableBody">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
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
                    <i class="fas fa-save"></i> Create Production Setting
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Materials data from PHP
const materialsData = <?= json_encode(isset($materials) ? $materials : []) ?>;
const wasteMaterialsData = <?= json_encode(isset($wasteMaterials) ? $wasteMaterials : []) ?>;
console.log('Materials data loaded:', materialsData.length, 'materials');
console.log('Waste materials data loaded:', wasteMaterialsData.length, 'waste materials');

// Material row template with enhanced functionality
function createMaterialRow(index) {
    return `
        <div class="material-row border rounded p-3 mb-3" data-index="${index}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Raw Material *</label>
                    <select class="form-select material-select" name="materials[${index}][material_id]" required>
                        <option value="">Select Raw Material</option>
                        ${materialsData.map(material => `
                            <option value="${material.id}" 
                                    data-unit="${material.unit || ''}"
                                    data-price="${material.unit_price || 0}"
                                    data-waste="${material.waste_percentage || 0}"
                                    data-reorder="${material.reorder_level || 0}">
                                ${material.product_code} - ${material.product_name}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Required Qty (Per Product) *</label>
                    <input type="number" class="form-control material-qty" name="materials[${index}][quantity]" 
                           step="0.001" min="0" required placeholder="0.000">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Unit</label>
                    <input type="text" class="form-control material-unit" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Waste (Per Product)</label>
                    <div class="input-group">
                        <input type="number" class="form-control material-waste-amount" name="materials[${index}][waste_amount]" 
                               step="0.001" min="0" placeholder="0.000">
                        <span class="input-group-text material-waste-unit">g</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total Cost (Per Product)</label>
                    <input type="text" class="form-control material-cost" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-material" title="Remove Material">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <small class="text-muted">Waste %: <span class="material-waste-percent">0.00%</span></small>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Total Qty (Per Product): <span class="material-total-qty">0.000</span></small>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Unit Cost: ₹<span class="material-unit-cost">0.00</span></small>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Stock Level: <span class="material-stock">N/A</span></small>
                </div>
            </div>
        </div>
    `;
}

// Waste material row template
function createWasteMaterialRow(index) {
    return `
        <div class="waste-material-row border rounded p-3 mb-3" data-index="${index}">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Waste Material *</label>
                    <select class="form-select waste-material-select" name="waste_materials[${index}][material_id]" required>
                        <option value="">Select Waste Material</option>
                        ${wasteMaterialsData.map(material => `
                            <option value="${material.id}" 
                                    data-unit="${material.unit || ''}"
                                    data-price="${material.unit_price || 0}">
                                ${material.product_code} - ${material.product_name}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Generated Qty (Per Product) *</label>
                    <input type="number" class="form-control waste-material-qty" name="waste_materials[${index}][quantity]" 
                           step="0.001" min="0" required placeholder="0.000">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit</label>
                    <input type="text" class="form-control waste-material-unit" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Value (Per Product)</label>
                    <input type="text" class="form-control waste-material-value" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-waste-material" title="Remove Waste Material">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <small class="text-muted">Unit Cost: ₹<span class="waste-material-unit-cost">0.00</span></small>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Total Value: ₹<span class="waste-material-total-value">0.00</span></small>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Stock Level: <span class="waste-material-stock">N/A</span></small>
                </div>
            </div>
        </div>
    `;
}

let materialIndex = 0;
let wasteMaterialIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    const addMaterialBtn = document.getElementById('addMaterialBtn');
    const materialsContainer = document.getElementById('materialsContainer');
    const calculateRequirementsBtn = document.getElementById('calculateRequirementsBtn');
    const optimizeWasteBtn = document.getElementById('optimizeWasteBtn');
    const addSampleBtn = document.getElementById('addSampleBtn');
    const addWasteMaterialBtn = document.getElementById('addWasteMaterialBtn');
    const wasteMaterialsContainer = document.getElementById('wasteMaterialsContainer');
    const productionQuantity = document.getElementById('production_quantity');

    console.log('DOM loaded, materials container:', materialsContainer);
    console.log('Add material button:', addMaterialBtn);

    // Add first material row
    addMaterialRow();
    addWasteMaterialRow(); // Add first waste material row

    // Add material button click
    addMaterialBtn.addEventListener('click', function() {
        console.log('Add material button clicked');
        addMaterialRow();
    });

    // Add waste material button click
    addWasteMaterialBtn.addEventListener('click', function() {
        console.log('Add waste material button clicked');
        addWasteMaterialRow();
    });

    // Calculate requirements button
    calculateRequirementsBtn.addEventListener('click', function() {
        calculateMaterialRequirements();
    });

    // Optimize waste button
    optimizeWasteBtn.addEventListener('click', function() {
        optimizeWastePercentages();
    });

    // Add sample materials button
    addSampleBtn.addEventListener('click', function() {
        addSampleMaterials();
    });

    // Add sample waste materials button
    const addSampleWasteBtn = document.getElementById('addSampleWasteBtn');
    addSampleWasteBtn.addEventListener('click', function() {
        addSampleWasteMaterials();
    });

    // Production quantity change
    productionQuantity.addEventListener('input', function() {
        updateAllMaterialCalculations();
    });

    // Remove material button click
    materialsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            const materialRow = e.target.closest('.material-row');
            if (document.querySelectorAll('.material-row').length > 1) {
                materialRow.remove();
                updateSummary();
                updateRequirementsTable();
            } else {
                showNotification('At least one material is required.', 'warning');
            }
        }
    });

    // Remove waste material button click
    wasteMaterialsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-waste-material')) {
            const wasteMaterialRow = e.target.closest('.waste-material-row');
            if (document.querySelectorAll('.waste-material-row').length > 1) {
                wasteMaterialRow.remove();
                updateSummary();
                updateRequirementsTable();
            } else {
                showNotification('At least one waste material is required.', 'warning');
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
            const waste = parseFloat(selectedOption.dataset.waste) || 0;
            
            row.querySelector('.material-unit').value = unit;
            row.querySelector('.material-waste-amount').value = waste; // Changed to waste-amount
            row.querySelector('.material-waste-unit').textContent = unit; // Changed to waste-unit
            row.querySelector('.material-unit-cost').textContent = price.toFixed(2);
            updateMaterialCalculations(row);
        }
    });

    // Waste material selection change
    wasteMaterialsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('waste-material-select')) {
            const row = e.target.closest('.waste-material-row');
            const selectedOption = e.target.selectedOptions[0];
            const unit = selectedOption.dataset.unit || '';
            const price = parseFloat(selectedOption.dataset.price) || 0;
            
            row.querySelector('.waste-material-unit').value = unit;
            row.querySelector('.waste-material-unit-cost').textContent = price.toFixed(2);
            updateWasteMaterialCalculations(row);
        }
    });

    // Quantity or waste change
    materialsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('material-qty') || e.target.classList.contains('material-waste-amount')) { // Changed to material-waste-amount
            const row = e.target.closest('.material-row');
            updateMaterialCalculations(row);
        }
    });

    // Waste material quantity or value change
    wasteMaterialsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('waste-material-qty') || e.target.classList.contains('waste-material-value')) {
            const row = e.target.closest('.waste-material-row');
            updateWasteMaterialCalculations(row);
        }
    });

    function addMaterialRow() {
        materialIndex++;
        console.log('Adding material row with index:', materialIndex);
        const newRow = createMaterialRow(materialIndex);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newRow;
        const materialRow = tempDiv.firstElementChild;
        materialsContainer.appendChild(materialRow);
        console.log('Material row added successfully');
    }

    function addWasteMaterialRow() {
        wasteMaterialIndex++;
        console.log('Adding waste material row with index:', wasteMaterialIndex);
        const newRow = createWasteMaterialRow(wasteMaterialIndex);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newRow;
        const wasteMaterialRow = tempDiv.firstElementChild;
        wasteMaterialsContainer.appendChild(wasteMaterialRow);
        console.log('Waste material row added successfully');
    }

    function updateMaterialCalculations(row) {
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('.material-qty');
        const wasteInput = row.querySelector('.material-waste-amount');
        const costInput = row.querySelector('.material-cost');
        const wastePercentSpan = row.querySelector('.material-waste-percent');
        const totalQtySpan = row.querySelector('.material-total-qty');
        
        if (select.value && qtyInput.value) {
            const selectedOption = select.selectedOptions[0];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const qty = parseFloat(qtyInput.value) || 0;
            const wasteAmount = parseFloat(wasteInput.value) || 0;
            
            // Calculate waste percentage and total quantities
            const wastePercent = qty > 0 ? (wasteAmount / qty) * 100 : 0;
            const totalQty = qty + wasteAmount;
            const totalCost = totalQty * price;
            
            // Update display
            costInput.value = '₹' + totalCost.toFixed(2);
            wastePercentSpan.textContent = wastePercent.toFixed(2) + '%';
            totalQtySpan.textContent = totalQty.toFixed(3);
        } else {
            costInput.value = '';
            wastePercentSpan.textContent = '0.00%';
            totalQtySpan.textContent = '0.000';
        }
        
        updateSummary();
        updateRequirementsTable();
    }

    function updateWasteMaterialCalculations(row) {
        const select = row.querySelector('.waste-material-select');
        const qtyInput = row.querySelector('.waste-material-qty');
        const valueInput = row.querySelector('.waste-material-value');
        const unitCostSpan = row.querySelector('.waste-material-unit-cost');
        const totalValueSpan = row.querySelector('.waste-material-total-value');

        if (select.value && qtyInput.value) {
            const selectedOption = select.selectedOptions[0];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const qty = parseFloat(qtyInput.value) || 0;
            const totalValue = qty * price;

            unitCostSpan.textContent = price.toFixed(2);
            totalValueSpan.textContent = '₹' + totalValue.toFixed(2);
        } else {
            unitCostSpan.textContent = '0.00';
            totalValueSpan.textContent = '0.00';
        }

        updateSummary();
        updateRequirementsTable();
    }

    function updateAllMaterialCalculations() {
        const rows = document.querySelectorAll('.material-row');
        rows.forEach(row => updateMaterialCalculations(row));
    }

    function calculateMaterialRequirements() {
        const productionQty = parseFloat(productionQuantity.value) || 1;
        const rows = document.querySelectorAll('.material-row');
        
        rows.forEach(row => {
            const qtyInput = row.querySelector('.material-qty');
            const currentQty = parseFloat(qtyInput.value) || 0;
            const newQty = currentQty * productionQty;
            qtyInput.value = newQty.toFixed(3); // Changed to 3 decimal places
            updateMaterialCalculations(row);
        });
        
        showNotification(`Material requirements calculated for ${productionQty} units`, 'success');
    }

    function optimizeWastePercentages() {
        const rows = document.querySelectorAll('.material-row');
        let totalWaste = 0;
        let validMaterials = 0;
        
        rows.forEach(row => {
            const wasteInput = row.querySelector('.material-waste-amount'); // Changed to material-waste-amount
            if (wasteInput.value) {
                totalWaste += parseFloat(wasteInput.value) || 0;
                validMaterials++;
            }
        });
        
        const avgWaste = validMaterials > 0 ? totalWaste / validMaterials : 0;
        
        rows.forEach(row => {
            const wasteInput = row.querySelector('.material-waste-amount'); // Changed to material-waste-amount
            const currentWaste = parseFloat(wasteInput.value) || 0;
            const optimizedWaste = Math.min(currentWaste * 0.9, 15); // Reduce waste by 10%, max 15%
            wasteInput.value = optimizedWaste.toFixed(3); // Changed to 3 decimal places
            updateMaterialCalculations(row);
        });
        
        showNotification(`Waste percentages optimized. Average waste reduced to ${avgWaste.toFixed(1)}%`, 'info');
    }

    function updateSummary() {
        const materials = document.querySelectorAll('.material-row');
        let totalRawMaterials = 0;
        let totalWasteMaterials = 0;
        let totalCost = 0;
        let totalWaste = 0;
        let totalQuantity = 0;
        let totalWasteQuantity = 0;
        let validMaterials = 0;

        materials.forEach(row => {
            const qtyInput = row.querySelector('.material-qty');
            const wasteInput = row.querySelector('.material-waste-amount'); // Changed to material-waste-amount
            const costInput = row.querySelector('.material-cost');
            
            if (qtyInput.value) {
                validMaterials++;
                totalQuantity += parseFloat(qtyInput.value) || 0;
                totalWasteQuantity += parseFloat(wasteInput.value) || 0;
                
                const costText = costInput.value.replace('₹', '');
                totalCost += parseFloat(costText) || 0;
            }
        });

        document.getElementById('totalRawMaterials').textContent = validMaterials;
        document.getElementById('totalWasteMaterials').textContent = materials.length - validMaterials;
        document.getElementById('totalCost').textContent = '₹' + totalCost.toFixed(2);
        document.getElementById('totalQuantity').textContent = totalQuantity.toFixed(2);
        document.getElementById('totalWasteQuantity').textContent = totalWasteQuantity.toFixed(2);
        
        const avgWaste = validMaterials > 0 ? totalWaste / validMaterials : 0;
        document.getElementById('totalWaste').textContent = avgWaste.toFixed(1) + '%';
    }

    function updateRequirementsTable() {
        const tbody = document.getElementById('requirementsTableBody');
        const rows = document.querySelectorAll('.material-row');
        
        let tableHTML = '';
        
        rows.forEach(row => {
            const select = row.querySelector('.material-select');
            const qtyInput = row.querySelector('.material-qty');
            const wasteInput = row.querySelector('.material-waste-amount'); // Changed to material-waste-amount
            const costInput = row.querySelector('.material-cost');
            
            if (select.value && qtyInput.value) {
                const selectedOption = select.selectedOptions[0];
                const materialName = selectedOption.textContent;
                const qty = parseFloat(qtyInput.value) || 0;
                const wasteAmount = parseFloat(wasteInput.value) || 0;
                const wastePercent = (wasteAmount / qty) * 100; // Calculate waste percentage
                const totalQty = qty + wasteAmount;
                const unitCost = parseFloat(selectedOption.dataset.price) || 0;
                const totalCost = parseFloat(costInput.value.replace('₹', '')) || 0;
                
                tableHTML += `
                    <tr>
                        <td>Raw Material</td>
                        <td>${materialName}</td>
                        <td>${qty.toFixed(3)}</td>
                        <td>${wasteAmount.toFixed(3)} (${wastePercent.toFixed(1)}%)</td>
                        <td>${totalQty.toFixed(3)}</td>
                        <td>₹${unitCost.toFixed(2)}</td>
                        <td>₹${totalCost.toFixed(2)}</td>
                    </tr>
                `;
            }
        });
        
        tbody.innerHTML = tableHTML;
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-info-circle"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    function addSampleMaterials() {
        // Clear existing materials
        materialsContainer.innerHTML = '';
        materialIndex = 0;
        
        // Add sample materials based on user's example
        const sampleMaterials = [
            {
                material_id: materialsData.length > 0 ? materialsData[0].id : '',
                quantity: 1.000,
                waste_amount: 0.050,
                unit: 'kg'
            },
            {
                material_id: materialsData.length > 1 ? materialsData[1].id : '',
                quantity: 2.000,
                waste_amount: 0.040,
                unit: 'kg'
            }
        ];
        
        sampleMaterials.forEach((sample, index) => {
            materialIndex++;
            const newRow = createMaterialRow(materialIndex);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newRow;
            const materialRow = tempDiv.firstElementChild;
            materialsContainer.appendChild(materialRow);
            
            // Set values for the sample material
            if (sample.material_id) {
                materialRow.querySelector('.material-select').value = sample.material_id;
                materialRow.querySelector('.material-qty').value = sample.quantity;
                materialRow.querySelector('.material-waste-amount').value = sample.waste_amount;
                materialRow.querySelector('.material-unit').value = sample.unit;
                materialRow.querySelector('.material-waste-unit').textContent = sample.unit;
                
                // Trigger change event to update calculations
                const select = materialRow.querySelector('.material-select');
                const event = new Event('change');
                select.dispatchEvent(event);
            }
        });
        
        showNotification('Sample materials added: 1kg Raw Material 1 (50g waste) and 2kg Raw Material 2 (40g waste)', 'success');
    }

    function addSampleWasteMaterials() {
        // Clear existing waste materials
        wasteMaterialsContainer.innerHTML = '';
        wasteMaterialIndex = 0;

        // Add sample waste materials based on user's example
        const sampleWasteMaterials = [
            {
                material_id: wasteMaterialsData.length > 0 ? wasteMaterialsData[0].id : '',
                quantity: 1.000,
                unit: 'kg'
            },
            {
                material_id: wasteMaterialsData.length > 1 ? wasteMaterialsData[1].id : '',
                quantity: 2.000,
                unit: 'kg'
            }
        ];

        sampleWasteMaterials.forEach((sample, index) => {
            wasteMaterialIndex++;
            const newRow = createWasteMaterialRow(wasteMaterialIndex);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newRow;
            const wasteMaterialRow = tempDiv.firstElementChild;
            wasteMaterialsContainer.appendChild(wasteMaterialRow);

            // Set values for the sample waste material
            if (sample.material_id) {
                wasteMaterialRow.querySelector('.waste-material-select').value = sample.material_id;
                wasteMaterialRow.querySelector('.waste-material-qty').value = sample.quantity;
                wasteMaterialRow.querySelector('.waste-material-unit').value = sample.unit;

                // Trigger change event to update calculations
                const select = wasteMaterialRow.querySelector('.waste-material-select');
                const event = new Event('change');
                select.dispatchEvent(event);
            }
        });

        showNotification('Sample waste materials added: 1kg Waste Material 1 and 2kg Waste Material 2', 'success');
    }
});
</script>
<?= $this->endSection() ?> 