<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('invoice') ?>">Invoices</a></li>
                        <li class="breadcrumb-item active">Create Invoice</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Invoice</h4>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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
                                <th>Quantity</th>
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
    
    <!-- Right Column - Invoice Details -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Invoice Details</h5>
            </div>
            <div class="card-body">
                <form id="invoiceForm" action="<?= base_url('invoice/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Invoice Number</label>
                        <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" value="<?= esc($invoice_number) ?>" readonly>
                        <div class="form-text text-muted">Auto-generated</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Customer</label>
                        <select class="form-select" id="customerSelect" name="customer_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>"><?= esc($customer['customer_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sales Order (Optional)</label>
                        <select class="form-select" id="soSelect" name="so_id">
                            <option value="">Select Sales Order</option>
                            <?php if (!empty($sales_orders)): ?>
                                <?php foreach ($sales_orders as $so): ?>
                                    <option value="<?= $so['id'] ?>" data-customer-id="<?= $so['customer_id'] ?>">
                                        <?= esc($so['so_number'] ?? 'SO-' . $so['id']) ?> - <?= esc($so['customer_name'] ?? 'N/A') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text text-muted">Select a sales order to auto-fill items</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Invoice Date</label>
                        <input type="date" class="form-control" id="invoiceDate" name="invoice_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Due Date</label>
                        <input type="date" class="form-control" id="dueDate" name="due_date">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Status</label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Invoice Summary -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3">Invoice Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>GST (18%):</span>
                            <span id="gstAmount">₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Amount:</span>
                            <span id="totalAmount" class="text-primary">₹0.00</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-check me-2"></i>Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 0;
let products = <?= json_encode($products ?? []) ?>;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('invoiceDate').value = today;
    
    // Set due date to 30 days from today
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 30);
    document.getElementById('dueDate').value = dueDate.toISOString().split('T')[0];
    
    // Handle sales order selection
    document.getElementById('soSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const customerId = selectedOption.getAttribute('data-customer-id');
            if (customerId) {
                document.getElementById('customerSelect').value = customerId;
            }
            // TODO: Load items from sales order
        }
    });
});

// Add new item row
function addItem() {
    itemCount++;
    const tbody = document.getElementById('productsTableBody');
    const row = document.createElement('tr');
    row.id = `itemRow${itemCount}`;
    
    row.innerHTML = `
        <td>${itemCount}</td>
        <td>
            <select class="form-select form-select-sm product-select" name="items[${itemCount}][product_id]" onchange="selectProduct(${itemCount}, this)" required>
                <option value="">Select Product</option>
                ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price ?? p.unit_price ?? 0}" data-stock="${p.current_stock ?? p.available_stock ?? 0}">${p.product_code} - ${p.product_name}</option>`).join('')}
            </select>
        </td>
        <td>
            <span class="stock-display">-</span>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm unit-price" name="items[${itemCount}][unit_price]" step="0.01" min="0" onchange="updateRowTotal(${itemCount})" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm quantity" name="items[${itemCount}][quantity]" step="0.01" min="0.01" onchange="updateRowTotal(${itemCount})" required>
        </td>
        <td>
            <span class="row-total">₹0.00</span>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCount})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
}

// Select product and populate fields
function selectProduct(rowId, selectElement) {
    const option = selectElement.options[selectElement.selectedIndex];
    const row = document.getElementById(`itemRow${rowId}`);
    
    if (option.value) {
        const price = parseFloat(option.getAttribute('data-price')) || 0;
        const stock = parseFloat(option.getAttribute('data-stock')) || 0;
        
        row.querySelector('.unit-price').value = price.toFixed(2);
        row.querySelector('.stock-display').textContent = stock.toFixed(2);
        row.querySelector('.quantity').value = '';
        row.querySelector('.quantity').max = stock;
        
        updateRowTotal(rowId);
    }
}

// Update row total
function updateRowTotal(rowId) {
    const row = document.getElementById(`itemRow${rowId}`);
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
    const lineTotal = quantity * unitPrice;
    
    row.querySelector('.row-total').textContent = '₹' + lineTotal.toFixed(2);
    updateTotals();
}

// Update invoice totals
function updateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('#productsTableBody tr').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        subtotal += quantity * unitPrice;
    });
    
    const gstAmount = subtotal * 0.18; // 18% GST
    const totalAmount = subtotal + gstAmount;
    
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('gstAmount').textContent = '₹' + gstAmount.toFixed(2);
    document.getElementById('totalAmount').textContent = '₹' + totalAmount.toFixed(2);
}

// Remove item row
function removeItem(rowId) {
    const row = document.getElementById(`itemRow${rowId}`);
    if (row) {
        row.remove();
        updateTotals();
        // Renumber rows
        renumberRows();
    }
}

// Renumber rows
function renumberRows() {
    const rows = document.querySelectorAll('#productsTableBody tr');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
    });
}

// Form submission
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    // Collect all item data
    const items = [];
    document.querySelectorAll('#productsTableBody tr').forEach(row => {
        const productId = row.querySelector('.product-select').value;
        const quantity = row.querySelector('.quantity').value;
        const unitPrice = row.querySelector('.unit-price').value;
        
        if (productId && quantity && unitPrice) {
            items.push({
                product_id: productId,
                quantity: quantity,
                unit_price: unitPrice
            });
        }
    });
    
    // Create hidden inputs for items
    items.forEach((item, index) => {
        const productInput = document.createElement('input');
        productInput.type = 'hidden';
        productInput.name = `items[${index}][product_id]`;
        productInput.value = item.product_id;
        this.appendChild(productInput);
        
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `items[${index}][quantity]`;
        quantityInput.value = item.quantity;
        this.appendChild(quantityInput);
        
        const priceInput = document.createElement('input');
        priceInput.type = 'hidden';
        priceInput.name = `items[${index}][unit_price]`;
        priceInput.value = item.unit_price;
        this.appendChild(priceInput);
    });
    
    // Disable submit button to prevent double submission
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
});
</script>

<?= $this->endSection() ?>

