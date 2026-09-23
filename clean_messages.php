<?php
$token = '8811158752:AAGKAYwneuZuJiQhC02QsnbgUgpAvgZFtHI';
$chatId = '1827362508';

echo "Cleaning previous bot messages for chat {$chatId}...\n";

// Attempt to delete messages in range 200 to 350
$deleted = 0;
for ($id = 350; $id >= 200; $id--) {
    $url = "https://api.telegram.org/bot{$token}/deleteMessage";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['chat_id' => $chatId, 'message_id' => $id]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($res, true);
    if ($json && ($json['ok'] ?? false)) {
        $deleted++;
    }
}

echo "Deleted {$deleted} messages successfully.\n";
