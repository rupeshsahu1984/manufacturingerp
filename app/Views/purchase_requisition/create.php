<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <div>
        <h1>Create Purchase Requisition</h1>
        <p class="text-muted mb-0">Add new purchase requisition</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('purchase-requisition') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Requisitions
        </a>
    </div>
</div>

            <form data-validate id="prForm" method="POST" action="<?= base_url('purchase-requisition/store') ?>">
                <div class="row">
                    <!-- PR Details -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="h5"><i class="fas fa-info-circle me-2"></i>PR Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">PR Number</label>
                                    <input type="text" name="pr_number" class="form-control" value="<?= $pr_number ?>" readonly>
                                </div>
                                <div class="mb-3">
                                      <label class="form-label mandatory">Department</label>
                                    <input type="text" name="department" class="form-control" required>
                                    <div class="invalid-feedback">Department is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label mandatory">Priority</label>
                                    <select name="priority" class="form-select" required>
                                        <option value="">Select Priority</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a priority.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label mandatory">Required Date</label>
                                    <input type="date" name="required_date" class="form-control" required>
                                    <div class="invalid-feedback">Required date is required.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Any additional remarks..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="h5"><i class="fas fa-list me-2"></i>Items</h5>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                    <i class="fas fa-plus me-2"></i>Add Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead class="table-warning">
                                            <tr data-selectable>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                            <!-- Items will be added here dynamically -->
                                        </tbody>
                                        <tfoot>
                                            <tr data-selectable>
                                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                <td colspan="4">
                                                    <span id="grandTotal" class="fw-bold">₹0.00</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Purchase Requisition
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm">
                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <select class="form-select" id="modalProduct" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" 
                                            data-price="<?= $product['cost_price'] ?>"
                                            data-name="<?= htmlspecialchars($product['product_name']) ?>"
                                            data-unit="<?= $product['unit'] ?>">
                                        <?= $product['product_name'] ?> (<?= $product['product_code'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="modalQuantity" min="1" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Price</label>
                                <input type="number" class="form-control" id="modalPrice" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" id="modalRemarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddItem()">Add Item</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Initialize items from old input (if validation failed) or empty array
    let items = <?= old('items', '[]') ?>;

    function updateHiddenInput() {
        // Create or update hidden input for items
        let input = document.querySelector('input[name="items"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'items';
            document.getElementById('prForm').appendChild(input);
        }
        input.value = JSON.stringify(items);
    }

    function renderTable() {
        const tbody = document.getElementById('itemsBody');
        tbody.innerHTML = '';
        let total = 0;

        items.forEach((item, index) => {
            const row = document.createElement('tr');
            const itemTotal = item.quantity * item.unit_price;
            total += itemTotal;

            row.innerHTML = `
                <td>${item.product_name}</td>
                <td>${item.quantity} ${item.unit}</td>
                <td>₹${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>₹${itemTotal.toFixed(2)}</td>
                <td>${item.remarks || '-'}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('grandTotal').innerText = '₹' + total.toFixed(2);
        updateHiddenInput();
    }

    // Product Selection Change
    document.getElementById('modalProduct').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById('modalPrice').value = option.dataset.price;
        } else {
            document.getElementById('modalPrice').value = '';
        }
    });

    function confirmAddItem() {
        const productSelect = document.getElementById('modalProduct');
        const quantity = parseFloat(document.getElementById('modalQuantity').value);
        const price = parseFloat(document.getElementById('modalPrice').value);
        const remarks = document.getElementById('modalRemarks').value;
        const selectedOption = productSelect.options[productSelect.selectedIndex];

        if (!productSelect.value) {
            alert('Please select a product');
            return;
        }
        if (!quantity || quantity <= 0) {
            alert('Please enter a valid quantity');
            return;
        }
        if (isNaN(price) || price < 0) {
            alert('Please enter a valid price');
            return;
        }

        items.push({
            product_id: productSelect.value,
            product_name: selectedOption.dataset.name,
            unit: selectedOption.dataset.unit,
            quantity: quantity,
            unit_price: price,
            remarks: remarks
        });

        renderTable();
        
        // Reset and Close Modal
        document.getElementById('addItemForm').reset();
        // Close modal manually if bootstrap object is available
        const modalEl = document.getElementById('addItemModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();
    }

    function removeItem(index) {
        items.splice(index, 1);
        renderTable();
    }

    // Initialize table on load if data exists
    document.addEventListener('DOMContentLoaded', function() {
        if (items.length > 0) {
            renderTable();
        }
    });
    </script>
<?= $this->endSection() ?> 