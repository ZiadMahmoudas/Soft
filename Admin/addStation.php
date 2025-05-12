<?php
require_once '../users.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['station_name']) || !isset($data['city'])) {
    echo json_encode(['status' => 'error', 'message' => 'Station name and city are required.']);
    exit;
}

$station_name = trim($data['station_name']);
$city = trim($data['city']);

if (empty($station_name) || empty($city)) {
    echo json_encode(['status' => 'error', 'message' => 'Station name and city cannot be empty.']);
    exit;
}

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO stations (station_name, city) VALUES (:station_name, :city)");
    $stmt->execute([
        ':station_name' => $station_name,
        ':city' => $city,
    ]);
    echo json_encode(['status' => 'success', 'message' => 'Station added successfully.']);
} catch (Exception $e) {
    error_log("Error adding station: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
