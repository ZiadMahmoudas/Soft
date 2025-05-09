<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../users.php';

class TrainSchedule {
    private $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function getSchedule() {
        $sql = "SELECT t.name AS train_name, tr.departure_time, tr.arrival_time, tr.duration, tr.price 
                FROM trips tr
                JOIN trains t ON tr.train_id = t.train_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

header('Content-Type: application/json');

try {
    $schedule = new TrainSchedule();
    $data = $schedule->getSchedule();
    error_log("Train schedule fetched: " . json_encode($data)); // تسجيل البيانات
    echo json_encode($data);
} catch (Exception $e) {
    error_log("Error fetching train schedule: " . $e->getMessage()); // تسجيل الخطأ
    echo json_encode(['error' => $e->getMessage()]);
}
?>
