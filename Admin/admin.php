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
            error_log("Rows affected: " . $stmt->rowCount()); // Debugging log
            if ($stmt->rowCount() > 0) {
                return true; // User deleted successfully
            } else {
                throw new Exception("User not found or could not be deleted.");
            }
        } else {
            throw new Exception("Unauthorized action.");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $adminManager = new AdminManager();

        $action = $_POST['action'] ?? null;

        if ($action === 'update_balance') {
            if (isset($_POST['admin_id'], $_POST['user_id'], $_POST['new_balance'])) {
                $admin_id = $_POST['admin_id'];
                $user_id = $_POST['user_id'];
                $new_balance = $_POST['new_balance'];
                $adminManager->updateBalance($admin_id, $user_id, $new_balance);
                echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
            }
        } elseif ($action === 'delete_user') {
            $user_id = $_POST['user_id'] ?? null;
            error_log("Received user_id: " . $user_id); // Log the user_id for debugging

            if ($user_id && is_numeric($user_id)) {
                try {
                    $adminManager->deleteUser(1, $user_id); // Assuming admin_id is 1
                    echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
                } catch (Exception $e) {
                    error_log("Error deleting user: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
            } else {
                error_log("Invalid or missing user_id");
                echo json_encode(['status' => 'error', 'message' => 'Invalid or missing user_id.']);
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
