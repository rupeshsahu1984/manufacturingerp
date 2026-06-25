<?= $this->extend('layouts/main') ?>\n\n<?= $this->section('content') ?>
    
            </div>

            <!-- Product Information -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr data-selectable>
                                    <td class="fw-bold" style="width: 150px;">Material Code:</td>
                                    <td><span class="badge bg-primary"><?= $product['product_code'] ?></span></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Material Name:</td>
                                    <td><?= $product['product_name'] ?></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Category:</td>
                                    <td><?= isset($product['category_name']) ? $product['category_name'] : 'N/A' ?></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Material Type:</td>
                                    <td>
                                        <span class="material-type-badge material-type-<?= $product['material_type'] ?>">
                                            <?= ucwords(str_replace('_', ' ', $product['material_type'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Unit:</td>
                                    <td><?= $product['unit'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr data-selectable>
                                    <td class="fw-bold" style="width: 150px;">Unit Price:</td>
                                    <td>
                                        <?php if ($product['unit_price'] > 0): ?>
                                            <span class="text-success fw-bold">₹<?= number_format($product['unit_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Selling Price:</td>
                                    <td>
                                        <?php if (!empty($product['selling_price']) && $product['selling_price'] > 0): ?>
                                            <span class="text-primary fw-bold">₹<?= number_format($product['selling_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Reorder Level:</td>
                                    <td><?= isset($product['reorder_level']) ? $product['reorder_level'] : 'Not set' ?></td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Waste Percentage:</td>
                                    <td>
                                        <?php if ($product['waste_percentage'] > 0): ?>
                                            <span class="text-warning"><?= $product['waste_percentage'] ?>%</span>
                                        <?php else: ?>
                                            <span class="text-muted">0%</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="status-badge <?= $product['status'] == 'active' ? 'status-active' : 'status-inactive' ?>">
                                            <?= ucfirst($product['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr data-selectable>
                                    <td class="fw-bold">Recyclable:</td>
                                    <td>
                                        <?php if ($product['is_recyclable']): ?>
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-times-circle"></i> No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($product['description'])): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary mb-2">Description:</h6>
                                <p class="text-muted"><?= $product['description'] ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- BOM Information (if applicable) -->
            <?php if (isset($product['bom']) && !empty($product['bom'])): ?>
                <div class="content-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list-alt me-2"></i>Bill of Materials</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>BOM Number:</strong> <?= $product['bom']['bom_number'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Version:</strong> <?= $product['bom']['version'] ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($product['bom']['description'])): ?>
                            <div class="mb-3">
                                <strong>Description:</strong> <?= $product['bom']['description'] ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($product['bom']['items']) && !empty($product['bom']['items'])): ?>
                            <h6 class="text-primary mb-3">Material Components:</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr data-selectable>
                                            <th>Material</th>
                                            <th>Quantity Required</th>
                                            <th>Waste %</th>
                                            <th>Total Quantity</th>
                                            <th>Unit Cost</th>
                                            <th>Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($product['bom']['items'] as $item): ?>
                                            <tr data-selectable>
                                                <td>
                                                    <strong><?= $item['material_name'] ?></strong><br>
                                                    <small class="text-muted"><?= $item['material_code'] ?></small>
                                                </td>
                                                <td><?= $item['quantity_required'] ?> <?= $item['material_unit'] ?></td>
                                                <td>
                                                    <?php if ($item['waste_percentage'] > 0): ?>
                                                        <span class="text-warning"><?= $item['waste_percentage'] ?>%</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">0%</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $item['total_quantity'] ?> <?= $item['material_unit'] ?></td>
                                                <td>₹<?= number_format($item['unit_cost'], 2) ?></td>
                                                <td><strong>₹<?= number_format($item['total_cost'], 2) ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No BOM items found for this material.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Production Information -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-industry me-2"></i>Production Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="stat-icon info mb-2">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="stat-value"><?= date('M Y', strtotime($product['created_at'])) ?></div>
                                <div class="stat-label">Created</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="stat-icon primary mb-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="stat-value"><?= isset($product['created_by_name']) ? $product['created_by_name'] : 'System' ?></div>
                                <div class="stat-label">Created By</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="stat-icon warning mb-2">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-value"><?= date('M Y', strtotime($product['updated_at'])) ?></div>
                                <div class="stat-label">Last Updated</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="stat-icon success mb-2">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-value">
                                    <a href="<?= base_url('product/performance-report/' . $product['id']) ?>" 
                                       class="text-decoration-none text-white">
                                        View Report
                                    </a>
                                </div>
                                <div class="stat-label">Performance</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
<?= $this->endSection() ?> 