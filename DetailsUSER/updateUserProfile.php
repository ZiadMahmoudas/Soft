<?php
require_once '../users.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

class UserProfileUpdater {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function updateProfile($user_id, $first_name, $last_name, $country, $email) {
        $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, country = :country, email = :email WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':country' => $country,
            ':email' => $email,
            ':user_id' => $user_id,
        ]);
        return $stmt->rowCount() > 0;
    }
}

try {
    $updater = new UserProfileUpdater();
    $updated = $updater->updateProfile(
        $_SESSION['user_id'],
        $data['first_name'],
        $data['last_name'],
        $data['country'],
        $data['email']
    );

    if ($updated) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes were made.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
