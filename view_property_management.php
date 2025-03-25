<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Fetch Property Data
$propertiesQuery = $pdo->prepare("SELECT * FROM church_properties");
$propertiesQuery->execute();
$properties = $propertiesQuery->fetchAll();

// Fetch Lease Data
$leasesQuery = $pdo->prepare("SELECT * FROM leases");
$leasesQuery->execute();
$leases = $leasesQuery->fetchAll();

// Fetch Property Document Data
$documentsQuery = $pdo->prepare("SELECT * FROM property_documents");
$documentsQuery->execute();
$documents = $documentsQuery->fetchAll();

// Fetch the total number of properties
$totalPropertiesQuery = $pdo->prepare("SELECT COUNT(*) FROM church_properties");
$totalPropertiesQuery->execute();
$totalProperties = $totalPropertiesQuery->fetchColumn();

// Fetch the total number of church buildings (where property_type = 'Building')
$totalChurchBuildingsQuery = $pdo->prepare("SELECT COUNT(*) FROM church_properties WHERE property_type = 'Building'");
$totalChurchBuildingsQuery->execute();
$totalChurchBuildings = $totalChurchBuildingsQuery->fetchColumn();

// Fetch the total number of leases
$totalLeasesQuery = $pdo->prepare("SELECT COUNT(*) FROM leases");
$totalLeasesQuery->execute();
$totalLeases = $totalLeasesQuery->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management</title>
    <link rel="stylesheet" href="view_property_managemen.css"> <!-- Your CSS file -->
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

<!-- Right Content Section -->
<!-- Right Content Section -->
<div class="main-content">
    <h2>Church Property</h2>

    <!-- Top Section with 3 Boxes -->
    <!-- Top Section with 3 Boxes -->
    <div class="top-boxes">
        <!-- First Box: Properties -->
        <div class="top-box">
            <div class="box-icon">
                <img src="assets/propicon1-03-01.png" alt="Property Icon">
            </div>
            <div class="box-content">
                <p class="number"><?php echo htmlspecialchars($totalProperties); ?></p>
                <p>Properties</p>
            </div>
        </div>

        <!-- Second Box: Church Buildings -->
        <div class="top-box">
            <div class="box-icon">
                <img src="assets/propicon1-03-02.png" alt="Building Icon">
            </div>
            <div class="box-content">
                <p class="number"><?php echo htmlspecialchars($totalChurchBuildings); ?></p>
                <p>Church Buildings</p>
            </div>
        </div>

        <!-- Third Box: Leases -->
        <div class="top-box">
            <div class="box-icon">
                <img src="assets/propicon1-03-03.png" alt="Lease Icon">
            </div>
            <div class="box-content">
                <p class="number"><?php echo htmlspecialchars($totalLeases); ?></p>
                <p>Leases</p>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="tables">
        <!-- Property List Table -->
        <h3>Property List</h3>
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP Code to fetch and display data -->
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($property['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_location']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_status']); ?></td>
                        <td><?php echo htmlspecialchars($property['price']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Leases List Table -->
        <h3>Leases List</h3>
        <table>
            <thead>
                <tr>
                    <th>Lease name</th>
                    <th>Tenant name</th>
                    <th>Lease start</th>
                    <th>Lease end</th>
                    <th>Lease Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP Code to fetch and display data -->
                <?php foreach ($leases as $lease): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lease['lease_name']); ?></td>
                        <td><?php echo htmlspecialchars($lease['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($lease['lease_start']); ?></td>
                        <td><?php echo htmlspecialchars($lease['lease_end']); ?></td>
                        <td><?php echo htmlspecialchars($lease['lease_amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Property Documents Table -->
        <h3>Property Documents</h3>
        <table>
            <thead>
                <tr>
                    <th>Document Name</th>
                    <th>Document File</th>
                    <th>Property ID</th>
                    <th>Upload Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP Code to fetch and display data -->
                <?php foreach ($documents as $document): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($document['document_name']); ?></td>
                        <td><?php echo htmlspecialchars($document['document_file']); ?></td>
                        <td><?php echo htmlspecialchars($document['property_id']); ?></td>
                        <td><?php echo htmlspecialchars($document['uploaded_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
