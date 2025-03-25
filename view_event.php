<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Get the current date and time
$currentDate = new DateTime();

// Fetch all events from the database, ordered by event date
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll();

// Find the next event that is in the future
$nextEvent = null;
foreach ($events as $event) {
    $eventDate = new DateTime($event['event_date']);
    if ($eventDate > $currentDate) {
        $nextEvent = $event;
        break;
    }
}

// If a next event is found, calculate the countdown
$timeRemaining = '';
if ($nextEvent) {
    $eventDate = new DateTime($nextEvent['event_date']);
    $interval = $currentDate->diff($eventDate);

    $timeRemaining = [
        'days' => $interval->days,
        'hours' => $interval->h,
        'minutes' => $interval->i,
        'seconds' => $interval->s
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Page</title>
    <link rel="stylesheet" href="view_event.css"> <!-- Link to your CSS file -->
</head>
<body>

<div class="dashboard-container">
        <!-- Left Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="logo.png" alt="Flocklink Logo" class="logo">
                <h2>FlockLink</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="member_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="memberl.php"><i class="fas fa-users"></i> People</a></li>
                    <li><a href="groups.php"><i class="fas fa-users-cog"></i> Groups</a></li>
                    <li><a href="view_event.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="certificate_request.php"><i class="fas fa-certificate"></i> 
                    certificates</a></li>
                    <li><a href="birthday_wishes.php"><i class="fas fa-birthday-cake"></i> Birthday Wishes</a></li>
                    <li><a href="profile_edit.php"><i class="fas fa-user-cog"></i> My Account</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>

    <!-- Right Side: Event Page -->
    <div class="event-page">
        <!-- Slider Section (Top part of event page) -->
        <div class="event-header">
            <div class="slider">
                <img src="assets/banner1.png" alt="Banner 1">
                <img src="assets/banner2.png" alt="Banner 2">
                <img src="assets/banner3.png" alt="Banner 3">
            </div>
        </div>

        <!-- Upcoming Event Section -->
        <div class="upcoming-event">
            <?php if ($nextEvent): ?>
                <div class="event-details">
                    <h2>Next Event: <?php echo htmlspecialchars($nextEvent['event_title']); ?></h2>
                    <p><?php echo date('F j, Y', strtotime($nextEvent['event_date'])); ?></p>
                    <div class="event-countdown">
                        <span><?php echo $timeRemaining['days']; ?> Days</span>
                        <span><?php echo $timeRemaining['hours']; ?> Hrs</span>
                        <span><?php echo $timeRemaining['minutes']; ?> Mins</span>
                        <span><?php echo $timeRemaining['seconds']; ?> Secs</span>
                    </div>
                </div>
                <div class="event-buttons">
                    <button onclick="window.location.href='all_events.php'">All Events</button>
                </div>
            <?php else: ?>
                <p>No upcoming events at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- 3 Box Section -->
        <div class="box-section">
            <div class="box">
                <img src="pastors.jpg" alt="Pastors">
                <h3>Our Pastors</h3>
                <p>Learn more about our pastors and their work.</p>
            </div>
            <div class="box">
                <img src="sermons.jpg" alt="Sermons">
                <h3>Sermons</h3>
                <p>Check out our recent sermons.</p>
            </div>
            <div class="box">
                <img src="worship.jpg" alt="Worships">
                <h3>Worships</h3>
                <p>Join us for inspiring worship sessions.</p>
            </div>
        </div>

        <!-- More Events and Recent Sermons -->
        <div class="event-info-section">
            <!-- More Coming Events -->
            <div class="more-events">
                <h3>More Coming Events</h3>
                <ul class="event-list">
                    <?php foreach ($events as $event): ?>
                        <li>
                            <div>
                                <h4><?php echo htmlspecialchars($event['event_title']); ?></h4>
                                <p class="event-date"><?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            </div>
                            <button class="details-btn">Details</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Recent Sermons -->
            <div class="recent-sermons">
                <h3>Recent Sermons</h3>
                <div class="sermon-box">
                    <h4>How to Recover the Cutting Edge</h4>
                    <video controls>
                        <source src="sermon_video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
