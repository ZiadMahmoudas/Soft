<?php
require_once '../../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Source = $_POST['Source'];
    $Destination = $_POST['Destination'];
    $Class = $_POST['Class'];
    $Ticket_type = $_POST['Ticket_type'];


    $distance = abs(ord($Destination[0]) - ord($Source[0])) * 50; //150
     // Alex Dhe  => A=>10    D => 13
    // Example: distance based on ASCII difference
    $base_price = $distance;

    // Adjust price based on Class
    if ($Class === 'VIP') {
        $base_price *= 1.5; 
    }

    // Adjust price for round-trip
    if ($Ticket_type === 'Round-trip') { //450
        $base_price *= 2;
    }

    echo json_encode(['price' => $base_price]);
}
?>
