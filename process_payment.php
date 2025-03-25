<?php
// Ensure the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

include 'db_connection.php'; // Include your database connection

// Get the user's ID from the session
$user_id = $_SESSION['user_id'];
$donation_type = $_POST['donation_type']; // 'once' or 'monthly'
$amount = $_POST['amount']; // Donation amount (from client)
$orderID = $_POST['orderID'];
$payerID = $_POST['payerID'];

// Get the user's first name and last name from the database
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

$first_name = $user['first_name'];
$last_name = $user['last_name'];
$donation_date = date("Y-m-d H:i:s"); // Current date and time

// Save the payment to the database in the tithes_and_offerings table
$stmt = $pdo->prepare("INSERT INTO tithes_and_offerings 
                        (user_id, first_name, last_name, donation_type, amount, donation_date, order_id, payer_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $first_name, $last_name, $donation_type, $amount, $donation_date, $orderID, $payerID]);

// Generate a receipt (example: 'receipt_123.pdf')
$receipt = 'receipt_' . time() . '.pdf';  // Use current timestamp as a unique identifier for the receipt

// Save the donation in the offering_history table
$stmt = $pdo->prepare("INSERT INTO offering_history 
                        (user_id, first_name, last_name, donation_type, amount, receipt, donation_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $first_name, $last_name, $donation_type, $amount, $receipt, $donation_date]);

// Respond with success
echo json_encode(["status" => "success", "message" => "Donation recorded successfully.", "receipt" => $receipt]);
?>
