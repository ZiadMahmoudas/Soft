<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided.']);
    exit;
}

$user_id = $data['user_id'];

class UserProfile {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function getProfile($user_id) {
        $sql = "SELECT user_name AS name, balance FROM users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: ['name' => '', 'balance' => 0];
    }
}

try {
    $profile = new UserProfile();
    $userProfile = $profile->getProfile($user_id);
    echo json_encode(['status' => 'success', 'data' => $userProfile]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
