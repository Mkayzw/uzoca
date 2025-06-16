<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    echo "<h2>Database Connection Test</h2>";
    
    // Test connection
    if ($conn) {
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        
        // Test users table
        $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Number of users in database: " . $result['count'] . "</p>";
        
        // Show table structure
        echo "<h3>Users Table Structure:</h3>";
        $stmt = $conn->query("DESCRIBE users");
        echo "<pre>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
        
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
} 