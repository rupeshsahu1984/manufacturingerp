<?php
// Database update script
$host = 'localhost';
$username = 'root';
$password = ''; // Add your MySQL password here if you have one
$database = 'manufacturingerp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Add missing columns to suppliers table
    $sql = "ALTER TABLE suppliers 
            ADD COLUMN IF NOT EXISTS supplier_category ENUM('raw_material', 'packaging', 'service') DEFAULT 'raw_material',
            ADD COLUMN IF NOT EXISTS bank_name VARCHAR(100),
            ADD COLUMN IF NOT EXISTS bank_account VARCHAR(50),
            ADD COLUMN IF NOT EXISTS bank_ifsc VARCHAR(20),
            ADD COLUMN IF NOT EXISTS payment_terms VARCHAR(100),
            ADD COLUMN IF NOT EXISTS credit_limit DECIMAL(15,2) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS return_policy TEXT,
            ADD COLUMN IF NOT EXISTS credit_terms VARCHAR(100)";
    
    $pdo->exec($sql);
    echo "Updated suppliers table successfully.\n";
    
    // Add missing columns to customers table
    $sql = "ALTER TABLE customers 
            ADD COLUMN IF NOT EXISTS pan_number VARCHAR(20),
            ADD COLUMN IF NOT EXISTS payment_terms VARCHAR(100),
            ADD COLUMN IF NOT EXISTS return_policy TEXT,
            ADD COLUMN IF NOT EXISTS debit_note_config TEXT,
            ADD COLUMN IF NOT EXISTS sales_zone VARCHAR(50),
            ADD COLUMN IF NOT EXISTS sales_region VARCHAR(50)";
    
    $pdo->exec($sql);
    echo "Updated customers table successfully.\n";
    
    // Add missing columns to categories table
    $sql = "ALTER TABLE categories 
            ADD COLUMN IF NOT EXISTS category_type ENUM('raw_material', 'packaging', 'finished_goods', 'waste') DEFAULT 'raw_material'";
    
    $pdo->exec($sql);
    echo "Updated categories table successfully.\n";
    
    // Add missing columns to products table
    $sql = "ALTER TABLE products 
            ADD COLUMN IF NOT EXISTS unit VARCHAR(20) DEFAULT 'PCS',
            ADD COLUMN IF NOT EXISTS reorder_level DECIMAL(10,2) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS material_type ENUM('raw_material', 'packaging', 'finished_goods', 'waste') DEFAULT 'raw_material',
            ADD COLUMN IF NOT EXISTS waste_percentage DECIMAL(5,2) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS is_recyclable BOOLEAN DEFAULT FALSE";
    
    $pdo->exec($sql);
    echo "Updated products table successfully.\n";
    
    // Add missing columns to warehouses table
    $sql = "ALTER TABLE warehouses 
            ADD COLUMN IF NOT EXISTS manager_id INT,
            ADD CONSTRAINT fk_warehouse_manager FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL";
    
    $pdo->exec($sql);
    echo "Updated warehouses table successfully.\n";
    
    // Create purchase_bills table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS purchase_bills (
        id INT PRIMARY KEY AUTO_INCREMENT,
        bill_number VARCHAR(20) UNIQUE NOT NULL,
        po_id INT,
        supplier_id INT NOT NULL,
        bill_date DATE NOT NULL,
        due_date DATE,
        invoice_number VARCHAR(50),
        subtotal DECIMAL(15,2) DEFAULT 0,
        gst_amount DECIMAL(15,2) DEFAULT 0,
        total_amount DECIMAL(15,2) DEFAULT 0,
        paid_amount DECIMAL(15,2) DEFAULT 0,
        status ENUM('draft', 'received', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "Created purchase_bills table successfully.\n";
    
    // Create purchase_bill_items table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS purchase_bill_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        bill_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        unit_price DECIMAL(15,2) NOT NULL,
        gst_rate DECIMAL(5,2) DEFAULT 18.00,
        gst_amount DECIMAL(15,2) DEFAULT 0,
        total_amount DECIMAL(15,2) NOT NULL,
        FOREIGN KEY (bill_id) REFERENCES purchase_bills(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    
    $pdo->exec($sql);
    echo "Created purchase_bill_items table successfully.\n";
    
    echo "Database update completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 