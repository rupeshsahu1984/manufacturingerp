<?php
/**
 * Script to verify the stock table structure
 */

// Database configuration
$host = 'localhost';
$dbname = 'manufacturingerp';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if stock table exists and show its structure
    echo "Checking stock table structure...\n";
    $result = $pdo->query("DESCRIBE stock");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Stock table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    // Check if items table exists
    echo "\nChecking items table structure...\n";
    $result = $pdo->query("DESCRIBE items");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Items table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    // Test the join query that's failing
    echo "\nTesting the join query...\n";
    try {
        $query = "
            SELECT items.*, 
                   COALESCE(stock.current_stock, 0) as current_stock,
                   categories.category_name
            FROM items 
            LEFT JOIN categories ON categories.id = items.category_id 
            LEFT JOIN suppliers ON suppliers.id = items.preferred_supplier_id 
            LEFT JOIN (
                SELECT item_id, 
                       SUM(CASE WHEN status = 'available' THEN quantity ELSE 0 END) as current_stock 
                FROM stock 
                GROUP BY item_id
            ) as stock ON stock.item_id = items.id 
            WHERE items.status = 'active'
        ";
        
        $result = $pdo->query($query);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        echo "Join query executed successfully. Found " . count($rows) . " rows.\n";
        
    } catch (Exception $e) {
        echo "Join query failed: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
