<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Get the current logged-in user's ID from session
if (!isset($_SESSION['id'])) {
    die("User is not logged in.");
}
$user_id = $_SESSION['id'];


// Fetch the Budget Income and Expenses
$incomeQuery = $pdo->prepare("SELECT SUM(amount) FROM income WHERE user_id = ?");
$incomeQuery->execute([$user_id]);
$totalIncome = $incomeQuery->fetchColumn();
$totalIncome = $totalIncome ? $totalIncome : 0;  // Set to 0 if null

// Fetch the Budget Expenses
$expenseQuery = $pdo->prepare("SELECT SUM(expense_amount) FROM expenses WHERE user_id = ?");
$expenseQuery->execute([$user_id]);
$totalExpenses = $expenseQuery->fetchColumn();
$totalExpenses = $totalExpenses ? $totalExpenses : 0;  // Set to 0 if null




// Fetch Income data for each month
$incomeQuery = $pdo->prepare("
    SELECT 
        income_category, 
        SUM(CASE WHEN MONTH(income_date) = 1 THEN amount ELSE 0 END) AS `Jan`,
        SUM(CASE WHEN MONTH(income_date) = 2 THEN amount ELSE 0 END) AS `Feb`,
        SUM(CASE WHEN MONTH(income_date) = 3 THEN amount ELSE 0 END) AS `Mar`,
        SUM(CASE WHEN MONTH(income_date) = 4 THEN amount ELSE 0 END) AS `Apr`,
        SUM(CASE WHEN MONTH(income_date) = 5 THEN amount ELSE 0 END) AS `May`,
        SUM(CASE WHEN MONTH(income_date) = 6 THEN amount ELSE 0 END) AS `Jun`,
        SUM(CASE WHEN MONTH(income_date) = 7 THEN amount ELSE 0 END) AS `Jul`,
        SUM(CASE WHEN MONTH(income_date) = 8 THEN amount ELSE 0 END) AS `Aug`,
        SUM(CASE WHEN MONTH(income_date) = 9 THEN amount ELSE 0 END) AS `Sep`,
        SUM(CASE WHEN MONTH(income_date) = 10 THEN amount ELSE 0 END) AS `Oct`,
        SUM(CASE WHEN MONTH(income_date) = 11 THEN amount ELSE 0 END) AS `Nov`,
        SUM(CASE WHEN MONTH(income_date) = 12 THEN amount ELSE 0 END) AS `Dec`
    FROM income 
    WHERE user_id = ?
    GROUP BY income_category
");
$incomeQuery->execute([$user_id]);
$incomeData = $incomeQuery->fetchAll();

// Fetch Expense data for each month
$expenseQuery = $pdo->prepare("
    SELECT 
        expense_category, 
        SUM(CASE WHEN MONTH(expense_date) = 1 THEN expense_amount ELSE 0 END) AS `Jan`,
        SUM(CASE WHEN MONTH(expense_date) = 2 THEN expense_amount ELSE 0 END) AS `Feb`,
        SUM(CASE WHEN MONTH(expense_date) = 3 THEN expense_amount ELSE 0 END) AS `Mar`,
        SUM(CASE WHEN MONTH(expense_date) = 4 THEN expense_amount ELSE 0 END) AS `Apr`,
        SUM(CASE WHEN MONTH(expense_date) = 5 THEN expense_amount ELSE 0 END) AS `May`,
        SUM(CASE WHEN MONTH(expense_date) = 6 THEN expense_amount ELSE 0 END) AS `Jun`,
        SUM(CASE WHEN MONTH(expense_date) = 7 THEN expense_amount ELSE 0 END) AS `Jul`,
        SUM(CASE WHEN MONTH(expense_date) = 8 THEN expense_amount ELSE 0 END) AS `Aug`,
        SUM(CASE WHEN MONTH(expense_date) = 9 THEN expense_amount ELSE 0 END) AS `Sep`,
        SUM(CASE WHEN MONTH(expense_date) = 10 THEN expense_amount ELSE 0 END) AS `Oct`,
        SUM(CASE WHEN MONTH(expense_date) = 11 THEN expense_amount ELSE 0 END) AS `Nov`,
        SUM(CASE WHEN MONTH(expense_date) = 12 THEN expense_amount ELSE 0 END) AS `Dec`
    FROM expenses
    WHERE user_id = ?
    GROUP BY expense_category
");
$expenseQuery->execute([$user_id]);
$expenseData = $expenseQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Budget</title>
    <link rel="stylesheet" href="view_budget.css"> <!-- Your CSS file -->
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
        <h2>Budget</h2>

        <!-- Budget Income and Expenses Section -->
        <div class="budget-section">
            <div class="budget-box">
                <div class="box-content">
                    <p class="number"><?php echo number_format($totalIncome, 2); ?></p>
                    <p>Income</p>
                </div>
            </div>

            <div class="budget-box">
                <div class="box-content">
                    <p class="number"><?php echo number_format($totalExpenses, 2); ?></p>
                    <p>Expenses</p>
                </div>
            </div>
            


            
            <div class="budget-box">
                <div class="box-content">
                    <p class="number"><?php echo number_format($totalIncome - $totalExpenses, 2); ?></p>
                    <p>Net Balance</p>
                </div>
            </div>
        </div>

        <!-- Monthly Income and Expenses Table -->
        <div class="monthly-budget-table">
            <h3>Income</h3>
            <table class="budget-table">
                <thead>
                    <tr>
                        <th>Revenue</th>
                        <th>Jan</th>
                        <th>Feb</th>
                        <th>Mar</th>
                        <th>Apr</th>
                        <th>May</th>
                        <th>Jun</th>
                        <th>Jul</th>
                        <th>Aug</th>
                        <th>Sep</th>
                        <th>Oct</th>
                        <th>Nov</th>
                        <th>Dec</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incomeData as $income): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($income['income_category']); ?></td>
                            <td><?php echo number_format($income['Jan'], 2); ?></td>
                            <td><?php echo number_format($income['Feb'], 2); ?></td>
                            <td><?php echo number_format($income['Mar'], 2); ?></td>
                            <td><?php echo number_format($income['Apr'], 2); ?></td>
                            <td><?php echo number_format($income['May'], 2); ?></td>
                            <td><?php echo number_format($income['Jun'], 2); ?></td>
                            <td><?php echo number_format($income['Jul'], 2); ?></td>
                            <td><?php echo number_format($income['Aug'], 2); ?></td>
                            <td><?php echo number_format($income['Sep'], 2); ?></td>
                            <td><?php echo number_format($income['Oct'], 2); ?></td>
                            <td><?php echo number_format($income['Nov'], 2); ?></td>
                            <td><?php echo number_format($income['Dec'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Expenses</h3>
            <table class="budget-table">
                <thead>
                    <tr>
                        <th>Expenses</th>
                        <th>Jan</th>
                        <th>Feb</th>
                        <th>Mar</th>
                        <th>Apr</th>
                        <th>May</th>
                        <th>Jun</th>
                        <th>Jul</th>
                        <th>Aug</th>
                        <th>Sep</th>
                        <th>Oct</th>
                        <th>Nov</th>
                        <th>Dec</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenseData as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['expense_category']); ?></td>
                            <td><?php echo number_format($expense['Jan'], 2); ?></td>
                            <td><?php echo number_format($expense['Feb'], 2); ?></td>
                            <td><?php echo number_format($expense['Mar'], 2); ?></td>
                            <td><?php echo number_format($expense['Apr'], 2); ?></td>
                            <td><?php echo number_format($expense['May'], 2); ?></td>
                            <td><?php echo number_format($expense['Jun'], 2); ?></td>
                            <td><?php echo number_format($expense['Jul'], 2); ?></td>
                            <td><?php echo number_format($expense['Aug'], 2); ?></td>
                            <td><?php echo number_format($expense['Sep'], 2); ?></td>
                            <td><?php echo number_format($expense['Oct'], 2); ?></td>
                            <td><?php echo number_format($expense['Nov'], 2); ?></td>
                            <td><?php echo number_format($expense['Dec'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
