<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../src/Register.php';
require_once __DIR__ . '/../functions.php';

use app\src\Register;

$register = new Register();
$message = $register->registerUser();

// If registration was successful and we have a redirect, don't show the form
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirectUrl = '';
    switch($_SESSION['role']) {
        case 'agent':
            $redirectUrl = '/uzoca/agent/subscription.php';
            break;
        case 'landlord':
            $redirectUrl = '/uzoca/landlord/dashboard.php';
            break;
        case 'admin':
            $redirectUrl = '/uzoca/admin/index.php';
            break;
        default:
            $redirectUrl = '/uzoca/login.php';
    }
    header("Location: $redirectUrl");
    exit();
}

// If we have a success message, redirect immediately to login
if (strpos($message, 'successful') !== false) {
    header("Location: /uzoca/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UZOCA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add favicon -->
    <link rel="icon" type="image/x-icon" href="/uzoca/assets/images/favicon.ico">
    <script>
        // Check for saved theme preference or use system preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    Create your account
                </h2>
                <button id="theme-toggle" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                    <!-- Sun icon -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-yellow-300 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon icon -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-gray-200 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
            </div>

            <?php if ($message): ?>
            <div class="rounded-md p-4 <?php echo strpos($message, 'successful') !== false ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900'; ?>">
                <p class="text-sm <?php echo strpos($message, 'successful') !== false ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'; ?>">
                    <?php echo $message; ?>
                </p>
            </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="userType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Type</label>
                        <select name="userType" id="userType" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select User Type</option>
                            <option value="landlord" <?php echo isset($_POST['userType']) && $_POST['userType'] === 'landlord' ? 'selected' : ''; ?>>Landlord</option>
                            <option value="agent" <?php echo isset($_POST['userType']) && $_POST['userType'] === 'agent' ? 'selected' : ''; ?>>Agent</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                        <input id="name" name="name" type="text" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                        <input id="phone" name="phone" type="tel" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email address</label>
                        <input id="email" name="email" type="email" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input id="password" name="password" type="password" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <input id="password_confirm" name="password-confirm" type="password" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <button type="submit" name="register-submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Register
                    </button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                Already have an account?
                    <a href="/uzoca/login.php" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Sign in
                </a>
            </p>
            </div>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        
        themeToggle.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });
    </script>
</body>

</html>