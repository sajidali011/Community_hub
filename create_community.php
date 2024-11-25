<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "community_hub";

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to create a community.'); window.location.href='login.php';</script>";
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
$user_id = ($result_user->num_rows > 0) ? $result_user->fetch_assoc()['id'] : null;

if (!$user_id) {
    echo "<script>alert('User not found.'); window.location.href='login.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $community_name = $_POST['community_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $privacy = $_POST['privacy'];

    // Handle file upload for the community logo
    $logo_file = $_FILES['logo']['name'];
    $logo_path = 'uploads/community_logos/';
    $logo_target = $logo_path . basename($logo_file);

    // Ensure the upload directory exists
    if (!file_exists($logo_path)) {
        mkdir($logo_path, 0777, true);
    }

    if ($logo_file && move_uploaded_file($_FILES['logo']['tmp_name'], $logo_target)) {
        $logo_url = $logo_target;
    } else {
        $logo_url = 'uploads/community_logos/default-logo.png'; // Default logo if none uploaded
    }

    // Insert into the communities table
    $sql_community = "INSERT INTO communities (name, description, category, logo, privacy, creator_id) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_community = $conn->prepare($sql_community);
    $stmt_community->bind_param("sssssi", $community_name, $description, $category, $logo_url, $privacy, $user_id);

    if ($stmt_community->execute()) {
        $community_id = $stmt_community->insert_id;

        // Add the creator as an admin to the community_members table
        $sql_member = "INSERT INTO community_members (community_id, user_id, role) VALUES (?, ?, 'admin')";
        $stmt_member = $conn->prepare($sql_member);
        $stmt_member->bind_param("ii", $community_id, $user_id);
        $stmt_member->execute();

        echo "<script>alert('Community created successfully!'); window.location.href='community_list.php';</script>";
    } else {
        echo "<script>alert('Error creating community. Please try again.');</script>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Community</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 20px 30px;
        }

        .card h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-group textarea {
            resize: none;
        }

        .form-group input[type="file"] {
            padding: 3px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #4e73df;
            color: #fff;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #375a7f;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>Create a Community</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="community_name">Community Name</label>
                <input type="text" id="community_name" name="community_name" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="Technology">Technology</option>
                    <option value="Art">Art</option>
                    <option value="Music">Music</option>
                    <option value="Gaming">Gaming</option>
                    <option value="Education">Education</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="privacy">Privacy</label>
                <select id="privacy" name="privacy" required>
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>
            </div>

            <div class="form-group">
                <label for="logo">Upload Logo</label>
                <input type="file" id="logo" name="logo" accept="image/*">
            </div>

            <button type="submit" class="btn">Create Community</button>
        </form>
    </div>

</body>

</html>