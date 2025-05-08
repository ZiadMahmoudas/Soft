<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided.']);
    exit;
}

$user_id = $data['user_id'];
$address = $data['address'] ?? null;
$password = $data['password'] ?? null;

if (!$address || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Address and password are required.']);
    exit;
}

class UserProfileUpdater {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function updateProfile($user_id, $address, $password) {
        $sql = "UPDATE users SET address = :address, password = :password WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':address' => $address,
            ':password' => $password,
            ':user_id' => $user_id,
        ]);
        return $stmt->rowCount() > 0;
    }
}

try {
    $updater = new UserProfileUpdater();
    $updated = $updater->updateProfile($user_id, $address, $password);

    if ($updated) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes were made.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
