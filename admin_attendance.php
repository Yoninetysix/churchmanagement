<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';  // Include your database connection file

// Fetch all attendance records with user and event details
$stmt = $pdo->prepare("SELECT a.id, u.first_name, u.last_name, u.role, u.ministry, a.status, a.attended_at, e.event_title, e.event_date, e.event_time
                        FROM attendance a
                        JOIN users u ON a.user_id = u.id
                        JOIN events e ON a.event_id = e.id
                        ORDER BY a.attended_at DESC");
$stmt->execute();
$attendance = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="attendance.css">  <!-- Link to your CSS file -->
</head>
<body>

<!-- Sidebar and Main Container -->
<div class="container">
    <h2>Attendance Management</h2>
    <table>
        <thead>
            <tr>
                <th>User Name</th>
                <th>Role</th>
                <th>Ministry</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Event Time</th>
                <th>Status</th>
                <th>Attended At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendance as $attend): ?>
                <tr>
                    <!-- Full name display (first_name and last_name) -->
                    <td><?php echo htmlspecialchars($attend['first_name'] . ' ' . $attend['last_name']); ?></td>

                    <!-- User role and ministry display -->
                    <td><?php echo htmlspecialchars($attend['role']); ?></td>
                    <td><?php echo htmlspecialchars($attend['ministry']); ?></td>

                    <!-- Event title, date and time display -->
                    <td><?php echo htmlspecialchars($attend['event_title']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($attend['event_date'])); ?></td> <!-- Date Format -->
                    <td><?php echo htmlspecialchars($attend['event_time']); ?></td>

                    <!-- Status display -->
                    <td><?php echo htmlspecialchars($attend['status']); ?></td>

                    <!-- Attendance status: If 'Pending', display 'Not Marked'; otherwise show the attendance date -->
                    <td>
                        <?php 
                            // If status is 'Pending', show 'Not Marked'
                            if ($attend['status'] == 'Pending') {
                                echo 'Not Marked';
                            } else {
                                // Show attended date and time, else mark it as "Not Marked"
                                echo $attend['attended_at'] ? date('Y-m-d H:i', strtotime($attend['attended_at'])) : 'Not Marked';
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
