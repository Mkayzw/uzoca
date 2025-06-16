<?php
require_once __DIR__ . '/../app/config/Database.php';

try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
    
    // Drop the table if it exists
    $conn->exec("DROP TABLE IF EXISTS `agent_payments`");
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/agent_payments.sql');
    $conn->exec($sql);
    
    // Verify the table was created
    $stmt = $conn->query("DESCRIBE `agent_payments`");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('created_at', $columns)) {
        echo "Agent payments table created successfully with all required columns!\n";
        echo "Columns found: " . implode(", ", $columns);
    } else {
        echo "Error: Table was created but 'created_at' column is missing!";
    }
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
} 