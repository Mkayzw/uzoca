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

if (!isset($data['amount']) || !isset($data['method'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

try {
    $agentPayment = new AgentPayment();
    
    // Create payment record
    $payment = $agentPayment->createPayment(
        $data['amount'],
        $data['method'],
        "Subscription payment for {$data['months']} month(s)"
    );

    if ($payment) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'reference' => $payment['reference']
        ]);
    } else {
        throw new Exception('Failed to create payment record');
    }
} catch (Exception $e) {
    error_log("Error in create-payment.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 