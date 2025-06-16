<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';

try {
    // Get the absolute path to the SQL file
    $sqlFile = __DIR__ . '/landlord_dashboard.sql';
    
    // Check if the SQL file exists
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: " . $sqlFile);
    }
    
    // Read the SQL file
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("Failed to read SQL file");
    }
    
    echo "<h2>Starting database update...</h2>";
    
    // Split the SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                echo "<p>Executing: " . substr($statement, 0, 100) . "...</p>";
                if ($conn->query($statement)) {
                    echo "<p style='color: green;'>✓ Success</p>";
                } else {
                    throw new Exception($conn->error);
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
                error_log("Error executing statement: " . $e->getMessage());
                error_log("Statement: " . $statement);
            }
        }
    }
    
    echo "<h2 style='color: green;'>Landlord dashboard tables updated successfully!</h2>";
} catch(Exception $e) {
    echo "<h2 style='color: red;'>Error updating landlord dashboard tables:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?> 