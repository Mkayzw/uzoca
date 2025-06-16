<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

echo "Starting database connection test...<br>";

try {
    // Test basic PDO connection
    echo "Attempting to connect to database...<br>";
    $conn = new PDO("mysql:host=localhost;dbname=uzoca", "root", "");
    echo "PDO connection created successfully<br>";
    
    // Test PDO attributes
    echo "Setting PDO attributes...<br>";
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "PDO attributes set successfully<br>";
    
    // Test simple query
    echo "Testing simple query...<br>";
    $result = $conn->query("SELECT 1");
    echo "Simple query successful<br>";
    
    // Test database version
    echo "Database version: " . $conn->getAttribute(PDO::ATTR_SERVER_VERSION) . "<br>";
    
    // Test if we can access the database
    echo "Testing database access...<br>";
    $result = $conn->query("SHOW DATABASES");
    echo "Available databases:<br>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Database'] . "<br>";
    }
    
} catch(PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "<br>";
    echo "Error Code: " . $e->getCode() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
} catch(Exception $e) {
    echo "General Error: " . $e->getMessage() . "<br>";
    echo "Error Code: " . $e->getCode() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// Test PHP version and extensions
echo "<br>PHP Information:<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO Extension: " . (extension_loaded('pdo') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "MySQL Extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not Loaded') . "<br>";

// Check if we can write to the error log
$test_log = error_log("Test error log entry", 3, __DIR__ . '/php_errors.log');
echo "Error log test: " . ($test_log ? 'Success' : 'Failed') . "<br>";
?> 