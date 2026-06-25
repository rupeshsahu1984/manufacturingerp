<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1">Create Sales Order</h1>
            <p class="text-muted mb-0">Prepare a sales order from active customers, products, and warehouses.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= base_url('sales/orders/store') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Select customer</option>
                            <?php foreach (($customers ?? []) as $customer): ?>
                                <option value="<?= esc($customer['id']) ?>"><?= esc($customer['customer_name'] ?? $customer['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Order Date</label>
                        <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Product</label>
                        <select name="items[0][product_id]" class="form-select" required>
                            <option value="">Select product</option>
                            <?php foreach (($products ?? []) as $product): ?>
                                <option value="<?= esc($product['id']) ?>"><?= esc($product['product_name'] ?? $product['item_name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[0][quantity]" class="form-control" min="1" step="0.01" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="items[0][unit_price]" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Warehouse</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">Select warehouse</option>
                            <?php foreach (($warehouses ?? []) as $warehouse): ?>
                                <option value="<?= esc($warehouse['id']) ?>"><?= esc($warehouse['warehouse_name'] ?? $warehouse['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Order</button>
                    <a href="<?= base_url('sales/orders') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
