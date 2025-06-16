<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

use app\assets\DB;

try {
    // Debug: Check if autoloader is working
    echo "Checking autoloader...<br>";
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        throw new Exception("vendor/autoload.php not found. Please run 'composer dump-autoload'");
    }

    // Debug: Check if DB class exists and show its methods
    echo "Checking DB class...<br>";
    if (!class_exists('app\assets\DB')) {
        throw new Exception("DB class not found. Make sure the namespace and class name are correct.");
    }
    
    // Debug: Show available methods
    $methods = get_class_methods('app\assets\DB');
    echo "Available methods in DB class: " . implode(', ', $methods) . "<br>";

    // Connect to database
    $db = DB::getInstance();

    // Check if users table exists
    try {
        $result = $db->query("SHOW TABLES LIKE 'users'");
        if ($result->num_rows === 0) {
            // Create users table
            $createTable = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
                phone VARCHAR(20),
                profile_pic VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $db->query($createTable);
            echo "Users table created successfully.<br>";
        }
    } catch (Exception $e) {
        throw new Exception("Failed to check/create users table: " . $e->getMessage());
    }

    // Admin credentials
    $adminEmail = 'admin@uzoca.com';
    $adminPassword = 'admin123';
    $adminName = 'Admin User';
    $adminPhone = '1234567890';

    // Hash the password
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

    // Check if admin exists
    try {
        // First, check if admin exists
        $checkQuery = "SELECT id FROM users WHERE email = ? AND role = 'admin'";
        try {
            $result = $db->prepare($checkQuery, "s", $adminEmail);
            $adminExists = $result->num_rows > 0;
        } catch (Exception $e) {
            throw new Exception("Failed to check admin existence: " . $e->getMessage());
        }

        if ($adminExists) {
            // Update existing admin
            $updateQuery = "UPDATE users SET password = ?, phone = ? WHERE email = ? AND role = 'admin'";
            try {
                $db->prepare($updateQuery, "sss", $hashedPassword, $adminPhone, $adminEmail);
                echo "Admin account updated successfully!<br>";
            } catch (Exception $e) {
                throw new Exception("Failed to update admin: " . $e->getMessage());
            }
        } else {
            // Create new admin
            $insertQuery = "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, 'admin', ?)";
            try {
                $db->prepare($insertQuery, "ssss", $adminName, $adminEmail, $hashedPassword, $adminPhone);
                echo "Admin account created successfully!<br>";
            } catch (Exception $e) {
                throw new Exception("Failed to create admin: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        throw new Exception("Failed to create/update admin account: " . $e->getMessage());
    }

    echo "Login details:<br>";
    echo "Email: " . htmlspecialchars($adminEmail) . "<br>";
    echo "Password: " . htmlspecialchars($adminPassword) . "<br>";
    echo "<a href='login.php'>Go to Login Page</a>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Debug Information:<br>";
    echo "Database Connection: " . (isset($db) ? "Established" : "Failed") . "<br>";
    if (isset($db)) {
        echo "Last Query: " . (isset($updateQuery) ? $updateQuery : (isset($insertQuery) ? $insertQuery : "None")) . "<br>";
        echo "Parameters: " . (isset($params) ? print_r($params, true) : "None") . "<br>";
    }
} 