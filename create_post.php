<?php
include 'db_connection.php'; // Include database connection

// Simulate logged-in user and community
$user_id = 1; // Replace with actual logged-in user ID
$community_id = 1; // Replace with the current community ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_title = mysqli_real_escape_string($conn, $_POST['post_title']);
    $post_content = mysqli_real_escape_string($conn, $_POST['post_content']);

    $insert_query = "INSERT INTO posts (user_id, community_id, title, content) 
                     VALUES ($user_id, $community_id, '$post_title', '$post_content')";

    if (mysqli_query($conn, $insert_query)) {
        echo "Post created successfully!";
    } else {
        echo "Error creating post: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
</head>
<body>
    <h1>Create a New Post</h1>
    <form action="" method="POST">
        <label for="post_title">Post Title:</label>
        <input type="text" id="post_title" name="post_title" required><br>

        <label for="post_content">Post Content:</label>
        <textarea id="post_content" name="post_content" required></textarea><br>

        <button type="submit">Create Post</button>
    </form>
</body>
</html>
