<?php
header('Content-Type: application/json');


    $filePath = 'notification.json';

    if (file_exists($filePath)) {
        $notificationData = json_decode(file_get_contents($filePath));

        if ($notificationData->updated) {
            $notificationData->updated = false;
            file_put_contents($filePath, json_encode($notificationData));
            echo json_encode(['message' => $notificationData->message]);
        } else {
            echo json_encode(['message' => null]);
        }
    } 
?>
