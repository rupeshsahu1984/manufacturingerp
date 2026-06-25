<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?= esc($payment['payment_number']) ?> - PRODX</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @media print {
            body {
                font-size: 12pt;
                line-height: 1.4;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                border: 2px solid #333 !important;
                box-shadow: none !important;
            }
            .receipt-header {
                background: #fff !important;
                color: #333 !important;
            }
            .btn {
                display: none !important;
            }
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #007bff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .receipt-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .receipt-header .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }
        
        .receipt-body {
            padding: 2rem;
        }
        
        .payment-details {
            margin-bottom: 2rem;
        }
        
        .payment-details h4 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            font-weight: 500;
            color: #333;
        }
        
        .amount-highlight {
            background: #e7f3ff;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .amount-highlight .amount {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
        }
        
        .payment-method-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .receipt-footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            border-top: 2px solid #007bff;
            text-align: center;
        }
        
        .receipt-footer .signature-area {
            margin-top: 2rem;
            display: flex;
            justify-content: space-around;
        }
        
        .signature-box {
            text-align: center;
            min-width: 200px;
        }
        
        .signature-line {
            border-bottom: 2px solid #333;
            margin-top: 3rem;
            padding-bottom: 0.5rem;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 4rem;
            opacity: 0.1;
            color: #007bff;
            font-weight: 700;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="no-print text-center mb-4">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print me-2"></i>Print Receipt
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-lg ms-2">
                <i class="fas fa-times me-2"></i>Close
            </button>
        </div>
        
        <div class="receipt-container position-relative">
            <div class="watermark">PAID</div>
            
            <!-- Receipt Header -->
            <div class="receipt-header">
                <h1><i class="fas fa-money-check-alt me-3"></i>PRODX ERP</h1>
                <div class="subtitle">Manufacturing ERP System</div>
                <div class="subtitle mt-2">Payment Receipt</div>
            </div>
            
            <!-- Receipt Body -->
            <div class="receipt-body">
                <!-- Payment Number and Date -->
                <div class="text-center mb-4">
                    <h2 class="text-primary">Receipt No: <?= esc($payment['payment_number']) ?></h2>
                    <p class="text-muted">Date: <?= date('d F Y', strtotime($payment['payment_date'])) ?></p>
                </div>
                
                <!-- Payment Details -->
                <div class="payment-details">
                    <h4><i class="fas fa-info-circle me-2"></i>Payment Details</h4>
                    
                    <div class="detail-row">
                        <span class="detail-label">Customer Name:</span>
                        <span class="detail-value"><?= esc($payment['customer_name'] ?? 'N/A') ?></span>
                    </div>
                    
                    <?php if (!empty($payment['invoice_number'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Invoice Number:</span>
                        <span class="detail-value"><?= esc($payment['invoice_number']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-row">
                        <span class="detail-label">Payment Date:</span>
                        <span class="detail-value"><?= date('d M Y', strtotime($payment['payment_date'])) ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value">
                            <span class="payment-method-badge"><?= ucfirst(esc($payment['payment_method'])) ?></span>
                        </span>
                    </div>
                    
                    <?php if (!empty($payment['reference_number'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Reference Number:</span>
                        <span class="detail-value"><?= esc($payment['reference_number']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($payment['notes'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Notes:</span>
                        <span class="detail-value"><?= esc($payment['notes']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Amount Highlight -->
                <div class="amount-highlight">
                    <div class="text-muted">Amount Paid</div>
                    <div class="amount">₹<?= number_format($payment['payment_amount'], 2) ?></div>
                    <div class="text-muted mt-2">
                        <?= number_format($payment['payment_amount'], 2) ?> Rupees Only
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="payment-details">
                    <h4><i class="fas fa-cog me-2"></i>Additional Information</h4>
                    
                    <div class="detail-row">
                        <span class="detail-label">Recorded By:</span>
                        <span class="detail-value"><?= esc($payment['created_by_name'] ?? 'System') ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Recorded At:</span>
                        <span class="detail-value"><?= date('d M Y H:i:s', strtotime($payment['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Receipt Footer -->
            <div class="receipt-footer">
                <div class="text-center mb-3">
                    <strong>This is a computer-generated receipt and does not require a signature</strong>
                </div>
                
                <div class="signature-area">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="text-muted mt-2">Authorized Signatory</div>
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="text-muted mt-2">Customer Signature</div>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    <small>Thank you for your business! | PRODX ERP System | Page 1 of 1</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(() => {
        //         window.print();
        //     }, 500);
        // };
        
        // Close window after printing
        window.onafterprint = function() {
            setTimeout(() => {
                window.close();
            }, 1000);
        };
    </script>
</body>
</html>
