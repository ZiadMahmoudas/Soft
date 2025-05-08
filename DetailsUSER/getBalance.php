<?php
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $User_id = $_POST['User_id']; // Pass the user_id dynamically (e.g., from session or frontend)

    $user = new User();
    $Balance = $user->checkBalance($User_id);

    if ($Balance) {
        echo json_encode(['status' => 'success', 'balance' => $Balance->Balance]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found or balance unavailable.']);
    }
}
?>
