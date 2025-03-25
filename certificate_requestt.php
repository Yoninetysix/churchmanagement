<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Certificate_Request.css">
    <title>Request Certificate</title>
    <script>
        // JavaScript to show relevant fields based on certificate type selection
        function toggleCertificateFields() {
            const certificateType = document.getElementById('certificate_type').value;
            const allFields = document.querySelectorAll('.certificate-field');
            allFields.forEach(field => field.style.display = 'none');
            
            if (certificateType === 'Baptism Certificate') {
                document.querySelectorAll('.baptism-fields').forEach(field => field.style.display = 'block');
            } else if (certificateType === 'Marriage Certificate') {
                document.querySelectorAll('.marriage-fields').forEach(field => field.style.display = 'block');
            } else if (certificateType === 'Membership Certificate') {
                document.querySelectorAll('.membership-fields').forEach(field => field.style.display = 'block');
            }
        }
    </script>
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
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="birthday_wishes.php">Birthday Wishes</a></li>
            <li><a href="certificate_request.php">Request Certificate</a></li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Request Certificate</h2>

    <form action="certificate_request.php" method="POST">
        <label for="certificate_type">Select Certificate Type:</label>
        <select id="certificate_type" name="certificate_type" required onchange="toggleCertificateFields()">
            <option value="Marriage Certificate">Marriage Certificate</option>
            <option value="Baptism Certificate">Baptism Certificate</option>
            <option value="Membership Certificate">Membership Certificate</option>
        </select><br>

        <label for="reason">Reason for Request:</label>
        <input type="text" name="reason" required><br>

        <label for="nic_number">NIC Number:</label>
        <input type="text" name="nic_number" required><br>

        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address"><br>

        <label for="gender">Gender:</label>
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select><br>

        <!-- Baptism Certificate Specific Fields -->
        <div class="certificate-field baptism-fields" style="display:none;">
            <h3>Baptism Certificate Specific Details</h3>
            <label for="baptism_date">Baptism Date:</label>
            <input type="date" name="baptism_date"><br>

            <label for="place_of_baptism">Place of Baptism:</label>
            <input type="text" name="place_of_baptism"><br>

            <label for="parents_names">Parent's Names:</label>
            <input type="text" name="parents_names"><br>

            <label for="godparents_names">Godparents' Names:</label>
            <input type="text" name="godparents_names"><br>

            <label for="minister_name">Minister's Name:</label>
            <input type="text" name="minister_name"><br>
        </div>

        <!-- Marriage Certificate Specific Fields -->
        <div class="certificate-field marriage-fields" style="display:none;">
            <h3>Marriage Certificate Specific Details</h3>
            <label for="groom_full_name">Full Name of Groom:</label>
            <input type="text" name="groom_full_name"><br>

            <label for="bride_full_name">Full Name of Bride:</label>
            <input type="text" name="bride_full_name"><br>

            <label for="wedding_date">Date of Marriage:</label>
            <input type="date" name="wedding_date"><br>

            <label for="place_of_marriage">Place of Marriage:</label>
            <input type="text" name="place_of_marriage"><br>

            <label for="witnesses_names">Witnesses' Names:</label>
            <input type="text" name="witnesses_names"><br>

            <label for="marriage_license_number">Marriage License Number:</label>
            <input type="text" name="marriage_license_number"><br>
        </div>

        <!-- Membership Certificate Specific Fields -->
        <div class="certificate-field membership-fields" style="display:none;">
            <h3>Membership Certificate Specific Details</h3>
            <label for="membership_date">Membership Date:</label>
            <input type="date" name="membership_date"><br>

            <label for="church_name">Church Name:</label>
            <input type="text" name="church_name"><br>
        </div>

        <button type="submit" name="request_certificate">Request Certificate</button>
    </form>

    <!-- Success Popup -->
    <div id="success-message" class="popup" style="display:none;">
        <div class="popup-content">
            <span class="close" onclick="document.getElementById('success-message').style.display='none'">&times;</span>
            <p>Your request for a certificate has been submitted!</p>
        </div>
    </div>
</div>

</body>
</html>
