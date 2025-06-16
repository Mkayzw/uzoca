<?php $pageTitle = "UZOCA | Landlord Approval"; ?>
<?php require_once("./includes/Header.php"); ?>
<?php

use app\src\LandlordApproval;

$landlordApproval = new LandlordApproval();
?>

<div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap -mb-4">
    <h3 class="header text-xl">
        Landlord Approval & Room Management
    </h3>
</div>

<div class="space-y-8">
    <!-- Pending Landlord Approvals -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
            <h2 class="text-2xl header">
                Pending Landlord Approvals
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Name</th>
                        <th class="pb-3 font-semibold">Email</th>
                        <th class="pb-3 font-semibold">Phone</th>
                        <th class="pb-3 font-semibold">Date Applied</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $landlordApproval->showPendingLandlords(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Room Management -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
            <h2 class="text-2xl header">
                Room Management
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Property</th>
                        <th class="pb-3 font-semibold">Room Number</th>
                        <th class="pb-3 font-semibold">Capacity</th>
                        <th class="pb-3 font-semibold">Occupied</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $landlordApproval->showRoomManagement(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approved Tenants -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
            <h2 class="text-2xl header">
                Approved Tenants
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="pb-3 font-semibold">Tenant Name</th>
                        <th class="pb-3 font-semibold">Property</th>
                        <th class="pb-3 font-semibold">Room Number</th>
                        <th class="pb-3 font-semibold">Date Approved</th>
                        <th class="pb-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $landlordApproval->showApprovedTenants(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once("./includes/Footer.php"); ?> 