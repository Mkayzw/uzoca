<?php
// Set session cookie parameters before starting the session
$session_lifetime = 86400; // 24 hours
$session_path = '/';
$session_domain = '';
$session_secure = false;
$session_httponly = true;

session_set_cookie_params(
    $session_lifetime,
    $session_path,
    $session_domain,
    $session_secure,
    $session_httponly
);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/init.php';

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

// Check if $login object is initialized
if (!isset($login)) {
    error_log("Login object not initialized!");
    $error = "System error: Login service not available";
} else {
    error_log("Login object initialized successfully");
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    error_log("POST data: " . print_r($_POST, true));
    
    if (isset($_POST['login-submit'])) {
        error_log("Login submit button clicked");
        
        $result = $login->login();
        error_log("Login result: " . print_r($result, true));
        
        if (is_array($result) && isset($result['success']) && $result['success']) {
            error_log("Login successful - Redirecting to: " . $result['redirect']);
            // Ensure session is written before redirect
            session_write_close();
            header("Location: " . $result['redirect']);
            exit();
        } else {
            $error = isset($result['error']) ? $result['error'] : 'An error occurred during login';
            error_log("Login failed - Error: " . $error);
        }
    } else {
        error_log("Login submit button not found in POST data");
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
    <link rel="stylesheet" href="/uzoca/assets/fonts/fonts.min.css">
    <link rel="stylesheet" href="/uzoca/assets/icons/uicons-regular-rounded/css/uicons-regular-rounded.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            padding: 10px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
        .submit-btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background: #0056b3;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .home-button:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .home-button i {
            font-size: 24px;
            color: #007bff;
        }
        .dark .home-button {
            background: #1f2937;
        }
        .dark .home-button i {
            color: #60a5fa;
        }
    </style>
</head>
<body class="dark:bg-slate-900">
    <a href="/uzoca/index.php" class="home-button" title="Go to Home">
        <i class="fr fi-rr-home"></i>
    </a>
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
            <div class="login-container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="loginForm">
                    <div class="form-group">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Type</label>
                        <select name="role" id="role" required class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                            <option value="">Select User Type</option>
                            <option value="landlord">Landlord</option>
                            <option value="agent">Agent</option>
                            <option value="admin">Admin</option>
                            <option value="tenant">Tenant</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email address</label>
                        <input id="email" name="email" type="email" required class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="form-group">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input id="password" name="password" type="password" required class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="login-submit" value="1" class="bg-sky-500 hover:bg-sky-600 focus:bg-sky-600 py-2 w-full text-white rounded-lg dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:bg-sky-700 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800">
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
        </div>
    </main>
</body>
</html>