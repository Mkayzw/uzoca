<?php
require_once("../includes/init.php");

use app\src\AgentPayment;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get payment reference
$reference = $_GET['reference'] ?? '';
if (empty($reference)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid reference']);
    exit();
}

try {
    $agentPayment = new AgentPayment();
    $payment = $agentPayment->getPaymentByReference($reference);

    if ($payment) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $payment['status']]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Payment not found']);
    }
} catch (Exception $e) {
    error_log("Error in check-payment.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
} 