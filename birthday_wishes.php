<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include 'db_connection.php';

// Fetch the logged-in user's ID based on their username
$userQuery = $pdo->prepare("SELECT id, profile_image FROM users WHERE username = ?");
$userQuery->execute([$_SESSION['username']]);
$user = $userQuery->fetch();
$user_id = $user['id'];  // Get the logged-in user's ID
$profile_image = $user['profile_image'];  // Fetch the profile image

// Fetch upcoming birthdays from the database
$birthdayQuery = $pdo->prepare("SELECT * FROM users WHERE DATE_FORMAT(date_of_birth, '%m-%d') >= DATE_FORMAT(NOW(), '%m-%d') ORDER BY date_of_birth ASC");
$birthdayQuery->execute();
$birthdays = $birthdayQuery->fetchAll();

// Fetch birthday wishes for the logged-in user
$birthdayWishesQuery = $pdo->prepare("SELECT wish_message, sent_by, u.first_name AS sender_name 
                                      FROM birthday_wishes bw 
                                      JOIN users u ON u.id = bw.sent_by 
                                      WHERE bw.user_id = ? ORDER BY bw.created_at DESC");
$birthdayWishesQuery->execute([$user_id]);
$birthdayWishes = $birthdayWishesQuery->fetchAll();

// Handle sending birthday wishes
if (isset($_POST['send_wish'])) {
    $user_id_to = $_POST['user_id']; // User whose birthday it is
    $wish_message = $_POST['wish_message']; // The birthday message

    // Insert the birthday wish into the database
    $stmt = $pdo->prepare("INSERT INTO birthday_wishes (user_id, wish_message, sent_by) VALUES (?, ?, ?)");
    $stmt->execute([$user_id_to, $wish_message, $user_id]); // Use the logged-in user's ID as the sender

    // Optional success message
    echo "Your birthday wish has been sent!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Wishes</title>
    <link rel="stylesheet" href="birthday_wish.css">
</head>
<body>

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
<div class="birthday-container">
    <h2>Upcoming Birthdays</h2>

    <div class="birthday-cards">
        <?php foreach ($birthdays as $user): ?>
            <div class="birthday-card">
                <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'profile_pics/default.png'; ?>" alt="User Profile" class="profile-img">
                <h4><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h4>
                <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>
                <p>Birthday: <?php echo date('F j, Y', strtotime($user['date_of_birth'])); ?></p>
                <button onclick="openBirthdayModal(<?php echo $user['id']; ?>, '<?php echo $user['first_name']; ?>')">Send Greeting</button>
            </div>
        <?php endforeach; ?>
    </div>

    <h3>Your Birthday Wishes</h3>
    <div class="birthday-wishes">
        <?php if (!empty($birthdayWishes)): ?>
            <ul>
                <?php foreach ($birthdayWishes as $wish): ?>
                    <li><strong><?php echo $wish['sender_name']; ?>:</strong> <?php echo htmlspecialchars($wish['wish_message']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No birthday wishes sent to you yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Birthday Wishes Modal -->
<div id="birthday-modal" class="modal">
    <div class="modal-content">
        <span id="close-modal-btn" class="close">&times;</span>
        <h3>Send Birthday Wish</h3>
        <form action="birthday_wishes.php" method="POST">
            <input type="hidden" id="user-id" name="user_id">
            <textarea name="wish_message" placeholder="Write your birthday wish..." required></textarea><br>
            <button type="submit" name="send_wish">Send Wish</button>
        </form>
    </div>
</div>

<script>
// Open Birthday Modal to send wish
function openBirthdayModal(userId, userName) {
    document.getElementById('user-id').value = userId; // Set the user ID in the hidden input field
    document.getElementById('birthday-modal').style.display = 'block'; // Show the modal
}

// Close the modal when the close button is clicked
document.getElementById('close-modal-btn').addEventListener('click', function() {
    document.getElementById('birthday-modal').style.display = 'none'; // Hide the modal
});
</script>

</body>
</html>
