<?php
require_once '../users.php';

header('Content-Type: application/json');

try {
    $schedule = new TrainSchedule();
    $data = $schedule->getAllSchedules(); // استدعاء الدالة مباشرةً من الكلاس
    echo json_encode($data); // إرجاع البيانات كـ JSON
} catch (Exception $e) {
    error_log("Error fetching train schedule: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
