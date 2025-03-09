<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch posts from the database
$query = "SELECT posts.*, users.username FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div class="mb-4">
            <a href="profile.php" class="btn btn-info">View Profile</a>
            <a href="create_post.php" class="btn btn-primary">Create a New Post</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
        <h2>Recent Posts</h2>
        <?php while ($post = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    <small class="text-muted">By <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></small>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
