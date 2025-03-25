<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include 'db_connection.php'; // Include the db_connection.php file

// Check if the database connection is established
if (!$pdo) {
    die("Failed to connect to the database.");
}

// Handle Edit Action (update user details)
if (isset($_POST['edit'])) {
    $user_id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role = $_POST['role'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $mobile_number = $_POST['mobile_number'];

    // Prepare update query
    $update_sql = "UPDATE users SET username = :username, email = :email, first_name = :first_name, last_name = :last_name, role = :role, date_of_birth = :dob, address = :address, mobile_number = :mobile_number WHERE id = :user_id";
    $stmt = $pdo->prepare($update_sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':dob', $dob);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':mobile_number', $mobile_number);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "User details updated successfully!";
        header('Location: member.php'); // Redirect back to the member page after update
        exit();
    } else {
        echo "Error updating user details.";
    }
}

// Handle Delete Action
if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];

    // Prepare delete query
    $delete_sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($delete_sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: member.php'); // Redirect back to member page after deletion
        exit();
    } else {
        echo "Error deleting user.";
    }
}

// Get the search query if set
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get total number of members
$totalMembersStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalMembers = $totalMembersStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get all members filtered by category or search
$category = isset($_GET['category']) ? $_GET['category'] : 'Manage Members';
$sql = "SELECT * FROM users";

// Add a search filter to the SQL query if search is provided
if ($search) {
    $sql .= " WHERE username LIKE :search";
}

// If a category other than "Manage Members" or "All Members" is selected, filter by role
if ($category !== 'Manage Members' && $category !== 'All Members') {
    $sql .= ($search ? " AND " : " WHERE ") . "role = :category";
}

$stmt = $pdo->prepare($sql);
if ($search) {
    // Use bindValue() instead of bindParam() to pass the search value directly
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
if ($category !== 'Manage Members' && $category !== 'All Members') {
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
}
$stmt->execute(); // Fetch the results

// Check if the 'edit_id' is set (i.e., edit button is clicked)
if (isset($_GET['edit_id'])) {
    $user_id = $_GET['edit_id'];

    // Fetch the user details from the database
    $sql = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Page</title>
    <link rel="stylesheet" href="member.css"> <!-- Link to your CSS file -->
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

    <!-- Right Side: Member Details -->
    <div class="member-details">
        <h2>Members</h2>
        <p><?php echo $totalMembers; ?> members found</p>

        <!-- Search bar -->
        <form method="GET" action="member.php" class="search-form">
            <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Buttons to filter members by category -->
        <div class="category-buttons">
            <a href="?category=Manage Members" class="category-btn">Manage Members</a>
            <a href="?category=All Members" class="category-btn">All Members</a>
            <a href="?category=pastor" class="category-btn">Pastors</a>
            <a href="?category=children" class="category-btn">Children</a>
            <a href="?category=adults" class="category-btn">Adults</a>
            <a href="?category=admin" class="category-btn">Admin</a>
        </div>

        <!-- Display the Edit Form if edit_id is set -->
        <?php if (isset($_GET['edit_id'])): ?>
            <form method="POST" action="member.php">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>

                <label for="role">Role:</label>
                <input type="text" name="role" value="<?php echo $user['role']; ?>" required>

                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" value="<?php echo $user['date_of_birth']; ?>" required>

                <label for="address">Address:</label>
                <input type="text" name="address" value="<?php echo $user['address']; ?>" required>

                <label for="mobile_number">Mobile Number:</label>
                <input type="text" name="mobile_number" value="<?php echo $user['mobile_number']; ?>" required>

                <button type="submit" name="edit">Save Changes</button>
            </form>
        <?php endif; ?>

        <!-- Table: Members -->
        <table class="member-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>DOB</th>
                    <th>Address</th>
                    <th>Mobile Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (!empty($row['profile_image']) && file_exists($row['profile_image'])): ?>
                                <img src="<?php echo $row['profile_image']; ?>" alt="User" class="profile-img">
                            <?php else: ?>
                                <img src="profile_pics/default.jpg" alt="Default User" class="profile-img">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['date_of_birth']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['mobile_number']; ?></td>
                        <td>
                            <?php if ($category == 'Manage Members'): ?>
                                <!-- Action buttons for "Manage Members" category -->
                                <a href="member.php?edit_id=<?php echo $row['id']; ?>"><button>Edit</button></a>
                                <a href="member.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><button>Delete</button></a>
                            <?php else: ?>
                                <!-- No actions for other categories like All Members, Pastors, Church Board, Children, Adults -->
                                No actions
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
$pdo = null;
?>
