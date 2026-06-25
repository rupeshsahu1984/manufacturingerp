<?php
$baseUrl = 'http://localhost/manufacturingerp/public/';

$pages = [
    'dashboard', 'inventory', 'production', 'purchase', 'sales', 'hr', 'accounting',
    'customer', 'supplier', 'product', 'warehouse', 'employee', 'department',
    'gate-entry', 'gate-exit', 'visitor-management', 'bom', 'quotation',
    'sales-order', 'dispatch', 'invoice', 'sales-return', 'customer-payment',
    'purchase-requisition', 'purchase-order', 'purchase-bill', 'goods-receipt',
    'supplier-invoice', 'debit-note', 'maintenance', 'gst', 'help'
];

function getLoginCookie($loginUrl, $username, $password) {
    $cookieFile = __DIR__ . '/cli_test_cookie.txt';
    if (file_exists($cookieFile)) unlink($cookieFile);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => $username, 'password' => $password]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
    return $cookieFile;
}

$cookieFile = getLoginCookie($baseUrl . 'login', 'admin', 'admin123');

echo str_repeat("-", 50) . "\n";
printf("%-25s | %-10s\n", "Page", "Status");
echo str_repeat("-", 50) . "\n";

foreach ($pages as $path) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $path);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    printf("%-25s | %-10d\n", $path, $code);
}
echo str_repeat("-", 50) . "\n";
