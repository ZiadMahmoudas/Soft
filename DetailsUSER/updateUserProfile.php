<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['User_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided.']);
    exit;
}

$User_id = $data['User_id'];
$Address = $data['Address'] ?? null;
$Password = $data['Password'] ?? null;

if (!$Address || !$Password) {
    echo json_encode(['status' => 'error', 'message' => 'Address and Password are required.']);
    exit;
}

class UserProfileUpdater {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function updateProfile($User_id, $Address, $Password) {
        $sql = "UPDATE users SET Address = :Address, Password = :Password WHERE User_id = :User_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':Address' => $Address,
            ':Password' => $Password,
            ':User_id' => $User_id,
        ]);
        return $stmt->rowCount() > 0;
    }
}

try {
    $updater = new UserProfileUpdater();
    $updated = $updater->updateProfile($User_id, $Address, $Password);

    if ($updated) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes were made.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
