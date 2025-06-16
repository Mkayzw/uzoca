<?php

namespace app\src;

use app\config\Database;
use PDO;

class UserProfile {
    private $conn;
    private $userId;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->userId = $_SESSION['user_id'] ?? null;
    }

    public function getUserProfile() {
        if (!$this->userId) {
            return null;
        }

        $query = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($data) {
        if (!$this->userId) {
            return false;
        }

        $query = "UPDATE users SET 
                 name = :name,
                 email = :email,
                 phone = :phone,
                 address = :address,
                 updated_at = NOW()
                 WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":address", $data['address']);
        $stmt->bindParam(":user_id", $this->userId);

        return $stmt->execute();
    }

    public function updatePassword($currentPassword, $newPassword) {
        if (!$this->userId) {
            return false;
        }

        // First verify current password
        $query = "SELECT password FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($currentPassword, $user['password'])) {
            return false;
        }

        // Update to new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET 
                 password = :password,
                 updated_at = NOW()
                 WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":user_id", $this->userId);

        return $stmt->execute();
    }

    public function updateProfilePicture($imageUrl) {
        if (!$this->userId) {
            return false;
        }

        $query = "UPDATE users SET 
                 profile_picture = :profile_picture,
                 updated_at = NOW()
                 WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":profile_picture", $imageUrl);
        $stmt->bindParam(":user_id", $this->userId);

        return $stmt->execute();
    }
} 