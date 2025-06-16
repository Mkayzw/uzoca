<?php
require_once("includes/Header.php");
require_once("../includes/init.php");

use app\src\AgentPayment;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

$agentPayment = new AgentPayment();

// Get payment reference
$reference = $_GET['reference'] ?? '';
if (empty($reference)) {
    header("Location: /uzoca/agent/subscription.php");
    exit();
}

// Get payment details
$payment = $agentPayment->getPaymentByReference($reference);
if (!$payment) {
    header("Location: /uzoca/agent/subscription.php");
    exit();
}

// Generate confirmation QR code
$confirmationData = [
    'reference' => $reference,
    'amount' => $payment['amount'],
    'date' => $payment['created_at'],
    'status' => 'paid'
];
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode(json_encode($confirmationData));
?>

<div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap -mb-4">
    <h3 class="header text-xl">
        Payment Confirmation
    </h3>
</div>

<div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fr fi-rr-check text-green-600 dark:text-green-400 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-semibold mb-2">Payment Successful!</h2>
            <p class="text-slate-600 dark:text-slate-400">
                Your subscription has been activated
            </p>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-lg mb-8">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Reference</span>
                    <span class="font-mono"><?php echo htmlspecialchars($payment['reference']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Amount</span>
                    <span class="font-medium">$<?php echo number_format($payment['amount'], 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Payment Method</span>
                    <span class="font-medium"><?php echo ucfirst($payment['payment_method']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Date</span>
                    <span><?php echo date('M j, Y H:i', strtotime($payment['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <div class="text-center mb-8">
            <img src="<?php echo $qrCodeUrl; ?>" alt="Payment Confirmation QR Code" class="mx-auto mb-4">
            <p class="text-sm text-slate-600 dark:text-slate-400">Scan this QR code to verify your payment</p>
        </div>

        <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fr fi-rr-envelope text-green-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Confirmation Email Sent</h3>
                    <p class="mt-2 text-sm text-green-700 dark:text-green-300">
                        A confirmation email has been sent to your registered email address with all the payment details.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/uzoca/agent/dashboard.php" class="inline-flex items-center justify-center px-6 py-2.5 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-300">
                <i class="fr fi-rr-home mr-2"></i>
                Go to Dashboard
            </a>
            <button onclick="window.print()" class="inline-flex items-center justify-center px-6 py-2.5 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-300">
                <i class="fr fi-rr-print mr-2"></i>
                Print Receipt
            </button>
        </div>
    </div>
</div>

<?php require_once("../includes/Footer.php"); ?> 