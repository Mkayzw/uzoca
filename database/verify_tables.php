<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    echo "<h2>Database Table Verification</h2>";
    
    // Check properties table
    $stmt = $conn->query("SHOW TABLES LIKE 'properties'");
    if ($stmt->rowCount() > 0) {
        echo "<h3>Properties table exists!</h3>";
        $stmt = $conn->query("DESCRIBE properties");
        echo "<h4>Properties table columns:</h4>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . $row['Field'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3 style='color: red;'>Properties table does not exist!</h3>";
    }
    
    // Check property_images table
    $stmt = $conn->query("SHOW TABLES LIKE 'property_images'");
    if ($stmt->rowCount() > 0) {
        echo "<h3>Property images table exists!</h3>";
        $stmt = $conn->query("DESCRIBE property_images");
        echo "<h4>Property images table columns:</h4>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . $row['Field'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3 style='color: red;'>Property images table does not exist!</h3>";
    }
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>Error checking tables: " . $e->getMessage() . "</h3>";
} 