<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $created_by = $_SESSION['username']; // Assuming user session is set

    // Insert event into the database
    $stmt = $pdo->prepare("INSERT INTO events (event_title, event_description, event_date, event_time, created_by) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$event_title, $event_description, $event_date, $event_time, $created_by]);

    // Redirect back to the dashboard or events page
    header('Location: dashboard.php');
}
?>
