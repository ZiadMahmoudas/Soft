<?php
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all users and their prices
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->query("SELECT user_id, user_name, balance FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'users' => $users]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user price
    $user_id = $_POST['user_id'];
    $new_balance = $_POST['new_balance'];

    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET balance = :balance WHERE user_id = :user_id");
    $stmt->execute([':balance' => $new_balance, ':user_id' => $user_id]);

    echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully.']);
}
?>
