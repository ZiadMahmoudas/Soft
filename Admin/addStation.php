<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['station_name']) || !isset($data['city'])) {
    echo json_encode(['status' => 'error', 'message' => 'Station name and city are required.']);
    exit;
}

$station_name = $data['station_name'];
$city = $data['city'];

try {
    $station = new Station();
    $station->addStation($station_name, $city);
    echo json_encode(['status' => 'success', 'message' => 'Station added successfully.']);
} catch (Exception $e) {
    error_log("Error adding station: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
