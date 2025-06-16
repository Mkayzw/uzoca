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
    
    // Check if bookings table exists
    $result = $db->query("SHOW TABLES LIKE 'bookings'");
    if ($result && $result->num_rows > 0) {
        echo "Bookings table exists.<br>";
        
        // Check if room_id column exists
        $result = $db->query("SHOW COLUMNS FROM bookings LIKE 'room_id'");
        if ($result && $result->num_rows > 0) {
            echo "room_id column exists.<br>";
        } else {
            echo "Adding room_id column...<br>";
            
            // Add room_id column
            $sql = "ALTER TABLE bookings ADD COLUMN room_id INT NULL AFTER property_id, ADD FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL";
            
            if ($db->query($sql)) {
                echo "room_id column added successfully.<br>";
            } else {
                echo "Error adding room_id column.<br>";
            }
        }
    } else {
        echo "Creating bookings table...<br>";
        
        // Create bookings table
        $sql = "CREATE TABLE bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            room_id INT NULL,
            tenant_id INT NOT NULL,
            status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
            check_in_date DATE NOT NULL,
            check_out_date DATE NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            agent_fee DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
            FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        
        if ($db->query($sql)) {
            echo "Bookings table created successfully.<br>";
        } else {
            echo "Error creating bookings table.<br>";
        }
    }
    
    // Show table structures
    echo "<h3>Rooms Table Structure:</h3>";
    $result = $db->query("DESCRIBE rooms");
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
    
    echo "<h3>Bookings Table Structure:</h3>";
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