<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to create a post.'); window.location.href='login.php';</script>";
    exit;
}

$community_id = $_POST['community_id'] ?? null;
$content = trim($_POST['content']) ?? '';
$image = $_FILES['image'] ?? null;
$status = $_POST['action'] == 'publish' ? 'published' : 'draft'; // Default to draft if not published
$created_at = date('Y-m-d H:i:s');

if (!$community_id || empty($content)) {
    echo "<script>alert('Community ID or content missing.'); window.location.href='create_post.php?community_id=" . htmlspecialchars($community_id) . "';</script>";
    exit;
}

// Validate file upload (image or video)
$image_path = '';
if ($image && $image['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
    if (!in_array($image['type'], $allowed_types)) {
        echo "<script>alert('Invalid file type. Please upload an image or video.'); window.location.href='create_post.php?community_id=" . htmlspecialchars($community_id) . "';</script>";
        exit;
    }

    // Check file size (5MB limit for example)
    if ($image['size'] > 5000000) {
        echo "<script>alert('File size too large. Maximum size is 5MB.'); window.location.href='create_post.php?community_id=" . htmlspecialchars($community_id) . "';</script>";
        exit;
    }

    // Move the uploaded file to the target directory
    $target_dir = 'uploads/';
    $image_path = $target_dir . basename($image['name']);
    if (!move_uploaded_file($image['tmp_name'], $image_path)) {
        echo "<script>alert('Error uploading image.'); window.location.href='create_post.php?community_id=" . htmlspecialchars($community_id) . "';</script>";
        exit;
    }
}

// Insert the post into the database
$stmt = $conn->prepare("INSERT INTO posts (community_id, user_id, title, content, image, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$title = "Post Title";  // Default title; you can modify to allow user input
$stmt->bind_param("iisssss", $community_id, $_SESSION['user_id'], $title, $content, $image_path, $status, $created_at);

if ($stmt->execute()) {
    // Redirect with success
    $post_id = $stmt->insert_id; // Get the inserted post ID
    echo "<script>alert('Post " . ($status == 'published' ? "published" : "saved as draft") . " successfully.'); window.location.href='view_post.php?id=" . $post_id . "';</script>";
} else {
    // Error while saving post
    echo "<script>alert('Error saving post.'); window.location.href='create_post.php?community_id=" . htmlspecialchars($community_id) . "';</script>";
}

$stmt->close();
?>
