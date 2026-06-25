<?php
// Add sample users with different roles
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'manufacturingerp';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Sample users data with different roles
$users = [
    [
        'username' => 'admin',
        'email' => 'admin@prodx.com',
        'password' => 'admin123',
        'full_name' => 'Super Administrator',
        'role' => 'super_admin',
        'status' => 'active'
    ],
    [
        'username' => 'purchase',
        'email' => 'purchase@prodx.com',
        'password' => 'purchase123',
        'full_name' => 'Purchase Manager',
        'role' => 'purchase',
        'status' => 'active'
    ],
    [
        'username' => 'sales',
        'email' => 'sales@prodx.com',
        'password' => 'sales123',
        'full_name' => 'Sales Manager',
        'role' => 'sales',
        'status' => 'active'
    ],
    [
        'username' => 'production',
        'email' => 'production@prodx.com',
        'password' => 'production123',
        'full_name' => 'Production Manager',
        'role' => 'production',
        'status' => 'active'
    ],
    [
        'username' => 'finance',
        'email' => 'finance@prodx.com',
        'password' => 'finance123',
        'full_name' => 'Finance Manager',
        'role' => 'finance',
        'status' => 'active'
    ],
    [
        'username' => 'gate_entry',
        'email' => 'gate@prodx.com',
        'password' => 'gate123',
        'full_name' => 'Gate Entry Officer',
        'role' => 'gate_entry',
        'status' => 'active'
    ],
    [
        'username' => 'hrm',
        'email' => 'hrm@prodx.com',
        'password' => 'hrm123',
        'full_name' => 'HR Manager',
        'role' => 'hrm',
        'status' => 'active'
    ],
    [
        'username' => 'reception',
        'email' => 'reception@prodx.com',
        'password' => 'reception123',
        'full_name' => 'Receptionist',
        'role' => 'reception',
        'status' => 'active'
    ]
];

// Check if users already exist
$result = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo "Sample users already exist in database. Skipping...\n";
} else {
    // Insert sample users
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    
    $inserted = 0;
    foreach ($users as $user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        
        $stmt->bind_param("ssssss", 
            $user['username'],
            $user['email'],
            $hashedPassword,
            $user['full_name'],
            $user['role'],
            $user['status']
        );
        
        if ($stmt->execute()) {
            $inserted++;
            echo "✓ Added: " . $user['full_name'] . " (" . $user['role'] . ")\n";
        } else {
            echo "✗ Failed to add: " . $user['full_name'] . " - " . $stmt->error . "\n";
        }
    }
    
    echo "\nSuccessfully added $inserted users to database.\n";
}

// Display login credentials
echo "\n=== LOGIN CREDENTIALS ===\n";
foreach ($users as $user) {
    echo $user['role'] . ": " . $user['username'] . " / " . $user['password'] . "\n";
}

$mysqli->close();
echo "\nScript completed!\n";
?> 