<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';  // Database connection file

// Fetch upcoming events
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll();

// Handle attendance marking
if (isset($_POST['mark_attendance'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['id'];
    $status = 'Present';
    $attended_at = date('Y-m-d H:i:s');

    // Check if the user already marked attendance
    $checkStmt = $pdo->prepare("SELECT * FROM attendance WHERE event_id = ? AND user_id = ?");
    $checkStmt->execute([$event_id, $user_id]);
    $attendance = $checkStmt->fetch();

    if ($attendance) {
        // If already attended, update attendance
        $updateStmt = $pdo->prepare("UPDATE attendance SET status = ?, attended_at = ? WHERE event_id = ? AND user_id = ?");
        $updateStmt->execute([$status, $attended_at, $event_id, $user_id]);
        echo "<script>alert('Attendance updated successfully!');</script>";
    } else {
        // If not marked, insert attendance
        $insertStmt = $pdo->prepare("INSERT INTO attendance (event_id, user_id, status, attended_at) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$event_id, $user_id, $status, $attended_at]);
        echo "<script>alert('Attendance marked successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="attendance.css">
</head>
<body>

<div class="container">
    <h2>Upcoming Events</h2>
    <table>
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Event Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['event_title']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?></td>
                    <td><?php echo htmlspecialchars($event['event_time']); ?></td>
                    <td>
                        <form action="user_attendance.php" method="POST">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <button type="submit" name="mark_attendance">Mark Attendance</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
