<?php
session_start();

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    header("Location: /uzoca/login.php");
    exit();
}

$pageTitle = "UZOCA | Bookings";
require_once("./includes/Header.php");

use app\src\ViewTenants;
use app\assets\DB;

$DB = DB::getInstance();
$viewTenants = new ViewTenants($DB);
?>

<div class="space-y-8">
    <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
        <h3 class="header text-2xl">
            Bookings
        </h3>
    </div>

    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Room</th>
                        <th class="pb-3 font-semibold">Tenant Name</th>
                        <th class="pb-3 font-semibold">Contact</th>
                        <th class="pb-3 font-semibold">Booking Date</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $viewTenants->showTenants(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once("./includes/Footer.php"); ?> 