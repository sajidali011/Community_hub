<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "community_hub";

// Start session to get logged-in user's information
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to view this page.'); window.location.href='login.php';</script>";
    exit;
}

$current_email = $_SESSION['email'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's id
$user_query = "SELECT id FROM register WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_row = $result->fetch_assoc();
    $user_id = $user_row['id'];
} else {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit;
}

// Get the community ID from the GET parameter
$community_id = $_GET['id'] ?? null;

if (!$community_id) {
    echo "<script>alert('No community selected.'); window.location.href='community_list.php';</script>";
    exit;
}

// Fetch community details
$community_query = "SELECT * FROM communities WHERE id = ?";
$stmt = $conn->prepare($community_query);
$stmt->bind_param("i", $community_id);
$stmt->execute();
$community_result = $stmt->get_result();

if ($community_result->num_rows > 0) {
    $community = $community_result->fetch_assoc();
} else {
    echo "<script>alert('Community not found.'); window.location.href='community_list.php';</script>";
    exit;
}

// Fetch posts in the community
$post_query = "SELECT p.id, p.title, p.content, p.image, p.created_at, r.username 
               FROM posts p
               JOIN register r ON p.user_id = r.id
               WHERE p.community_id = ? 
               ORDER BY p.created_at DESC";
$stmt = $conn->prepare($post_query);
$stmt->bind_param("i", $community_id);
$stmt->execute();
$posts = $stmt->get_result();

// Check if the user is already a member of the community
$membership_query = "SELECT * FROM community_members WHERE user_id = ? AND community_id = ?";
$stmt = $conn->prepare($membership_query);
$stmt->bind_param("ii", $user_id, $community_id);
$stmt->execute();
$is_member = $stmt->get_result()->num_rows > 0;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($community['name']); ?> - Explore</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2><?php echo htmlspecialchars($community['name']); ?></h2>
                <p><?php echo htmlspecialchars($community['description']); ?></p>
            </div>
            <div class="card-body">
                <h4>Community Posts</h4>
                <?php if ($posts->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($post = $posts->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <h5><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                <?php if ($post['image']): ?>
                                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="img-fluid">
                                <?php endif; ?>
                                <small>Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No posts available in this community.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <?php if ($is_member): ?>
                    <a href="leave_community.php?id=<?php echo $community_id; ?>" class="btn btn-danger">Leave Community</a>
                <?php else: ?>
                    <a href="join_community.php?id=<?php echo $community_id; ?>" class="btn btn-success">Join Community</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
