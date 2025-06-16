<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../includes/init.php");

// Debug logging
error_log("Agent dashboard accessed");
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
    error_log("Unauthorized access attempt to agent dashboard");
    header("Location: /uzoca/lib/pages/login.php");
    exit();
}

$pageTitle = "UZOCA | Agent Dashboard";
require_once("includes/Header.php");

use app\src\AgentDashboard;
use app\src\UserProfile;

try {
    $agentDashboard = new AgentDashboard();
    $userProfile = new UserProfile();
    $user = $userProfile->getUserProfile();
} catch (\Exception $e) {
    error_log("Error in agent dashboard: " . $e->getMessage());
    $user = null;
}

// Get user name safely
$userName = $user && isset($user->name) ? $user->name : 'Agent';
?>

    <!-- Profile Overview -->
<div class="rounded-xl p-4 lg:p-6 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
    <div class="flex flex-col items-center text-center gap-4">
        <div class="flex items-center gap-4">
                <img src="/uzoca/assets/images/<?php echo $user->profile_pic ?? 'default.png'; ?>" 
                     alt="Profile Picture" 
                 class="w-16 h-16 lg:w-20 lg:h-20 rounded-full object-cover border-4 border-slate-200 dark:border-slate-700">
                <div class="text-center">
                <h1 class="text-xl lg:text-2xl font-bold">Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
                <p class="text-sm lg:text-base text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($user->email ?? ''); ?></p>
                </div>
            </div>
            <a href="/uzoca/agent/profile.php" 
           class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors">
                <i class="fr fi-rr-user mr-2"></i>
                View Profile
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
<div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white p-4 rounded-xl dark:bg-slate-900 dark:text-slate-100 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-sky-100 rounded-full dark:bg-sky-900">
                <i class="fr fi-rr-home text-xl text-sky-500"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Properties</p>
                <p class="text-xl font-bold text-sky-500"><?php echo $agentDashboard->getTotalProperties(); ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl dark:bg-slate-900 dark:text-slate-100 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-green-100 rounded-full dark:bg-green-900">
                <i class="fr fi-rr-document-signed text-xl text-green-500"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Bookings</p>
                <p class="text-xl font-bold text-green-500"><?php echo $agentDashboard->getTotalBookings(); ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl dark:bg-slate-900 dark:text-slate-100 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-amber-100 rounded-full dark:bg-amber-900">
                <i class="fr fi-rr-time-past text-xl text-amber-500"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Pending Bookings</p>
                <p class="text-xl font-bold text-amber-500"><?php echo $agentDashboard->getPendingBookingsCount(); ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl dark:bg-slate-900 dark:text-slate-100 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="p-3 bg-rose-100 rounded-full dark:bg-rose-900">
                <i class="fr fi-rr-credit-card text-xl text-rose-500"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Monthly Earnings</p>
                <p class="text-xl font-bold text-rose-500">$<?php echo number_format($agentDashboard->getMonthlyEarnings(), 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Status -->
<div class="rounded-xl p-4 lg:p-6 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
        <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Subscription Status</h2>
            <a href="/uzoca/agent/subscription.php" class="text-sky-500 hover:text-sky-600 inline-flex items-center">
                <i class="fr fi-rr-credit-card mr-2"></i>
                Manage Subscription
            </a>
        </div>
        <?php $agentDashboard->showSubscriptionStatus(); ?>
    </div>

    <!-- Properties Section -->
<div class="rounded-xl p-4 lg:p-6 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
        <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-rose-500 dark:text-rose-400">
            <i class="fr fi-rr-home relative top-1"></i>
            Properties
        </h3>
        <div class="flex gap-2">
            <a class="inline-flex items-center px-3 py-1.5 text-sm text-white bg-sky-500 rounded-lg hover:bg-sky-600 transition-colors" href="/uzoca/agent/properties.php">
                <i class="fr fi-rr-eye mr-1.5"></i>
                    View Properties
                </a>
            <a class="inline-flex items-center px-3 py-1.5 text-sm text-white bg-sky-500 rounded-lg hover:bg-sky-600 transition-colors" href="/uzoca/agent/properties/add.php">
                <i class="fr fi-rr-plus mr-1.5"></i>
                    Add Property
                </a>
            </div>
        </div>
    </div>

    <!-- Bookings Section -->
<div class="rounded-xl p-4 lg:p-6 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
        <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-rose-500 dark:text-rose-400">
            <i class="fr fi-rr-document-signed relative top-1"></i>
            Bookings
        </h3>
        <a class="inline-flex items-center px-3 py-1.5 text-sm text-white bg-sky-500 rounded-lg hover:bg-sky-600 transition-colors" href="/uzoca/agent/bookings.php">
            <i class="fr fi-rr-eye mr-1.5"></i>
                    View Bookings
                </a>
    </div>
</div>

    <!-- Monthly Earnings Chart -->
<div class="rounded-xl grid gap-4 p-4 lg:p-6 md:grid-cols-12 bg-white dark:bg-slate-900 dark:text-slate-100">
    <div class="md:col-span-8 overflow-x-auto">
        <h2 class="text-xl font-semibold mb-4">Monthly Earnings</h2>
        <canvas class="!h-[250px] !md:h-[300px]" id="myAreaChart"></canvas>
        </div>
        <div class="md:col-span-4 flex flex-col gap-4">
            <!-- These stats are duplicates of Quick Stats, consider removing or refactoring -->
    </div>
</div>

    <!-- Pending Bookings and Payment History -->
<div class="grid gap-4 grid-cols-1 lg:grid-cols-2">
    <!-- Pending Bookings -->
    <div class="bg-white p-4 lg:p-6 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-4">
            <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Pending Bookings</h2>
            <a class="text-sky-500 hover:text-sky-600 inline-flex items-center text-sm" href="/uzoca/agent/bookings.php?status=pending">
                <i class="fr fi-rr-eye mr-1.5"></i>
                See All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-2 font-semibold">Tenant Name</th>
                        <th class="pb-2 font-semibold">Property</th>
                        <th class="pb-2 font-semibold">Room</th>
                        <th class="pb-2 font-semibold">Date Booked</th>
                        <th class="pb-2 font-semibold">Agent Fee</th>
                        <th class="pb-2 font-semibold">Status</th>
                        <th class="pb-2 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $agentDashboard->showPendingBookings(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Payment History -->
    <div class="bg-white p-4 lg:p-6 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-4">
            <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Payment History</h2>
            <a class="text-sky-500 hover:text-sky-600 inline-flex items-center text-sm" href="/uzoca/agent/payments.php">
                <i class="fr fi-rr-eye mr-1.5"></i>
                See All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-2 font-semibold">Date</th>
                        <th class="pb-2 font-semibold">Type</th>
                        <th class="pb-2 font-semibold">Amount</th>
                        <th class="pb-2 font-semibold">Status</th>
                        <th class="pb-2 font-semibold">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $agentDashboard->showPaymentHistory(); ?>
                </tbody>
            </table>
    </div>
    </div>
</div>

<?php // require_once("../includes/Footer.php"); ?>

<script src="../assets/js/chart.min.js" defer="true"></script>
<script src="../assets/js/chart-config.js" defer="true"></script>
<script src="../assets/js/flowbite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('myAreaChart');
    if (!ctx) return;

    // Get the current month and previous 5 months
    const months = [];
    const currentDate = new Date();
    for (let i = 5; i >= 0; i--) {
        const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
        months.push(date.toLocaleString('default', { month: 'short' }));
    }

    // Get monthly earnings data from PHP
    const monthlyEarnings = <?php echo json_encode($agentDashboard->getMonthlyEarnings()); ?>;

    // Create the chart
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Earnings',
                data: monthlyEarnings,
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                borderColor: 'rgb(14, 165, 233)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return '$${context.raw.toFixed(2)}';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>