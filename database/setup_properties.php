<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    // Drop the tables in correct order
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("DROP TABLE IF EXISTS property_images");
    $conn->exec("DROP TABLE IF EXISTS properties");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Create properties table
    $sql = "CREATE TABLE IF NOT EXISTS properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        location VARCHAR(255) NOT NULL,
        bedrooms INT NOT NULL,
        bathrooms INT NOT NULL,
        status ENUM('available', 'sold', 'rented') DEFAULT 'available',
        type ENUM('sale', 'rent') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "Properties table created successfully";
    
} catch(PDOException $e) {
    echo "Error creating properties table: " . $e->getMessage();
} 