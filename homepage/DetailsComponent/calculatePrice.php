<?php
require_once '../../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $class = $_POST['class'];
    $ticket_type = $_POST['ticket_type'];

    // Example logic for calculating price based on distance
    $distance = abs(ord($destination[0]) - ord($source[0])) * 50; // Example: distance based on ASCII difference
    $base_price = $distance;

    // Adjust price based on class
    if ($class === 'VIP') {
        $base_price *= 1.5; // VIP is 50% more expensive
    }

    // Adjust price for round-trip
    if ($ticket_type === 'Round-trip') {
        $base_price *= 2;
    }

    echo json_encode(['price' => $base_price]);
}
?>
