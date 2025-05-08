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
            $this->connection = new PDO("mysql:host=localhost;dbname=softwareproject", "root", "");
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
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
        return $stmt->fetch();
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
        $sql = "SELECT tr.trip_id, tr.source, tr.destination, tr.departure_time, tr.arrival_time, t.name 
                FROM trips tr
                JOIN trains t ON tr.train_id = t.train_id";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
            echo "Trip ID: $row->trip_id, From: $row->source, To: $row->destination" . PHP_EOL;
        }
    }
}

class Ticket {
    protected $db;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    public function bookTicket($User_id, $source, $destination, $class, $ticket_type, $purchase_time) {
        $price = match ($class) {
            'Economy' => 100.00,
            'VIP' => 250.00,
            default => 0
        };
        if ($ticket_type === 'Round-trip') $price *= 2;

        $Balance = (new User())->checkBalance($User_id);
        if ($Balance && $Balance->Balance >= $price) {
            $trainStmt = $this->db->query("SELECT train_id FROM trains LIMIT 1");
            $train = $trainStmt->fetch();

            $tripStmt = $this->db->prepare("INSERT INTO trips (train_id, source, destination) VALUES (:train_id, :source, :destination)");
            $tripStmt->execute([':train_id' => $train->train_id, ':source' => $source, ':destination' => $destination]);
            $trip_id = $this->db->lastInsertId();

            $ticketStmt = $this->db->prepare("INSERT INTO ticket (User_id, trip_id, ticket_type, class, price, purchase_time)
                                              VALUES (:User_id, :trip_id, :ticket_type, :class, :price, :purchase_time)");
            $ticketStmt->execute([
                ':User_id' => $User_id,
                ':trip_id' => $trip_id,
                ':ticket_type' => $ticket_type,
                ':class' => $class,
                ':price' => $price,
                ':purchase_time' => $purchase_time
            ]);

            $this->db->prepare("UPDATE users SET Balance = Balance - :price WHERE User_id = :User_id")
                     ->execute([':price' => $price, ':User_id' => $User_id]);

            return "Ticket booked!";
        } else {
            return "Insufficient Balance.";
        }
    }

    public function printTicket($ticket_id) {
        $sql = "SELECT t.*, u.User_name, tr.source, tr.destination 
                FROM ticket t 
                JOIN users u ON t.User_id = u.User_id
                JOIN trips tr ON t.trip_id = tr.trip_id
                WHERE t.ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticket_id]);
        $ticket = $stmt->fetch();

        if ($ticket) {
            echo "Ticket for {$ticket->User_name}, From {$ticket->source} to {$ticket->destination}, Class: {$ticket->class}, Price: {$ticket->price}";
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

?>