<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';  // Database connection file

// Upload sermon
if (isset($_POST['upload_sermon'])) {
    $sermon_title = $_POST['sermon_title'];
    $sermon_description = $_POST['sermon_description'];

    // Handle file upload
    $target_dir = "uploads/sermons/";
    $target_file = $target_dir . basename($_FILES["sermon_file"]["name"]);
    if (move_uploaded_file($_FILES["sermon_file"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO sermons (title, description, file_path) VALUES (?, ?, ?)");
        $stmt->execute([$sermon_title, $sermon_description, $target_file]);
        echo "<script>alert('Sermon uploaded successfully!');</script>";
    } else {
        echo "<script>alert('Failed to upload sermon.');</script>";
    }
}

// Post daily Bible verse
if (isset($_POST['post_bible_verse'])) {
    $verse = $_POST['verse'];
    $reference = $_POST['reference'];
    $stmt = $pdo->prepare("INSERT INTO daily_verses (verse, reference) VALUES (?, ?)");
    $stmt->execute([$verse, $reference]);
    echo "<script>alert('Daily Bible verse posted successfully!');</script>";
}

// Post news and views
if (isset($_POST['post_news'])) {
    $news_title = $_POST['news_title'];
    $news_content = $_POST['news_content'];
    $stmt = $pdo->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
    $stmt->execute([$news_title, $news_content]);
    echo "<script>alert('News posted successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Services</title>
    <link rel="stylesheet" href="services.css">
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
            <li><a href="document.php">Documents</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Church Services Management</h2>

    <!-- Upload Sermon -->
    <h3>Upload Previous Sermon</h3>
    <form action="services.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="sermon_title" placeholder="Sermon Title" required><br>
        <textarea name="sermon_description" placeholder="Sermon Description" required></textarea><br>
        <input type="file" name="sermon_file" required><br>
        <button type="submit" name="upload_sermon">Upload Sermon</button>
    </form>

    <!-- Post Daily Bible Verse -->
    <h3>Post Daily Bible Verse</h3>
    <form action="services.php" method="POST">
        <textarea name="verse" placeholder="Bible Verse" required></textarea><br>
        <input type="text" name="reference" placeholder="Verse Reference (e.g., John 3:16)" required><br>
        <button type="submit" name="post_bible_verse">Post Bible Verse</button>
    </form>

    <!-- Post News -->
    <h3>Post News & Views</h3>
    <form action="services.php" method="POST">
        <input type="text" name="news_title" placeholder="News Title" required><br>
        <textarea name="news_content" placeholder="News Content" required></textarea><br>
        <button type="submit" name="post_news">Post News</button>
    </form>

</div>

</body>
</html>
