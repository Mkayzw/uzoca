<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure we can see the output
ob_start();

try {
    require_once __DIR__ . '/../lib/assets/Config.php';
    require_once __DIR__ . '/../lib/assets/DB.php';
    
    echo "Starting database check and fix...<br>";
    
    $db = \app\assets\DB::getInstance();
    
    echo "Database connection established.<br>";

    // Check if properties table exists
    $result = $db->query("SHOW TABLES LIKE 'properties'");
    if ($result->num_rows === 0) {
        echo "Properties table does not exist. Creating it...<br>";
        
        // Read the SQL file
        $sql = file_get_contents(__DIR__ . '/landlord_dashboard.sql');
        if ($sql === false) {
            throw new Exception("Could not read landlord_dashboard.sql file");
        }
        
        // Execute the SQL
        if ($db->query($sql)) {
            echo "Properties table created successfully.<br>";
        } else {
            throw new Exception("Error creating properties table");
        }
    } else {
        echo "Properties table exists. Checking for image column...<br>";
        
        // Check for image column
        $result = $db->query("SHOW COLUMNS FROM properties LIKE 'image'");
        if ($result->num_rows === 0) {
            echo "Image column does not exist. Adding it...<br>";
            
            // Add image column
            $sql = "ALTER TABLE properties ADD COLUMN image VARCHAR(255) DEFAULT NULL";
            if ($db->query($sql)) {
                echo "Image column added successfully.<br>";
            } else {
                throw new Exception("Error adding image column");
            }
        } else {
            echo "Image column already exists.<br>";
        }
    }

    // Set default images for properties without images
    echo "Setting default images for properties without images...<br>";
    $sql = "UPDATE properties SET image = 'default-property.jpg' WHERE image IS NULL";
    if ($db->query($sql)) {
        echo "Default images set successfully.<br>";
    } else {
        throw new Exception("Error setting default images");
    }

    echo "<br>Database structure check and fix completed successfully!";

} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}

// Flush the output buffer
ob_end_flush(); 