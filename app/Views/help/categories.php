<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="header">
    <h1><i class="fas fa-tags me-3"></i>Material Categories Guide</h1>
    <div class="header-actions">
        <a href="<?= base_url('category') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
        <a href="<?= base_url('help/video-categories') ?>" class="btn btn-outline-info ms-2" target="_blank">
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
                    <li><a href="#category-types" class="text-decoration-none">🏷️ Category Types</a></li>
                    <li><a href="#creating-categories" class="text-decoration-none">➕ Creating Categories</a></li>
                    <li><a href="#managing-categories" class="text-decoration-none">⚙️ Managing Categories</a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><a href="#best-practices" class="text-decoration-none">💡 Best Practices</a></li>
                    <li><a href="#common-issues" class="text-decoration-none">⚠️ Common Issues</a></li>
                    <li><a href="#faq" class="text-decoration-none">❓ FAQ</a></li>
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
        <h6>What are Material Categories?</h6>
        <p>Material Categories are organizational groups that help you classify and manage different types of materials in your ERP system. They provide structure and make it easier to find, filter, and manage materials.</p>
        
        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Well-organized categories make your ERP system more efficient and easier to navigate.
        </div>

        <h6>Why Use Categories?</h6>
        <ul>
            <li><strong>Organization:</strong> Group similar materials together</li>
            <li><strong>Filtering:</strong> Easily find materials by category</li>
            <li><strong>Reporting:</strong> Generate reports by category</li>
            <li><strong>Permissions:</strong> Control access by category</li>
            <li><strong>Workflow:</strong> Different processes for different categories</li>
        </ul>
    </div>
</div>

<!-- Category Types Section -->
<div class="content-card mb-4" id="category-types">
    <div class="card-header">
        <h5><i class="fas fa-tags me-2"></i>Category Types</h5>
    </div>
    <div class="card-body">
        <p>There are four main category types in the system, each serving a specific purpose:</p>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-box me-2"></i>Raw Material</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Basic materials used in production</p>
                        <p><strong>Examples:</strong></p>
                        <ul>
                            <li>Steel, Aluminum, Plastic</li>
                            <li>Chemicals, Adhesives</li>
                            <li>Electronic Components</li>
                            <li>Textiles, Fabrics</li>
                        </ul>
                        <p><strong>Use Case:</strong> Materials that go into making finished products</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-box-open me-2"></i>Packaging</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Materials used for packaging finished goods</p>
                        <p><strong>Examples:</strong></p>
                        <ul>
                            <li>Cardboard Boxes</li>
                            <li>Plastic Bags</li>
                            <li>Bubble Wrap</li>
                            <li>Labels, Stickers</li>
                        </ul>
                        <p><strong>Use Case:</strong> Materials used to package and protect finished products</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Finished Goods</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Completed products ready for sale</p>
                        <p><strong>Examples:</strong></p>
                        <ul>
                            <li>Completed Products</li>
                            <li>Assembled Items</li>
                            <li>Ready-to-Sell Goods</li>
                            <li>Final Products</li>
                        </ul>
                        <p><strong>Use Case:</strong> Products that are ready for customer delivery</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-recycle me-2"></i>Waste</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Purpose:</strong> Materials generated during production</p>
                        <p><strong>Examples:</strong></p>
                        <ul>
                            <li>Scrap Metal</li>
                            <li>Waste Plastic</li>
                            <li>Defective Items</li>
                            <li>Production Waste</li>
                        </ul>
                        <p><strong>Use Case:</strong> Materials that are byproducts of production</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Creating Categories Section -->
<div class="content-card mb-4" id="creating-categories">
    <div class="card-header">
        <h5><i class="fas fa-plus-circle me-2"></i>Creating Categories</h5>
    </div>
    <div class="card-body">
        <h6>Step-by-Step Guide</h6>
        
        <div class="row">
            <div class="col-md-8">
                <ol>
                    <li><strong>Navigate to Categories:</strong>
                        <ul>
                            <li>Go to <strong>Master Settings</strong> → <strong>Material Categories</strong></li>
                            <li>Click the <strong>"Add Category"</strong> button</li>
                        </ul>
                    </li>
                    
                    <li><strong>Fill in Category Details:</strong>
                        <ul>
                            <li><strong>Category Name:</strong> Enter a descriptive name (e.g., "Steel Sheets", "Electronic Components")</li>
                            <li><strong>Category Type:</strong> Select the appropriate type (Raw Material, Packaging, Finished Goods, or Waste)</li>
                            <li><strong>Description:</strong> Add a detailed description of the category</li>
                            <li><strong>Status:</strong> Set to "Active" to make it available for use</li>
                        </ul>
                    </li>
                    
                    <li><strong>Save the Category:</strong>
                        <ul>
                            <li>Click <strong>"Create Category"</strong> to save</li>
                            <li>The category will now appear in dropdowns throughout the system</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>Category names must be unique</li>
                        <li>Choose the correct category type</li>
                        <li>Use descriptive names</li>
                        <li>Add helpful descriptions</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <h6>Example Category Creation</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Field</th>
                        <th>Example Value</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Category Name</td>
                        <td>Steel Sheets</td>
                        <td>Clear, descriptive name</td>
                    </tr>
                    <tr>
                        <td>Category Type</td>
                        <td>Raw Material</td>
                        <td>Used in production</td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>Various grades of steel sheets used in manufacturing</td>
                        <td>Detailed description</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>Active</td>
                        <td>Available for use</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Managing Categories Section -->
<div class="content-card mb-4" id="managing-categories">
    <div class="card-header">
        <h5><i class="fas fa-cogs me-2"></i>Managing Categories</h5>
    </div>
    <div class="card-body">
        <h6>Available Actions</h6>
        
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-eye text-primary"></i> View Category Details</h6>
                <ul>
                    <li>Click the <strong>eye icon</strong> to view category details</li>
                    <li>See how many products are in the category</li>
                    <li>View category information and description</li>
                </ul>
                
                <h6><i class="fas fa-edit text-warning"></i> Edit Category</h6>
                <ul>
                    <li>Click the <strong>edit icon</strong> to modify category details</li>
                    <li>Update name, description, or status</li>
                    <li>Changes apply immediately</li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h6><i class="fas fa-toggle-on text-success"></i> Toggle Status</h6>
                <ul>
                    <li>Activate/deactivate categories</li>
                    <li>Inactive categories won't appear in dropdowns</li>
                    <li>Useful for seasonal or discontinued categories</li>
                </ul>
                
                <h6><i class="fas fa-trash text-danger"></i> Delete Category</h6>
                <ul>
                    <li>Only available if no products are assigned</li>
                    <li>Cannot delete categories with products</li>
                    <li>Consider deactivating instead of deleting</li>
                </ul>
            </div>
        </div>
        
        <h6>Search and Filter</h6>
        <p>Use the search and filter options to find categories quickly:</p>
        <ul>
            <li><strong>Search:</strong> Find categories by name</li>
            <li><strong>Category Type:</strong> Filter by type (Raw Material, Packaging, etc.)</li>
            <li><strong>Status:</strong> Filter by active/inactive status</li>
        </ul>
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
                    <li>Use clear, descriptive category names</li>
                    <li>Group similar materials together</li>
                    <li>Add detailed descriptions</li>
                    <li>Choose the correct category type</li>
                    <li>Use consistent naming conventions</li>
                    <li>Review and update categories regularly</li>
                    <li>Deactivate unused categories instead of deleting</li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h6><i class="fas fa-times-circle text-danger"></i> Don'ts</h6>
                <ul>
                    <li>Don't create too many categories</li>
                    <li>Don't use vague or unclear names</li>
                    <li>Don't mix different material types in one category</li>
                    <li>Don't delete categories with assigned products</li>
                    <li>Don't use abbreviations without explanation</li>
                    <li>Don't create duplicate categories</li>
                    <li>Don't ignore category descriptions</li>
                </ul>
            </div>
        </div>
        
        <h6>Naming Conventions</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Good Examples</th>
                        <th>Bad Examples</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Steel Sheets</td>
                        <td>SS</td>
                        <td>Clear and descriptive</td>
                    </tr>
                    <tr>
                        <td>Electronic Components</td>
                        <td>Stuff</td>
                        <td>Specific and meaningful</td>
                    </tr>
                    <tr>
                        <td>Plastic Packaging</td>
                        <td>Pkg</td>
                        <td>Complete and understandable</td>
                    </tr>
                </tbody>
            </table>
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
                        Can't delete a category
                    </button>
                </h2>
                <div id="issue1" class="accordion-collapse collapse show" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Delete button is not available or shows an error.<br>
                        <strong>Solution:</strong> Categories with assigned products cannot be deleted. Either:
                        <ul>
                            <li>Move products to another category first</li>
                            <li>Deactivate the category instead</li>
                            <li>Delete the products first (if appropriate)</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue2">
                        Category not appearing in dropdowns
                    </button>
                </h2>
                <div id="issue2" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> Category exists but doesn't appear in material creation forms.<br>
                        <strong>Solution:</strong> Check if the category is:
                        <ul>
                            <li>Set to "Active" status</li>
                            <li>Has the correct category type</li>
                            <li>Is not filtered out by other criteria</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue3">
                        Duplicate category name error
                    </button>
                </h2>
                <div id="issue3" class="accordion-collapse collapse" data-bs-parent="#issuesAccordion">
                    <div class="accordion-body">
                        <strong>Problem:</strong> System shows "Category name already exists" error.<br>
                        <strong>Solution:</strong> Category names must be unique. Try:
                        <ul>
                            <li>Using a different name</li>
                            <li>Adding a suffix (e.g., "Steel Sheets - Type A")</li>
                            <li>Checking for existing categories with similar names</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="content-card mb-4" id="faq">
    <div class="card-header">
        <h5><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h5>
    </div>
    <div class="card-body">
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        How many categories should I create?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Create enough categories to organize your materials effectively, but not so many that it becomes confusing. A good rule of thumb is 10-20 categories per type, depending on your business size and complexity.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Can I change a category type after creation?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, you can edit a category and change its type. However, be careful as this might affect how materials in that category behave in the system, especially for production and stock management.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        What happens if I deactivate a category?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        When you deactivate a category:
                        <ul>
                            <li>It won't appear in dropdown menus for new materials</li>
                            <li>Existing materials in that category remain unchanged</li>
                            <li>You can reactivate it later if needed</li>
                            <li>Reports will still show materials in that category</li>
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
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-3x text-primary mb-3"></i>
                        <h6>Creating Categories</h6>
                        <p class="text-muted">Step-by-step guide to creating material categories</p>
                        <a href="<?= base_url('help/video-create-categories') ?>" class="btn btn-primary">Watch Video</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs fa-3x text-success mb-3"></i>
                        <h6>Managing Categories</h6>
                        <p class="text-muted">How to edit, deactivate, and organize categories</p>
                        <a href="<?= base_url('help/video-manage-categories') ?>" class="btn btn-success">Watch Video</a>
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
        <p>If you need additional assistance with material categories, our support team is here to help!</p>
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
