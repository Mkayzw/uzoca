<?php
// Health check endpoint for Render
header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'app' => 'UZOCA',
    'version' => '1.0.0'
];

// Test database connection
try {
    require_once 'config/database.php';
    
    if ($conn->connect_error) {
        $health['status'] = 'error';
        $health['database'] = 'connection_failed';
        $health['error'] = $conn->connect_error;
    } else {
        $health['database'] = 'connected';
        $conn->close();
    }
} catch (Exception $e) {
    $health['status'] = 'error';
    $health['database'] = 'error';
    $health['error'] = $e->getMessage();
}

// Set appropriate HTTP status code
http_response_code($health['status'] === 'ok' ? 200 : 503);

echo json_encode($health, JSON_PRETTY_PRINT);
?>
