<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - <?= esc($sales_order['so_number']) ?></title>
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
        
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .order-details, .customer-details {
            flex: 1;
        }
        
        .order-details {
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
            width: 120px;
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
            width: 100px;
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
        .status-confirmed { background: #28a745; color: white; }
        .status-processing { background: #ffc107; color: black; }
        .status-ready { background: #17a2b8; color: white; }
        .status-dispatched { background: #007bff; color: white; }
        .status-delivered { background: #28a745; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
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
    <div class="document-title">SALES ORDER</div>
    
    <!-- Order and Customer Information -->
    <div class="order-info">
        <div class="order-details">
            <div class="section-title">Order Information</div>
            <div class="info-row">
                <span class="info-label">SO Number:</span>
                <span class="info-value"><?= esc($sales_order['so_number']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value"><?= date('d M Y', strtotime($sales_order['order_date'])) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Delivery Date:</span>
                <span class="info-value">
                    <?= $sales_order['delivery_date'] ? date('d M Y', strtotime($sales_order['delivery_date'])) : 'Not set' ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge status-<?= $sales_order['status'] ?>">
                        <?= ucfirst($sales_order['status']) ?>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Terms:</span>
                <span class="info-value"><?= esc(isset($sales_order['payment_terms']) ? $sales_order['payment_terms'] : 'Not specified') ?></span>
            </div>
        </div>
        
        <div class="customer-details">
            <div class="section-title">Customer Information</div>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value"><?= esc(isset($sales_order['customer_name']) ? $sales_order['customer_name'] : 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer Code:</span>
                <span class="info-value"><?= esc(isset($sales_order['customer_code']) ? $sales_order['customer_code'] : 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Contact Person:</span>
                <span class="info-value"><?= esc(isset($sales_order['contact_person']) ? $sales_order['contact_person'] : 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?= esc(isset($sales_order['phone']) ? $sales_order['phone'] : 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= esc(isset($sales_order['email']) ? $sales_order['email'] : 'N/A') ?></span>
            </div>
            <?php if (!empty($sales_order['delivery_address'])): ?>
            <div class="info-row">
                <span class="info-label">Delivery Address:</span>
                <span class="info-value"><?= nl2br(esc($sales_order['delivery_address'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="section-title">Order Items</div>
    <?php if (empty($sales_order['items'])): ?>
        <p>No items found in this sales order.</p>
    <?php else: ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Product</th>
                    <th width="25%">Description</th>
                    <th width="10%" class="text-center">Quantity</th>
                    <th width="12%" class="text-right">Unit Price</th>
                    <th width="13%" class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_order['items'] as $index => $item): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td>
                            <strong><?= esc(isset($item['product_name']) ? $item['product_name'] : 'N/A') ?></strong><br>
                            <small><?= esc(isset($item['product_code']) ? $item['product_code'] : '') ?></small>
                        </td>
                        <td><?= esc(isset($item['description']) ? $item['description'] : '') ?></td>
                        <td class="text-center"><?= number_format($item['quantity']) ?></td>
                        <td class="text-right">₹<?= number_format($item['unit_price'], 2) ?></td>
                        <td class="text-right"><strong>₹<?= number_format(isset($item['total_amount']) ? $item['total_amount'] : ($item['quantity'] * $item['unit_price']), 2) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">₹<?= number_format($sales_order['subtotal'], 2) ?></span>
        </div>
        <div class="total-row">
            <span class="total-label">GST (18%):</span>
            <span class="total-value">₹<?= number_format($sales_order['gst_amount'], 2) ?></span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">₹<?= number_format($sales_order['total_amount'], 2) ?></span>
        </div>
    </div>
    
    <!-- Terms and Conditions -->
    <div style="margin-top: 40px;">
        <div class="section-title">Terms and Conditions</div>
        <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Payment is due within the specified payment terms.</li>
            <li>Delivery will be made to the address specified above.</li>
            <li>Goods once delivered will not be taken back unless defective.</li>
            <li>All disputes are subject to local jurisdiction.</li>
            <li>Prices are subject to change without prior notice.</li>
        </ol>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>This is a computer generated document. No signature required.</p>
        <p>Generated on: <?= date('d M Y H:i:s') ?> | PRODX Manufacturing ERP System</p>
    </div>
</body>
</html>
