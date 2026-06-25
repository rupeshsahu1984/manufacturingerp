<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('purchase-bill') ?>">Purchase Bills</a></li>
                        <li class="breadcrumb-item active">Create Purchase Bill</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Purchase Bill</h4>
            </div>
        </div>
    </div>

<!-- Main Form Layout -->
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
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>CGST</th>
                                <th>SGST</th>
                                <th>IGST</th>
                                <th>Amount</th>
                                <th>Billed</th>
                                <th>Shipped</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Products will be added here -->
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary" id="addProductBtn" onclick="addItem(event)">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Bill Details -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Bill Details</h5>
            </div>
            <div class="card-body">
                <form id="billForm" action="<?= base_url('purchase-bill/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bill Number</label>
                        <input type="text" class="form-control bg-light" id="bill_number" name="bill_number" value="<?= $bill_number ?>" readonly>
                        <div class="form-text text-muted">Auto-generated</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Supplier</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSupplier(event)">
                                <i class="fas fa-plus"></i>
                            </button>
                            <select class="form-select" id="supplier_id" name="supplier_id" required style="display: block; visibility: visible;">
                                <option value="">Select a supplier</option>
                                <?php if (isset($suppliers) && !empty($suppliers)): ?>
                                    <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier['id'] ?>" 
                                            data-credit-limit="<?= isset($supplier['credit_limit']) ? $supplier['credit_limit'] : 0 ?>" 
                                            data-payment-terms="<?= isset($supplier['payment_terms']) ? esc($supplier['payment_terms']) : '' ?>">
                                        <?= esc($supplier['supplier_name']) ?> (<?= esc($supplier['supplier_code'] ?? 'N/A') ?>)
                                    </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No suppliers available. Please add a supplier first.</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php if (empty($suppliers)): ?>
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i> No active suppliers found. Please add a supplier first.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Date</label>
                        <input type="date" class="form-control" id="bill_date" name="bill_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Supplier Memo</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Memo no">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Note"></textarea>
                    </div>
                    
                    <!-- Bill Summary -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3">Bill Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total amount:</span>
                            <span id="totalAmount">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total tax amount:</span>
                            <span id="totalTaxAmount">₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Payable:</span>
                            <span id="totalPayable" class="text-primary">₹0.00</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-2"></i>Create Bill
                        </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="supplierForm">
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="newSupplierName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier Code</label>
                        <input type="text" class="form-control" id="newSupplierCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="newSupplierMobile" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="newSupplierAddress" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSupplier()">Save Supplier</button>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Set default date
        const billDateInput = document.getElementById('bill_date');
        if (billDateInput) {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            billDateInput.value = `${year}-${month}-${day}`;
        }
        
        // Initialize Select2 for supplier (with fallback to regular select)
        if (typeof $ !== 'undefined' && $.fn.select2) {
            const supplierSelect = $('#supplier_id');
            if (supplierSelect.length) {
                try {
                    supplierSelect.select2({
                        theme: 'bootstrap-5', 
                        width: '100%',
                        placeholder: 'Select a supplier',
                        allowClear: true
                    });
                } catch (e) {
                    console.warn('Select2 initialization failed, using regular select:', e);
                }
            }
        }
        
        // Ensure supplier dropdown is visible
        const supplierSelect = document.getElementById('supplier_id');
        if (supplierSelect) {
            supplierSelect.style.display = 'block';
            supplierSelect.style.visibility = 'visible';
        }
        
        // Load products for search
        loadProducts();
        
        // Add initial product row
        if (itemCount === 0) {
            addProductToTable();
        }
    } catch (error) {
        console.error('Error initializing page:', error);
        // Don't redirect on initialization errors
    }
});

// Load products from server
function loadProducts() {
    fetch('<?= base_url('purchase-bill/get-products') ?>')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data)) {
                window.products = data;
                console.log('Products loaded:', data.length);
            } else {
                console.warn('Invalid products data format:', data);
                window.products = [];
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            // Don't redirect on error, just log it
            window.products = [];
            // Show user-friendly message
            const addProductBtn = document.getElementById('addProductBtn');
            if (addProductBtn) {
                addProductBtn.title = 'Error loading products. Please refresh the page.';
            }
        });
}

// Show product search for specific row
function showProductSearch(rowId) {
    const searchResults = document.getElementById(`searchResults_${rowId}`);
    const productInput = document.querySelector(`#row_${rowId} input[name*="[product_name]"]`);
    
    if (!searchResults || !productInput) {
        console.error('Product search elements not found for row:', rowId);
        return;
    }
    
    // Check if products are loaded
    if (!window.products || window.products.length === 0) {
        alert('Products are still loading. Please wait a moment and try again.');
        loadProducts(); // Try to reload
        return;
    }
    
    // Create search input
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control form-control-sm';
    searchInput.placeholder = 'Search products...';
    searchInput.style.width = '100%';
    searchInput.style.border = 'none';
    searchInput.style.borderBottom = '1px solid #ddd';
    searchInput.style.borderRadius = '0';
    
    // Add search input to results div
    searchResults.innerHTML = '';
    searchResults.appendChild(searchInput);
    searchResults.style.display = 'block';
    
    // Focus on search input
    setTimeout(() => searchInput.focus(), 100);
    
    // Handle search input
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        if (searchTerm.length < 1) {
            searchResults.innerHTML = '';
            searchResults.appendChild(searchInput);
            return;
        }
        
        if (!window.products || !Array.isArray(window.products)) {
            searchResults.innerHTML = '<div class="p-2 text-muted">Products not loaded yet...</div>';
            return;
        }
        
        const filteredProducts = window.products.filter(product => 
            (product.product_name && product.product_name.toLowerCase().includes(searchTerm)) ||
            (product.product_code && product.product_code.toLowerCase().includes(searchTerm))
        );
        
        displayProductResults(filteredProducts, searchResults, rowId, productInput);
    });
    
    // Close search when clicking outside
    const closeSearchHandler = function(e) {
        if (!searchResults.contains(e.target) && !productInput.contains(e.target)) {
            searchResults.style.display = 'none';
            document.removeEventListener('click', closeSearchHandler);
        }
    };
    setTimeout(() => {
        document.addEventListener('click', closeSearchHandler);
    }, 100);
}

// Display product search results
function displayProductResults(products, container, rowId, productInput) {
    container.innerHTML = '';
    
    if (products.length === 0) {
        container.innerHTML = '<div class="p-2 text-muted">No products found</div>';
        return;
    }
    
    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.className = 'p-2 border-bottom product-result';
        productDiv.style.cursor = 'pointer';
        productDiv.innerHTML = `
            <div><strong>${product.product_name}</strong></div>
            <small class="text-muted">${product.product_code} - Stock: ${product.available_stock || 0}</small>
        `;
        
        productDiv.addEventListener('click', function() {
            selectProduct(product, rowId, productInput);
        });
        
        container.appendChild(productDiv);
    });
}

// Select product from search results
function selectProduct(product, rowId, productInput) {
    productInput.value = product.product_name;
    const hiddenInput = productInput.nextElementSibling;
    if (hiddenInput && hiddenInput.type === 'hidden') {
        hiddenInput.value = product.id;
    }
    
    // Auto-fill other fields
    const row = document.getElementById(`row_${rowId}`);
    if (row) {
        const purchasePriceInput = row.querySelector('input[name*="[purchase_price]"]');
        const sellingPriceInput = row.querySelector('input[name*="[selling_price]"]');
        const cgstInput = row.querySelector('input[name*="[cgst]"]');
        const sgstInput = row.querySelector('input[name*="[sgst]"]');
        const igstInput = row.querySelector('input[name*="[igst]"]');
        
        if (purchasePriceInput) {
            purchasePriceInput.value = product.purchase_price || product.unit_price || product.cost_price || 0;
        }
        if (sellingPriceInput) {
            sellingPriceInput.value = product.selling_price || 0;
        }
        if (cgstInput) {
            cgstInput.value = product.cgst_rate || 0;
        }
        if (sgstInput) {
            sgstInput.value = product.sgst_rate || 0;
        }
        if (igstInput) {
            igstInput.value = product.igst_rate || 0;
        }
    }
    
    // Hide search results
    const searchResults = document.getElementById(`searchResults_${rowId}`);
    if (searchResults) {
        searchResults.style.display = 'none';
    }
    
    // Update calculations
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
                    <input type="text" class="form-control form-control-sm product-search" 
                           name="items[${itemCount}][product_name]" placeholder="Click to search product" 
                           style="width: 150px;" readonly onclick="showProductSearch(${itemCount})">
                    <input type="hidden" name="items[${itemCount}][product_id]" value="">
                    <div class="product-search-results" id="searchResults_${itemCount}" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1000; width: 200px; background: white; border: 1px solid #ddd; border-radius: 4px; max-height: 200px; overflow-y: auto;"></div>
                </div>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][quantity]" placeholder="Qty" min="1" 
                       onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][purchase_price]" placeholder="Purchase Price" 
                       step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 100px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][selling_price]" placeholder="Selling Price" 
                       step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 100px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][cgst]" placeholder="CGST %" 
                       step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][sgst]" placeholder="SGST %" 
                       step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][igst]" placeholder="IGST %" 
                       step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <span id="amount_${itemCount}">₹0.00</span>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][billed_qty]" placeholder="Billed" 
                       min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCount}][shipped_qty]" placeholder="Shipped" 
                       min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${itemCount})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', row);
    updateRowTotal(itemCount);
    updateTotals();
}

// Update row total
function updateRowTotal(rowId) {
    const row = document.getElementById(`row_${rowId}`);
    const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
    const purchasePrice = parseFloat(row.querySelector('input[name*="[purchase_price]"]').value) || 0;
    const cgst = parseFloat(row.querySelector('input[name*="[cgst]"]').value) || 0;
    const sgst = parseFloat(row.querySelector('input[name*="[sgst]"]').value) || 0;
    const igst = parseFloat(row.querySelector('input[name*="[igst]"]').value) || 0;
    
    const subtotal = quantity * purchasePrice;
    
    // Calculate GST amounts
    const cgstAmount = subtotal * (cgst / 100);
    const sgstAmount = subtotal * (sgst / 100);
    const igstAmount = subtotal * (igst / 100);
    
    const total = subtotal + cgstAmount + sgstAmount + igstAmount;
    
    document.getElementById(`amount_${rowId}`).textContent = `₹${total.toFixed(2)}`;
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
        
        const purchasePriceInput = row.querySelector('input[name*="[purchase_price]"]');
        if (purchasePriceInput) {
            purchasePriceInput.onchange = () => updateRowTotal(index + 1);
        }
        
        const sellingPriceInput = row.querySelector('input[name*="[selling_price]"]');
        if (sellingPriceInput) {
            sellingPriceInput.onchange = () => updateRowTotal(index + 1);
        }
        
        const cgstInput = row.querySelector('input[name*="[cgst]"]');
        if (cgstInput) {
            cgstInput.onchange = () => updateRowTotal(index + 1);
        }
        
        const sgstInput = row.querySelector('input[name*="[sgst]"]');
        if (sgstInput) {
            sgstInput.onchange = () => updateRowTotal(index + 1);
        }
        
        const igstInput = row.querySelector('input[name*="[igst]"]');
        if (igstInput) {
            igstInput.onchange = () => updateRowTotal(index + 1);
        }
    });
    
    itemCount = rows.length;
}

// Update totals
function updateTotals() {
    let totalAmount = 0;
    let totalCgstAmount = 0;
    let totalSgstAmount = 0;
    let totalIgstAmount = 0;
    
    const rows = document.querySelectorAll('#productsTableBody tr');
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const purchasePrice = parseFloat(row.querySelector('input[name*="[purchase_price]"]').value) || 0;
        const cgst = parseFloat(row.querySelector('input[name*="[cgst]"]').value) || 0;
        const sgst = parseFloat(row.querySelector('input[name*="[sgst]"]').value) || 0;
        const igst = parseFloat(row.querySelector('input[name*="[igst]"]').value) || 0;
        
        const rowSubtotal = quantity * purchasePrice;
        const rowCgst = rowSubtotal * (cgst / 100);
        const rowSgst = rowSubtotal * (sgst / 100);
        const rowIgst = rowSubtotal * (igst / 100);
        
        totalAmount += rowSubtotal;
        totalCgstAmount += rowCgst;
        totalSgstAmount += rowSgst;
        totalIgstAmount += rowIgst;
    });
    
    const totalTaxAmount = totalCgstAmount + totalSgstAmount + totalIgstAmount;
    const totalPayable = totalAmount + totalTaxAmount;
    
    document.getElementById('totalAmount').textContent = `₹${totalAmount.toFixed(2)}`;
    document.getElementById('totalTaxAmount').textContent = `₹${totalTaxAmount.toFixed(2)}`;
    document.getElementById('totalPayable').textContent = `₹${totalPayable.toFixed(2)}`;
}

// Add item manually
function addItem(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    try {
        addProductToTable();
    } catch (error) {
        console.error('Error adding product:', error);
        alert('Error adding product. Please try again.');
    }
    return false;
}

// Add supplier
function addSupplier(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    try {
        const modalElement = document.getElementById('addSupplierModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            alert('Supplier modal not found. Please refresh the page.');
        }
    } catch (error) {
        console.error('Error opening supplier modal:', error);
        alert('Error opening supplier form. Please refresh the page.');
    }
    return false;
}

// Save supplier
function saveSupplier(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const name = document.getElementById('newSupplierName').value;
    const code = document.getElementById('newSupplierCode').value;
    const mobile = document.getElementById('newSupplierMobile').value;
    const address = document.getElementById('newSupplierAddress').value;
    
    if (!name || !code || !mobile) {
        alert('Please fill in required fields');
        return;
    }
    
    // Here you would typically save to server
    // For now, just add to select
    const select = document.getElementById('supplier_id');
    const option = document.createElement('option');
    option.value = 'new_' + Date.now();
    option.textContent = name + ' (' + code + ')';
    select.appendChild(option);
    select.value = option.value;
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('addSupplierModal')).hide();
    
    // Clear form
    document.getElementById('supplierForm').reset();
}

// Prevent all buttons from submitting form unless it's the submit button
document.addEventListener('DOMContentLoaded', function() {
    // Ensure all buttons inside form have type="button" except submit button
    const billForm = document.getElementById('billForm');
    if (billForm) {
        const allButtons = billForm.querySelectorAll('button');
        allButtons.forEach(button => {
            if (button.type !== 'submit' && button.type !== 'button') {
                button.type = 'button';
            }
            // Add click handler to prevent any accidental form submission
            if (button.type === 'button') {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);
            }
        });
    }
    
    // Also prevent form submission on Enter key in non-submit inputs
    billForm.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
            const form = e.target.closest('form');
            if (form && form.id === 'billForm') {
                e.preventDefault();
                return false;
            }
        }
    });
});

// Form submission
const billForm = document.getElementById('billForm');
if (billForm) {
    billForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const supplier = document.getElementById('supplier_id').value;
        const date = document.getElementById('bill_date').value;
        const items = document.querySelectorAll('#productsTableBody tr');
        
        if (!supplier) {
            alert('Please select a supplier');
            return false;
        }
        
        if (items.length === 0) {
            alert('Please add at least one product');
            return false;
        }
        
        // Collect all items data
        const itemsData = [];
        items.forEach((row, index) => {
            const productId = row.querySelector('input[name*="[product_id]"]')?.value;
            const quantity = row.querySelector('input[name*="[quantity]"]')?.value;
            const purchasePrice = row.querySelector('input[name*="[purchase_price]"]')?.value;
            const sellingPrice = row.querySelector('input[name*="[selling_price]"]')?.value;
            const cgst = row.querySelector('input[name*="[cgst]"]')?.value || 0;
            const sgst = row.querySelector('input[name*="[sgst]"]')?.value || 0;
            const igst = row.querySelector('input[name*="[igst]"]')?.value || 0;
            
            if (productId && quantity && purchasePrice) {
                itemsData.push({
                    product_id: productId,
                    quantity: quantity,
                    purchase_price: purchasePrice,
                    selling_price: sellingPrice || purchasePrice,
                    cgst: cgst,
                    sgst: sgst,
                    igst: igst
                });
            }
        });
        
        if (itemsData.length === 0) {
            alert('Please add at least one valid product with quantity and price');
            return false;
        }
        
        // Add items as JSON to hidden input
        let itemsInput = document.getElementById('items_json');
        if (!itemsInput) {
            itemsInput = document.createElement('input');
            itemsInput.type = 'hidden';
            itemsInput.name = 'items';
            itemsInput.id = 'items_json';
            billForm.appendChild(itemsInput);
        }
        itemsInput.value = JSON.stringify(itemsData);
        
        // Remove event listener to prevent double submission
        billForm.removeEventListener('submit', arguments.callee);
        
        // Submit the form
        billForm.submit();
    });
}

// Prevent any accidental form submissions from buttons
document.addEventListener('click', function(e) {
    // If clicking a button that's not type="submit" inside a form, prevent form submission
    const button = e.target.closest('button');
    if (button && button.type !== 'submit' && button.closest('form')) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
    
    // If clicking a link inside a form, prevent form submission
    const link = e.target.closest('a');
    if (link && link.closest('form')) {
        // Only prevent if it's not a navigation link
        if (!link.hasAttribute('href') || link.getAttribute('href') === '#' || link.getAttribute('href') === 'javascript:void(0)') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }
}, true);

// Additional protection: Ensure all buttons have proper type
document.addEventListener('DOMContentLoaded', function() {
    const billForm = document.getElementById('billForm');
    if (billForm) {
        billForm.querySelectorAll('button').forEach(button => {
            if (button.type !== 'submit') {
                button.type = 'button';
            }
        });
    }
    
    // Prevent form submission on Enter key except in textareas
    document.querySelectorAll('form input, form select').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.type !== 'submit' && this.tagName !== 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Prevent JavaScript errors from causing redirects
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
    e.preventDefault();
    return false;
}, true);
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
</style>

<?= $this->endSection() ?>
