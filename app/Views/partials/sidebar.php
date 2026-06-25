<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <span class="sidebar-logo-text">ProDX ERP</span>
    </div>

    <!-- Sidebar Search -->
    <div class="sidebar-search">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="search" placeholder="Search components..." />
        </div>
    </div>

    <!-- Dashboard (single entry) -->
    <div class="sidebar-section">
        <div class="sidebar-title">
            <i class="fas fa-tachometer-alt"></i>
            DASHBOARD
        </div>
        <a href="<?= base_url('dashboard') ?>" class="sidebar-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
            <i class="fas fa-home me-2"></i>Dashboard
        </a>
    </div>


    <!-- Master Settings Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#masterSettingsCollapse" aria-expanded="false" aria-controls="masterSettingsCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-cog"></i>
                    MASTER SETTINGS
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="masterSettingsCollapse">
                <a href="<?= base_url('supplier') ?>" class="sidebar-link <?= strpos(current_url(), 'supplier') !== false ? 'active' : '' ?>">
                    <i class="fas fa-truck me-2"></i>Supplier Master
                </a>
                <a href="<?= base_url('customer') ?>" class="sidebar-link <?= strpos(current_url(), 'customer') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users me-2"></i>Customer Master
                </a>
                <a href="<?= base_url('product') ?>" class="sidebar-link <?= (strpos(current_url(), 'product') !== false && strpos(current_url(), 'production-settings') === false) ? 'active' : '' ?>">
                    <i class="fas fa-box me-2"></i>Material Master
                </a>
                <a href="<?= base_url('category') ?>" class="sidebar-link <?= strpos(current_url(), 'category') !== false ? 'active' : '' ?>">
                    <i class="fas fa-tags me-2"></i>Category Master
                </a>
                <a href="<?= base_url('production-settings') ?>" class="sidebar-link <?= strpos(current_url(), 'production-settings') !== false ? 'active' : '' ?>">
                    <i class="fas fa-cogs me-2"></i>Production Settings
                </a>
                <a href="<?= base_url('bom') ?>" class="sidebar-link <?= strpos(current_url(), 'bom') !== false ? 'active' : '' ?>">
                    <i class="fas fa-list-alt me-2"></i>BOM Management
                </a>
                <a href="<?= base_url('warehouse') ?>" class="sidebar-link <?= strpos(current_url(), 'warehouse') !== false ? 'active' : '' ?>">
                    <i class="fas fa-warehouse me-2"></i>Warehouse Master
                </a>
                <a href="<?= base_url('department') ?>" class="sidebar-link <?= strpos(current_url(), 'department') !== false ? 'active' : '' ?>">
                    <i class="fas fa-building me-2"></i>Department Master
                </a>
                <a href="<?= base_url('employee') ?>" class="sidebar-link <?= strpos(current_url(), 'employee') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-tie me-2"></i>Employee Master
                </a>
            </div>
        </div>
    </div>

    <!-- Purchase Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#purchaseCollapse" aria-expanded="false" aria-controls="purchaseCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-shopping-cart"></i>
                    PURCHASE
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="purchaseCollapse">
                <a href="<?= base_url('purchase-requisition') ?>" class="sidebar-link <?= strpos(current_url(), 'purchase-requisition') !== false ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list me-2"></i>Purchase Requisition
                </a>
                <a href="<?= base_url('purchase-order') ?>" class="sidebar-link <?= strpos(current_url(), 'purchase-order') !== false ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart me-2"></i>Purchase Order
                </a>
                <a href="<?= base_url('purchase-bill') ?>" class="sidebar-link <?= strpos(current_url(), 'purchase-bill') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice me-2"></i>Purchase Bills
                </a>
                <a href="<?= base_url('purchase-return') ?>" class="sidebar-link <?= strpos(current_url(), 'purchase-return') !== false ? 'active' : '' ?>">
                    <i class="fas fa-undo me-2"></i>Purchase Returns
                </a>
                <a href="<?= base_url('vendor-payment') ?>" class="sidebar-link <?= strpos(current_url(), 'vendor-payment') !== false ? 'active' : '' ?>">
                    <i class="fas fa-credit-card me-2"></i>Vendor Payments
                </a>
            </div>
        </div>
    </div>

    <!-- Sales Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#salesCollapse" aria-expanded="false" aria-controls="salesCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-chart-line"></i>
                    SALES
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="salesCollapse">
                <a href="<?= base_url('sales-orders') ?>" class="sidebar-link <?= strpos(current_url(), 'sales-orders') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar me-2"></i>Sales Orders
                </a>
                <a href="<?= base_url('sales-invoice') ?>" class="sidebar-link <?= strpos(current_url(), 'sales-invoice') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Sales Invoices
                </a>
                <a href="<?= base_url('sales-return') ?>" class="sidebar-link <?= strpos(current_url(), 'sales-return') !== false ? 'active' : '' ?>">
                    <i class="fas fa-undo me-2"></i>Sales Returns
                </a>
                <a href="<?= base_url('customer-payment') ?>" class="sidebar-link <?= strpos(current_url(), 'customer-payment') !== false ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave me-2"></i>Customer Payments
                </a>
                <a href="<?= base_url('quotation') ?>" class="sidebar-link <?= strpos(current_url(), 'quotation') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-contract me-2"></i>Quotations
                </a>
            </div>
        </div>
    </div>

    <!-- Production Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#productionCollapse" aria-expanded="false" aria-controls="productionCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-industry"></i>
                    PRODUCTION
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="productionCollapse">
                <a href="<?= base_url('work-orders') ?>" class="sidebar-link <?= strpos(current_url(), 'work-orders') !== false ? 'active' : '' ?>">
                    <i class="fas fa-tasks me-2"></i>Work Orders
                </a>
                <a href="<?= base_url('production-planning') ?>" class="sidebar-link <?= strpos(current_url(), 'production-planning') !== false ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt me-2"></i>Production Planning
                </a>
                <a href="<?= base_url('production-tracking') ?>" class="sidebar-link <?= strpos(current_url(), 'production-tracking') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line me-2"></i>Production Tracking
                </a>
                <a href="<?= base_url('quality-control') ?>" class="sidebar-link <?= strpos(current_url(), 'quality-control') !== false ? 'active' : '' ?>">
                    <i class="fas fa-check-circle me-2"></i>Quality Control
                </a>
                <a href="<?= base_url('maintenance') ?>" class="sidebar-link <?= strpos(current_url(), 'maintenance') !== false ? 'active' : '' ?>">
                    <i class="fas fa-tools me-2"></i>Maintenance
                </a>
            </div>
        </div>
    </div>

    <!-- Inventory & Stock Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#inventoryCollapse" aria-expanded="false" aria-controls="inventoryCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-boxes"></i>
                    INVENTORY & STOCK
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="inventoryCollapse">
                <a href="<?= base_url('stock') ?>" class="sidebar-link <?= strpos(current_url(), 'stock') !== false ? 'active' : '' ?>">
                    <i class="fas fa-boxes me-2"></i>Stock Management
                </a>
                <a href="<?= base_url('stock-transfer') ?>" class="sidebar-link <?= strpos(current_url(), 'stock-transfer') !== false ? 'active' : '' ?>">
                    <i class="fas fa-exchange-alt me-2"></i>Stock Transfer
                </a>
                <a href="<?= base_url('stock-adjustment') ?>" class="sidebar-link <?= strpos(current_url(), 'stock-adjustment') !== false ? 'active' : '' ?>">
                    <i class="fas fa-balance-scale me-2"></i>Stock Adjustment
                </a>
                <a href="<?= base_url('stock-count') ?>" class="sidebar-link <?= strpos(current_url(), 'stock-count') !== false ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-check me-2"></i>Stock Count
                </a>
                <a href="<?= base_url('low-stock-alerts') ?>" class="sidebar-link <?= strpos(current_url(), 'low-stock-alerts') !== false ? 'active' : '' ?>">
                    <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts
                </a>
            </div>
        </div>
    </div>

    <!-- HR Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#hrCollapse" aria-expanded="false" aria-controls="hrCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-users"></i>
                    HUMAN RESOURCES
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="hrCollapse">
                <a href="<?= base_url('attendance') ?>" class="sidebar-link <?= strpos(current_url(), 'attendance') !== false ? 'active' : '' ?>">
                    <i class="fas fa-clock me-2"></i>Attendance
                </a>
                <a href="<?= base_url('leave-management') ?>" class="sidebar-link <?= strpos(current_url(), 'leave-management') !== false ? 'active' : '' ?>">
                    <i class="fas fa-calendar-times me-2"></i>Leave Management
                </a>
                <a href="<?= base_url('salary') ?>" class="sidebar-link <?= strpos(current_url(), 'salary') !== false ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave me-2"></i>Salary Management
                </a>
                <a href="<?= base_url('payroll') ?>" class="sidebar-link <?= strpos(current_url(), 'payroll') !== false ? 'active' : '' ?>">
                    <i class="fas fa-calculator me-2"></i>Payroll
                </a>
                <a href="<?= base_url('documents') ?>" class="sidebar-link <?= strpos(current_url(), 'documents') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-alt me-2"></i>Documents
                </a>
                <a href="<?= base_url('training') ?>" class="sidebar-link <?= strpos(current_url(), 'training') !== false ? 'active' : '' ?>">
                    <i class="fas fa-graduation-cap me-2"></i>Training
                </a>
            </div>
        </div>
    </div>

    <!-- Accounting Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#accountingCollapse" aria-expanded="false" aria-controls="accountingCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-calculator"></i>
                    ACCOUNTING
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="accountingCollapse">
                <a href="<?= base_url('general-ledger') ?>" class="sidebar-link <?= strpos(current_url(), 'general-ledger') !== false ? 'active' : '' ?>">
                    <i class="fas fa-book me-2"></i>General Ledger
                </a>
                <a href="<?= base_url('accounts-payable') ?>" class="sidebar-link <?= strpos(current_url(), 'accounts-payable') !== false ? 'active' : '' ?>">
                    <i class="fas fa-hand-holding-usd me-2"></i>Accounts Payable
                </a>
                <a href="<?= base_url('accounts-receivable') ?>" class="sidebar-link <?= strpos(current_url(), 'accounts-receivable') !== false ? 'active' : '' ?>">
                    <i class="fas fa-hand-holding-usd me-2"></i>Accounts Receivable
                </a>
                <a href="<?= base_url('bank-reconciliation') ?>" class="sidebar-link <?= strpos(current_url(), 'bank-reconciliation') !== false ? 'active' : '' ?>">
                    <i class="fas fa-university me-2"></i>Bank Reconciliation
                </a>
                <a href="<?= base_url('journal-entries') ?>" class="sidebar-link <?= strpos(current_url(), 'journal-entries') !== false ? 'active' : '' ?>">
                    <i class="fas fa-journal-whills me-2"></i>Journal Entries
                </a>
                <a href="<?= base_url('financial-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'financial-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie me-2"></i>Financial Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Gate Entry/Exit Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#gateEntryCollapse" aria-expanded="false" aria-controls="gateEntryCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-door-open"></i>
                    GATE ENTRY/EXIT
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="gateEntryCollapse">
                <a href="<?= base_url('gate-entry') ?>" class="sidebar-link <?= strpos(current_url(), 'gate-entry') !== false ? 'active' : '' ?>">
                    <i class="fas fa-truck-loading me-2"></i>Gate Entry
                </a>
                <a href="<?= base_url('gate-exit') ?>" class="sidebar-link <?= strpos(current_url(), 'gate-exit') !== false ? 'active' : '' ?>">
                    <i class="fas fa-truck me-2"></i>Gate Exit
                </a>
                <a href="<?= base_url('visitor-management') ?>" class="sidebar-link <?= strpos(current_url(), 'visitor-management') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-friends me-2"></i>Visitor Management
                </a>
            </div>
        </div>
    </div>

    <!-- Reports Section Accordion -->
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#reportsCollapse" aria-expanded="false" aria-controls="reportsCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-chart-bar"></i>
                    REPORTS
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="reportsCollapse">
                <a href="<?= base_url('sales-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'sales-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line me-2"></i>Sales Reports
                </a>
                <a href="<?= base_url('purchase-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'purchase-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart me-2"></i>Purchase Reports
                </a>
                <a href="<?= base_url('production-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'production-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-industry me-2"></i>Production Reports
                </a>
                <a href="<?= base_url('inventory-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'inventory-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-boxes me-2"></i>Inventory Reports
                </a>
                <a href="<?= base_url('hr-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'hr-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users me-2"></i>HR Reports
                </a>
                <a href="<?= base_url('financial-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'financial-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie me-2"></i>Financial Reports
                </a>
                <a href="<?= base_url('custom-reports') ?>" class="sidebar-link <?= strpos(current_url(), 'custom-reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-cogs me-2"></i>Custom Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Company Settings Section (Super Admin Only) -->
    <?php
        $role = session()->get('user_role');
        $isCompanyAdmin = in_array($role, ['superadmin', 'super_admin', 'SuperAdmin'], true);
    ?>
    <?php if ($isCompanyAdmin): ?>
    <div class="sidebar-section">
        <div class="sidebar-accordion">
            <div class="sidebar-accordion-header" data-target="#companySettingsCollapse" aria-expanded="false" aria-controls="companySettingsCollapse">
                <div class="sidebar-title">
                    <i class="fas fa-building"></i>
                    COMPANY SETTINGS
                </div>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="collapse" id="companySettingsCollapse">
                <a href="<?= base_url('company-profile') ?>" class="sidebar-link <?= strpos(current_url(), 'company-profile') !== false ? 'active' : '' ?>">
                    <i class="fas fa-building me-2"></i>Company Profile
                </a>
                <a href="<?= base_url('logo-settings') ?>" class="sidebar-link <?= strpos(current_url(), 'logo-settings') !== false ? 'active' : '' ?>">
                    <i class="fas fa-image me-2"></i>Logo & Branding
                </a>
                <a href="<?= base_url('department-assignment') ?>" class="sidebar-link <?= strpos(current_url(), 'department-assignment') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-shield me-2"></i>Department Assignment
                </a>
                <a href="<?= base_url('user-management') ?>" class="sidebar-link <?= strpos(current_url(), 'user-management') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users-cog me-2"></i>User Management
                </a>
                <a href="<?= base_url('role-permissions') ?>" class="sidebar-link <?= strpos(current_url(), 'role-permissions') !== false ? 'active' : '' ?>">
                    <i class="fas fa-key me-2"></i>Role & Permissions
                </a>
                <a href="<?= base_url('system-settings') ?>" class="sidebar-link <?= strpos(current_url(), 'system-settings') !== false ? 'active' : '' ?>">
                    <i class="fas fa-cogs me-2"></i>System Settings
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Help & Support Section -->
    <div class="sidebar-section">
        <div class="sidebar-title">
            <i class="fas fa-question-circle"></i>
            HELP & SUPPORT
        </div>
        <a href="<?= base_url('help') ?>" class="sidebar-link <?= strpos(current_url(), 'help') !== false ? 'active' : '' ?>">
            <i class="fas fa-book me-2"></i>Help Center
        </a>
        <a href="<?= base_url('support') ?>" class="sidebar-link <?= strpos(current_url(), 'support') !== false ? 'active' : '' ?>">
            <i class="fas fa-headset me-2"></i>Support
        </a>
    </div>

    <!-- What's New Section -->
    <div class="sidebar-section">
        <div class="sidebar-title">
            <i class="fas fa-star"></i>
            WHAT'S NEW?
        </div>
        <div class="sidebar-highlight">
            <div class="title">Advanced Analytics</div>
            <div class="description">Get real-time insights and predictive analytics for better decision making</div>
            <a href="#" class="btn">Learn More ></a>
        </div>
    </div>

    <!-- Logout Section -->
    <div class="sidebar-section">
        <div class="sidebar-title">
            <i class="fas fa-sign-out-alt"></i>
            ACCOUNT
        </div>
        <a href="<?= base_url('logout') ?>" class="sidebar-link" onclick="return confirm('Are you sure you want to logout?')">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
    </div>
</div> 