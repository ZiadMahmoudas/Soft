<?php
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user_id'])) {
        // Search for a specific user by ID
        $user_id = $_GET['user_id'];
        $db = DBConnection::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT user_id, user_name, balance FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode(['status' => 'success', 'user' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } else {
        // Fetch all users
        $db = DBConnection::getInstance()->getConnection();
        $stmt = $db->query("SELECT user_id, user_name, balance FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'users' => $users]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user balance
    $user_id = $_POST['user_id'];
    $new_balance = $_POST['new_balance'];

    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET balance = :balance WHERE user_id = :user_id");
    $stmt->execute([':balance' => $new_balance, ':user_id' => $user_id]);

    echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully.']);
}
?>
