<?php
require_once(realpath(__DIR__ . '/vendor') . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(realpath(__DIR__ . '/includes/init.php'));

use app\assets\DB;

$db = DB::getInstance();

// Get the table structure
$result = $db->query("DESCRIBE properties");
if ($result) {
    echo "<h2>Properties Table Structure:</h2>";
    echo "<pre>";
    while ($row = $result->fetch_object()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error getting table structure: " . $db->con->error;
}

// Get a sample row to see the actual column names
$result = $db->query("SELECT * FROM properties LIMIT 1");
if ($result) {
    echo "<h2>Sample Property Data:</h2>";
    echo "<pre>";
    while ($row = $result->fetch_object()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error getting sample data: " . $db->con->error;
} 