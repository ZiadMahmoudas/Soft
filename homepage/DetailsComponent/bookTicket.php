<?php
require_once '../../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Source = $_POST['Source'] ?? null;
    $Destination = $_POST['Destination'] ?? null;
    $Class = $_POST['Class'] ?? null;
    $Ticket_type = $_POST['Ticket_type'] ?? null;
    $Price = $_POST['Price'] ?? null;

    if (!$Source || !$Destination || !$Class || !$Ticket_type || !$Price) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $purchase_time = date('Y-m-d H:i:s');
    $User_id = 1; // Replace with dynamic user_id (e.g., from session)

    $user = new User();
    $Balance = $user->checkBalance($User_id);

    if ($Balance && isset($Balance->Balance)) {
        // Debugging: Log the balance to ensure it's being fetched correctly
        error_log("User Balance: " . $Balance->Balance);

        if ($Balance->Balance >= $Price) {
            $ticket = new Ticket();
            $result = $ticket->bookTicket($User_id, $Source, $Destination, $Class, $Ticket_type, $purchase_time);

            if ($result === "Ticket booked!") {
                // Deduct the Price from the user's balance
                $user->updateUserBalance($User_id, $Balance->Balance - $Price);

                echo json_encode([
                    'status' => 'success',
                    'message' => $result,
                    'ticket' => [
                        'Source' => $Source,
                        'Destination' => $Destination,
                        'Class' => $Class,
                        'Ticket_type' => $Ticket_type,
                        'Price' => $Price,
                    ],
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to fetch balance.']);
    }
}
?>
