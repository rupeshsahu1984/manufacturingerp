<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sales-order') ?>">Sales Orders</a></li>
                        <li class="breadcrumb-item active">Edit Sales Order</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Sales Order #<?= esc($sales_order['so_number']) ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Edit Sales Order</h4>
                </div>
                <div class="card-body">
                    <form id="salesOrderForm" action="<?= base_url('sales-order/update/' . $sales_order['id']) ?>" method="POST">
                        <div class="row">
                            <!-- Invoice and Customer Details -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoice_no" class="form-label">Invoice No</label>
                                    <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?= esc(isset($sales_order['invoice_no']) ? $sales_order['invoice_no'] : $sales_order['so_number']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="order_date" name="order_date" value="<?= $sales_order['order_date'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customers Name</label>
                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Select</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>" <?= $sales_order['customer_id'] == $customer['id'] ? 'selected' : '' ?>>
                                                <?= esc($customer['customer_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">ENTER ADDRESS</label>
                                    <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="ENTER ADDRESS" value="<?= esc(isset($sales_order['customer_address']) ? $sales_order['customer_address'] : '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_mobile" class="form-label">ENTER MNO</label>
                                    <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" placeholder="ENTER MNO" value="<?= esc(isset($sales_order['customer_mobile']) ? $sales_order['customer_mobile'] : '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_gstn" class="form-label">ENTER GSTN</label>
                                    <input type="text" class="form-control" id="customer_gstn" name="customer_gstn" placeholder="ENTER GSTN" value="<?= esc(isset($sales_order['customer_gstn']) ? $sales_order['customer_gstn'] : '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">Product Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="orderItemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product name</th>
                                                <th>Unit</th>
                                                <th>Price</th>
                                                <th>Discount</th>
                                                <th>Billed Qty</th>
                                                <th>Total</th>
                                                <th>CGST</th>
                                                <th>SGST</th>
                                                <th>IGST</th>
                                                <th>Tax</th>
                                                <th>Ship Qty</th>
                                                <th>Avail Stk</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderItemsBody">
                                            <!-- Order items will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                                    <i class="fas fa-plus"></i> Add/Rem
                                </button>
                            </div>
                        </div>

                        <!-- Transport Details -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="transport_amount" class="form-label">Transport Amount</label>
                                    <input type="number" class="form-control" id="transport_amount" name="transport_amount" step="0.01" min="0" value="<?= isset($sales_order['transport_amount']) ? $sales_order['transport_amount'] : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="transport_tax" class="form-label">Transport Tax</label>
                                    <input type="number" class="form-control" id="transport_tax" name="transport_tax" step="0.01" min="0" value="<?= isset($sales_order['transport_tax']) ? $sales_order['transport_tax'] : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?= esc(isset($sales_order['description']) ? $sales_order['description'] : '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Order Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-6">Subtotal:</div>
                                            <div class="col-6 text-end" id="subtotal">₹0.00</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">Discount Total:</div>
                                            <div class="col-6 text-end" id="discountTotal">₹0.00</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">CGST Total:</div>
                                            <div class="col-6 text-end" id="cgstTotal">₹0.00</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">SGST Total:</div>
                                            <div class="col-6 text-end" id="sgstTotal">₹0.00</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">IGST Total:</div>
                                            <div class="col-6 text-end" id="igstTotal">₹0.00</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">Transport Amount:</div>
                                            <div class="col-6 text-end" id="transportAmountDisplay">₹0.00</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">Transport Tax:</div>
                                            <div class="col-6 text-end" id="transportTaxDisplay">₹0.00</div>
                                        </div>
                                        <hr>
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Final Total:</strong></div>
                                            <div class="col-6 text-end"><strong id="finalTotal">₹0.00</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Sales Order</button>
                                <a href="<?= base_url('sales-order/show/' . $sales_order['id']) ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden template for order items -->
<template id="orderItemTemplate">
    <tr class="order-item">
        <td>
            <select class="form-select product-select" name="items[INDEX][product_id]" required onchange="updateProductInfo(this, INDEX)">
                <option value="">Select Item</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control" name="items[INDEX][unit]" placeholder="Un" value="Un" readonly>
        </td>
        <td>
            <input type="number" class="form-control unit-price" name="items[INDEX][unit_price]" step="0.01" min="0" required onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control discount" name="items[INDEX][discount]" step="0.01" min="0" value="0" onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control quantity" name="items[INDEX][quantity]" min="1" required onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control line-total" name="items[INDEX][line_total]" step="0.01" readonly>
        </td>
        <td>
            <input type="number" class="form-control cgst" name="items[INDEX][cgst]" step="0.01" min="0" value="0" onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control sgst" name="items[INDEX][sgst]" step="0.01" min="0" value="0" onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control igst" name="items[INDEX][igst]" step="0.01" min="0" value="0" onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control tax-amount" name="items[INDEX][tax_amount]" step="0.01" readonly>
        </td>
        <td>
            <input type="number" class="form-control ship-qty" name="items[INDEX][ship_qty]" min="1" value="1" onchange="calculateLineTotal(INDEX)">
        </td>
        <td>
            <input type="number" class="form-control available-stock" name="items[INDEX][available_stock]" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
let itemIndex = 0;
let products = [];
let customers = <?= json_encode($customers) ?>;
let existingItems = <?= json_encode(isset($order_items) ? $order_items : []) ?>;

// Load products and customers on page load
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadExistingItems();
    calculateTotals();
});

function loadProducts() {
    fetch('<?= base_url('sales-order/get-products') ?>')
        .then(response => response.json())
        .then(data => {
            products = data;
            updateProductOptions();
        })
        .catch(error => console.error('Error loading products:', error));
}

function loadExistingItems() {
    existingItems.forEach((item, index) => {
        addItem(item, index);
    });
    if (existingItems.length === 0) {
        addItem(); // Add first item if no existing items
    }
}

function updateProductOptions() {
    const productSelects = document.querySelectorAll('.product-select');
    productSelects.forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Select Item</option>';
        products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.product_name;
            option.dataset.price = product.unit_price || 0;
            option.dataset.stock = product.reorder_level || 0;
            option.dataset.cgst = 0;
            option.dataset.sgst = 0;
            option.dataset.igst = 0;
            if (product.id == currentValue) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    });
}

// Update customer details when customer is selected
document.getElementById('customer_id').addEventListener('change', function() {
    const selectedCustomer = customers.find(c => c.id == this.value);
    if (selectedCustomer) {
        document.getElementById('customer_address').value = selectedCustomer.address || '';
        document.getElementById('customer_mobile').value = selectedCustomer.phone || '';
        document.getElementById('customer_gstn').value = selectedCustomer.gst_number || '';
    }
});

function addItem(existingItem = null, existingIndex = null) {
    const template = document.getElementById('orderItemTemplate');
    const tbody = document.getElementById('orderItemsBody');
    const newRow = template.content.cloneNode(true);
    
    const currentIndex = existingIndex !== null ? existingIndex : itemIndex;
    
    // Update all INDEX placeholders
    newRow.querySelectorAll('[name*="INDEX"]').forEach(element => {
        element.name = element.name.replace('INDEX', currentIndex);
    });
    
    // Update product options for this row
    const productSelect = newRow.querySelector('.product-select');
    productSelect.innerHTML = '<option value="">Select Item</option>';
    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = product.product_name;
        option.dataset.price = product.unit_price || 0;
        option.dataset.stock = product.reorder_level || 0;
        option.dataset.cgst = 0;
        option.dataset.sgst = 0;
        option.dataset.igst = 0;
        productSelect.appendChild(option);
    });
    
    // If editing existing item, populate the fields
    if (existingItem) {
        productSelect.value = existingItem.product_id;
        newRow.querySelector('.unit-price').value = existingItem.unit_price || 0;
        newRow.querySelector('.discount').value = existingItem.discount || 0;
        newRow.querySelector('.quantity').value = existingItem.quantity || 1;
        newRow.querySelector('.cgst').value = existingItem.cgst || 0;
        newRow.querySelector('.sgst').value = existingItem.sgst || 0;
        newRow.querySelector('.igst').value = existingItem.igst || 0;
        newRow.querySelector('.ship-qty').value = existingItem.ship_qty || existingItem.quantity || 1;
        newRow.querySelector('.available-stock').value = existingItem.available_stock || 0;
        
        // Update product info and calculate totals
        updateProductInfo(productSelect, currentIndex);
    }
    
    tbody.appendChild(newRow);
    
    if (existingIndex === null) {
        itemIndex++;
    }
}

function removeItem(button) {
    button.closest('tr').remove();
    calculateTotals();
}

function updateProductInfo(select, index) {
    const selectedProduct = products.find(p => p.id == select.value);
    if (selectedProduct) {
        const row = select.closest('tr');
        row.querySelector('.unit-price').value = selectedProduct.unit_price || 0;
        row.querySelector('.available-stock').value = selectedProduct.reorder_level || 0;
        row.querySelector('.cgst').value = 0;
        row.querySelector('.sgst').value = 0;
        row.querySelector('.igst').value = 0;
        
        // Set max quantity based on available stock
        const quantityInput = row.querySelector('.quantity');
        quantityInput.max = selectedProduct.reorder_level || 1;
        
        // Add stock warning if low stock
        const stockInput = row.querySelector('.available-stock');
        if (selectedProduct.reorder_level <= 10) {
            stockInput.classList.add('text-danger', 'fw-bold');
        } else {
            stockInput.classList.remove('text-danger', 'fw-bold');
        }
        
        calculateLineTotal(index);
    }
}

function calculateLineTotal(index) {
    const row = document.querySelector(`[name="items[${index}][product_id]"]`).closest('tr');
    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const discount = parseFloat(row.querySelector('.discount').value) || 0;
    const cgst = parseFloat(row.querySelector('.cgst').value) || 0;
    const sgst = parseFloat(row.querySelector('.sgst').value) || 0;
    const igst = parseFloat(row.querySelector('.igst').value) || 0;
    
    // Calculate line total
    const subtotal = unitPrice * quantity;
    const discountAmount = (subtotal * discount) / 100;
    const taxableAmount = subtotal - discountAmount;
    
    // Calculate tax amounts
    const cgstAmount = (taxableAmount * cgst) / 100;
    const sgstAmount = (taxableAmount * sgst) / 100;
    const igstAmount = (taxableAmount * igst) / 100;
    const totalTax = cgstAmount + sgstAmount + igstAmount;
    
    // Set values
    row.querySelector('.line-total').value = (taxableAmount + totalTax).toFixed(2);
    row.querySelector('.tax-amount').value = totalTax.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let discountTotal = 0;
    let cgstTotal = 0;
    let sgstTotal = 0;
    let igstTotal = 0;
    
    document.querySelectorAll('.order-item').forEach(row => {
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const discount = parseFloat(row.querySelector('.discount').value) || 0;
        const cgst = parseFloat(row.querySelector('.cgst').value) || 0;
        const sgst = parseFloat(row.querySelector('.sgst').value) || 0;
        const igst = parseFloat(row.querySelector('.igst').value) || 0;
        
        const lineSubtotal = unitPrice * quantity;
        const lineDiscount = (lineSubtotal * discount) / 100;
        const taxableAmount = lineSubtotal - lineDiscount;
        
        subtotal += lineSubtotal;
        discountTotal += lineDiscount;
        cgstTotal += (taxableAmount * cgst) / 100;
        sgstTotal += (taxableAmount * sgst) / 100;
        igstTotal += (taxableAmount * igst) / 100;
    });
    
    const transportAmount = parseFloat(document.getElementById('transport_amount').value) || 0;
    const transportTax = parseFloat(document.getElementById('transport_tax').value) || 0;
    
    // Update display
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('discountTotal').textContent = '₹' + discountTotal.toFixed(2);
    document.getElementById('cgstTotal').textContent = '₹' + cgstTotal.toFixed(2);
    document.getElementById('sgstTotal').textContent = '₹' + sgstTotal.toFixed(2);
    document.getElementById('igstTotal').textContent = '₹' + igstTotal.toFixed(2);
    document.getElementById('transportAmountDisplay').textContent = '₹' + transportAmount.toFixed(2);
    document.getElementById('transportTaxDisplay').textContent = '₹' + transportTax.toFixed(2);
    
    const finalTotal = subtotal - discountTotal + cgstTotal + sgstTotal + igstTotal + transportAmount + transportTax;
    document.getElementById('finalTotal').textContent = '₹' + finalTotal.toFixed(2);
}

// Update totals when transport fields change
document.getElementById('transport_amount').addEventListener('input', calculateTotals);
document.getElementById('transport_tax').addEventListener('input', calculateTotals);

// Form validation
document.getElementById('salesOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate that at least one item is added
    const items = document.querySelectorAll('.order-item');
    if (items.length === 0) {
        alert('Please add at least one product item.');
        return;
    }
    
    // Validate all required fields
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (isValid) {
        this.submit();
    } else {
        alert('Please fill in all required fields.');
    }
});
</script>

<style>
.order-item .available-stock {
    background-color: #f8f9fa;
    color: #6c757d;
}

.order-item .available-stock.text-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.is-invalid {
    border-color: #dc3545;
}
</style>

<?= $this->endSection() ?>
