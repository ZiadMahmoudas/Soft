<?php
require_once '../users.php';

header('Content-Type: application/json');

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->query("SELECT User_id, User_name, Balance FROM users");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (Exception $e) {
    error_log("Error fetching users: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
