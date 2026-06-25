<?php
$envFile = '.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $env[$name] = $value;
    }
}
$host = $env['database.default.hostname'] ?? 'localhost';
$user = $env['database.default.username'] ?? 'root';
$pass = $env['database.default.password'] ?? '';
$db   = $env['database.default.database'] ?? 'manufacturingerp';
$conn = new mysqli($host, $user, $pass, $db);
$result = $conn->query("DESCRIBE quotations");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "," . $row['Type'] . "\n";
}
