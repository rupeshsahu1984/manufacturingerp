<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= esc($invoice['invoice_number']) ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 14px;
            color: #666;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .details-box {
            flex: 1;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        
        .total-row {
            margin-bottom: 5px;
        }
        
        .total-label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }
        
        .total-value {
            display: inline-block;
            width: 120px;
            text-align: right;
        }
        
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Invoice</button>
    
    <div class="header">
        <div class="company-name">PRODX MANUFACTURING ERP</div>
        <div class="company-info">
            123 Industrial Park, Manufacturing Zone<br>
            Phone: +91-1234567890 | Email: info@prodx.com<br>
            GSTIN: 12ABCDE1234F1Z5
        </div>
    </div>
    
    <div class="document-title">TAX INVOICE</div>
    
    <div class="info-section">
        <div class="details-box">
            <div class="section-title">Invoice Details</div>
            <div class="info-row"><span class="info-label">Invoice No:</span> <?= esc($invoice['invoice_number']) ?></div>
            <div class="info-row"><span class="info-label">Date:</span> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></div>
            <div class="info-row"><span class="info-label">Due Date:</span> <?= $invoice['due_date'] ? date('d M Y', strtotime($invoice['due_date'])) : 'N/A' ?></div>
            <div class="info-row"><span class="info-label">Status:</span> <?= ucfirst($invoice['status']) ?></div>
        </div>
        
        <div class="details-box" style="margin-left: 50px;">
            <div class="section-title">Bill To</div>
            <div class="info-row"><strong><?= esc($invoice['customer_name']) ?></strong></div>
            <div class="info-row">Phone: <?= esc($invoice['phone'] ?: 'N/A') ?></div>
            <div class="info-row">Email: <?= esc($invoice['email'] ?: 'N/A') ?></div>
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Product Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="10%" class="text-right">GST %</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice['items'] as $index => $item): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td>
                    <strong><?= esc($item['product_name']) ?></strong><br>
                    <small><?= esc($item['product_code']) ?></small>
                </td>
                <td class="text-center"><?= number_format($item['quantity'], 2) ?></td>
                <td class="text-right">₹<?= number_format($item['unit_price'], 2) ?></td>
                <td class="text-right"><?= number_format($item['gst_rate'], 2) ?>%</td>
                <td class="text-right">₹<?= number_format($item['total_amount'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">₹<?= number_format($invoice['subtotal'], 2) ?></span>
        </div>
        <div class="total-row">
            <span class="total-label">GST Amount:</span>
            <span class="total-value">₹<?= number_format($invoice['gst_amount'], 2) ?></span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">Grand Total:</span>
            <span class="total-value">₹<?= number_format($invoice['total_amount'], 2) ?></span>
        </div>
        <div class="total-row">
            <span class="total-label">Amount Paid:</span>
            <span class="total-value">₹<?= number_format($invoice['paid_amount'], 2) ?></span>
        </div>
        <div class="total-row">
            <span class="total-label">Balance Due:</span>
            <span class="total-value"><strong>₹<?= number_format($invoice['total_amount'] - $invoice['paid_amount'], 2) ?></strong></span>
        </div>
    </div>
    
    <div class="footer">
        <p>This is a computer generated invoice. No signature required.</p>
        <p>Thank you for your business!</p>
        <p>Generated on: <?= date('d M Y H:i:s') ?></p>
    </div>
</body>
</html>
