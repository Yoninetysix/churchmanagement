<?php
session_start();
include 'db_connection.php';

// Check if the user is a pastor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pastor') {
    header('Location: login.php');
    exit();
}

// Fetch all pending service requests
$stmt = $pdo->prepare("SELECT * FROM service_requests WHERE request_status = 'pending'");
$stmt->execute();
$requests = $stmt->fetchAll();

// Handle approve action
if (isset($_GET['approve_request_id'])) {
    $request_id = $_GET['approve_request_id'];
    
    // Fetch request details
    $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    // Approve the request in service_requests table
    if (isset($_POST['start_time']) && isset($_POST['end_time'])) {
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        
        // Update request status to approved
        $stmt = $pdo->prepare("UPDATE service_requests SET request_status = 'approved' WHERE id = ?");
        $stmt->execute([$request_id]);
        
        // Add the approved request as an event in the events table
        $stmt = $pdo->prepare("INSERT INTO events (event_title, event_date, event_description, event_time, event_end_time, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$request['request_type'], $request['request_date'], $request['request_description'], $start_time, $end_time, $_SESSION['username']]);
        
        // Redirect back with success
        header('Location: pastor_dashboard.php?status=approved');
        exit();
    }
}

// Handle decline action
if (isset($_GET['decline_request_id'])) {
    $request_id = $_GET['decline_request_id'];

    // Fetch the details of the declined request
    $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    // Check if a decline reason has been submitted
    if (isset($_POST['decline_reason'])) {
        $decline_reason = $_POST['decline_reason'];
        
        // Update the service_requests table with decline reason and status
        $stmt = $pdo->prepare("UPDATE service_requests SET request_status = 'declined', decline_reason = ? WHERE id = ?");
        $stmt->execute([$decline_reason, $request_id]);

        // Redirect with success message
        header('Location: pastor_dashboard.php?status=declined');
        exit();
    }
}

// Fetch history of approved requests
$stmt = $pdo->prepare("SELECT * FROM events WHERE created_by = ? ORDER BY event_date DESC");
$stmt->execute([$_SESSION['id']]);
$history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_service_reqest.css">
    <title>Pastor Dashboard</title>
</head>
<body>

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
    <!-- Right Sidebar (Service Request Approvals) -->
    <div class="right-sidebar">
        <h2>Pending Service Requests</h2>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'approved'): ?>
            <div class="alert success">
                Request approved and event added successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'declined'): ?>
            <div class="alert error">
                Request declined successfully!
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Request Date</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['request_type']); ?></td>
                        <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                        <td><?php echo htmlspecialchars($request['request_description']); ?></td>
                        <td>
                            <a href="?approve_request_id=<?php echo $request['id']; ?>">Approve</a>
                            <a href="?decline_request_id=<?php echo $request['id']; ?>" class="decline-btn">Decline</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Approve Request Form -->
        <?php if (isset($_GET['approve_request_id'])): ?>
            <h3>Set Start and End Time for the Request</h3>
            <form action="?approve_request_id=<?php echo $_GET['approve_request_id']; ?>" method="POST">
                <label for="start_time">Start Time:</label><br>
                <input type="time" name="start_time" required><br><br>

                <label for="end_time">End Time:</label><br>
                <input type="time" name="end_time" required><br><br>

                <button type="submit">Approve Request</button>
            </form>
        <?php endif; ?>

        <!-- Decline Request Form -->
        <?php if (isset($_GET['decline_request_id'])): ?>
            <h3>Provide Decline Reason</h3>
            <form action="?decline_request_id=<?php echo $_GET['decline_request_id']; ?>" method="POST">
                <label for="decline_reason">Decline Reason:</label><br>
                <textarea name="decline_reason" id="decline_reason" rows="4" cols="50" required></textarea><br><br>
                <button type="submit">Submit Decline Reason</button>
            </form>
        <?php endif; ?>

        <!-- Approved Service Request History -->
        <h2>Approved Service Requests History</h2>
        <table>
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Event Date</th>
                    <th>Description</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['event_title']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_description']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_time']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_end_time']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
