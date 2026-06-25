<?php
/**
 * ERP Page Tester
 * This script logs in as admin and checks the status of various pages.
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$baseUrl = 'http://localhost/manufacturingerp/public/'; // Standard CI4 base
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'test_all_pages.php') !== false) {
    // If accessed via web
    $baseUrl = 'http://localhost/manufacturingerp/public/';
}

// Pages to test
$pages = [
    'dashboard' => 'Main Dashboard',
    'inventory' => 'Inventory Management',
    'production' => 'Production Management',
    'purchase' => 'Purchase Management',
    'sales' => 'Sales & Distribution',
    'hr' => 'HR Management',
    'accounting' => 'Accounting',
    'customer' => 'Customer Master',
    'supplier' => 'Supplier Master',
    'product' => 'Product Master',
    'warehouse' => 'Warehouse Master',
    'employee' => 'Employee Master',
    'department' => 'Department Master',
    'gate-entry' => 'Gate Entry',
    'gate-exit' => 'Gate Exit',
    'visitor-management' => 'Visitor Management',
    'bom' => 'BOM Management',
    'quotation' => 'Quotations',
    'sales-order' => 'Sales Orders',
    'dispatch' => 'Dispatch Notes',
    'invoice' => 'Invoices',
    'sales-return' => 'Sales Returns',
    'customer-payment' => 'Customer Payments',
    'purchase-requisition' => 'Purchase Requisitions',
    'purchase-order' => 'Purchase Orders',
    'purchase-bill' => 'Purchase Bills',
    'goods-receipt' => 'GRN',
    'supplier-invoice' => 'Supplier Invoices',
    'debit-note' => 'Debit Notes',
    'maintenance' => 'Maintenance',
    'gst' => 'GST Management',
    'help' => 'Help & Support'
];

// 1. Get Login Cookie
function getLoginCookie($loginUrl, $username, $password) {
    $cookieFile = __DIR__ . '/test_cookie.txt';
    if (file_exists($cookieFile)) unlink($cookieFile);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'password' => $password
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    return [
        'success' => $info['http_code'] == 200,
        'cookieFile' => $cookieFile
    ];
}

// 2. Test Page
function testPage($url, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $status = 'OK';
    if ($httpCode >= 400) $status = 'ERROR';
    if ($httpCode == 302) $status = 'REDIRECT';
    
    return [
        'code' => $httpCode,
        'status' => $status,
        'error' => $error,
        'size' => strlen($content)
    ];
}

// Execute
$login = getLoginCookie($baseUrl . 'login', 'admin', 'admin123');

?>
<!DOCTYPE html>
<html>
<head>
    <title>ERP Page Test Results</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #ff6b35; color: white; }
        .status-OK { color: green; font-weight: bold; }
        .status-ERROR { color: red; font-weight: bold; }
        .status-REDIRECT { color: orange; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-200 { background: #d4edda; color: #155724; }
        .badge-404 { background: #f8d7da; color: #721c24; }
        .badge-302 { background: #fff3cd; color: #856404; }
        .badge-500 { background: #f8d7da; color: #721c24; border: 1px solid red; }
    </style>
</head>
<body>
    <h1>ERP System Page Status Report</h1>
    <p>Testing connectivity and response of core modules as 'admin' user.</p>
    
    <?php if (!$login['success']): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px;">
            <strong>Login Failed!</strong> Could not authenticate with admin credentials. Please ensure the server is running and credentials are correct.
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Module / Page</th>
                <th>URL Path</th>
                <th>HTTP Code</th>
                <th>Status</th>
                <th>Page Size</th>
                <th>Diagnostic</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $path => $name): ?>
            <?php 
                $url = $baseUrl . $path;
                $result = testPage($url, $login['cookieFile']);
            ?>
            <tr>
                <td><strong><?php echo $name; ?></strong></td>
                <td><code>/<?php echo $path; ?></code></td>
                <td><span class="badge badge-<?php echo $result['code']; ?>"><?php echo $result['code']; ?></span></td>
                <td><span class="status-<?php echo $result['status']; ?>"><?php echo $result['status']; ?></span></td>
                <td><?php echo number_format($result['size'] / 1024, 2); ?> KB</td>
                <td>
                    <?php 
                    if ($result['code'] == 200) echo "✓ Functional";
                    elseif ($result['code'] == 302) echo "→ Redirected (Login/Auth?)";
                    elseif ($result['code'] == 404) echo "✗ Not Found";
                    elseif ($result['code'] == 500) echo "!! Server Error";
                    else echo "Unknown Status";
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php
    // CLI Output (only if run from command line)
    if (php_sapi_name() === 'cli') {
        echo "\nERP Page Test Results (CLI Summary):\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-30s | %-15s | %-10s | %-15s\n", "Page", "URL", "Code", "Status");
        echo str_repeat("-", 80) . "\n";
        foreach ($pages as $path => $name) {
            $url = $baseUrl . $path;
            $result = testPage($url, $login['cookieFile']);
            printf("%-30s | /%-14s | %-10s | %-15s\n", $name, $path, $result['code'], $result['status'] == 'OK' ? "Functional" : "FAIL (" . $result['code'] . ")");
        }
        echo str_repeat("-", 80) . "\n";
    }
    ?>
    
    <div style="margin-top: 20px; font-size: 12px; color: #666;">
        Report generated at: <?php echo date('Y-m-d H:i:s'); ?>
    </div>
</body>
</html>
