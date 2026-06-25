<?php

namespace App\Controllers;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\SupplierInvoice;
use App\Models\DebitNote;
use App\Models\Supplier;
use App\Models\Product;

class PurchaseReportController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Purchase Reports & Analytics',
            'summary_stats' => [],
            'monthly_trends' => [
                'purchase_orders' => [],
                'goods_receipts' => [],
                'invoices' => [],
            ],
            'top_suppliers' => ['by_volume' => [], 'by_value' => [], 'by_quality' => []],
            'top_products' => ['by_quantity' => [], 'by_value' => [], 'by_frequency' => []],
        ];
        try {
            $data['summary_stats'] = $this->getSummaryStats();
            $data['monthly_trends'] = $this->getMonthlyTrends();
            $data['top_suppliers'] = $this->getTopSuppliers();
            $data['top_products'] = $this->getTopProducts();
        } catch (\Throwable $e) {
            log_message('error', 'PurchaseReportController::index: ' . $e->getMessage());
            $data['report_error'] = 'Some purchase analytics queries failed (missing tables or columns).';
        }

        return view('purchase_report/index', $data);
    }

    public function pendingOrders()
    {
        $purchaseOrder = new PurchaseOrder();
        $purchaseRequisition = new PurchaseRequisition();

        return $this->renderReportPage('Pending Purchase Orders', [
            'pending_orders' => $this->safeData(static fn () => $purchaseOrder->getPendingOrders()),
            'pending_requisitions' => $this->safeData(static fn () => $purchaseRequisition->where('status', 'pending')->findAll()),
            'overdue_orders' => $this->safeData(static fn () => $purchaseOrder->getOverdueOrders()),
        ]);
    }

    public function supplierHistory()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        $supplier = new Supplier();
        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        $supplierInvoice = new SupplierInvoice();
        
        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        return $this->renderReportPage('Supplier History & Performance', [
            'suppliers' => $this->safeData(static fn () => $supplier->getAllActive()),
            'purchase_orders' => $supplierId ? $this->safeData(static fn () => $purchaseOrder->getBySupplier($supplierId, $filters)) : [],
            'goods_receipts' => $supplierId ? $this->safeData(static fn () => $goodsReceipt->getBySupplier($supplierId, $filters)) : [],
            'invoices' => $supplierId ? $this->safeData(static fn () => $supplierInvoice->getBySupplier($supplierId, $filters)) : [],
        ]);
    }

    public function priceTrends()
    {
        $productId = $this->request->getGet('product_id');
        $supplierId = $this->request->getGet('supplier_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        $product = new Product();
        $supplier = new Supplier();
        $purchaseOrder = new PurchaseOrder();
        
        $filters = [
            'product_id' => $productId,
            'supplier_id' => $supplierId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        return $this->renderReportPage('Price Trends Analysis', [
            'products' => $this->safeData(static fn () => $product->getAllActive()),
            'suppliers' => $this->safeData(static fn () => $supplier->getAllActive()),
            'price_trends' => $this->safeData(fn () => $this->getPriceTrends($filters)),
            'price_comparison' => $this->safeData(fn () => $this->getPriceComparison($filters)),
        ]);
    }

    public function qualityMetrics()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        $supplier = new Supplier();
        $goodsReceipt = new GoodsReceipt();
        
        $filters = [
            'supplier_id' => $supplierId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        return $this->renderReportPage('Quality Metrics & Analysis', [
            'suppliers' => $this->safeData(static fn () => $supplier->getAllActive()),
            'quality_metrics' => $this->safeData(fn () => $this->getQualityMetrics($filters)),
            'rejection_analysis' => $this->safeData(fn () => $this->getRejectionAnalysis($filters)),
            'supplier_ratings' => $this->safeData(fn () => $this->getSupplierRatings($filters)),
        ]);
    }

    public function costAnalysis()
    {
        $categoryId = $this->request->getGet('category_id');
        $supplierId = $this->request->getGet('supplier_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        $product = new Product();
        $supplier = new Supplier();
        $purchaseOrder = new PurchaseOrder();
        
        $filters = [
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        return $this->renderReportPage('Cost Analysis & Budget Tracking', [
            'categories' => $this->safeData(static fn () => $product->getCategories()),
            'suppliers' => $this->safeData(static fn () => $supplier->getAllActive()),
            'cost_breakdown' => $this->safeData(fn () => $this->getCostBreakdown($filters)),
            'budget_variance' => $this->safeData(fn () => $this->getBudgetVariance($filters)),
            'cost_trends' => $this->safeData(fn () => $this->getCostTrends($filters)),
        ]);
    }

    public function exportReport()
    {
        $reportType = $this->request->getGet('type');
        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'category_id' => $this->request->getGet('category_id')
        ];
        
        switch ($reportType) {
            case 'purchase_orders':
                $data = $this->exportPurchaseOrders($filters);
                break;
            case 'supplier_performance':
                $data = $this->exportSupplierPerformance($filters);
                break;
            case 'price_trends':
                $data = $this->exportPriceTrends($filters);
                break;
            case 'quality_metrics':
                $data = $this->exportQualityMetrics($filters);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }
        
        return $this->downloadExcel($data, $reportType);
    }

    // Private helper methods
    private function getSummaryStats()
    {
        $purchaseRequisition = new PurchaseRequisition();
        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        $supplierInvoice = new SupplierInvoice();
        
        return [
            'total_requisitions' => $purchaseRequisition->countAllResults(),
            'pending_requisitions' => $purchaseRequisition->where('status', 'pending')->countAllResults(),
            'total_orders' => $purchaseOrder->countAllResults(),
            'pending_orders' => $purchaseOrder->where('status', 'pending')->countAllResults(),
            'total_receipts' => $goodsReceipt->countAllResults(),
            'pending_receipts' => $goodsReceipt->where('status', 'received')->countAllResults(),
            'total_invoices' => $supplierInvoice->countAllResults(),
            'pending_invoices' => $supplierInvoice->where('status', 'pending')->countAllResults(),
            'overdue_invoices' => $supplierInvoice->getOverdueCount()
        ];
    }

    private function getMonthlyTrends()
    {
        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        $supplierInvoice = new SupplierInvoice();
        
        $year = date('Y');
        
        return [
            'purchase_orders' => $purchaseOrder->getMonthlyStats($year),
            'goods_receipts' => $goodsReceipt->getMonthlyStats($year),
            'invoices' => $supplierInvoice->getMonthlyStats($year)
        ];
    }

    private function getTopSuppliers()
    {
        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        
        return [
            'by_volume' => $purchaseOrder->getTopSuppliersByVolume(),
            'by_value' => $purchaseOrder->getTopSuppliersByValue(),
            'by_quality' => $goodsReceipt->getSupplierPerformance()
        ];
    }

    private function getTopProducts()
    {
        $purchaseOrder = new PurchaseOrder();
        $goodsReceipt = new GoodsReceipt();
        
        return [
            'by_quantity' => $purchaseOrder->getTopProductsByQuantity(),
            'by_value' => $purchaseOrder->getTopProductsByValue(),
            'by_frequency' => $purchaseOrder->getTopProductsByFrequency()
        ];
    }

    private function getSupplierPerformance($supplierId, $filters)
    {
        $goodsReceipt = new GoodsReceipt();
        $purchaseOrder = new PurchaseOrder();
        $supplierInvoice = new SupplierInvoice();
        
        return [
            'quality_metrics' => $goodsReceipt->getSupplierQualityMetrics($supplierId, $filters),
            'delivery_performance' => $purchaseOrder->getSupplierDeliveryPerformance($supplierId, $filters),
            'payment_history' => $supplierInvoice->getSupplierPaymentHistory($supplierId, $filters),
            'overall_rating' => $this->calculateSupplierRating($supplierId, $filters)
        ];
    }

    private function getPriceTrends($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getPriceTrends($filters);
    }

    private function getPriceComparison($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getPriceComparison($filters);
    }

    private function getQualityMetrics($filters)
    {
        $goodsReceipt = new GoodsReceipt();
        return $goodsReceipt->getQualityMetrics($filters);
    }

    private function getRejectionAnalysis($filters)
    {
        $goodsReceipt = new GoodsReceipt();
        return $goodsReceipt->getRejectionAnalysis($filters);
    }

    private function getSupplierRatings($filters)
    {
        $goodsReceipt = new GoodsReceipt();
        $purchaseOrder = new PurchaseOrder();
        $supplierInvoice = new SupplierInvoice();
        
        $suppliers = $supplierInvoice->getAllSuppliers();
        $ratings = [];
        
        foreach ($suppliers as $supplier) {
            $ratings[] = [
                'supplier' => $supplier,
                'quality_score' => $goodsReceipt->getSupplierQualityScore($supplier['id'], $filters),
                'delivery_score' => $purchaseOrder->getSupplierDeliveryScore($supplier['id'], $filters),
                'price_score' => $purchaseOrder->getSupplierPriceScore($supplier['id'], $filters),
                'overall_score' => $this->calculateSupplierRating($supplier['id'], $filters)
            ];
        }
        
        // Sort by overall score
        usort($ratings, function($a, $b) {
            return $b['overall_score'] <=> $a['overall_score'];
        });
        
        return $ratings;
    }

    private function getCostBreakdown($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getCostBreakdown($filters);
    }

    private function getBudgetVariance($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getBudgetVariance($filters);
    }

    private function getCostTrends($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getCostTrends($filters);
    }

    private function calculateSupplierRating($supplierId, $filters)
    {
        $goodsReceipt = new GoodsReceipt();
        $purchaseOrder = new PurchaseOrder();
        
        $qualityScore = $goodsReceipt->getSupplierQualityScore($supplierId, $filters);
        $deliveryScore = $purchaseOrder->getSupplierDeliveryScore($supplierId, $filters);
        $priceScore = $purchaseOrder->getSupplierPriceScore($supplierId, $filters);
        
        // Weighted average: Quality 40%, Delivery 35%, Price 25%
        $overallScore = ($qualityScore * 0.4) + ($deliveryScore * 0.35) + ($priceScore * 0.25);
        
        return round($overallScore, 2);
    }

    private function exportPurchaseOrders($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getAllWithRelations($filters);
    }

    private function exportSupplierPerformance($filters)
    {
        $goodsReceipt = new GoodsReceipt();
        return $goodsReceipt->getSupplierPerformance($filters);
    }

    private function exportPriceTrends($filters)
    {
        $purchaseOrder = new PurchaseOrder();
        return $purchaseOrder->getPriceTrends($filters);
    }

    private function exportQualityMetrics($filters)
    {
        $goodsReceipt = new GoodsReceipt();
        return $goodsReceipt->getQualityMetrics($filters);
    }

    private function downloadExcel($data, $reportType)
    {
        // Implementation for Excel export
        // This would use a library like PhpSpreadsheet to generate Excel files
        
        $filename = $reportType . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // For now, return JSON response
        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'filename' => $filename
        ]);
    }

    public function getDashboardData()
    {
        $data = [
            'summary_stats' => $this->getSummaryStats(),
            'monthly_trends' => $this->getMonthlyTrends(),
            'top_suppliers' => $this->getTopSuppliers(),
            'top_products' => $this->getTopProducts()
        ];
        
        return $this->response->setJSON($data);
    }

    public function getSupplierComparison()
    {
        $supplierIds = $this->request->getGet('supplier_ids');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        if (!$supplierIds) {
            return $this->response->setJSON(['error' => 'Supplier IDs required']);
        }
        
        $supplierIds = explode(',', $supplierIds);
        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        $comparison = [];
        foreach ($supplierIds as $supplierId) {
            $comparison[] = [
                'supplier_id' => $supplierId,
                'performance' => $this->getSupplierPerformance($supplierId, $filters)
            ];
        }
        
        return $this->response->setJSON($comparison);
    }

    private function renderReportPage(string $title, array $sections)
    {
        $summary = [];
        foreach ($sections as $label => $rows) {
            $summary[ucwords(str_replace('_', ' ', $label))] = is_array($rows) ? count($rows) : 0;
        }

        return view('shared/module_page', [
            'title' => $title,
            'page_title' => $title,
            'message' => 'Purchase report page is available.',
            'summary' => $summary,
        ]);
    }

    private function safeData(callable $loader): array
    {
        try {
            return $loader() ?: [];
        } catch (\Throwable $e) {
            log_message('error', 'PurchaseReportController report query: ' . $e->getMessage());
            return [];
        }
    }
}
