<?php
session_start();
error_log("Landlord dashboard accessed");
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    error_log("Unauthorized access attempt to landlord dashboard");
    header("Location: /uzoca/login.php");
    exit();
}

$pageTitle = "UZOCA | Landlord Dashboard";
require_once("./includes/Header.php");

use app\src\ViewProperties;
use app\src\ViewTenants;
use app\src\ViewTransactionHistory;
use app\src\LandlordDashboard;
use app\assets\DB;

$DB = DB::getInstance();
$viewProperties = new ViewProperties($DB);
$viewTenants = new ViewTenants($DB);
$viewTransactionHistory = new ViewTransactionHistory($DB);
$landlordDashboard = new LandlordDashboard();
$monthlyIncome = $landlordDashboard->getMonthlyIncome();
?>

<div class="rounded-xl p-4 lg:p-8 lg:gap-8 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
    <h3 class="header text-2xl text-rose-500 dark:text-rose-400">
        <i class="fr fi-rr-megaphone relative top-1.5"></i>
        Welcome to Your Dashboard
    </h3>

    <div class="space-y-2.5">
        <p>
            Dear <?= htmlspecialchars($_SESSION['user'] ?? '') ?>, manage your property listings and view booking details from this dashboard.
        </p>

        <div class="flex gap-4">
            <a class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" href="/uzoca/landlord/properties/add.php">
                Add New Listing
            </a>
            <a class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" href="/uzoca/landlord/settings.php">
                Settings
            </a>
        </div>
    </div>
</div>

<div class="rounded-xl grid gap-8 p-4 lg:p-8 lg:gap-8 md:grid-cols-12 bg-white dark:bg-slate-900 dark:text-slate-100">
    <div class="md:col-span-8 overflow-x-auto">
        <h2 class="header text-2xl">
            Room Occupancy Overview
        </h2>

        <canvas class="!h-[200px] !md:h-[inherit]" id="myAreaChart"></canvas>
    </div>

    <div class="md:col-span-4 flex flex-col gap-4 sm:grid sm:grid-cols-2 md:flex">
        <div class="rounded-xl bg-white shadow flex gap-4 items-center py-8 px-4 dark:bg-slate-800 dark:text-slate-100">
            <span class="rounded-full inline-block bg-green-100 text-green-500 py-1.5 px-2.5">
                <i class="fr fi-rr-home"></i>
            </span>

            <div class="-space-y-1">
                <p class="text-sm text-slate-500 dark:text-slate-400">Active Listings</p>
                <p class="text-xl font-semibold">0</p>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow flex gap-4 items-center py-8 px-4 dark:bg-slate-800 dark:text-slate-100">
            <span class="rounded-full inline-block bg-amber-100 text-amber-500 py-1.5 px-2.5">
                <i class="fr fi-rr-users"></i>
            </span>

            <div class="-space-y-1">
                <p class="text-sm text-slate-500 dark:text-slate-400">Occupied Rooms</p>
                <p class="text-xl font-semibold">0</p>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow flex gap-4 items-center py-8 px-4 dark:bg-slate-800 dark:text-slate-100">
            <span class="rounded-full inline-block bg-blue-100 text-blue-500 py-1.5 px-2.5">
                <i class="fr fi-rr-dollar"></i>
            </span>

            <div class="-space-y-1">
                <p class="text-sm text-slate-500 dark:text-slate-400">Monthly Income</p>
                <p class="text-xl font-semibold">$<?= number_format($monthlyIncome, 2) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
    <!-- Active Listings -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
            <h2 class="text-2xl header">
                Active Listings
            </h2>

            <a class="text-sky-500 hover:text-sky-600 focus:text-sky-600 dark:text-sky-600 dark:hover:text-sky-700" href="/uzoca/landlord/properties.php">
                See All
            </a>
        </div>

        <div class="overflow-x-auto">
            <?php $viewProperties->showProperties(); ?>
        </div>
    </div>

    <!-- Room Bookings -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
            <h2 class="text-2xl header">
                Recent Bookings
            </h2>

            <a class="text-sky-500 hover:text-sky-600 focus:text-sky-600 dark:text-sky-600 dark:hover:text-sky-700" href="/uzoca/landlord/bookings.php">
                See All
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Room</th>
                        <th class="pb-3 font-semibold">Tenant Name</th>
                        <th class="pb-3 font-semibold">Contact</th>
                        <th class="pb-3 font-semibold">Booking Date</th>
                        <th class="pb-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $viewTenants->showTenants(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
    <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
        <h2 class="text-2xl header">
            Payment History
        </h2>

        <a class="text-sky-500 hover:text-sky-600 focus:text-sky-600 dark:text-sky-600 dark:hover:text-sky-700" href="/uzoca/landlord/transaction-history.php">
            See All
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                    <th class="pb-3 font-semibold">Date</th>
                    <th class="pb-3 font-semibold">Tenant</th>
                    <th class="pb-3 font-semibold">Room</th>
                    <th class="pb-3 font-semibold">Amount</th>
                    <th class="pb-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $viewTransactionHistory->showTransactionHistory(); ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../assets/js/chart.min.js" defer="true"></script>
<?php require_once("./includes/Footer.php"); ?> 