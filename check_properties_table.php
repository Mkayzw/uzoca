<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/assets/Config.php';
require_once __DIR__ . '/lib/assets/DB.php';

try {
    $db = \app\assets\DB::getInstance();
    
    // Check if properties table exists
    $result = $db->query("SHOW TABLES LIKE 'properties'");
    if ($result && $result->num_rows > 0) {
        echo "Properties table exists.<br>";
        
        // Check if owner_id column exists
        $result = $db->query("SHOW COLUMNS FROM properties LIKE 'owner_id'");
        if ($result && $result->num_rows > 0) {
            echo "owner_id column exists.<br>";
        } else {
            echo "Adding owner_id column...<br>";
            
            // Add owner_id column
            $sql = "ALTER TABLE properties ADD COLUMN owner_id INT NOT NULL AFTER id, ADD FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE";
            
            if ($db->query($sql)) {
                echo "owner_id column added successfully.<br>";
            } else {
                echo "Error adding owner_id column.<br>";
            }
        }
        
        // Show table structure
        $result = $db->query("DESCRIBE properties");
        echo "<pre>";
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "Properties table does not exist. Creating it...<br>";
        
        // Create properties table
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
            echo "Properties table created successfully.";
        } else {
            echo "Error creating properties table.";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 