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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
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
        
        .route-visualization {
            background: #f8f9fa;
            border: 2px dashed #28a745;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
        }
        
        .warehouse-box {
            background: white;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            display: inline-block;
            min-width: 200px;
        }
        
        .route-arrow {
            font-size: 2rem;
            color: #28a745;
            margin: 0 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .item-row {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            position: relative;
        }
        
        .item-row:hover {
            background: #e9ecef;
            border-color: #28a745;
        }
        
        .remove-item {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .remove-item:hover {
            background: #c82333;
        }
        
        .stock-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            font-size: 0.9rem;
        }
        
        .stock-available {
            color: #2e7d32;
            font-weight: 600;
        }
        
        .stock-low {
            color: #f57f17;
            font-weight: 600;
        }
        
        .stock-unavailable {
            color: #c62828;
            font-weight: 600;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
            color: #28a745;
        }
        
        .section-title {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #28a745;
        }
        
        .btn-add-item {
            background: #28a745;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-add-item:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .status-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .status-warning .alert-heading {
            color: #856404;
            font-weight: 600;
        }
        
        .status-warning .alert-body {
            color: #856404;
        }
        
        .existing-items {
            background: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .existing-items .alert-heading {
            color: #155724;
            font-weight: 600;
        }
        
        .existing-items .alert-body {
            color: #155724;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-edit me-3"></i>
                Edit Stock Transfer
            </h1>
            <p class="mb-0">Transfer #<?= esc(isset($transfer['transfer_number']) ? $transfer['transfer_number'] : 'N/A') ?></p>
        </div>

        <!-- Navigation Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/inventory" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Inventory Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/inventory/transfers" class="text-decoration-none">Stock Transfers</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/inventory/transfers/view/<?= $transfer['id'] ?>" class="text-decoration-none">Transfer Details</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit Transfer</li>
            </ol>
        </nav>

        <!-- Status Warning -->
        <?php if (in_array(isset($transfer['status']) ? $transfer['status'] : '', ['approved', 'in_transit', 'received'])): ?>
            <div class="status-warning">
                <div class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Transfer Status Warning
                </div>
                <div class="alert-body">
                    This transfer is currently in <strong><?= ucwords(str_replace('_', ' ', isset($transfer['status']) ? $transfer['status'] : '')) ?></strong> status. 
                    Some fields may be restricted from editing to maintain data integrity.
                </div>
            </div>
        <?php endif; ?>

        <form action="/inventory/transfers/update/<?= $transfer['id'] ?>" method="POST" id="transferForm">
            <!-- Basic Information -->
            <div class="form-container">
                <h4 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Transfer Information
                </h4>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="transfer_date" class="form-label">Transfer Date *</label>
                        <input type="date" class="form-control" id="transfer_date" name="transfer_date" 
                               value="<?= isset($transfer['transfer_date']) ? $transfer['transfer_date'] : date('Y-m-d') ?>" required
                               <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['approved', 'in_transit', 'received']) ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-6">
                        <label for="expected_delivery_date" class="form-label">Expected Delivery Date *</label>
                        <input type="date" class="form-control" id="expected_delivery_date" name="expected_delivery_date" 
                               value="<?= isset($transfer['expected_delivery_date']) ? $transfer['expected_delivery_date'] : date('Y-m-d', strtotime('+3 days')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="priority" class="form-label">Priority *</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            <?php foreach ($priorities as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($transfer['priority']) ? $transfer['priority'] : '') === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="requested_by" class="form-label">Requested By *</label>
                        <select class="form-select" id="requested_by" name="requested_by" required
                                <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['approved', 'in_transit', 'received']) ? 'disabled' : '' ?>>
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>" <?= (isset($transfer['requested_by']) ? $transfer['requested_by'] : '') == $employee['id'] ? 'selected' : '' ?>>
                                    <?= esc($employee['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="approved_by" class="form-label">Approved By</label>
                        <select class="form-select" id="approved_by" name="approved_by">
                            <option value="">Select Approver</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>" <?= (isset($transfer['approved_by']) ? $transfer['approved_by'] : '') == $employee['id'] ? 'selected' : '' ?>>
                                    <?= esc($employee['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Route Information -->
            <div class="form-container">
                <h4 class="section-title">
                    <i class="fas fa-route me-2"></i>
                    Transfer Route
                </h4>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="source_warehouse_id" class="form-label">Source Warehouse *</label>
                        <select class="form-select" id="source_warehouse_id" name="source_warehouse_id" required
                                <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['approved', 'in_transit', 'received']) ? 'disabled' : '' ?>>
                            <option value="">Select Source Warehouse</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>" 
                                        data-capacity="<?= $warehouse['capacity_total'] ?>"
                                        data-available="<?= isset($warehouse['capacity_available']) ? $warehouse['capacity_available'] : $warehouse['capacity_total'] ?>"
                                        <?= (isset($transfer['source_warehouse_id']) ? $transfer['source_warehouse_id'] : '') == $warehouse['id'] ? 'selected' : '' ?>>
                                    <?= esc($warehouse['warehouse_name']) ?> 
                                    (<?= esc($warehouse['warehouse_type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="destination_warehouse_id" class="form-label">Destination Warehouse *</label>
                        <select class="form-select" id="destination_warehouse_id" name="destination_warehouse_id" required
                                <?= in_array(isset($transfer['status']) ? $transfer['status'] : '', ['approved', 'in_transit', 'received']) ? 'disabled' : '' ?>>
                            <option value="">Select Destination Warehouse</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>" 
                                        data-capacity="<?= $warehouse['capacity_total'] ?>"
                                        data-available="<?= isset($warehouse['capacity_available']) ? $warehouse['capacity_available'] : $warehouse['capacity_total'] ?>"
                                        <?= (isset($transfer['destination_warehouse_id']) ? $transfer['destination_warehouse_id'] : '') == $warehouse['id'] ? 'selected' : '' ?>>
                                    <?= esc($warehouse['warehouse_name']) ?> 
                                    (<?= esc($warehouse['warehouse_type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Route Visualization -->
                <div class="route-visualization" id="routeVisualization" style="display: none;">
                    <div class="warehouse-box" id="sourceWarehouseBox">
                        <i class="fas fa-warehouse fa-2x text-primary mb-2"></i>
                        <h6 id="sourceWarehouseName">Source</h6>
                        <small class="text-muted" id="sourceWarehouseType"></small>
                    </div>
                    
                    <i class="fas fa-arrow-right route-arrow"></i>
                    
                    <div class="warehouse-box" id="destinationWarehouseBox">
                        <i class="fas fa-warehouse fa-2x text-success mb-2"></i>
                        <h6 id="destinationWarehouseName">Destination</h6>
                        <small class="text-muted" id="destinationWarehouseType"></small>
                    </div>
                </div>
            </div>

            <!-- Transport Information -->
            <div class="form-container">
                <h4 class="section-title">
                    <i class="fas fa-truck me-2"></i>
                    Transport Details
                </h4>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="transport_mode" class="form-label">Transport Mode</label>
                        <select class="form-select" id="transport_mode" name="transport_mode">
                            <option value="">Select Transport Mode</option>
                            <?php foreach ($transport_modes as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($transfer['transport_mode']) ? $transfer['transport_mode'] : '') === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="carrier" class="form-label">Carrier/Company</label>
                        <input type="text" class="form-control" id="carrier" name="carrier" 
                               value="<?= esc(isset($transfer['carrier']) ? $transfer['carrier'] : '') ?>"
                               placeholder="Transport company name">
                    </div>
                    <div class="col-md-4">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" id="tracking_number" name="tracking_number" 
                               value="<?= esc(isset($transfer['tracking_number']) ? $transfer['tracking_number'] : '') ?>"
                               placeholder="Tracking/AWB number">
                    </div>
                    <div class="col-md-6">
                        <label for="special_instructions" class="form-label">Special Instructions</label>
                        <textarea class="form-control" id="special_instructions" name="special_instructions" 
                                  rows="3" placeholder="Any special handling instructions..."><?= esc(isset($transfer['special_instructions']) ? $transfer['special_instructions'] : '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" 
                                  rows="3" placeholder="Additional notes or comments..."><?= esc(isset($transfer['notes']) ? $transfer['notes'] : '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Existing Items Information -->
            <?php if (isset($transfer_items) && is_array($transfer_items) && count($transfer_items) > 0): ?>
                <div class="existing-items">
                    <div class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Existing Transfer Items
                    </div>
                    <div class="alert-body">
                        This transfer currently has <strong><?= count($transfer_items) ?> items</strong> with a total value of 
                        <strong>₹<?= number_format(isset($transfer['total_value']) ? $transfer['total_value'] : 0, 2) ?></strong>.
                        <br>
                        <small>To modify items, please use the "Manage Items" section below or visit the dedicated items page.</small>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Items to Transfer -->
            <div class="form-container">
                <h4 class="section-title">
                    <i class="fas fa-boxes me-2"></i>
                    Manage Transfer Items
                </h4>
                
                <div id="itemsContainer">
                    <!-- Items will be added here dynamically -->
                </div>
                
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-add-item" onclick="addItemRow()">
                        <i class="fas fa-plus me-2"></i>Add New Item
                    </button>
                </div>
                
                <div class="mt-3">
                    <a href="/inventory/transfers/items/<?= $transfer['id'] ?>" class="btn btn-outline-info">
                        <i class="fas fa-cog me-2"></i>Manage All Items
                    </a>
                </div>
            </div>

            <!-- Transfer Summary -->
            <div class="form-container">
                <h4 class="section-title">
                    <i class="fas fa-calculator me-2"></i>
                    Transfer Summary
                </h4>
                
                <div class="summary-card">
                    <div class="summary-item">
                        <span>Total Items:</span>
                        <span id="totalItems"><?= isset($transfer['total_items']) ? $transfer['total_items'] : 0 ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Total Quantity:</span>
                        <span id="totalQuantity">0</span>
                    </div>
                    <div class="summary-item">
                        <span>Total Value:</span>
                        <span id="totalValue">₹<?= number_format(isset($transfer['total_value']) ? $transfer['total_value'] : 0, 2) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Estimated Weight:</span>
                        <span id="totalWeight">0 kg</span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-container text-center">
                <button type="button" class="btn btn-secondary me-3" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Update Transfer
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        let itemCounter = 0;
        let items = [];

        $(document).ready(function() {
            // Initialize Select2
            $('.form-select').select2({
                theme: 'bootstrap-5'
            });

            // Update route visualization when warehouses change
            $('#source_warehouse_id, #destination_warehouse_id').change(function() {
                updateRouteVisualization();
            });

            // Show initial route visualization
            updateRouteVisualization();
        });

        function updateRouteVisualization() {
            const sourceId = $('#source_warehouse_id').val();
            const destId = $('#destination_warehouse_id').val();
            
            if (sourceId && destId && sourceId !== destId) {
                const sourceWarehouse = $('#source_warehouse_id option:selected');
                const destWarehouse = $('#destination_warehouse_id option:selected');
                
                $('#sourceWarehouseName').text(sourceWarehouse.text());
                $('#sourceWarehouseType').text(sourceWarehouse.text().match(/\((.*?)\)/)?.[1] || '');
                
                $('#destinationWarehouseName').text(destWarehouse.text());
                $('#destinationWarehouseType').text(destWarehouse.text().match(/\((.*?)\)/)?.[1] || '');
                
                $('#routeVisualization').show();
            } else {
                $('#routeVisualization').hide();
            }
        }

        function addItemRow() {
            itemCounter++;
            const itemRow = `
                <div class="item-row" id="itemRow${itemCounter}">
                    <button type="button" class="remove-item" onclick="removeItem(${itemCounter})">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Item *</label>
                            <select class="form-select item-select" name="items[${itemCounter}][item_id]" required 
                                    onchange="loadItemDetails(${itemCounter}, this.value)">
                                <option value="">Select Item</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-cost="<?= isset($item['standard_cost']) ? $item['standard_cost'] : 0 ?>"
                                            data-weight="<?= isset($item['weight']) ? $item['weight'] : 0 ?>"
                                            data-uom="<?= esc($item['uom']) ?>"
                                            data-stock="<?= isset($item['current_stock']) ? $item['current_stock'] : 0 ?>">
                                        <?= esc($item['item_name']) ?> (<?= esc($item['item_code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity *</label>
                            <input type="number" class="form-control item-quantity" 
                                   name="items[${itemCounter}][quantity]" 
                                   min="1" step="1" required
                                   onchange="updateItemTotal(${itemCounter})">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit Cost</label>
                            <input type="number" class="form-control item-cost" 
                                   name="items[${itemCounter}][unit_cost]" 
                                   step="0.01" min="0"
                                   onchange="updateItemTotal(${itemCounter})">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total Cost</label>
                            <input type="text" class="form-control item-total" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Batch Number</label>
                            <input type="text" class="form-control" 
                                   name="items[${itemCounter}][batch_number]" 
                                   placeholder="Optional">
                        </div>
                    </div>
                    
                    <div class="stock-info" id="stockInfo${itemCounter}" style="display: none;">
                        <strong>Stock Information:</strong>
                        <span id="stockStatus${itemCounter}"></span>
                    </div>
                    
                    <input type="hidden" name="items[${itemCounter}][source_warehouse_id]" 
                           value="${$('#source_warehouse_id').val()}">
                    <input type="hidden" name="items[${itemCounter}][destination_warehouse_id]" 
                           value="${$('#destination_warehouse_id').val()}">
                </div>
            `;
            
            $('#itemsContainer').append(itemRow);
            
            // Initialize Select2 for new item row
            $(`#itemRow${itemCounter} .item-select`).select2({
                theme: 'bootstrap-5'
            });
        }

        function removeItem(counter) {
            $(`#itemRow${counter}`).remove();
            updateSummary();
        }

        function loadItemDetails(counter, itemId) {
            if (!itemId) return;
            
            const item = $(`#itemRow${counter} .item-select option:selected`);
            const unitCost = parseFloat(item.data('cost')) || 0;
            const weight = parseFloat(item.data('weight')) || 0;
            const uom = item.data('uom') || '';
            const stock = parseInt(item.data('stock')) || 0;
            
            // Set unit cost
            $(`#itemRow${counter} .item-cost`).val(unitCost);
            
            // Show stock information
            const stockInfo = $(`#stockInfo${counter}`);
            const stockStatus = $(`#stockStatus${counter}`);
            
            let statusClass = 'stock-available';
            let statusText = `Available: ${stock} ${uom}`;
            
            if (stock === 0) {
                statusClass = 'stock-unavailable';
                statusText = `Out of Stock`;
            } else if (stock < 10) {
                statusClass = 'stock-low';
                statusText = `Low Stock: ${stock} ${uom}`;
            }
            
            stockStatus.removeClass().addClass(statusClass).text(statusText);
            stockInfo.show();
            
            updateItemTotal(counter);
        }

        function updateItemTotal(counter) {
            const quantity = parseFloat($(`#itemRow${counter} .item-quantity`).val()) || 0;
            const unitCost = parseFloat($(`#itemRow${counter} .item-cost`).val()) || 0;
            const total = quantity * unitCost;
            
            $(`#itemRow${counter} .item-total`).val(`₹${total.toFixed(2)}`);
            updateSummary();
        }

        function updateSummary() {
            let totalItems = 0;
            let totalQuantity = 0;
            let totalValue = 0;
            let totalWeight = 0;
            
            $('.item-row').each(function() {
                const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                const unitCost = parseFloat($(this).find('.item-cost').val()) || 0;
                const item = $(this).find('.item-select option:selected');
                const weight = parseFloat(item.data('weight')) || 0;
                
                if (quantity > 0) {
                    totalItems++;
                    totalQuantity += quantity;
                    totalValue += quantity * unitCost;
                    totalWeight += quantity * weight;
                }
            });
            
            // Add existing items count
            const existingItems = <?= count(isset($transfer_items) ? $transfer_items : []) ?>;
            totalItems += existingItems;
            
            $('#totalItems').text(totalItems);
            $('#totalQuantity').text(totalQuantity);
            $('#totalValue').text(`₹${totalValue.toFixed(2)}`);
            $('#totalWeight').text(`${totalWeight.toFixed(2)} kg`);
        }

        // Form validation
        $('#transferForm').submit(function(e) {
            const sourceWarehouse = $('#source_warehouse_id').val();
            const destWarehouse = $('#destination_warehouse_id').val();
            
            if (sourceWarehouse === destWarehouse) {
                e.preventDefault();
                alert('Source and destination warehouses cannot be the same.');
                return false;
            }
            
            // Validate all items have required fields
            let isValid = true;
            $('.item-row').each(function() {
                const itemId = $(this).find('.item-select').val();
                const quantity = $(this).find('.item-quantity').val();
                
                if (!itemId || !quantity) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields for all items.');
                return false;
            }
        });
    </script>
</body>
</html>
