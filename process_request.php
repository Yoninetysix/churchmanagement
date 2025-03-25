<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    // Get the submitted form data
    $request_type = $_POST['request_type'];
    $request_description = $_POST['request_description'];
    $request_date = $_POST['request_date']; // Get the selected request date
    
    // Get the logged-in user's ID
    $user_id = $_SESSION['id'];
    
    // Insert the request into the database
    $stmt = $pdo->prepare("INSERT INTO service_requests (user_id, request_type, request_description, request_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $request_type, $request_description, $request_date]);

    // Redirect with success status
    header("Location: service_request.php?status=success");
    exit();
}
?>
