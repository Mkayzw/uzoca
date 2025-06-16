<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/assets/Config.php';
require_once __DIR__ . '/lib/assets/DB.php';

try {
    $db = \app\assets\DB::getInstance();
    
    // Disable foreign key checks
    echo "Disabling foreign key checks...<br>";
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop existing table
    echo "Dropping existing properties table...<br>";
    $db->query("DROP TABLE IF EXISTS properties");
    
    // Create properties table with correct structure
    echo "Creating properties table...<br>";
    $sql = "CREATE TABLE properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_id INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        index_img VARCHAR(500) NOT NULL,
        title VARCHAR(500) NOT NULL,
        summary TEXT NOT NULL,
        img_1 VARCHAR(500) NOT NULL,
        img_2 VARCHAR(500) NOT NULL,
        img_3 VARCHAR(500) NOT NULL,
        img_4 VARCHAR(500) NOT NULL,
        img_5 VARCHAR(500) NOT NULL,
        description TEXT NOT NULL,
        type ENUM('For Rent','For Sale') NOT NULL DEFAULT 'For Rent',
        location VARCHAR(255) NOT NULL,
        status ENUM('available','sold','rented') NOT NULL DEFAULT 'available',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin";
    
    if ($db->query($sql)) {
        echo "Properties table created successfully.<br>";
        
        // Re-enable foreign key checks
        echo "Re-enabling foreign key checks...<br>";
        $db->query("SET FOREIGN_KEY_CHECKS = 1");
        
        // Show table structure
        $result = $db->query("DESCRIBE properties");
        echo "<pre>";
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "Error creating properties table.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    
    // Make sure to re-enable foreign key checks even if there's an error
    try {
        $db->query("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Exception $e2) {
        echo "<br>Error re-enabling foreign key checks: " . $e2->getMessage();
    }
}
?> 