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

// Function to safely output debug information
function debug_output($label, $data) {
    echo "<div class='debug-section'>";
    echo "<h3>" . htmlspecialchars($label) . "</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    echo "</div>";
}

// Debug information
echo "<h2>Debug Information</h2>";

// Test database connection and user query
echo "<h3>Database Connection Test</h3>";
try {
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', 'test@example.com');
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Database connection successful.<br>";
    echo "Test user found: " . ($user ? 'Yes' : 'No') . "<br>";
    
    if ($user) {
        debug_output("User details", $user);
    }
} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['test-login'])) {
        echo "<h3>Login Test Results</h3>";
        try {
            // Debug POST data
            debug_output("POST data received", $_POST);
            
            // Ensure login object is initialized
            if (!isset($login)) {
                throw new Exception("Login object not initialized");
            }
            
            $result = $login->login();
            debug_output("Login result", $result);
            
            if (isset($result['success']) && $result['success']) {
                debug_output("Session data after login", $_SESSION);
                
                // Ensure session is written before redirect
                session_write_close();
                
                // Redirect after successful login
                header("Location: " . $result['redirect']);
                exit();
            }
        } catch (Exception $e) {
            echo "Login error: " . htmlspecialchars($e->getMessage()) . "<br>";
        }
    } elseif (isset($_POST['logout'])) {
        if (isset($login)) {
            $login->logout();
        }
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Check session
echo "<h3>Current Session Data</h3>";
echo "Session ID: " . htmlspecialchars(session_id()) . "<br>";
echo "Session Name: " . htmlspecialchars(session_name()) . "<br>";
echo "Session Status: " . session_status() . "<br>";
debug_output("Session Cookie Parameters", session_get_cookie_params());
debug_output("Session Data", $_SESSION);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { padding: 5px; width: 200px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; background: #f8f9fa; }
        .debug-section pre { margin: 0; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h2>Test Login Form</h2>
    <?php if ($isLoggedIn): ?>
        <div style="margin-bottom: 20px;">
            <p>Currently logged in as: <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?> (<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>)</p>
            <form method="POST">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="">Select Role</option>
                    <option value="landlord">Landlord</option>
                    <option value="agent">Agent</option>
                    <option value="admin">Admin</option>
                    <option value="tenant">Tenant</option>
                </select>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" name="test-login">Test Login</button>
        </form>
    <?php endif; ?>
</body>
</html>
