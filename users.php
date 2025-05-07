<?php
abstract class Person {
    protected $db;
    protected $name;
    protected $password;

    public function __construct() {
        $this->db = DBConnection::getInstance()->getConnection();
    }

    abstract public function login($name, $password);
    abstract public function signup($name, $address, $password);
    abstract public function logout();
}

class DBConnection {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=softwareproject", "root", "");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    public function login($name, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_name = :name AND password = :password");
        $stmt->execute([':name' => $name, ':password' => $password]);
        return $stmt->fetch() ?: null;
    }

    public function signup($name, $address, $password) {
        $stmt = $this->db->prepare("INSERT INTO users (user_name, address, password, balance, admin_id)
                                    VALUES (:name, :address, :password, 0.00, 1)");
        $stmt->execute([':name' => $name, ':address' => $address, ':password' => $password]);
    }

    public function logout() {
        session_start();
        session_destroy();
    }

    public function updateUser($user_id, $new_address, $new_password) {
        $stmt = $this->db->prepare("UPDATE users SET address = :address, password = :password WHERE user_id = :user_id");
        $stmt->execute([':address' => $new_address, ':password' => $new_password, ':user_id' => $user_id]);
    }

    public function deleteUser($user_id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }

    public function checkBalance($user_id) {
        $stmt = $this->db->prepare("SELECT balance FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch();
    }
}

class Admin extends Person {
    public function login($name, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE admin_name = :name AND password = :password");
        $stmt->execute([':name' => $name, ':password' => $password]);
        return $stmt->fetch() ?: null;
    }

    public function signup($name, $address, $password) {
        $stmt = $this->db->prepare("INSERT INTO admin (admin_name, address, password) VALUES (:name, :address, :password)");
        $stmt->execute([':name' => $name, ':address' => $address, ':password' => $password]);
    }

    public function logout() {
        session_start();
        session_destroy();
    }

    public function updateUserBalanceByAdmin($admin_id, $user_id, $new_balance) {
        $adminCheck = $this->db->prepare("SELECT * FROM admin WHERE admin_id = :admin_id");
        $adminCheck->execute([':admin_id' => $admin_id]);

        if ($adminCheck->fetch()) {
            $stmt = $this->db->prepare("UPDATE users SET balance = :balance WHERE user_id = :user_id");
            $stmt->execute([':balance' => $new_balance, ':user_id' => $user_id]);
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

    public function bookTicket($user_id, $source, $destination, $class, $ticket_type, $purchase_time) {
        $price = match ($class) {
            'Economy' => 100.00,
            'VIP' => 250.00,
            default => 0
        };
        if ($ticket_type === 'Round-trip') $price *= 2;

        $balance = (new User())->checkBalance($user_id);
        if ($balance && $balance->balance >= $price) {
            $trainStmt = $this->db->query("SELECT train_id FROM trains LIMIT 1");
            $train = $trainStmt->fetch();

            $tripStmt = $this->db->prepare("INSERT INTO trips (train_id, source, destination) VALUES (:train_id, :source, :destination)");
            $tripStmt->execute([':train_id' => $train->train_id, ':source' => $source, ':destination' => $destination]);
            $trip_id = $this->db->lastInsertId();

            $ticketStmt = $this->db->prepare("INSERT INTO ticket (user_id, trip_id, ticket_type, class, price, purchase_time)
                                              VALUES (:user_id, :trip_id, :ticket_type, :class, :price, :purchase_time)");
            $ticketStmt->execute([
                ':user_id' => $user_id,
                ':trip_id' => $trip_id,
                ':ticket_type' => $ticket_type,
                ':class' => $class,
                ':price' => $price,
                ':purchase_time' => $purchase_time
            ]);

            $this->db->prepare("UPDATE users SET balance = balance - :price WHERE user_id = :user_id")
                     ->execute([':price' => $price, ':user_id' => $user_id]);

            return "Ticket booked!";
        } else {
            return "Insufficient balance.";
        }
    }

    public function printTicket($ticket_id) {
        $sql = "SELECT t.*, u.user_name, tr.source, tr.destination 
                FROM ticket t 
                JOIN users u ON t.user_id = u.user_id
                JOIN trips tr ON t.trip_id = tr.trip_id
                WHERE t.ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticket_id]);
        $ticket = $stmt->fetch();

        if ($ticket) {
            echo "Ticket for {$ticket->user_name}, From {$ticket->source} to {$ticket->destination}, Class: {$ticket->class}, Price: {$ticket->price}";
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
        echo " Station '$name' in '$city' added successfully." . PHP_EOL;
    }

    public function getAllStations() {
        $sql = "SELECT * FROM station";
        $stmt = $this->db->query($sql);
        $stations = $stmt->fetchAll();

        echo " Stations List:" . PHP_EOL;
        foreach ($stations as $station) {
            echo "ID: $station->station_id, Name: $station->station_name, City: $station->city" . PHP_EOL;
        }
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