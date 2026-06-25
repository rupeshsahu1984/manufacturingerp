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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid #dc3545;
        }
        
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        
        .form-section h6 {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 8px;
        }
        
        .item-row {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #dee2e6;
            position: relative;
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
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .remove-item:hover {
            background: #c82333;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .btn-create {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .quality-issue {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .quality-issue h6 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px;
            padding: 0.375rem 0.75rem;
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-undo me-3"></i>
                Create Debit Note
            </h1>
            <p class="mb-0">Generate debit note for supplier returns and quality issues</p>
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
                    <a href="/purchase/debit-notes" class="text-decoration-none">Debit Notes</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Create Debit Note</li>
            </ol>
        </nav>

        <!-- Form -->
        <form id="debitNoteForm" method="POST" action="/purchase/debit-notes/store">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Basic Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="debit_note_number" class="form-label">Debit Note Number</label>
                                    <input type="text" class="form-control" id="debit_note_number" name="debit_note_number" 
                                           value="<?= isset($debit_note_number) ? $debit_note_number : '' ?>" readonly>
                                    <div class="form-text">Auto-generated number</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="debit_note_date" class="form-label">Debit Note Date</label>
                                    <input type="date" class="form-control" id="debit_note_date" name="debit_note_date" 
                                           value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Supplier *</label>
                                    <select class="form-select select2" id="supplier_id" name="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier['id'] ?>"><?= esc($supplier['supplier_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reference Information -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-link me-2"></i>
                            Reference Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="grn_id" class="form-label">Goods Receipt Note (GRN)</label>
                                    <select class="form-select select2" id="grn_id" name="grn_id">
                                        <option value="">Select GRN (Optional)</option>
                                        <?php foreach ($goods_receipts as $grn): ?>
                                            <option value="<?= $grn['id'] ?>" data-supplier="<?= $grn['supplier_id'] ?>">
                                                <?= esc($grn['grn_number']) ?> - <?= esc($grn['supplier_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Link to specific GRN for returns</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purchase_order_id" class="form-label">Purchase Order</label>
                                    <select class="form-select select2" id="purchase_order_id" name="purchase_order_id">
                                        <option value="">Select PO (Optional)</option>
                                        <?php foreach ($purchase_orders as $po): ?>
                                            <option value="<?= $po['id'] ?>" data-supplier="<?= $po['supplier_id'] ?>">
                                                <?= esc($po['po_number']) ?> - <?= esc($po['supplier_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Link to specific purchase order</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Return Items -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-boxes me-2"></i>
                            Return Items
                        </h5>
                        
                        <div id="itemsContainer">
                            <!-- Items will be added here dynamically -->
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-primary" onclick="addItemRow()">
                                <i class="fas fa-plus me-2"></i>Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Quality Issues & Return Reason -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Quality Issues & Return Reason
                        </h5>
                        
                        <div class="quality-issue">
                            <h6>Quality Issue Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quality_issue_type" class="form-label">Issue Type</label>
                                        <select class="form-select" id="quality_issue_type" name="quality_issue_type">
                                            <option value="">Select Issue Type</option>
                                            <option value="damaged">Damaged Goods</option>
                                            <option value="defective">Defective Quality</option>
                                            <option value="wrong_specification">Wrong Specification</option>
                                            <option value="expired">Expired Items</option>
                                            <option value="shortage">Quantity Shortage</option>
                                            <option value="excess">Excess Supply</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="severity" class="form-label">Severity Level</label>
                                        <select class="form-select" id="severity" name="severity">
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                            <option value="critical">Critical</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="return_reason" class="form-label">Detailed Return Reason *</label>
                                <textarea class="form-control" id="return_reason" name="return_reason" rows="3" 
                                          placeholder="Describe the reason for return in detail..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="corrective_action" class="form-label">Corrective Action Required</label>
                                <textarea class="form-control" id="corrective_action" name="corrective_action" rows="2" 
                                          placeholder="What action is expected from the supplier?"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-sticky-note me-2"></i>
                            Additional Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="return_method" class="form-label">Return Method</label>
                                    <select class="form-select" id="return_method" name="return_method">
                                        <option value="pickup">Supplier Pickup</option>
                                        <option value="courier">Courier Return</option>
                                        <option value="replacement">Direct Replacement</option>
                                        <option value="credit">Credit Note</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_resolution_date" class="form-label">Expected Resolution Date</label>
                                    <input type="date" class="form-control" id="expected_resolution_date" name="expected_resolution_date" 
                                           value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information or special instructions..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h5 class="mb-3">
                            <i class="fas fa-calculator me-2"></i>
                            Debit Note Summary
                        </h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total Items:</span>
                                <span class="summary-value" id="totalItems">0</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total Quantity:</span>
                                <span class="summary-value" id="totalQuantity">0</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total Value:</span>
                                <span class="summary-value" id="totalValue">₹0.00</span>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn-create">
                                <i class="fas fa-save me-2"></i>Create Debit Note
                            </button>
                            <a href="/purchase/debit-notes" class="btn btn-outline-light">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
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
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            // Add first item row
            addItemRow();
            
            // Update summary when items change
            updateSummary();
        });

        let itemCounter = 0;

        function addItemRow() {
            itemCounter++;
            const itemRow = `
                <div class="item-row" id="item_${itemCounter}">
                    <button type="button" class="remove-item" onclick="removeItem(${itemCounter})">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Item *</label>
                                <select class="form-select" name="items[]" required onchange="updateItemDetails(${itemCounter})">
                                    <option value="">Select Item</option>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= $item['id'] ?>" 
                                                data-price="<?= isset($item['standard_cost']) ? $item['standard_cost'] : 0 ?>"
                                                data-uom="<?= isset($item['uom']) ? $item['uom'] : 'PCS' ?>">
                                            <?= esc($item['item_name']) ?> (<?= esc($item['item_code']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">UOM</label>
                                <input type="text" class="form-control" name="uoms[]" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantities[]" 
                                       step="0.01" min="0.01" required onchange="updateItemTotal(${itemCounter})">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Unit Price</label>
                                <input type="number" class="form-control" name="unit_prices[]" 
                                       step="0.01" min="0" onchange="updateItemTotal(${itemCounter})">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Total</label>
                                <input type="number" class="form-control" name="totals[]" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Batch Number</label>
                                <input type="text" class="form-control" name="batch_numbers[]" 
                                       placeholder="Batch/Lot number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Item Notes</label>
                                <input type="text" class="form-control" name="item_notes[]" 
                                       placeholder="Item-specific notes">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', itemRow);
        }

        function removeItem(itemId) {
            const itemElement = document.getElementById(`item_${itemId}`);
            if (itemElement) {
                itemElement.remove();
                updateSummary();
            }
        }

        function updateItemDetails(itemId) {
            const itemElement = document.getElementById(`item_${itemId}`);
            const itemSelect = itemElement.querySelector('select[name="items[]"]');
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            
            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                const uom = selectedOption.getAttribute('data-uom');
                
                itemElement.querySelector('input[name="unit_prices[]"]').value = price;
                itemElement.querySelector('input[name="uoms[]"]').value = uom;
                
                updateItemTotal(itemId);
            }
        }

        function updateItemTotal(itemId) {
            const itemElement = document.getElementById(`item_${itemId}`);
            const quantity = parseFloat(itemElement.querySelector('input[name="quantities[]"]').value) || 0;
            const unitPrice = parseFloat(itemElement.querySelector('input[name="unit_prices[]"]').value) || 0;
            const total = quantity * unitPrice;
            
            itemElement.querySelector('input[name="totals[]"]').value = total.toFixed(2);
            updateSummary();
        }

        function updateSummary() {
            let totalItems = 0;
            let totalQuantity = 0;
            let totalValue = 0;
            
            document.querySelectorAll('.item-row').forEach(row => {
                totalItems++;
                const quantity = parseFloat(row.querySelector('input[name="quantities[]"]').value) || 0;
                const total = parseFloat(row.querySelector('input[name="totals[]"]').value) || 0;
                
                totalQuantity += quantity;
                totalValue += total;
            });
            
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalQuantity').textContent = totalQuantity;
            document.getElementById('totalValue').textContent = '₹' + totalValue.toFixed(2);
        }

        // Form validation
        document.getElementById('debitNoteForm').addEventListener('submit', function(e) {
            const items = document.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the debit note.');
                return false;
            }
            
            const returnReason = document.getElementById('return_reason').value.trim();
            if (!returnReason) {
                e.preventDefault();
                alert('Please provide a detailed return reason.');
                return false;
            }
        });

        // Auto-populate supplier when GRN or PO is selected
        document.getElementById('grn_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const supplierId = selectedOption.getAttribute('data-supplier');
                document.getElementById('supplier_id').value = supplierId;
                $('#supplier_id').trigger('change');
            }
        });

        document.getElementById('purchase_order_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const supplierId = selectedOption.getAttribute('data-supplier');
                document.getElementById('supplier_id').value = supplierId;
                $('#supplier_id').trigger('change');
            }
        });
    </script>
</body>
</html>
