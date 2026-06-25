<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Create Sales Order</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">New Sales Order</h4>
                </div>
                <div class="card-body">
                    <form id="salesOrderForm" action="<?= base_url('sales-order/store') ?>" method="POST">
                        <!-- First Row - Invoice and Customer Details -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="invoice_no" class="form-label">Invoice No</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="1" required>
                            </div>
                            <div class="col-md-3">
                                <label for="order_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="order_date" name="order_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="customer_id" class="form-label">Customer Name</label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="customer_mobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" placeholder="Mobile Number" required>
                            </div>
                        </div>

                        <!-- Second Row - Customer Address and GSTN -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Enter Address" required>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_gstn" class="form-label">GST Number</label>
                                <input type="text" class="form-control" id="customer_gstn" name="customer_gstn" placeholder="GST Number">
                            </div>
                        </div>

                        <!-- Product Details Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5 class="mb-3">Product Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="orderItemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Price</th>
                                                <th>Discount</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>CGST</th>
                                                <th>SGST</th>
                                                <th>IGST</th>
                                                <th>Tax Amount</th>
                                                <th>Ship Qty</th>
                                                <th>Stock</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderItemsBody">
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                                <button type="button" class="btn btn-info btn-sm ms-2" onclick="testProductLoading()">
                                    <i class="fas fa-bug"></i> Test Products
                                </button>
                            </div>
                        </div>

                        <!-- Transport and Description Row -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="transport_amount" class="form-label">Transport Amount</label>
                                <input type="number" class="form-control" id="transport_amount" name="transport_amount" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label for="transport_tax" class="form-label">Transport Tax</label>
                                <input type="number" class="form-control" id="transport_tax" name="transport_tax" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                            </div>
                        </div>

                        <!-- Order Summary Row -->
                        <div class="row mb-3">
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

                        <!-- Submit Buttons Row -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Create Sales Order</button>
                                <a href="<?= base_url('sales-order') ?>" class="btn btn-secondary">Cancel</a>
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
            <select class="form-select product-select" name="items[INDEX][product_id]" required>
                <option value="">Search Product...</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control unit-input" name="items[INDEX][unit]" placeholder="Un" value="Un" readonly>
        </td>
        <td>
            <input type="number" class="form-control unit-price" name="items[INDEX][unit_price]" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" class="form-control discount-input" name="items[INDEX][discount]" step="0.01" min="0" value="0">
        </td>
        <td>
            <input type="number" class="form-control quantity" name="items[INDEX][quantity]" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control line-total" name="items[INDEX][line_total]" step="0.01" readonly>
        </td>
        <td>
            <input type="number" class="form-control cgst" name="items[INDEX][cgst]" step="0.01" min="0" value="0">
        </td>
        <td>
            <input type="number" class="form-control sgst" name="items[INDEX][sgst]" step="0.01" min="0" value="0">
        </td>
        <td>
            <input type="number" class="form-control igst" name="items[INDEX][igst]" step="0.01" min="0" value="0">
        </td>
        <td>
            <input type="number" class="form-control tax-amount" name="items[INDEX][tax_amount]" step="0.01" readonly>
        </td>
        <td>
            <input type="number" class="form-control ship-qty" name="items[INDEX][ship_qty]" min="1" value="1">
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
let customers = [];

// Load products and customers on page load
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadCustomers();
    addItem(); // Add first item by default
    setDefaultDate();
});

function setDefaultDate() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('order_date').value = today;
}

function loadProducts() {
    console.log('=== Loading products ===');
    const apiUrl = '<?= base_url('sales-order/get-products') ?>';
    console.log('API URL:', apiUrl);
    console.log('Base URL:', '<?= base_url() ?>');
    
    fetch(apiUrl)
        .then(response => {
            console.log('API Response Status:', response.status);
            console.log('API Response Headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Products loaded from API:', data);
            console.log('Number of products:', data.length);
            if (data.length > 0) {
                console.log('First product sample:', data[0]);
                alert(`Products loaded successfully: ${data.length} products found`);
            } else {
                alert('No products found in the API response');
            }
            products = data;
            updateProductOptions();
        })
        .catch(error => {
            console.error('Error loading products:', error);
            console.error('Error details:', error.message);
        });
}

function loadCustomers() {
    fetch('<?= base_url('sales-order/get-customers') ?>')
        .then(response => response.json())
        .then(data => {
            customers = data;
            updateCustomerOptions();
        })
        .catch(error => console.error('Error loading customers:', error));
}

function updateProductOptions() {
    console.log('=== Updating product options ===');
    console.log('Products array:', products);
    console.log('Products array length:', products.length);
    
    const productSelects = document.querySelectorAll('.product-select');
    console.log('Found product selects:', productSelects.length);
    
    productSelects.forEach((select, index) => {
        console.log(`Updating product select ${index}:`, select);
        updateSingleProductSelect(select);
    });
}

function updateSingleProductSelect(select) {
    console.log('=== updateSingleProductSelect called ===');
    console.log('Select element:', select);
    console.log('Products to add:', products.length);
    
    const firstOption = select.querySelector('option:first-child');
    console.log('First option:', firstOption);
    
    select.innerHTML = '';
    select.appendChild(firstOption);
    
    products.forEach((product, index) => {
        console.log(`Adding product ${index}:`, product);
        
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.product_code} - ${product.product_name}`;
        option.dataset.sellingPrice = product.selling_price || product.unit_price || 0;
        option.dataset.unitPrice = product.unit_price || 0;
        option.dataset.availableStock = product.available_stock || product.current_stock || 0;
        option.dataset.unit = product.unit || 'Un';
        option.dataset.gstRate = product.gst_rate || 0;
        
        console.log(`Created option:`, option);
        select.appendChild(option);
    });
    
    console.log('Final select options count:', select.options.length);
}

function updateCustomerOptions() {
    const customerSelect = document.getElementById('customer_id');
    customerSelect.innerHTML = '<option value="">Select Customer</option>';
    customers.forEach(customer => {
        const option = document.createElement('option');
        option.value = customer.id;
        option.textContent = customer.customer_name;
        option.dataset.address = customer.address || '';
        option.dataset.mobile = customer.phone || '';
        option.dataset.gstn = customer.gst_number || '';
        customerSelect.appendChild(option);
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

function addItem() {
    console.log('=== Adding new item ===');
    console.log('Current itemIndex:', itemIndex);
    console.log('Products available:', products ? products.length : 'undefined');
    
    const template = document.getElementById('orderItemTemplate');
    const tbody = document.getElementById('orderItemsBody');
    
    console.log('Template found:', template);
    console.log('Table body found:', tbody);
    
    const newRow = template.content.cloneNode(true);
    console.log('New row cloned from template');
    
    // Update all INDEX placeholders
    newRow.querySelectorAll('[name*="INDEX"]').forEach(element => {
        element.name = element.name.replace('INDEX', itemIndex);
    });
    
    // Set up product select for this row
    const productSelect = newRow.querySelector('.product-select');
    console.log('Product select found:', productSelect);
    
    if (productSelect) {
        updateSingleProductSelect(productSelect);
        
        // Add change event to populate product details
        productSelect.addEventListener('change', function() {
            console.log('Product selected for index:', itemIndex);
            updateProductInfo(this, itemIndex);
        });
    } else {
        console.error('Product select NOT found in new row!');
    }
    
    tbody.appendChild(newRow);
    
    // Attach calculateLineTotal to all relevant inputs
    const currentIndex = itemIndex;
    const newRowElement = tbody.lastElementChild;
    
    ['.unit-price', '.discount-input', '.quantity', '.cgst', '.sgst', '.igst', '.ship-qty'].forEach(selector => {
        const input = newRowElement.querySelector(selector);
        if (input) {
            input.addEventListener('change', function() {
                calculateLineTotal(currentIndex);
            });
        }
    });
    
    itemIndex++;
    console.log('Item index incremented to:', itemIndex);
}

function removeItem(button) {
    button.closest('tr').remove();
    calculateTotals();
}

function updateProductInfo(select, index) {
    if (!select.value) return;
    
    const selectedProduct = products.find(p => p.id == select.value);
    if (!selectedProduct) return;
    
    const container = select.closest('tr');
    if (!container) return;
    
    const unitPrice = (selectedProduct.selling_price && selectedProduct.selling_price > 0) ? selectedProduct.selling_price : selectedProduct.unit_price || 0;
    const availableStock = selectedProduct.available_stock || selectedProduct.current_stock || 0;
    const gstRate = parseFloat(selectedProduct.gst_rate) || 0;
    
    const unitPriceInput = container.querySelector('.unit-price');
    const stockInput = container.querySelector('.available-stock');
    const cgstInput = container.querySelector('.cgst');
    const sgstInput = container.querySelector('.sgst');
    const igstInput = container.querySelector('.igst');
    const unitInput = container.querySelector('.unit-input');
    const quantityInput = container.querySelector('.quantity');
    
    if (unitPriceInput) unitPriceInput.value = unitPrice;
    if (stockInput) stockInput.value = availableStock;
    if (cgstInput) cgstInput.value = gstRate / 2;
    if (sgstInput) sgstInput.value = gstRate / 2;
    if (igstInput) igstInput.value = gstRate;
    if (unitInput) unitInput.value = selectedProduct.unit || 'Un';
    
    if (quantityInput && (!quantityInput.value || quantityInput.value === '0' || quantityInput.value === '')) {
        quantityInput.value = '1';
    }
    if (quantityInput) quantityInput.max = availableStock || 999999;
    
    if (stockInput) {
        if (availableStock <= 10) {
            stockInput.classList.add('text-danger', 'fw-bold');
        } else {
            stockInput.classList.remove('text-danger', 'fw-bold');
        }
    }
    
    calculateLineTotal(index);
}

function calculateLineTotal(index) {
    const row = document.querySelector(`[name="items[${index}][product_id]"]`)?.closest('tr');
    if (!row) return;
    
    const unitPrice = parseFloat(row.querySelector('.unit-price')?.value) || 0;
    const quantity = parseFloat(row.querySelector('.quantity')?.value) || 0;
    const discount = parseFloat(row.querySelector('.discount-input')?.value) || 0;
    const cgst = parseFloat(row.querySelector('.cgst')?.value) || 0;
    const sgst = parseFloat(row.querySelector('.sgst')?.value) || 0;
    const igst = parseFloat(row.querySelector('.igst')?.value) || 0;
    
    const subtotal = unitPrice * quantity;
    const discountAmount = (subtotal * discount) / 100;
    const taxableAmount = subtotal - discountAmount;
    
    const cgstAmount = (taxableAmount * cgst) / 100;
    const sgstAmount = (taxableAmount * sgst) / 100;
    const igstAmount = (taxableAmount * igst) / 100;
    const totalTax = cgstAmount + sgstAmount + igstAmount;
    
    const lineTotalInput = row.querySelector('.line-total');
    const taxAmountInput = row.querySelector('.tax-amount');
    
    if (lineTotalInput) lineTotalInput.value = (taxableAmount + totalTax).toFixed(2);
    if (taxAmountInput) taxAmountInput.value = totalTax.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let discountTotal = 0;
    let cgstTotal = 0;
    let sgstTotal = 0;
    let igstTotal = 0;
    
    document.querySelectorAll('.order-item').forEach(row => {
        const unitPrice = parseFloat(row.querySelector('.unit-price')?.value) || 0;
        const quantity = parseFloat(row.querySelector('.quantity')?.value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input')?.value) || 0;
        const cgst = parseFloat(row.querySelector('.cgst')?.value) || 0;
        const sgst = parseFloat(row.querySelector('.sgst')?.value) || 0;
        const igst = parseFloat(row.querySelector('.igst')?.value) || 0;
        
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

// Test function to debug product loading
function testProductLoading() {
    console.log('=== Testing Product Loading ===');
    console.log('Products array:', products);
    console.log('Products array length:', products.length);
    console.log('Products type:', typeof products);
    
    // Test the API endpoint manually
    console.log('Testing API endpoint manually...');
    fetch('<?= base_url('sales-order/get-products') ?>')
        .then(response => {
            console.log('Manual API Response Status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Manual API Response Data:', data);
            console.log('Manual API Data Length:', data.length);
            if (data.length > 0) {
                console.log('First product from manual API call:', data[0]);
                alert(`Manual API test successful: ${data.length} products found`);
            } else {
                alert('Manual API test: No products found');
            }
        })
        .catch(error => {
            console.error('Manual API Error:', error);
        });
    
    // Test if product selects exist
    const productSelects = document.querySelectorAll('.product-select');
    console.log('Product selects found:', productSelects.length);
    productSelects.forEach((select, index) => {
        console.log(`Product select ${index}:`, select);
        console.log(`Product select ${index} options:`, select.options.length);
        console.log(`Product select ${index} innerHTML:`, select.innerHTML);
    });
}

// Update totals when transport fields change
document.getElementById('transport_amount').addEventListener('input', calculateTotals);
document.getElementById('transport_tax').addEventListener('input', calculateTotals);

// Form validation
document.getElementById('salesOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const items = document.querySelectorAll('.order-item');
    if (items.length === 0) {
        alert('Please add at least one product item.');
        return;
    }
    
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

#orderItemsTable {
    table-layout: fixed;
    width: 100%;
}

#orderItemsTable th,
#orderItemsTable td {
    padding: 8px;
    vertical-align: middle;
}

#orderItemsTable input[type="number"],
#orderItemsTable input[type="text"],
#orderItemsTable select {
    min-width: 60px !important;
    width: 100% !important;
    box-sizing: border-box;
    font-size: 14px;
}

/* Specific column widths */
#orderItemsTable th:nth-child(1) { width: 20%; } /* Product */
#orderItemsTable th:nth-child(2) { width: 8%; }  /* Unit */
#orderItemsTable th:nth-child(3) { width: 10%; } /* Price */
#orderItemsTable th:nth-child(4) { width: 8%; }  /* Discount */
#orderItemsTable th:nth-child(5) { width: 8%; }  /* Quantity */
#orderItemsTable th:nth-child(6) { width: 10%; } /* Total */
#orderItemsTable th:nth-child(7) { width: 6%; }  /* CGST */
#orderItemsTable th:nth-child(8) { width: 6%; }  /* SGST */
#orderItemsTable th:nth-child(9) { width: 6%; }  /* IGST */
#orderItemsTable th:nth-child(10) { width: 8%; } /* Tax */
#orderItemsTable th:nth-child(11) { width: 8%; } /* Ship Qty */
#orderItemsTable th:nth-child(12) { width: 8%; } /* Stock */
#orderItemsTable th:nth-child(13) { width: 5%; } /* Action */

.product-select {
    cursor: pointer;
}

.product-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
</style>

<?= $this->endSection() ?>
