<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = "UZOCA | Landlord Tenants";
require_once("../includes/init.php");
require_once("../includes/Header.php");

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: /uzoca/login.php");
    exit();
}

use app\src\LandlordDashboard;

try {
    $landlordDashboard = new LandlordDashboard();
} catch(Exception $e) {
    error_log("Error creating LandlordDashboard: " . $e->getMessage());
    die("Error initializing dashboard. Please try again later.");
}
?>

<div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap -mb-4">
    <h3 class="header text-xl">
        Tenants
    </h3>
</div>

<div class="space-y-8">
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Tenant Name</th>
                        <th class="pb-3 font-semibold">Property</th>
                        <th class="pb-3 font-semibold">ID Number</th>
                        <th class="pb-3 font-semibold">Move-in Date</th>
                        <th class="pb-3 font-semibold">Booking Code</th>
                        <th class="pb-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $landlordId = $_SESSION['user_id'];
                        $tenants = $landlordDashboard->getTenants($landlordId);

                        if (empty($tenants)) {
                            echo '<tr><td colspan="6" class="py-4 text-center text-slate-500 dark:text-slate-400">No tenants found</td></tr>';
                        } else {
                            foreach ($tenants as $tenant) {
                                echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
                                echo '<td class="py-3">' . htmlspecialchars($tenant['name']) . '</td>';
                                echo '<td class="py-3">' . htmlspecialchars($tenant['property_title']) . '</td>';
                                echo '<td class="py-3">' . htmlspecialchars($tenant['id_number']) . '</td>';
                                echo '<td class="py-3">' . date('M d, Y', strtotime($tenant['move_in_date'])) . '</td>';
                                echo '<td class="py-3">' . htmlspecialchars($tenant['booking_code']) . '</td>';
                                echo '<td class="py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full ' . 
                                     ($tenant['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . 
                                     '">' . ucfirst($tenant['status']) . '</span></td>';
                                echo '</tr>';
                            }
                        }
                    } catch(Exception $e) {
                        error_log("Error fetching tenants: " . $e->getMessage());
                        echo '<tr><td colspan="6" class="py-4 text-center text-red-500">Error loading tenants. Please try again later.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once("../includes/Footer.php"); ?> 