<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to create a post.'); window.location.href='login.php';</script>";
    exit;
}

$community_id = $_GET['community_id'] ?? null;
if (!$community_id) {
    echo "<script>alert('Invalid community ID.'); window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Create Post for Community</h1>
        
        <form action="save_post.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="5" required placeholder="Enter your content here..."></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image/Video</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*,video/*">
            </div>
            <input type="hidden" name="community_id" value="<?= htmlspecialchars($community_id) ?>">
            <button type="submit" name="action" value="publish" class="btn btn-success">Publish</button>
            <button type="submit" name="action" value="draft" class="btn btn-warning">Save as Draft</button>
        </form>
    </div>
</body>
</html>
