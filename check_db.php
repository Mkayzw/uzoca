<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("includes/init.php");

use app\assets\DB;

try {
    $db = DB::getInstance();
    
    // Check if tables exist
    $tables = [
        'users',
        'properties',
        'rooms',
        'bookings',
        'payments',
        'subscriptions',
        'plans'
    ];
    
    echo "<h1>Database Structure Check</h1>";
    
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<h2>Table: $table</h2>";
            
            // Get table structure
            $structure = $db->query("DESCRIBE $table");
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($row = $structure->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<h2>Table: $table</h2>";
            echo "<p style='color: red;'>Table does not exist!</p>";
        }
    }

    // Check if users table exists
    $result = $db->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        echo "<h2>Users table</h2>";
        echo "<p style='color: red;'>Users table does not exist. Creating it...</p>";
        
        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'agent', 'landlord') NOT NULL DEFAULT 'landlord',
            phone VARCHAR(20),
            profile_pic VARCHAR(255) DEFAULT 'profile-pic.jpg',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if ($db->query($sql)) {
            echo "<p style='color: green;'>Users table created successfully</p>";
        } else {
            echo "<p style='color: red;'>Error creating users table: " . $db->error . "</p>";
        }
    } else {
        echo "<h2>Users table</h2>";
        echo "<p style='color: green;'>Users table exists. Checking structure...</p>";
        
        // Check table structure
        $result = $db->query("DESCRIBE users");
        echo "<p>Table structure:</p>";
        while ($row = $result->fetch_assoc()) {
            echo htmlspecialchars($row['Field']) . " - " . htmlspecialchars($row['Type']) . "<br>";
        }
    }

    // Check if there are any users
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<p>Number of users in database: " . htmlspecialchars($row['count']) . "</p>";
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 