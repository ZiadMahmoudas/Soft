<?php
require_once '../users.php';

header('Content-Type: application/json');
    $schedule = new TrainSchedule();
    $data = $schedule->getAllSchedules(); 
    echo json_encode($data); 
?>
