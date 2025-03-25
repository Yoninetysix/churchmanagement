<?php
session_start();

// Ensure the user is logged in and is a pastor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pastor') {
    header('Location: login.php');
    exit();
}

include 'db_connection.php'; // Database connection file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Load PHPMailer

// Fetch pending certificate requests
$stmt = $pdo->prepare("SELECT cr.id, cr.user_id, cr.certificate_type, cr.reason, cr.nic_number, cr.full_name, cr.address, cr.gender, cr.request_date, u.email, u.mobile_number
                        FROM certificate_requests cr
                        JOIN users u ON cr.user_id = u.id
                        WHERE cr.status = 'pending'");
$stmt->execute();
$requests = $stmt->fetchAll();

// Handle approval
if (isset($_POST['approve_request'])) {
    $request_id = $_POST['request_id'];
    $pastor_id = $_SESSION['id']; // The ID of the logged-in pastor

    // Get certificate_type of the request to include in the approval message
    $stmt = $pdo->prepare("SELECT certificate_type FROM certificate_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $certificate_request = $stmt->fetch();
    $certificate_type = $certificate_request['certificate_type']; // Get the certificate type for later use

    // Update the request status to 'approved'
    $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'approved', approval_date = NOW(), approved_by = ? WHERE id = ?");
    $stmt->execute([$pastor_id, $request_id]);

    // Get the details of the user who requested the certificate
    $stmt = $pdo->prepare("SELECT u.email, u.first_name FROM certificate_requests cr JOIN users u ON cr.user_id = u.id WHERE cr.id = ?");
    $stmt->execute([$request_id]);
    $user = $stmt->fetch();

    // Send email notification to the user about approval
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'systemcheckmy@gmail.com';  // Replace with your Gmail address
        $mail->Password = 'ezid fxvx yhew dvnj';  // Replace with your Gmail password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('systemcheckmy@gmail.com', 'WP Church');
        $mail->addAddress($user['email'], $user['first_name']);  // Recipient's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Certificate Request Has Been Approved';
        $mail->Body    = "Dear " . $user['first_name'] . ",<br>Your request for a " . $certificate_type . " has been approved.";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }

    // Success message - notify the pastor that the approval is successful
    echo '<script type="text/javascript">window.onload = function() { document.getElementById("success-popup").style.display = "flex"; }</script>';
}

// Handle declined request
if (isset($_POST['decline_request'])) {
    $request_id = $_POST['request_id'];
    $pastor_id = $_SESSION['id']; // The ID of the logged-in pastor

    // Update the request status to 'declined'
    $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'declined', approval_date = NOW(), approved_by = ? WHERE id = ?");
    $stmt->execute([$pastor_id, $request_id]);

    // Send email to user about the decline
    $stmt = $pdo->prepare("SELECT u.email, u.first_name FROM certificate_requests cr JOIN users u ON cr.user_id = u.id WHERE cr.id = ?");
    $stmt->execute([$request_id]);
    $user = $stmt->fetch();

    // Send email notification about decline
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'systemcheckmy@gmail.com';
        $mail->Password = 'ezid fxvx yhew dvnj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('systemcheckmy@gmail.com', 'WP Church');
        $mail->addAddress($user['email'], $user['first_name']);
        $mail->Subject = 'Your Certificate Request Has Been Declined';
        $mail->Body    = "Dear " . $user['first_name'] . ",<br>Your request for a certificate has been declined.";
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }

    // Success message - notify the pastor that the decline is successful
    echo '<script type="text/javascript">window.onload = function() { document.getElementById("decline-popup").style.display = "flex"; }</script>';
}

// Handle ready for collection
if (isset($_POST['ready_for_collection'])) {
    $request_id = $_POST['request_id'];
    $pastor_id = $_SESSION['id']; // The ID of the logged-in pastor

    // Update the request status to 'ready for collection'
    $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'ready for collection', approval_date = NOW(), approved_by = ? WHERE id = ?");
    $stmt->execute([$pastor_id, $request_id]);

    // Send email to user about the collection status
    $stmt = $pdo->prepare("SELECT u.email, u.first_name FROM certificate_requests cr JOIN users u ON cr.user_id = u.id WHERE cr.id = ?");
    $stmt->execute([$request_id]);
    $user = $stmt->fetch();

    // Send email notification about the collection status
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'systemcheckmy@gmail.com';
        $mail->Password = 'ezid fxvx yhew dvnj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('systemcheckmy@gmail.com', 'WP Church');
        $mail->addAddress($user['email'], $user['first_name']);
        $mail->Subject = 'Your Certificate is Ready for Collection';
        $mail->Body    = "Dear " . $user['first_name'] . ",<br>Your certificate is now ready for collection.";
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }

    // Success message - notify the pastor that the ready for collection status is set
    echo '<script type="text/javascript">window.onload = function() { document.getElementById("ready-popup").style.display = "flex"; }</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="approve_certificates.css">
    <title>Approve Certificate Requests</title>

    <script>
        // Close the success popup when the close button is clicked
        function closePopup() {
            document.getElementById("success-popup").style.display = "none";
            document.getElementById("decline-popup").style.display = "none";
            document.getElementById("ready-popup").style.display = "none";
        }

        // Show only Ready for Collection button after approval
        function showReadyForCollection(requestId) {
            document.getElementById('approve-btn-' + requestId).style.display = 'none';
            document.getElementById('decline-btn-' + requestId).style.display = 'none';
            document.getElementById('ready-btn-' + requestId).style.display = 'inline-block';
        }

        // Hide all buttons and show declined message
        function declineRequest(requestId) {
            document.getElementById('approve-btn-' + requestId).style.display = 'none';
            document.getElementById('decline-btn-' + requestId).style.display = 'none';
            document.getElementById('declined-msg-' + requestId).style.display = 'block';
        }
    </script>
</head>
<body>

<!-- Overlay for Popup -->
<div id="overlay" class="overlay" style="display: none;" onclick="closePopup()"></div>

<!-- Dashboard Container -->
<!-- Dashboard Container -->
<div class="dashboard-container">
        <!-- Left Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="assets/logo.png" alt="Flocklink Logo" class="logo">
            </div>
            <nav>
                <ul>
                <li><a href="pastor_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
<li><a href="member.php"><i class="fas fa-users"></i> People</a></li>
<li><a href="group_manage.php"><i class="fas fa-comments"></i> Groups</a></li>
<li><a href="events.php"><i class="fas fa-calendar-day"></i> Events</a></li>
<li><a href="Tithes.php"><i class="fas fa-hand-holding-usd"></i> Tithes and Offerings</a></li>
<li><a href="approve_request.php"><i class="fas fa-certificate"></i> Issuance of Certificates</a></li>
<li><a href="view_service_req.php"><i class="fas fa-clipboard-list"></i> Service Requests</a></li>
<li><a href="view_budget.php"><i class="fas fa-money-check-alt"></i> Income and Expenses</a></li>
<li><a href="view_property_management.php"><i class="fas fa-home"></i> Church Property</a></li>
<li><a href="view_messages.php"><i class="fas fa-comment-dots"></i> Chat with Members</a></li>
<li><a href="birthday_wishes.php"><i class="fas fa-birthday-cake"></i> Birthday Wishes</a></li>
<li><a href="report.php"><i class="fas fa-chart-line"></i> Reports</a></li>
<li><a href="profile_edit.php"><i class="fas fa-user-cog"></i> My Account</a></li>
<li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

                </ul>
            </nav>
        </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Pending Certificate Requests</h2>

        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Certificate Type</th>
                    <th>Reason</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                        <td><?php echo htmlspecialchars($request['mobile_number']); ?></td>
                        <td><?php echo htmlspecialchars($request['address']); ?></td>
                        <td><?php echo htmlspecialchars($request['certificate_type']); ?></td>
                        <td><?php echo htmlspecialchars($request['reason']); ?></td>
                        <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                        <td>
                            <form action="approve_request.php" method="POST">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="approve_request" id="approve-btn-<?php echo $request['id']; ?>" onclick="showReadyForCollection(<?php echo $request['id']; ?>)">Approve</button>
                                <button type="submit" name="decline_request" id="decline-btn-<?php echo $request['id']; ?>" onclick="declineRequest(<?php echo $request['id']; ?>)">Decline</button>
                                <button type="submit" name="ready_for_collection" id="ready-btn-<?php echo $request['id']; ?>" style="display:none;">Ready for Collection</button>
                                <span id="declined-msg-<?php echo $request['id']; ?>" style="display:none; color: red;">Declined</span>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Success Popup for Approval -->
<div id="success-popup" style="display:none;">
    <div class="popup-content">
        <h3>Your request for a certificate has been approved!</h3>
        <button onclick="closePopup()">Close</button>
    </div>
</div>

<!-- Success Popup for Decline -->
<div id="decline-popup" style="display:none;">
    <div class="popup-content">
        <h3>Your certificate request has been declined.</h3>
        <button onclick="closePopup()">Close</button>
    </div>
</div>

<!-- Success Popup for Ready for Collection -->
<div id="ready-popup" style="display:none;">
    <div class="popup-content">
        <h3>Your certificate is ready for collection!</h3>
        <button onclick="closePopup()">Close</button>
    </div>
</div>

</body>
</html>
