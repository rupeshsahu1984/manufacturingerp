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
                        <li class="breadcrumb-item active">Create Sales Order</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Sales Order</h4>
            </div>
            </div>
</div>

<!-- Menu Toggle Button -->
<div class="row mb-3">
    <div class="col-12">
        <button type="button" id="menuToggleBtn" class="btn btn-success btn-sm" onclick="toggleMenu()">
            <i class="fas fa-times"></i> Close Menu
        </button>
        <span class="ms-3 text-muted">Use this button to open/close the side menu</span>
    </div>
</div>

<!-- Main Form -->
<div class="row">
    <!-- Left Column - Product Entry -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus me-2"></i>Add Products</h5>
            </div>
            <div class="card-body">
                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="productsTable">
                        <thead class="table-light">
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Billed</th>
                                <th>Shipped</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Products will be added here -->
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary" onclick="addItem()">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Order Details -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Order Details</h5>
            </div>
            <div class="card-body">
                <form id="orderForm" action="<?= base_url('sales-order/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Invoice No</label>
                        <input type="text" class="form-control" id="invoiceNo" name="invoice_no" value="<?= $so_number ?>" readonly>
                        <div class="form-text text-muted">Auto-generated</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Customer</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCustomer()">
                                <i class="fas fa-plus"></i>
                            </button>
                            <select class="form-select" id="customerSelect" name="customer_id" required>
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>"><?= esc($customer['customer_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Date</label>
                        <input type="date" class="form-control" id="orderDate" name="order_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Customer Address</label>
                        <input type="text" class="form-control" id="customerAddress" name="customer_address" placeholder="Enter customer address" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Customer Mobile</label>
                        <input type="text" class="form-control" id="customerMobile" name="customer_mobile" placeholder="Enter customer mobile number" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer GSTN</label>
                        <input type="text" class="form-control" id="customerGstn" name="customer_gstn" placeholder="Enter customer GSTN">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Note</label>
                        <textarea class="form-control" id="orderNote" name="description" rows="3" placeholder="Any additional notes..."></textarea>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3">Order Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹0.00</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <span id="discountTotal">₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Amount:</span>
                            <span id="totalAmount" class="text-primary">₹0.00</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-check me-2"></i>Create Sales Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="newCustomerName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="newCustomerMobile" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="newCustomerAddress" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 0;
let finishedProducts = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default date
    document.getElementById('orderDate').value = new Date().toISOString().split('T')[0];
    
    // Load finished products for dropdown
    loadFinishedProducts();
    
    // Add keyboard shortcut for menu toggle (Esc key)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggleMenu();
        }
    });
});

// Load finished products from server
function loadFinishedProducts() {
    // Try to load from the real API endpoint first
    fetch('<?= base_url('product/finished-goods-dropdown') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                finishedProducts = data.products;
                console.log('Loaded finished products from API:', finishedProducts);
                populateProductDropdowns();
            } else {
                console.log('API returned no success:', data);
                loadFallbackData();
            }
        })
        .catch(error => {
            console.error('Error loading products from API:', error);
            console.log('Trying fallback data...');
            loadFallbackData();
        });
}

// Load fallback data if API fails
function loadFallbackData() {
    console.log('Loading fallback data...');
    finishedProducts = [
        { id: 1, product_code: 'FG001', product_name: 'Premium Widget A', selling_price: 100, cgst_rate: 9, sgst_rate: 9, igst_rate: 18 },
        { id: 2, product_code: 'FG002', product_name: 'Standard Widget B', selling_price: 150, cgst_rate: 9, sgst_rate: 9, igst_rate: 18 },
        { id: 3, product_code: 'FG003', product_name: 'Economy Widget C', selling_price: 75, cgst_rate: 6, sgst_rate: 6, igst_rate: 12 }
    ];
    console.log('Fallback data loaded:', finishedProducts);
    populateProductDropdowns();
}

// Populate all product dropdowns with finished products
function populateProductDropdowns() {
    const dropdowns = document.querySelectorAll('.product-select');
    dropdowns.forEach(dropdown => {
        populateSingleDropdown(dropdown);
    });
}

// Populate a single product dropdown
function populateSingleDropdown(dropdown) {
    // Clear existing options except the first one
    dropdown.innerHTML = '<option value="">Select Finished Product</option>';
    
    if (finishedProducts) {
        finishedProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = `${product.product_code} - ${product.product_name}`;
            option.setAttribute('data-product', JSON.stringify(product));
            dropdown.appendChild(option);
        });
    }
}

// Fetch product stock from server
function fetchProductStock(productId, rowId) {
    fetch(`<?= base_url('product/stock/') ?>${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stockInput = document.querySelector(`#row_${rowId} input[name*="[stock]"]`);
                const availableStockInput = document.querySelector(`#row_${rowId} input[name*="[available_stock]"]`);
                if (stockInput) {
                    stockInput.value = data.stock || 0;
                    stockInput.placeholder = `Available: ${data.stock || 0}`;
                }
                if (availableStockInput) {
                    availableStockInput.value = data.stock || 0;
                }
            } else {
                console.log('Stock fetch failed:', data.message);
                // Set default stock value
                const stockInput = document.querySelector(`#row_${rowId} input[name*="[stock]"]`);
                const availableStockInput = document.querySelector(`#row_${rowId} input[name*="[available_stock]"]`);
                if (stockInput) {
                    stockInput.value = 0;
                    stockInput.placeholder = 'Stock';
                }
                if (availableStockInput) {
                    availableStockInput.value = 0;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching stock:', error);
            // Set default stock value
            const stockInput = document.querySelector(`#row_${rowId} input[name*="[stock]"]`);
            if (stockInput) {
                stockInput.value = 1;
                stockInput.placeholder = 'Stock';
            }
        });
}

// Handle product selection from dropdown
function selectProductFromDropdown(rowId) {
    const row = document.getElementById(`row_${rowId}`);
    const dropdown = row.querySelector('.product-select');
    const productId = dropdown.value;
    const productNameInput = row.querySelector('input[name*="[product_name]"]');
    
    if (!productId) {
        // Clear all fields if no product selected
        clearProductFields(rowId);
        return;
    }
    
    // Get product details from selected option
    const selectedOption = dropdown.options[dropdown.selectedIndex];
    const productData = JSON.parse(selectedOption.getAttribute('data-product'));
    
    // Set product name
    productNameInput.value = productData.product_name;
    
    // Auto-fill other fields
    row.querySelector('input[name*="[unit_price]"]').value = productData.selling_price || 0;
    row.querySelector('input[name*="[price]"]').value = productData.selling_price || 0;
    
                // Auto-fetch stock for the selected product
    fetchProductStock(productData.id, rowId);
    
    // Set available_stock
    const availableStockInput = row.querySelector('input[name*="[available_stock]"]');
    if (availableStockInput) {
        // Will be set when stock is fetched
    }
    
    // Set default quantity to 1 if empty
    const quantityInput = row.querySelector('input[name*="[quantity]"]');
    if (!quantityInput.value || quantityInput.value == 0) {
        quantityInput.value = 1;
    }
    
    // Update calculations
    updateRowTotal(rowId);
}

// Clear product fields when no product is selected
function clearProductFields(rowId) {
    const row = document.getElementById(`row_${rowId}`);
    row.querySelector('input[name*="[product_name]"]').value = '';
    row.querySelector('input[name*="[unit_price]"]').value = '';
    row.querySelector('input[name*="[price]"]').value = '';
    updateRowTotal(rowId);
}

// Add product to table
function addProductToTable() {
    itemCount++;
    const tbody = document.getElementById('productsTableBody');
    
    const row = `
        <tr id="row_${itemCount}">
            <td>${itemCount}</td>
            <td>
                <div class="position-relative">
                    <select class="form-control form-control-sm product-select" 
                           name="items[${itemCount}][product_id]" 
                           style="width: 200px;" 
                           onchange="selectProductFromDropdown(${itemCount})">
                        <option value="">Select Finished Product</option>
                    </select>
                    <input type="hidden" name="items[${itemCount}][product_name]" value="">
                    <input type="hidden" name="items[${itemCount}][available_stock]" value="0">
                </div>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][stock]" placeholder="Stock" min="1" 
                       onchange="updateRowTotal(${itemCount})" style="width: 80px;" readonly>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][unit_price]" placeholder="Price" 
                       step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 100px;">
                <input type="hidden" name="items[${itemCount}][price]" value="">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][discount]" placeholder="Discount %" 
                       step="0.01" min="0" value="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][quantity]" placeholder="Billed" 
                       min="1" required onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][ship_qty]" placeholder="Shipped" 
                       min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
                <input type="hidden" name="items[${itemCount}][cgst]" value="0">
                <input type="hidden" name="items[${itemCount}][sgst]" value="0">
                <input type="hidden" name="items[${itemCount}][igst]" value="0">
                <input type="hidden" name="items[${itemCount}][tax_amount]" value="0">
                <input type="hidden" name="items[${itemCount}][line_total]" value="0">
            </td>
            <td>
                <span id="amount_${itemCount}">₹0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${itemCount})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', row);
    
    // Populate the new dropdown with finished products
    const newDropdown = document.querySelector(`#row_${itemCount} .product-select`);
    populateSingleDropdown(newDropdown);
    
    updateRowTotal(itemCount);
    updateTotals();
}

// Update row total
function updateRowTotal(rowId) {
    const row = document.getElementById(`row_${rowId}`);
    const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
    const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
    const discount = parseFloat(row.querySelector('input[name*="[discount]"]').value) || 0;
    
    const subtotal = quantity * unitPrice;
    const discountAmount = subtotal * (discount / 100);
    const lineTotal = subtotal - discountAmount;
    
    // Update hidden fields
    row.querySelector('input[name*="[line_total]"]').value = lineTotal.toFixed(2);
    
    // Update display
    document.getElementById(`amount_${rowId}`).textContent = `₹${lineTotal.toFixed(2)}`;
    
    // Set ship_qty to quantity if not set
    const shipQtyInput = row.querySelector('input[name*="[ship_qty]"]');
    if (!shipQtyInput.value && quantity > 0) {
        shipQtyInput.value = quantity;
    }
    
    updateTotals();
}

// Remove row
function removeRow(rowId) {
    document.getElementById(`row_${rowId}`).remove();
    updateTotals();
    renumberRows();
}

// Renumber rows
function renumberRows() {
    const rows = document.querySelectorAll('#productsTableBody tr');
    rows.forEach((row, index) => {
        const slCell = row.cells[0];
        slCell.textContent = index + 1;
        
        // Update input names
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.name;
            if (name.includes('[')) {
                input.name = name.replace(/\[\d+\]/, `[${index + 1}]`);
            }
        });
        
        // Update amount span id
        const amountSpan = row.querySelector('[id^="amount_"]');
        if (amountSpan) {
            amountSpan.id = `amount_${index + 1}`;
        }
        
        // Update onclick functions
        const removeBtn = row.querySelector('button[onclick*="removeRow"]');
        if (removeBtn) {
            removeBtn.onclick = () => removeRow(index + 1);
        }
        
        const quantityInput = row.querySelector('input[name*="[quantity]"]');
        if (quantityInput) {
            quantityInput.onchange = () => updateRowTotal(index + 1);
        }
        
        const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');
        if (unitPriceInput) {
            unitPriceInput.onchange = () => updateRowTotal(index + 1);
        }
        
        const discountInput = row.querySelector('input[name*="[discount]"]');
        if (discountInput) {
            discountInput.onchange = () => updateRowTotal(index + 1);
        }
        

    });
    
    itemCount = rows.length;
}

// Update totals
function updateTotals() {
    let subtotal = 0;
    let discountTotal = 0;
    
    const rows = document.querySelectorAll('#productsTableBody tr');
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
        const discount = parseFloat(row.querySelector('input[name*="[discount]"]').value) || 0;

        const rowSubtotal = quantity * unitPrice;
        const rowDiscount = rowSubtotal * (discount / 100);
        
        subtotal += rowSubtotal;
        discountTotal += rowDiscount;
    });
    
    const totalAmount = subtotal - discountTotal;
    
    if (document.getElementById('subtotal')) {
        document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    }
    if (document.getElementById('discountTotal')) {
        document.getElementById('discountTotal').textContent = `₹${discountTotal.toFixed(2)}`;
    }
    if (document.getElementById('totalAmount')) {
        document.getElementById('totalAmount').textContent = `₹${totalAmount.toFixed(2)}`;
    }
}

// Add item manually
function addItem() {
    addProductToTable();
}

// Add customer
function addCustomer() {
    const modal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
    modal.show();
}

// Save customer
function saveCustomer() {
    const name = document.getElementById('newCustomerName').value;
    const mobile = document.getElementById('newCustomerMobile').value;
    const address = document.getElementById('newCustomerAddress').value;
    
    if (!name || !mobile) {
        alert('Please fill in required fields');
        return;
    }
    
    // Here you would typically save to server
    // For now, just add to select
    const select = document.getElementById('customerSelect');
    const option = document.createElement('option');
    option.value = 'new_' + Date.now();
    option.textContent = name;
    select.appendChild(option);
    select.value = option.value;
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
    
    // Clear form
    document.getElementById('customerForm').reset();
}

// Menu state management
let isMenuOpen = true;

// Toggle menu open/close
function toggleMenu() {
    const sidebar = document.querySelector('.sidebar, .main-sidebar, #sidebar, .col-lg-3, .col-md-3');
    const mainContent = document.querySelector('.main-content, .col-lg-9, .col-md-9, .col-12');
    const toggleBtn = document.getElementById('menuToggleBtn');
    
    if (isMenuOpen) {
        // Close menu
        if (sidebar) {
            sidebar.style.transform = 'translateX(-100%)';
            sidebar.style.transition = 'transform 0.3s ease';
            sidebar.style.position = 'absolute';
            sidebar.style.zIndex = '1000';
        }
        
        if (mainContent) {
            mainContent.classList.remove('col-lg-9', 'col-md-9');
            mainContent.classList.add('col-12');
        }
        
        if (toggleBtn) {
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i> Open Menu';
            toggleBtn.classList.remove('btn-success');
            toggleBtn.classList.add('btn-primary');
        }
        
        isMenuOpen = false;
    } else {
        // Open menu
        if (sidebar) {
            sidebar.style.transform = 'translateX(0)';
            sidebar.style.transition = 'transform 0.3s ease';
            sidebar.style.position = 'relative';
        }
        
        if (mainContent) {
            mainContent.classList.remove('col-12');
            mainContent.classList.add('col-lg-9', 'col-md-9');
        }
        
        if (toggleBtn) {
            toggleBtn.innerHTML = '<i class="fas fa-times"></i> Close Menu';
            toggleBtn.classList.remove('btn-primary');
            toggleBtn.classList.add('btn-success');
        }
        
        isMenuOpen = true;
    }
}

// Shrink menu to left (for order creation)
function shrinkMenu() {
    const sidebar = document.querySelector('.sidebar, .main-sidebar, #sidebar, .col-lg-3, .col-md-3');
    const mainContent = document.querySelector('.main-content, .col-lg-9, .col-md-9');
    
    if (sidebar) {
        sidebar.style.transform = 'translateX(-100%)';
        sidebar.style.transition = 'transform 0.3s ease';
        sidebar.style.position = 'absolute';
        sidebar.style.zIndex = '1000';
    }
    
    if (mainContent) {
        mainContent.classList.remove('col-lg-9', 'col-md-9');
        mainContent.classList.add('col-12');
    }
    
    // Update toggle button
    const toggleBtn = document.getElementById('menuToggleBtn');
    if (toggleBtn) {
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i> Open Menu';
        toggleBtn.classList.remove('btn-success');
        toggleBtn.classList.add('btn-primary');
    }
    
    isMenuOpen = false;
    
    // Don't show success message here - wait for actual form submission
}

// Form submission
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const customer = document.getElementById('customerSelect').value;
    const date = document.getElementById('orderDate').value;
    const address = document.getElementById('customerAddress').value;
    const mobile = document.getElementById('customerMobile').value;
    const items = document.querySelectorAll('#productsTableBody tr');
    
    if (!customer) {
        alert('Please select a customer');
        return false;
    }
    
    if (!address) {
        alert('Please enter customer address');
        return false;
    }
    
    if (!mobile) {
        alert('Please enter customer mobile number');
        return false;
    }
    
    if (items.length === 0) {
        alert('Please add at least one product');
        return false;
    }
    
    // Validate that all items have product_id and quantity
    let hasValidItems = false;
    items.forEach((row, index) => {
        const productId = row.querySelector('select[name*="[product_id]"]')?.value;
        const quantity = row.querySelector('input[name*="[quantity]"]')?.value;
        
        if (productId && quantity && parseFloat(quantity) > 0) {
            hasValidItems = true;
        }
    });
    
    if (!hasValidItems) {
        alert('Please add at least one product with quantity');
        return false;
    }
    
    // Collect all items data and add to form
    const form = document.getElementById('orderForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Order...';
    
    // Remove any existing items data
    const existingItems = form.querySelectorAll('input[name^="items["]');
    existingItems.forEach(input => input.remove());
    
    // Add items data to form
    let itemIndex = 0;
    items.forEach((row, index) => {
        const productId = row.querySelector('select[name*="[product_id]"]')?.value;
        const quantity = row.querySelector('input[name*="[quantity]"]')?.value;
        const unitPrice = row.querySelector('input[name*="[unit_price]"]')?.value;
        const discount = row.querySelector('input[name*="[discount]"]')?.value || 0;
        const lineTotal = row.querySelector('input[name*="[line_total]"]')?.value || 0;
        const availableStock = row.querySelector('input[name*="[available_stock]"]')?.value || 0;
        
        if (productId && quantity && parseFloat(quantity) > 0) {
            // Create hidden inputs for each item field
            const fields = {
                'product_id': productId,
                'quantity': quantity,
                'unit_price': unitPrice || 0,
                'discount': discount || 0,
                'line_total': lineTotal || 0,
                'cgst': 0,
                'sgst': 0,
                'igst': 0,
                'tax_amount': 0,
                'ship_qty': quantity,
                'available_stock': availableStock || 0
            };
            
            Object.keys(fields).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `items[${itemIndex}][${key}]`;
                input.value = fields[key];
                form.appendChild(input);
            });
            itemIndex++;
        }
    });
    
    // Log what we're sending
    console.log('Submitting form with items:', itemIndex);
    
    if (itemIndex === 0) {
        alert('No valid items to submit. Please check your product selections.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Create Sales Order';
        return false;
    }
    
    // Submit the form programmatically
    form.submit();
});
</script>

<style>
.product-search-results {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid #ddd;
}

.product-result {
    transition: background-color 0.2s ease;
}

.product-result:hover {
    background-color: #f8f9fa;
}

.product-result:last-child {
    border-bottom: none !important;
}

/* Menu toggle button styles */
#menuToggleBtn {
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

#menuToggleBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

/* Floating toggle button for mobile */
@media (max-width: 768px) {
    #menuToggleBtn {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #menuToggleBtn i {
        font-size: 18px;
    }
}

/* Smooth sidebar transitions */
.sidebar, .main-sidebar, #sidebar, .col-lg-3, .col-md-3 {
    transition: transform 0.3s ease, position 0.3s ease;
}

/* Main content transitions */
.main-content, .col-lg-9, .col-md-9, .col-12 {
    transition: all 0.3s ease;
}
</style>

<?= $this->endSection() ?>
