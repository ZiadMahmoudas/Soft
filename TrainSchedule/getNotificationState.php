<?php
header('Content-Type: application/json');

try {
    $filePath = 'notification.json';

    if (file_exists($filePath)) {
        $notificationData = json_decode(file_get_contents($filePath), true);

        if ($notificationData['updated']) {
            // Reset the notification state after fetching
            $notificationData['updated'] = false;
            file_put_contents($filePath, json_encode($notificationData));

            echo json_encode(['message' => $notificationData['message']]);
        } else {
            echo json_encode(['message' => null]);
        }
    } else {
        echo json_encode(['message' => null]);
    }
} catch (Exception $e) {
    error_log("Error fetching notification state: " . $e->getMessage());
    echo json_encode(['message' => null]);
}
?>
