<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include 'db_connection.php';

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch the event details
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update event details
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];

    $stmt = $pdo->prepare("UPDATE events SET event_title = ?, event_description = ?, event_date = ?, event_time = ? WHERE id = ?");
    $stmt->execute([$event_title, $event_description, $event_date, $event_time, $event_id]);

    header("Location: events.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="event-management-container">
    <h2>Edit Event</h2>

    <form action="edit_event.php?id=<?php echo $event['id']; ?>" method="POST">
        <input type="text" name="event_title" value="<?php echo htmlspecialchars($event['event_title']); ?>" placeholder="Event Title" required><br>
        <textarea name="event_description" placeholder="Event Description" required><?php echo htmlspecialchars($event['event_description']); ?></textarea><br>
        <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required><br>
        <input type="time" name="event_time" value="<?php echo $event['event_time']; ?>" required><br>
        <button type="submit">Update Event</button>
    </form>
</div>

</body>
</html>
