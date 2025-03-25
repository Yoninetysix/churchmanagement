<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Fetch the logged-in user's ID from the session
$user_id = $_SESSION['id'];

// Fetch all users except the logged-in user for the user list
$stmt = $pdo->prepare("SELECT id, first_name, last_name, profile_image FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll();

// Fetch messages for the chat if there’s a selected receiver
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id'];

    // Fetch all messages between the logged-in user and the selected user
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
    $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output messages as JSON for the front-end
    echo json_encode($messages);
    exit();
}

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message']) && isset($_POST['receiver_id'])) {
    $message = $_POST['message'];
    $receiver_id = $_POST['receiver_id'];

    // Insert the new message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $receiver_id, $message]);

    // Respond with a success status
    echo json_encode(['status' => 'success']);
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="view_messages.css">
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

    <!-- Right Sidebar (User List & Search Bar) -->
    <div class="right-sidebar">
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search users..." onkeyup="filterUsers()">
        </div>

        <div class="user-list">
            <ul id="user-list">
                <?php foreach ($users as $user): ?>
                    <li class="user-item" onclick="startChat(<?php echo $user['id']; ?>)">
                        <a href="#">
                        <img src="profile_pics/default.png" alt="Profile Image" class="profile-img">
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Chat Panel (optional, right panel would have the chat history) -->
    <div class="chat-panel">
        <div class="chat-header">
            <img src="profile_pics/default.png" alt="Profile Image" class="profile-img">
            <div class="chat-header-info">
                <h3>Selected User</h3>
                <span>Online</span>
            </div>
        </div>

        <div class="chat-history" id="chat-history">
            <!-- Messages will be dynamically inserted here -->
        </div>

        <div class="message-input">
            <textarea id="message" placeholder="Type your message..."></textarea>
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
</div>

<script>
// JavaScript for handling the message sending functionality

let receiverId = null;  // Receiver ID of the selected user

function startChat(id) {
    receiverId = id;
    loadMessages(receiverId);
}

function loadMessages(receiverId) {
    fetch('view_messages.php?receiver_id=' + receiverId)
        .then(response => response.json())
        .then(messages => {
            const chatHistory = document.getElementById('chat-history');
            chatHistory.innerHTML = '';  // Clear previous messages
            messages.forEach(msg => {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', msg.receiver_id == <?php echo $_SESSION['id']; ?> ? 'received' : 'sent');
                messageElement.innerHTML = ` 
                    <div class="message-content">
                        <p>${msg.message}</p>
                        <span class="message-time">${msg.sent_at}</span>
                    </div>
                `;
                chatHistory.appendChild(messageElement);
            });
        });
}

function sendMessage() {
    const message = document.getElementById('message').value;
    if (message.trim() === '') return;

    fetch('view_messages.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(message) + '&receiver_id=' + receiverId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('message').value = '';  // Clear input field
            loadMessages(receiverId);  // Reload messages
        }
    });
}

// Poll for new messages every 3 seconds
setInterval(() => {
    if (receiverId) {
        loadMessages(receiverId);
    }
}, 3000);
</script>

</body>
</html>
