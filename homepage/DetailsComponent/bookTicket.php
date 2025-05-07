<?php
require_once '../../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $class = $_POST['class'];
    $ticket_type = $_POST['ticket_type'];
    $purchase_time = date('Y-m-d H:i:s');

    $ticket = new Ticket();
    $result = $ticket->bookTicket(1, $source, $destination, $class, $ticket_type, $purchase_time); // Replace 1 with dynamic user_id

    if ($result === "Ticket booked!") {
        echo json_encode([
            'status' => 'success',
            'message' => $result,
            'ticket' => [
                'source' => $source,
                'destination' => $destination,
                'class' => $class,
                'ticket_type' => $ticket_type,
                'purchase_time' => $purchase_time,
                'price' => $_POST['price'] // Pass the price from the frontend
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result]);
    }
}
?>
