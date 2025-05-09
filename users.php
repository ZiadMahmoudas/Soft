<?php
abstract class Person {
    protected $db;
    protected $name;
    protected $Password;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    abstract public function login($name, $Password);
    abstract public function signup($name, $Address, $Password);
    abstract public function logout();
}

class DBConnection {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=soft2", "root", "");
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            error_log("Database connection established successfully."); // تسجيل نجاح الاتصال
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage()); // تسجيل الخطأ
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}

class User extends Person {
    public function login($User_name, $Password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE User_name = :User_name AND Password = :Password");
        $stmt->execute([':User_name' => $User_name, ':Password' => $Password]);
        $user = $stmt->fetch();

        if ($user) {
            session_start();
            $_SESSION['USER'] = $user; // تخزين بيانات المستخدم في الجلسة
            return $user;
        }
        return null;
    }

    public function getUserDetails() {
        session_start();
        if (isset($_SESSION['USER'])) {
            return $_SESSION['USER']; // إرجاع بيانات المستخدم من الجلسة
        }
        return "NO USER IS LOGGED IN.";
    }

    public function signup($User_name, $Address, $Password) {
        $stmt = $this->db->prepare("INSERT INTO users (User_name, Address, Password, Balance, Admin_id)
                                    VALUES (:User_name, :Address, :Password, 0.00, 1)");
        $stmt->execute([':User_name' => $User_name, ':Address' => $Address, ':Password' => $Password]);
    }

    public function logout() {
        session_start();
        session_destroy();
    }

    public function updateUser($User_id, $new_Address, $new_Password) {
        $stmt = $this->db->prepare("UPDATE users SET Address = :Address, Password = :Password WHERE User_id = :User_id");
        $stmt->execute([':Address' => $new_Address, ':Password' => $new_Password, ':User_id' => $User_id]);
    }

    public function deleteUser($User_id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE User_id = :User_id");
        $stmt->execute([':User_id' => $User_id]);
    }

    public function checkBalance($User_id) {
        $stmt = $this->db->prepare("SELECT Balance FROM users WHERE User_id = :User_id");
        $stmt->execute([':User_id' => $User_id]);
        $balance = $stmt->fetch(PDO::FETCH_OBJ);

        if ($balance && isset($balance->Balance)) {
            return $balance; // Return the balance object
        } else {
            error_log("Balance not found for User ID: " . $User_id); // Debugging
            return null; // Return null if no balance found
        }
    }

    public function updateUserBalance($User_id, $new_Balance) {
        $stmt = $this->db->prepare("UPDATE users SET Balance = :Balance WHERE User_id = :User_id");
        $stmt->execute([':Balance' => $new_Balance, ':User_id' => $User_id]);
    }
}

class Admin extends Person {
    public function login($Admin_name, $Password) {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE Admin_name = :Admin_name AND Password = :Password");
        $stmt->execute([':Admin_name' => $Admin_name, ':Password' => $Password]);
        return $stmt->fetch() ?: null;
    }

    public function signup($Admin_name, $Address, $Password) {
        $stmt = $this->db->prepare("INSERT INTO admin (Admin_name, Address, Password) VALUES (:Admin_name, :Address, :Password)");
        $stmt->execute([':Admin_name' => $Admin_name, ':Address' => $Address, ':Password' => $Password]);
    }

    public function logout() {
        session_start();
        session_destroy();
    }

    public function updateUserBalanceByAdmin($Admin_id, $User_id, $new_Balance) {
        $adminCheck = $this->db->prepare("SELECT * FROM admin WHERE Admin_id = :Admin_id");
        $adminCheck->execute([':Admin_id' => $Admin_id]);

        if ($adminCheck->fetch()) {
            $stmt = $this->db->prepare("UPDATE users SET Balance = :Balance WHERE User_id = :User_id");
            $stmt->execute([':Balance' => $new_Balance, ':User_id' => $User_id]);
            echo "Balance updated.";
        } else {
            echo "Admin not found.";
        }
    }

    public function displayAllSchedules() {
        $sql = "SELECT tr.trip_id, tr.Source, tr.Destination, tr.departure_time, tr.arrival_time, t.name 
                FROM trips tr
                JOIN trains t ON tr.train_id = t.train_id";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
            echo "Trip ID: $row->trip_id, From: $row->Source, To: $row->Destination" . PHP_EOL;
        }
    }
}

class Ticket {
    protected $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function bookTicket($User_id, $Source, $Destination, $Class, $Ticket_type,) {
        $Price = match ($Class) {
            'Economy' => 100.00,
            'VIP' => 250.00,
            default => 0
        };
        if ($Ticket_type === 'Round-trip') $Price *= 2;

        $Balance = (new User())->checkBalance($User_id);
        if ($Balance && $Balance->Balance >= $Price) {
            $trainStmt = $this->db->query("SELECT train_id FROM trains LIMIT 1");
            $train = $trainStmt->fetch();

            $tripStmt = $this->db->prepare("INSERT INTO trips (train_id, Source, Destination) VALUES (:train_id, :Source, :Destination)");
            $tripStmt->execute([':train_id' => $train->train_id, ':Source' => $Source, ':Destination' => $Destination]);
            $trip_id = $this->db->lastInsertId();

            $ticket = $this->db->prepare("INSERT INTO ticket (User_id, trip_id, Ticket_type, Class, Price)
                                              VALUES (:User_id, :trip_id, :Ticket_type, :Class, :Price)");
            $ticket->execute([
                ':User_id' => $User_id,
                ':trip_id' => $trip_id,
                ':Ticket_type' => $Ticket_type,
                ':Class' => $Class,
                ':Price' => $Price,
                ':Source'=>$Source,
                ':Destination'=>$Destination,
            ]);

            $this->db->prepare("UPDATE users SET Balance = Balance - :Price WHERE User_id = :User_id")
                     ->execute([':Price' => $Price, ':User_id' => $User_id]);

            return "Ticket booked!";
        } else {
            return "Insufficient Balance.";
        }
    }

    public function printTicket($ticket_id) {
        $sql = "SELECT t.*, u.User_name, tr.Source, tr.Destination 
                FROM ticket t 
                JOIN users u ON t.User_id = u.User_id
                JOIN trips tr ON t.trip_id = tr.trip_id
                WHERE t.ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticket_id]);
        $ticket = $stmt->fetch();

        if ($ticket) {
            echo "Ticket for {$ticket->User_name}, From {$ticket->Source} to {$ticket->Destination}, Class: {$ticket->Class}, Price: {$ticket->Price}";
        } else {
            echo "Ticket not found.";
        }
    }
}
class Notification {
    protected $db;
    protected $prompt;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function sendPrompt($prompt) {
        $this->prompt = $prompt;
        echo " Emergency Prompt Sent: $this->prompt" . PHP_EOL;
    }

    public function sendDelayPrompt($train_id, $delayTime) {
        $sql = "SELECT name FROM trains WHERE train_id = :train_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':train_id' => $train_id]);
        $train = $stmt->fetch();

        if ($train) {
            $message = " Delay Alert: Train {$train->name} delayed by $delayTime minutes.";
            echo $message . PHP_EOL;
        } else {
            echo " Train not found." . PHP_EOL;
        }
    }
}

class Station {
    protected $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function addStation($name, $city) {
        $sql = "INSERT INTO station (station_name, city) VALUES (:name, :city)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name, ':city' => $city]);
    }

    public function getAllStations() {
        $sql = "SELECT station_id, station_name, city FROM station";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // تأكد من إرجاع البيانات كـ Array
    }
}
/* Train => Name  */
class Train {
    protected $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function addTrain($name, $status = 'Active') {
        $sql = "INSERT INTO trains (name, status) VALUES (:name, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name, ':status' => $status]);
        echo " Train '$name' added with status '$status'." . PHP_EOL;
    }

    public function updateStatus($train_id, $status) {
        $sql = "UPDATE trains SET status = :status WHERE train_id = :train_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status, ':train_id' => $train_id]);
        echo " Train ID $train_id status updated to '$status'." . PHP_EOL;
    }

    public function getAllTrains() {
        $sql = "SELECT * FROM trains";
        $stmt = $this->db->query($sql);
        $trains = $stmt->fetchAll();

        echo " Trains List:" . PHP_EOL;
        foreach ($trains as $train) {
            echo "ID: $train->train_id, Name: $train->name, Status: $train->status" . PHP_EOL;
        }
    }
    public function updateTrain($train_id, $new_name, $new_status) {
        $sql = "UPDATE trains SET name = :name, status = :status WHERE train_id = :train_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $new_name,
            ':status' => $new_status,
            ':train_id' => $train_id
        ]);
        echo " Train ID $train_id updated to Name: '$new_name', Status: '$new_status'" . PHP_EOL;
    }
    
}

class TrainSchedule {
    protected $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function addSchedule($Train_name, $Station_name, $Departure_time, $Arrival_time) {
        $sql = "INSERT INTO train_station_times (Train_name, Station_name, Departure_time, Arrival_time) 
                VALUES (:Train_name, :Station_name, :Departure_time, :Arrival_time)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':Train_name' => $Train_name,
            ':Station_name' => $Station_name,
            ':Departure_time' => $Departure_time,
            ':Arrival_time' => $Arrival_time
        ]);
        echo "Schedule added for Train: '$Train_name' at Station: '$Station_name'." . PHP_EOL;
    }

    public function getScheduleByTrain($Train_name) {
        $sql = "SELECT * FROM train_station_times WHERE Train_name = :Train_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':Train_name' => $Train_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScheduleByStation($Station_name) {
        $sql = "SELECT * FROM train_station_times WHERE Station_name = :Station_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':Station_name' => $Station_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSchedule($id, $Train_name, $Station_name, $Departure_time, $Arrival_time) {
        $sql = "UPDATE train_station_times 
                SET Train_name = :Train_name, Station_name = :Station_name, 
                    Departure_time = :Departure_time, Arrival_time = :Arrival_time 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':Train_name' => $Train_name,
            ':Station_name' => $Station_name,
            ':Departure_time' => $Departure_time,
            ':Arrival_time' => $Arrival_time,
            ':id' => $id
        ]);
        echo "Schedule ID $id updated successfully." . PHP_EOL;
    }

    public function deleteSchedule($id) {
        $sql = "DELETE FROM train_station_times WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        echo "Schedule ID $id deleted successfully." . PHP_EOL;
    }

    public function getAllSchedules() {
        $sql = "SELECT * FROM train_station_times";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScheduleWithFilters($filters) {
        $sql = "SELECT * FROM train_station_times WHERE 1=1";
        $params = [];

        if (!empty($filters['Train_name'])) {
            $sql .= " AND Train_name = :Train_name";
            $params[':Train_name'] = $filters['Train_name'];
        }

        if (!empty($filters['Station_name'])) {
            $sql .= " AND Station_name = :Station_name";
            $params[':Station_name'] = $filters['Station_name'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>