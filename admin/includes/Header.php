<?php

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once(realpath(__DIR__ . '/../../vendor') . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(realpath(__DIR__ . '/../../includes/init.php'));
require_once __DIR__ . '/../../lib/src/Notification.php';

use app\src\UserProfile;
use app\src\Notification;

$userDetails = new UserProfile();
$notification = new Notification();
$unreadCount = 0;
$notifications = [];

if (isset($_SESSION['user_id'])) {
    $unreadCount = $notification->getUnreadCount($_SESSION['user_id']);
    $notifications = $notification->getForUser($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : "UZOCA | Admin Dashboard" ?></title>
    <link rel="icon" href="../assets/img/logo-light.png">

    <!-- Preload stylesheets and JavaScript files -->
    <link rel="preload" href="<?= realpath('../assets/css/style.css') ?>" as="style">
    <link rel="preload" href="../assets/fonts/fonts.min.css" as="style">
    <link rel="preload" href="../assets/icons/uicons-brands/css/uicons-brands.min.css" as="style">
    <link rel="preload" href="../assets/icons/uicons-regular-rounded/css/uicons-regular-rounded.min.css" as="style">
    <link rel="preload" href="../assets/js/main..min.js" as="script">
    <link rel="preload" href="../assets/js/chart.min.js" as="script">

    <!-- Important stylesheets -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/fonts/fonts.min.css">
    <link rel="stylesheet" href="../assets/icons/uicons-brands/css/uicons-brands.min.css">
    <link rel="stylesheet" href="../assets/icons/uicons-regular-rounded/css/uicons-regular-rounded.min.css">
    <script src="../assets/js/notifications.js" defer></script>
</head>

<body>

    <div class="lg:grid lg:grid-cols-12 min-h-screen">
        <header class="lg:flex-col lg:drop-shadow-none lg:col-span-3 lg:border-r lg:border-r-slate-200 lg:dark:border-r-slate-700 lg:p-0">
            <a class="lg:hidden lg:not-sr-only" href="../admin/">
                <img class="w-20 lg:w-24 logo" src="../assets/img/logo.png" alt="UZOCA" width="100" height="100">
            </a>

            <nav class="scale-0 lg:scale-100 lg:w-full lg:sticky lg:top-0 lg:bottom-full lg:min-h-screen lg:space-y-2 lg:pt-2.5 z-50">
                <a class="hidden not-sr-only lg:block" href="../admin/" aria-label="UZOCA logo">
                    <img class="w-20 lg:w-24 logo lg:ml-4 lg:mb-3.5 hidden not-sr-only lg:block" src="../assets/img/logo.png" alt="UZOCA" width="100" height="100">
                </a>

                <ul class="flex flex-col gap-1">
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold block border-l-4 border-transparent" href="../admin/">
                            <i class="fr fi-rr-apps pr-1.5"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold block border-l-4 border-transparent" href="../admin/properties.php">
                            <i class="fr fi-rr-home pr-1.5"></i>
                            Properties
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold block border-l-4 border-transparent" href="../admin/tenants.php">
                            <i class="fr fi-rr-users pr-1.5"></i>
                            Tenants
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold block border-l-4 border-transparent" href="../admin/payment-history.php">
                            <i class="fr fi-rr-money pr-1.5"></i>
                            Payment History
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold block border-l-4 border-transparent" href="../admin/settings.php">
                            <i class="fr fi-rr-settings pr-1.5"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold block border-l-4 border-transparent" href="../admin/logout.php" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fr fi-rr-exit pr-1.5"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="flex items-center gap-4 lg:hidden">
                <a class="text-xl relative before:content-['1'] before:absolute before:text-xs before:bg-rose-600 before:text-white before:rounded-full before:py-[0.1rem] before:px-1.5 before:z-50 before:-top-[30%] before:-right-[50%] hidden" href="./messages.php" aria-label="Messages" title="View messages">
                    <i class="fr fi-rr-envelope"></i>
                </a>

                <button class="mode-toggle text-xl" type="button" aria-label="Theme toggle button">
                    <i class="fr fi-rr-moon"></i>
                </button>

                <button class="menu-toggle text-xl lg:hidden" type="button" aria-label="Mobile menu toggle button">
                    <i class="fr fi-rr-apps"></i>
                </button>
            </div>
        </header>

        <main class="lg:col-span-9 bg-slate-100 dark:bg-slate-800 dark:text-slate-100">
            <div class="flex items-center justify-between gap-x-8 gap-y-8 flex-wrap p-4 lg:px-[2.5%] lg:py-2.5 lg:sticky lg:top-0 bg-white dark:bg-slate-900 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 z-[1024]">
                <div>
                    <h4 class="header text-lg">
                        Welcome back, <?= htmlspecialchars($_SESSION['user'] ?? '') ?> 👋
                    </h4>
                    <p class="dark:text-slate-300">
                        Here is an overview of your dashboard
                    </p>
                </div>

                <div class="flex items-center flex-wrap gap-x-6 gap-y-2">
                    <button class="mode-toggle hidden not-sr-only lg:block text-xl" type="button" aria-label="Theme toggle button">
                        <i class="fr fi-rr-moon"></i>
                    </button>

                    <a class="hidden text-xl relative before:content-['1'] before:absolute before:text-xs before:bg-rose-600 before:text-white before:rounded-full before:py-[0.1rem] before:px-1.5 before:z-50 before:-top-[30%] before:-right-[50%]" href="./messages.php" aria-label="Messages" title="View messages">
                        <i class="fr fi-rr-envelope"></i>
                    </a>

                    <div class="relative">
                        <button id="notificationButton" class="relative p-2 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200">
                            <i class="fr fi-rr-bell text-xl"></i>
                            <?php if($unreadCount > 0): ?>
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full notification-count">
                                <?php echo $unreadCount; ?>
                            </span>
                            <?php endif; ?>
                        </button>
                        
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg dark:bg-slate-800 z-50">
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-200">Notifications</h3>
                                    <button id="markAllRead" class="text-sm text-sky-500 hover:text-sky-600">Mark all as read</button>
                                </div>
                                <div id="notificationList" class="max-h-96 overflow-y-auto">
                                    <?php if(empty($notifications)): ?>
                                    <p class="text-slate-500 dark:text-slate-400 text-center py-4">No new notifications</p>
                                    <?php else: ?>
                                    <?php foreach($notifications as $notification): ?>
                                    <div class="notification-item p-3 border-b border-slate-200 dark:border-slate-700 last:border-0 <?= $notification['is_read'] ? '' : 'bg-slate-50 dark:bg-slate-700' ?>">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-slate-900 dark:text-slate-200"><?= htmlspecialchars($notification['title']) ?></h4>
                                                <p class="text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($notification['message']) ?></p>
                                            </div>
                                            <span class="text-xs text-slate-500 dark:text-slate-400"><?= date('M d, Y', strtotime($notification['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-2 gap-y-2">
                        <?php 
                        $user = $userDetails->getUserDetails() ? $userDetails->getUserDetails()->fetch_object() : null;
                        if ($user && isset($user->profile_pic)): ?>
                            <img class="rounded-full w-10 h-10" src="../admin/assets/img/<?= htmlspecialchars($user->profile_pic) ?>" alt="<?= htmlspecialchars($user->name) ?>" width="40" height="40" />
                        <?php else: ?>
                            <div class="rounded-full w-10 h-10 bg-slate-200 flex items-center justify-center"><i class="fr fi-rr-user text-slate-500"></i></div>
                        <?php endif; ?>
                        <span class="-space-y-1">
                            <h4 class="header">
                                <?= htmlspecialchars($_SESSION['user'] ?? '') ?>
                            </h4>
                            <p class="text-green-500 tracking-wider dark:text-green-400">
                                Online
                            </p>
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-4 space-y-12 lg:px-[2.5%] py-8 relative">