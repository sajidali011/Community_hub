<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "community_hub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session to track the logged-in user
session_start();

// Assuming that you have a session for user login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$community_id = isset($_GET['community_id']) ? $_GET['community_id'] : null;

// If user is not logged in, redirect to login page
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Handle form submission for adding or updating posts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $image = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing post
        $id = $_POST['id'];
        $sql = "UPDATE posts SET community_id='$community_id', user_id='$user_id', title='$title', content='$content', image='$image' WHERE id=$id";
    } else {
        // Add new post
        $sql = "INSERT INTO posts (community_id, user_id, title, content, image, created_at) 
                VALUES ('$community_id', '$user_id', '$title', '$content', '$image', NOW())";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: view_community.php?community_id=$community_id");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch posts from the database
$sql = "SELECT * FROM posts WHERE community_id='$community_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Dashboard</title>
    <!-- Include TinyMCE with custom API key -->
    <script src="https://cdn.tiny.cloud/1/6bqijycchpekslvz270gg5zvh62svkikhvmjqc8ntrmpadyu/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content', // Attach to textarea
            plugins: 'advlist autolink lists link image charmap preview anchor table code',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image table code',
            height: 400,
            menubar: false,
            branding: false
        });
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type="file"] {
            padding: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #bd2130;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Community Dashboard</h2>

        <!-- Form for adding/updating posts -->
        <form action="view_community.php?community_id=<?= $community_id ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id">
            <div class="form-group">
                <label for="title">Post Title:</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="content">Post Content:</label>
                <textarea id="content" name="content"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Feature Image:</label>
                <input type="file" name="image" id="image">
            </div>
            <button type="submit" class="btn">Save Post</button>
        </form>

        <!-- Display posts -->
        <h3>All Posts</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Community ID</th>
                    <th>User ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['community_id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars(substr($row['content'], 0, 50)) ?>...</td>
                        <td>
                            <?php if ($row['image']): ?>
                                <img src="<?= $row['image'] ?>" alt="Feature Image" width="100">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <a href="edit_post.php?id=<?= $row['id'] ?>&community_id=<?= $row['community_id'] ?>" class="btn">Edit</a>
                            <a href="delete_post.php?id=<?= $row['id'] ?>&community_id=<?= $row['community_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
