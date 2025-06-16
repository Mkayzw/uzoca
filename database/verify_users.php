<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    echo "<h2>Users Table Verification</h2>";
    
    // Check users table structure
    $stmt = $conn->query("DESCRIBE users");
    echo "<h3>Table Structure:</h3>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . "</li>";
    }
    echo "</ul>";
    
    // Check sample data (without passwords)
    $stmt = $conn->query("SELECT id, name, email, role, created_at FROM users");
    echo "<h3>Sample Users:</h3>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>ID: " . $row['id'] . ", Name: " . $row['name'] . ", Email: " . $row['email'] . ", Role: " . $row['role'] . "</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
} 