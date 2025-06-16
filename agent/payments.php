<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../includes/init.php");

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/lib/pages/login.php");
    exit();
}

$pageTitle = "UZOCA | Payment History";
require_once("includes/Header.php");

use app\src\AgentDashboard;

try {
    $agentDashboard = new AgentDashboard();
} catch (\Exception $e) {
    error_log("Error in payment history: " . $e->getMessage());
}
?>

<div class="px-4 space-y-12 lg:px-[2.5%] py-8 relative">
    <div class="rounded-xl p-4 lg:p-8 lg:gap-8 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
        <div class="flex justify-between items-center">
            <h3 class="header text-2xl text-rose-500 dark:text-rose-400">
                <i class="fr fi-rr-credit-card relative top-1.5"></i>
                Payment History
            </h3>
            <a href="/uzoca/agent" class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800">
                <i class="fr fi-rr-arrow-left relative top-1.5"></i>
                Back to Dashboard
            </a>
        </div>

        <div class="space-y-2.5">
            <p>
                View your complete payment history and transaction details.
            </p>
        </div>
    </div>

    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Date</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Amount</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $agentDashboard->showPaymentHistory(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once("includes/Footer.php"); ?> 