<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sales-return') ?>">Sales Returns</a></li>
                        <li class="breadcrumb-item active">Create Sales Return</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Sales Return</h4>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-undo me-2"></i>Return Items</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="addItem()">
                    <i class="fas fa-plus me-1"></i>Add Item
                </button>
            </div>
            <div class="card-body">
                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="productsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">SL</th>
                                <th width="35%">Product</th>
                                <th width="15%">Unit Price</th>
                                <th width="15%">Quantity</th>
                                <th width="20%">Line Total</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                </div>
                
                <div id="emptyState" class="text-center py-4">
                    <p class="text-muted mb-0">No items added. Click "Add Item" to start.</p>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Overall Return Reason</h5>
            </div>
            <div class="card-body">
                <textarea class="form-control" name="return_reason" id="return_reason_main" rows="3" placeholder="Enter the reason for this return..." form="salesReturnForm" required></textarea>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Return Details -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Return Details</h5>
            </div>
            <div class="card-body">
                <form id="salesReturnForm" action="<?= base_url('sales-return/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Return Number</label>
                        <input type="text" class="form-control" id="returnNumber" name="return_number" value="<?= esc($return_number) ?>" readonly>
                        <div class="form-text text-muted">Auto-generated</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Invoice</label>
                        <select class="form-select" id="invoiceSelect" name="invoice_id" required>
                            <option value="">Select Invoice</option>
                            <?php foreach ($invoices as $invoice): ?>
                                <option value="<?= $invoice['id'] ?>" data-customer-id="<?= $invoice['customer_id'] ?>">
                                    <?= esc($invoice['invoice_number']) ?> - <?= esc($invoice['customer_name'] ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                        <label class="form-label fw-bold text-danger">* Return Date</label>
                        <input type="date" class="form-control" id="returnDate" name="return_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">* Status</label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="processed">Processed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Return Summary -->
                    <div class="border-top pt-3 mt-3">
                        <h6 class="fw-bold mb-3">Return Summary</h6>
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
                            <span>Total Refundable:</span>
                            <span id="totalAmount" class="text-primary">₹0.00</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-check me-2"></i>Create Sales Return
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
    // Set default date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('returnDate').value = today;
    
    // Handle invoice selection
    document.getElementById('invoiceSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const customerId = selectedOption.getAttribute('data-customer-id');
            if (customerId) {
                document.getElementById('customerSelect').value = customerId;
            }
        }
    });

    // Handle form submission
    document.getElementById('salesReturnForm').addEventListener('submit', function(e) {
        // Collect all item data
        const items = [];
        let hasError = false;

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
            } else {
                hasError = true;
            }
        });

        if (items.length === 0) {
            alert('Please add at least one item to return.');
            e.preventDefault();
            return false;
        }

        if (hasError) {
            alert('Please fill in all item details.');
            e.preventDefault();
            return false;
        }

        // Add hidden inputs for items
        items.forEach((item, index) => {
            const container = document.getElementById('salesReturnForm');
            
            const pInput = document.createElement('input');
            pInput.type = 'hidden';
            pInput.name = `items[${index}][product_id]`;
            pInput.value = item.product_id;
            container.appendChild(pInput);
            
            const qInput = document.createElement('input');
            qInput.type = 'hidden';
            qInput.name = `items[${index}][quantity]`;
            qInput.value = item.quantity;
            container.appendChild(qInput);
            
            const upInput = document.createElement('input');
            upInput.type = 'hidden';
            upInput.name = `items[${index}][unit_price]`;
            upInput.value = item.unit_price;
            container.appendChild(upInput);
        });

        // Add main return reason if not already present
        if (!document.querySelector('input[name="return_reason"]')) {
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'return_reason';
            reasonInput.value = document.getElementById('return_reason_main').value;
            this.appendChild(reasonInput);
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    });
});

// Add new item row
function addItem() {
    itemCount++;
    document.getElementById('emptyState').classList.add('d-none');
    
    const tbody = document.getElementById('productsTableBody');
    const row = document.createElement('tr');
    row.id = `itemRow${itemCount}`;
    
    row.innerHTML = `
        <td>${itemCount}</td>
        <td>
            <select class="form-select form-select-sm product-select" required onchange="updateUnitPrice(${itemCount}, this)">
                <option value="">Select Product</option>
                ${products.map(p => `
                    <option value="${p.id}" data-price="${p.selling_price || p.unit_price || 0}">
                        ${p.product_code} - ${p.product_name}
                    </option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm unit-price" step="0.01" min="0" required onchange="updateLineTotal(${itemCount})">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm quantity" step="0.01" min="0.01" required onchange="updateLineTotal(${itemCount})">
        </td>
        <td>
            <span class="line-total-display">₹0.00</span>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCount})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    renumberRows();
}

function updateUnitPrice(rowId, select) {
    const row = document.getElementById(`itemRow${rowId}`);
    const price = select.options[select.selectedIndex].getAttribute('data-price') || 0;
    row.querySelector('.unit-price').value = parseFloat(price).toFixed(2);
    updateLineTotal(rowId);
}

function updateLineTotal(rowId) {
    const row = document.getElementById(`itemRow${rowId}`);
    const price = parseFloat(row.querySelector('.unit-price').value) || 0;
    const qty = parseFloat(row.querySelector('.quantity').value) || 0;
    const total = price * qty;
    
    row.querySelector('.line-total-display').textContent = '₹' + total.toFixed(2);
    updateSummary();
}

function updateSummary() {
    let subtotal = 0;
    document.querySelectorAll('#productsTableBody tr').forEach(row => {
        const price = parseFloat(row.querySelector('.unit-price').value) || 0;
        const qty = parseFloat(row.querySelector('.quantity').value) || 0;
        subtotal += price * qty;
    });
    
    const gst = subtotal * 0.18;
    const total = subtotal + gst;
    
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('gstAmount').textContent = '₹' + gst.toFixed(2);
    document.getElementById('totalAmount').textContent = '₹' + total.toFixed(2);
}

function removeItem(rowId) {
    document.getElementById(`itemRow${rowId}`).remove();
    renumberRows();
    updateSummary();
    
    if (document.querySelectorAll('#productsTableBody tr').length === 0) {
        document.getElementById('emptyState').classList.remove('d-none');
    }
}

function renumberRows() {
    const rows = document.querySelectorAll('#productsTableBody tr');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
    });
}
</script>

<?= $this->endSection() ?>
