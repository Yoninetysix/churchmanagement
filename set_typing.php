<?php
session_start();
include 'db_connection.php';

if (isset($_POST['typing'])) {
    $receiver_id = $_SESSION['id'];
    $sender_id = $_POST['sender_id'];

    // Insert typing status into the database or update if already exists
    $stmt = $pdo->prepare("INSERT INTO typing_status (sender_id, receiver_id, typing) 
                           VALUES (?, ?, 1) 
                           ON DUPLICATE KEY UPDATE typing = 1");
    $stmt->execute([$sender_id, $receiver_id]);
    echo json_encode(['status' => 'typing']);
    exit();
}

if (isset($_POST['stop_typing'])) {
    $receiver_id = $_SESSION['id'];
    $sender_id = $_POST['sender_id'];

    // Set typing status to 0 when the user stops typing
    $stmt = $pdo->prepare("UPDATE typing_status SET typing = 0 WHERE sender_id = ? AND receiver_id = ?");
    $stmt->execute([$sender_id, $receiver_id]);
    echo json_encode(['status' => 'stopped']);
    exit();
}
?>
