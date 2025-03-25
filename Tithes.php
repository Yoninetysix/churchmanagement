<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php'; // Include your database connection
require('fpdf/fpdf.php');  // Include FPDF for PDF generation

// Get the user_id from the session
$user_id = $_SESSION['id'];

// Fetch the user's first name and last name from the users table
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Check if user exists
if (!$user) {
    die("User not found.");
}

$first_name = $user['first_name'];
$last_name = $user['last_name'];

// Handle tithes and offerings donation (for the "Tithes and Offerings Form")
if (isset($_POST['donate']) && isset($_POST['donation_type']) && isset($_POST['amount'])) {
    // Get form data
    $donation_type = $_POST['donation_type'];  // 'once' or 'monthly'
    $amount = $_POST['amount'];  // Donation amount
    $donation_date = date("Y-m-d H:i:s");  // Get current date and time

    // Validate donation amount
    if ($amount <= 0) {
        die("Invalid donation amount.");
    }

    // Insert donation into tithes_and_offerings table
    $stmt = $pdo->prepare("INSERT INTO tithes_and_offerings 
                           (user_id, first_name, last_name, donation_type, amount, donation_date) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $first_name, $last_name, $donation_type, $amount, $donation_date]);

    // Generate a receipt PDF
    $receipt_file = 'receipts/receipt_' . time() . '.pdf';  // Generate a unique receipt filename
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    $pdf->Cell(0, 10, 'Receipt for Donation', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    
    $pdf->Cell(40, 10, 'First Name: ' . $first_name);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Last Name: ' . $last_name);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Donation Type: ' . ucfirst($donation_type));
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Amount: $' . number_format($amount, 2));
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Donation Date: ' . $donation_date);
    
    // Output PDF to file
    $pdf->Output('F', $receipt_file);

    // Insert donation into offering_history table with receipt
    $stmt = $pdo->prepare("INSERT INTO offering_history 
                           (user_id, first_name, last_name, donation_type, amount, receipt, donation_date) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $first_name, $last_name, $donation_type, $amount, $receipt_file, $donation_date]);

    // Show success message with the receipt download link
    echo "Donation successful! Your receipt is available. <a href='$receipt_file' download>Download Receipt</a>";
}

// Handle new donation (for the "New Donation Section")
if (isset($_POST['submit_donation'])) {
    // Get new donation data
    $donation_type = $_POST['donation_type'];  // Building Funds, Charity, Donation, etc.
    $description = $_POST['description'];  // Description
    $amount = $_POST['amount'];  // Donation amount
    $donation_date = date("Y-m-d H:i:s");  // Current date and time

    // Validate donation amount
    if ($amount <= 0) {
        die("Invalid donation amount.");
    }

    // Insert new donation into donations table
    $stmt = $pdo->prepare("INSERT INTO donations 
                           (user_id, donation_type, description, amount, receipt, donation_date) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $donation_type, $description, $amount, '', $donation_date]);

    // Generate a receipt PDF
    $receipt_file = 'receipts/receipt_' . time() . '.pdf';  // Generate a unique receipt filename
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    $pdf->Cell(0, 10, 'Receipt for Donation', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    
    $pdf->Cell(40, 10, 'Donation Type: ' . ucfirst($donation_type));
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Description: ' . $description);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Amount: $' . number_format($amount, 2));
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Donation Date: ' . $donation_date);
    
    // Output PDF to file
    $pdf->Output('F', $receipt_file);

    // Update the donations table with the receipt
    $stmt = $pdo->prepare("UPDATE donations SET receipt = ? WHERE user_id = ? AND donation_date = ?");
    $stmt->execute([$receipt_file, $user_id, $donation_date]);

    // Show success message with the receipt download link
    echo "Donation successful! Your receipt is available. <a href='$receipt_file' download>Download Receipt</a>";
}

// Fetch user's offering history if data exists
$offering_history = [];
$stmt = $pdo->prepare("SELECT * FROM offering_history WHERE user_id = ? ORDER BY donation_date DESC");
$stmt->execute([$user_id]);
$offering_history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Management - Tithes and Offerings</title>
    <link rel="stylesheet" href="Tithess.css"> <!-- Link to your CSS file -->
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
    <div class="main-content">
        <h2>Tithes and Offerings</h2>

        <!-- Top Four Boxes -->
        <div class="top-boxes">
            <div class="box">
                <h3>Tithes and Offerings</h3>
                <p>1500</p>
            </div>
            <div class="box">
                <h3>Donation</h3>
                <p>2000</p>
            </div>
            <div class="box">
                <h3>Building Funds</h3>
                <p>4000</p>
            </div>
            <div class="box">
                <h3>Other</h3>
                <p>4500</p>
            </div>
        </div>

        <!-- Donation Form -->
        <div class="donation-container">
            <!-- Left Side Image -->
            <div class="left-side">
                <img src="assets/banner3.png" alt="Community Image" class="donation-image">
            </div>

            <!-- Right Side (Tithes and Offerings Form) -->
            <div class="right-side">
                <h3>Tithes and Offerings</h3>

                <form action="tithes.php" method="POST">
                    <div class="donation-options">
                        <label>Give:</label>
                        <label for="give_once">
                            <input type="radio" id="give_once" name="donation_type" value="once" checked> Give Once
                        </label>
                        <label for="monthly">
                            <input type="radio" id="monthly" name="donation_type" value="monthly"> Monthly
                        </label>
                    </div>

                    <div class="donation-amounts">
                        <label>Secure Donation:</label>
                        <div class="donation-buttons">
                            <button type="button" class="donation-btn" data-amount="20000">20000</button>
                            <button type="button" class="donation-btn" data-amount="10000">10000</button>
                            <button type="button" class="donation-btn" data-amount="5000">5000</button>
                            <button type="button" class="donation-btn" data-amount="2000">2000</button>
                            <button type="button" class="donation-btn" data-amount="1000">1000</button>
                            <button type="button" class="donation-btn" data-amount="500">500</button>
                        </div>
                    </div>

                    <div class="custom-donation">
                        <label for="custom_amount">Or enter custom amount:</label>
                        <input type="number" id="custom_amount" name="custom_amount" placeholder="RS51" required>
                    </div>

                    <div class="total-amount">
                        <label>Total: </label>
                        <span id="total-amount">RS51</span> LKR
                    </div>

                    <input type="hidden" id="amount" name="amount" value="51"> <!-- Hidden input for amount -->
                    
                    <button type="submit" name="donate" class="donate-btn">Donate Monthly</button>
                </form>
            </div>
        </div>

        <!-- Your New Donation Section -->
        <div class="donation-form">
            <h3>Make a New Donation</h3>
            <form action="Tithes.php" method="POST">
                <!-- Donation Type Selection -->
                <div class="donation-options">
                    <label for="donation_type">Select Donation Type:</label><br>
                    <select name="donation_type" id="donation_type" required>
                        <option value="building_funds">Building Funds</option>
                        <option value="charity">Charity</option>
                        <option value="donation">Donation</option>
                        <option value="programme">Programme</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Donation Description -->
                <div class="donation-description">
                    <label for="description">Description:</label><br>
                    <textarea name="description" id="description" rows="4" placeholder="Enter a brief description of your donation" required></textarea>
                </div>

                <!-- Donation Amount -->
                <div class="donation-amount">
                    <label for="amount">Donation Amount (LKR):</label><br>
                    <input type="number" name="amount" id="amount" min="1" placeholder="Enter donation amount" required><br><br>
                </div>

                <!-- Submit Button -->
                <div class="form-action">
                    <button type="submit" name="submit_donation" class="donate-btn">Submit Donation</button>
                </div>
            </form>
        </div>

        <!-- Receipt Section for the New Donation Form -->
        <div class="new-donation-receipt">
            <?php if (isset($_POST['submit_donation'])): ?>
                <?php
                    // Handle donation logic here and show the receipt download
                    $donation_type = $_POST['donation_type'];
                    $description = $_POST['description'];
                    $amount = $_POST['amount'];

                    // Generate the donation receipt PDF
                    $receipt_file = 'receipts/receipt_' . time() . '.pdf';
                    $pdf = new FPDF();
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 16);
                    $pdf->Cell(0, 10, 'Receipt for ' . ucfirst($donation_type), 0, 1, 'C');
                    $pdf->Ln(10);
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->Cell(40, 10, 'Donation Type: ' . ucfirst($donation_type));
                    $pdf->Ln();
                    $pdf->Cell(40, 10, 'Description: ' . $description);
                    $pdf->Ln();
                    $pdf->Cell(40, 10, 'Amount: LKR ' . number_format($amount, 2));
                    $pdf->Ln();
                    $pdf->Cell(40, 10, 'Donation Date: ' . date("Y-m-d H:i:s"));
                    $pdf->Output('F', $receipt_file);

                    // Display success message with receipt download link
                    echo "Donation successful! Your receipt is available. <a href='$receipt_file' download>Download Receipt</a>";
                ?>
            <?php endif; ?>
        </div>

        <!-- Your Offering History Section -->
        <div class="history-section">
            <h3>Your Offering History</h3>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Donation Type</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th>Date of Donation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offering_history as $history): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['donation_type']); ?></td>
                            <td>LKR <?php echo htmlspecialchars($history['amount']); ?></td>
                            <!-- Link to download the receipt PDF -->
                            <td><a href="receipts/<?php echo basename($history['receipt']); ?>" download>Download Receipt</a></td>
                            <td><?php echo htmlspecialchars($history['donation_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script> 
// Dynamically update total amount
const donationButtons = document.querySelectorAll('.donation-btn');
const customAmountInput = document.getElementById('custom_amount');
const totalAmount = document.getElementById('total-amount');
const hiddenAmount = document.getElementById('amount');  // Hidden amount field

donationButtons.forEach(button => {
    button.addEventListener('click', function() {
        const amount = this.getAttribute('data-amount');
        customAmountInput.value = '';  // Clear custom input
        totalAmount.textContent = `$${amount}`;  // Update total amount display
        hiddenAmount.value = amount;  // Set the hidden amount field
    });
});

customAmountInput.addEventListener('input', function() {
    totalAmount.textContent = `$${customAmountInput.value}`;  // Update total display dynamically
    hiddenAmount.value = customAmountInput.value;  // Update hidden amount field
});
</script>

</body>
</html>
