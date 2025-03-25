<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php'; // Include the db_connection.php file

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare delete query
    $sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "User deleted successfully!";
        header('Location: member.php'); // Redirect back to the member page
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "No user ID specified!";
}

$pdo = null;
?>
