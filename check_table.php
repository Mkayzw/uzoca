<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

use app\assets\DB;

try {
    require_once("vendor/autoload.php");
    $db = DB::getInstance();

    // Check if users table exists
    $result = $db->query("SHOW TABLES LIKE 'users'");
    if (!$result || $result->num_rows === 0) {
        echo "The 'users' table does not exist in the database.<br>";
        exit;
    }

    // Get table structure
    $result = $db->query("DESCRIBE users");
    if (!$result) {
        echo "Failed to get table structure.<br>";
        exit;
    }

    echo "<h3>Current 'users' table structure:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check if required columns exist
    $requiredColumns = ['id', 'name', 'email', 'password', 'role', 'phone'];
    $existingColumns = [];
    
    $result = $db->query("SHOW COLUMNS FROM users");
    while ($row = $result->fetch_assoc()) {
        $existingColumns[] = $row['Field'];
    }

    echo "<h3>Missing columns:</h3>";
    $missingColumns = array_diff($requiredColumns, $existingColumns);
    if (empty($missingColumns)) {
        echo "All required columns are present.<br>";
    } else {
        echo "The following required columns are missing:<br>";
        foreach ($missingColumns as $column) {
            echo "- " . htmlspecialchars($column) . "<br>";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
} 