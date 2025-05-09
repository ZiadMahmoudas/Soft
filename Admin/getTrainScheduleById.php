<?php
require_once '../users.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Schedule ID is required.']);
    exit;
}

$id = $_GET['id'];

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM train_station_times WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($schedule);
} catch (Exception $e) {
    error_log("Error fetching schedule by ID: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
