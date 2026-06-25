<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class GSTController extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $data = [
            'title' => 'GST Management - PRODX',
            'products' => $this->productModel->getProductsWithGST(),
            'gst_summary' => $this->productModel->getGSTSummary(),
            'hsn_codes' => $this->productModel->getUniqueHSNCodes()
        ];

        return view('gst/index', $data);
    }

    public function updateGSTRate()
    {
        $productId = $this->request->getPost('product_id');
        $gstRate = $this->request->getPost('gst_rate');

        if (!$productId || !is_numeric($gstRate)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data provided']);
        }

        if ($this->productModel->update($productId, ['gst_rate' => $gstRate])) {
            return $this->response->setJSON(['success' => true, 'message' => 'GST rate updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update GST rate']);
        }
    }

    public function updateHSNCode()
    {
        $productId = $this->request->getPost('product_id');
        $hsnCode = $this->request->getPost('hsn_code');

        if (!$productId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product ID is required']);
        }

        if ($this->productModel->update($productId, ['hsn_code' => $hsnCode])) {
            return $this->response->setJSON(['success' => true, 'message' => 'HSN code updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update HSN code']);
        }
    }

    public function bulkUpdateGST()
    {
        $categoryId = $this->request->getPost('category_id');
        $gstRate = $this->request->getPost('gst_rate');

        if (!$categoryId || !is_numeric($gstRate)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data provided']);
        }

        $products = $this->productModel->where('category_id', $categoryId)->findAll();
        $updated = 0;

        foreach ($products as $product) {
            if ($this->productModel->update($product['id'], ['gst_rate' => $gstRate])) {
                $updated++;
            }
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => "Updated GST rate for $updated products"
        ]);
    }

    public function getGSTReport()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $gstRate = $this->request->getGet('gst_rate');

        $data = [
            'title' => 'GST Report - PRODX',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'gst_rate' => $gstRate,
            'report_data' => $this->getGSTReportData($startDate, $endDate, $gstRate)
        ];

        return view('gst/report', $data);
    }

    private function getGSTReportData($startDate, $endDate, $gstRate)
    {
        // This would typically query sales/invoice data
        // For now, return product GST summary
        return [
            'gst_summary' => $this->productModel->getGSTSummary(),
            'products_by_gst' => $this->productModel->select('gst_rate, COUNT(*) as count, SUM(selling_price) as total_value')
                ->where('gst_rate IS NOT NULL')
                ->where('status', 'active')
                ->groupBy('gst_rate')
                ->findAll()
        ];
    }

    public function exportGSTReport()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $gstRate = $this->request->getGet('gst_rate');

        $reportData = $this->getGSTReportData($startDate, $endDate, $gstRate);
        
        $filename = 'gst_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Product Code', 'Product Name', 'HSN Code', 'GST Rate (%)', 'Selling Price', 'Category'
        ]);
        
        $products = $this->productModel->getProductsWithGST();
        foreach ($products as $product) {
            fputcsv($output, [
                $product['product_code'],
                $product['product_name'],
                isset($product['hsn_code']) ? $product['hsn_code'] : '',
                isset($product['gst_rate']) ? $product['gst_rate'] : 18.00,
                isset($product['selling_price']) ? $product['selling_price'] : 0,
                isset($product['category_name']) ? $product['category_name'] : ''
            ]);
        }
        
        fclose($output);
        exit;
    }
}
