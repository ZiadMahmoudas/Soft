<?php
require_once '../users.php';

class AdminManager {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    private function isAdmin($admin_id) {
        $stmt = $this->db->prepare("SELECT role FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $admin_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['role'] === 'admin';
    }

    public function updateBalance($admin_id, $user_id, $new_balance) {
        if ($this->isAdmin($admin_id)) {
            $stmt = $this->db->prepare("UPDATE users SET balance = :balance WHERE user_id = :user_id");
            $stmt->execute([':balance' => $new_balance, ':user_id' => $user_id]);
        } else {
            throw new Exception("Unauthorized action.");
        }
    }

    public function deleteUser($admin_id, $user_id) {
        if ($this->isAdmin($admin_id)) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
        } else {
            throw new Exception("Unauthorized action.");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $adminManager = new AdminManager();

        if ($_POST['action'] === 'update_balance') {
            if (isset($_POST['admin_id'], $_POST['user_id'], $_POST['new_balance'])) {
                $admin_id = $_POST['admin_id'];
                $user_id = $_POST['user_id'];
                $new_balance = $_POST['new_balance'];
                $adminManager->updateBalance($admin_id, $user_id, $new_balance);
                echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
            }
        } elseif ($_POST['action'] === 'delete_user') {
            $admin_id = 1; // Hardcoded admin ID
            if (isset($_POST['user_id'])) {
                $user_id = $_POST['user_id'];
                if (is_numeric($user_id)) {
                    $adminManager->deleteUser($admin_id, $user_id);
                    echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid user_id.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
