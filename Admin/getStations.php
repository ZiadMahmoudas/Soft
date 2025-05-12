<?php
require_once '../users.php';

header('Content-Type: application/json');

try {
    $station = new Station();
    $data = $station->getAllStations();
    echo json_encode($data);
} catch (Exception $e) {
    error_log("Error fetching station: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
