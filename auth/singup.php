<?php
header("Content-Type: application/json");
require_once '../users.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        $name = $_POST['name'];
        $password = $_POST['password'];

        // Check if the user is an admin
        $adminResult = (new Admin())->login($name, $password);
        if ($adminResult) {
            echo json_encode(['status' => 'success', 'isAdmin' => true, 'message' => 'Admin login successful']);
            exit;
        }

        // Check if the user is a regular user
        $userResult = (new User())->login($name, $password);
        if ($userResult) {
            echo json_encode(['status' => 'success', 'isAdmin' => false, 'message' => 'User login successful']);
            exit;
        }

        // If neither, return an error
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        exit;
    } elseif ($action === 'signup') {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $password = $_POST['password'];

        try {
            (new User())->signup($name, $address, $password);
            echo json_encode(['status' => 'success', 'message' => 'Signup successful']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Signup failed: ' . $e->getMessage()]);
        }
    }
    exit;
}

?>