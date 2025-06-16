<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/assets/Config.php';
require_once __DIR__ . '/lib/assets/DB.php';

try {
    $db = \app\assets\DB::getInstance();
    
    // Check if users table exists
    $result = $db->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "Users table exists.<br>";
        
        // Show table structure
        $result = $db->query("DESCRIBE users");
        echo "<pre>";
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "Users table does not exist. Creating it...<br>";
        
        // Create users table
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            user_type ENUM('landlord', 'agent') NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($db->query($sql)) {
            echo "Users table created successfully.";
        } else {
            echo "Error creating users table.";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 