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
        
        $data = [
            'title' => 'Pending Purchase Orders',
            'pending_orders' => $purchaseOrder->getPendingOrders(),
            'pending_requisitions' => $purchaseRequisition->getPendingRequisitions(),
            'overdue_orders' => $purchaseOrder->getOverdueOrders()
        ];
        
        return view('purchase_report/pending_orders', $data);
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
        
        $data = [
            'title' => 'Supplier History & Performance',
            'suppliers' => $supplier->getAllActive(),
            'selected_supplier' => $supplierId ? $supplier->find($supplierId) : null,
            'purchase_orders' => $supplierId ? $purchaseOrder->getBySupplier($supplierId, $filters) : [],
            'goods_receipts' => $supplierId ? $goodsReceipt->getBySupplier($supplierId, $filters) : [],
            'invoices' => $supplierId ? $supplierInvoice->getBySupplier($supplierId, $filters) : [],
            'performance_metrics' => $supplierId ? $this->getSupplierPerformance($supplierId, $filters) : [],
            'filters' => $filters
        ];
        
        return view('purchase_report/supplier_history', $data);
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
        
        $data = [
            'title' => 'Price Trends Analysis',
            'products' => $product->getAllActive(),
            'suppliers' => $supplier->getAllActive(),
            'selected_product' => $productId ? $product->find($productId) : null,
            'selected_supplier' => $supplierId ? $supplier->find($supplierId) : null,
            'price_trends' => $this->getPriceTrends($filters),
            'price_comparison' => $this->getPriceComparison($filters),
            'filters' => $filters
        ];
        
        return view('purchase_report/price_trends', $data);
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
        
        $data = [
            'title' => 'Quality Metrics & Analysis',
            'suppliers' => $supplier->getAllActive(),
            'selected_supplier' => $supplierId ? $supplier->find($supplierId) : null,
            'quality_metrics' => $this->getQualityMetrics($filters),
            'rejection_analysis' => $this->getRejectionAnalysis($filters),
            'supplier_ratings' => $this->getSupplierRatings($filters),
            'filters' => $filters
        ];
        
        return view('purchase_report/quality_metrics', $data);
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
        
        $data = [
            'title' => 'Cost Analysis & Budget Tracking',
            'categories' => $product->getCategories(),
            'suppliers' => $supplier->getAllActive(),
            'selected_category' => $categoryId,
            'selected_supplier' => $supplierId,
            'cost_breakdown' => $this->getCostBreakdown($filters),
            'budget_variance' => $this->getBudgetVariance($filters),
            'cost_trends' => $this->getCostTrends($filters),
            'filters' => $filters
        ];
        
        return view('purchase_report/cost_analysis', $data);
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
}
