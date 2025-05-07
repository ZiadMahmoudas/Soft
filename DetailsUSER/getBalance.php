<?php
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id']; // Pass the user_id dynamically (e.g., from session or frontend)

    $user = new User();
    $balance = $user->checkBalance($user_id);

    if ($balance) {
        echo json_encode(['status' => 'success', 'balance' => $balance->balance]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found or balance unavailable.']);
    }
}
?>
