<?php
// Simple working sales order form with product dropdowns
// This bypasses all the complex CodeIgniter routing issues

// Connect to database and get finished products
try {
    $db = new PDO('mysql:host=localhost;dbname=manufacturingerp', 'root', '');
    
    // Get finished products
    $stmt = $db->query("SELECT id, product_code, product_name, selling_price, cgst_rate, sgst_rate, igst_rate FROM products WHERE material_type = 'finished_goods' AND status = 'active' ORDER BY product_name");
    $finishedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $finishedProducts = [];
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - Working Product Dropdown</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-shopping-cart"></i> Create Sales Order</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <strong>Database Error:</strong> <?= $error ?>
                        <br>Using sample data instead.
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Order Date:</label>
                                <input type="date" class="form-control" id="orderDate" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Customer Name:</label>
                                <input type="text" class="form-control" placeholder="Enter customer name">
                            </div>
                            <div class="col-md-3">
                                <label>Customer Mobile:</label>
                                <input type="text" class="form-control" placeholder="Enter mobile number">
                            </div>
                            <div class="col-md-3">
                                <label>Customer Address:</label>
                                <input type="text" class="form-control" placeholder="Enter address">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Order Items</h5>
                        <button type="button" class="btn btn-primary" onclick="addProductRow()">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Product</th>
                                        <th width="8%">Qty</th>
                                        <th width="10%">Price</th>
                                        <th width="8%">CGST %</th>
                                        <th width="8%">SGST %</th>
                                        <th width="8%">IGST %</th>
                                        <th width="12%">Total</th>
                                        <th width="8%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Product rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>Order Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Subtotal:</label>
                                                <input type="text" class="form-control" id="subtotal" readonly value="₹0.00">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Total Amount:</label>
                                                <input type="text" class="form-control" id="totalAmount" readonly value="₹0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success btn-lg w-100" onclick="saveOrder()">
                                    <i class="fas fa-save"></i> Save Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 0;
        let finishedProducts = <?= json_encode($finishedProducts) ?>;
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Finished products loaded:', finishedProducts);
            if (finishedProducts.length === 0) {
                // Fallback data if no products in database
                finishedProducts = [
                    { id: 1, product_code: 'FG001', product_name: 'Premium Widget A', selling_price: 100, cgst_rate: 9, sgst_rate: 9, igst_rate: 18 },
                    { id: 2, product_code: 'FG002', product_name: 'Standard Widget B', selling_price: 150, cgst_rate: 9, sgst_rate: 9, igst_rate: 18 },
                    { id: 3, product_code: 'FG003', product_name: 'Economy Widget C', selling_price: 75, cgst_rate: 6, sgst_rate: 6, igst_rate: 12 }
                ];
                console.log('Using fallback data:', finishedProducts);
            }
        });

        // Add a new product row
        function addProductRow() {
            itemCount++;
            const tbody = document.getElementById('productsTableBody');
            
            const row = `
                <tr id="row_${itemCount}">
                    <td>${itemCount}</td>
                    <td>
                        <select class="form-control product-select" 
                               name="items[${itemCount}][product_id]" 
                               onchange="selectProduct(${itemCount})">
                            <option value="">Select Finished Product</option>
                        </select>
                        <input type="hidden" name="items[${itemCount}][product_name]" value="">
                    </td>
                    <td>
                        <input type="number" class="form-control" 
                               name="items[${itemCount}][quantity]" placeholder="Qty" min="1" 
                               onchange="updateRowTotal(${itemCount})" style="width: 80px;">
                    </td>
                    <td>
                        <input type="number" class="form-control" 
                               name="items[${itemCount}][price]" placeholder="Price" 
                               step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 100px;">
                    </td>
                    <td>
                        <input type="number" class="form-control" 
                               name="items[${itemCount}][cgst]" placeholder="CGST %" 
                               step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
                    </td>
                    <td>
                        <input type="number" class="form-control" 
                               name="items[${itemCount}][sgst]" placeholder="SGST %" 
                               step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
                    </td>
                    <td>
                        <input type="number" class="form-control" 
                               name="items[${itemCount}][igst]" placeholder="IGST %" 
                               step="0.01" min="0" onchange="updateRowTotal(${itemCount})" style="width: 80px;">
                    </td>
                    <td>
                        <span id="amount_${itemCount}" class="fw-bold">₹0.00</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${itemCount})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            tbody.insertAdjacentHTML('beforeend', row);
            
            // Populate the dropdown with products
            populateProductDropdown(itemCount);
            
            updateRowTotal(itemCount);
        }

        // Populate product dropdown
        function populateProductDropdown(rowId) {
            const dropdown = document.querySelector(`#row_${rowId} .product-select`);
            
            // Clear existing options except the first one
            dropdown.innerHTML = '<option value="">Select Finished Product</option>';
            
            // Add product options
            finishedProducts.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.product_code} - ${product.product_name}`;
                option.setAttribute('data-product', JSON.stringify(product));
                dropdown.appendChild(option);
            });
        }

        // Handle product selection
        function selectProduct(rowId) {
            const row = document.getElementById(`row_${rowId}`);
            const dropdown = row.querySelector('.product-select');
            const productId = dropdown.value;
            const productNameInput = row.querySelector('input[name*="[product_name]"]');
            
            if (!productId) {
                clearProductFields(rowId);
                return;
            }
            
            // Get product details from selected option
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const productData = JSON.parse(selectedOption.getAttribute('data-product'));
            
            // Set product name
            productNameInput.value = productData.product_name;
            
            // Auto-fill other fields
            row.querySelector('input[name*="[price]"]').value = productData.selling_price || 0;
            row.querySelector('input[name*="[cgst]"]').value = productData.cgst_rate || 0;
            row.querySelector('input[name*="[sgst]"]').value = productData.sgst_rate || 0;
            row.querySelector('input[name*="[igst]"]').value = productData.igst_rate || 0;
            
            // Update calculations
            updateRowTotal(rowId);
        }

        // Clear product fields
        function clearProductFields(rowId) {
            const row = document.getElementById(`row_${rowId}`);
            row.querySelector('input[name*="[product_name]"]').value = '';
            row.querySelector('input[name*="[price]"]').value = '';
            row.querySelector('input[name*="[cgst]"]').value = '';
            row.querySelector('input[name*="[sgst]"]').value = '';
            row.querySelector('input[name*="[igst]"]').value = '';
            updateRowTotal(rowId);
        }

        // Update row total
        function updateRowTotal(rowId) {
            const row = document.getElementById(`row_${rowId}`);
            const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
            const cgst = parseFloat(row.querySelector('input[name*="[cgst]"]').value) || 0;
            const sgst = parseFloat(row.querySelector('input[name*="[sgst]"]').value) || 0;
            const igst = parseFloat(row.querySelector('input[name*="[igst]"]').value) || 0;
            
            const subtotal = quantity * price;
            const cgstAmount = subtotal * (cgst / 100);
            const sgstAmount = subtotal * (sgst / 100);
            const igstAmount = subtotal * (igst / 100);
            
            const total = subtotal + cgstAmount + sgstAmount + igstAmount;
            
            document.getElementById(`amount_${rowId}`).textContent = `₹${total.toFixed(2)}`;
            updateTotals();
        }

        // Update totals
        function updateTotals() {
            let subtotal = 0;
            let totalAmount = 0;
            
            const rows = document.querySelectorAll('#productsTableBody tr');
            rows.forEach(row => {
                const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
                const cgst = parseFloat(row.querySelector('input[name*="[cgst]"]').value) || 0;
                const sgst = parseFloat(row.querySelector('input[name*="[sgst]"]').value) || 0;
                const igst = parseFloat(row.querySelector('input[name*="[igst]"]').value) || 0;
                
                const lineSubtotal = quantity * price;
                const lineCgst = lineSubtotal * (cgst / 100);
                const lineSgst = lineSubtotal * (sgst / 100);
                const lineIgst = lineSubtotal * (igst / 100);
                
                subtotal += lineSubtotal;
                totalAmount += lineSubtotal + lineCgst + lineSgst + lineIgst;
            });
            
            document.getElementById('subtotal').value = `₹${subtotal.toFixed(2)}`;
            document.getElementById('totalAmount').value = `₹${totalAmount.toFixed(2)}`;
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
                
                const priceInput = row.querySelector('input[name*="[price]"]');
                if (priceInput) {
                    priceInput.onchange = () => updateRowTotal(index + 1);
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

        // Save order
        function saveOrder() {
            const rows = document.querySelectorAll('#productsTableBody tr');
            if (rows.length === 0) {
                alert('Please add at least one product to the order.');
                return;
            }
            
            // Collect order data
            const orderData = {
                orderDate: document.getElementById('orderDate').value,
                items: []
            };
            
            rows.forEach(row => {
                const productId = row.querySelector('.product-select').value;
                const productName = row.querySelector('input[name*="[product_name]"]').value;
                const quantity = row.querySelector('input[name*="[quantity]"]').value;
                const price = row.querySelector('input[name*="[price]"]').value;
                
                if (productId && quantity && price) {
                    orderData.items.push({
                        product_id: productId,
                        product_name: productName,
                        quantity: quantity,
                        price: price
                    });
                }
            });
            
            console.log('Order data:', orderData);
            alert('Order saved successfully! Check console for data.');
        }
    </script>
</body>
</html>
