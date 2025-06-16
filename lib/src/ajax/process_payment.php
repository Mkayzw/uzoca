<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../BookingPayment.php';

use app\config\Database;
use app\src\BookingPayment;

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['booking_id']) || !isset($_POST['amount']) || !isset($_POST['payment_method'])) {
        throw new Exception('Missing required fields');
    }

    $bookingId = $_POST['booking_id'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];
    $referenceNumber = isset($_POST['reference_number']) ? $_POST['reference_number'] : uniqid('PAY-');

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Initialize BookingPayment
    $bookingPayment = new BookingPayment($conn);

    // Process payment and create tenant
    $success = $bookingPayment->processPayment($bookingId, $amount, $paymentMethod, $referenceNumber);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment processed successfully and tenant created',
            'reference_number' => $referenceNumber
        ]);
    } else {
        throw new Exception('Failed to process payment');
    }
} catch (Exception $e) {
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 