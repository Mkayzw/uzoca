<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/init.php';

// Debug logging
error_log("Login page accessed");
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    error_log("User already logged in. Redirecting to appropriate dashboard.");
    switch($_SESSION['role']) {
        case 'landlord':
            header("Location: /uzoca/landlord/dashboard.php");
            exit();
        case 'agent':
            header("Location: /uzoca/agent/index.php");
            exit();
        case 'admin':
            header("Location: /uzoca/admin/index.php");
            exit();
        case 'tenant':
            header("Location: /uzoca/tenant/dashboard.php");
            exit();
    }
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Login form submitted");
    error_log("POST data: " . print_r($_POST, true));
    
    // Validate required fields
    if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        error_log("Missing required fields");
        $error = "All fields are required";
    } else {
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize_input($_POST['role']);
        
        error_log("Login attempt - Email: " . $email . ", Role: " . $role);
        
        try {
            if ($login->authenticate($email, $password, $role)) {
                error_log("Login successful - Redirecting to dashboard");
                error_log("Session after successful login: " . print_r($_SESSION, true));

                // --- DEBUG START ---
                error_log("DEBUG: Authentication successful!");
                error_log("DEBUG: Authenticated Role is: " . $role);
                error_log("DEBUG: Session User ID is: " . ($_SESSION['user_id'] ?? 'Not Set'));
                error_log("DEBUG: Session Role is: " . ($_SESSION['role'] ?? 'Not Set'));
                // --- DEBUG END ---

                // Redirect based on user role
                switch($role) {
                    case 'landlord':
                        error_log("Redirecting to landlord dashboard");
                        header("Location: /uzoca/landlord/dashboard.php");
                        exit();
                    case 'agent':
                        error_log("Redirecting to agent dashboard");
                        header("Location: /uzoca/agent/index.php");
                        exit();
                    case 'admin':
                        error_log("Redirecting to admin dashboard");
                        header("Location: /uzoca/admin/index.php");
                        exit();
                    case 'tenant':
                        error_log("Redirecting to tenant dashboard");
                        header("Location: /uzoca/tenant/dashboard.php");
                        exit();
                    default:
                        error_log("Invalid role specified: " . $role);
                        $error = "Invalid user role";
                }
            } else {
                error_log("Login failed - Invalid credentials");
                $error = "Invalid email or password";
            }
        } catch (Exception $e) {
            error_log("Exception during login: " . $e->getMessage());
            $error = "An error occurred during login. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UZOCA | Login</title>
    <link rel="stylesheet" href="/uzoca/assets/css/style.css">
</head>
<body class="dark:bg-slate-900">
    <main class="grid place-items-center min-h-screen w-full py-8 px-4 dark:bg-slate-900 dark:text-slate-400 lg:px-[20rem]">
        <div class="bg-slate-100 py-8 px-4 w-full rounded-xl dark:bg-slate-800">
        <div class="text-center mx-auto w-[90%] mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">
                    Sign in to your account
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Or
                    <a href="/uzoca/register.php" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
                        create a new account
                    </a>
                </p>
            </div>
            <form method="POST" action="/uzoca/login.php" id="loginForm">
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
                <div class="grid gap-y-3 gap-x-4">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Type</label>
                        <select name="role" id="role" required class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    <option value="">Select User Type</option>
                            <option value="landlord">Landlord</option>
                            <option value="agent">Agent</option>
                    <option value="admin">Admin</option>
                            <option value="tenant">Tenant</option>
                </select>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email address</label>
                        <input id="email" name="email" type="email" required class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input id="password" name="password" type="password" required class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                </div>
                <div>
                    <button type="submit" name="login-submit" class="bg-sky-500 hover:bg-sky-600 focus:bg-sky-600 py-2 w-full text-white rounded-lg dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:bg-sky-700 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800">
                Login
            </button>
        </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Forgot your password?
                    <a href="/uzoca/forgot-password.php" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
                        Reset it
                </a>
            </p>
            </div>
        </div>
</main>
</body>
</html>