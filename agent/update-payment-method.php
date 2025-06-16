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
$reference = $data['reference'] ?? '';
$method = $data['method'] ?? '';

if (empty($reference) || empty($method)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

// Validate payment method
$validMethods = ['ecocash', 'mukuru', 'innbucks'];
if (!in_array($method, $validMethods)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid payment method']);
    exit();
}

// Update payment method in database
$query = "UPDATE agent_payments SET payment_method = ? WHERE transaction_id = ? AND agent_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $method, $reference, $_SESSION['user_id']);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to update payment method']);
} 