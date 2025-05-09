<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['User_id']) || !isset($data['Balance'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID and Balance are required.']);
    exit;
}

$User_id = $data['User_id'];
$Balance = $data['Balance'];

try {
    $user = new User();
    $user->updateUserBalance($User_id, $Balance);
    echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully.']);
} catch (Exception $e) {
    error_log("Error updating balance: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
