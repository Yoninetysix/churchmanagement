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

// Get the search query if set
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get total number of members
$totalMembersStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalMembers = $totalMembersStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get all members filtered by search
$sql = "SELECT * FROM users";

// Add a search filter to the SQL query if search is provided
if ($search) {
    $sql .= " WHERE first_name LIKE :search OR last_name LIKE :search";
}

$stmt = $pdo->prepare($sql);
if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->execute(); // Fetch the results
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Page</title>
    <link rel="stylesheet" href="memberl.css"> <!-- Link to your CSS file -->
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

    <!-- Right Side: Member Details -->
    <div class="member-details">
        <h2>Members</h2>
        <p><?php echo $totalMembers; ?> members found</p>

        <!-- Search bar -->
        <form method="GET" action="memberl.php" class="search-form">
            <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Table: Members -->
        <table class="member-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>DOB</th>
                    <th>Address</th>
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
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['date_of_birth']; ?></td>
                        <td><?php echo $row['address']; ?></td>
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
