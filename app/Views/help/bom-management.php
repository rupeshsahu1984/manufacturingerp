<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-list-alt me-3"></i>BOM Management Guide</h1>
    <div class="header-actions">
        <a href="<?= base_url('bom') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to BOM Management
        </a>
        <a href="<?= base_url('help/video-bom-management') ?>" class="btn btn-outline-info ms-2" target="_blank">
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
                    <li><a href="#what-is-bom" class="text-decoration-none">📋 What is BOM?</a></li>
                    <li><a href="#bom-vs-production-settings" class="text-decoration-none">🔄 BOM vs Production Settings</a></li>
                    <li><a href="#bom-types" class="text-decoration-none">🏷️ BOM Types</a></li>
                    <li><a href="#creating-bom" class="text-decoration-none">➕ Creating BOM</a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><a href="#managing-bom" class="text-decoration-none">⚙️ Managing BOM</a></li>
                    <li><a href="#bom-versions" class="text-decoration-none">📝 BOM Versions</a></li>
                    <li><a href="#best-practices" class="text-decoration-none">💡 Best Practices</a></li>
                    <li><a href="#common-issues" class="text-decoration-none">⚠️ Common Issues</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- What is BOM Section -->
<div class="content-card mb-4" id="what-is-bom">
    <div class="card-header">
        <h5><i class="fas fa-info-circle me-2"></i>What is BOM?</h5>
    </div>
    <div class="card-body">
        <h6>BOM Definition</h6>
        <p><strong>BOM (Bill of Materials)</strong> is a comprehensive list of all materials, components, and assemblies required to manufacture a finished product. It serves as the complete recipe for production.</p>
        
        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Think of BOM as:</strong> A detailed recipe that lists every ingredient, quantity, and instruction needed to make a product.
        </div>

        <h6>Key Components of a BOM:</h6>
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <li><strong>Finished Product:</strong> The end product being manufactured</li>
                    <li><strong>Raw Materials:</strong> Basic materials used in production</li>
                    <li><strong>Components:</strong> Sub-assemblies and parts</li>
                    <li><strong>Quantities:</strong> Exact amounts needed</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul>
                    <li><strong>Units of Measure:</strong> How quantities are measured</li>
                    <li><strong>Waste Factors:</strong> Expected waste during production</li>
                    <li><strong>Cost Information:</strong> Material and labor costs</li>
                    <li><strong>Production Steps:</strong> Manufacturing sequence</li>
                </ul>
            </div>
        </div>

        <h6>Example BOM Structure:</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Level</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-primary">
                        <td>0</td>
                        <td>Finished Product</td>
                        <td>Steel Chair</td>
                        <td>1</td>
                        <td>piece</td>
                        <td>Finished Good</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Steel Frame</td>
                        <td>Main chair frame</td>
                        <td>1</td>
                        <td>piece</td>
                        <td>Component</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Steel Sheet</td>
                        <td>Seat material</td>
                        <td>2.5</td>
                        <td>kg</td>
                        <td>Raw Material</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Paint</td>
                        <td>Surface coating</td>
                        <td>0.5</td>
                        <td>liters</td>
                        <td>Raw Material</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- BOM vs Production Settings Section -->
<div class="content-card mb-4" id="bom-vs-production-settings">
    <div class="card-header">
        <h5><i class="fas fa-exchange-alt me-2"></i>BOM vs Production Settings</h5>
    </div>
    <div class="card-body">
        <h6>Understanding the Difference</h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Production Settings</h6>
                    </div>
                    <div class="card-body">
                        <h6>Purpose:</h6>
                        <ul>
                            <li>Configure production parameters</li>
                            <li>Set efficiency and batch sizes</li>
                            <li>Define waste calculations</li>
                            <li>Plan production quantities</li>
                        </ul>
                        
                        <h6>When to Use:</h6>
                        <ul>
                            <li>Setting up new products</li>
                            <li>Configuring production processes</li>
                            <li>Planning material requirements</li>
                            <li>Calculating costs</li>
                        </ul>
                        
                        <h6>Key Features:</h6>
                        <ul>
                            <li>Production planning</li>
                            <li>Efficiency settings</li>
                            <li>Waste management</li>
                            <li>Cost calculations</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i>BOM Management</h6>
                    </div>
                    <div class="card-body">
                        <h6>Purpose:</h6>
                        <ul>
                            <li>Manage complete material lists</li>
                            <li>Track BOM versions</li>
                            <li>Compare different BOMs</li>
                            <li>Maintain BOM history</li>
                        </ul>
                        
                        <h6>When to Use:</h6>
                        <ul>
                            <li>Managing multiple BOM versions</li>
                            <li>Comparing different designs</li>
                            <li>Tracking BOM changes</li>
                            <li>Approving BOM revisions</li>
                        </ul>
                        
                        <h6>Key Features:</h6>
                        <ul>
                            <li>Version control</li>
                            <li>BOM comparison</li>
                            <li>Approval workflows</li>
                            <li>Change tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Important:</strong> Production Settings create the BOM, while BOM Management helps you organize, version, and maintain multiple BOMs for the same product.
        </div>
    </div>
</div>

<!-- BOM Types Section -->
<div class="content-card mb-4" id="bom-types">
    <div class="card-header">
        <h5><i class="fas fa-tags me-2"></i>BOM Types</h5>
    </div>
    <div class="card-body">
        <h6>Different Types of BOMs</h6>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-industry me-2"></i>Manufacturing BOM (MBOM)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Used for actual manufacturing</p>
                        <p><strong>Contains:</strong></p>
                        <ul>
                            <li>All materials needed</li>
                            <li>Production quantities</li>
                            <li>Waste factors</li>
                            <li>Manufacturing steps</li>
                        </ul>
                        <p><strong>Use Case:</strong> Production planning and execution</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Sales BOM (SBOM)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Used for sales and pricing</p>
                        <p><strong>Contains:</strong></p>
                        <ul>
                            <li>Customer-facing components</li>
                            <li>Pricing information</li>
                            <li>Optional features</li>
                            <li>Packaging details</li>
                        </ul>
                        <p><strong>Use Case:</strong> Sales quotations and orders</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Engineering BOM (EBOM)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Used for design and engineering</p>
                        <p><strong>Contains:</strong></p>
                        <ul>
                            <li>Design specifications</li>
                            <li>Technical requirements</li>
                            <li>Engineering drawings</li>
                            <li>Design notes</li>
                        </ul>
                        <p><strong>Use Case:</strong> Product design and development</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Assembly BOM (ABOM)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Used for assembly processes</p>
                        <p><strong>Contains:</strong></p>
                        <ul>
                            <li>Assembly sequence</li>
                            <li>Sub-assemblies</li>
                            <li>Assembly instructions</li>
                            <li>Quality checkpoints</li>
                        </ul>
                        <p><strong>Use Case:</strong> Assembly line planning</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Creating BOM Section -->
<div class="content-card mb-4" id="creating-bom">
    <div class="card-header">
        <h5><i class="fas fa-plus-circle me-2"></i>Creating BOM</h5>
    </div>
    <div class="card-body">
        <h6>Step-by-Step BOM Creation Process</h6>
        
        <div class="row">
            <div class="col-md-8">
                <ol>
                    <li><strong>Start with Production Settings:</strong>
                        <ul>
                            <li>Go to Production Settings</li>
                            <li>Configure your product requirements</li>
                            <li>Set up raw materials and waste</li>
                            <li>Define production parameters</li>
                        </ul>
                    </li>
                    
                    <li><strong>Create BOM from Production Settings:</strong>
                        <ul>
                            <li>Save your production settings</li>
                            <li>This automatically creates a BOM</li>
                            <li>BOM is stored in BOM Management</li>
                            <li>You can now manage versions</li>
                        </ul>
                    </li>
                    
                    <li><strong>Manage BOM Versions:</strong>
                        <ul>
                            <li>Go to BOM Management</li>
                            <li>View all BOMs for the product</li>
                            <li>Create new versions as needed</li>
                            <li>Compare different versions</li>
                        </ul>
                    </li>
                    
                    <li><strong>Approve and Activate:</strong>
                        <ul>
                            <li>Review BOM for accuracy</li>
                            <li>Get necessary approvals</li>
                            <li>Activate the BOM for production</li>
                            <li>Track BOM usage</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <div class="col-md-4">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>Production Settings create the initial BOM</li>
                        <li>BOM Management handles versions</li>
                        <li>Always review before activation</li>
                        <li>Track changes for audit purposes</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <h6>BOM Creation Workflow</h6>
        <div class="text-center">
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-cogs fa-2x text-primary mb-2"></i>
                            <h6>1. Production Settings</h6>
                            <small>Configure product requirements</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-right fa-2x text-success mb-2"></i>
                            <h6>2. Generate BOM</h6>
                            <small>Auto-create from settings</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-list-alt fa-2x text-warning mb-2"></i>
                            <h6>3. BOM Management</h6>
                            <small>Version and organize</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                            <h6>4. Activate</h6>
                            <small>Ready for production</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Managing BOM Section -->
<div class="content-card mb-4" id="managing-bom">
    <div class="card-header">
        <h5><i class="fas fa-cogs me-2"></i>Managing BOM</h5>
    </div>
    <div class="card-body">
        <h6>BOM Management Features</h6>
        
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-eye text-primary"></i> View BOM Details</h6>
                <ul>
                    <li>View complete material list</li>
                    <li>See quantities and costs</li>
                    <li>Check waste calculations</li>
                    <li>Review production parameters</li>
                </ul>
                
                <h6><i class="fas fa-edit text-warning"></i> Edit BOM</h6>
                <ul>
                    <li>Modify material quantities</li>
                    <li>Update waste factors</li>
                    <li>Change production parameters</li>
                    <li>Add/remove materials</li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h6><i class="fas fa-copy text-success"></i> Create New Version</h6>
                <ul>
                    <li>Create BOM version</li>
                    <li>Track changes</li>
                    <li>Maintain history</li>
                    <li>Compare versions</li>
                </ul>
                
                <h6><i class="fas fa-toggle-on text-info"></i> Activate/Deactivate</h6>
                <ul>
                    <li>Activate for production</li>
                    <li>Deactivate old versions</li>
                    <li>Control which BOM is active</li>
                    <li>Prevent conflicts</li>
                </ul>
            </div>
        </div>
        
        <h6>BOM Management Dashboard</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>BOM Version</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Last Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Steel Chair</td>
                        <td>v1.0</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>2024-01-15</td>
                        <td>2024-01-15</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                            <button class="btn btn-sm btn-outline-warning">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Steel Chair</td>
                        <td>v1.1</td>
                        <td><span class="badge bg-secondary">Draft</span></td>
                        <td>2024-01-20</td>
                        <td>2024-01-20</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                            <button class="btn btn-sm btn-outline-success">Activate</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- BOM Versions Section -->
<div class="content-card mb-4" id="bom-versions">
    <div class="card-header">
        <h5><i class="fas fa-code-branch me-2"></i>BOM Versions</h5>
    </div>
    <div class="card-body">
        <h6>Version Control System</h6>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Version Control:</strong> BOM versions help you track changes, maintain history, and ensure only approved BOMs are used in production.
        </div>
        
        <h6>Version Numbering System</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Major Version (v1.0)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Changes:</strong></p>
                        <ul>
                            <li>Major design changes</li>
                            <li>New materials added</li>
                            <li>Significant cost changes</li>
                            <li>Production process changes</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">Minor Version (v1.1)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Changes:</strong></p>
                        <ul>
                            <li>Quantity adjustments</li>
                            <li>Waste factor updates</li>
                            <li>Minor cost changes</li>
                            <li>Documentation updates</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Revision (v1.1.1)</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Changes:</strong></p>
                        <ul>
                            <li>Typo corrections</li>
                            <li>Formatting changes</li>
                            <li>Minor clarifications</li>
                            <li>Documentation fixes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <h6>Version Comparison Example</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th>v1.0 Quantity</th>
                        <th>v1.1 Quantity</th>
                        <th>Change</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Steel Sheet</td>
                        <td>2.0 kg</td>
                        <td>2.5 kg</td>
                        <td><span class="text-success">+0.5 kg</span></td>
                        <td>Optimized for strength</td>
                    </tr>
                    <tr>
                        <td>Paint</td>
                        <td>0.3 liters</td>
                        <td>0.5 liters</td>
                        <td><span class="text-success">+0.2 liters</span></td>
                        <td>Better coverage</td>
                    </tr>
                    <tr>
                        <td>Waste Factor</td>
                        <td>5%</td>
                        <td>3%</td>
                        <td><span class="text-danger">-2%</span></td>
                        <td>Improved efficiency</td>
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
                    <li>Always create BOMs from Production Settings</li>
                    <li>Use descriptive version names</li>
                    <li>Document changes in version notes</li>
                    <li>Review BOMs before activation</li>
                    <li>Keep only one active BOM per product</li>
                    <li>Archive old versions for reference</li>
                    <li>Get approvals for major changes</li>
                    <li>Track BOM usage in production</li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h6><i class="fas fa-times-circle text-danger"></i> Don'ts</h6>
                <ul>
                    <li>Don't edit active BOMs directly</li>
                    <li>Don't delete BOM versions</li>
                    <li>Don't skip approval process</li>
                    <li>Don't use unclear version names</li>
                    <li>Don't activate untested BOMs</li>
                    <li>Don't ignore change documentation</li>
                    <li>Don't have multiple active BOMs</li>
                    <li>Don't forget to update related documents</li>
                </ul>
            </div>
        </div>
        
        <h6>BOM Management Checklist</h6>
        <div class="alert alert-info">
            <h6><i class="fas fa-clipboard-check me-2"></i> Before Activating a BOM</h6>
            <ul class="mb-0">
                <li>✅ All materials are available</li>
                <li>✅ Quantities are accurate</li>
                <li>✅ Waste factors are realistic</li>
                <li>✅ Costs are up-to-date</li>
                <li>✅ Production process is defined</li>
                <li>✅ Quality checks are included</li>
                <li>✅ Approvals are obtained</li>
                <li>✅ Documentation is complete</li>
            </ul>
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
                        Can't create BOM from Production Settings
                    </button>
                </h2>
                <div id="issue1" class="accordion-collapse collapse show" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Production Settings don't create a BOM automatically.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Ensure all required fields are filled</li>
                            <li>Check that materials are selected</li>
                            <li>Verify production settings are saved</li>
                            <li>Contact administrator if issue persists</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue2">
                        Multiple active BOMs for same product
                    </button>
                </h2>
                <div id="issue2" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Multiple BOMs are active for the same product.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Deactivate old BOM versions</li>
                            <li>Keep only the latest version active</li>
                            <li>Review which version should be active</li>
                            <li>Update production to use correct BOM</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue3">
                        BOM version comparison not working
                    </button>
                </h2>
                <div id="issue3" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Can't compare different BOM versions.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Ensure you have multiple versions</li>
                            <li>Select different versions to compare</li>
                            <li>Check if versions are properly saved</li>
                            <li>Refresh the page and try again</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue4">
                        Can't activate BOM version
                    </button>
                </h2>
                <div id="issue4" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Activate button is not available or not working.<br>
                        <strong>Solutions:</strong>
                        <ul>
                            <li>Check if you have permission to activate</li>
                            <li>Ensure BOM is complete and valid</li>
                            <li>Deactivate current active BOM first</li>
                            <li>Contact administrator for permissions</li>
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
                        <h6>BOM Basics</h6>
                        <p class="text-muted">Understanding BOM concepts and structure</p>
                        <a href="<?= base_url('help/video-bom-basics') ?>" class="btn btn-primary">Watch Video</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-code-branch fa-3x text-success mb-3"></i>
                        <h6>Version Management</h6>
                        <p class="text-muted">Managing BOM versions and changes</p>
                        <a href="<?= base_url('help/video-version-management') ?>" class="btn btn-success">Watch Video</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs fa-3x text-warning mb-3"></i>
                        <h6>BOM Workflow</h6>
                        <p class="text-muted">Complete BOM creation and management workflow</p>
                        <a href="<?= base_url('help/video-bom-workflow') ?>" class="btn btn-warning">Watch Video</a>
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
        <p>If you need additional assistance with BOM management, our support team is here to help!</p>
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
