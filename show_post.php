<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view the post.'); window.location.href='login.php';</script>";
    exit;
}

// Get the logged-in user's email from session
$current_email = $_SESSION['email'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the logged-in user's ID
$sql_user = "SELECT id FROM register WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $current_email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit;
}
$user_id = $user['id'];

// Get the community ID and post ID from URL
$community_id = $_GET['community_id'] ?? null;
$post_id = $_GET['post_id'] ?? null;

if (!$community_id || !$post_id) {
    echo "<script>alert('Invalid post or community ID. Redirecting to home.'); window.location.href='index.php';</script>";
    exit;
}

// Fetch the post details from the database
$sql_post = "SELECT p.id, p.content, p.image, p.video, p.created_at, p.category, p.tags, r.username AS author, c.name AS community_name
             FROM posts p
             JOIN register r ON p.user_id = r.id
             JOIN communities c ON p.community_id = c.id
             WHERE p.id = ? AND p.community_id = ?";
$stmt_post = $conn->prepare($sql_post);
$stmt_post->bind_param("ii", $post_id, $community_id);
$stmt_post->execute();
$result_post = $stmt_post->get_result();
$post = $result_post->fetch_assoc();
$stmt_post->close();

// Debugging - Check if the post is fetched successfully
if (!$post) {
    echo "<script>alert('Post not found.'); window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['community_name']) ?> - Post Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .post-image {
            max-width: 100%;
            height: auto;
            max-height: 400px; /* Adjust the height as needed */
            border-radius: 8px; /* Optional: Adds rounded corners */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Adds subtle shadow */
        }

        .post-content {
            margin-top: 30px;
            margin-bottom: 50px;
        }

        .timestamp {
            color: #888;
            font-size: 0.9rem;
        }

        .post-details {
            margin-top: 20px;
        }

        .post-video {
            width: 100%;
            border-radius: 8px;
        }

        .content-text h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .content-text p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<!-- Container for the post -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Card for post details -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($post['community_name']) ?> - Post by <?= htmlspecialchars($post['author']) ?></h2>
                    <p class="timestamp"><?= htmlspecialchars($post['created_at']) ?></p>

                    <div class="post-details">
                        <div class="content-text">
                            <h3>Content:</h3>
                            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                            <!-- Image display with responsive styling -->
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="post-image img-fluid">
                            <?php endif; ?>

                            <!-- Video display -->
                            <?php if (!empty($post['video'])): ?>
                                <video controls class="post-video">
                                    <source src="<?= htmlspecialchars($post['video']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
