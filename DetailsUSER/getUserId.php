<?php
session_start();

if (isset($_SESSION['USER']) && isset($_SESSION['USER']->User_id)) {
    echo json_encode(['status' => 'success', 'User_id' => $_SESSION['USER']->User_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
}
?>
