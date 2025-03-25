<?php
// Include PhpSpreadsheet library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require 'vendor/autoload.php';  // If you're using Composer

// Include the database connection
include 'db_connection.php';

// Handle the export request for users, donations, events, and certificate requests
if (isset($_GET['export'])) {
    $exportType = $_GET['export'];

    // Export Users Report
    if ($exportType == 'users_excel') {
        $stmt = $pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'User ID');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Role');
        $sheet->setCellValue('E1', 'First Name');
        $sheet->setCellValue('F1', 'Last Name');
        $sheet->setCellValue('G1', 'Date of Birth');
        $sheet->setCellValue('H1', 'Address');
        $sheet->setCellValue('I1', 'Mobile Number');
        $sheet->setCellValue('J1', 'Join Church Date');
        $sheet->setCellValue('K1', 'Profile Image');

        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user['id']);
            $sheet->setCellValue('B' . $row, $user['username']);
            $sheet->setCellValue('C' . $row, $user['email']);
            $sheet->setCellValue('D' . $row, $user['role']);
            $sheet->setCellValue('E' . $row, $user['first_name']);
            $sheet->setCellValue('F' . $row, $user['last_name']);
            $sheet->setCellValue('G' . $row, $user['date_of_birth']);
            $sheet->setCellValue('H' . $row, $user['address']);
            $sheet->setCellValue('I' . $row, $user['mobile_number']);
            $sheet->setCellValue('J' . $row, $user['join_church_date']);
            $sheet->setCellValue('K' . $row, $user['profile_image']);
            $row++;
        }
    }

    // Export Donations Report
    if ($exportType == 'donations_excel') {
        $stmt = $pdo->prepare("SELECT * FROM tithes_and_offerings");
        $stmt->execute();
        $donations = $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Donation ID');
        $sheet->setCellValue('B1', 'User ID');
        $sheet->setCellValue('C1', 'First Name');
        $sheet->setCellValue('D1', 'Last Name');
        $sheet->setCellValue('E1', 'Donation Type');
        $sheet->setCellValue('F1', 'Amount');
        $sheet->setCellValue('G1', 'Donation Date');

        $row = 2;
        foreach ($donations as $donation) {
            $sheet->setCellValue('A' . $row, $donation['id']);
            $sheet->setCellValue('B' . $row, $donation['user_id']);
            $sheet->setCellValue('C' . $row, $donation['first_name']);
            $sheet->setCellValue('D' . $row, $donation['last_name']);
            $sheet->setCellValue('E' . $row, $donation['donation_type']);
            $sheet->setCellValue('F' . $row, $donation['amount']);
            $sheet->setCellValue('G' . $row, $donation['donation_date']);
            $row++;
        }
    }

    // Export Events Report
    if ($exportType == 'events_excel') {
        $stmt = $pdo->prepare("SELECT * FROM events");
        $stmt->execute();
        $events = $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Event ID');
        $sheet->setCellValue('B1', 'Event Title');
        $sheet->setCellValue('C1', 'Event Description');
        $sheet->setCellValue('D1', 'Event Date');
        $sheet->setCellValue('E1', 'Event Time');
        $sheet->setCellValue('F1', 'Created By');
        $sheet->setCellValue('G1', 'Event End Time');

        $row = 2;
        foreach ($events as $event) {
            $sheet->setCellValue('A' . $row, $event['id']);
            $sheet->setCellValue('B' . $row, $event['event_title']);
            $sheet->setCellValue('C' . $row, $event['event_description']);
            $sheet->setCellValue('D' . $row, $event['event_date']);
            $sheet->setCellValue('E' . $row, $event['event_time']);
            $sheet->setCellValue('F' . $row, $event['created_by']);
            $sheet->setCellValue('G' . $row, $event['event_end_time']);
            $row++;
        }
    }

    // Export Certificate Requests Report
    if ($exportType == 'certificate_requests_excel') {
        $stmt = $pdo->prepare("SELECT * FROM certificate_requests");
        $stmt->execute();
        $certificate_requests = $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Request ID');
        $sheet->setCellValue('B1', 'User ID');
        $sheet->setCellValue('C1', 'Certificate Type');
        $sheet->setCellValue('D1', 'Reason');
        $sheet->setCellValue('E1', 'NIC Number');
        $sheet->setCellValue('F1', 'Full Name');
        $sheet->setCellValue('G1', 'Address');
        $sheet->setCellValue('H1', 'Gender');
        $sheet->setCellValue('I1', 'Request Date');

        $row = 2;
        foreach ($certificate_requests as $request) {
            $sheet->setCellValue('A' . $row, $request['id']);
            $sheet->setCellValue('B' . $row, $request['user_id']);
            $sheet->setCellValue('C' . $row, $request['certificate_type']);
            $sheet->setCellValue('D' . $row, $request['reason']);
            $sheet->setCellValue('E' . $row, $request['nic_number']);
            $sheet->setCellValue('F' . $row, $request['full_name']);
            $sheet->setCellValue('G' . $row, $request['address']);
            $sheet->setCellValue('H' . $row, $request['gender']);
            $sheet->setCellValue('I' . $row, $request['request_date']);
            $row++;
        }
    }

    // Create Excel file and download
    $writer = new Xlsx($spreadsheet);
    $filename = $exportType . "_report_" . date('Y-m-d_H-i-s') . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="reportt.css">
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

    <div class="main-content">
        <h2>Reports</h2>

        <!-- Buttons for Exporting Different Reports -->
        <div class="export-btn">
            <a href="report.php?export=users_excel"><button>Export Users Report</button></a>
            <a href="report.php?export=donations_excel"><button>Export Donations Report</button></a>
            <a href="report.php?export=events_excel"><button>Export Events Report</button></a>
            <a href="report.php?export=certificate_requests_excel"><button>Export Certificate Requests Report</button></a>
        </div>

        <!-- Optionally, you can also show tables here for viewing the reports on the web page -->
    </div>
</div>

</body>
</html>
