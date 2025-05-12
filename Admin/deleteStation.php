<?php
require_once '../users.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['station_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Station ID is required.']);
    exit;
}

$station_id = $data['station_id'];

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM station WHERE station_id = :station_id");
    $stmt->execute([':station_id' => $station_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Station deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Station not found or could not be deleted.']);
    }
} catch (Exception $e) {
    error_log("Error deleting station: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
