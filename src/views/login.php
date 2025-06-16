<?php 
$pageTitle = "UZOCA | Login";
require_once(__DIR__ . "/../../includes/Header.php"); 
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8 p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white uppercase">
                Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-200">
                Or
                <a href="/uzoca/registration.php" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-300 dark:hover:text-sky-200">
                    create a new account
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="/uzoca/login.php">
            <div class="space-y-4">
                <div>
                    <label for="userType" class="block text-sm font-medium text-gray-700 dark:text-gray-200">User Type</label>
                    <select name="userType" id="userType" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="" class="text-gray-900 dark:text-white">Select User Type</option>
                        <option value="landlord" class="text-gray-900 dark:text-white">Landlord</option>
                        <option value="agent" class="text-gray-900 dark:text-white">Agent</option>
                    </select>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email address</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="Enter your email">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                    <input id="password" name="password" type="password" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="Enter your password">
                </div>
            </div>
            <div>
                <button type="submit" name="login-submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-gray-800">
                    Login
                </button>
            </div>
        </form>
        <div class="text-center mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-200">
                Forgot your password?
                <a href="/uzoca/forgot-password.php" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-300 dark:hover:text-sky-200">
                    Reset it
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../includes/Footer.php"); ?> 