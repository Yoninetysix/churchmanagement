<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Get logged-in user's ID
$user_id = $_SESSION['id'];

// Fetch service request history for the logged-in user
$stmt = $pdo->prepare("SELECT request_type, request_date, request_status FROM service_requests WHERE user_id = ? ORDER BY request_date DESC");
$stmt->execute([$user_id]);
$history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Service</title>
    <link rel="stylesheet" href="service_reques.css"> <!-- Link to your CSS file -->
</head>
<body>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Left Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="logo.png" alt="Flocklink Logo" class="logo">
            <h2>FlockLink</h2>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="people.php"><i class="fas fa-users"></i> People</a></li>
                <li><a href="groups.php"><i class="fas fa-users-cog"></i> Groups</a></li>
                <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="approve_request.php"><i class="fas fa-certificate"></i> Issuance of certificates</a></li>
                <li><a href="birthday_wishes.php"><i class="fas fa-birthday-cake"></i> Birthday Wishes</a></li>
                <li><a href="profile_edit.php"><i class="fas fa-user-cog"></i> My Account</a></li>
                <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>

    <!-- Right Sidebar (Service Request Form & History) -->
    <div class="right-sidebar">
        <!-- Left Side: Service Request Form -->
        <div class="left-column">
            <form action="process_request.php" method="POST" id="requestForm">
                <h2>Request a Service</h2>
                
                <!-- Dropdown for choosing request type -->
                <label for="request_type">Choose Service Type:</label>
                <select name="request_type" id="request_type" required>
                    <option value="Prayer">Prayer</option>
                    <option value="Prayer Meeting">Prayer Meeting</option>
                    <option value="Counseling">Counseling</option>
                </select><br><br>

                <!-- Textarea to describe the request -->
                <label for="request_description">Request Description:</label><br>
                <textarea name="request_description" id="request_description" rows="4" cols="50" required></textarea><br><br>

                <!-- Date picker for selecting request date -->
                <label for="request_date">Select Request Date:</label><br>
                <input type="date" name="request_date" id="request_date" required><br><br>

                <!-- Submit button -->
                <button type="submit" name="submit_request" id="submitRequestBtn">Submit Request</button>
            </form>
        </div>

        <!-- Right Side: Service Request History -->
        <div class="right-column">
            <h2>Service Request History</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Service Type</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['request_type']); ?></td>
                            <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                            <td><?php echo htmlspecialchars($request['request_status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Success Modal -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'success') { ?>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Your request has been successfully submitted!</h3>
        </div>
    </div>
<?php } ?>


<script>
// Modal functionality to open and close
const modal = document.getElementById('successModal');
const closeBtn = document.getElementsByClassName("close-btn")[0];

// When the form is submitted successfully (redirect with status=success)
if (window.location.search.includes('status=success')) {
    modal.style.display = "block"; // Show the modal
}

// When the user clicks on the close button, close the modal
closeBtn.onclick = function() {
    modal.style.display = 'none';
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Font Awesome for icons -->
</body>
</html>
