<?php
// Start session
session_start();

// Include required files
require_once __DIR__ . '/includes/init.php';

// Handle logout first
if (isset($_POST['logout'])) {
    error_log("Logout requested");
    $login->logout();
    error_log("Logout completed, redirecting");
    // Redirect to prevent POST resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Debug information
echo "<h2>Debug Information</h2>";

// Check database connection
echo "<h3>Database Connection</h3>";
try {
    $sql = "SELECT * FROM users LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database connection successful.<br>";
    echo "Sample user data: <pre>" . print_r($user, true) . "</pre>";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

// Check login object
echo "<h3>Login Object</h3>";
if (isset($login)) {
    echo "Login object is initialized.<br>";
    echo "Login object class: " . get_class($login) . "<br>";
    echo "Is user logged in? " . ($login->isLoggedIn() ? "Yes" : "No") . "<br>";
} else {
    echo "Login object is not initialized!";
}

// Check session
echo "<h3>Session Data</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Name: " . session_name() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "Session Cookie Parameters: <pre>" . print_r(session_get_cookie_params(), true) . "</pre>";
echo "Session Data: <pre>" . print_r($_SESSION, true) . "</pre>";

// Test login functionality
echo "<h3>Test Login</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:<br>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    if (isset($_POST['test-login'])) {
        $result = $login->login();
        echo "Login result:<br>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        
        if (isset($result['success']) && $result['success']) {
            echo "<div style='color: green;'>Login successful! Redirecting to: " . $result['redirect'] . "</div>";
            echo "<script>setTimeout(function() { window.location.href = '" . $result['redirect'] . "'; }, 2000);</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { padding: 5px; width: 200px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h2>Test Login Form</h2>
    <?php if ($login->isLoggedIn()): ?>
        <div style="margin-bottom: 20px;">
            <p>Currently logged in as: <?php echo htmlspecialchars($_SESSION['email']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</p>
            <form method="POST" onsubmit="return confirm('Are you sure you want to logout?');">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST">
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