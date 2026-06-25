<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Edit Purchase Order</h1>
        <p class="text-muted mb-0">Modify purchase order details</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-order') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Purchase Orders
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="form-card">
    <div class="card-header">
        <h5 class="h5">
            <i class="fas fa-edit me-2"></i>Edit Purchase Order
        </h5>
    </div>
    <div class="card-body">
        <form data-validate action="<?= base_url('purchase-order/update/' . $purchase_order['id']) ?>" method="POST" id="purchaseOrderForm">
            <?= csrf_field() ?>
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Basic Information</h6>
                    
                    <div class="form-group">
                        <label for="po_number" class="form-label mandatory">PO Number</label>
                        <input type="text" class="form-control" id="po_number" name="po_number" 
                               value="<?= esc($purchase_order['po_number']) ?>" readonly>
                        <div class="form-text">PO number cannot be changed</div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id" class="form-label mandatory">Supplier</label>
                        <select class="form-control" id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach (isset($suppliers) ? $suppliers : [] as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>" 
                                        <?= $purchase_order['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>
                                        data-contact="<?= esc($supplier['contact_person']) ?>"
                                        data-phone="<?= esc($supplier['phone']) ?>"
                                        data-email="<?= esc($supplier['email']) ?>"
                                        data-address="<?= esc($supplier['address']) ?>">
                                    <?= esc($supplier['supplier_name']) ?> - <?= esc($supplier['supplier_code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a supplier.</div>
                    </div>

                    <div class="form-group">
                        <label for="order_date" class="form-label mandatory">Order Date</label>
                        <input type="date" class="form-control" id="order_date" name="order_date" 
                               value="<?= $purchase_order['order_date'] ?>" required>
                        <div class="invalid-feedback">Order date is required.</div>
                    </div>

                    <div class="form-group">
                        <label for="expected_date" class="form-label mandatory">Expected Delivery Date</label>
                        <input type="date" class="form-control" id="expected_date" name="expected_date" 
                               value="<?= $purchase_order['expected_date'] ?>" required>
                        <div class="invalid-feedback">Expected delivery date is required.</div>
                    </div>

                    <div class="form-group">
                        <label for="delivery_address" class="form-label">Delivery Address</label>
                        <textarea class="form-control" id="delivery_address" name="delivery_address" 
                                  rows="3" placeholder="Enter delivery address"><?= esc(isset($purchase_order['delivery_address']) ? $purchase_order['delivery_address'] : '') ?></textarea>
                    </div>
                </div>

                <!-- Supplier Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Supplier Information</h6>
                    
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
                        <label for="payment_terms" class="form-label">Payment Terms</label>
                        <select class="form-control" id="payment_terms" name="payment_terms">
                            <option value="">Select Payment Terms</option>
                            <option value="immediate" <?= (isset($purchase_order['payment_terms']) ? $purchase_order['payment_terms'] : '') === 'immediate' ? 'selected' : '' ?>>Immediate</option>
                            <option value="7_days" <?= (isset($purchase_order['payment_terms']) ? $purchase_order['payment_terms'] : '') === '7_days' ? 'selected' : '' ?>>7 Days</option>
                            <option value="15_days" <?= (isset($purchase_order['payment_terms']) ? $purchase_order['payment_terms'] : '') === '15_days' ? 'selected' : '' ?>>15 Days</option>
                            <option value="30_days" <?= (isset($purchase_order['payment_terms']) ? $purchase_order['payment_terms'] : '') === '30_days' ? 'selected' : '' ?>>30 Days</option>
                            <option value="45_days" <?= (isset($purchase_order['payment_terms']) ? $purchase_order['payment_terms'] : '') === '45_days' ? 'selected' : '' ?>>45 Days</option>
                            <option value="60_days" <?= (isset($purchase_order['payment_terms']) ? $purchase_order['payment_terms'] : '') === '60_days' ? 'selected' : '' ?>>60 Days</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_urgent" name="is_urgent" value="1"
                                   <?= (isset($purchase_order['is_urgent']) ? $purchase_order['is_urgent'] : 0) ? 'checked' : '' ?>>
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
                    <h6 class="mb-3 text-primary">Order Items</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th width="30%">Product</th>
                                    <th width="15%">Quantity</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="15%">Total</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Items will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem()">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </button>
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
                                  placeholder="Enter any additional notes..."><?= esc(isset($purchase_order['notes']) ? $purchase_order['notes'] : '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                        <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3" 
                                  placeholder="Enter terms and conditions..."><?= esc(isset($purchase_order['terms_conditions']) ? $purchase_order['terms_conditions'] : '') ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">Order Summary</h6>
                    
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
                            <span>Discount:</span>
                            <span id="discount">₹0.00</span>
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
                <a href="<?= base_url('purchase-order') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save me-2"></i>Update Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let itemCounter = 0;
let existingItems = <?= json_encode(isset($purchase_order['items']) ? $purchase_order['items'] : []) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize supplier information
    updateSupplierInfo();
    
    // Add event listeners
    document.getElementById('supplier_id').addEventListener('change', updateSupplierInfo);
    
    // Form validation
    const form = document.getElementById('purchaseOrderForm');
    form.addEventListener('submit', validateForm);
    
    // Load existing items
    loadExistingItems();
});

function updateSupplierInfo() {
    const supplierSelect = document.getElementById('supplier_id');
    const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('contact_person').value = selectedOption.dataset.contact || '';
        document.getElementById('supplier_phone').value = selectedOption.dataset.phone || '';
        document.getElementById('supplier_email').value = selectedOption.dataset.email || '';
        document.getElementById('supplier_address').value = selectedOption.dataset.address || '';
    } else {
        document.getElementById('contact_person').value = '';
        document.getElementById('supplier_phone').value = '';
        document.getElementById('supplier_email').value = '';
        document.getElementById('supplier_address').value = '';
    }
}

function loadExistingItems() {
    existingItems.forEach(function(item) {
        addItem(item);
    });
}

function addItem(existingItem = null) {
    itemCounter++;
    const tbody = document.getElementById('itemsTableBody');
    
    const row = document.createElement('tr');
    row.id = `item_${itemCounter}`;
    row.innerHTML = `
        <td>
            <select class="form-control product-select" name="items[${itemCounter}][product_id]" required>
                <option value="">Select Product</option>
                <?php foreach (isset($products) ? $products : [] as $product): ?>
                    <option value="<?= $product['id'] ?>" 
                            data-price="<?= isset($product['purchase_price']) ? $product['purchase_price'] : 0 ?>"
                            data-stock="<?= isset($product['current_stock']) ? $product['current_stock'] : 0 ?>">
                        <?= esc($product['product_name']) ?> - <?= esc($product['product_code']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a product.</div>
        </td>
        <td>
            <input type="number" class="form-control quantity-input" 
                   name="items[${itemCounter}][quantity]" 
                   min="1" step="1" placeholder="Qty" required>
            <div class="invalid-feedback">Quantity is required.</div>
        </td>
        <td>
            <input type="number" class="form-control price-input" 
                   name="items[${itemCounter}][unit_price]" 
                   min="0" step="0.01" placeholder="Price" required>
            <div class="invalid-feedback">Unit price is required.</div>
        </td>
        <td>
            <input type="text" class="form-control total-input" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(${itemCounter})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Add event listeners to new row
    const productSelect = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const priceInput = row.querySelector('.price-input');
    
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            priceInput.value = selectedOption.dataset.price || 0;
            calculateItemTotal(row);
        }
    });
    
    quantityInput.addEventListener('input', function() {
        calculateItemTotal(row);
    });
    
    priceInput.addEventListener('input', function() {
        calculateItemTotal(row);
    });
    
    // Set existing values if editing
    if (existingItem) {
        productSelect.value = existingItem.product_id;
        quantityInput.value = existingItem.quantity;
        priceInput.value = existingItem.unit_price;
        calculateItemTotal(row);
    }
}

function removeItem(counter) {
    const row = document.getElementById(`item_${counter}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function calculateItemTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    
    row.querySelector('.total-input').value = total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.total-input').forEach(function(input) {
        subtotal += parseFloat(input.value) || 0;
    });
    
    const tax = subtotal * 0.18; // 18% tax
    const discount = 0; // Can be made dynamic
    
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '₹' + tax.toFixed(2);
    document.getElementById('discount').textContent = '₹' + discount.toFixed(2);
    document.getElementById('total').textContent = '₹' + (subtotal + tax - discount).toFixed(2);
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
    
    // Check if at least one item is added
    const items = document.querySelectorAll('.product-select');
    let hasItems = false;
    
    items.forEach(function(item) {
        if (item.value) {
            hasItems = true;
        }
    });
    
    if (!hasItems) {
        isValid = false;
        alert('Please add at least one item to the purchase order.');
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

.product-select, .quantity-input, .price-input {
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
