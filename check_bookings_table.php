<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/assets/Config.php';
require_once __DIR__ . '/lib/assets/DB.php';

try {
    $db = \app\assets\DB::getInstance();
    
    // Check if bookings table exists
    $result = $db->query("SHOW TABLES LIKE 'bookings'");
    if ($result && $result->num_rows > 0) {
        echo "Bookings table exists.<br>";
        
        // Check if tenant_id column exists
        $result = $db->query("SHOW COLUMNS FROM bookings LIKE 'tenant_id'");
        if ($result && $result->num_rows > 0) {
            echo "tenant_id column exists.<br>";
        } else {
            echo "Adding tenant_id column...<br>";
            
            // Add tenant_id column
            $sql = "ALTER TABLE bookings ADD COLUMN tenant_id INT NOT NULL AFTER id";
            
            if ($db->query($sql)) {
                echo "tenant_id column added successfully.<br>";
            } else {
                echo "Error adding tenant_id column.<br>";
            }
        }
    } else {
        echo "Creating bookings table...<br>";
        
        // Create bookings table
        $sql = "CREATE TABLE bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            property_id INT NOT NULL,
            room_id INT,
            check_in_date DATE NOT NULL,
            check_out_date DATE NOT NULL,
            status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
            total_amount DECIMAL(10,2) NOT NULL,
            payment_status ENUM('pending', 'paid', 'refunded') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
        )";
        
        if ($db->query($sql)) {
            echo "Bookings table created successfully.<br>";
        } else {
            echo "Error creating bookings table.<br>";
        }
    }
    
    // Show table structure
    $result = $db->query("DESCRIBE bookings");
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 