<?php
require_once '../users.php';

header('Content-Type: application/json');

try {
    $schedule = new TrainSchedule();
    $data = $schedule->getAllSchedules();
    echo json_encode($data);
} catch (Exception $e) {
    error_log("Error fetching train schedule: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
