<?php
session_start();


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Fetch data from the database
$eventsQuery = $pdo->prepare("SELECT * FROM events");
$eventsQuery->execute();
$events = $eventsQuery->fetchAll();

$birthdaysQuery = $pdo->prepare("SELECT * FROM users");
$birthdaysQuery->execute();
$birthdays = $birthdaysQuery->fetchAll();

$notificationsQuery = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = FALSE ORDER BY created_at DESC");
$notificationsQuery->execute([$_SESSION['id']]);
$notifications = $notificationsQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <link rel="stylesheet" href="pastor_dashboardd.css">
</head>
<body>



    
  
<div class="dashboard-container">
      
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

        <!-- Right Content Section -->
        <div class="dashboard-content">
        <div class="header">
    <div class="church-info">
        <h2>Lighthouse Church</h2>
        <p>Kandy, Sri Lanka</p>
    </div>
    <div class="stats">
        <div class="stat-item">
            <div class="number">6</div>
            <div class="icon">
                <img src="assets/icon8-04.png" alt="Pastors Icon">
            </div>
            <div class="name">Pastors</div>
        </div>
        <div class="stat-item">
            <div class="number">10</div>
            <div class="icon">
                <img src="assets/icon8-03.png" alt="Events Icon">
            </div>
            <div class="name">Events</div>
        </div>
        <div class="stat-item">
            <div class="number">10</div>
            <div class="icon">
                <img src="assets/icon8-02.png" alt="Groups Icon">
            </div>
            <div class="name">Groups</div>
        </div>
        <div class="stat-item">
            <div class="number">10</div>
            <div class="icon">
                <img src="assets/icon8-01.png" alt="Sunday Schools Icon">
            </div>
            <div class="name">Sunday Schools</div>
        </div>
    </div>
</div>



<div class="container">
    <!-- Left Column -->
    <div class="left-column">
    <!-- People Count -->
    <div class="people-count">
        <div class="people-number">
            <span>106</span>
            <p>PEOPLE</p>
        </div>
        <div class="people-details">
            <div class="stat-item">
                <h4>Adults</h4>
                <p>84</p>
            </div>
            <div class="stat-item">
                <h4>Children</h4>
                <p>22</p>
            </div>
        </div>
    </div>

    <!-- Requests -->
    <div class="request-section">
        <div class="request-item">
            <span>Certificate Request</span>
            <div class="request-number">
                <span>22</span>
            </div>
        </div>
        <div class="request-item">
            <span>Service Request</span>
            <div class="request-number">
                <span>22</span>
            </div>
        </div>
        <div class="request-item">
            <span>Message Request</span>
            <div class="request-number">
                <span>22</span>
            </div>
        </div>
    </div>
</div>

    <!-- Center Column -->
    <div class="center-column">
    <!-- Upcoming Event -->
    <div class="upcoming-event">
        <h3>Upcoming Event</h3>
        <div class="event-card">
            <h4>Event Title</h4>
            <p>Event Date and Time</p>
        </div>
    </div>

    <!-- Group Message Notifications -->
    <div class="group-messages">
        <h3>Group Messages</h3>
        <div class="message-item">
            <div class="message-icon">
                <img src="message-icon.png" alt="Message Icon">
            </div>
            <div class="message-details">
                <p>Name 12-15-2025</p>
                <p>Group Message</p>
            </div>
        </div>
        <div class="message-item">
            <div class="message-icon">
                <img src="message-icon.png" alt="Message Icon">
            </div>
            <div class="message-details">
                <p>Name 12-15-2025</p>
                <p>Group Message</p>
            </div>
        </div>
        <div class="message-item">
            <div class="message-icon">
                <img src="message-icon.png" alt="Message Icon">
            </div>
            <div class="message-details">
                <p>Name 12-15-2025</p>
                <p>Group Message</p>
            </div>
        </div>
    </div>
</div>


    <!-- Right Column -->
    <div class="right-column">
    <!-- Birthdays Section -->
    <div class="birthdays">
        <h3>
            <img src="birthday-icon.png" alt="Birthday Icon" class="icon"> Birthdays
        </h3>
        <div class="birthday-item">
            <p>Name 11-30-1996</p>
        </div>
        <div class="birthday-item">
            <p>Name 11-30-1996</p>
        </div>
        <div class="birthday-item">
            <p>Name 11-30-1996</p>
        </div>
    </div>

    <!-- New Signups Section -->
    <div class="new-signups">
        <h3>New Signups</h3>
        <div class="signup-list">
            <img src="profile-pic1.jpg" alt="Profile 1" class="signup-img">
            <img src="profile-pic2.jpg" alt="Profile 2" class="signup-img">
            <img src="profile-pic3.jpg" alt="Profile 3" class="signup-img">
            <img src="profile-pic4.jpg" alt="Profile 4" class="signup-img">
            <img src="profile-pic5.jpg" alt="Profile 5" class="signup-img">
        </div>
        <button class="view-all">View All</button>
    </div>
</div>

</body>
</html>
