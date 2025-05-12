<?php
require_once '../users.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['train_name'], $data['station_name'], $data['departure_time'], $data['arrival_time'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$train_name = $data['train_name'];
$station_name = $data['station_name'];
$departure_time = $data['departure_time'];
$arrival_time = $data['arrival_time'];

try {
    $db = DBConnection::getInstance()->getConnection();

    // Insert into train_station_times
    $stmt = $db->prepare("INSERT INTO train_station_times (Train_name, Station_name, Departure_time, Arrival_time) 
                          VALUES (:train_name, :station_name, :departure_time, :arrival_time)");
    $stmt->execute([
        ':train_name' => $train_name,
        ':station_name' => $station_name,
        ':departure_time' => $departure_time,
        ':arrival_time' => $arrival_time,
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Train schedule added successfully.']);
} catch (Exception $e) {
    error_log("Error adding train schedule: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
