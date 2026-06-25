<?php
$envFile = '.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value);
    }
}
$host = $env['database.default.hostname'] ?? 'localhost';
$user = $env['database.default.username'] ?? 'root';
$pass = $env['database.default.password'] ?? '';
$db   = $env['database.default.database'] ?? 'manufacturingerp';
$conn = new mysqli($host, $user, $pass, $db);

$output = "";
$tables = ['stock', 'bom', 'items', 'products', 'purchase_bills', 'goods_receipt_notes'];
foreach ($tables as $table) {
    $output .= "--- $table ---\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $output .= $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        $output .= "Table $table not found or error: " . $conn->error . "\n";
    }
    $output .= "\n";
}
file_put_contents('comprehensive_schema.txt', $output);
echo "Schema dumped to comprehensive_schema.txt\n";
