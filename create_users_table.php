<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

echo "Starting users table creation script...<br>";

try {
    // Connect to MySQL
    echo "Attempting to connect to MySQL...<br>";
    $conn = new PDO("mysql:host=localhost", "root", "");
    echo "Connected to MySQL successfully<br>";
    
    // Set error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS uzoca");
    echo "Database 'uzoca' created or already exists<br>";
    
    // Select the database
    $conn->exec("USE uzoca");
    echo "Selected database 'uzoca'<br>";
    
    // Create users table
    echo "Creating users table...<br>";
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'landlord', 'tenant', 'agent') NOT NULL,
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Users table created successfully<br>";
    
    echo "<br>All operations completed successfully!<br>";
    
} catch(PDOException $e) {
    echo "<br>Database Error:<br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "Code: " . $e->getCode() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    
    // Log the error
    error_log("Database Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Display PHP and MySQL information
echo "<br>System Information:<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO Extension: " . (extension_loaded('pdo') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not Loaded') . "<br>";
?> 