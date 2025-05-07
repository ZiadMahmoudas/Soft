<?php
require_once '../users.php';

session_start(); // تأكد من بدء الجلسة

if (!isset($_SESSION['user_id'])) {
    error_log("Session user_id not set."); // رسالة تصحيح
    echo json_encode(['error' => 'User not logged in. Please log in to access this page.']);
    exit;
}

class UserData {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function getUser($user_id) {
        $sql = "SELECT user_name AS name, address, balance FROM users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

try {
    $userData = new UserData();
    $user = $userData->getUser($_SESSION['user_id']);
    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found in the database.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
