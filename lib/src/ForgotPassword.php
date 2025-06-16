<?php

namespace app\src;

use app\config\Database;
use PDO;
use PDOException;

class ForgotPassword
{
    private $conn;
    private $phoneEmail;

    public function __construct()
    {
        try {
            $database = new Database();
            $this->conn = $database->getConnection();
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new PDOException("Database connection failed");
        }
    }

    // Sets the phone number or email field of the form
    public function setPhoneEmail(): string
    {
        return $this->phoneEmail = isset($_POST['phoneEmail']) ? strtolower(trim(strip_tags($_POST['phoneEmail']))) : "";
    }

    public function resetPassword()
    {
        if (isset($_POST['submit'])) {
            // Check if a email or phone number was entered
            if (empty($this->setPhoneEmail())) {
                displayMessage("<span class='font-bold'>Email or Phone Number</span> field is required.", "text-rose-500");
                return;
            }

            try {
                // Check if user exists in the users table
                $query = "SELECT id, email, name FROM users WHERE phone = :phone_email OR email = :phone_email";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':phone_email', $this->phoneEmail);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    displayMessage("No account found with that email or phone number.", "text-rose-500");
                return;
                }

                // Generate new password
                $newPassword = bin2hex(random_bytes(4)); // 8 characters
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update user's password
                $updateQuery = "UPDATE users SET password = :password WHERE id = :user_id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':password', $hashedPassword);
                $updateStmt->bindParam(':user_id', $user['id']);
                
                if ($updateStmt->execute()) {
                    // Prepare email content
                    $to = $user['email'];
                    $subject = "UZOCA - Password Reset";
                $message = "
                    <html>
                        <head>
                                <title>Password Reset</title>
                        </head>
                        <body>
                                <p>Dear " . htmlspecialchars($user['name']) . ",</p>
                                <p>Your password has been reset as requested.</p>
                                <p>Your new password is: <strong>" . $newPassword . "</strong></p>
                                <p>Please login with this password and change it immediately in your account settings.</p>
                                <p>If you did not request this password reset, please contact support immediately.</p>
                                <br>
                                <p>Best regards,</p>
                                <p>UZOCA Team</p>
                        </body>
                    </html>
                ";

                    // Email headers
                    $headers = array(
                        'MIME-Version' => '1.0',
                        'Content-type' => 'text/html; charset=UTF-8',
                        'From' => 'noreply@uzoca.com',
                        'Reply-To' => 'support@uzoca.com'
                    );

                    // Send email
                    if (@mail($to, $subject, $message, implode("\r\n", $headers))) {
                        displayMessage("Password reset successful! Please check your email for the new password. If you can't find the email, please check your spam folder.", "text-green-500");
                        // Clear the form
                    $this->phoneEmail = "";
                        // Redirect after 5 seconds
                        header("Refresh: 5; url=/uzoca/login.php");
                    } else {
                        $error = error_get_last();
                        error_log("Failed to send password reset email to: " . $to . ". Error: " . ($error ? $error['message'] : 'Unknown error'));
                        displayMessage("Your password has been reset to: " . $newPassword . ". Please save this password and change it after logging in.", "text-green-500");
                        // Redirect after 10 seconds
                        header("Refresh: 10; url=/uzoca/login.php");
                    }
                } else {
                    displayMessage("Failed to reset password. Please try again.", "text-rose-500");
                }
            } catch (PDOException $e) {
                error_log("Password reset error: " . $e->getMessage());
                displayMessage("An error occurred. Please try again later.", "text-rose-500");
            }
        } else {
            displayMessage("Reset your password");
        }
    }
}
