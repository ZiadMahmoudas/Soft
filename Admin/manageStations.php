<?php
require_once '../users.php';

$station = new Station();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stations = $station->getAllStations();
        echo json_encode(['status' => 'success', 'stations' => $stations]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $station_name = $_POST['station_name'];
        $city = $_POST['city'];
        $station->addStation($station_name, $city);
        echo json_encode(['status' => 'success', 'message' => 'Station added successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>
