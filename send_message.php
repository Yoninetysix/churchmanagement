<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Handle new message sending
if (isset($_POST['message'])) {
    $receiver_id = $_POST['receiver_id'];  // Get the receiver ID from POST
    $message = $_POST['message'];  // Get the message text from POST
    $sender_id = $_SESSION['id'];  // Get the sender ID from session

    // Insert the new message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at) 
                           VALUES (?, ?, ?, NOW())");
    $stmt->execute([$sender_id, $receiver_id, $message]);

    // Return a success response
    echo json_encode(["status" => "success"]);
    exit();
}
?>
