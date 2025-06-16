<?php
require_once("../includes/init.php");

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/alter_properties.sql');
    $conn->exec($sql);
    
    echo "Tables altered successfully";
} catch(PDOException $e) {
    echo "Error altering tables: " . $e->getMessage();
} 