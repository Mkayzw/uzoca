<?php
require_once 'app/config/Database.php';
require_once 'lib/src/BookingPayment.php';

use app\config\Database;
use app\src\BookingPayment;

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get booking details
$bookingId = $_GET['booking_id'] ?? null;
if (!$bookingId) {
    header('Location: index.php');
    exit;
}

$bookingPayment = new BookingPayment($conn);
$booking = $bookingPayment->getBookingDetails($bookingId);

if (!$booking) {
    header('Location: index.php');
    exit;
}

$pageTitle = "Payment - " . $booking['property_title'];
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Complete Your Payment</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Booking Summary</h2>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Property:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($booking['property_title']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Check-in:</span>
                    <span class="font-medium"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Check-out:</span>
                    <span class="font-medium"><?php echo date('M d, Y', strtotime($booking['end_date'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-medium text-lg">₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>

        <form id="paymentForm" class="bg-white rounded-lg shadow-md p-6">
            <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">
            <input type="hidden" name="amount" value="<?php echo $booking['total_amount']; ?>">
            
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="radio" id="gcash" name="payment_method" value="gcash" class="h-4 w-4 text-blue-600" required>
                        <label for="gcash" class="ml-2 block text-sm text-gray-900">
                            GCash
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" class="h-4 w-4 text-blue-600">
                        <label for="bank_transfer" class="ml-2 block text-sm text-gray-900">
                            Bank Transfer
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Tenant Information</h2>
                <div class="space-y-4">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Pay Now
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/payment.js"></script>

<?php include 'includes/footer.php'; ?> 