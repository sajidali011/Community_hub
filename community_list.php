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

// Get the current email from session
$current_email = $_SESSION['email'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user ID from the 'register' table
$user_query = "SELECT id FROM register WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$current_user_id = $user_data['id'];
$stmt->close();

// Fetch all communities or filter by category
$filter_category = isset($_GET['category']) ? $_GET['category'] : null;
if ($filter_category) {
    $sql = "SELECT * FROM communities WHERE category = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter_category);
} else {
    $sql = "SELECT * FROM communities ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();

$communities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $communities[] = $row;
    }
}
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
        }

        .header select {
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .community-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .community-card {
            flex: 1 1 calc(33.333% - 20px);
            max-width: calc(33.333% - 20px);
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .community-card:hover {
            transform: translateY(-5px);
        }

        .community-logo {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .community-details {
            padding: 15px;
        }

        .community-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .community-description {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .community-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-top: 1px solid #ddd;
        }

        .join-btn {
            padding: 8px 16px;
            font-size: 14px;
            color: #fff;
            background: #4e73df;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .join-btn:hover {
            background: #3b5fbf;
        }

        .privacy {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Communities</h1>
            <form method="GET" action="community_list.php">
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="Technology" <?= $filter_category === 'Technology' ? 'selected' : '' ?>>Technology</option>
                    <option value="Art" <?= $filter_category === 'Art' ? 'selected' : '' ?>>Art</option>
                    <option value="Music" <?= $filter_category === 'Music' ? 'selected' : '' ?>>Music</option>
                    <option value="Education" <?= $filter_category === 'Education' ? 'selected' : '' ?>>Education</option>
                </select>
            </form>
        </div>

        <div class="community-list">
            <?php if (count($communities) > 0): ?>
                <?php foreach ($communities as $community): ?>
                    <div class="community-card">
                        <img src="<?= htmlspecialchars($community['logo']); ?>" alt="Community Logo" class="community-logo">
                        <div class="community-details">
                            <div class="community-name"><?= htmlspecialchars($community['name']); ?></div>
                            <div class="community-description"><?= htmlspecialchars($community['description']); ?></div>
                        </div>
                        <div class="community-footer">
                            <span class="privacy"><?= ucfirst($community['privacy']); ?> Community</span>
                            <a href="explore_community.php?id=<?= $community['id']; ?>" class="join-btn">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No communities found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>