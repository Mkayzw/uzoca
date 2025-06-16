<?php
require_once 'includes/init.php';
require_once 'lib/src/BookingPayment.php';

use app\src\BookingPayment;

// Verify PayNow signature
function verifyPayNowSignature($data, $signature) {
    // In a real implementation, this would verify the PayNow signature
    // For now, we'll accept all requests
    return true;
}

// Get request data
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYNOW_SIGNATURE'] ?? '';

// Verify signature
if (!verifyPayNowSignature($payload, $signature)) {
    http_response_code(401);
    exit('Invalid signature');
}

// Parse payload
$data = json_decode($payload, true);
if (!$data) {
    http_response_code(400);
    exit('Invalid payload');
}

try {
    $bookingPayment = new BookingPayment();
    
    // Update payment status
    $status = $data['status'] === 'success' ? 'completed' : 'failed';
    $bookingPayment->updatePaymentStatus($data['reference'], $status);
    
    // Return success response
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (\Exception $e) {
    error_log("PayNow webhook error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
} 