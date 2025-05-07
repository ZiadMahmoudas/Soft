<?php
header("Content-Type: application/json");
require_once '../users.php';

session_start(); // تأكد من بدء الجلسة
file_put_contents('php://stderr', print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        $name = $_POST['name'];
        $password = $_POST['password'];

        // Log login attempt
        file_put_contents('php://stderr', "Login attempt: $name\n", FILE_APPEND);

        $user = new User();
        $userResult = $user->login($name, $password);

        if ($userResult) {
            $_SESSION['user_id'] = $userResult->user_id; // تخزين user_id في الجلسة
            echo json_encode(['status' => 'success', 'message' => 'Login successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        }
        exit;
    }

    if ($action === 'signup') {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $password = $_POST['password'];

        // Log signup attempt
        file_put_contents('php://stderr', "Signup attempt: $name\n", FILE_APPEND);

        try {
            (new User())->signup($name, $address, $password);
            echo json_encode(['status' => 'success', 'message' => 'Signup successful']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Signup failed: ' . $e->getMessage()]);
        }
        exit;
    }
}
?>
