<?php
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: " . DB_NAME . "\n";
    
    // First, let's see what we have
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $totalProducts = $stmt->fetchColumn();
    echo "Total products in database: $totalProducts\n";
    
    // Check current material_type values
    $stmt = $pdo->query("SELECT DISTINCT material_type FROM products");
    $materialTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current material types: " . implode(', ', $materialTypes) . "\n";
    
    // Check current status values
    $stmt = $pdo->query("SELECT DISTINCT status FROM products");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current statuses: " . implode(', ', $statuses) . "\n";
    
    // Update ALL existing products to be finished goods and active
    echo "\nUpdating all existing products...\n";
    
    $sql = "UPDATE products SET material_type = 'finished_goods', status = 'active'";
    $affected = $pdo->exec($sql);
    echo "✅ Updated $affected products to finished_goods and active status\n";
    
    // Set default prices and GST rates for products that don't have them
    echo "\nSetting default prices and GST rates...\n";
    
    $sql = "UPDATE products SET 
            selling_price = COALESCE(selling_price, 100.00), 
            unit_price = COALESCE(unit_price, 100.00), 
            gst_rate = COALESCE(gst_rate, 18.00), 
            cgst_rate = COALESCE(cgst_rate, 9.00), 
            sgst_rate = COALESCE(sgst_rate, 9.00), 
            igst_rate = COALESCE(igst_rate, 18.00)";
    $affected = $pdo->exec($sql);
    echo "✅ Updated $affected products with default prices and GST rates\n";
    
    // Verify the update
    echo "\nVerifying update...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE material_type = 'finished_goods' AND status = 'active'");
    $finishedCount = $stmt->fetchColumn();
    echo "✅ Active finished goods: $finishedCount\n";
    
    // Show sample products
    $stmt = $pdo->query("SELECT id, product_code, product_name, material_type, status, selling_price, cgst_rate, sgst_rate, igst_rate FROM products WHERE material_type = 'finished_goods' AND status = 'active' LIMIT 5");
    echo "\nSample finished goods:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['product_code']}: {$row['product_name']} (₹{$row['selling_price']})\n";
    }
    
    echo "\n🎉 All products are now finished goods and active!\n";
    echo "Now try your sales order page again.\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
