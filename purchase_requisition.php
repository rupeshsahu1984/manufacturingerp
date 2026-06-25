<?php
session_start();

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'manufacturingerp';

// Connect to database
$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get raw materials for dropdown
function getRawMaterials($mysqli) {
    $result = $mysqli->query("SELECT id, product_code, product_name, unit, cost_price, gst_rate FROM products WHERE material_type = 'raw_material' AND status = 'active' ORDER BY product_name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get suppliers for dropdown
function getSuppliers($mysqli) {
    $result = $mysqli->query("SELECT id, supplier_code, supplier_name, address, phone, gst_number FROM suppliers WHERE status = 'active' ORDER BY supplier_name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$raw_materials = getRawMaterials($mysqli);
$suppliers = getSuppliers($mysqli);
$pr_number = 'PR' . date('Y') . date('m') . '001';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Requisition - PRODX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Create Purchase Requisition</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">New Purchase Requisition</h4>
                    </div>
                    <div class="card-body">
                        <form id="purchaseRequisitionForm" method="POST">
                            <!-- First Row - PR Number and Supplier Details -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="pr_number" class="form-label">PR Number</label>
                                    <input type="text" class="form-control" id="pr_number" name="pr_number" value="<?= $pr_number ?>" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="order_date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="order_date" name="order_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="supplier_id" class="form-label">Supplier Name</label>
                                    <select class="form-select" id="supplier_id" name="supplier_id" required onchange="loadSupplierDetails()">
                                        <option value="">Select Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier['id'] ?>" 
                                                    data-address="<?= htmlspecialchars($supplier['address']) ?>"
                                                    data-phone="<?= htmlspecialchars($supplier['phone']) ?>"
                                                    data-gst="<?= htmlspecialchars($supplier['gst_number']) ?>">
                                                <?= htmlspecialchars($supplier['supplier_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="supplier_mobile" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" id="supplier_mobile" name="supplier_mobile" placeholder="Mobile Number" required>
                                </div>
                            </div>

                            <!-- Second Row - Supplier Address and GSTN -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="supplier_address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="supplier_address" name="supplier_address" placeholder="Enter Address" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="supplier_gstn" class="form-label">GST Number</label>
                                    <input type="text" class="form-control" id="supplier_gstn" name="supplier_gstn" placeholder="GST Number">
                                </div>
                            </div>

                            <!-- Raw Material Details Section -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="mb-3">Raw Material Details</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="orderItemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Raw Material</th>
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
                                        <i class="fas fa-plus"></i> Add Raw Material
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
                                    <button type="submit" class="btn btn-primary">Create Purchase Requisition</button>
                                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
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
                    <option value="">Search Raw Material...</option>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let itemIndex = 0;
        let rawMaterials = <?= json_encode($raw_materials) ?>;

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem(); // Add first item by default
            setDefaultDate();
        });

        function setDefaultDate() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('order_date').value = today;
        }

        function loadSupplierDetails() {
            const supplierSelect = document.getElementById('supplier_id');
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            
            if (selectedOption.value) {
                const selectedSupplier = <?= json_encode($suppliers) ?>.find(s => s.id == selectedOption.value);
                if (selectedSupplier) {
                    document.getElementById('supplier_address').value = selectedSupplier.address || '';
                    document.getElementById('supplier_mobile').value = selectedSupplier.phone || '';
                    document.getElementById('supplier_gstn').value = selectedSupplier.gst_number || '';
                }
            } else {
                document.getElementById('supplier_address').value = '';
                document.getElementById('supplier_mobile').value = '';
                document.getElementById('supplier_gstn').value = '';
            }
        }

        function addItem() {
            const template = document.getElementById('orderItemTemplate');
            const tbody = document.getElementById('orderItemsBody');
            
            const newRow = template.content.cloneNode(true);
            
            // Update all INDEX placeholders
            newRow.querySelectorAll('[name*="INDEX"]').forEach(element => {
                element.name = element.name.replace('INDEX', itemIndex);
            });
            
            // Set up product select for this row
            const productSelect = newRow.querySelector('.product-select');
            if (productSelect) {
                updateProductSelect(productSelect);
                
                // Add change event to populate product details
                productSelect.addEventListener('change', function() {
                    updateProductInfo(this, itemIndex);
                });
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
        }

        function updateProductSelect(select) {
            select.innerHTML = '<option value="">Search Raw Material...</option>';
            rawMaterials.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.product_code} - ${product.product_name}`;
                option.dataset.costPrice = product.cost_price || 0;
                option.dataset.unit = product.unit || 'Un';
                option.dataset.gstRate = product.gst_rate || 0;
                select.appendChild(option);
            });
        }

        function removeItem(button) {
            button.closest('tr').remove();
            calculateTotals();
        }

        function updateProductInfo(select, index) {
            if (!select.value) return;
            
            const selectedProduct = rawMaterials.find(p => p.id == select.value);
            if (!selectedProduct) return;
            
            const container = select.closest('tr');
            if (!container) return;
            
            const unitPrice = selectedProduct.cost_price || 0;
            const gstRate = parseFloat(selectedProduct.gst_rate) || 0;
            
            const unitPriceInput = container.querySelector('.unit-price');
            const cgstInput = container.querySelector('.cgst');
            const sgstInput = container.querySelector('.sgst');
            const igstInput = container.querySelector('.igst');
            const unitInput = container.querySelector('.unit-input');
            const quantityInput = container.querySelector('.quantity');
            
            if (unitPriceInput) unitPriceInput.value = unitPrice;
            if (cgstInput) cgstInput.value = gstRate / 2;
            if (sgstInput) sgstInput.value = gstRate / 2;
            if (igstInput) igstInput.value = gstRate;
            if (unitInput) unitInput.value = selectedProduct.unit || 'Un';
            
            if (quantityInput && (!quantityInput.value || quantityInput.value === '0' || quantityInput.value === '')) {
                quantityInput.value = '1';
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

        // Update totals when transport fields change
        document.getElementById('transport_amount').addEventListener('input', calculateTotals);
        document.getElementById('transport_tax').addEventListener('input', calculateTotals);

        // Form validation
        document.getElementById('purchaseRequisitionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const items = document.querySelectorAll('.order-item');
            if (items.length === 0) {
                alert('Please add at least one raw material item.');
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
                alert('Purchase Requisition form is valid! This would submit to the server.');
                // this.submit(); // Uncomment when backend is ready
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

        .product-select {
            cursor: pointer;
        }

        .product-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
</body>
</html>
