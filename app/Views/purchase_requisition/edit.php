<?= $this->extend('layouts/main') ?>\n\n<?= $this->section('content') ?>
    <!-- Header -->
    <div class="header">
        <div>
            <h1>Edit Purchase Requisition</h1>
            <p class="text-muted mb-0">Update purchase requisition details</p>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('purchase-requisition') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>


            <form data-validate id="prForm" method="POST" action="<?= base_url('purchase-requisition/update/' . $pr['id']) ?>">
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
                                    <input type="text" name="pr_number" class="form-control" value="<?= $pr['pr_number'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" value="<?= $pr['department'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" <?= $pr['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                        <option value="medium" <?= $pr['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                        <option value="high" <?= $pr['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                        <option value="urgent" <?= $pr['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Required Date</label>
                                    <input type="date" name="required_date" class="form-control" value="<?= $pr['required_date'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Any additional remarks..."><?= $pr['remarks'] ?></textarea>
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
                                            <!-- Items will be loaded here -->
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
                        <i class="fas fa-save me-2"></i>Update Purchase Requisition
                    </button>
                </div>
            </form>


    
    
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
                                        data-name="<?= $product['product_name'] ?>"
                                        data-unit="<?= $product['unit'] ?>">
                                    <?= $product['product_name'] ?> (<?= $product['product_code'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="modalQuantity" required min="1" step="0.01">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Unit Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="modalPrice" required min="0" step="0.01">
                                </div>
                            </div>
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
    // Initialize items from database data or old input (if validation failed)
    let items = <?= old('items') ? old('items') : json_encode($pr['items'] ?? []) ?>;
    
    // Ensure numeric values are numbers
    items = items.map(item => ({
        ...item,
        quantity: parseFloat(item.quantity),
        unit_price: parseFloat(item.unit_price),
        total_amount: parseFloat(item.quantity) * parseFloat(item.unit_price)
    }));

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
        let grandTotal = 0;

        items.forEach((item, index) => {
            const total = parseFloat(item.quantity) * parseFloat(item.unit_price);
            grandTotal += total;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-medium">${item.product_name || 'Loading...'}</div>
                    <small class="text-muted">${item.product_code || ''}</small>
                </td>
                <td>${item.quantity} ${item.unit || ''}</td>
                <td>₹${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>₹${total.toFixed(2)}</td>
                <td>${item.remarks || '-'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-remove" data-index="${index}">
                        <i class="fas fa-trash pointer-events-none"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('grandTotal').innerText = '₹' + grandTotal.toFixed(2);
        updateHiddenInput();
    }

    // Modal Item Selection Logic
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
        const quantityInput = document.getElementById('modalQuantity');
        const priceInput = document.getElementById('modalPrice');
        const remarksInput = document.getElementById('modalRemarks');

        if (!productSelect.value) {
            alert('Please select a product');
            return;
        }
        if (!quantityInput.value || quantityInput.value <= 0) {
            alert('Please enter a valid quantity');
            return;
        }

        const option = productSelect.options[productSelect.selectedIndex];
        
        const item = {
            product_id: productSelect.value,
            product_name: option.dataset.name,
            product_code: option.text.split('(')[1]?.replace(')', '') || '',
            unit: option.dataset.unit,
            quantity: parseFloat(quantityInput.value),
            unit_price: parseFloat(priceInput.value),
            remarks: remarksInput.value
        };

        items.push(item);
        renderTable();
        
        // Reset and close modal
        document.getElementById('addItemForm').reset();
        bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
    }

    // Event delegation for remove button
    document.getElementById('itemsBody').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove');
        if (btn) {
            const index = parseInt(btn.dataset.index);
            const item = items[index];
            
            if(confirm('Are you sure you want to delete this item?')) {
                if (item.id) {
                    // Item exists in DB, delete via AJAX
                    fetch(`<?= base_url('purchase-requisition/delete-item/') ?>${item.id}`)
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                items.splice(index, 1);
                                renderTable();
                            } else {
                                alert(data.message || 'Failed to delete item');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the item');
                        });
                } else {
                    // New item (not saved yet), just remove from array
                    items.splice(index, 1);
                    renderTable();
                }
            }
        }
    });

    // Initialize table on load if data exists
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('prForm');
        form.addEventListener('submit', function() {
            updateHiddenInput();
        });

        if (items.length > 0) {
            renderTable();
        }
    });
</script>
<?= $this->endSection() ?> 