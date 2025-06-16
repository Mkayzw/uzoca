<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../lib/config/Database.php';

try {
    $database = new \app\config\Database();
    $conn = $database->getConnection();

    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/add_image_column.sql');
    
    if ($conn->multi_query($sql)) {
        echo "Image column added successfully to properties table.";
    } else {
        throw new Exception("Error executing SQL: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 