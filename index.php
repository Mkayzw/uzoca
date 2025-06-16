<?php
require_once('./vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once('./includes/init.php');

// Check if user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    error_log("User logged in, redirecting to appropriate dashboard");
    switch($_SESSION['role']) {
        case 'landlord':
            header("Location: /uzoca/landlord/dashboard.php");
            exit();
            break;
        case 'agent':
            header("Location: /uzoca/agent/index.php");
            exit();
            break;
        case 'admin':
            header("Location: /uzoca/admin/index.php");
            exit();
            break;
        case 'tenant':
            header("Location: /uzoca/tenant/dashboard.php");
            exit();
            break;
    }
}

// Set the page title
$pageTitle = "UZOCA | Home";

// Render the view
view("index", ["title" => $pageTitle]);
