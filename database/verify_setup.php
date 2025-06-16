<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    echo "<h2>Database Setup Verification</h2>";
    
    // Check database connection
    echo "<h3>1. Database Connection</h3>";
    echo "<p style='color: green;'>✓ Connected to database successfully</p>";
    
    // Check users table
    echo "<h3>2. Users Table Structure</h3>";
    $stmt = $conn->query("DESCRIBE users");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check existing users
    echo "<h3>3. Existing Users</h3>";
    $stmt = $conn->query("SELECT id, name, email, role, created_at FROM users");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created At</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test user creation
    echo "<h3>4. Test User Creation</h3>";
    $testEmail = "test_" . time() . "@example.com";
    $testPassword = password_hash("test123", PASSWORD_DEFAULT);
    $testName = "Test User";
    $testRole = "landlord";
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())");
    $stmt->bindParam(':name', $testName);
    $stmt->bindParam(':email', $testEmail);
    $stmt->bindParam(':password', $testPassword);
    $stmt->bindParam(':role', $testRole);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✓ Test user created successfully</p>";
        echo "<p>Test user details:</p>";
        echo "<ul>";
        echo "<li>Email: " . $testEmail . "</li>";
        echo "<li>Password: test123</li>";
        echo "<li>Role: " . $testRole . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create test user</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
} 