<?php
// Environment configuration checker
// This file helps debug environment variables on Render

echo "<h1>UZOCA Environment Configuration</h1>";

echo "<h2>Environment Variables</h2>";
echo "<pre>";
$env_vars = ['DB_HOST', 'DB_USER', 'DB_NAME', 'DB_PORT', 'DATABASE_URL'];
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    // Mask sensitive data
    if (in_array($var, ['DB_PASS', 'DATABASE_URL']) && $value !== 'NOT SET') {
        $value = '[MASKED]';
    }
    echo "$var: $value\n";
}
echo "</pre>";

echo "<h2>PHP Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Current Working Directory: " . getcwd() . "\n";
echo "</pre>";

echo "<h2>Required PHP Extensions</h2>";
echo "<pre>";
$required_extensions = ['mysqli', 'pdo', 'json', 'mbstring', 'curl'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? 'LOADED' : 'MISSING';
    echo "$ext: $status\n";
}
echo "</pre>";

echo "<h2>Database Connection Test</h2>";
echo "<pre>";
try {
    require_once 'config/database.php';
    
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error . "\n";
    } else {
        echo "Database connection: SUCCESS\n";
        echo "Server info: " . $conn->server_info . "\n";
        $conn->close();
    }
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}
echo "</pre>";

echo "<h2>File System Check</h2>";
echo "<pre>";
$important_files = [
    'composer.json',
    'config/database.php',
    'includes/init.php',
    'vendor/autoload.php'
];

foreach ($important_files as $file) {
    $status = file_exists($file) ? 'EXISTS' : 'MISSING';
    echo "$file: $status\n";
}
echo "</pre>";

// Only show this in development
if (isset($_GET['debug'])) {
    echo "<h2>All Environment Variables</h2>";
    echo "<pre>";
    foreach ($_ENV as $key => $value) {
        // Mask sensitive data
        if (strpos(strtolower($key), 'pass') !== false || 
            strpos(strtolower($key), 'secret') !== false ||
            strpos(strtolower($key), 'key') !== false) {
            $value = '[MASKED]';
        }
        echo "$key: $value\n";
    }
    echo "</pre>";
}
?>
