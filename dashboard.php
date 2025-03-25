<?php 
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Fetch events from the database
$eventsQuery = $pdo->prepare("SELECT * FROM events");
$eventsQuery->execute();
$events = $eventsQuery->fetchAll();

// Fetch birthdays from the database
$birthdaysQuery = $pdo->prepare("SELECT * FROM users");
$birthdaysQuery->execute();
$birthdays = $birthdaysQuery->fetchAll();

// Fetch notifications
$stmt_notifications = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = FALSE ORDER BY created_at DESC");
$stmt_notifications->execute([$_SESSION['id']]);
$notifications = $stmt_notifications->fetchAll();

// Mark notifications as read when the user views them
$stmt_mark_read = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE");
$stmt_mark_read->execute([$_SESSION['id']]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome icons -->
</head>
<body>
    <div class="dashboard-container">
        <!-- Left Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>MABINAY CHURCH</h2>
                <p><?php echo $_SESSION['username']; ?></p> <!-- Display logged-in user's username -->
            </div>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="birthday_wishes.php">Birthday Wishes</a></li>
                    <li><a href="certificate_request.php">Certificate</a></li>
                    <li><a href="approve_request.php">Certificate Req</a></li>
                </ul>
            </nav>
        </div>

        <!-- Right Dashboard Section -->
        <div class="dashboard-content">
            <div class="dashboard-header">
                <h2>Dashboard</h2>
            </div>

            <div class="stats">
                <div class="stat-box">
                    <h3>Total Baptism</h3>
                    <p>329</p>
                </div>
                <div class="stat-box">
                    <h3>Total Users</h3>
                    <p>5</p>
                </div>
                <div class="stat-box">
                    <h3>Total Events</h3>
                    <p>3</p>
                </div>
                <div class="stat-box">
                    <h3>Total Approval Request</h3>
                    <p>2</p>
                </div>
            </div>

            <!-- Notification Section -->
            <div class="notification-section">
                <h3>Recent Notifications</h3>
                <?php if ($notifications): ?>
                    <ul>
                        <?php foreach ($notifications as $notification): ?>
                            <li>
                                <p><strong>Notification:</strong> <?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                <p><small>Received on: <?php echo $notification['created_at']; ?></small></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No new notifications.</p>
                <?php endif; ?>
            </div>

            <!-- Event Section -->
            <div class="event-section">
                <!-- Left: Event Calendar -->
                <div class="event-calendar">
                    <h3>Event Calendar</h3>
                    <div id="calendar-navigation">
                        <button id="prev-month">&lt; Prev</button>
                        <span id="current-month"></span>
                        <button id="next-month">Next &gt;</button>
                    </div>
                    <div id="calendar"></div> <!-- Calendar will be displayed here -->
                </div>

                <!-- Right: Add New Event -->
                <div class="add-new-event">
                    <h3>Add New Event</h3>
                    <form action="add_event.php" method="POST">
                        <input type="text" name="event_title" placeholder="Event Title" required><br>
                        <textarea name="event_description" placeholder="Event Description" required></textarea><br>
                        <input type="date" name="event_date" required><br>
                        <input type="time" name="event_time" required><br>
                        <button type="submit">Save Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal (Popup) -->
    <div id="event-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span id="close-modal" class="close">&times;</span>
            <h2 id="event-title"></h2>
            <p id="event-description"></p>
            <p><strong>Event Date:</strong> <span id="event-date"></span></p>
            <p><strong>Event Time:</strong> <span id="event-time"></span></p>
            <p><strong>Created By:</strong> <span id="event-creator"></span></p>
        </div>
    </div>

    <div class="chat-container">
        <!-- Chatbot Floating Icon -->
        <div id="chatbot-icon" onclick="toggleChat()">
            <img src="chat-icon.png" alt="Chatbot Icon" />
        </div>

        <!-- Chatbot Popup -->
        <div id="chatbot-popup" class="chat-popup">
            <div class="chat-header">
                <span>Chat with us</span>
                <button class="close-btn" onclick="toggleChat()">X</button>
            </div>
            <div class="chat-body" id="chat-body">
                <div class="message bot-message">Hi! How can I assist you today?</div>
            </div>
            <input type="text" id="user-input" placeholder="Type a message..." onkeydown="sendMessage(event)">
        </div>
    </div>

    <script>
    // Pass PHP data to JavaScript as JSON
    const events = <?php echo json_encode($events, JSON_HEX_TAG); ?>;
    const birthdays = <?php echo json_encode($birthdays, JSON_HEX_TAG); ?>;

    // Fix for months is not defined error
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    // Current month navigation state
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    // Function to update and render the calendar
    function updateCalendar() {
        document.getElementById('current-month').innerText = `${months[currentMonth]} ${currentYear}`;
        generateCalendar(events, birthdays, currentMonth, currentYear);
    }

    // Function to generate the calendar for the selected month and year
    function generateCalendar(events, birthdays, month, year) {
        const calendar = document.getElementById('calendar');
        const daysInMonth = new Date(year, month + 1, 0).getDate();  // Get total days in current month
        const firstDay = new Date(year, month, 1).getDay();  // Get first day of the month

        let calendarHTML = `<h3>${months[month]} ${year}</h3><table><thead><tr>`;

        daysOfWeek.forEach(day => {
            calendarHTML += `<th>${day}</th>`;
        });
        calendarHTML += `</tr></thead><tbody><tr>`;

        // Add empty cells for days before the first day of the month
        for (let i = 0; i < firstDay; i++) {
            calendarHTML += `<td></td>`;
        }

        // Add the days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            if ((firstDay + day - 1) % 7 === 0 && day !== 1) {
                calendarHTML += `</tr><tr>`;
            }

            let dayHTML = `<td>${day}`;

            // Add events for this day
            events.forEach(event => {
                const eventDate = new Date(event.event_date);
                if (eventDate.getDate() === day && eventDate.getMonth() === month && eventDate.getFullYear() === year) {
                    dayHTML += `<div class="event" onclick="openEventModal(${JSON.stringify(event)})">${event.event_title}</div>`;
                }
            });

            // Add birthdays for this day
            birthdays.forEach(user => {
                const birthday = new Date(user.date_of_birth);
                if (birthday.getDate() === day && birthday.getMonth() === month) {
                    dayHTML += `<div class="birthday">${user.first_name}'s Birthday</div>`;
                }
            });

            dayHTML += `</td>`;
            calendarHTML += dayHTML;
        }

        calendarHTML += `</tr></tbody></table>`;
        calendar.innerHTML = calendarHTML;
    }

    // Function to open the event modal with event details
    function openEventModal(event) {
        document.getElementById('event-title').innerText = event.event_title;
        document.getElementById('event-description').innerText = event.event_description;
        document.getElementById('event-date').innerText = event.event_date;
        document.getElementById('event-time').innerText = event.event_time;
        document.getElementById('event-creator').innerText = event.created_by;

        // Show the modal
        document.getElementById('event-modal').style.display = 'block';
    }

    // Close the modal when the close button is clicked
    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('event-modal').style.display = 'none';
    });

    // Close the modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('event-modal')) {
            document.getElementById('event-modal').style.display = 'none';
        }
    });

    // Calendar navigation (prev/next month)
    document.getElementById('prev-month').addEventListener('click', function() {
        if (currentMonth === 0) {
            currentMonth = 11;
            currentYear--;
        } else {
            currentMonth--;
        }
        updateCalendar();
    });

    document.getElementById('next-month').addEventListener('click', function() {
        if (currentMonth === 11) {
            currentMonth = 0;
            currentYear++;
        } else {
            currentMonth++;
        }
        updateCalendar();
    });

    // Initial calendar load
    document.addEventListener('DOMContentLoaded', function() {
        updateCalendar();
    });

    function sendMessage(event) {
        if (event.key === 'Enter') {
            const userInput = document.getElementById('user-input').value.trim();
            if (userInput !== "") {
                // Append the user's message
                appendMessage(userInput, 'user');
                document.getElementById('user-input').value = '';  // Clear the input field

                // Debug: Check if the message is sent
                console.log("User message:", userInput); // Log the user message to ensure it is captured

                // Send the message to the backend using Fetch API
                fetch('chatbot_backend.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'user_message': userInput
                    })
                })
                .then(response => response.json())  // Parse the JSON response
                .then(data => {
                    if (data.response) {
                        appendMessage(data.response, 'bot');  // Display the response
                    } else {
                        appendMessage('Sorry, I could not understand that.', 'bot');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    appendMessage('Error: Unable to connect to the server.', 'bot');
                });
            }
        }
    }

    // Append a message to the chat
    function appendMessage(message, sender) {
        const chatBody = document.getElementById('chat-body');
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.classList.add(sender === 'user' ? 'user-message' : 'bot-message');
        messageElement.innerText = message;
        chatBody.appendChild(messageElement);

        // Scroll to the bottom of the chat
        chatBody.scrollTop = chatBody.scrollHeight;
    }
</script>

</body>
</html>
