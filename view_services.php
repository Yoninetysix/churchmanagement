<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';  // Database connection file

// Fetch the most recent sermon
$stmt_sermon = $pdo->prepare("SELECT * FROM sermons ORDER BY uploaded_at DESC LIMIT 1");
$stmt_sermon->execute();
$sermon = $stmt_sermon->fetch();

// Fetch the latest daily Bible verse
$stmt_verse = $pdo->prepare("SELECT * FROM daily_verses ORDER BY posted_at DESC LIMIT 1");
$stmt_verse->execute();
$daily_verse = $stmt_verse->fetch();

// Fetch the most recent news
$stmt_news = $pdo->prepare("SELECT * FROM news ORDER BY posted_at DESC LIMIT 1");
$stmt_news->execute();
$news = $stmt_news->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Services</title>
    <link rel="stylesheet" href="view_services.css">
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
            <li><a href="events.php">Events</a></li>
            <li><a href="attendance.php">Attendance</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="documents.php">Documents</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Church Services - View Content</h2>

    <!-- Display Latest Sermon -->
    <div class="sermon">
        <h3>Latest Sermon</h3>
        <?php if ($sermon): ?>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($sermon['title']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($sermon['description'])); ?></p>
            <p><strong>Uploaded By:</strong> <?php echo htmlspecialchars($sermon['created_by']); ?></p>
            <p><strong>File:</strong> 
                <?php if (!empty($sermon['file_path'])): ?>
                    <a href="<?php echo htmlspecialchars($sermon['file_path']); ?>" target="_blank">Download Sermon</a>
                <?php else: ?>
                    No file available for download.
                <?php endif; ?>
            </p>
        <?php else: ?>
            <p>No sermon available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Display Daily Bible Verse -->
    <div class="daily-verse">
        <h3>Daily Bible Verse</h3>
        <?php if ($daily_verse): ?>
            <p><strong>Verse:</strong> <?php echo nl2br(htmlspecialchars($daily_verse['verse'])); ?></p>
            <p><strong>Reference:</strong> <?php echo htmlspecialchars($daily_verse['reference']); ?></p>
        <?php else: ?>
            <p>No Bible verse available for today.</p>
        <?php endif; ?>
    </div>

    <!-- Display Latest News -->
    <div class="news">
        <h3>Latest News & Views</h3>
        <?php if ($news): ?>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($news['title']); ?></p>
            <p><strong>Content:</strong> <?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
        <?php else: ?>
            <p>No news available at the moment.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
