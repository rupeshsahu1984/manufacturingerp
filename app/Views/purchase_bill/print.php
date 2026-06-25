<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Bill - <?= esc($bill['bill_number']) ?></title>
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
        
        .bill-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .bill-details, .supplier-details {
            flex: 1;
        }
        
        .bill-details {
            margin-right: 50px;
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
            width: 140px;
        }
        
        .info-value {
            display: inline-block;
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
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
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
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background: #6c757d; color: white; }
        .status-received { background: #17a2b8; color: white; }
        .status-paid { background: #28a745; color: white; }
        .status-overdue { background: #dc3545; color: white; }
        .status-cancelled { background: #343a40; color: white; }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
    
    <!-- Header -->
    <div class="header">
        <div class="company-name">PRODX MANUFACTURING ERP</div>
        <div class="company-info">
            123 Industrial Park, Manufacturing Zone<br>
            Phone: +91-1234567890 | Email: info@prodx.com<br>
            GSTIN: 12ABCDE1234F1Z5
        </div>
    </div>
    
    <!-- Document Title -->
    <div class="document-title">PURCHASE BILL</div>
    
    <!-- Bill and Supplier Information -->
    <div class="bill-info">
        <div class="bill-details">
            <div class="section-title">Bill Information</div>
            <div class="info-row">
                <span class="info-label">Bill Number:</span>
                <span class="info-value"><?= esc($bill['bill_number']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Bill Date:</span>
                <span class="info-value"><?= date('d M Y', strtotime($bill['bill_date'])) ?></span>
            </div>
            <?php if (!empty($bill['due_date'])): ?>
            <div class="info-row">
                <span class="info-label">Due Date:</span>
                <span class="info-value"><?= date('d M Y', strtotime($bill['due_date'])) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($bill['invoice_number'])): ?>
            <div class="info-row">
                <span class="info-label">Supplier Memo:</span>
                <span class="info-value"><?= esc($bill['invoice_number']) ?></span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge status-<?= $bill['status'] ?>">
                        <?= ucfirst($bill['status']) ?>
                    </span>
                </span>
            </div>
        </div>
        
        <div class="supplier-details">
            <div class="section-title">Supplier Information</div>
            <div class="info-row">
                <span class="info-label">Supplier Name:</span>
                <span class="info-value"><?= esc($bill['supplier_name'] ?? 'N/A') ?></span>
            </div>
            <?php if (!empty($bill['supplier_code'])): ?>
            <div class="info-row">
                <span class="info-label">Supplier Code:</span>
                <span class="info-value"><?= esc($bill['supplier_code']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($bill['contact_person'])): ?>
            <div class="info-row">
                <span class="info-label">Contact Person:</span>
                <span class="info-value"><?= esc($bill['contact_person']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($bill['phone'])): ?>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?= esc($bill['phone']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($bill['email'])): ?>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= esc($bill['email']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($bill['address'])): ?>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value"><?= nl2br(esc($bill['address'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bill Items -->
    <div class="section-title">Bill Items</div>
    <?php if (empty($bill['items'])): ?>
        <p>No items found in this purchase bill.</p>
    <?php else: ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="25%">Product</th>
                    <th width="10%" class="text-center">Quantity</th>
                    <th width="10%" class="text-right">Unit Price</th>
                    <th width="8%" class="text-center">CGST %</th>
                    <th width="8%" class="text-center">SGST %</th>
                    <th width="8%" class="text-center">IGST %</th>
                    <th width="12%" class="text-right">GST Amount</th>
                    <th width="14%" class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bill['items'] as $index => $item): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td>
                            <strong><?= esc($item['product_name'] ?? 'N/A') ?></strong><br>
                            <small><?= esc($item['product_code'] ?? '') ?></small>
                        </td>
                        <td class="text-center"><?= number_format($item['quantity'], 2) ?> <?= esc($item['unit'] ?? 'PCS') ?></td>
                        <td class="text-right">₹<?= number_format($item['unit_price'], 2) ?></td>
                        <td class="text-center"><?= number_format($item['cgst_rate'] ?? 0, 2) ?>%</td>
                        <td class="text-center"><?= number_format($item['sgst_rate'] ?? 0, 2) ?>%</td>
                        <td class="text-center"><?= number_format($item['igst_rate'] ?? 0, 2) ?>%</td>
                        <td class="text-right">₹<?= number_format($item['gst_amount'] ?? 0, 2) ?></td>
                        <td class="text-right"><strong>₹<?= number_format($item['total_amount'], 2) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">₹<?= number_format($bill['subtotal'] ?? 0, 2) ?></span>
        </div>
        <div class="total-row">
            <span class="total-label">GST Amount:</span>
            <span class="total-value">₹<?= number_format($bill['gst_amount'] ?? 0, 2) ?></span>
        </div>
        <?php if (isset($bill['paid_amount']) && $bill['paid_amount'] > 0): ?>
        <div class="total-row">
            <span class="total-label">Paid Amount:</span>
            <span class="total-value" style="color: #28a745;">₹<?= number_format($bill['paid_amount'], 2) ?></span>
        </div>
        <div class="total-row">
            <span class="total-label">Outstanding:</span>
            <span class="total-value" style="color: #dc3545;">₹<?= number_format($bill['total_amount'] - $bill['paid_amount'], 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row grand-total">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">₹<?= number_format($bill['total_amount'], 2) ?></span>
        </div>
    </div>
    
    <!-- Notes -->
    <?php if (!empty($bill['note'])): ?>
    <div style="margin-top: 40px;">
        <div class="section-title">Notes</div>
        <p style="margin: 10px 0; padding: 10px; background-color: #f5f5f5; border-radius: 4px;">
            <?= nl2br(esc($bill['note'])) ?>
        </p>
    </div>
    <?php endif; ?>
    
    <!-- Terms and Conditions -->
    <div style="margin-top: 40px;">
        <div class="section-title">Terms and Conditions</div>
        <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Payment should be made as per the payment terms mentioned.</li>
            <li>Goods received are subject to quality inspection.</li>
            <li>Returns will be accepted only for defective goods within 7 days of delivery.</li>
            <li>All disputes are subject to local jurisdiction.</li>
            <li>This is a computer generated document and is valid without signature.</li>
        </ol>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>This is a computer generated document. No signature required.</p>
        <p>Generated on: <?= date('d M Y H:i:s') ?> | PRODX Manufacturing ERP System</p>
    </div>
</body>
</html>

