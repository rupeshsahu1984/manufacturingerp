<?php
$db = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'manufacturingerp'
];

$conn = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables_to_check = [
    'leads',
    'goods_receipt_notes',
    'dispatch_notes',
    'debit_notes',
    'quotations',
    'supplier_invoices',
    'employees',
    'departments',
    'invoices',
    'customers',
    'suppliers',
    'products',
    'warehouses',
    'purchase_orders',
    'purchase_order_items',
    'sales_orders',
    'sales_order_items',
    'stocks',
    'gst_settings'
];

echo "Checking tables in " . $db['database'] . ":\n";
foreach ($tables_to_check as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "EXISTS: $table\n";
    } else {
        echo "MISSING: $table\n";
    }
}

$conn->close();
