<?php
require_once '../../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Source = $_POST['Source'];
    $Destination = $_POST['Destination'];
    $Class = $_POST['Class'];
    $Ticket_type = $_POST['Ticket_type'];

    // Example logic for calculating price based on distance
    $distance = abs(ord($Destination[0]) - ord($Source[0])) * 50; // Example: distance based on ASCII difference
    $base_price = $distance;

    // Adjust price based on Class
    if ($Class === 'VIP') {
        $base_price *= 1.5; // VIP is 50% more expensive
    }

    // Adjust price for round-trip
    if ($Ticket_type === 'Round-trip') {
        $base_price *= 2;
    }

    echo json_encode(['price' => $base_price]);
}
?>
