<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';  // Include the database connection

// Handle Property Insertion
if (isset($_POST['submit_property'])) {
    $property_name = $_POST['property_name'];
    $property_type = $_POST['property_type'];
    $property_location = $_POST['property_location'];
    $property_status = $_POST['property_status'];

    // Insert data into the church_properties table
    $stmt = $pdo->prepare("INSERT INTO church_properties (property_name, property_type, property_location, property_status) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$property_name, $property_type, $property_location, $property_status]);

    echo "Property successfully added!";
}

// Handle Lease Insertion
if (isset($_POST['submit_lease'])) {
    $lease_name = $_POST['lease_name'];
    $tenant_name = $_POST['tenant_name'];
    $lease_start = $_POST['lease_start'];
    $lease_end = $_POST['lease_end'];
    $lease_status = $_POST['lease_status'];
    $property_id = $_POST['property_id'];

    // Insert data into the leases table
    $stmt = $pdo->prepare("INSERT INTO leases (lease_name, tenant_name, lease_start, lease_end, lease_status, property_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$lease_name, $tenant_name, $lease_start, $lease_end, $lease_status, $property_id]);

    echo "Lease successfully added!";
}

// Handle Document Insertion
if (isset($_POST['submit_document'])) {
    $document_name = $_POST['document_name'];
    $document_file = $_POST['document_file'];  // Assuming file upload is handled
    $property_id = $_POST['property_id'];

    // Insert data into the property_documents table
    $stmt = $pdo->prepare("INSERT INTO property_documents (document_name, document_file, property_id) 
                           VALUES (?, ?, ?)");
    $stmt->execute([$document_name, $document_file, $property_id]);

    echo "Document successfully added!";
}

// Fetch all properties
$stmt = $pdo->prepare("SELECT * FROM church_properties");
$stmt->execute();
$properties = $stmt->fetchAll();

// Fetch all leases
$stmt = $pdo->prepare("SELECT * FROM leases");
$stmt->execute();
$leases = $stmt->fetchAll();

// Fetch all property documents
$stmt = $pdo->prepare("SELECT * FROM property_documents");
$stmt->execute();
$documents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Property Management</title>
    <link rel="stylesheet" href="assets_management.css">
</head>
<body>
    <!-- Sidebar Section -->
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

    <!-- Main Content Section -->
    <div class="main-content">
        <h1>Manage Church Properties</h1>

        <!-- Property Insertion Form -->
        <h3>Add New Property</h3>
        <form action="assets_management.php" method="POST">
            <label for="property_name">Property Name:</label>
            <input type="text" name="property_name" required><br>

            <label for="property_type">Property Type:</label>
            <input type="text" name="property_type" required><br>

            <label for="property_location">Property Location:</label>
            <input type="text" name="property_location" required><br>

            <label for="property_status">Property Status:</label>
            <select name="property_status">
                <option value="Available">Available</option>
                <option value="Leased">Leased</option>
                <option value="Under Maintenance">Under Maintenance</option>
            </select><br>

            <button type="submit" name="submit_property">Add Property</button>
        </form>

        <!-- Lease Insertion Form -->
        <h3>Add New Lease</h3>
        <form action="assets_management.php" method="POST">
            <label for="lease_name">Lease Name:</label>
            <input type="text" name="lease_name" required><br>

            <label for="tenant_name">Tenant Name:</label>
            <input type="text" name="tenant_name" required><br>

            <label for="lease_start">Lease Start Date:</label>
            <input type="date" name="lease_start" required><br>

            <label for="lease_end">Lease End Date:</label>
            <input type="date" name="lease_end" required><br>

            <label for="lease_status">Lease Status:</label>
            <select name="lease_status">
                <option value="Active">Active</option>
                <option value="Expired">Expired</option>
            </select><br>

            <label for="property_id">Select Property:</label>
            <select name="property_id">
                <?php foreach ($properties as $property) { ?>
                    <option value="<?php echo $property['id']; ?>"><?php echo $property['property_name']; ?></option>
                <?php } ?>
            </select><br>

            <button type="submit" name="submit_lease">Add Lease</button>
        </form>

        <!-- Document Insertion Form -->
        <h3>Add Property Document</h3>
        <form action="assets_management.php" method="POST" enctype="multipart/form-data">
            <label for="document_name">Document Name:</label>
            <input type="text" name="document_name" required><br>

            <label for="document_file">Upload Document:</label>
            <input type="file" name="document_file" required><br>

            <label for="property_id">Select Property:</label>
            <select name="property_id">
                <?php foreach ($properties as $property) { ?>
                    <option value="<?php echo $property['id']; ?>"><?php echo $property['property_name']; ?></option>
                <?php } ?>
            </select><br>

            <button type="submit" name="submit_document">Add Document</button>
        </form>

        <!-- Display Properties -->
        <h3>Properties List</h3>
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>Property Type</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($property['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_location']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Display Leases -->
        <h3>Leases List</h3>
        <table>
            <thead>
                <tr>
                    <th>Lease Name</th>
                    <th>Tenant Name</th>
                    <th>Lease Start</th>
                    <th>Lease End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leases as $lease) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lease['lease_name']); ?></td>
                        <td><?php echo htmlspecialchars($lease['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($lease['lease_start']); ?></td>
                        <td><?php echo htmlspecialchars($lease['lease_end']); ?></td>
                        <td><?php echo htmlspecialchars($lease['lease_status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Display Documents -->
        <h3>Property Documents</h3>
        <table>
            <thead>
                <tr>
                    <th>Document Name</th>
                    <th>Document File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $document) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($document['document_name']); ?></td>
                        <td><a href="<?php echo 'documents/' . htmlspecialchars($document['document_file']); ?>" download>Download</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
