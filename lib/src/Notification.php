<?php

namespace app\src;

class Notification
{
    private $con;

    public function __construct()
    {
        global $conn;
        if (!$conn) {
            throw new \Exception("Database connection not initialized");
        }
        $this->con = $conn;
    }

    /**
     * Create a new notification
     * 
     * @param int $userId The ID of the user to notify
     * @param string $title The notification title
     * @param string $message The notification message
     * @param string $type The notification type (e.g., 'property', 'booking', 'system')
     * @return bool True if notification was created successfully, false otherwise
     */
    public function create($userId, $title, $message, $type = 'system')
    {
        try {
            $sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (:user_id, :title, :message, :type)";
            $stmt = $this->con->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':message' => $message,
                ':type' => $type
            ]);
        } catch (\PDOException $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all notifications for a user
     * 
     * @param int $userId The ID of the user
     * @param int $limit Optional limit of notifications to return
     * @return array Array of notifications
     */
    public function getForUser($userId, $limit = 10)
    {
        try {
            $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unread notifications count for a user
     * 
     * @param int $userId The ID of the user
     * @return int Number of unread notifications
     */
    public function getUnreadCount($userId)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark a notification as read
     * 
     * @param int $notificationId The ID of the notification
     * @return bool True if notification was marked as read successfully, false otherwise
     */
    public function markAsRead($notificationId)
    {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
            $stmt = $this->con->prepare($sql);
            return $stmt->execute([':id' => $notificationId]);
        } catch (\PDOException $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId The ID of the user
     * @return bool True if notifications were marked as read successfully, false otherwise
     */
    public function markAllAsRead($userId)
    {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id";
            $stmt = $this->con->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (\PDOException $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a notification
     * 
     * @param int $notificationId The ID of the notification
     * @return bool True if notification was deleted successfully, false otherwise
     */
    public function delete($notificationId)
    {
        try {
            $sql = "DELETE FROM notifications WHERE id = :id";
            $stmt = $this->con->prepare($sql);
            return $stmt->execute([':id' => $notificationId]);
        } catch (\PDOException $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all notifications for a user
     * 
     * @param int $userId The ID of the user
     * @return bool True if notifications were deleted successfully, false otherwise
     */
    public function deleteAllForUser($userId)
    {
        try {
            $sql = "DELETE FROM notifications WHERE user_id = :user_id";
            $stmt = $this->con->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (\PDOException $e) {
            error_log("Error deleting all notifications: " . $e->getMessage());
            return false;
        }
    }
}
?> 