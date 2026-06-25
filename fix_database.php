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

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sqls = [
    // Create supplier_invoices if not exists
    "CREATE TABLE IF NOT EXISTS `supplier_invoices` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `invoice_number` varchar(50) NOT NULL,
        `invoice_date` date NOT NULL,
        `supplier_id` int(11) NOT NULL,
        `purchase_order_id` int(11) DEFAULT NULL,
        `goods_receipt_id` int(11) DEFAULT NULL,
        `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
        `gst_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
        `transport_cost` decimal(15,2) DEFAULT '0.00',
        `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
        `payment_terms` varchar(255) DEFAULT NULL,
        `due_date` date DEFAULT NULL,
        `status` enum('draft','pending','approved','paid','partially_paid','overdue','cancelled') DEFAULT 'pending',
        `notes` text,
        `created_by` int(11) DEFAULT '1',
        `updated_by` int(11) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `invoice_number` (`invoice_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    // Create debit_notes if not exists
    "CREATE TABLE IF NOT EXISTS `debit_notes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `debit_note_number` varchar(50) NOT NULL,
        `debit_note_date` date NOT NULL,
        `supplier_id` int(11) NOT NULL,
        `purchase_order_id` int(11) DEFAULT NULL,
        `goods_receipt_id` int(11) DEFAULT NULL,
        `invoice_id` int(11) DEFAULT NULL,
        `return_reason` text NOT NULL,
        `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
        `gst_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
        `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
        `status` enum('draft','pending','approved','processed','cancelled') DEFAULT 'pending',
        `notes` text,
        `created_by` int(11) DEFAULT '1',
        `updated_by` int(11) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `debit_note_number` (`debit_note_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    // Create distributors table if not exists
    "CREATE TABLE IF NOT EXISTS `distributors` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `distributor_name` varchar(255) NOT NULL,
        `distributor_code` varchar(50) DEFAULT NULL,
        `contact_person` varchar(100) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `address` text,
        `status` enum('active','inactive') DEFAULT 'active',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    // Create customer_communications table if not exists
    "CREATE TABLE IF NOT EXISTS `customer_communications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `contact_id` int(11) DEFAULT NULL,
        `communication_type` varchar(50) DEFAULT NULL,
        `communication_date` date DEFAULT NULL,
        `subject` varchar(255) DEFAULT NULL,
        `notes` text,
        `created_by` int(11) DEFAULT '1',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    // Create customer_notes table if not exists
    "CREATE TABLE IF NOT EXISTS `customer_notes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `note` text NOT NULL,
        `created_by` int(11) DEFAULT '1',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    // Add missing columns to bom table
    "ALTER TABLE `bom` ADD COLUMN IF NOT EXISTS `bom_type` varchar(50) DEFAULT 'standard' AFTER `bom_number`;",
    
    // Add product_id to stock table if it doesn't exist
    "ALTER TABLE `stock` ADD COLUMN IF NOT EXISTS `product_id` int(11) DEFAULT NULL AFTER `item_id`;"
];

foreach ($sqls as $sql) {
    echo "Executing: " . substr($sql, 0, 100) . "...\n";
    if ($conn->query($sql)) {
        echo "Success\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}

$conn->close();
echo "Migration complete.\n";
