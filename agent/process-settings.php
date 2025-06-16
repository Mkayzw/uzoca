<?php
require_once("../includes/init.php");

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

// Validate required fields
$requiredFields = ['ecocash_number', 'ecocash_name', 'mukuru_number', 'mukuru_name', 'innbucks_number', 'innbucks_name'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'All fields are required']);
        exit();
    }
}

try {
    // First check if settings exist
    $checkQuery = "SELECT id FROM agent_payment_settings WHERE agent_id = ?";
    $checkResult = $conn->prepare($checkQuery, "i", $_SESSION['user_id']);
    
    if ($checkResult->num_rows === 0) {
        // Insert new settings
        $insertQuery = "INSERT INTO agent_payment_settings 
                       (agent_id, ecocash_number, ecocash_name, mukuru_number, mukuru_name, innbucks_number, innbucks_name) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $result = $conn->prepare($insertQuery, "issssss", 
            $_SESSION['user_id'],
            $data['ecocash_number'],
            $data['ecocash_name'],
            $data['mukuru_number'],
            $data['mukuru_name'],
            $data['innbucks_number'],
            $data['innbucks_name']
        );
    } else {
        // Update existing settings
        $updateQuery = "UPDATE agent_payment_settings SET 
                       ecocash_number = ?, 
                       ecocash_name = ?, 
                       mukuru_number = ?, 
                       mukuru_name = ?, 
                       innbucks_number = ?, 
                       innbucks_name = ? 
                       WHERE agent_id = ?";
        $result = $conn->prepare($updateQuery, "ssssssi", 
            $data['ecocash_number'],
            $data['ecocash_name'],
            $data['mukuru_number'],
            $data['mukuru_name'],
            $data['innbucks_number'],
            $data['innbucks_name'],
            $_SESSION['user_id']
        );
    }

    if ($result) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to update payment settings');
    }
} catch (Exception $e) {
    error_log("Error in process-settings.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to update payment settings']);
} 