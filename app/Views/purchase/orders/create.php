<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1">Create Purchase Order</h1>
            <p class="text-muted mb-0">Create a supplier purchase order from available requisitions and items.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= base_url('purchase/orders/store') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Select supplier</option>
                            <?php foreach (($suppliers ?? []) as $supplier): ?>
                                <option value="<?= esc($supplier['id']) ?>"><?= esc($supplier['supplier_name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Terms</label>
                        <input type="text" name="payment_terms" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Item</label>
                        <select name="items[]" class="form-select" required>
                            <option value="">Select item</option>
                            <?php foreach (($items ?? []) as $item): ?>
                                <option value="<?= esc($item['id']) ?>"><?= esc($item['item_name'] ?? $item['product_name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantities[]" class="form-control" min="1" step="0.01" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rate</label>
                        <input type="number" name="rates[]" class="form-control" min="0" step="0.01" value="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tax</label>
                        <input type="number" name="taxes[]" class="form-control" min="0" step="0.01" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Delivery Terms</label>
                        <input type="text" name="delivery_terms" class="form-control">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Order</button>
                    <a href="<?= base_url('purchase/orders') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
