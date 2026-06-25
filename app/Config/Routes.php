<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Test route to verify routing is working
$routes->get('test', function() {
    return 'Routing is working!';
});

// Direct stock route (outside inventory group)
$routes->get('stock', 'InventoryManagementController::stock');
$routes->get('stock/stock-in', 'InventoryManagementController::stockIn');
$routes->post('stock/stock-in', 'InventoryManagementController::stockInStore');
$routes->get('stock/edit/(:num)', 'InventoryManagementController::stockEdit/$1');
$routes->post('stock/update/(:num)', 'InventoryManagementController::stockUpdate/$1');
$routes->get('stock/stock-out', 'InventoryManagementController::stockOut');
$routes->post('stock/stock-out', 'InventoryManagementController::stockOutStore');
$routes->get('stock/quick-stock-in', 'InventoryManagementController::quickStockIn');
$routes->get('stock/quick-stock-out', 'InventoryManagementController::quickStockOut');
$routes->get('stock-transfer', 'InventoryManagementController::transfers');
$routes->get('stock-adjustment', 'InventoryManagementController::adjustments');
$routes->get('stock-count', 'InventoryManagementController::stockCount');
$routes->get('low-stock-alerts', 'InventoryManagementController::expiryAlerts');

/*
|--------------------------------------------------------------------------
| Sidebar-friendly URLs (aliases & redirects)
| Menu in app/Views/partials/sidebar.php must match these.
|--------------------------------------------------------------------------
*/
$routes->get('salary', 'HRController::salaryManagement');

// Work orders: sidebar uses /work-orders; canonical routes are also under /production/work-orders
$routes->get('work-orders', 'ProductionController::workOrders');
$routes->get('work-orders/create', 'ProductionController::workOrderCreate');
$routes->post('work-orders/store', 'ProductionController::workOrderStore');
$routes->get('work-orders/view/(:num)', 'ProductionController::workOrderView/$1');
$routes->post('work-orders/start/(:num)', 'ProductionController::workOrderStart/$1');
$routes->post('work-orders/complete/(:num)', 'ProductionController::workOrderComplete/$1');

// Accounting menu shortcuts → existing accounting/* actions
$routes->get('general-ledger', 'AccountingController::ledger');
$routes->get('accounts-payable', 'AccountingController::payables');
$routes->get('accounts-receivable', 'AccountingController::receivables');
$routes->get('bank-reconciliation', 'AccountingController::bankReconciliations');
$routes->get('journal-entries', 'AccountingController::journal');
$routes->get('financial-reports', 'AccountingController::reports');

// Reports menu: hyphenated paths → real module report routes
$routes->addRedirect('sales-reports', 'sales/reports', 302);
$routes->addRedirect('purchase-reports', 'purchase/reports', 302);
$routes->addRedirect('production-reports', 'production/reports', 302);
$routes->addRedirect('inventory-reports', 'inventory/reports', 302);
$routes->addRedirect('hr-reports', 'hr/reports', 302);
$routes->addRedirect('custom-reports', 'reports', 302);

// Inventory Management Routes
$routes->group('inventory', function($routes) {
    // Dashboard
    $routes->get('/', 'InventoryManagementController::index');
    
    // Warehouses
    $routes->get('warehouses', 'InventoryManagementController::warehouses');
    $routes->get('warehouses/create', 'InventoryManagementController::warehouseCreate');
    $routes->post('warehouses/store', 'InventoryManagementController::warehouseStore');
    $routes->get('warehouses/edit/(:num)', 'InventoryManagementController::warehouseEdit/$1');
    $routes->post('warehouses/update/(:num)', 'InventoryManagementController::warehouseUpdate/$1');
    $routes->get('warehouses/view/(:num)', 'InventoryManagementController::warehouseView/$1');
    $routes->get('warehouses/map', 'InventoryManagementController::warehouseMap');
    $routes->get('warehouses/locations/(:num)', 'InventoryManagementController::warehouseLocations/$1');
    $routes->get('warehouses/stock/(:num)', 'InventoryManagementController::warehouseStock/$1');
    
    // Items
    $routes->get('items', 'InventoryManagementController::items');
    $routes->get('items/create', 'InventoryManagementController::itemCreate');
    $routes->post('items/store', 'InventoryManagementController::itemStore');
    $routes->get('items/edit/(:num)', 'InventoryManagementController::itemEdit/$1');
    $routes->post('items/update/(:num)', 'InventoryManagementController::itemUpdate/$1');
    $routes->get('items/view/(:num)', 'InventoryManagementController::itemView/$1');
    
    // Stock Management
    $routes->get('stock', 'InventoryManagementController::stock');
    $routes->get('stock/stock-in', 'InventoryManagementController::stockIn');
    $routes->post('stock/stock-in', 'InventoryManagementController::stockInStore');
    $routes->get('stock/edit/(:num)', 'InventoryManagementController::stockEdit/$1');
    $routes->post('stock/update/(:num)', 'InventoryManagementController::stockUpdate/$1');
    $routes->get('stock/stock-out', 'InventoryManagementController::stockOut');
    $routes->post('stock/stock-out', 'InventoryManagementController::stockOutStore');
    $routes->get('stock/scan', 'InventoryManagementController::stockScan');
    $routes->get('stock/rfid', 'InventoryManagementController::stockRFID');
    $routes->get('stock/count', 'InventoryManagementController::stockCount');
    $routes->get('stock/batch-tracking', 'InventoryManagementController::batchTracking');
    $routes->get('stock/expiry-alerts', 'InventoryManagementController::expiryAlerts');
    
    // Stock Transfers
    $routes->get('transfers', 'InventoryManagementController::transfers');
    $routes->get('transfers/create', 'InventoryManagementController::transferCreate');
    $routes->post('transfers/store', 'InventoryManagementController::transferStore');
    $routes->get('transfers/edit/(:num)', 'InventoryManagementController::transferEdit/$1');
    $routes->post('transfers/update/(:num)', 'InventoryManagementController::transferUpdate/$1');
    $routes->get('transfers/view/(:num)', 'InventoryManagementController::transferView/$1');
    $routes->post('transfers/approve/(:num)', 'InventoryManagementController::transferApprove/$1');
    $routes->post('transfers/start/(:num)', 'InventoryManagementController::transferStart/$1');
    $routes->post('transfers/complete/(:num)', 'InventoryManagementController::transferComplete/$1');
    
    // Stock Adjustments
    $routes->get('adjustments', 'InventoryManagementController::adjustments');
    $routes->get('adjustments/create', 'InventoryManagementController::adjustmentCreate');
    $routes->post('adjustments/store', 'InventoryManagementController::adjustmentStore');
    $routes->get('adjustments/edit/(:num)', 'InventoryManagementController::adjustmentEdit/$1');
    $routes->post('adjustments/update/(:num)', 'InventoryManagementController::adjustmentUpdate/$1');
    $routes->get('adjustments/view/(:num)', 'InventoryManagementController::adjustmentView/$1');
    $routes->post('adjustments/approve/(:num)', 'InventoryManagementController::adjustmentApprove/$1');
    
    // Reports
    $routes->get('reports', 'InventoryManagementController::reports');
    $routes->get('reports/export/(:any)', 'InventoryManagementController::exportReport/$1');
    $routes->get('reports/stock-aging', 'InventoryManagementController::stockAgingReport');
    $routes->get('reports/stock-valuation', 'InventoryManagementController::stockValuationReport');
    $routes->get('reports/movement-analysis', 'InventoryManagementController::movementAnalysisReport');
});

// Production & Manufacturing Routes
$routes->group('production', function($routes) {
    // Dashboard
    $routes->get('/', 'ProductionController::index');
    
    // Bill of Materials (BOM)
    $routes->get('boms', 'ProductionController::boms');
    $routes->get('boms/create', 'ProductionController::bomCreate');
    $routes->post('boms/store', 'ProductionController::bomStore');
    $routes->get('boms/view/(:num)', 'ProductionController::bomView/$1');
    $routes->get('boms/edit/(:num)', 'ProductionController::bomEdit/$1');
    $routes->post('boms/update/(:num)', 'ProductionController::bomUpdate/$1');
    $routes->post('boms/approve/(:num)', 'ProductionController::bomApprove/$1');
    $routes->get('boms/explode/(:num)', 'ProductionController::bomExplode/$1');
    
    // Work Orders
    $routes->get('work-orders', 'ProductionController::workOrders');
    $routes->get('work-orders/create', 'ProductionController::workOrderCreate');
    $routes->post('work-orders/store', 'ProductionController::workOrderStore');
    $routes->get('work-orders/view/(:num)', 'ProductionController::workOrderView/$1');
    $routes->post('work-orders/start/(:num)', 'ProductionController::workOrderStart/$1');
    $routes->post('work-orders/complete/(:num)', 'ProductionController::workOrderComplete/$1');
    
    // Job Cards
    $routes->get('job-cards', 'ProductionController::jobCards');
    $routes->get('job-cards/view/(:num)', 'ProductionController::jobCardView/$1');
    $routes->post('job-cards/start/(:num)', 'ProductionController::jobCardStart/$1');
    $routes->post('job-cards/complete/(:num)', 'ProductionController::jobCardComplete/$1');
    
    // Material Requirements Planning (MRP)
    $routes->get('mrp', 'ProductionController::mrp');
    $routes->post('mrp/run', 'ProductionController::mrpRun');
    
    // Reports
    $routes->get('reports', 'ProductionController::reports');
    $routes->get('reports/export/(:any)', 'ProductionController::exportReport/$1');
});

// Purchase Management Routes
$routes->group('purchase', function($routes) {
    // Dashboard
    $routes->get('/', 'PurchaseManagementController::index');
    
    // Suppliers
    $routes->get('suppliers', 'PurchaseManagementController::suppliers');
    $routes->get('suppliers/create', 'PurchaseManagementController::supplierCreate');
    $routes->post('suppliers/store', 'PurchaseManagementController::supplierStore');
    $routes->get('suppliers/edit/(:num)', 'PurchaseManagementController::supplierEdit/$1');
    $routes->post('suppliers/update/(:num)', 'PurchaseManagementController::supplierUpdate/$1');
    $routes->post('suppliers/activate/(:num)', 'PurchaseManagementController::activateSupplier/$1');
    $routes->post('suppliers/deactivate/(:num)', 'PurchaseManagementController::deactivateSupplier/$1');
    $routes->get('suppliers/performance/(:num)', 'PurchaseManagementController::getSupplierPerformance/$1');
    
    // Purchase Requisitions
    $routes->get('requisitions', 'PurchaseManagementController::purchaseRequisitions');
    $routes->get('requisitions/create', 'PurchaseManagementController::requisitionCreate');
    $routes->post('requisitions/store', 'PurchaseManagementController::requisitionStore');
    $routes->post('requisitions/approve/(:num)', 'PurchaseManagementController::requisitionApprove/$1');
    $routes->post('requisitions/reject/(:num)', 'PurchaseManagementController::requisitionReject/$1');
    $routes->get('requisitions/view/(:num)', 'PurchaseManagementController::requisitionView/$1');
    $routes->get('requisitions/edit/(:num)', 'PurchaseManagementController::requisitionEdit/$1');
    $routes->post('requisitions/update/(:num)', 'PurchaseManagementController::requisitionUpdate/$1');
    $routes->get('requisitions/print/(:num)', 'PurchaseManagementController::requisitionPrint/$1');
    
    // Purchase Orders
    $routes->get('orders', 'PurchaseManagementController::purchaseOrders');
    $routes->get('orders/create', 'PurchaseManagementController::orderCreate');
    $routes->post('orders/store', 'PurchaseManagementController::orderStore');
    $routes->post('orders/approve/(:num)', 'PurchaseManagementController::orderApprove/$1');
    $routes->get('orders/view/(:num)', 'PurchaseManagementController::orderView/$1');
    $routes->get('orders/edit/(:num)', 'PurchaseManagementController::orderEdit/$1');
    $routes->post('orders/update/(:num)', 'PurchaseManagementController::orderUpdate/$1');
    $routes->get('orders/print/(:num)', 'PurchaseManagementController::orderPrint/$1');
    
    // Goods Receipt Notes (GRN)
    $routes->get('grn', 'PurchaseManagementController::goodsReceipts');
    $routes->get('grn/create', 'PurchaseManagementController::grnCreate');
    $routes->post('grn/store', 'PurchaseManagementController::grnStore');
    $routes->get('grn/view/(:num)', 'PurchaseManagementController::grnView/$1');
    $routes->get('grn/edit/(:num)', 'PurchaseManagementController::grnEdit/$1');
    $routes->post('grn/update/(:num)', 'PurchaseManagementController::grnUpdate/$1');
    $routes->get('grn/print/(:num)', 'PurchaseManagementController::grnPrint/$1');
    
    // Supplier Invoices
    $routes->get('invoices', 'PurchaseManagementController::supplierInvoices');
    $routes->get('invoices/create', 'PurchaseManagementController::invoiceCreate');
    $routes->post('invoices/store', 'PurchaseManagementController::invoiceStore');
    $routes->get('invoices/view/(:num)', 'PurchaseManagementController::invoiceView/$1');
    $routes->get('invoices/edit/(:num)', 'PurchaseManagementController::invoiceEdit/$1');
    $routes->post('invoices/update/(:num)', 'PurchaseManagementController::invoiceUpdate/$1');
    $routes->get('invoices/print/(:num)', 'PurchaseManagementController::invoicePrint/$1');
    
    // Debit Notes
    $routes->get('debit-notes', 'PurchaseManagementController::debitNotes');
    $routes->get('debit-notes/create', 'PurchaseManagementController::debitNoteCreate');
    $routes->post('debit-notes/store', 'PurchaseManagementController::debitNoteStore');
    $routes->get('debit-notes/view/(:num)', 'PurchaseManagementController::debitNoteView/$1');
    $routes->get('debit-notes/edit/(:num)', 'PurchaseManagementController::debitNoteEdit/$1');
    $routes->post('debit-notes/update/(:num)', 'PurchaseManagementController::debitNoteUpdate/$1');
    $routes->post('debit-notes/approve/(:num)', 'PurchaseManagementController::debitNoteApprove/$1');
    $routes->post('debit-notes/reject/(:num)', 'PurchaseManagementController::debitNoteReject/$1');
    $routes->post('debit-notes/process/(:num)', 'PurchaseManagementController::debitNoteProcess/$1');
    $routes->get('debit-notes/print/(:num)', 'PurchaseManagementController::debitNotePrint/$1');
    $routes->get('debit-notes/view/(:num)', 'PurchaseManagementController::debitNoteView/$1');
    $routes->get('debit-notes/edit/(:num)', 'PurchaseManagementController::debitNoteEdit/$1');
    $routes->post('debit-notes/update/(:num)', 'PurchaseManagementController::debitNoteUpdate/$1');
    $routes->get('debit-notes/print/(:num)', 'PurchaseManagementController::debitNotePrint/$1');
    
    // Reports
    $routes->get('reports', 'PurchaseManagementController::reports');
    $routes->get('reports/export/(:any)', 'PurchaseManagementController::exportReport/$1');
});

// Authentication Routes
// `Auth::login` already handles both GET (show form) and POST (process login),
// so point both HTTP methods to the same controller method.
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Dashboard
$routes->get('dashboard', 'Dashboard::index');

// Purchase Requisition Routes
$routes->get('purchase-requisition', 'PurchaseRequisitionController::index');
$routes->get('purchase-requisition/create', 'PurchaseRequisitionController::create');
$routes->post('purchase-requisition/store', 'PurchaseRequisitionController::store');
$routes->get('purchase-requisition/edit/(:num)', 'PurchaseRequisitionController::edit/$1');
$routes->post('purchase-requisition/update/(:num)', 'PurchaseRequisitionController::update/$1');
$routes->get('purchase-requisition/delete/(:num)', 'PurchaseRequisitionController::delete/$1');

// Purchase Order Routes
$routes->get('purchase-order', 'PurchaseOrderController::index');
$routes->get('purchase-order/create', 'PurchaseOrderController::create');
$routes->post('purchase-order/store', 'PurchaseOrderController::store');
$routes->get('purchase-order/show/(:num)', 'PurchaseOrderController::show/$1');
$routes->get('purchase-order/edit/(:num)', 'PurchaseOrderController::edit/$1');
$routes->post('purchase-order/update/(:num)', 'PurchaseOrderController::update/$1');
$routes->delete('purchase-order/delete/(:num)', 'PurchaseOrderController::delete/$1');
$routes->get('purchase-order/approve/(:num)', 'PurchaseOrderController::approve/$1');
$routes->get('purchase-order/order/(:num)', 'PurchaseOrderController::order/$1');
$routes->get('purchase-order/receive/(:num)', 'PurchaseOrderController::receive/$1');
$routes->get('purchase-order/cancel/(:num)', 'PurchaseOrderController::cancel/$1');
$routes->get('purchase-order/print/(:num)', 'PurchaseOrderController::print/$1');
$routes->get('purchase-order/export', 'PurchaseOrderController::export');

// Purchase Return Routes
$routes->get('purchase-return', 'PurchaseReturnController::index');
$routes->get('purchase-return/create', 'PurchaseReturnController::create');
$routes->post('purchase-return/store', 'PurchaseReturnController::store');
$routes->get('purchase-return/show/(:num)', 'PurchaseReturnController::show/$1');
$routes->get('purchase-return/edit/(:num)', 'PurchaseReturnController::edit/$1');
$routes->post('purchase-return/update/(:num)', 'PurchaseReturnController::update/$1');
$routes->delete('purchase-return/delete/(:num)', 'PurchaseReturnController::delete/$1');
$routes->get('purchase-return/approve/(:num)', 'PurchaseReturnController::approve/$1');
$routes->get('purchase-return/process/(:num)', 'PurchaseReturnController::process/$1');
$routes->get('purchase-return/complete/(:num)', 'PurchaseReturnController::complete/$1');
$routes->get('purchase-return/cancel/(:num)', 'PurchaseReturnController::cancel/$1');
$routes->get('purchase-return/print/(:num)', 'PurchaseReturnController::print/$1');
$routes->get('purchase-return/export', 'PurchaseReturnController::export');
$routes->get('purchase-return/get-po-items/(:num)', 'PurchaseReturnController::getPOItems/$1');
$routes->get('purchase-return/get-products', 'PurchaseReturnController::getProducts');

// Purchase Bill Routes
$routes->get('purchase-bill', 'PurchaseBillController::index');
$routes->get('purchase-bill/create', 'PurchaseBillController::create');
$routes->post('purchase-bill/store', 'PurchaseBillController::store');
$routes->get('purchase-bill/show/(:num)', 'PurchaseBillController::show/$1');
$routes->get('purchase-bill/edit/(:num)', 'PurchaseBillController::edit/$1');
$routes->post('purchase-bill/update/(:num)', 'PurchaseBillController::update/$1');
$routes->get('purchase-bill/delete/(:num)', 'PurchaseBillController::delete/$1');
$routes->post('purchase-bill/update-status/(:num)', 'PurchaseBillController::updateStatus/$1');
$routes->post('purchase-bill/record-payment/(:num)', 'PurchaseBillController::recordPayment/$1');
$routes->get('purchase-bill/overdue', 'PurchaseBillController::getOverdueBills');
$routes->get('purchase-bill/print/(:num)', 'PurchaseBillController::print/$1');
$routes->get('purchase-bill/download/(:num)', 'PurchaseBillController::download/$1');
$routes->get('purchase-bill/export', 'PurchaseBillController::export');
$routes->get('purchase-bill/get-products', 'PurchaseBillController::getProducts');
$routes->get('purchase-bill/get-purchase-orders', 'PurchaseBillController::getPurchaseOrders');

// Goods Receipt Note (GRN) Routes
$routes->get('goods-receipt', 'GoodsReceiptController::index');
$routes->get('goods-receipt/create', 'GoodsReceiptController::create');
$routes->post('goods-receipt/store', 'GoodsReceiptController::store');
$routes->get('goods-receipt/show/(:num)', 'GoodsReceiptController::show/$1');
$routes->get('goods-receipt/edit/(:num)', 'GoodsReceiptController::edit/$1');
$routes->post('goods-receipt/update/(:num)', 'GoodsReceiptController::update/$1');
$routes->get('goods-receipt/delete/(:num)', 'GoodsReceiptController::delete/$1');
$routes->get('goods-receipt/approve/(:num)', 'GoodsReceiptController::approve/$1');
$routes->get('goods-receipt/print/(:num)', 'GoodsReceiptController::print/$1');
$routes->get('goods-receipt/export', 'GoodsReceiptController::export');
$routes->get('goods-receipt/get-po-items/(:num)', 'GoodsReceiptController::getPOItems/$1');

// Supplier Invoice Routes
$routes->get('supplier-invoice', 'SupplierInvoiceController::index');
$routes->get('supplier-invoice/create', 'SupplierInvoiceController::create');
$routes->post('supplier-invoice/store', 'SupplierInvoiceController::store');
$routes->get('supplier-invoice/show/(:num)', 'SupplierInvoiceController::show/$1');
$routes->get('supplier-invoice/edit/(:num)', 'SupplierInvoiceController::edit/$1');
$routes->post('supplier-invoice/update/(:num)', 'SupplierInvoiceController::update/$1');
$routes->get('supplier-invoice/delete/(:num)', 'SupplierInvoiceController::delete/$1');
$routes->get('supplier-invoice/approve/(:num)', 'SupplierInvoiceController::approve/$1');
$routes->post('supplier-invoice/record-payment/(:num)', 'SupplierInvoiceController::recordPayment/$1');
$routes->get('supplier-invoice/print/(:num)', 'SupplierInvoiceController::print/$1');
$routes->get('supplier-invoice/export', 'SupplierInvoiceController::export');
$routes->get('supplier-invoice/overdue', 'SupplierInvoiceController::getOverdueInvoices');

// Debit Note Routes
$routes->get('debit-note', 'DebitNoteController::index');
$routes->get('debit-note/create', 'DebitNoteController::create');
$routes->post('debit-note/store', 'DebitNoteController::store');
$routes->get('debit-note/show/(:num)', 'DebitNoteController::show/$1');
$routes->get('debit-note/edit/(:num)', 'DebitNoteController::edit/$1');
$routes->post('debit-note/update/(:num)', 'DebitNoteController::update/$1');
$routes->get('debit-note/delete/(:num)', 'DebitNoteController::delete/$1');
$routes->get('debit-note/approve/(:num)', 'DebitNoteController::approve/$1');
$routes->get('debit-note/print/(:num)', 'DebitNoteController::print/$1');
$routes->get('debit-note/export', 'DebitNoteController::export');

// Purchase Reports Routes
$routes->get('purchase-report', 'PurchaseReportController::index');
$routes->get('purchase-report/pending-orders', 'PurchaseReportController::pendingOrders');
$routes->get('purchase-report/supplier-history', 'PurchaseReportController::supplierHistory');
$routes->get('purchase-report/price-trends', 'PurchaseReportController::priceTrends');
$routes->get('purchase-report/quality-metrics', 'PurchaseReportController::qualityMetrics');
$routes->get('purchase-report/cost-analysis', 'PurchaseReportController::costAnalysis');
$routes->get('purchase-report/export', 'PurchaseReportController::exportReport');

// Supplier Master Routes
$routes->get('supplier', 'SupplierController::index');
$routes->get('supplier/create', 'SupplierController::create');
$routes->post('supplier/store', 'SupplierController::store');
$routes->get('supplier/show/(:num)', 'SupplierController::show/$1');
$routes->get('supplier/edit/(:num)', 'SupplierController::edit/$1');
$routes->post('supplier/update/(:num)', 'SupplierController::update/$1');
$routes->get('supplier/delete/(:num)', 'SupplierController::delete/$1');
$routes->post('supplier/toggle-status/(:num)', 'SupplierController::toggleStatus/$1');
$routes->get('supplier/get-by-category', 'SupplierController::getSuppliersByCategory');
$routes->get('supplier/outstanding-payments', 'SupplierController::getOutstandingPayments');
$routes->get('supplier/print/(:num)', 'SupplierController::print/$1');
$routes->get('supplier/export', 'SupplierController::export');

// Customer Master Routes
$routes->get('customer', 'CustomerController::index');
$routes->get('customer/create', 'CustomerController::create');
$routes->post('customer/store', 'CustomerController::store');
$routes->get('customer/show/(:num)', 'CustomerController::show/$1');
$routes->get('customer/edit/(:num)', 'CustomerController::edit/$1');
$routes->post('customer/update/(:num)', 'CustomerController::update/$1');
$routes->put('customer/update/(:num)', 'CustomerController::update/$1');
$routes->get('customer/delete/(:num)', 'CustomerController::delete/$1');
$routes->post('customer/toggle-status/(:num)', 'CustomerController::toggleStatus/$1');
$routes->get('customer/get-by-zone', 'CustomerController::getCustomersByZone');
$routes->get('customer/get-by-region', 'CustomerController::getCustomersByRegion');
$routes->get('customer/outstanding-payments', 'CustomerController::getOutstandingPayments');
$routes->get('customer/print/(:num)', 'CustomerController::print/$1');
$routes->get('customer/export', 'CustomerController::export');
$routes->get('customer/performance-report/(:num)', 'CustomerController::performanceReport/$1');
$routes->get('customer/get-sales-zones', 'CustomerController::getSalesZones');
$routes->get('customer/get-sales-regions', 'CustomerController::getSalesRegions');
$routes->get('customer/get/(:num)', 'CustomerController::getCustomer/$1');
$routes->get('customer/search', 'CustomerController::searchCustomers');

// Material Master Routes
$routes->get('product', 'ProductController::index');
$routes->get('product/create', 'ProductController::create');
$routes->post('product/store', 'ProductController::store');
$routes->get('product/show/(:num)', 'ProductController::show/$1');
$routes->get('product/edit/(:num)', 'ProductController::edit/$1');
$routes->post('product/update/(:num)', 'ProductController::update/$1');
$routes->get('product/delete/(:num)', 'ProductController::delete/$1');
$routes->post('product/toggle-status/(:num)', 'ProductController::toggleStatus/$1');
$routes->get('product/get-by-material-type', 'ProductController::getProductsByMaterialType');
$routes->get('product/get-by-category', 'ProductController::getProductsByCategory');
$routes->get('product/export', 'ProductController::export');
$routes->get('product/search', 'ProductController::searchProducts');
$routes->get('product/finished-goods-dropdown', 'ProductController::getFinishedGoodsForDropdown');
$routes->get('product/details/(:num)', 'ProductController::getProductDetails/$1');
$routes->get('product/stock/(:num)', 'ProductController::getStock/$1');
$routes->get('product/performance/(:num)', 'ProductController::performanceReport/$1');

// Category Master Routes
$routes->get('category', 'CategoryController::index');
$routes->get('category/create', 'CategoryController::create');
$routes->post('category/store', 'CategoryController::store');
$routes->get('category/show/(:num)', 'CategoryController::show/$1');
$routes->get('category/edit/(:num)', 'CategoryController::edit/$1');
$routes->post('category/update/(:num)', 'CategoryController::update/$1');
$routes->get('category/delete/(:num)', 'CategoryController::delete/$1');
$routes->post('category/toggle-status/(:num)', 'CategoryController::toggleStatus/$1');
$routes->get('category/get-by-type', 'CategoryController::getCategoriesByType');
$routes->get('category/export', 'CategoryController::export');

// Warehouse Master Routes
$routes->get('warehouse', 'WarehouseController::index');
$routes->get('warehouse/create', 'WarehouseController::create');
$routes->post('warehouse/store', 'WarehouseController::store');
$routes->get('warehouse/show/(:num)', 'WarehouseController::show/$1');
$routes->get('warehouse/edit/(:num)', 'WarehouseController::edit/$1');
$routes->post('warehouse/update/(:num)', 'WarehouseController::update/$1');
$routes->get('warehouse/delete/(:num)', 'WarehouseController::delete/$1');
$routes->post('warehouse/toggle-status/(:num)', 'WarehouseController::toggleStatus/$1');
$routes->get('warehouse/get-warehouses', 'WarehouseController::getWarehouses');
$routes->get('warehouse/search', 'WarehouseController::searchWarehouses');
$routes->get('warehouse/export', 'WarehouseController::export');

// Employee Master Routes
$routes->get('employee', 'EmployeeController::index');
$routes->get('employee/create', 'EmployeeController::create');
$routes->post('employee/store', 'EmployeeController::store');
$routes->get('employee/show/(:num)', 'EmployeeController::show/$1');
$routes->get('employee/edit/(:num)', 'EmployeeController::edit/$1');
$routes->post('employee/update/(:num)', 'EmployeeController::update/$1');
$routes->get('employee/delete/(:num)', 'EmployeeController::delete/$1');
$routes->post('employee/toggle-status/(:num)', 'EmployeeController::toggleStatus/$1');
$routes->get('employee/get-by-department', 'EmployeeController::getEmployeesByDepartment');
$routes->get('employee/search', 'EmployeeController::searchEmployees');
$routes->get('employee/export', 'EmployeeController::export');

// Department Master Routes
$routes->get('department', 'DepartmentController::index');
$routes->get('department/create', 'DepartmentController::create');
$routes->post('department/store', 'DepartmentController::store');
$routes->get('department/show/(:num)', 'DepartmentController::show/$1');
$routes->get('department/edit/(:num)', 'DepartmentController::edit/$1');
$routes->post('department/update/(:num)', 'DepartmentController::update/$1');
$routes->get('department/delete/(:num)', 'DepartmentController::delete/$1');
$routes->post('department/toggle-status/(:num)', 'DepartmentController::toggleStatus/$1');
$routes->get('department/get-departments', 'DepartmentController::getDepartments');
$routes->get('department/search', 'DepartmentController::searchDepartments');
$routes->get('department/export', 'DepartmentController::export');

// Production Settings Routes
$routes->get('production-settings', 'ProductionSettingsController::index');
$routes->get('production-settings/create', 'ProductionSettingsController::create');
$routes->post('production-settings/store', 'ProductionSettingsController::store');
$routes->get('production-settings/show/(:num)', 'ProductionSettingsController::show/$1');
$routes->get('production-settings/edit/(:num)', 'ProductionSettingsController::edit/$1');
$routes->post('production-settings/update/(:num)', 'ProductionSettingsController::update/$1');
$routes->get('production-settings/delete/(:num)', 'ProductionSettingsController::delete/$1');
$routes->post('production-settings/toggle-status/(:num)', 'ProductionSettingsController::toggleStatus/$1');
$routes->post('production-settings/calculate-production/(:num)', 'ProductionSettingsController::calculateProduction/$1');
$routes->post('production-settings/get-material-requirements/(:num)', 'ProductionSettingsController::getMaterialRequirements/$1');
$routes->post('production-settings/check-availability/(:num)', 'ProductionSettingsController::checkAvailability/$1');

// Gate Entry Routes
$routes->get('gate-entry', 'SimpleModuleController::index');
$routes->get('gate-entry/create', 'SimpleModuleController::create');
$routes->post('gate-entry/store', 'SimpleModuleController::store');
$routes->get('gate-entry/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('gate-entry/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('gate-entry/delete/(:num)', 'SimpleModuleController::delete/$1');

// Gate Exit Routes
$routes->get('gate-exit', 'SimpleModuleController::index');
$routes->get('gate-exit/create', 'SimpleModuleController::create');
$routes->post('gate-exit/store', 'SimpleModuleController::store');
$routes->get('gate-exit/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('gate-exit/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('gate-exit/delete/(:num)', 'SimpleModuleController::delete/$1');

// Visitor Management Routes
$routes->get('visitor-management', 'SimpleModuleController::index');
$routes->get('visitor-management/create', 'SimpleModuleController::create');
$routes->post('visitor-management/store', 'SimpleModuleController::store');
$routes->get('visitor-management/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('visitor-management/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('visitor-management/delete/(:num)', 'SimpleModuleController::delete/$1');

// Supplier Master Routes
$routes->get('supplier-master', 'SimpleModuleController::index');
$routes->get('supplier-master/create', 'SimpleModuleController::create');
$routes->post('supplier-master/store', 'SimpleModuleController::store');
$routes->get('supplier-master/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('supplier-master/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('supplier-master/delete/(:num)', 'SimpleModuleController::delete/$1');

// BOM Management Routes
$routes->get('bom', 'SimpleModuleController::index');
$routes->get('bom/create', 'SimpleModuleController::create');
$routes->post('bom/store', 'SimpleModuleController::store');
$routes->get('bom/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('bom/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('bom/delete/(:num)', 'SimpleModuleController::delete/$1');



// Production Planning Routes
$routes->get('production-planning', 'SimpleModuleController::index');
$routes->get('production-planning/create', 'SimpleModuleController::create');
$routes->post('production-planning/store', 'SimpleModuleController::store');
$routes->get('production-planning/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('production-planning/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('production-planning/delete/(:num)', 'SimpleModuleController::delete/$1');

// Production Tracking Routes
$routes->get('production-tracking', 'SimpleModuleController::index');
$routes->get('production-tracking/create', 'SimpleModuleController::create');
$routes->post('production-tracking/store', 'SimpleModuleController::store');
$routes->get('production-tracking/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('production-tracking/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('production-tracking/delete/(:num)', 'SimpleModuleController::delete/$1');

// Quality Control Routes
$routes->get('quality-control', 'SimpleModuleController::index');
$routes->get('quality-control/create', 'SimpleModuleController::create');
$routes->post('quality-control/store', 'SimpleModuleController::store');
$routes->get('quality-control/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('quality-control/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('quality-control/delete/(:num)', 'SimpleModuleController::delete/$1');

// Sales Orders Routes
$routes->get('sales-order', 'SalesOrderController::index');
$routes->get('sales-order/create', 'SalesOrderController::create');
$routes->post('sales-order/store', 'SalesOrderController::store');
$routes->get('sales-order/show/(:num)', 'SalesOrderController::show/$1');
$routes->get('sales-order/edit/(:num)', 'SalesOrderController::edit/$1');
$routes->post('sales-order/update/(:num)', 'SalesOrderController::update/$1');
$routes->get('sales-order/delete/(:num)', 'SalesOrderController::delete/$1');
$routes->post('sales-order/update-status/(:num)', 'SalesOrderController::updateStatus/$1');
$routes->get('sales-order/export', 'SalesOrderController::export');
$routes->get('sales-order/print/(:num)', 'SalesOrderController::print/$1');
$routes->get('sales-order/get-products', 'SalesOrderController::getProducts');
$routes->get('sales-order/get-finished-goods-dropdown', 'SalesOrderController::getFinishedGoodsForDropdown');
$routes->get('sales-order/get-customers', 'SalesOrderController::getCustomers');

// Sales Orders Routes (Plural - for compatibility)
$routes->get('sales-orders', 'SalesOrderController::index');
$routes->get('sales-orders/create', 'SalesOrderController::create');
$routes->post('sales-orders/store', 'SalesOrderController::store');
$routes->get('sales-orders/show/(:num)', 'SalesOrderController::show/$1');
$routes->get('sales-orders/edit/(:num)', 'SalesOrderController::edit/$1');
$routes->post('sales-orders/update/(:num)', 'SalesOrderController::update/$1');
$routes->get('sales-orders/delete/(:num)', 'SalesOrderController::delete/$1');
$routes->post('sales-orders/update-status/(:num)', 'SalesOrderController::updateStatus/$1');
$routes->get('sales-orders/export', 'SalesOrderController::export');
$routes->get('sales-orders/print/(:num)', 'SalesOrderController::print/$1');
$routes->get('sales-orders/get-products', 'SalesOrderController::getProducts');
$routes->get('sales-orders/get-customers', 'SalesOrderController::getCustomers');

// ==================== SALES & DISTRIBUTION MODULE ====================

// Sales & Distribution Dashboard
$routes->get('sales', 'SalesDistributionController::index');

// Customer Master (CRM) Routes
$routes->get('sales/customers', 'SalesDistributionController::customers');
$routes->get('sales/customers/create', 'SalesDistributionController::customerCreate');
$routes->post('sales/customers/store', 'SalesDistributionController::customerStore');
$routes->get('sales/customers/view/(:num)', 'SalesDistributionController::customerView/$1');
$routes->get('sales/customers/edit/(:num)', 'SalesDistributionController::customerEdit/$1');
$routes->post('sales/customers/update/(:num)', 'SalesDistributionController::customerUpdate/$1');
$routes->post('sales/customers/toggle-status/(:num)', 'SalesDistributionController::customerToggleStatus/$1');
$routes->get('sales/customers/export', 'SalesDistributionController::customerExport');

// Lead Management Routes
$routes->get('sales/leads', 'SalesDistributionController::leads');
$routes->get('sales/leads/create', 'SalesDistributionController::leadCreate');
$routes->post('sales/leads/store', 'SalesDistributionController::leadStore');
$routes->get('sales/leads/view/(:num)', 'SalesDistributionController::leadView/$1');
$routes->get('sales/leads/edit/(:num)', 'SalesDistributionController::leadEdit/$1');
$routes->post('sales/leads/update/(:num)', 'SalesDistributionController::leadUpdate/$1');
$routes->post('sales/leads/update-status/(:num)', 'SalesDistributionController::leadUpdateStatus/$1');
$routes->post('sales/leads/assign/(:num)', 'SalesDistributionController::leadAssign/$1');
$routes->post('sales/leads/convert/(:num)', 'SalesDistributionController::leadConvert/$1');
$routes->get('sales/leads/export', 'SalesDistributionController::leadExport');

// Quotation Routes
$routes->get('sales/quotations', 'SalesDistributionController::quotations');
$routes->get('sales/quotations/create', 'SalesDistributionController::quotationCreate');
$routes->post('sales/quotations/store', 'SalesDistributionController::quotationStore');
$routes->get('sales/quotations/view/(:num)', 'SalesDistributionController::quotationView/$1');
$routes->get('sales/quotations/edit/(:num)', 'SalesDistributionController::quotationEdit/$1');
$routes->post('sales/quotations/update/(:num)', 'SalesDistributionController::quotationUpdate/$1');
$routes->post('sales/quotations/update-status/(:num)', 'SalesDistributionController::quotationUpdateStatus/$1');
$routes->get('sales/quotations/convert/(:num)', 'SalesDistributionController::convertToOrder/$1');
$routes->post('sales/quotations/convert/(:num)', 'SalesDistributionController::quotationConvert/$1');
$routes->get('sales/quotations/print/(:num)', 'SalesDistributionController::quotationPrint/$1');
$routes->get('sales/quotations/export', 'SalesDistributionController::quotationExport');

// Sales Orders Routes (Enhanced)
$routes->get('sales/orders', 'SalesDistributionController::salesOrders');
$routes->get('sales/orders/create', 'SalesDistributionController::orderCreate');
$routes->post('sales/orders/store', 'SalesDistributionController::orderStore');
$routes->get('sales/orders/view/(:num)', 'SalesDistributionController::orderView/$1');
$routes->get('sales/orders/edit/(:num)', 'SalesDistributionController::orderEdit/$1');
$routes->post('sales/orders/update/(:num)', 'SalesDistributionController::orderUpdate/$1');
$routes->post('sales/orders/update-status/(:num)', 'SalesDistributionController::orderUpdateStatus/$1');
$routes->get('sales/orders/print/(:num)', 'SalesDistributionController::orderPrint/$1');
$routes->get('sales/orders/export', 'SalesDistributionController::orderExport');

// Dispatch Notes Routes
$routes->get('sales/dispatch', 'SalesDistributionController::dispatchNotes');
$routes->get('sales/dispatch/create', 'SalesDistributionController::dispatchCreate');
$routes->post('sales/dispatch/store', 'SalesDistributionController::dispatchStore');
$routes->get('sales/dispatch/view/(:num)', 'SalesDistributionController::dispatchView/$1');
$routes->get('sales/dispatch/edit/(:num)', 'SalesDistributionController::dispatchEdit/$1');
$routes->post('sales/dispatch/update/(:num)', 'SalesDistributionController::dispatchUpdate/$1');
$routes->post('sales/dispatch/update-status/(:num)', 'SalesDistributionController::dispatchUpdateStatus/$1');
$routes->get('sales/dispatch/print/(:num)', 'SalesDistributionController::dispatchPrint/$1');
$routes->get('sales/dispatch/export', 'SalesDistributionController::dispatchExport');

// Invoice Routes
$routes->get('sales/invoices', 'SalesDistributionController::invoices');
$routes->get('sales/invoices/create', 'SalesDistributionController::invoiceCreate');
$routes->post('sales/invoices/store', 'SalesDistributionController::invoiceStore');
$routes->get('sales/invoices/view/(:num)', 'SalesDistributionController::invoiceView/$1');
$routes->get('sales/invoices/edit/(:num)', 'SalesDistributionController::invoiceEdit/$1');
$routes->post('sales/invoices/update/(:num)', 'SalesDistributionController::invoiceUpdate/$1');
$routes->post('sales/invoices/update-status/(:num)', 'SalesDistributionController::invoiceUpdateStatus/$1');
$routes->get('sales/invoices/print/(:num)', 'SalesDistributionController::invoicePrint/$1');
$routes->get('sales/invoices/export', 'SalesDistributionController::invoiceExport');

// Sales Returns Routes
$routes->get('sales/returns', 'SalesDistributionController::salesReturns');
$routes->get('sales/returns/create', 'SalesDistributionController::returnCreate');
$routes->post('sales/returns/store', 'SalesDistributionController::returnStore');
$routes->get('sales/returns/view/(:num)', 'SalesDistributionController::returnView/$1');
$routes->get('sales/returns/edit/(:num)', 'SalesDistributionController::returnEdit/$1');
$routes->post('sales/returns/update/(:num)', 'SalesDistributionController::returnUpdate/$1');
$routes->post('sales/returns/update-status/(:num)', 'SalesDistributionController::returnUpdateStatus/$1');
$routes->get('sales/returns/print/(:num)', 'SalesDistributionController::returnPrint/$1');
$routes->get('sales/returns/export', 'SalesDistributionController::returnExport');

// Customer Payments Routes
$routes->get('sales/payments', 'SalesDistributionController::payments');
$routes->get('sales/payments/create', 'SalesDistributionController::paymentCreate');
$routes->post('sales/payments/store', 'SalesDistributionController::paymentStore');
$routes->get('sales/payments/view/(:num)', 'SalesDistributionController::paymentView/$1');
$routes->get('sales/payments/edit/(:num)', 'SalesDistributionController::paymentEdit/$1');
$routes->post('sales/payments/update/(:num)', 'SalesDistributionController::paymentUpdate/$1');
$routes->post('sales/payments/update-status/(:num)', 'SalesDistributionController::paymentUpdateStatus/$1');
$routes->get('sales/payments/print/(:num)', 'SalesDistributionController::paymentPrint/$1');
$routes->get('sales/payments/export', 'SalesDistributionController::paymentExport');

// Distributor Management Routes
$routes->get('sales/distributors', 'SalesDistributionController::distributors');
$routes->get('sales/distributors/create', 'SalesDistributionController::distributorCreate');
$routes->post('sales/distributors/store', 'SalesDistributionController::distributorStore');
$routes->get('sales/distributors/view/(:num)', 'SalesDistributionController::distributorView/$1');
$routes->get('sales/distributors/edit/(:num)', 'SalesDistributionController::distributorEdit/$1');
$routes->post('sales/distributors/update/(:num)', 'SalesDistributionController::distributorUpdate/$1');
$routes->post('sales/distributors/toggle-status/(:num)', 'SalesDistributionController::distributorToggleStatus/$1');
$routes->get('sales/distributors/export', 'SalesDistributionController::distributorExport');

// Sales Reports Routes
$routes->get('sales/reports', 'SalesDistributionController::reports');
$routes->get('sales/reports/customer', 'SalesDistributionController::customerReports');
$routes->get('sales/reports/product', 'SalesDistributionController::productReports');
$routes->get('sales/reports/region', 'SalesDistributionController::regionReports');
$routes->get('sales/reports/trends', 'SalesDistributionController::salesTrends');
$routes->get('sales/reports/export', 'SalesDistributionController::exportReports');

// API Routes for AJAX
$routes->get('sales/api/customers', 'SalesDistributionController::apiGetCustomers');
$routes->get('sales/api/products', 'SalesDistributionController::apiGetProducts');
$routes->get('sales/api/quotations', 'SalesDistributionController::apiGetQuotations');
$routes->get('sales/api/orders', 'SalesDistributionController::apiGetOrders');
$routes->get('sales/api/dispatches', 'SalesDistributionController::apiGetDispatches');
$routes->get('sales/api/invoices', 'SalesDistributionController::apiGetInvoices');
$routes->get('sales/api/returns', 'SalesDistributionController::apiGetReturns');
$routes->get('sales/api/payments', 'SalesDistributionController::apiGetPayments');
$routes->get('sales/api/distributors', 'SalesDistributionController::apiGetDistributors');

// Dispatch Notes Routes
$routes->get('dispatch', 'DispatchController::index');
$routes->get('dispatch/create', 'DispatchController::create');
$routes->post('dispatch/store', 'DispatchController::store');
$routes->get('dispatch/show/(:num)', 'DispatchController::show/$1');
$routes->get('dispatch/edit/(:num)', 'DispatchController::edit/$1');
$routes->post('dispatch/update/(:num)', 'DispatchController::update/$1');
$routes->get('dispatch/delete/(:num)', 'DispatchController::delete/$1');
$routes->post('dispatch/update-status/(:num)', 'DispatchController::updateStatus/$1');
$routes->get('dispatch/export', 'DispatchController::export');
$routes->get('dispatch/print/(:num)', 'DispatchController::print/$1');
$routes->get('dispatch/get-sales-orders', 'DispatchController::getSalesOrders');

// Invoice Routes
$routes->get('invoice', 'InvoiceController::index');
$routes->get('invoice/create', 'InvoiceController::create');
$routes->post('invoice/store', 'InvoiceController::store');
$routes->get('invoice/show/(:num)', 'InvoiceController::show/$1');
$routes->get('invoice/edit/(:num)', 'InvoiceController::edit/$1');
$routes->post('invoice/update/(:num)', 'InvoiceController::update/$1');
$routes->get('invoice/delete/(:num)', 'InvoiceController::delete/$1');
$routes->post('invoice/update-status/(:num)', 'InvoiceController::updateStatus/$1');
$routes->post('invoice/record-payment/(:num)', 'InvoiceController::recordPayment/$1');
$routes->get('invoice/export', 'InvoiceController::export');
$routes->get('invoice/print/(:num)', 'InvoiceController::printInvoice/$1');
$routes->get('invoice/get-sales-order-items/(:num)', 'InvoiceController::getSalesOrderItems/$1');
$routes->get('invoice/get-products', 'InvoiceController::getProducts');
$routes->get('invoice/get-customers', 'InvoiceController::getCustomers');

// Sales Invoice Routes (Alias for Invoice)
$routes->get('sales-invoice', 'InvoiceController::index');
$routes->get('sales-invoice/create', 'InvoiceController::create');
$routes->post('sales-invoice/store', 'InvoiceController::store');
$routes->get('sales-invoice/show/(:num)', 'InvoiceController::show/$1');
$routes->get('sales-invoice/edit/(:num)', 'InvoiceController::edit/$1');
$routes->post('sales-invoice/update/(:num)', 'InvoiceController::update/$1');
$routes->get('sales-invoice/delete/(:num)', 'InvoiceController::delete/$1');
$routes->post('sales-invoice/update-status/(:num)', 'InvoiceController::updateStatus/$1');
$routes->post('sales-invoice/record-payment/(:num)', 'InvoiceController::recordPayment/$1');
$routes->get('sales-invoice/export', 'InvoiceController::export');
$routes->get('sales-invoice/print/(:num)', 'InvoiceController::printInvoice/$1');
$routes->get('sales-invoice/get-sales-order-items/(:num)', 'InvoiceController::getSalesOrderItems/$1');
$routes->get('sales-invoice/get-products', 'InvoiceController::getProducts');
$routes->get('sales-invoice/get-customers', 'InvoiceController::getCustomers');

// Sales Return Routes
$routes->get('sales-return', 'SalesReturnController::index');
$routes->get('sales-return/create', 'SalesReturnController::create');
$routes->post('sales-return/store', 'SalesReturnController::store');
$routes->get('sales-return/show/(:num)', 'SalesReturnController::show/$1');
$routes->get('sales-return/edit/(:num)', 'SalesReturnController::edit/$1');
$routes->post('sales-return/update/(:num)', 'SalesReturnController::update/$1');
$routes->get('sales-return/delete/(:num)', 'SalesReturnController::delete/$1');
$routes->post('sales-return/update-status/(:num)', 'SalesReturnController::updateStatus/$1');
$routes->get('sales-return/export', 'SalesReturnController::export');
$routes->get('sales-return/print/(:num)', 'SalesReturnController::print/$1');
$routes->get('sales-return/get-invoices', 'SalesReturnController::getInvoices');
$routes->get('sales-return/get-products', 'SalesReturnController::getProducts');
$routes->get('sales-return/get-customers', 'SalesReturnController::getCustomers');

// Customer Payment Routes
$routes->get('customer-payment', 'CustomerPaymentController::index');
$routes->get('customer-payment/create', 'CustomerPaymentController::create');
$routes->post('customer-payment/store', 'CustomerPaymentController::store');
$routes->get('customer-payment/show/(:num)', 'CustomerPaymentController::show/$1');
$routes->get('customer-payment/edit/(:num)', 'CustomerPaymentController::edit/$1');
$routes->post('customer-payment/update/(:num)', 'CustomerPaymentController::update/$1');
$routes->get('customer-payment/delete/(:num)', 'CustomerPaymentController::delete/$1');
$routes->get('customer-payment/export', 'CustomerPaymentController::export');

// Vendor Payment Routes
$routes->get('vendor-payment', 'VendorPaymentController::index');
$routes->get('vendor-payment/create', 'VendorPaymentController::create');
$routes->post('vendor-payment/store', 'VendorPaymentController::store');
$routes->get('vendor-payment/show/(:num)', 'VendorPaymentController::show/$1');
$routes->get('vendor-payment/edit/(:num)', 'VendorPaymentController::edit/$1');
$routes->post('vendor-payment/update/(:num)', 'VendorPaymentController::update/$1');
$routes->get('vendor-payment/delete/(:num)', 'VendorPaymentController::delete/$1');
$routes->get('vendor-payment/export', 'VendorPaymentController::export');

// Maintenance Routes
$routes->get('maintenance', 'MaintenanceController::index');
$routes->get('maintenance/create', 'MaintenanceController::create');
$routes->post('maintenance/store', 'MaintenanceController::store');
$routes->get('maintenance/show/(:num)', 'MaintenanceController::show/$1');
$routes->get('maintenance/edit/(:num)', 'MaintenanceController::edit/$1');
$routes->post('maintenance/update/(:num)', 'MaintenanceController::update/$1');
$routes->get('maintenance/delete/(:num)', 'MaintenanceController::delete/$1');
$routes->get('maintenance/export', 'MaintenanceController::export');
$routes->get('customer-payment/print/(:num)', 'CustomerPaymentController::print/$1');
$routes->get('customer-payment/get-invoices', 'CustomerPaymentController::getInvoices');
$routes->get('customer-payment/get-customers', 'CustomerPaymentController::getCustomers');

// Quotation Routes
$routes->get('quotation', 'QuotationController::index');
$routes->get('quotation/create', 'QuotationController::create');
$routes->post('quotation/store', 'QuotationController::store');
$routes->get('quotation/show/(:num)', 'QuotationController::show/$1');
$routes->get('quotation/edit/(:num)', 'QuotationController::edit/$1');
$routes->post('quotation/update/(:num)', 'QuotationController::update/$1');
$routes->get('quotation/delete/(:num)', 'QuotationController::delete/$1');
$routes->post('quotation/update-status/(:num)', 'QuotationController::updateStatus/$1');
$routes->get('quotation/export', 'QuotationController::export');
$routes->get('quotation/print/(:num)', 'QuotationController::print/$1');
$routes->get('quotation/get-products', 'QuotationController::getProducts');
$routes->get('quotation/get-customers', 'QuotationController::getCustomers');
$routes->get('quotation/convert-to-order/(:num)', 'QuotationController::convertToOrder/$1');

// Finance Routes (Accounting)
$routes->get('finance', 'InvoiceController::index');
$routes->get('finance/create', 'InvoiceController::create');
$routes->post('finance/store', 'InvoiceController::store');
$routes->get('finance/show/(:num)', 'InvoiceController::show/$1');
$routes->get('finance/edit/(:num)', 'InvoiceController::edit/$1');
$routes->post('finance/update/(:num)', 'InvoiceController::update/$1');
$routes->get('finance/delete/(:num)', 'InvoiceController::delete/$1');
$routes->post('finance/update-status/(:num)', 'InvoiceController::updateStatus/$1');
$routes->post('finance/record-payment/(:num)', 'InvoiceController::recordPayment/$1');
$routes->get('finance/export', 'InvoiceController::export');
$routes->get('finance/print/(:num)', 'InvoiceController::printInvoice/$1');

// HR Management Routes
$routes->get('hr', 'HRController::index');
$routes->get('hr/employees', 'HRController::employees');
$routes->get('hr/departments', 'HRController::departments');
$routes->get('hr/employee/create', 'HRController::createEmployee');
$routes->post('hr/employee/store', 'HRController::storeEmployee');
$routes->get('hr/employee/edit/(:num)', 'HRController::editEmployee/$1');
$routes->post('hr/employee/update/(:num)', 'HRController::updateEmployee/$1');
$routes->get('hr/employee/delete/(:num)', 'HRController::deleteEmployee/$1');
$routes->get('hr/department/create', 'HRController::createDepartment');
$routes->post('hr/department/store', 'HRController::storeDepartment');
$routes->get('hr/department/edit/(:num)', 'HRController::editDepartment/$1');
$routes->post('hr/department/update/(:num)', 'HRController::updateDepartment/$1');
$routes->get('hr/department/delete/(:num)', 'HRController::deleteDepartment/$1');
$routes->get('hr/analytics', 'HRController::analytics');
$routes->get('hr/reports', 'HRController::reports');
$routes->get('hr/export/employees', 'HRController::exportEmployees');
$routes->get('hr/export/departments', 'HRController::exportDepartments');
$routes->get('hr/employee/(:num)', 'HRController::getEmployeeDetails/$1');
$routes->get('hr/department/(:num)/employees', 'HRController::getDepartmentEmployees/$1');
$routes->post('hr/employee/(:num)/status', 'HRController::updateEmployeeStatus');

// HR Additional Routes
$routes->get('hr/attendance', 'HRController::attendance');
$routes->get('hr/leave', 'HRController::leaveManagement');
$routes->get('hr/salary', 'HRController::salaryManagement');
$routes->get('hr/payroll', 'HRController::payroll');
$routes->get('hr/documents', 'HRController::documents');
$routes->get('hr/training', 'HRController::training');

// HR Management Routes (Legacy)
$routes->get('hrm', 'HRController::index');
$routes->get('hrm/employees', 'HRController::employees');
$routes->get('hrm/departments', 'HRController::departments');

// Direct HR Module Routes
$routes->get('attendance', 'HRController::attendance');
$routes->get('leave-management', 'HRController::leaveManagement');
$routes->get('salary-management', 'HRController::salaryManagement');
$routes->get('payroll', 'HRController::payroll');
$routes->get('documents', 'HRController::documents');
$routes->get('training', 'HRController::training');

// Reception Routes
$routes->get('reception', 'SimpleModuleController::index');
$routes->get('reception/create', 'SimpleModuleController::create');
$routes->post('reception/store', 'SimpleModuleController::store');
$routes->get('reception/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('reception/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('reception/delete/(:num)', 'SimpleModuleController::delete/$1');

// Accounting Routes
$routes->get('accounting', 'AccountingController::index');
$routes->get('accounting/invoices', 'AccountingController::invoices');
$routes->get('accounting/bills', 'AccountingController::bills');
$routes->get('accounting/receivables', 'AccountingController::receivables');
$routes->get('accounting/payables', 'AccountingController::payables');
$routes->get('accounting/reports', 'AccountingController::reports');
$routes->get('accounting/analytics', 'AccountingController::analytics');

// Direct Accounting Routes
$routes->get('accounts', 'AccountingController::index');
$routes->get('accounts/invoices', 'AccountingController::invoices');
$routes->get('accounts/bills', 'AccountingController::bills');
$routes->get('accounts/receivables', 'AccountingController::receivables');
$routes->get('accounts/payables', 'AccountingController::payables');
$routes->get('accounts/reports', 'AccountingController::reports');
$routes->get('accounting/journal', 'AccountingController::journal');
$routes->get('accounting/ledger', 'AccountingController::ledger');
$routes->get('accounting/export/report', 'AccountingController::exportReport');

// Accounting: Chart of Accounts
$routes->get('accounting/coa', 'AccountingController::coa');
$routes->get('accounting/coa/create', 'AccountingController::coaCreate');
$routes->post('accounting/coa/store', 'AccountingController::coaStore');
$routes->post('accounting/coa/toggle-status/(:num)', 'AccountingController::coaToggleStatus/$1');

// Accounting: Cost Centers
$routes->get('accounting/cost-centers', 'AccountingController::costCenters');
$routes->get('accounting/cost-centers/create', 'AccountingController::costCenterCreate');
$routes->post('accounting/cost-centers/store', 'AccountingController::costCenterStore');
$routes->post('accounting/cost-centers/toggle-status/(:num)', 'AccountingController::costCenterToggleStatus/$1');

// Accounting: Journal Entries
$routes->get('accounting/journal/create', 'AccountingController::journalCreate');
$routes->post('accounting/journal/store', 'AccountingController::journalStore');

// Accounting: AR/AP aliases
$routes->get('accounting/ar', 'AccountingController::receivables');
$routes->get('accounting/ap', 'AccountingController::payables');

// Accounting: Bank & Reconciliation
$routes->get('accounting/bank-accounts', 'AccountingController::bankAccounts');
$routes->get('accounting/bank-reconciliation', 'AccountingController::bankReconciliations');
$routes->post('accounting/bank-reconciliation/store', 'AccountingController::bankReconciliationStore');

// Accounting: Expenses
$routes->get('accounting/expenses', 'AccountingController::expenses');
$routes->get('accounting/expenses/create', 'AccountingController::expenseCreate');
$routes->post('accounting/expenses/store', 'AccountingController::expenseStore');

// Accounting: Taxes
$routes->get('accounting/taxes', 'AccountingController::taxes');
$routes->get('accounting/taxes/create', 'AccountingController::taxCreate');
$routes->post('accounting/taxes/store', 'AccountingController::taxStore');

// Reports Routes
$routes->get('reports', 'SimpleModuleController::index');
$routes->get('reports/create', 'SimpleModuleController::create');
$routes->post('reports/store', 'SimpleModuleController::store');
$routes->get('reports/edit/(:num)', 'SimpleModuleController::edit/$1');
$routes->post('reports/update/(:num)', 'SimpleModuleController::update/$1');
$routes->get('reports/delete/(:num)', 'SimpleModuleController::delete/$1');

// Specific Report Routes
$routes->get('sales-report', 'SimpleModuleController::index');
$routes->get('purchase-report', 'SimpleModuleController::index');
$routes->get('all-features', 'SimpleModuleController::index');

// Help & Support Routes
$routes->get('help', 'HelpController::index');
$routes->get('help/support', 'HelpController::support');
$routes->get('help/documentation', 'HelpController::documentation');
$routes->get('help/faq', 'HelpController::faq');
$routes->get('help/contact', 'HelpController::contact');

// GST Management Routes
$routes->get('gst', 'GSTController::index');
$routes->post('gst/update-rate', 'GSTController::updateGSTRate');
$routes->post('gst/update-hsn', 'GSTController::updateHSNCode');
$routes->post('gst/bulk-update', 'GSTController::bulkUpdateGST');
$routes->get('gst/report', 'GSTController::getGSTReport');
$routes->get('gst/export', 'GSTController::exportGSTReport');

// Short URLs (sidebar & bookmarks)
$routes->get('support', 'HelpController::support');

// Company profile & branding
$routes->get('company-profile', 'CompanyProfileController::index');
$routes->post('company-profile/update', 'CompanyProfileController::update');
$routes->get('company-profile/get-profile', 'CompanyProfileController::getProfile');
$routes->post('company-profile/delete-logo', 'CompanyProfileController::deleteLogo');
$routes->get('logo-settings', 'CompanyProfileController::index');

// Manufacturing (standalone manufacturing orders)
$routes->get('manufacturing', 'ManufacturingController::index');
$routes->get('manufacturing/create', 'ManufacturingController::create');
$routes->post('manufacturing/store', 'ManufacturingController::store');
$routes->get('manufacturing/start/(:num)', 'ManufacturingController::startProduction/$1');
$routes->get('manufacturing/complete/(:num)', 'ManufacturingController::completeProduction/$1');
$routes->get('manufacturing/show/(:num)', 'ManufacturingController::show/$1');

// Admin settings (Settings controller — matches views under settings/*)
$routes->get('module-assignments', 'Settings::moduleAssignments');
$routes->get('department-assignment', 'Settings::moduleAssignments');
$routes->get('role-permissions', 'Settings::moduleAssignments');
$routes->post('settings/assign-modules-to-department', 'Settings::assignModulesToDepartment');
$routes->post('settings/assign-modules-to-employee', 'Settings::assignModulesToEmployee');
$routes->post('settings/remove-module-assignment', 'Settings::removeModuleAssignment');
$routes->get('settings/get-module-assignments', 'Settings::getModuleAssignments');
$routes->get('settings/company', 'Settings::companySettings');
$routes->post('settings/company/update', 'Settings::updateCompanySettings');
$routes->get('settings/departments', 'Settings::departments');
$routes->post('settings/departments/create', 'Settings::createDepartment');
$routes->post('settings/departments/update/(:num)', 'Settings::updateDepartment/$1');
$routes->get('settings/departments/delete/(:num)', 'Settings::deleteDepartment/$1');
$routes->get('system-settings', 'Settings::systemSettings');
$routes->get('user-management', 'Settings::userManagement');
$routes->post('settings/users/create', 'Settings::createUser');
$routes->post('settings/users/update/(:num)', 'Settings::updateUser/$1');
$routes->get('settings/users/delete/(:num)', 'Settings::deleteUser/$1');
