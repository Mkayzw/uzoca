<?php
require_once '../lib/src/LandlordDashboard.php';

header('Content-Type: application/json');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

$dashboard = new \app\src\LandlordDashboard();

// Check if the booking exists and belongs to the landlord
$bookings = $dashboard->getPendingBookings();
$bookingExists = false;

foreach ($bookings as $booking) {
    if ($booking['id'] == $data['booking_id']) {
        $bookingExists = true;
        break;
    }
}

if (!$bookingExists) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or unauthorized']);
    exit;
}

// Check room availability
$propertyId = $bookings[0]['property_id'];
if (!$dashboard->checkRoomAvailability($propertyId)) {
    echo json_encode(['success' => false, 'message' => 'No rooms available in this property']);
    exit;
}

// Approve the booking
if ($dashboard->approveBooking($data['booking_id'])) {
    // Update similar listings if needed
    $dashboard->updateSimilarListings($propertyId);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to approve booking']);
} 