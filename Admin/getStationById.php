<?php
require_once '../users.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Station ID is required.']);
    exit;
}

$station_id = $_GET['id'];

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT station_id, station_name, city FROM station WHERE station_id = :station_id");
    $stmt->execute([':station_id' => $station_id]);
    $station = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($station) {
        echo json_encode($station);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Station not found.']);
    }
} catch (Exception $e) {
    error_log("Error fetching station by ID: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
