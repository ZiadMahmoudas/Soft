<?php
require_once '../users.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['station_id'], $data['station_name'], $data['city'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$station_id = $data['station_id'];
$station_name = trim($data['station_name']);
$city = trim($data['city']);

try {
    $db = DBConnection::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE station SET station_name = :station_name, city = :city WHERE station_id = :station_id");
    $stmt->execute([
        ':station_name' => $station_name,
        ':city' => $city,
        ':station_id' => $station_id,
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Station updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes were made.']);
    }
} catch (Exception $e) {
    error_log("Error updating station: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
