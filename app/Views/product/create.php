<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    
            </div>

            <!-- Content Card -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-box me-2"></i>Material Information</h5>
                </div>
                
                <div class="card-body p-3">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                    <?php endif; ?>

                    <form data-validate action="<?= base_url('product/store') ?>" method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h6 class="mb-3 text-primary border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                                
                                <!-- Material Details Section -->
                                <div class="card mb-3 border-0 bg-light">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0"><i class="fas fa-tag me-2"></i>Material Details</h6>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="product_name" class="form-label fw-bold">Material Name *</label>
                                                <input type="text" 
                                                       class="form-control <?= session('errors.product_name') ? 'is-invalid' : '' ?>" 
                                                       id="product_name" 
                                                       name="product_name" 
                                                       value="<?= old('product_name') ?>" 
                                                       placeholder="Enter material name"
                                                       required>
                                                <?php if (session('errors.product_name')): ?>
                                                    <div class="invalid-feedback"><?= session('errors.product_name') ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="category_id" class="form-label fw-bold">Material Category *</label>
                                                <select class="form-select <?= session('errors.category_id') ? 'is-invalid' : '' ?>" 
                                                        id="category_id" 
                                                        name="category_id" 
                                                        required>
                                                    <option value="">Select Material Category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>" 
                                                                <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                                            <?= $category['category_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (session('errors.category_id')): ?>
                                                    <div class="invalid-feedback"><?= session('errors.category_id') ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="material_type" class="form-label fw-bold">Material Type *</label>
                                                <select class="form-select <?= session('errors.material_type') ? 'is-invalid' : '' ?>" 
                                                        id="material_type" 
                                                        name="material_type" 
                                                        required>
                                                    <option value="">Select Material Type</option>
                                                    <option value="raw_material" <?= old('material_type') == 'raw_material' ? 'selected' : '' ?>>Raw Material</option>
                                                    <option value="packaging" <?= old('material_type') == 'packaging' ? 'selected' : '' ?>>Packaging Material</option>
                                                    <option value="finished_goods" <?= old('material_type') == 'finished_goods' ? 'selected' : '' ?>>Finished Goods</option>
                                                    <option value="waste" <?= old('material_type') == 'waste' ? 'selected' : '' ?>>Waste Material</option>
                                                </select>
                                                <?php if (session('errors.material_type')): ?>
                                                    <div class="invalid-feedback"><?= session('errors.material_type') ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="unit" class="form-label fw-bold">Unit of Measurement *</label>
                                                <select class="form-select <?= session('errors.unit') ? 'is-invalid' : '' ?>" 
                                                        id="unit" 
                                                        name="unit" 
                                                        required>
                                                    <option value="">Select Unit</option>
                                                    <option value="kg" <?= old('unit') == 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                                                    <option value="g" <?= old('unit') == 'g' ? 'selected' : '' ?>>Gram (g)</option>
                                                    <option value="l" <?= old('unit') == 'l' ? 'selected' : '' ?>>Liter (L)</option>
                                                    <option value="ml" <?= old('unit') == 'ml' ? 'selected' : '' ?>>Milliliter (ml)</option>
                                                    <option value="pcs" <?= old('unit') == 'pcs' ? 'selected' : '' ?>>Pieces (pcs)</option>
                                                    <option value="m" <?= old('unit') == 'm' ? 'selected' : '' ?>>Meter (m)</option>
                                                    <option value="cm" <?= old('unit') == 'cm' ? 'selected' : '' ?>>Centimeter (cm)</option>
                                                    <option value="mm" <?= old('unit') == 'mm' ? 'selected' : '' ?>>Millimeter (mm)</option>
                                                    <option value="box" <?= old('unit') == 'box' ? 'selected' : '' ?>>Box</option>
                                                    <option value="pack" <?= old('unit') == 'pack' ? 'selected' : '' ?>>Pack</option>
                                                    <option value="roll" <?= old('unit') == 'roll' ? 'selected' : '' ?>>Roll</option>
                                                    <option value="sheet" <?= old('unit') == 'sheet' ? 'selected' : '' ?>>Sheet</option>
                                                </select>
                                                <?php if (session('errors.unit')): ?>
                                                    <div class="invalid-feedback"><?= session('errors.unit') ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing & Inventory Section -->
                                <div class="card mb-3 border-0 bg-light">
                                    <div class="card-header bg-success text-white py-2">
                                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Pricing & Inventory</h6>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="unit_price" class="form-label fw-bold">Unit Price (₹)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="unit_price" 
                                                           name="unit_price" 
                                                           value="<?= old('unit_price') ?>" 
                                                           step="0.01" 
                                                           min="0" 
                                                           placeholder="0.00">
                                                </div>
                                                <div class="form-text">Cost per unit</div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="selling_price" class="form-label fw-bold">Selling Price (₹)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="selling_price" 
                                                           name="selling_price" 
                                                           value="<?= old('selling_price') ?>" 
                                                           step="0.01" 
                                                           min="0" 
                                                           placeholder="0.00">
                                                </div>
                                                <div class="form-text">Sale price per unit</div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="reorder_level" class="form-label fw-bold">Reorder Level</label>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="reorder_level" 
                                                       name="reorder_level" 
                                                       value="<?= old('reorder_level') ?>" 
                                                       min="0" 
                                                       placeholder="0">
                                                <div class="form-text">Minimum stock level</div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="hsn_code" class="form-label fw-bold">HSN Code</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="hsn_code" 
                                                       name="hsn_code" 
                                                       value="<?= old('hsn_code') ?>" 
                                                       maxlength="20" 
                                                       placeholder="e.g., 8471">
                                                <div class="form-text">Harmonized System Nomenclature</div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="gst_rate" class="form-label fw-bold">GST Rate (%)</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="gst_rate" 
                                                           name="gst_rate" 
                                                           value="<?= old('gst_rate', '18.00') ?>" 
                                                           min="0" 
                                                           max="100" 
                                                           step="0.01" 
                                                           placeholder="18.00"
                                                           onchange="updateGSTBreakdown()">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="form-text">Applicable GST rate</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="cgst_rate" class="form-label fw-bold">CGST Rate (%)</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="cgst_rate" 
                                                           name="cgst_rate" 
                                                           value="<?= old('cgst_rate', '9.00') ?>" 
                                                           min="0" 
                                                           max="100" 
                                                           step="0.01" 
                                                           placeholder="9.00"
                                                           onchange="updateGSTTotal()">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="form-text">Central GST rate</div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="sgst_rate" class="form-label fw-bold">SGST Rate (%)</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="sgst_rate" 
                                                           name="sgst_rate" 
                                                           value="<?= old('sgst_rate', '9.00') ?>" 
                                                           min="0" 
                                                           max="100" 
                                                           step="0.01" 
                                                           placeholder="9.00"
                                                           onchange="updateGSTTotal()">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="form-text">State GST rate</div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label for="igst_rate" class="form-label fw-bold">IGST Rate (%)</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="igst_rate" 
                                                           name="igst_rate" 
                                                           value="<?= old('igst_rate', '18.00') ?>" 
                                                           min="0" 
                                                           max="100" 
                                                           step="0.01" 
                                                           placeholder="18.00"
                                                           onchange="updateGSTTotal()">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="form-text">Inter-state GST rate</div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="waste_percentage" class="form-label fw-bold">Waste Percentage (%)</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="waste_percentage" 
                                                           name="waste_percentage" 
                                                           value="<?= old('waste_percentage') ?>" 
                                                           min="0" 
                                                           max="100" 
                                                           step="0.1" 
                                                           placeholder="0.0">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="form-text">Production waste</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Properties Section -->
                                <div class="card mb-3 border-0 bg-light">
                                    <div class="card-header bg-info text-white py-2">
                                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Status & Properties</h6>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="status" class="form-label fw-bold">Material Status *</label>
                                                <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" 
                                                        id="status" 
                                                        name="status" 
                                                        required>
                                                    <option value="">Select Status</option>
                                                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                                <?php if (session('errors.status')): ?>
                                                    <div class="invalid-feedback"><?= session('errors.status') ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="is_recyclable" 
                                                           name="is_recyclable" 
                                                           value="1" 
                                                           <?= old('is_recyclable') ? 'checked' : '' ?>>
                                                    <label class="form-check-label fw-bold" for="is_recyclable">
                                                        <i class="fas fa-recycle me-2"></i>Recyclable Material
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-4">
                                <h6 class="mb-3 text-primary border-bottom pb-2">
                                    <i class="fas fa-file-alt me-2"></i>Additional Information
                                </h6>
                                
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-warning text-dark py-2">
                                        <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Material Description</h6>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="mb-3">
                                            <label for="description" class="form-label fw-bold">Detailed Description</label>
                                            <textarea class="form-control" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="12" 
                                                      placeholder="Enter detailed material description, specifications, usage notes, or any additional information..."><?= old('description') ?></textarea>
                                        </div>

                                        <div class="alert alert-info small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Help:</strong> Include material specifications, usage guidelines, safety notes, or any relevant information that helps identify and use this material properly.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?= base_url('product') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Material
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // GST Calculation Functions
        function updateGSTBreakdown() {
            const gstRate = parseFloat(document.getElementById('gst_rate').value) || 0;
            const cgstRate = gstRate / 2;
            const sgstRate = gstRate / 2;
            const igstRate = gstRate;
            
            document.getElementById('cgst_rate').value = cgstRate.toFixed(2);
            document.getElementById('sgst_rate').value = sgstRate.toFixed(2);
            document.getElementById('igst_rate').value = igstRate.toFixed(2);
        }
        
        function updateGSTTotal() {
            const cgstRate = parseFloat(document.getElementById('cgst_rate').value) || 0;
            const sgstRate = parseFloat(document.getElementById('sgst_rate').value) || 0;
            const igstRate = parseFloat(document.getElementById('igst_rate').value) || 0;
            
            // For same-state transactions, use CGST + SGST
            // For inter-state transactions, use IGST
            const gstTotal = Math.max(cgstRate + sgstRate, igstRate);
            
            document.getElementById('gst_rate').value = gstTotal.toFixed(2);
        }
        
        // Initialize GST breakdown on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateGSTBreakdown();
        });
    </script>
    
<?= $this->endSection() ?> 