<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/assets/Config.php';
require_once __DIR__ . '/lib/assets/DB.php';

try {
    $db = \app\assets\DB::getInstance();
    
    // Check if rooms table exists
    $result = $db->query("SHOW TABLES LIKE 'rooms'");
    if ($result && $result->num_rows > 0) {
        echo "Rooms table exists.<br>";
        
        // Check if description column exists
        $result = $db->query("SHOW COLUMNS FROM rooms LIKE 'description'");
        if ($result && $result->num_rows > 0) {
            echo "description column exists.<br>";
        } else {
            echo "Adding description column...<br>";
            
            // Add description column
            $sql = "ALTER TABLE rooms ADD COLUMN description TEXT AFTER name";
            
            if ($db->query($sql)) {
                echo "description column added successfully.<br>";
            } else {
                echo "Error adding description column.<br>";
            }
        }
        
        // Check if price column exists
        $result = $db->query("SHOW COLUMNS FROM rooms LIKE 'price'");
        if ($result && $result->num_rows > 0) {
            echo "price column exists.<br>";
        } else {
            echo "Adding price column...<br>";
            
            // Add price column
            $sql = "ALTER TABLE rooms ADD COLUMN price DECIMAL(10,2) NOT NULL AFTER description";
            
            if ($db->query($sql)) {
                echo "price column added successfully.<br>";
            } else {
                echo "Error adding price column.<br>";
            }
        }
    } else {
        echo "Creating rooms table...<br>";
        
        // Create rooms table
        $sql = "CREATE TABLE rooms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
        )";
        
        if ($db->query($sql)) {
            echo "Rooms table created successfully.<br>";
        } else {
            echo "Error creating rooms table.<br>";
        }
    }
    
    // Show table structure
    $result = $db->query("DESCRIBE rooms");
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 