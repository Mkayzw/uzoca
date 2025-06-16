<?php
require_once("../includes/init.php");

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/create_agent_subscriptions.sql');
    $conn->exec($sql);
    
    echo "Tables created successfully";
} catch(PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
} 