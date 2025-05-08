<?php
require_once '../users.php';

class AdminManager {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function updateBalance($admin_id, $user_id, $new_balance) {
        $admin = new Admin();
        $admin->updateUserBalanceByAdmin($admin_id, $user_id, $new_balance);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $adminManager = new AdminManager();

        if ($_POST['action'] === 'update_balance') {
            $admin_id = $_POST['admin_id'];
            $user_id = $_POST['user_id'];
            $new_balance = $_POST['new_balance'];
            $adminManager->updateBalance($admin_id, $user_id, $new_balance);
            echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
