<?php
require_once __DIR__ . '/init.php';

if(isset($_POST['login-submit'])) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $userType = sanitize_input($_POST['userType']);

    // Validate inputs
    if(empty($email) || empty($password) || empty($userType)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: /uzoca/login.php");
        exit();
    }

    // Check user in database based on user type
    $table = 'users';  // All users are in the users table
    $role = $userType; // Use the userType directly as role

    // First check if user exists with the email
    $sql = "SELECT * FROM $table WHERE email = ?";
    $result = $conn->prepare($sql, "s", $email);

    if($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Then check if the role matches
        if($user['role'] !== $role) {
            $_SESSION['error'] = "Invalid user type for this email";
            header("Location: /uzoca/login.php");
            exit();
        }
        
        if(password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on user type
            switch($userType) {
                case 'admin':
                    header("Location: /uzoca/admin/index.php");
                    break;
                case 'agent':
                    header("Location: /uzoca/agent/index.php");
                    break;
                case 'landlord':
                    header("Location: /uzoca/landlord/dashboard.php");
                    break;
                default:
                    header("Location: /uzoca/index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password";
        }
    } else {
        $_SESSION['error'] = "No account found with that email";
    }

    header("Location: /uzoca/login.php");
    exit();
} else {
    header("Location: /uzoca/login.php");
    exit();
} 