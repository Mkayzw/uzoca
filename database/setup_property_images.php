<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    // Create property_images table
    $sql = "CREATE TABLE IF NOT EXISTS property_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        property_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
    )";
    
    $conn->exec($sql);
    echo "Property images table created successfully";
    
} catch(PDOException $e) {
    echo "Error creating property images table: " . $e->getMessage();
} 