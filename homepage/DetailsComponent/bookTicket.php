<?php
header('Content-Type: application/json');
require_once '../../users.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Source = $_POST['Source'] ?? null;
        $Destination = $_POST['Destination'] ?? null;
        $Class = $_POST['Class'] ?? null;
        $Ticket_type = $_POST['Ticket_type'] ?? null;
        $Price = $_POST['Price'] ?? null;

        if (!isset($_SESSION['USER']) || !isset($_SESSION['USER']->User_id)) {
            throw new Exception('User ID not found in session.');
        }

        $User_id = $_SESSION['USER']->User_id;

        if (!$Source || !$Destination || !$Class || !$Ticket_type || !$Price) {
            throw new Exception('All fields are required.');
        }

        $purchase_time = date('Y-m-d H:i:s');
        $user = new User();
        $Balance = $user->checkBalance($User_id);

        if ($Balance && isset($Balance->Balance)) {
            if ($Balance->Balance >= $Price) {
                $ticket = new Ticket();
                $result = $ticket->bookTicket($User_id, $Source, $Destination, $Class, $Ticket_type, $purchase_time);

                if ($result === "Ticket booked!") {
                    // Deduct the Price from the user's balance
                    $new_Balance = $Balance->Balance - $Price;
                    $user->updateUserBalance($User_id, $new_Balance);

                    echo json_encode([
                        'status' => 'success',
                        'message' => $result,
                        'balance' => $new_Balance,
                        'ticket' => [
                            'Source' => $Source,
                            'Destination' => $Destination,
                            'Class' => $Class,
                            'Ticket_type' => $Ticket_type,
                            'Price' => $Price,
                        ],
                    ]);
                } else {
                    throw new Exception($result);
                }
            } else {
                throw new Exception('Insufficient balance.');
            }
        } else {
            throw new Exception('Unable to fetch balance.');
        }
}
?>
