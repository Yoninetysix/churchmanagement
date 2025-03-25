<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Load PHPMailer

include 'db_connection.php';

$role = $_GET['role']; // Get the role from URL parameter

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $job_role = isset($_POST['job_role']) ? $_POST['job_role'] : null;
    $ministry = isset($_POST['ministry']) ? $_POST['ministry'] : null;

    // New fields
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $address = $_POST['address'];
    $mobile_number = $_POST['mobile_number'];
    $join_church_date = $_POST['join_church_date'];
    $baptist_date = $_POST['baptist_date'];

    // Handle Profile Image Upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_tmp = $_FILES['profile_image']['tmp_name'];
        $image_name = $_FILES['profile_image']['name'];
        $image_dir = 'uploads/';
        $profile_image = $image_dir . $image_name;

        move_uploaded_file($image_tmp, $profile_image);  // Move uploaded file to desired directory
    }

    // Insert user data into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, job_role, ministry, first_name, last_name, date_of_birth, address, mobile_number, join_church_date, baptist_date, profile_image) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $username, $email, $password, $role, $job_role, $ministry, 
        $first_name, $last_name, $date_of_birth, $address, $mobile_number, 
        $join_church_date, $baptist_date, $profile_image
    ]);

    // Send email to Admin and Pastor for user registration approval
    $admin_email = "systemcheckmy@gmail.com";  // Change to your admin's email address
    $pastor_email = "Yohanshalo52@gmail.com"; // Change to your pastor's email address
    $subject = "New User Registration Approval Needed";
    $message = "A new user has registered and is awaiting approval. Please review and approve or reject the registration.";

    // Send email using PHPMailer to Admin
    sendEmail($admin_email, $subject, $message);

    // Send email using PHPMailer to Pastor
    sendEmail($pastor_email, $subject, $message);

    // Redirect to a success page or login page after registration
    header('Location: registration_success.php');
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
        $mail->setFrom('no-reply@example.com', 'Smart Church Management');
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
    <title>Register - <?php echo ucfirst($role); ?> </title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <div class="container">
        <!-- Left Side (General Information) -->
        <div class="left-side">
            <h2>Register as a <?php echo ucfirst($role); ?></h2>
            <form action="register.php?role=<?php echo $role; ?>" method="POST" enctype="multipart/form-data">
                <!-- Basic Fields up to Date of Birth -->
                <label for="username">Username:</label>
                <input type="text" name="username" required><br>

                <label for="email">Email:</label>
                <input type="email" name="email" required><br>

                <label for="password">Password:</label>
                <input type="password" name="password" required><br>

                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" required><br>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" required><br>

                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" name="date_of_birth" required><br>
        </div>

        <!-- Right Side (Contact Information) -->
        <div class="right-side">
                <!-- Fields starting with Address -->
                <label for="address">Address:</label>
                <input type="text" name="address" required><br>

                <label for="mobile_number">Mobile Number:</label>
                <input type="text" name="mobile_number" required><br>

                <label for="join_church_date">Join Church Date:</label>
                <input type="date" name="join_church_date" required><br>

                <label for="baptist_date">Baptism Date:</label>
                <input type="date" name="baptist_date" required><br>

                <!-- Profile Image Upload -->
                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image"><br>

                <!-- Job Role and Ministry dropdown -->
                <?php if ($role == 'pastor' || $role == 'board_member'): ?>
                    <label for="job_role">Job Role:</label>
                    <select name="job_role" required>
                        <?php if ($role == 'pastor'): ?>
                            <option value="senior_pastor">Senior Pastor</option>
                            <option value="associate_pastor">Associate Pastor</option>
                            <option value="assistant_pastor">Assistant Pastor</option>
                        <?php elseif ($role == 'board_member'): ?>
                            <option value="chairperson">Chairperson</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                            <option value="board_member">Board Member</option>
                        <?php endif; ?>
                    </select><br>
                <?php endif; ?>

                <!-- Ministry Dropdown -->
                <label for="ministry">Ministry:</label>
                <select name="ministry" required>
                    <option value="church_ministry">Church's Ministry</option>
                    <option value="worship">Worship</option>
                    <option value="youth_ministry">Youth Ministry</option>
                    <option value="childrens_ministry">Children's Ministry</option>
                    <option value="prayer_ministry">Prayer Ministry</option>
                    <option value="evangelism">Evangelism</option>
                    <option value="believer">Believer</option>
                </select><br>

                <input type="submit" value="Register">
            </form>

            <!-- Back Button -->
            <div class="back-button">
                <a href="javascript:history.back()">Back</a>
            </div>

            <!-- Already Registered Message -->
            <div class="login-message">
                <p>Already registered? <a href="login.php">You can login here</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>
