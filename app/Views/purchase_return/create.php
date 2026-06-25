<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Create Purchase Return</h1>
        <p class="text-muted mb-0">Create a new purchase return</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-return') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Purchase Returns
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="form-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-plus me-2"></i>Purchase Return Information
        </h5>
    </div>
    <div class="card-body">
        <form data-validate action="<?= base_url('purchase-return/store') ?>" method="POST" id="purchaseReturnForm">
            <?= csrf_field() ?>
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Basic Information</h6>
                    
                    <div class="form-group">
                        <label for="return_number" class="form-label mandatory">Return Number</label>
                        <input type="text" class="form-control" id="return_number" name="return_number" 
                               value="<?= isset($return_number) ? $return_number : '' ?>" readonly>
                        <div class="form-text">Auto-generated return number</div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id" class="form-label mandatory">Supplier</label>
                        <select class="form-control" id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach (isset($suppliers) ? $suppliers : [] as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>" 
                                        data-name="<?= esc($supplier['supplier_name']) ?>"
                                        data-contact="<?= esc($supplier['contact_person'] ?? '') ?>"
                                        data-phone="<?= esc($supplier['phone'] ?? '') ?>"
                                        data-email="<?= esc($supplier['email'] ?? '') ?>"
                                        data-address="<?= esc($supplier['address'] ?? '') ?>">
                                    <?= esc($supplier['supplier_name']) ?> - <?= esc($supplier['supplier_code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a supplier.</div>
                    </div>

                    <div class="form-group">
                        <label for="purchase_order_id" class="form-label">Original Purchase Order (Optional)</label>
                        <select class="form-control" id="purchase_order_id" name="purchase_order_id">
                            <option value="">Select Purchase Order (Optional)</option>
                            <?php foreach (isset($purchase_orders) ? $purchase_orders : [] as $po): ?>
                                <option value="<?= $po['id'] ?>" 
                                        data-supplier-id="<?= $po['supplier_id'] ?>"
                                        data-po-number="<?= esc($po['po_number']) ?>">
                                    <?= esc($po['po_number']) ?> - <?= esc($po['supplier_name']) ?> (<?= date('d/m/Y', strtotime($po['order_date'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Select a purchase order to auto-load items, or add items manually below.</div>
                    </div>

                    <div class="form-group">
                        <label for="return_date" class="form-label mandatory">Return Date</label>
                        <input type="date" class="form-control" id="return_date" name="return_date" 
                               value="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Return date is required.</div>
                    </div>

                    <div class="form-group">
                        <label for="return_reason" class="form-label mandatory">Return Reason</label>
                        <select class="form-control" id="return_reason" name="return_reason" required>
                            <option value="">Select Reason</option>
                            <option value="damaged">Damaged Goods</option>
                            <option value="defective">Defective Products</option>
                            <option value="wrong_item">Wrong Item Received</option>
                            <option value="quality_issue">Quality Issues</option>
                            <option value="expired">Expired Products</option>
                            <option value="overstock">Overstock</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select a return reason.</div>
                    </div>

                    <div class="form-group">
                        <label for="return_method" class="form-label">Return Method</label>
                        <select class="form-control" id="return_method" name="return_method">
                            <option value="">Select Method</option>
                            <option value="pickup">Supplier Pickup</option>
                            <option value="delivery">Delivery to Supplier</option>
                            <option value="courier">Courier Service</option>
                        </select>
                    </div>
                </div>

                <!-- Supplier Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Supplier Information</h6>
                    
                    <div class="form-group">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="supplier_name" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="supplier_phone" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="supplier_email" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="supplier_address" rows="3" readonly></textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_urgent" name="is_urgent" value="1">
                            <label class="form-check-label" for="is_urgent">
                                Mark as Urgent
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Items Section -->
            <div class="row">
                <div class="col-12">
                    <h6 class="mb-3 text-primary">Return Items</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th width="25%">Product</th>
                                    <th width="15%">Original Qty</th>
                                    <th width="15%">Return Qty</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="15%">Total</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Items will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="addItemBtn" onclick="addItemRow()">
                            <i class="fas fa-plus me-2"></i>Add Item
                        </button>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        You can either select a purchase order to auto-load items, or manually add items using the "Add Item" button above.
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Summary Section -->
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Additional Information</h6>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Enter any additional notes about the return..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="return_instructions" class="form-label">Return Instructions</label>
                        <textarea class="form-control" id="return_instructions" name="return_instructions" rows="3" 
                                  placeholder="Enter specific instructions for the return..."></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Return Summary</h6>
                    
                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (18%):</span>
                            <span id="tax">₹0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Restocking Fee:</span>
                            <span id="restocking_fee">₹0.00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="total">₹0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('purchase-return') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save me-2"></i>Create Purchase Return
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let originalItems = [];
let itemCounter = 0;
let products = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load products
    loadProducts();
    
    // Add event listeners
    document.getElementById('purchase_order_id').addEventListener('change', loadPurchaseOrderItems);
    document.getElementById('supplier_id').addEventListener('change', updateSupplierInfo);
    
    // Form validation
    const form = document.getElementById('purchaseReturnForm');
    form.addEventListener('submit', validateForm);
});

function loadProducts() {
    fetch('<?= base_url('purchase-return/get-products') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                products = data.products;
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
        });
}

function updateSupplierInfo() {
    const supplierSelect = document.getElementById('supplier_id');
    const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
    
    if (!selectedOption.value) {
        clearSupplierInfo();
        return;
    }
    
    document.getElementById('supplier_name').value = selectedOption.dataset.name || '';
    document.getElementById('contact_person').value = selectedOption.dataset.contact || '';
    document.getElementById('supplier_phone').value = selectedOption.dataset.phone || '';
    document.getElementById('supplier_email').value = selectedOption.dataset.email || '';
    document.getElementById('supplier_address').value = selectedOption.dataset.address || '';
}

function loadPurchaseOrderItems() {
    const poSelect = document.getElementById('purchase_order_id');
    const selectedOption = poSelect.options[poSelect.selectedIndex];
    
    if (!selectedOption.value) {
        clearSupplierInfo();
        clearItemsTable();
        return;
    }

    // Update supplier information
    updateSupplierInfo(selectedOption);
    
    // Load items from the selected purchase order
    fetch(`<?= base_url('purchase-return/get-po-items') ?>/${selectedOption.value}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                originalItems = data.items;
                populateItemsTable();
            } else {
                alert('Error loading purchase order items: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading purchase order items.');
        });
}

function updateSupplierInfo(selectedOption) {
    // This would typically fetch supplier details from the server
    // For now, we'll use the data attributes
    document.getElementById('supplier_name').value = selectedOption.dataset.supplier || '';
    // You would populate other supplier fields here
}

function clearSupplierInfo() {
    document.getElementById('supplier_name').value = '';
    document.getElementById('contact_person').value = '';
    document.getElementById('supplier_phone').value = '';
    document.getElementById('supplier_email').value = '';
    document.getElementById('supplier_address').value = '';
}

function populateItemsTable() {
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    itemCounter = 0;
    
    originalItems.forEach(function(item, index) {
        addItemToTable(item, item.original_quantity || 0);
    });
    
    calculateTotals();
}

function addItemRow() {
    addItemToTable(null, 0);
}

function addItemToTable(item = null, originalQty = 0) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.id = `item_row_${itemCounter}`;
    
    if (item) {
        // Item from purchase order
        row.innerHTML = `
            <td>
                <strong>${item.product_name}</strong>
                <br>
                <small class="text-muted">${item.product_code}</small>
                <input type="hidden" name="items[${itemCounter}][product_id]" value="${item.product_id}">
            </td>
            <td>${originalQty}</td>
            <td>
                <input type="number" class="form-control return-quantity" 
                       name="items[${itemCounter}][return_quantity]" 
                       min="0" max="${originalQty}" 
                       value="0" onchange="calculateItemTotal(this)">
                <input type="hidden" name="items[${itemCounter}][original_quantity]" value="${originalQty}">
            </td>
            <td>₹${parseFloat(item.unit_price).toFixed(2)}</td>
            <td>
                <input type="text" class="form-control item-total" readonly value="₹0.00">
                <input type="hidden" name="items[${itemCounter}][unit_price]" value="${item.unit_price}">
            </td>
            <td>
                <button type="button" class="btn btn-outline-secondary btn-sm" 
                        onclick="returnAllItems(${itemCounter}, ${originalQty})">
                    Return All
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm mt-1" 
                        onclick="removeItem(${itemCounter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
    } else {
        // Manual item addition
        let productOptions = '<option value="">Select Product</option>';
        products.forEach(function(product) {
            productOptions += `<option value="${product.id}" data-price="${product.unit_price || 0}">${product.product_code} - ${product.product_name}</option>`;
        });
        
        row.innerHTML = `
            <td>
                <select class="form-control product-select" name="items[${itemCounter}][product_id]" required onchange="updateProductInfo(this)">
                    ${productOptions}
                </select>
                <input type="hidden" name="items[${itemCounter}][original_quantity]" value="0">
            </td>
            <td>-</td>
            <td>
                <input type="number" class="form-control return-quantity" 
                       name="items[${itemCounter}][return_quantity]" 
                       min="0" step="0.01"
                       value="0" onchange="calculateItemTotal(this)" required>
            </td>
            <td>
                <input type="number" class="form-control unit-price-input" 
                       name="items[${itemCounter}][unit_price]" 
                       min="0" step="0.01"
                       value="0" onchange="calculateItemTotal(this)" required>
            </td>
            <td>
                <input type="text" class="form-control item-total" readonly value="₹0.00">
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm" 
                        onclick="removeItem(${itemCounter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
    }
    
    tbody.appendChild(row);
    itemCounter++;
}

function updateProductInfo(select) {
    const row = select.closest('tr');
    const selectedOption = select.options[select.selectedIndex];
    const unitPrice = parseFloat(selectedOption.dataset.price) || 0;
    
    const priceInput = row.querySelector('.unit-price-input');
    if (priceInput) {
        priceInput.value = unitPrice;
        calculateItemTotal(priceInput);
    }
}

function removeItem(index) {
    const row = document.getElementById(`item_row_${index}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function clearItemsTable() {
    document.getElementById('itemsTableBody').innerHTML = '';
    originalItems = [];
    calculateTotals();
}

function returnAllItems(index, originalQuantity) {
    const quantityInput = document.querySelector(`input[name="items[${index}][return_quantity]"]`);
    quantityInput.value = originalQuantity;
    calculateItemTotal(quantityInput);
}

function calculateItemTotal(input) {
    const row = input.closest('tr');
    const quantity = parseFloat(input.value) || 0;
    const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
    const total = quantity * unitPrice;
    
    row.querySelector('.item-total').value = '₹' + total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.item-total').forEach(function(input) {
        const value = input.value.replace('₹', '');
        subtotal += parseFloat(value) || 0;
    });
    
    const tax = subtotal * 0.18; // 18% tax
    const restockingFee = subtotal * 0.05; // 5% restocking fee
    const total = subtotal + tax + restockingFee;
    
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '₹' + tax.toFixed(2);
    document.getElementById('restocking_fee').textContent = '₹' + restockingFee.toFixed(2);
    document.getElementById('total').textContent = '₹' + total.toFixed(2);
}

function validateForm(e) {
    const requiredFields = document.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Check if at least one item has return quantity
    const returnQuantities = document.querySelectorAll('.return-quantity');
    let hasReturns = false;
    
    returnQuantities.forEach(function(input) {
        if (parseFloat(input.value) > 0) {
            hasReturns = true;
        }
    });
    
    if (!hasReturns) {
        isValid = false;
        alert('Please specify return quantities for at least one item.');
    }
    
    if (!isValid) {
        e.preventDefault();
        return false;
    }
}
</script>

<style>
.summary-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #dee2e6;
}

.summary-row.total {
    font-weight: bold;
    font-size: 1.1em;
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.return-quantity {
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
<?= $this->endSection() ?>
