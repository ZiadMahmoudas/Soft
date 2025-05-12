<?php
require_once '../users.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['Train_name'], $data['Station_name'], $data['Departure_time'], $data['Arrival_time'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$id = $data['id'];
$train_name = trim($data['Train_name']);
$station_name = trim($data['Station_name']);
$departure_time = trim($data['Departure_time']);
$arrival_time = trim($data['Arrival_time']);

try {
    $db = DBConnection::getInstance()->getConnection();

    // Update train_station_times
    $stmt = $db->prepare("UPDATE train_station_times 
                          SET Train_name = :Train_name, Station_name = :Station_name, 
                              Departure_time = :Departure_time, Arrival_time = :Arrival_time
                          WHERE id = :id");
    $stmt->execute([
        ':Train_name' => $train_name,
        ':Station_name' => $station_name,
        ':Departure_time' => $departure_time,
        ':Arrival_time' => $arrival_time,
        ':id' => $id,
    ]);

    if ($stmt->rowCount() > 0) {
        // Update notification state in a JSON file
        $notificationData = ['updated' => true, 'message' => 'Train schedule has been updated. Please check the train schedule for updated timings.'];
        file_put_contents('../TrainSchedule/notification.json', json_encode($notificationData));

        echo json_encode(['status' => 'success', 'message' => 'Schedule updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes were made.']);
    }
} catch (Exception $e) {
    error_log("Error updating train schedule: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
