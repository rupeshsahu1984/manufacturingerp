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
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }
        
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-section h5 {
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .items-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background: #495057;
            color: white;
            border: none;
            padding: 15px;
        }
        
        .items-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .btn-remove-item {
            background: #dc3545;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        
        .btn-remove-item:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .item-row {
            transition: all 0.3s ease;
        }
        
        .item-row:hover {
            background-color: #f8f9fa;
        }
        
        .total-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .total-amount {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="h2 mb-2">
                <i class="fas fa-clipboard-plus me-3"></i>
                Create Purchase Requisition
            </h1>
            <p class="mb-0">Request materials, tools, or services for your department</p>
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
                    <a href="/purchase/requisitions" class="text-decoration-none">Purchase Requisitions</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Create New</li>
            </ol>
        </nav>

        <!-- Form Container -->
        <div class="form-container">
            <form id="requisitionForm" action="/purchase/requisitions/store" method="POST">
                <!-- Basic Information -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-info-circle me-2"></i>
                        Requisition Details
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label required-field">Department</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept ?>"><?= $dept ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" id="department_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="requested_by" class="form-label required-field">Requested By</label>
                            <input type="text" class="form-control" id="requested_by" name="requested_by" 
                                   required placeholder="Enter your name">
                            <div class="error-message" id="requested_by_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <?php foreach ($priorities as $priority): ?>
                                    <option value="<?= $priority ?>" <?= $priority === 'normal' ? 'selected' : '' ?>>
                                        <?= ucfirst($priority) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reason" class="form-label">Reason/Justification</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" 
                                      placeholder="Explain why this requisition is needed..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-boxes me-2"></i>
                        Items Required
                    </h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="item_select" class="form-label">Select Item</label>
                            <select class="form-select" id="item_select">
                                <option value="">Choose an item...</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-code="<?= esc($item['item_code']) ?>"
                                            data-name="<?= esc($item['item_name']) ?>"
                                            data-uom="<?= esc($item['uom']) ?>"
                                            data-cost="<?= isset($item['standard_cost']) ? $item['standard_cost'] : 0 ?>">
                                        <?= esc($item['item_code']) ?> - <?= esc($item['item_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="item_quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="item_quantity" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <label for="item_required_date" class="form-label">Required Date</label>
                            <input type="date" class="form-control" id="item_required_date">
                        </div>
                        <div class="col-md-2">
                            <label for="item_priority" class="form-label">Priority</label>
                            <select class="form-select" id="item_priority">
                                <?php foreach ($priorities as $priority): ?>
                                    <option value="<?= $priority ?>" <?= $priority === 'normal' ? 'selected' : '' ?>>
                                        <?= ucfirst($priority) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-success w-100" onclick="addItem()">
                                <i class="fas fa-plus me-2"></i>Add
                            </button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="items-table">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>Required Date</th>
                                    <th>Priority</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Items will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3" id="noItemsMessage">
                        <p class="text-muted">
                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                            No items added yet. Use the form above to add items.
                        </p>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="total-section">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Total Items: <span id="totalItems">0</span></h6>
                            <h6>Estimated Value: <span id="estimatedValue">₹0.00</span></h6>
                        </div>
                        <div class="col-md-6">
                            <div class="total-amount" id="totalAmount">₹0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between align-items-center pt-4">
                    <a href="/purchase/requisitions" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Requisitions
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-submit" id="submitBtn" disabled>
                            <i class="fas fa-save me-2"></i>Create Requisition
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let items = [];
        let itemCounter = 0;
        
        // Add item to the requisition
        function addItem() {
            const itemSelect = document.getElementById('item_select');
            const quantity = document.getElementById('item_quantity').value;
            const requiredDate = document.getElementById('item_required_date').value;
            const priority = document.getElementById('item_priority').value;
            
            if (!itemSelect.value || !quantity || quantity < 1) {
                alert('Please select an item and enter a valid quantity');
                return;
            }
            
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            const itemData = {
                id: itemSelect.value,
                item_code: selectedOption.dataset.code,
                item_name: selectedOption.dataset.name,
                uom: selectedOption.dataset.uom,
                quantity: parseInt(quantity),
                required_date: requiredDate,
                priority: priority,
                cost: parseFloat(selectedOption.dataset.cost) || 0
            };
            
            items.push(itemData);
            itemCounter++;
            
            // Add to table
            addItemToTable(itemData, itemCounter);
            
            // Update totals
            updateTotals();
            
            // Reset form
            itemSelect.value = '';
            document.getElementById('item_quantity').value = '1';
            document.getElementById('item_required_date').value = '';
            
            // Show/hide messages
            document.getElementById('noItemsMessage').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
        }
        
        // Add item row to table
        function addItemToTable(itemData, counter) {
            const tbody = document.getElementById('itemsTableBody');
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.id = `item_${counter}`;
            
            row.innerHTML = `
                <td><strong>${itemData.item_code}</strong></td>
                <td>${itemData.item_name}</td>
                <td>${itemData.quantity}</td>
                <td>${itemData.uom}</td>
                <td>${itemData.required_date || 'Not specified'}</td>
                <td>
                    <span class="badge bg-${getPriorityColor(itemData.priority)}">
                        ${itemData.priority.charAt(0).toUpperCase() + itemData.priority.slice(1)}
                    </span>
                </td>
                <td>
                    <button type="button" class="btn-remove-item" onclick="removeItem(${counter})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(row);
        }
        
        // Remove item from requisition
        function removeItem(counter) {
            const row = document.getElementById(`item_${counter}`);
            if (row) {
                row.remove();
                
                // Remove from items array
                const index = items.findIndex(item => item.counter === counter);
                if (index > -1) {
                    items.splice(index, 1);
                }
                
                updateTotals();
                
                // Show no items message if empty
                if (items.length === 0) {
                    document.getElementById('noItemsMessage').style.display = 'block';
                    document.getElementById('submitBtn').disabled = true;
                }
            }
        }
        
        // Update totals
        function updateTotals() {
            const totalItems = items.length;
            const estimatedValue = items.reduce((sum, item) => sum + (item.quantity * item.cost), 0);
            
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('estimatedValue').textContent = `₹${estimatedValue.toFixed(2)}`;
            document.getElementById('totalAmount').textContent = `₹${estimatedValue.toFixed(2)}`;
        }
        
        // Get priority color
        function getPriorityColor(priority) {
            switch (priority) {
                case 'low': return 'info';
                case 'normal': return 'success';
                case 'high': return 'warning';
                case 'urgent': return 'danger';
                default: return 'secondary';
            }
        }
        
        // Form validation
        document.getElementById('requisitionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearErrors();
            
            // Validate required fields
            let isValid = true;
            
            const requiredFields = ['department', 'requested_by'];
            requiredFields.forEach(field => {
                const value = document.getElementById(field).value.trim();
                if (!value) {
                    showError(field, 'This field is required');
                    isValid = false;
                }
            });
            
            // Validate items
            if (items.length === 0) {
                alert('Please add at least one item to the requisition');
                isValid = false;
            }
            
            if (isValid) {
                // Add hidden inputs for items
                items.forEach((item, index) => {
                    const itemInput = document.createElement('input');
                    itemInput.type = 'hidden';
                    itemInput.name = `items[${index}]`;
                    itemInput.value = item.id;
                    this.appendChild(itemInput);
                    
                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `quantities[${index}]`;
                    qtyInput.value = item.quantity;
                    this.appendChild(qtyInput);
                    
                    const dateInput = document.createElement('input');
                    dateInput.type = 'hidden';
                    dateInput.name = `required_dates[${index}]`;
                    dateInput.value = item.required_date;
                    this.appendChild(dateInput);
                    
                    const priorityInput = document.createElement('input');
                    priorityInput.type = 'hidden';
                    priorityInput.name = `priorities[${index}]`;
                    priorityInput.value = item.priority;
                    this.appendChild(priorityInput);
                });
                
                // Submit form
                this.submit();
            }
        });
        
        // Validation helper functions
        function showError(fieldId, message) {
            const errorDiv = document.getElementById(fieldId + '_error');
            if (errorDiv) {
                errorDiv.textContent = message;
                document.getElementById(fieldId).classList.add('is-invalid');
            }
        }
        
        function clearErrors() {
            const errorDivs = document.querySelectorAll('.error-message');
            errorDivs.forEach(div => div.textContent = '');
            
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => input.classList.remove('is-invalid'));
        }
        
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                document.getElementById('requisitionForm').reset();
                clearErrors();
                
                // Clear items
                items = [];
                document.getElementById('itemsTableBody').innerHTML = '';
                document.getElementById('noItemsMessage').style.display = 'block';
                document.getElementById('submitBtn').disabled = true;
                updateTotals();
            }
        }
        
        // Set default required date to tomorrow
        document.addEventListener('DOMContentLoaded', function() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('item_required_date').value = tomorrow.toISOString().split('T')[0];
        });
        
        // Auto-fill requested by with current user (if available)
        if (typeof currentUser !== 'undefined' && currentUser.name) {
            document.getElementById('requested_by').value = currentUser.name;
        }
    </script>
</body>
</html>
