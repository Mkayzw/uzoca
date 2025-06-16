<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

// Get user details
use app\src\UserProfile;
try {
    $userProfile = new UserProfile();
    $user = $userProfile->getUserProfile();
} catch (\Exception $e) {
    error_log("Error getting user details: " . $e->getMessage());
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'UZOCA | Agent Dashboard'; ?></title>
    <link rel="stylesheet" href="/uzoca/assets/css/style.css">
    <link rel="stylesheet" href="/uzoca/assets/css/flaticon.css">
    <link rel="stylesheet" href="/uzoca/assets/icons/uicons-brands/css/uicons-brands.min.css">
    <link rel="stylesheet" href="/uzoca/assets/icons/uicons-regular-rounded/css/uicons-regular-rounded.min.css">
    <script src="/uzoca/assets/js/script.js" defer="true"></script>
</head>
<body class="bg-slate-100 dark:bg-slate-900 overflow-x-hidden">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 transform -translate-x-full transition-transform duration-200 ease-in-out lg:translate-x-0" id="sidebar">
            <div class="h-full flex flex-col">
                <div class="px-6 py-6">
                    <a href="/uzoca/agent" class="flex items-center mb-8">
                        <img src="/uzoca/assets/images/logo.png" class="h-8 me-3" alt="UZOCA Logo" />
                        <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">UZOCA</span>
                    </a>
                </div>
                <div class="flex-1 px-4">
                    <ul class="space-y-3 font-medium">
                        <li>
                            <a href="/uzoca/agent" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-home w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Dashboard</span>
                    </a>
                </li>
                <li>
                            <a href="/uzoca/agent/properties" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-house w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Properties</span>
                    </a>
                </li>
                <li>
                            <a href="/uzoca/agent/bookings" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-document-signed w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Bookings</span>
                    </a>
                </li>
                <li>
                            <a href="/uzoca/agent/subscription.php" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-credit-card w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Subscription</span>
                    </a>
                </li>
                        <li>
                            <a href="/uzoca/agent/profile.php" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-user w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Profile</span>
                    </a>
                </li>
                <li>
                            <a href="/uzoca/agent/settings.php" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-settings w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Settings</span>
                    </a>
                </li>
                        <li>
                            <a href="/uzoca/logout.php" class="flex items-center p-3 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group">
                                <i class="fr fi-rr-sign-out w-6 h-6 text-slate-500 transition duration-75 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white"></i>
                                <span class="ms-4 text-lg">Sign out</span>
                    </a>
                </li>
            </ul>
                </div>
                </div>
    </aside>

        <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen lg:w-[calc(100%-16rem)] bg-slate-50 dark:bg-slate-900 px-4 pt-0 pb-4 lg:px-6 lg:pt-0 lg:pb-6">
                <?php if (isset($pageTitle)): ?>
                <div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap mb-4">
                    <h3 class="header text-xl">
                        <?php echo htmlspecialchars($pageTitle); ?>
                    </h3>
                </div>
                <?php endif; ?>

                <!-- Your page content will be inserted here -->
                <div class="space-y-6">
                    <!-- Content goes here, starting from the top -->
            