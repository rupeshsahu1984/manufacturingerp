<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .component-row, .operation-row, .byproduct-row {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .remove-row {
            color: #dc3545;
            cursor: pointer;
        }
        .remove-row:hover {
            color: #c82333;
        }
        .form-section {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .section-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-industry me-2"></i>PRODX
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= base_url('production') ?>">
                    <i class="fas fa-cogs me-1"></i>Production
                </a>
                <a class="nav-link" href="<?= base_url('production/boms') ?>">
                    <i class="fas fa-list-alt me-1"></i>BOMs
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-dark">
                            <i class="fas fa-plus me-2 text-primary"></i>Create New BOM
                        </h1>
                        <p class="text-muted mb-0">Define the structure and components for your finished product</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('production/boms') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to BOMs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= base_url('production/boms/store') ?>" method="POST" id="bomForm">
            <!-- Basic BOM Information -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Basic BOM Information
                    </h5>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="bom_number" class="form-label">BOM Number *</label>
                        <input type="text" class="form-control" id="bom_number" name="bom_number" 
                               value="<?= $bomNumber ?>" required readonly>
                        <div class="form-text">Auto-generated BOM number</div>
                    </div>
                    <div class="col-md-6">
                        <label for="item_id_fg" class="form-label">Finished Good Item *</label>
                        <select class="form-select select2" id="item_id_fg" name="item_id_fg" required>
                            <option value="">Select Finished Good Item</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id'] ?>">
                                    <?= $item['item_code'] ?> - <?= $item['item_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="revision" class="form-label">Revision *</label>
                        <input type="text" class="form-control" id="revision" name="revision" 
                               value="A" required>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2" 
                                  placeholder="Enter BOM description..."></textarea>
                    </div>
                    <div class="col-md-3">
                        <label for="uom" class="form-label">Unit of Measure *</label>
                        <input type="text" class="form-control" id="uom" name="uom" 
                               placeholder="e.g., PCS, KG, M" required>
                    </div>
                    <div class="col-md-3">
                        <label for="qty_per" class="form-label">Quantity Per *</label>
                        <input type="number" class="form-control" id="qty_per" name="qty_per" 
                               value="1" min="0.01" step="0.01" required>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="bom_type" class="form-label">BOM Type *</label>
                        <select class="form-select" id="bom_type" name="bom_type" required>
                            <option value="">Select BOM Type</option>
                            <?php foreach ($bomTypes as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="effective_from" class="form-label">Effective From *</label>
                        <input type="date" class="form-control" id="effective_from" name="effective_from" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="effective_to" class="form-label">Effective To</label>
                        <input type="date" class="form-control" id="effective_to" name="effective_to">
                        <div class="form-text">Leave blank for indefinite validity</div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_phantom" name="is_phantom" value="1">
                            <label class="form-check-label" for="is_phantom">
                                Phantom BOM (Explode only, no stock)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Active BOM
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOM Components -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-puzzle-piece me-2"></i>BOM Components
                    </h5>
                    <p class="text-muted mb-0">Define the raw materials and sub-assemblies required</p>
                </div>
                
                <div id="componentsContainer">
                    <!-- Component rows will be added here -->
                </div>
                
                <div class="text-center">
                    <button type="button" class="btn btn-outline-primary" onclick="addComponentRow()">
                        <i class="fas fa-plus me-2"></i>Add Component
                    </button>
                </div>
            </div>

            <!-- BOM Operations -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-cogs me-2"></i>BOM Operations
                    </h5>
                    <p class="text-muted mb-0">Define the manufacturing operations and routing</p>
                </div>
                
                <div id="operationsContainer">
                    <!-- Operation rows will be added here -->
                </div>
                
                <div class="text-center">
                    <button type="button" class="btn btn-outline-primary" onclick="addOperationRow()">
                        <i class="fas fa-plus me-2"></i>Add Operation
                    </button>
                </div>
            </div>

            <!-- By-Products -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-recycle me-2"></i>By-Products
                    </h5>
                    <p class="text-muted mb-0">Define secondary outputs from the manufacturing process</p>
                </div>
                
                <div id="byproductsContainer">
                    <!-- By-product rows will be added here -->
                </div>
                
                <div class="text-center">
                    <button type="button" class="btn btn-outline-primary" onclick="addByProductRow()">
                        <i class="fas fa-plus me-2"></i>Add By-Product
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-sticky-note me-2"></i>Additional Information
                    </h5>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Enter any additional notes or special instructions..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('production/boms') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create BOM
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="saveAsDraft()">
                            <i class="fas fa-edit me-2"></i>Save as Draft
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let componentCounter = 0;
        let operationCounter = 0;
        let byproductCounter = 0;

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Add initial rows
            addComponentRow();
            addOperationRow();

            // Form validation
            $('#bomForm').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    alert('Please fill in all required fields and ensure at least one component is added.');
                }
            });
        });

        function addComponentRow() {
            componentCounter++;
            const row = `
                <div class="component-row" id="component_${componentCounter}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Component ${componentCounter}</h6>
                        <i class="fas fa-times remove-row" onclick="removeRow('component_${componentCounter}')"></i>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Component Item *</label>
                            <select class="form-select select2" name="components[${componentCounter}][item_id]" required>
                                <option value="">Select Component Item</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?= $item['id'] ?>">
                                        <?= $item['item_code'] ?> - <?= $item['item_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity *</label>
                            <input type="number" class="form-control" name="components[${componentCounter}][qty]" 
                                   value="1" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">UOM *</label>
                            <input type="text" class="form-control" name="components[${componentCounter}][uom]" 
                                   placeholder="UOM" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Scrap %</label>
                            <input type="number" class="form-control" name="components[${componentCounter}][scrap_pct]" 
                                   value="0" min="0" max="100" step="0.1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Yield %</label>
                            <input type="number" class="form-control" name="components[${componentCounter}][yield_pct]" 
                                   value="100" min="1" max="100" step="0.1">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Position</label>
                            <input type="number" class="form-control" name="components[${componentCounter}][position]" 
                                   value="${componentCounter}" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Priority</label>
                            <input type="number" class="form-control" name="components[${componentCounter}][priority]" 
                                   value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Reference Designator</label>
                            <input type="text" class="form-control" name="components[${componentCounter}][reference_designator]" 
                                   placeholder="e.g., R1, C2">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="components[${componentCounter}][is_alternate]" value="1">
                                <label class="form-check-label">Alternate Component</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="components[${componentCounter}][notes]" rows="1" 
                                      placeholder="Component notes..."></textarea>
                        </div>
                    </div>
                </div>
            `;
            $('#componentsContainer').append(row);
            
            // Initialize Select2 for new row
            $(`#component_${componentCounter} .select2`).select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        function addOperationRow() {
            operationCounter++;
            const row = `
                <div class="operation-row" id="operation_${operationCounter}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Operation ${operationCounter}</h6>
                        <i class="fas fa-times remove-row" onclick="removeRow('operation_${operationCounter}')"></i>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Operation Name *</label>
                            <input type="text" class="form-control" name="operations[${operationCounter}][operation_name]" 
                                   placeholder="e.g., Cutting, Assembly" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Workcenter ID *</label>
                            <input type="number" class="form-control" name="operations[${operationCounter}][workcenter_id]" 
                                   placeholder="Workcenter ID" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Setup Time (min)</label>
                            <input type="number" class="form-control" name="operations[${operationCounter}][setup_time]" 
                                   value="0" min="0" step="0.1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Run Time/Unit (min)</label>
                            <input type="number" class="form-control" name="operations[${operationCounter}][run_time_per_unit]" 
                                   value="0" min="0" step="0.1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Labor Rate/hr</label>
                            <input type="number" class="form-control" name="operations[${operationCounter}][labor_rate]" 
                                   value="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Machine Rate/hr</label>
                            <input type="number" class="form-control" name="operations[${operationCounter}][machine_rate]" 
                                   value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="operations[${operationCounter}][notes]" rows="1" 
                                      placeholder="Operation notes..."></textarea>
                        </div>
                    </div>
                </div>
            `;
            $('#operationsContainer').append(row);
        }

        function addByProductRow() {
            byproductCounter++;
            const row = `
                <div class="byproduct-row" id="byproduct_${byproductCounter}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">By-Product ${byproductCounter}</h6>
                        <i class="fas fa-times remove-row" onclick="removeRow('byproduct_${byproductCounter}')"></i>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">By-Product Item *</label>
                            <select class="form-select select2" name="by_products[${byproductCounter}][item_id]" required>
                                <option value="">Select By-Product Item</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?= $item['id'] ?>">
                                        <?= $item['item_code'] ?> - <?= $item['item_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Yield Qty *</label>
                            <input type="number" class="form-control" name="by_products[${byproductCounter}][yield_qty]" 
                                   value="1" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Yield %</label>
                            <input type="number" class="form-control" name="by_products[${byproductCounter}][yield_pct]" 
                                   value="0" min="0" max="100" step="0.1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Valuation Method</label>
                            <select class="form-select" name="by_products[${byproductCounter}][valuation_method]">
                                <option value="standard_cost">Standard Cost</option>
                                <option value="market_price">Market Price</option>
                                <option value="zero">Zero Value</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="by_products[${byproductCounter}][notes]" rows="1" 
                                      placeholder="By-product notes..."></textarea>
                        </div>
                    </div>
                </div>
            `;
            $('#byproductsContainer').append(row);
            
            // Initialize Select2 for new row
            $(`#byproduct_${byproductCounter} .select2`).select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        function removeRow(rowId) {
            $(`#${rowId}`).remove();
        }

        function validateForm() {
            // Check if at least one component is added
            if ($('.component-row').length === 0) {
                return false;
            }

            // Check required fields
            const requiredFields = $('#bomForm').find('[required]');
            let isValid = true;
            
            requiredFields.each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            return isValid;
        }

        function saveAsDraft() {
            // Add a hidden field for draft status
            if (!$('#status').length) {
                $('#bomForm').append('<input type="hidden" name="status" value="draft">');
            }
            $('#submitBtn').text('Save as Draft');
            $('#bomForm').submit();
        }
    </script>
</body>
</html>
