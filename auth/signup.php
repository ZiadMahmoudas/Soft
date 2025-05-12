<?php
header("Content-Type: application/json");
require_once '../users.php';
file_put_contents('php://stderr', print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        $name = $_POST['name'];
        $password = $_POST['password'];

        file_put_contents('php://stderr', "Login attempt: $name\n", FILE_APPEND);

        $adminResult = (new Admin())->login($name, $password);
        if ($adminResult) {
            echo json_encode(['status' => 'success', 'isAdmin' => true, 'message' => 'Admin login successful']);
            exit;
        }

        $userResult = (new User())->login($name, $password);
        if ($userResult) {
            echo json_encode(['status' => 'success', 'isAdmin' => false, 'message' => 'User login successful']);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        exit;
    }

    if ($action === 'signup') {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $password = $_POST['password'];

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
