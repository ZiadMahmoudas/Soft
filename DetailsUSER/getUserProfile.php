<?php
require_once '../users.php';

session_start(); // تأكد من بدء الجلسة

if (!isset($_SESSION['user_id'])) {
    error_log("Session user_id not set."); // رسالة تصحيح
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

class UserProfile {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function getProfile($user_id) {
        $sql = "SELECT user_name AS name, address, balance 
                FROM users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

try {
    $profile = new UserProfile();
    $userProfile = $profile->getProfile($_SESSION['user_id']);
    if ($userProfile) {
        echo json_encode(['status' => 'success', 'data' => $userProfile]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User profile not found.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
