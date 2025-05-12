<?php
require_once '../users.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
    exit;
}

$user_id = $data['user_id'];

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found or could not be deleted.']);
    }
} catch (Exception $e) {
    error_log("Error deleting user: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
