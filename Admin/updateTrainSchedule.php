<?php
require_once '../users.php';

header('Content-Type: application/json'); // تأكد من أن الاستجابة تكون JSON

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['Train_name']) || !isset($data['Station_name']) || !isset($data['Departure_time']) || !isset($data['Arrival_time'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$id = $data['id'];
$Train_name = $data['Train_name'];
$Station_name = $data['Station_name'];
$Departure_time = $data['Departure_time'];
$Arrival_time = $data['Arrival_time'];

try {
    $schedule = new TrainSchedule();
    $schedule->updateSchedule($id, $Train_name, $Station_name, $Departure_time, $Arrival_time);
    echo json_encode(['status' => 'success', 'message' => 'Schedule updated successfully.']);
} catch (Exception $e) {
    error_log("Error updating schedule: " . $e->getMessage()); // تسجيل الخطأ
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
