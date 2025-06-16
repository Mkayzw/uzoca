<?php
require_once(realpath(__DIR__ . '/vendor') . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(realpath(__DIR__ . '/includes/init.php'));

use app\assets\DB;

$db = DB::getInstance();

// Add image column to properties table
$sql = "ALTER TABLE properties ADD COLUMN image VARCHAR(255) AFTER title";
$result = $db->query($sql);

if ($result) {
    echo "Successfully added image column to properties table";
} else {
    echo "Error adding image column: " . $db->con->error;
} 