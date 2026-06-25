<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-cogs me-3"></i>Production Settings Guide</h1>
    <div class="header-actions">
        <a href="<?= base_url('production-settings') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Production Settings
        </a>
        <a href="<?= base_url('help/video-production-settings') ?>" class="btn btn-outline-info ms-2" target="_blank">
            <i class="fas fa-play"></i> Watch Video
        </a>
    </div>
</div>

<!-- Table of Contents -->
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Table of Contents</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><a href="#overview" class="text-decoration-none">📋 Overview</a></li>
                    <li><a href="#production-planning" class="text-decoration-none">📊 Production Planning</a></li>
                    <li><a href="#raw-materials" class="text-decoration-none">📦 Raw Materials</a></li>
                    <li><a href="#waste-materials" class="text-decoration-none">♻️ Waste Materials</a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><a href="#calculations" class="text-decoration-none">🧮 Calculations</a></li>
                    <li><a href="#best-practices" class="text-decoration-none">💡 Best Practices</a></li>
                    <li><a href="#common-issues" class="text-decoration-none">⚠️ Common Issues</a></li>
                    <li><a href="#video-tutorials" class="text-decoration-none">🎥 Video Tutorials</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Overview Section -->
<div class="content-card mb-4" id="overview">
    <div class="card-header">
        <h5><i class="fas fa-info-circle me-2"></i>Overview</h5>
    </div>
    <div class="card-body">
        <h6>What are Production Settings?</h6>
        <p>Production Settings (also known as Bill of Materials or BOM) define how to manufacture a finished product. They specify the exact quantities of raw materials needed, waste materials generated, and production parameters for efficient manufacturing.</p>
        
        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Production Settings are the blueprint for manufacturing. Accurate settings ensure efficient production and proper stock management.
        </div>

        <h6>Key Components:</h6>
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <li><strong>Production Planning:</strong> Quantity, efficiency, and batch settings</li>
                    <li><strong>Raw Materials:</strong> Materials consumed in production</li>
                    <li><strong>Waste Materials:</strong> Materials generated during production</li>
                    <li><strong>Calculations:</strong> Automatic cost and quantity calculations</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul>
                    <li><strong>Stock Management:</strong> Automatic stock updates</li>
                    <li><strong>Cost Tracking:</strong> Material and waste costs</li>
                    <li><strong>Efficiency Planning:</strong> Account for production losses</li>
                    <li><strong>Waste Management:</strong> Track and value waste materials</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Production Planning Section -->
<div class="content-card mb-4" id="production-planning">
    <div class="card-header">
        <h5><i class="fas fa-chart-line me-2"></i>Production Planning</h5>
    </div>
    <div class="card-body">
        <h6>Production Planning Fields</h6>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Production Quantity</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Base quantity for calculations</p>
                        <p><strong>Example:</strong> 100 units</p>
                        <p><strong>Use:</strong> All material quantities are calculated per this base quantity</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Batch Size</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Optimal production batch size</p>
                        <p><strong>Example:</strong> 10 units per batch</p>
                        <p><strong>Use:</strong> For production planning and efficiency</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-percentage me-2"></i>Production Efficiency</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Expected production efficiency</p>
                        <p><strong>Example:</strong> 95% (5% waste/loss)</p>
                        <p><strong>Use:</strong> Adjusts material requirements for efficiency losses</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-ruler me-2"></i>Base Unit</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Unit of measurement for finished product</p>
                        <p><strong>Example:</strong> pieces, kg, liters</p>
                        <p><strong>Use:</strong> Standardizes production measurements</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h6>Efficiency Calculation Example</h6>
        <div class="alert alert-warning">
            <strong>Formula:</strong> Adjusted Quantity = Base Quantity ÷ Efficiency Factor<br>
            <strong>Example:</strong> If you need 1kg of material at 95% efficiency:<br>
            1kg ÷ 0.95 = 1.053kg (adjusted for efficiency losses)
        </div>
    </div>
</div>

<!-- Raw Materials Section -->
<div class="content-card mb-4" id="raw-materials">
    <div class="card-header">
        <h5><i class="fas fa-box me-2"></i>Raw Materials Configuration</h5>
    </div>
    <div class="card-body">
        <h6>Setting Up Raw Materials</h6>
        
        <div class="row">
            <div class="col-md-8">
                <h6>Step-by-Step Process:</h6>
                <ol>
                    <li><strong>Select Raw Material:</strong>
                        <ul>
                            <li>Choose from available raw materials in the dropdown</li>
                            <li>Only raw materials and packaging materials appear here</li>
                            <li>Materials must be active and have stock</li>
                        </ul>
                    </li>
                    
                    <li><strong>Set Required Quantity:</strong>
                        <ul>
                            <li>Enter quantity needed per finished product</li>
                            <li>Use precise measurements (e.g., 1.000 kg)</li>
                            <li>System will scale this for production quantity</li>
                        </ul>
                    </li>
                    
                    <li><strong>Configure Waste Amount:</strong>
                        <ul>
                            <li>Enter exact waste amount per product (e.g., 0.050 kg)</li>
                            <li>System calculates waste percentage automatically</li>
                            <li>Waste is added to total material requirement</li>
                        </ul>
                    </li>
                    
                    <li><strong>Review Calculations:</strong>
                        <ul>
                            <li>Check waste percentage calculation</li>
                            <li>Verify total quantity per product</li>
                            <li>Review unit costs and total costs</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <div class="col-md-4">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>Quantities are per finished product</li>
                        <li>Waste amounts are exact quantities</li>
                        <li>System calculates percentages automatically</li>
                        <li>All calculations include efficiency adjustments</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <h6>Example: Raw Material Configuration</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Field</th>
                        <th>Example Value</th>
                        <th>Calculation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Raw Material</td>
                        <td>Steel Sheet</td>
                        <td>Selected from dropdown</td>
                    </tr>
                    <tr>
                        <td>Required Quantity</td>
                        <td>1.000 kg</td>
                        <td>Per finished product</td>
                    </tr>
                    <tr>
                        <td>Waste Amount</td>
                        <td>0.050 kg</td>
                        <td>Exact waste per product</td>
                    </tr>
                    <tr>
                        <td>Waste Percentage</td>
                        <td>5.00%</td>
                        <td>0.050 ÷ 1.000 × 100</td>
                    </tr>
                    <tr>
                        <td>Total Quantity</td>
                        <td>1.050 kg</td>
                        <td>1.000 + 0.050</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Waste Materials Section -->
<div class="content-card mb-4" id="waste-materials">
    <div class="card-header">
        <h5><i class="fas fa-recycle me-2"></i>Waste Materials Configuration</h5>
    </div>
    <div class="card-body">
        <h6>Setting Up Waste Materials</h6>
        
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Important:</strong> Waste materials are separate from raw material waste. They are materials that are generated during production and can be tracked, valued, and potentially reused or sold.
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <h6>Step-by-Step Process:</h6>
                <ol>
                    <li><strong>Select Waste Material:</strong>
                        <ul>
                            <li>Choose from available waste materials in the dropdown</li>
                            <li>Only materials marked as "waste" type appear here</li>
                            <li>These materials will be generated during production</li>
                        </ul>
                    </li>
                    
                    <li><strong>Set Generated Quantity:</strong>
                        <ul>
                            <li>Enter quantity generated per finished product</li>
                            <li>This is the amount of waste material created</li>
                            <li>System will add this to waste material stock</li>
                        </ul>
                    </li>
                    
                    <li><strong>Review Value Calculations:</strong>
                        <ul>
                            <li>System calculates value based on waste material cost</li>
                            <li>Total value = quantity × unit cost</li>
                            <li>This helps track waste material value</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <div class="col-md-4">
                <div class="alert alert-success">
                    <h6><i class="fas fa-lightbulb me-2"></i>Benefits</h6>
                    <ul class="mb-0">
                        <li>Track waste generation</li>
                        <li>Calculate waste value</li>
                        <li>Plan waste disposal</li>
                        <li>Identify reuse opportunities</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <h6>Example: Waste Material Configuration</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Field</th>
                        <th>Example Value</th>
                        <th>Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Waste Material</td>
                        <td>Scrap Metal</td>
                        <td>Generated during cutting</td>
                    </tr>
                    <tr>
                        <td>Generated Quantity</td>
                        <td>0.090 kg</td>
                        <td>Per finished product</td>
                    </tr>
                    <tr>
                        <td>Unit Cost</td>
                        <td>₹5.00/kg</td>
                        <td>Waste material value</td>
                    </tr>
                    <tr>
                        <td>Total Value</td>
                        <td>₹0.45</td>
                        <td>0.090 × ₹5.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <h6>Waste Material vs Raw Material Waste</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">Raw Material Waste</h6>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Part of raw material consumption</li>
                            <li>Reduces raw material stock</li>
                            <li>Increases material cost</li>
                            <li>Example: 50g waste from 1kg steel</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">Waste Materials</h6>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Separate materials generated</li>
                            <li>Increases waste material stock</li>
                            <li>Can have value and be tracked</li>
                            <li>Example: 90g scrap metal generated</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calculations Section -->
<div class="content-card mb-4" id="calculations">
    <div class="card-header">
        <h5><i class="fas fa-calculator me-2"></i>Automatic Calculations</h5>
    </div>
    <div class="card-body">
        <h6>System Calculations</h6>
        
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-percentage text-primary"></i> Waste Percentage Calculation</h6>
                <div class="alert alert-primary">
                    <strong>Formula:</strong> Waste % = (Waste Amount ÷ Required Quantity) × 100<br>
                    <strong>Example:</strong> (0.050 kg ÷ 1.000 kg) × 100 = 5.00%
                </div>
                
                <h6><i class="fas fa-plus text-success"></i> Total Quantity Calculation</h6>
                <div class="alert alert-success">
                    <strong>Formula:</strong> Total Qty = Required Qty + Waste Amount<br>
                    <strong>Example:</strong> 1.000 kg + 0.050 kg = 1.050 kg
                </div>
            </div>
            
            <div class="col-md-6">
                <h6><i class="fas fa-chart-line text-warning"></i> Efficiency Adjustment</h6>
                <div class="alert alert-warning">
                    <strong>Formula:</strong> Adjusted Qty = Base Qty ÷ Efficiency Factor<br>
                    <strong>Example:</strong> 1.000 kg ÷ 0.95 = 1.053 kg
                </div>
                
                <h6><i class="fas fa-rupee-sign text-info"></i> Cost Calculation</h6>
                <div class="alert alert-info">
                    <strong>Formula:</strong> Total Cost = Total Qty × Unit Cost<br>
                    <strong>Example:</strong> 1.050 kg × ₹10.00 = ₹10.50
                </div>
            </div>
        </div>
        
        <h6>Production Scaling Example</h6>
        <p>When you set production quantity to 100 units:</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th>Per Product</th>
                        <th>For 100 Products</th>
                        <th>Calculation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Raw Material 1</td>
                        <td>1.050 kg</td>
                        <td>105.000 kg</td>
                        <td>1.050 × 100</td>
                    </tr>
                    <tr>
                        <td>Raw Material 2</td>
                        <td>2.040 kg</td>
                        <td>204.000 kg</td>
                        <td>2.040 × 100</td>
                    </tr>
                    <tr>
                        <td>Waste Material 1</td>
                        <td>0.090 kg</td>
                        <td>9.000 kg</td>
                        <td>0.090 × 100</td>
                    </tr>
                    <tr>
                        <td>Waste Material 2</td>
                        <td>0.080 kg</td>
                        <td>8.000 kg</td>
                        <td>0.080 × 100</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Best Practices Section -->
<div class="content-card mb-4" id="best-practices">
    <div class="card-header">
        <h5><i class="fas fa-lightbulb me-2"></i>Best Practices</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-check-circle text-success"></i> Do's</h6>
                <ul>
                    <li>Use precise measurements for quantities</li>
                    <li>Set realistic production efficiency</li>
                    <li>Include all raw materials needed</li>
                    <li>Track all waste materials generated</li>
                    <li>Review calculations before saving</li>
                    <li>Update settings when processes change</li>
                    <li>Use descriptive material names</li>
                    <li>Test with small production quantities first</li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h6><i class="fas fa-times-circle text-danger"></i> Don'ts</h6>
                <ul>
                    <li>Don't guess quantities - measure accurately</li>
                    <li>Don't ignore waste materials</li>
                    <li>Don't set efficiency to 100%</li>
                    <li>Don't forget to include packaging materials</li>
                    <li>Don't use outdated material costs</li>
                    <li>Don't skip waste calculations</li>
                    <li>Don't create settings without testing</li>
                    <li>Don't ignore efficiency adjustments</li>
                </ul>
            </div>
        </div>
        
        <h6>Setting Up Your First Production Settings</h6>
        <div class="alert alert-info">
            <h6><i class="fas fa-rocket me-2"></i> Quick Start Guide</h6>
            <ol>
                <li><strong>Start Simple:</strong> Begin with one raw material and one waste material</li>
                <li><strong>Use Real Data:</strong> Measure actual quantities from your process</li>
                <li><strong>Set Conservative Efficiency:</strong> Start with 90-95% efficiency</li>
                <li><strong>Test Small:</strong> Create settings for 1-10 products first</li>
                <li><strong>Review Results:</strong> Check all calculations before proceeding</li>
                <li><strong>Scale Up:</strong> Gradually increase complexity and quantities</li>
            </ol>
        </div>
    </div>
</div>

<!-- Common Issues Section -->
<div class="content-card mb-4" id="common-issues">
    <div class="card-header">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Common Issues & Solutions</h5>
    </div>
    <div class="card-body">
        <div class="accordion" id="issuesAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#issue1">
                        "Add Material" button not working
                    </button>
                </h2>
                <div id="issue1" class="accordion-collapse collapse show" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Clicking "Add Material" doesn't add a new row.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Check if JavaScript is enabled in your browser</li>
                            <li>Refresh the page and try again</li>
                            <li>Clear browser cache and cookies</li>
                            <li>Try using a different browser</li>
                            <li>Contact support if issue persists</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue2">
                        Calculations not updating
                    </button>
                </h2>
                <div id="issue2" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Waste percentages and totals not updating when you change values.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Make sure you've selected a material from the dropdown</li>
                            <li>Enter valid numeric values</li>
                            <li>Click outside the input field to trigger calculation</li>
                            <li>Use the "Calculate Total Requirements" button</li>
                            <li>Check for JavaScript errors in browser console</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue3">
                        No materials available in dropdown
                    </button>
                </h2>
                <div id="issue3" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Material dropdowns are empty.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Create materials in the Material Master first</li>
                            <li>Ensure materials are set to "Active" status</li>
                            <li>Check material type (raw_material, packaging, waste)</li>
                            <li>Verify materials have categories assigned</li>
                            <li>Contact administrator if materials exist but don't appear</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue4">
                        Efficiency calculations seem wrong
                    </button>
                </h2>
                <div id="issue4" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Material quantities seem too high after efficiency adjustment.<br>
                        <strong>Explanation:</strong>
                        <ul>
                            <li>Efficiency adjustment increases quantities to account for losses</li>
                            <li>95% efficiency means you need 5% more material</li>
                            <li>Formula: Adjusted Qty = Base Qty ÷ Efficiency Factor</li>
                            <li>Example: 1kg ÷ 0.95 = 1.053kg (5.3% more)</li>
                            <li>This ensures you have enough material despite losses</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Tutorials Section -->
<div class="content-card mb-4" id="video-tutorials">
    <div class="card-header">
        <h5><i class="fas fa-play-circle me-2"></i>Video Tutorials</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-3x text-primary mb-3"></i>
                        <h6>Basic Setup</h6>
                        <p class="text-muted">Creating your first production settings</p>
                        <a href="<?= base_url('help/video-basic-setup') ?>" class="btn btn-primary">Watch Video</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-recycle fa-3x text-warning mb-3"></i>
                        <h6>Waste Management</h6>
                        <p class="text-muted">Setting up waste materials and calculations</p>
                        <a href="<?= base_url('help/video-waste-management') ?>" class="btn btn-warning">Watch Video</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-calculator fa-3x text-success mb-3"></i>
                        <h6>Advanced Calculations</h6>
                        <p class="text-muted">Understanding efficiency and scaling</p>
                        <a href="<?= base_url('help/video-calculations') ?>" class="btn btn-success">Watch Video</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Support -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="fas fa-headset me-2"></i>Need More Help?</h5>
    </div>
    <div class="card-body text-center">
        <p>If you need additional assistance with production settings, our support team is here to help!</p>
        <div class="row">
            <div class="col-md-4">
                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                <h6>Email Support</h6>
                <p>support@prodx.com</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-phone fa-2x text-success mb-2"></i>
                <h6>Phone Support</h6>
                <p>+1-800-PRODX</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-comments fa-2x text-info mb-2"></i>
                <h6>Live Chat</h6>
                <p>Available 9AM-6PM</p>
            </div>
        </div>
        <a href="<?= base_url('help/contact-support') ?>" class="btn btn-primary">Contact Support</a>
    </div>
</div>

<?= $this->endSection() ?>
