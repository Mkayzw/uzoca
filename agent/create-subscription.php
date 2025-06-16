<?php
require_once("../includes/init.php");

use app\src\AgentPayment;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reference']) || !isset($data['months'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

try {
    $agentPayment = new AgentPayment();
    
    // Get payment details
    $payment = $agentPayment->getPaymentByReference($data['reference']);
    if (!$payment) {
        throw new Exception('Payment not found');
    }

    // Create subscription
    $success = $agentPayment->createSubscription($payment['id'], $data['months']);

    if ($success) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to create subscription');
    }
} catch (Exception $e) {
    error_log("Error in create-subscription.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 