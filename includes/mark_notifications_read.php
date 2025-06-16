<?php
session_start();
require_once __DIR__ . "/../lib/src/Notification.php";

use app\src\Notification;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$notification = new Notification();
$success = $notification->markAllAsRead($_SESSION['user_id']);

echo json_encode([
    'success' => $success,
    'message' => $success ? 'All notifications marked as read' : 'Failed to mark notifications as read'
]);
?> 