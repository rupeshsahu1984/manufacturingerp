<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Orders Export</title>
    <script>
        // Auto-print when page loads (for browser print-to-PDF)
        window.onload = function() {
            // Only auto-print if accessed via export route
            if (window.location.search.includes('export=pdf')) {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        };
    </script>
    <style>
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }
        .summary {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .summary p {
            margin: 3px 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #ff6b35;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        .status-draft { background-color: #6c757d; color: white; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-approved { background-color: #17a2b8; color: white; }
        .status-ordered { background-color: #007bff; color: white; }
        .status-received { background-color: #28a745; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Purchase Orders Report</h1>
        <p>Generated on: <?= esc($export_date) ?></p>
        <p class="no-print" style="color: #666; font-size: 9px;">
            <strong>Note:</strong> Use your browser's Print function (Ctrl+P) and select "Save as PDF" to download as PDF.
        </p>
    </div>

    <div class="summary">
        <p><strong>Total Orders:</strong> <?= $total_count ?></p>
        <p><strong>Total Value:</strong> ₹<?= number_format($total_amount, 2) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Date</th>
                <th>Status</th>
                <th class="text-right">Items</th>
                <th class="text-right">Total Amount</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($purchase_orders)): ?>
                <tr>
                    <td colspan="8" class="text-center">No purchase orders found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($purchase_orders as $po): ?>
                    <tr>
                        <td><?= esc($po['po_number'] ?? '') ?></td>
                        <td><?= esc($po['supplier_name'] ?? '') ?></td>
                        <td><?= !empty($po['order_date']) ? date('d/m/Y', strtotime($po['order_date'])) : '' ?></td>
                        <td><?= !empty($po['expected_date']) ? date('d/m/Y', strtotime($po['expected_date'])) : '' ?></td>
                        <td>
                            <span class="status status-<?= esc($po['status'] ?? 'draft') ?>">
                                <?= strtoupper(esc($po['status'] ?? 'DRAFT')) ?>
                            </span>
                        </td>
                        <td class="text-right"><?= $po['item_count'] ?? 0 ?></td>
                        <td class="text-right">₹<?= number_format($po['total_amount'] ?? 0, 2) ?></td>
                        <td><?= !empty($po['created_at']) ? date('d/m/Y H:i', strtotime($po['created_at'])) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>

