<?php
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
    $schedule = new TrainSchedule();
    $data = $schedule->getSchedule();
    echo json_encode($data);
?>
