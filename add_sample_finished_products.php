<?php
// Add sample finished products to the database
echo "<h1>Adding Sample Finished Products</h1>";

try {
    // Connect to database
    $db = new PDO('mysql:host=localhost;dbname=manufacturingerp', 'root', '');
    echo "✅ Database connection successful<br><br>";
    
    // Check if products table exists
    $stmt = $db->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Products table does not exist! Please create it first.<br>";
        exit;
    }
    
    // Check if categories table exists
    $stmt = $db->query("SHOW TABLES LIKE 'categories'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Categories table does not exist! Please create it first.<br>";
        exit;
    }
    
    // Get or create a default category
    $stmt = $db->query("SELECT id FROM categories LIMIT 1");
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        // Create a default category
        $stmt = $db->prepare("INSERT INTO categories (category_name, category_type, description, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Finished Goods', 'product', 'Default category for finished goods', 'active']);
        $categoryId = $db->lastInsertId();
        echo "✅ Created default category with ID: $categoryId<br>";
    } else {
        $categoryId = $category['id'];
        echo "✅ Using existing category with ID: $categoryId<br>";
    }
    
    // Sample finished products data
    $sampleProducts = [
        [
            'product_code' => 'FG001',
            'product_name' => 'Premium Widget A',
            'description' => 'High quality finished widget for industrial use',
            'category_id' => $categoryId,
            'unit' => 'PCS',
            'unit_price' => 80.00,
            'selling_price' => 100.00,
            'reorder_level' => 10,
            'material_type' => 'finished_goods',
            'waste_percentage' => 2.0,
            'is_recyclable' => 1,
            'gst_rate' => 18.00,
            'cgst_rate' => 9.00,
            'sgst_rate' => 9.00,
            'igst_rate' => 18.00,
            'hsn_code' => 'HSN001',
            'status' => 'active'
        ],
        [
            'product_code' => 'FG002',
            'product_name' => 'Standard Widget B',
            'description' => 'Standard finished widget for general use',
            'category_id' => $categoryId,
            'unit' => 'PCS',
            'unit_price' => 120.00,
            'selling_price' => 150.00,
            'reorder_level' => 15,
            'material_type' => 'finished_goods',
            'waste_percentage' => 1.5,
            'is_recyclable' => 1,
            'gst_rate' => 18.00,
            'cgst_rate' => 9.00,
            'sgst_rate' => 9.00,
            'igst_rate' => 18.00,
            'hsn_code' => 'HSN002',
            'status' => 'active'
        ],
        [
            'product_code' => 'FG003',
            'product_name' => 'Economy Widget C',
            'description' => 'Economy finished widget for budget applications',
            'category_id' => $categoryId,
            'unit' => 'PCS',
            'unit_price' => 60.00,
            'selling_price' => 75.00,
            'reorder_level' => 20,
            'material_type' => 'finished_goods',
            'waste_percentage' => 3.0,
            'is_recyclable' => 0,
            'gst_rate' => 12.00,
            'cgst_rate' => 6.00,
            'sgst_rate' => 6.00,
            'igst_rate' => 12.00,
            'hsn_code' => 'HSN003',
            'status' => 'active'
        ],
        [
            'product_code' => 'FG004',
            'product_name' => 'Luxury Widget D',
            'description' => 'Premium finished widget for high-end applications',
            'category_id' => $categoryId,
            'unit' => 'PCS',
            'unit_price' => 240.00,
            'selling_price' => 300.00,
            'reorder_level' => 5,
            'material_type' => 'finished_goods',
            'waste_percentage' => 1.0,
            'is_recyclable' => 1,
            'gst_rate' => 28.00,
            'cgst_rate' => 14.00,
            'sgst_rate' => 14.00,
            'igst_rate' => 28.00,
            'hsn_code' => 'HSN004',
            'status' => 'active'
        ],
        [
            'product_code' => 'FG005',
            'product_name' => 'Bulk Widget E',
            'description' => 'Bulk finished widget for large orders',
            'category_id' => $categoryId,
            'unit' => 'BOX',
            'unit_price' => 400.00,
            'selling_price' => 500.00,
            'reorder_level' => 8,
            'material_type' => 'finished_goods',
            'waste_percentage' => 0.5,
            'is_recyclable' => 1,
            'gst_rate' => 18.00,
            'cgst_rate' => 9.00,
            'sgst_rate' => 9.00,
            'igst_rate' => 18.00,
            'hsn_code' => 'HSN005',
            'status' => 'active'
        ]
    ];
    
    // Insert sample products
    $stmt = $db->prepare("
        INSERT INTO products (
            product_code, product_name, description, category_id, unit, unit_price, selling_price,
            reorder_level, material_type, waste_percentage, is_recyclable, gst_rate, cgst_rate,
            sgst_rate, igst_rate, hsn_code, status, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
        )
    ");
    
    $insertedCount = 0;
    foreach ($sampleProducts as $product) {
        try {
            $stmt->execute([
                $product['product_code'],
                $product['product_name'],
                $product['description'],
                $product['category_id'],
                $product['unit'],
                $product['unit_price'],
                $product['selling_price'],
                $product['reorder_level'],
                $product['material_type'],
                $product['waste_percentage'],
                $product['is_recyclable'],
                $product['gst_rate'],
                $product['cgst_rate'],
                $product['sgst_rate'],
                $product['igst_rate'],
                $product['hsn_code'],
                $product['status']
            ]);
            $insertedCount++;
            echo "✅ Added product: {$product['product_code']} - {$product['product_name']}<br>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠️ Product {$product['product_code']} already exists, skipping...<br>";
            } else {
                echo "❌ Error adding product {$product['product_code']}: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    echo "<br>✅ Successfully added $insertedCount sample finished products!<br>";
    
    // Verify the products were added
    $stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE material_type = 'finished_goods' AND status = 'active'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<br>Total active finished goods products now: " . $result['count'] . "<br>";
    
    // Show the added products
    $stmt = $db->query("SELECT id, product_code, product_name, selling_price, cgst_rate, sgst_rate, igst_rate FROM products WHERE material_type = 'finished_goods' AND status = 'active' ORDER BY product_code");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<br><h3>Available Finished Products:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Price</th><th>CGST</th><th>SGST</th><th>IGST</th></tr>";
    
    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>{$product['id']}</td>";
        echo "<td>{$product['product_code']}</td>";
        echo "<td>{$product['product_name']}</td>";
        echo "<td>₹{$product['selling_price']}</td>";
        echo "<td>{$product['cgst_rate']}%</td>";
        echo "<td>{$product['sgst_rate']}%</td>";
        echo "<td>{$product['igst_rate']}%</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Now test the dropdown: <a href='test_product_dropdown.php' target='_blank'>test_product_dropdown.php</a></li>";
    echo "<li>Test the API endpoint: <a href='product/finished-goods-dropdown' target='_blank'>product/finished-goods-dropdown</a></li>";
    echo "<li>Use in your sales order form</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
