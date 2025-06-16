<?php
require_once("../includes/init.php");

use app\src\AgentDashboard;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

$agentDashboard = new AgentDashboard();

// Get agent's bookings
$bookings = $agentDashboard->getAgentBookings($_SESSION['user_id']);

// Now include the header
require_once("includes/Header.php");
?>

<div class="rounded-xl p-4 lg:p-8 lg:gap-8 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
    <div class="flex justify-between items-center">
        <h3 class="header text-2xl text-rose-500 dark:text-rose-400">
            <i class="fr fi-rr-document-signed relative top-1.5"></i>
            Bookings
        </h3>
        <a href="/uzoca/agent" class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800">
            <i class="fr fi-rr-arrow-left relative top-1.5"></i>
            Back to Dashboard
        </a>
    </div>

    <div class="space-y-2.5">
        <p>
            Manage your bookings from this page.
        </p>
    </div>
</div>

<div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
    <?php if (empty($bookings)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fr fi-rr-calendar text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-medium mb-2">No Bookings Yet</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">
                You don't have any bookings for your properties yet
            </p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                    <?php echo htmlspecialchars($booking['property_title']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900 dark:text-slate-100">
                                    <?php echo htmlspecialchars($booking['client_name']); ?>
                                </div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    <?php echo htmlspecialchars($booking['client_email']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900 dark:text-slate-100">
                                    <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                <button onclick="viewBookingDetails(<?php echo $booking['id']; ?>)" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">
                                    <i class="fr fi-rr-eye mr-1"></i>
                                    View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function viewBookingDetails(id) {
    // Implement booking details view logic
    alert('Booking details view will be implemented soon.');
}
</script>

<?php require_once("../includes/Footer.php"); ?> 