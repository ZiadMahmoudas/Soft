<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['station_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Station ID is required.']);
    exit;
}

$station_id = $data['station_id'];

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM stations WHERE station_id = :station_id");
    $stmt->execute([':station_id' => $station_id]);
    echo json_encode(['status' => 'success', 'message' => 'Station deleted successfully.']);
} catch (Exception $e) {
    error_log("Error deleting station: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
