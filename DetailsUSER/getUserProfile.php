<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['User_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided.']);
    exit;
}

$User_id = $data['User_id'];

class UserProfile {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function getProfile($User_id) {
        $sql = "SELECT User_name AS name, Balance AS balance FROM users WHERE User_id = :User_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':User_id' => $User_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: ['name' => '', 'balance' => 0];
    }
}

try {
    $profile = new UserProfile();
    $userProfile = $profile->getProfile($User_id);
    echo json_encode(['status' => 'success', 'data' => $userProfile]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
