<?php
// This script is for testing purposes only and should be removed from a production environment

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Replace these placeholders with actual admin user details from your database
$admin_user_id = 1; // Example: Replace with the actual ID of an admin user
$admin_email = 'kidmatrixx01@gmail.com'; // Example: Replace with the actual email of an admin user
$admin_name = 'Admin User'; // Example: Replace with the actual name of an admin user
$admin_role = 'admin';

// Set the necessary session variables to simulate a successful admin login
$_SESSION['user_id'] = $admin_user_id;
$_SESSION['email'] = $admin_email;
$_SESSION['user'] = $admin_name; // Corrected session variable name to match admin header
$_SESSION['role'] = $admin_role;
$_SESSION['loggedUser'] = strtolower($admin_name . $admin_user_id); // Assuming this format is used elsewhere
$_SESSION['id'] = $admin_user_id; // Ensure 'id' is also set if checked by UserProfile

echo "Simulating admin login... Redirecting to admin dashboard.";

// Redirect to the admin dashboard
header('Location: /uzoca/admin/');
exit();
?> 