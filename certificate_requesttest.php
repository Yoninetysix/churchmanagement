<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php'; // Database connection file

// PHPMailer integration for sending email notifications
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // If you're using Composer

// Handle certificate request
if (isset($_POST['request_certificate'])) {
    $user_id = $_SESSION['id'];  // Get logged-in user ID
    $certificate_type = $_POST['certificate_type'];
    $reason = $_POST['reason'];
    $nic_number = $_POST['nic_number'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    // Baptism certificate-specific fields
    $baptism_date = $_POST['baptism_date'] ?? null;
    $place_of_baptism = $_POST['place_of_baptism'] ?? null;
    $parents_names = $_POST['parents_names'] ?? null;
    $godparents_names = $_POST['godparents_names'] ?? null;
    $minister_name = $_POST['minister_name'] ?? null;

    // Marriage certificate-specific fields
    $groom_full_name = $_POST['groom_full_name'] ?? null;
    $bride_full_name = $_POST['bride_full_name'] ?? null;
    $wedding_date = $_POST['wedding_date'] ?? null;
    $place_of_marriage = $_POST['place_of_marriage'] ?? null;
    $witnesses_names = $_POST['witnesses_names'] ?? null;
    $marriage_license_number = $_POST['marriage_license_number'] ?? null;

    // Membership certificate-specific fields
    $membership_date = $_POST['membership_date'] ?? null;
    $church_name = $_POST['church_name'] ?? null;

    // Insert request into the database (certificate_requests table)
    $stmt = $pdo->prepare("INSERT INTO certificate_requests 
                            (user_id, certificate_type, reason, nic_number, full_name, address, gender) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $certificate_type, $reason, $nic_number, $full_name, $address, $gender]);

    // Get the ID of the inserted request
    $request_id = $pdo->lastInsertId();

    // Insert into the relevant certificate table
    if ($certificate_type == 'Baptism Certificate') {
        $stmt = $pdo->prepare("INSERT INTO baptism_certificates 
                            (certificate_request_id, baptism_date, place_of_baptism, parents_names, godparents_names, minister_name) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$request_id, $baptism_date, $place_of_baptism, $parents_names, $godparents_names, $minister_name]);
    } elseif ($certificate_type == 'Marriage Certificate') {
        $stmt = $pdo->prepare("INSERT INTO marriage_certificates 
                            (certificate_request_id, groom_full_name, bride_full_name, wedding_date, place_of_marriage, witnesses_names, marriage_license_number) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$request_id, $groom_full_name, $bride_full_name, $wedding_date, $place_of_marriage, $witnesses_names, $marriage_license_number]);
    } elseif ($certificate_type == 'Membership Certificate') {
        $stmt = $pdo->prepare("INSERT INTO membership_certificates 
                            (certificate_request_id, membership_date, church_name) 
                            VALUES (?, ?, ?)");
        $stmt->execute([$request_id, $membership_date, $church_name]);
    }

    // Send email to pastor (using PHPMailer)
    try {
        $mail = new PHPMailer(true);  // Instantiate PHPMailer
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'systemcheckmy@gmail.com';  // Replace with your email
        $mail->Password = 'ezid fxvx yhew dvnj';  // Replace with your email password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('systemcheckmy@gmail.com', 'Church Management');
        $mail->addAddress('Yohanwork.lk@gmail.com');  // Replace with the pastor's email address

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'New Certificate Request';
        $mail->Body    = "User with ID $user_id has requested a $certificate_type certificate. <br><br> Details: $full_name";

        // Send email
        $mail->send();

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Show the success message using JavaScript
    // Set the success message to show the modal
    echo '<script type="text/javascript">window.onload = function() { document.getElementById("success-popup").style.display = "flex"; }</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Certificate_Request.css">
    <title>Request Certificate</title>

    <script>
        // JavaScript to show relevant fields based on certificate type selection
        function toggleCertificateFields() {
            const certificateType = document.getElementById('certificate_type').value;
            const allFields = document.querySelectorAll('.certificate-field');
            allFields.forEach(field => field.style.display = 'none');
            
            if (certificateType === 'Baptism Certificate') {
                document.querySelectorAll('.baptism-fields').forEach(field => field.style.display = 'block');
            } else if (certificateType === 'Marriage Certificate') {
                document.querySelectorAll('.marriage-fields').forEach(field => field.style.display = 'block');
            } else if (certificateType === 'Membership Certificate') {
                document.querySelectorAll('.membership-fields').forEach(field => field.style.display = 'block');
            }
        }

        // Close the success popup when the close button is clicked
        function closeSuccessPopup() {
            document.getElementById("success-popup").style.display = "none";
        }
    </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>WP CHURCH</h2>
        <p><?php echo $_SESSION['username']; ?></p>
    </div>
    <nav>
        <ul>
            <li><a href="home.php">Dashboard</a></li>
            <li><a href="events.php">Services</a></li>
            <li><a href="attendance.php">Attendance</a></li>
            <li><a href="document.php">Documents</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="birthday_wishes.php">Birthday Wishes</a></li>
            <li><a href="certificate_request.php">Request Certificate</a></li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Request Certificate</h2>

    <form action="certificate_request.php" method="POST">
        <label for="certificate_type">Select Certificate Type:</label>
        <select id="certificate_type" name="certificate_type" required onchange="toggleCertificateFields()">
            <option value="Marriage Certificate">Marriage Certificate</option>
            <option value="Baptism Certificate">Baptism Certificate</option>
            <option value="Membership Certificate">Membership Certificate</option>
        </select><br>

        <label for="reason">Reason for Request:</label>
        <input type="text" name="reason" required><br>

        <label for="nic_number">NIC Number:</label>
        <input type="text" name="nic_number" required><br>

        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address"><br>

        <label for="gender">Gender:</label>
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select><br>

        <!-- Baptism Certificate Specific Fields -->
        <div class="certificate-field baptism-fields" style="display:none;">
            <h3>Baptism Certificate Specific Details</h3>
            <label for="baptism_date">Baptism Date:</label>
            <input type="date" name="baptism_date"><br>

            <label for="place_of_baptism">Place of Baptism:</label>
            <input type="text" name="place_of_baptism"><br>

            <label for="parents_names">Parent's Names:</label>
            <input type="text" name="parents_names"><br>

            <label for="godparents_names">Godparents' Names:</label>
            <input type="text" name="godparents_names"><br>

            <label for="minister_name">Minister's Name:</label>
            <input type="text" name="minister_name"><br>
        </div>

        <!-- Marriage Certificate Specific Fields -->
        <div class="certificate-field marriage-fields" style="display:none;">
            <h3>Marriage Certificate Specific Details</h3>
            <label for="groom_full_name">Full Name of Groom:</label>
            <input type="text" name="groom_full_name"><br>

            <label for="bride_full_name">Full Name of Bride:</label>
            <input type="text" name="bride_full_name"><br>

            <label for="wedding_date">Date of Marriage:</label>
            <input type="date" name="wedding_date"><br>

            <label for="place_of_marriage">Place of Marriage:</label>
            <input type="text" name="place_of_marriage"><br>

            <label for="witnesses_names">Witnesses' Names:</label>
            <input type="text" name="witnesses_names"><br>

            <label for="marriage_license_number">Marriage License Number:</label>
            <input type="text" name="marriage_license_number"><br>
        </div>

        <!-- Membership Certificate Specific Fields -->
        <div class="certificate-field membership-fields" style="display:none;">
            <h3>Membership Certificate Specific Details</h3>
            <label for="membership_date">Membership Date:</label>
            <input type="date" name="membership_date"><br>

            <label for="church_name">Church Name:</label>
            <input type="text" name="church_name"><br>
        </div>

        <button type="submit" name="request_certificate">Request Certificate</button>
    </form>
</div>

<!-- Success Popup -->
<div id="success-popup">
    <div class="popup-content">
        <h3>Your request for a certificate has been submitted!</h3>
        <button onclick="closeSuccessPopup()">Close</button>
    </div>
</div>

</body>
</html>