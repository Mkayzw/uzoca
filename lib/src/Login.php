<?php
namespace app\src;

use app\assets\DB;

class Login {
    private $con;
    private $email;
    private $password;
    private $role;

    public function __construct($conn) {
        $this->con = $conn;
        error_log("Login class initialized with connection: " . print_r($conn, true));
    }

    public function login() {
        error_log("Login::login() called");
        error_log("POST data: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid request method");
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Get and validate input
        $this->email = $this->getEmail();
        $this->password = $this->getPassword();
        $this->role = $this->getRole();

        error_log("Login attempt - Email: " . $this->email . ", Role: " . $this->role);

        if (empty($this->email) || empty($this->password) || empty($this->role)) {
            error_log("Missing required fields");
            return ['success' => false, 'error' => 'All fields are required'];
        }

        try {
            // Query the database
            $sql = "SELECT * FROM users WHERE email = :email AND role = :role LIMIT 1";
            error_log("Executing SQL: " . $sql);
            error_log("Parameters - Email: " . $this->email . ", Role: " . $this->role);
            
            $stmt = $this->con->prepare($sql);
            
            if (!$stmt) {
                error_log("Database prepare error: " . print_r($this->con->errorInfo(), true));
                return ['success' => false, 'error' => 'Database error occurred'];
            }

            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':role', $this->role);
            $stmt->execute();
            
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            error_log("Database query result: " . print_r($user, true));

            if ($user && password_verify($this->password, $user['password'])) {
                error_log("Password verified successfully");
                
                // Clear any existing session data
                $_SESSION = array();
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                error_log("Session variables set: " . print_r($_SESSION, true));

                // Determine redirect URL based on role
                $redirect = $this->getRedirectUrl($user['role']);
                error_log("Redirecting to: " . $redirect);

                return [
                    'success' => true,
                    'redirect' => $redirect
                ];
            } else {
                error_log("Invalid credentials - User not found or password mismatch");
                return ['success' => false, 'error' => 'Invalid email or password'];
            }
        } catch (\Exception $e) {
            error_log("Exception during login: " . $e->getMessage());
            return ['success' => false, 'error' => 'An error occurred during login. Please try again.'];
        }
    }

    private function getEmail() {
        return isset($_POST['email']) ? strtolower(trim(strip_tags($_POST['email']))) : "";
    }

    private function getPassword() {
        return isset($_POST['password']) ? $_POST['password'] : "";
    }

    private function getRole() {
        return isset($_POST['role']) ? trim(strip_tags($_POST['role'])) : "";
    }

    private function getRedirectUrl($role) {
        switch($role) {
            case 'landlord':
                return '/uzoca/landlord/dashboard.php';
            case 'agent':
                return '/uzoca/agent/index.php';
            case 'admin':
                return '/uzoca/admin/index.php';
            case 'tenant':
                return '/uzoca/tenant/dashboard.php';
            default:
                error_log("Invalid role specified: " . $role);
                return '/uzoca/login.php';
        }
    }

    public function isLoggedIn() {
        error_log("Checking if user is logged in. Session data: " . print_r($_SESSION, true));
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        error_log("Logging out user. Previous session data: " . print_r($_SESSION, true));
        
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        error_log("Logout completed. Session should be destroyed.");
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $stmt = $this->con->prepare("SELECT id, name, email, role, phone, profile_pic FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
} 