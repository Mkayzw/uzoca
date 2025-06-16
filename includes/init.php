<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Check if required files exist
$required_files = [
    BASE_PATH . '/vendor/autoload.php',
    BASE_PATH . '/app/config/Database.php',
    BASE_PATH . '/lib/functions.php',
    BASE_PATH . '/lib/src/Register.php',
    BASE_PATH . '/lib/src/Login.php',
    BASE_PATH . '/lib/src/AgentPayment.php',
    BASE_PATH . '/app/src/AgentDashboard.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Required file not found: $file");
    }
}

// Include Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Include database connection
require_once BASE_PATH . '/app/config/Database.php';

// Initialize database connection
try {
    $database = new app\config\Database();
    $conn = $database->getConnection();
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function get_user_role() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Function to redirect user
function redirect($url) {
    header("Location: $url");
    exit();
}

// Include required files
require_once BASE_PATH . "/lib/functions.php";
require_once BASE_PATH . "/lib/src/Register.php";
require_once BASE_PATH . "/lib/src/Login.php";
require_once BASE_PATH . "/lib/src/AgentPayment.php";
require_once BASE_PATH . "/app/src/AgentDashboard.php";

// Initialize instances
try {
    $login = new \app\src\Login($conn);
    $agentPayment = new \app\src\AgentPayment($conn);
} catch(Exception $e) {
    error_log("Error initializing classes: " . $e->getMessage());
    die("Error initializing required classes. Please try again later.");
}
?> 