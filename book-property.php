<?php
require_once 'includes/init.php';
require_once 'lib/src/BookingPayment.php';

use app\src\BookingPayment;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if property ID is provided
if (!isset($_GET['id'])) {
    header('Location: properties.php');
    exit();
}

$propertyId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];
$isAgentBooking = isset($_GET['agent']) && $_GET['agent'] === 'true';

$bookingPayment = new BookingPayment();
$error = null;
$paymentData = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $paymentMethod = $_POST['payment_method'] ?? '';
        
        if (!in_array($paymentMethod, ['ecocash', 'mukuru', 'innbucks'])) {
            throw new \Exception('Invalid payment method');
        }

        $paymentData = $bookingPayment->createBookingPayment($propertyId, $userId, $paymentMethod, $isAgentBooking);
        
        if (!$paymentData) {
            throw new \Exception('Failed to create booking payment');
        }
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = "Book Property";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Book Property</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($paymentData): ?>
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Amount to Pay</p>
                        <p class="text-2xl font-bold text-sky-600 dark:text-sky-400">
                            $<?= number_format($paymentData['amount'], 2) ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Payment Method</p>
                        <p class="font-medium"><?= ucfirst($paymentData['payment_method']) ?></p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Reference Number</p>
                        <p class="font-medium"><?= $paymentData['reference'] ?></p>
                    </div>

                    <?php if ($paymentData['is_agent_booking']): ?>
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <p class="text-sm text-green-700 dark:text-green-300">
                                Booking through agent - Full payment will be processed to the agent's account
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Payment will be processed to the admin's <?= ucfirst($paymentData['payment_method']) ?> account
                            </p>
                            <?php if (isset($paymentData['admin_account'])): ?>
                                <div class="mt-2 text-sm">
                                    <p class="text-blue-600 dark:text-blue-400">
                                        Account Name: <?= htmlspecialchars($paymentData['admin_account']['account_name']) ?>
                                    </p>
                                    <p class="text-blue-600 dark:text-blue-400">
                                        Account Number: <?= htmlspecialchars($paymentData['admin_account']['account_number']) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-center my-6">
                        <img src="<?= $paymentData['qr_code'] ?>" 
                             alt="PayNow QR Code" 
                             class="w-48 h-48"
                             title="Scan to pay">
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
                            Scan the QR code to complete your payment
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Your booking will be confirmed once payment is received
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Select Payment Method</h2>
                
                <form method="POST" class="space-y-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Payment Method
                            </label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <input type="radio" name="payment_method" value="ecocash" class="sr-only" required>
                                    <div class="flex items-center justify-center w-full">
                                        <span class="text-sm font-medium">EcoCash</span>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <input type="radio" name="payment_method" value="mukuru" class="sr-only" required>
                                    <div class="flex items-center justify-center w-full">
                                        <span class="text-sm font-medium">Mukuru</span>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <input type="radio" name="payment_method" value="innbucks" class="sr-only" required>
                                    <div class="flex items-center justify-center w-full">
                                        <span class="text-sm font-medium">InnBucks</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <?php if ($isAgentBooking): ?>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    Booking through agent - Full payment will be processed to the agent's account
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Payment will be processed to the admin's account
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 