<?php

namespace app\src;

use PDO;
use PDOException;

class Register
{
    private $conn;
    private $name;
    private $phoneNumber;
    private $email;
    private $password;
    private $userType;
    private $errors = [];

    public function __construct()
    {
        $this->conn = $GLOBALS['conn'];
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Function to validate password strength
    private function isStrongPassword($password) {
        // Password must be at least 8 characters long and contain at least one uppercase letter,
        // one lowercase letter, and one number
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password);
    }

    public function registerUser()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register-submit'])) {
            // Validate name
            if (empty($_POST['name'])) {
                $this->errors[] = "Name is required";
            }

            // Validate phone
            if (empty($_POST['phone'])) {
                $this->errors[] = "Phone number is required";
            } elseif (!preg_match("/^[0-9]{10}$/", $_POST['phone'])) {
                $this->errors[] = "Phone number must be 10 digits";
            }

            // Validate email
            if (empty($_POST['email'])) {
                $this->errors[] = "Email is required";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Invalid email format";
            }

            // Validate password
            if (empty($_POST['password'])) {
                $this->errors[] = "Password is required";
            } elseif (!$this->isStrongPassword($_POST['password'])) {
                $this->errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number";
            }

            // Validate user type
            if (empty($_POST['userType'])) {
                $this->errors[] = "User type is required";
            } elseif (!in_array($_POST['userType'], ['agent', 'landlord'])) {
                $this->errors[] = "Invalid user type";
            }

            // Validate password confirmation
            if (empty($_POST['password-confirm'])) {
                $this->errors[] = "Please confirm your password";
            } elseif ($_POST['password'] !== $_POST['password-confirm']) {
                $this->errors[] = "Passwords do not match";
            }

            // If there are no errors, proceed with registration
            if (empty($this->errors)) {
                try {
                    // Check if email already exists
                    $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$_POST['email']]);
                    if ($stmt->rowCount() > 0) {
                        $this->errors[] = "Email already exists";
                        return $this->formatErrors();
                    }

                    // Hash password
                    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    // Insert new user
                    $stmt = $this->conn->prepare("INSERT INTO users (name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $hashedPassword,
                        $_POST['userType']
                    ]);

                    // Clear any existing errors
                    $this->errors = [];

                    return "Registration successful! You can now log in with your credentials.";
                } catch (PDOException $e) {
                    error_log("Registration error: " . $e->getMessage());
                    $this->errors[] = "Registration failed. Please try again.";
                    return $this->formatErrors();
                }
            }

            return $this->formatErrors();
        }
        return "";
    }

    private function formatErrors() {
        if (empty($this->errors)) {
            return "";
        }
        return "<ul class='list-disc list-inside'><li>" . implode("</li><li>", $this->errors) . "</li></ul>";
    }
} 