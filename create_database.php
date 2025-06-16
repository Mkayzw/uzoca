<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

echo "Starting database creation script...<br>";

try {
    // First, try to connect without specifying a database
    echo "Attempting to connect to MySQL...<br>";
    $conn = new PDO("mysql:host=localhost", "root", "");
    echo "Connected to MySQL successfully<br>";
    
    // Set error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "PDO error mode set successfully<br>";
    
    // Check MySQL version
    $version = $conn->query('select version()')->fetchColumn();
    echo "MySQL Version: " . $version . "<br>";
    
    // Check if database exists
    echo "Checking if database exists...<br>";
    $stmt = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'uzoca'");
    $exists = $stmt->fetchColumn();
    
    if (!$exists) {
        echo "Database 'uzoca' does not exist. Creating it...<br>";
        $conn->exec("CREATE DATABASE uzoca");
        echo "Database created successfully<br>";
    } else {
        echo "Database 'uzoca' already exists<br>";
    }
    
    // Try to select the database
    echo "Attempting to select database...<br>";
    $conn->exec("USE uzoca");
    echo "Database selected successfully<br>";
    
    // Create properties table
    echo "Creating properties table...<br>";
    $conn->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            location VARCHAR(255) NOT NULL,
            bedrooms INT NOT NULL,
            bathrooms INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            capacity INT NOT NULL DEFAULT 1,
            status ENUM('available', 'unavailable', 'maintenance') NOT NULL DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Properties table created successfully<br>";
    
    // Create property_landlords table
    echo "Creating property_landlords table...<br>";
    $conn->exec("
        CREATE TABLE IF NOT EXISTS property_landlords (
        id INT PRIMARY KEY AUTO_INCREMENT,
            property_id INT NOT NULL,
            user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_property_landlord (property_id, user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Property_landlords table created successfully<br>";
    
    echo "<br>All operations completed successfully!<br>";
    
} catch(PDOException $e) {
    echo "<br>Database Error:<br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "Code: " . $e->getCode() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    
    // Log the error
    error_log("Database Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
} catch(Exception $e) {
    echo "<br>General Error:<br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "Code: " . $e->getCode() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    
    // Log the error
    error_log("General Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Display PHP and MySQL information
echo "<br>System Information:<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO Extension: " . (extension_loaded('pdo') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "MySQL Extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not Loaded') . "<br>";

// Check if we can write to the error log
$test_log = error_log("Test error log entry", 3, __DIR__ . '/php_errors.log');
echo "Error log test: " . ($test_log ? 'Success' : 'Failed') . "<br>";
?> 