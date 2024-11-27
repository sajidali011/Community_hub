<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view this page.'); window.location.href='login.php';</script>";
    exit;
}

$current_email = $_SESSION['email'];

// Get user ID
$stmt = $conn->prepare("SELECT id FROM register WHERE email = ?");
$stmt->bind_param("s", $current_email);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$current_user_id = $user_data['id'];
$stmt->close();

// Get community details
$community_id = $_GET['id'] ?? null;
if (!$community_id) {
    echo "<script>alert('Invalid community ID.'); window.location.href='community_list.php';</script>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM communities WHERE id = ?");
$stmt->bind_param("i", $community_id);
$stmt->execute();
$community = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if the community exists
if (!$community) {
    echo "<script>alert('Community not found.'); window.location.href='community_list.php';</script>";
    exit;
}

// Check if the current user is part of the community
$stmt = $conn->prepare("SELECT * FROM community_members WHERE user_id = ? AND community_id = ?");
$stmt->bind_param("ii", $current_user_id, $community_id);
$stmt->execute();
$is_member = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$is_member) {
    echo "<script>alert('You are not part of this community.'); window.location.href='community_list.php';</script>";
    exit;
}

// Get published posts
$stmt = $conn->prepare("SELECT * FROM posts WHERE community_id = ? AND status = 'published' ORDER BY created_at DESC");
$stmt->bind_param("i", $community_id);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Community</title>
    <script src="https://cdn.tiny.cloud/1/387xy2rgyvd5984o4xra9ers0tskoz5fu648sp4w97zd829q/tinymce/6/tinymce.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 30px auto; }
        .post-card img, .post-card video { width: 100%; max-height: 300px; object-fit: cover; }
        .post-card { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak code table media imagetools fullscreen insertdatetime',
            toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code fullscreen',
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image media',
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype === 'image' || meta.filetype === 'media') {
                    let input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', meta.filetype === 'image' ? 'image/*' : 'video/*');
                    input.onchange = function () {
                        let file = this.files[0];
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            callback(e.target.result, { title: file.name });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                }
            },
            menubar: true,
            height: 500,
            branding: false,
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Welcome to <?= htmlspecialchars($community['name']) ?> Community</h2>
        <!-- Post Form -->
        <form action="save_post.php" method="post" enctype="multipart/form-data">
    <textarea id="content" name="content" class="form-control" rows="5" required></textarea>
    <input type="hidden" name="community_id" value="<?= $community_id ?>">
    <input type="file" name="image" class="form-control my-2" accept="image/*,video/*">
    
    <!-- Only Publish button, others removed -->
    <button type="submit" name="action" value="publish" class="btn btn-success">Publish</button>
</form>


        <!-- Displaying Posts -->
        <h3>Published Posts</h3>
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <?php if (strpos($post['image'], '.mp4') !== false): ?>
                        <video controls>
                            <source src="<?= htmlspecialchars($post['image']) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">
                    <?php endif; ?>
                    <p><?= htmlspecialchars(substr($post['content'], 0, 200)) ?>...</p>
                    <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-primary">View Post</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts available in this community.</p>
        <?php endif; ?>
    </div>
</body>
</html>
