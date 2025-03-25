<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include 'db_connection.php';

// Fetch events from the database
$eventsQuery = $pdo->prepare("SELECT * FROM events ORDER BY event_date ASC");
$eventsQuery->execute();
$events = $eventsQuery->fetchAll();

// Fetch birthdays from the database
$birthdaysQuery = $pdo->prepare("SELECT * FROM users ORDER BY date_of_birth ASC");
$birthdaysQuery->execute();
$birthdays = $birthdaysQuery->fetchAll();

// Handle sending birthday wishes
if (isset($_POST['send_wish'])) {
    $user_id = $_POST['user_id'];
    $wish_message = $_POST['wish_message'];

    // Insert the birthday wish into the database (or email, or any other functionality)
    $stmt = $pdo->prepare("INSERT INTO birthday_wishes (user_id, wish_message, sent_by) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $wish_message, $_SESSION['user_id']]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Event and Birthday</title>
    <link rel="stylesheet" href="calendar.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>WP CHURCH</h2>
        <p><?php echo $_SESSION['username']; ?></p>
    </div>
    <nav>
        <ul>
            <li><a href="home.php">Dashboard</a></li>
            <li><a href="events.php">Services</a></li>
            <li><a href="attendance.php">Attendance</a></li>
            <li><a href="document.php">Documents</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="calendar.php">Calendar</a></li> <!-- Calendar Page Link -->
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="calendar-container">
    <h2>Calendar</h2>

    <div class="calendar-navigation">
        <button id="prev-month">&lt; Prev</button>
        <span id="current-month"></span>
        <button id="next-month">Next &gt;</button>
    </div>

    <!-- Calendar -->
    <div id="calendar"></div>

    <!-- Modal for Birthday Wishes -->
    <div id="birthday-modal" class="modal">
        <div class="modal-content">
            <span id="close-modal-btn" class="close">&times;</span>
            <h3>Send Birthday Wish</h3>
            <form action="calendar.php" method="POST">
                <input type="hidden" id="user-id" name="user_id">
                <textarea name="wish_message" placeholder="Write your birthday wish..." required></textarea><br>
                <button type="submit" name="send_wish">Send Wish</button>
            </form>
        </div>
    </div>

</div>

<script>
// JavaScript for Calendar and Modal

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

// Event and birthday data passed from PHP
const events = <?php echo json_encode($events); ?>;
const birthdays = <?php echo json_encode($birthdays); ?>;

// Function to render the calendar
function updateCalendar() {
    document.getElementById('current-month').innerText = `${months[currentMonth]} ${currentYear}`;
    generateCalendar(events, birthdays, currentMonth, currentYear);
}

// Function to generate the calendar for the selected month and year
function generateCalendar(events, birthdays, month, year) {
    const calendar = document.getElementById('calendar');
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();

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
                dayHTML += `<div class="event" onclick="showEventDetails(${JSON.stringify(event)})">${event.event_title}</div>`;
            }
        });

        // Add birthdays for this day
        birthdays.forEach(user => {
            const birthday = new Date(user.date_of_birth);
            if (birthday.getDate() === day && birthday.getMonth() === month) {
                dayHTML += `<div class="birthday" onclick="openBirthdayModal(${user.id}, '${user.first_name}')">${user.first_name}'s Birthday</div>`;
            }
        });

        dayHTML += `</td>`;
        calendarHTML += dayHTML;
    }

    calendarHTML += `</tr></tbody></table>`;
    calendar.innerHTML = calendarHTML;
}

// Open Birthday Modal to send wish
function openBirthdayModal(userId, userName) {
    document.getElementById('user-id').value = userId; // Set the user ID in the hidden input field
    document.getElementById('birthday-modal').style.display = 'block'; // Show the modal
    // Remove the alert as it's not needed anymore
    // alert('Happy Birthday ' + userName + '!'); // Optional greeting alert
}

// Close the modal when the close button is clicked
document.getElementById('close-modal-btn').addEventListener('click', function() {
    document.getElementById('birthday-modal').style.display = 'none'; // Hide the modal
});

// Prevent form submission from refreshing the page and handle the wish
document.querySelector('.modal form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally
    
    const userId = document.getElementById('user-id').value;
    const wishMessage = document.querySelector('[name="wish_message"]').value;

    // Send the wish via AJAX or other appropriate methods
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('wish_message', wishMessage);

    fetch('calendar.php', { // Send to your PHP backend for processing
        method: 'POST',
        body: formData
    }).then(response => response.json())
    .then(data => {
        // Handle success, close modal, and clear the form
        document.getElementById('birthday-modal').style.display = 'none';  // Close the modal after wish is sent
        alert('Birthday wish sent successfully!');  // Optional: Inform the user that the wish was sent
        document.querySelector('[name="wish_message"]').value = ''; // Clear the input field
    })
    .catch(error => {
        // Handle error
        alert('Something went wrong, please try again.');
    });
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

// Initialize the calendar
document.addEventListener('DOMContentLoaded', function() {
    updateCalendar();
});
</script>

</body>
</html>
