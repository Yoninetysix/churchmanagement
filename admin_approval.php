<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Include PHPMailer

include 'db_connection.php';

// Fetch all users with status 'pending'
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = 'pending'");
$stmt->execute();

// Check if we have any users with 'pending' status
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];

    // Update user status
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $user_id]);

    // Get the user's email address
    $stmt = $pdo->prepare("SELECT email, username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($status == 'approved') {
        // Send email to the user informing them of the approval
        $user_email = $user['email'];
        $subject = "Your Registration Has Been Approved";
        $message = "Dear " . $user['username'] . ",\n\nYour registration has been successfully approved. You are now a registered member.\n\nThank you.";

        // Send email to user
        sendEmail($user_email, $subject, $message);
    }

    // Redirect back to admin approval page
    header('Location: admin_approval.php');
    exit();
}

// Check if users are available for displaying
if ($users === false || empty($users)) {
    echo "No pending users found.";
}

// Function to send emails using PHPMailer
function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                          // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'systemcheckmy@gmail.com';               // SMTP username
        $mail->Password   = 'ezid fxvx yhew dvnj';                        // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('systemcheckmy@gmail.com', 'Smart Church Management');
        $mail->addAddress($to);                                      // Recipient's email address

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Approval</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>User Approvals</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>

            <?php
            // Display users if available
            if (!empty($users)) {
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>
                            <form action='admin_approval.php' method='POST'>
                                <input type='hidden' name='user_id' value='" . $user['id'] . "'>
                                <select name='status'>
                                    <option value='approved'>Approve</option>
                                    <option value='rejected'>Reject</option>
                                </select>
                                <input type='submit' value='Update'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </div>
</body>
</html>
