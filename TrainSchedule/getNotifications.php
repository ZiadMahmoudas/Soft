<?php
require_once '../users.php';

header('Content-Type: application/json');

try {
    $db = DBConnection::getInstance()->getConnection();

    // Fetch the latest active notification
    $stmt = $db->query("SELECT message FROM notifications WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($notification) {
        echo json_encode(['message' => $notification['message']]);
    } else {
        echo json_encode(['message' => null]);
    }
} catch (Exception $e) {
    error_log("Error fetching notifications: " . $e->getMessage());
    echo json_encode(['message' => null]);
}
?>
