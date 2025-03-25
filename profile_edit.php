<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Fetch the current user's account details
$user_id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle form submission to update user details
if (isset($_POST['save_changes'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $ministry = $_POST['ministry'];

    // Handle file upload for profile image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "profile_pics/";  // Ensure the directory exists
        $profile_image = basename($_FILES["profile_image"]["name"]);  // Get file name
        $target_file = $target_dir . $profile_image;  // Full file path
        
        // Ensure only image files are uploaded
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_extension, ['jpg', 'png', 'jpeg'])) {
            // Move the uploaded file to the server
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                // If file uploaded successfully, update the profile_image field in the database
                $update_stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $update_stmt->execute([$profile_image, $user_id]);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // If password is entered, hash it
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If password is empty, keep the current one
        $password = $user['password'];
    }

    // Update user data in the database
    $update_stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ?, password = ?, ministry = ? WHERE id = ?");
    $update_stmt->execute([$first_name, $last_name, $email, $mobile_number, $address, $password, $ministry, $user_id]);

    // Reload the page to reflect updated details
    header("Location: profile_edit.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="profile_edits.css"> <!-- Link to your CSS file -->
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

    <!-- Right Side: User Account Details -->
    <div class="account-details">
        <h2>Edit User Account</h2>

        <!-- Profile Container -->
        <form action="profile_edit.php" method="POST" enctype="multipart/form-data">
    <div class="profile-container">
        <!-- Left Side: Profile Picture -->
        <div class="profile-picture">
            <img src="profile_pics/<?php echo $user['profile_image']; ?>" alt="Profile Picture" class="profile-img">
            <div class="upload-btn">
                <label for="file-upload" class="upload-label">Click here to upload new image</label>
                <input type="file" id="file-upload" name="profile_image" accept="image/*">
            </div>
        </div>

        <!-- Right Side: Account Information -->
        <div class="profile-details">
            <div class="user-info">
                <label for="username">Username:</label>
                <p><?php echo $user['username']; ?></p>

                <label for="email">Email:</label>
                <p><?php echo $user['email']; ?></p>

                <label for="job_role">Job Role:</label>
                <p><?php echo $user['job_role']; ?></p>
            </div>
        </div>
    </div>

    <!-- Bottom Part: Two Columns for User Details -->
    <div class="two-columns">
        <div>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required><br>

            <label for="mobile_number">Mobile Number:</label>
            <input type="text" id="mobile_number" name="mobile_number" value="<?php echo $user['mobile_number']; ?>" required><br>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $user['address']; ?>" required><br>
        </div>

        <div>
            <label for="password">New Password (Leave blank if unchanged):</label>
            <input type="password" id="password" name="password" placeholder="Enter new password"><br>

            <label for="ministry">Ministry:</label>
            <input type="text" id="ministry" name="ministry" value="<?php echo $user['ministry']; ?>"><br>

            <button type="submit" name="save_changes">Save Changes</button>
        </div>
    </div>
</form>


    </div>
</div>

</body>
</html>
