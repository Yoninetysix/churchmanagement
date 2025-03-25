<?php
// db_connection.php

$host = 'localhost';     // Database host (usually localhost)
$dbname = 'church_management'; // Database name
$username = 'root';      // Database username (default for XAMPP is 'root')
$password = '';          // Database password (default for XAMPP is empty)

try {
    // Create a new PDO instance for database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception for debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // If the connection fails, display an error message
    die("Connection failed: " . $e->getMessage());
}
?>
