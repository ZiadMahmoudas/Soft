<?php
require_once '../../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['User_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not provided.']);
        exit;
    }

    $User_id = $data['User_id'];

    $user = new User();
    $Balance = $user->checkBalance($User_id);

    if ($Balance) {
        echo json_encode(['status' => 'success', 'balance' => $Balance->Balance]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found or balance unavailable.']);
    }
}
?>
