<?php
include "db_connection.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to create a post.'); window.location.href='login.php';</script>";
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

// Get the community ID from URL
$community_id = $_GET['community_id'] ?? null;
if (!$community_id) {
    echo "<script>alert('Community ID is missing. Redirecting to home.'); window.location.href='home.php';</script>";
    exit;
}

// Fetch community details
$sql_community = "SELECT name FROM communities WHERE id = ?";
$stmt_community = $conn->prepare($sql_community);
$stmt_community->bind_param("i", $community_id);
$stmt_community->execute();
$result_community = $stmt_community->get_result();
$community = $result_community->fetch_assoc();
$stmt_community->close();

if (!$community) {
    echo "<script>alert('Community not found.'); window.location.href='home.php';</script>";
    exit;
}

$community_name = $community['name'];

// Close the connection
$conn->close();

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $image = $_FILES['image']['name'] ?? null;
    $video = $_FILES['video']['name'] ?? null;  // Video support
    $category = $_POST['category'] ?? null;    // Category support
    $tags = $_POST['tags'] ?? null;            // Tags support
    $scheduled_time = $_POST['scheduled_time'] ?? null; // Scheduling support

    // Handle image upload
    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // Handle video upload
    if ($video) {
        $target_video_dir = "uploads/videos/";
        $target_video_file = $target_video_dir . basename($_FILES["video"]["name"]);
        move_uploaded_file($_FILES["video"]["tmp_name"], $target_video_file);
    }

    // Save the post to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    $sql_post = "INSERT INTO posts (user_id, community_id, content, image, video, category, tags, scheduled_time, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt_post = $conn->prepare($sql_post);
    $stmt_post->bind_param("iissssss", $user_id, $community_id, $content, $image, $video, $category, $tags, $scheduled_time);
    $stmt_post->execute();
    $stmt_post->close();

    // Redirect to the community page
    header("Location: explore_community.php?community_id=" . $community_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Post - <?= htmlspecialchars($community_name); ?></title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/create_post.css">

  <!-- Include TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/l0srg35kfmvqsfcfvv3bcttplkbaoxrmgjbote9penzenwx1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

  <script>
    // Initialize TinyMCE editor
    tinymce.init({
      selector: 'textarea',
      plugins: 'image media link table charmap code emoticons',  // Add emoji support
      toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image media | bullist numlist | table charmap | code emoticons',
      height: 400,
      image_title: true,
      automatic_uploads: true,
      file_picker_types: 'image',
      file_picker_callback: function (callback, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function () {
          var file = input.files[0];
          var reader = new FileReader();
          reader.onload = function () {
            callback(reader.result, { alt: file.name });
          };
          reader.readAsDataURL(file);
        };
        input.click();
      },
      setup: function (editor) {
        editor.on('change', function () {
          tinymce.triggerSave();
        });
      }
    });
  </script>
</head>
<body>

<!-- Create Post Form -->
<div class="create-post-container">
  <h2>Create a Post in <?= htmlspecialchars($community_name); ?></h2>
  <form action="create_post.php?community_id=<?= $community_id; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="user_id" value="<?= $user_id; ?>">
    <input type="hidden" name="community_id" value="<?= $community_id; ?>">

    <!-- TinyMCE Editor Textarea -->
    <textarea name="content" placeholder="Write your post here..." required></textarea><br><br>

    <!-- File upload (for optional image upload) -->
    <label for="image">Upload an image (optional):</label>
    <input type="file" name="image" id="image"><br><br>

    <!-- File upload (for optional video upload) -->
    <label for="video">Upload a video (optional):</label>
    <input type="file" name="video" id="video" accept="video/*"><br><br>

    <!-- Category and Tags -->
    <label for="category">Category:</label>
    <input type="text" name="category" id="category"><br><br>

    <label for="tags">Tags (comma separated):</label>
    <input type="text" name="tags" id="tags"><br><br>

    <!-- Scheduled time -->
    <label for="scheduled_time">Scheduled Time:</label>
    <input type="datetime-local" name="scheduled_time" id="scheduled_time"><br><br>

    <button type="submit">Post</button>
  </form>
</div>

<script src="create_post.js"></script>
</body>
</html>
