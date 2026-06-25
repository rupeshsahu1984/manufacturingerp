<?php
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: " . DB_NAME . "\n";
    
    // Check if products table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();
    echo "Current products count: $count\n";
    
    if ($count == 0) {
        echo "\nAdding sample products...\n";
        
        // Sample products data
        $sampleProducts = [
            [
                'product_code' => 'FG001',
                'product_name' => 'Premium Widget A',
                'description' => 'High-quality premium widget for industrial use',
                'unit' => 'PCS',
                'selling_price' => 150.00,
                'unit_price' => 120.00,
                'gst_rate' => 18.00,
                'cgst_rate' => 9.00,
                'sgst_rate' => 9.00,
                'igst_rate' => 18.00,
                'hsn_code' => 'HSN001',
                'category_id' => 1,
                'material_type' => 'finished_goods',
                'status' => 'active'
            ],
            [
                'product_code' => 'FG002',
                'product_name' => 'Standard Widget B',
                'description' => 'Standard quality widget for general use',
                'unit' => 'PCS',
                'selling_price' => 100.00,
                'unit_price' => 80.00,
                'gst_rate' => 18.00,
                'cgst_rate' => 9.00,
                'sgst_rate' => 9.00,
                'igst_rate' => 18.00,
                'hsn_code' => 'HSN002',
                'category_id' => 1,
                'material_type' => 'finished_goods',
                'status' => 'active'
            ],
            [
                'product_code' => 'FG003',
                'product_name' => 'Economy Widget C',
                'description' => 'Economy widget for budget applications',
                'unit' => 'PCS',
                'selling_price' => 75.00,
                'unit_price' => 60.00,
                'gst_rate' => 12.00,
                'cgst_rate' => 6.00,
                'sgst_rate' => 6.00,
                'igst_rate' => 12.00,
                'hsn_code' => 'HSN003',
                'category_id' => 1,
                'material_type' => 'finished_goods',
                'status' => 'active'
            ],
            [
                'product_code' => 'FG004',
                'product_name' => 'Industrial Component X',
                'description' => 'Heavy-duty industrial component',
                'unit' => 'PCS',
                'selling_price' => 250.00,
                'unit_price' => 200.00,
                'gst_rate' => 18.00,
                'cgst_rate' => 9.00,
                'sgst_rate' => 9.00,
                'igst_rate' => 18.00,
                'hsn_code' => 'HSN004',
                'category_id' => 1,
                'material_type' => 'finished_goods',
                'status' => 'active'
            ],
            [
                'product_code' => 'FG005',
                'product_name' => 'Precision Tool Y',
                'description' => 'High-precision measurement tool',
                'unit' => 'PCS',
                'selling_price' => 500.00,
                'unit_price' => 400.00,
                'gst_rate' => 18.00,
                'cgst_rate' => 9.00,
                'sgst_rate' => 9.00,
                'igst_rate' => 18.00,
                'hsn_code' => 'HSN005',
                'category_id' => 1,
                'material_type' => 'finished_goods',
                'status' => 'active'
            ]
        ];
        
        // Insert sample products
        $sql = "INSERT INTO products (product_code, product_name, description, unit, selling_price, unit_price, gst_rate, cgst_rate, sgst_rate, igst_rate, hsn_code, category_id, material_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $inserted = 0;
        foreach ($sampleProducts as $product) {
            $stmt->execute([
                $product['product_code'],
                $product['product_name'],
                $product['description'],
                $product['unit'],
                $product['selling_price'],
                $product['unit_price'],
                $product['gst_rate'],
                $product['cgst_rate'],
                $product['sgst_rate'],
                $product['igst_rate'],
                $product['hsn_code'],
                $product['category_id'],
                $product['material_type'],
                $product['status']
            ]);
            $inserted++;
        }
        
        echo "✅ Successfully added $inserted sample products!\n";
        
        // Verify the insertion
        $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE material_type = 'finished_goods' AND status = 'active'");
        $finishedCount = $stmt->fetchColumn();
        echo "✅ Active finished goods: $finishedCount\n";
        
        // Show the products
        $stmt = $pdo->query("SELECT product_code, product_name, selling_price, cgst_rate, sgst_rate, igst_rate FROM products WHERE material_type = 'finished_goods' AND status = 'active'");
        echo "\nSample products added:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['product_code']}: {$row['product_name']} (₹{$row['selling_price']})\n";
        }
        
        echo "\n🎉 Now your dropdown should work!\n";
        echo "Try your sales order page again: http://localhost/manufacturingerp/sales-order/create\n";
        
    } else {
        echo "Products table already has $count products.\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 