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

// Query to fetch current user data from the 'register' table using email
$sql = "SELECT imgupload, firstname, username, bio FROM register WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imgupload = $row['imgupload'] ? $row['imgupload'] : 'default-avatar.png';
    $firstname = $row['firstname'];
    $username = $row['username'];
    $bio = $row['bio'];
} else {
    echo "<script>alert('No data found for this user!'); window.location.href='login.php';</script>";
    exit;
}

// Update user data after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile details
    if (isset($_POST['update_profile'])) {
        $updated_username = $_POST['username'];
        $updated_firstname = $_POST['firstname'];
        $updated_bio = $_POST['bio'];

        // Handle file upload
        $profile_image = $_FILES['profile_image']['name'];
        if ($profile_image) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
            move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        } else {
            $target_file = $imgupload; // Use existing image if none uploaded
        }

        // Update database
        $update_query = "UPDATE register SET username = ?, firstname = ?, bio = ?, imgupload = ? WHERE email = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('sssss', $updated_username, $updated_firstname, $updated_bio, $target_file, $current_email);
        $stmt->execute();
        $stmt->close();
        header('Location: settings.php');
        exit();
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Fetch current password from database
        $sql = "SELECT password FROM register WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $current_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        if (password_verify($old_password, $stored_password)) {
            if ($new_password === $confirm_password) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE register SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param('ss', $new_hashed_password, $current_email);
                $stmt->execute();
                $stmt->close();
                header('Location: settings.php');
                exit();
            } else {
                echo "<script>alert('New passwords do not match!');</script>";
            }
        } else {
            echo "<script>alert('Old password is incorrect!');</script>";
        }
    }

    // Delete account
    if (isset($_POST['delete_account'])) {
        $delete_query = "DELETE FROM register WHERE email = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param('s', $current_email);
        $stmt->execute();
        $stmt->close();
        session_destroy();
        echo "<script>alert('Your account has been deleted.'); window.location.href='index.php';</script>";
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .settings-container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .settings-container h1 {
            font-size: 30px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group input[type="file"] {
            display: none;
        }

        .upload-container {
            position: relative;
            cursor: pointer;
            display: inline-block;
            margin-bottom: 20px;
        }

        .upload-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #6a11cb;
        }

        .upload-container .upload-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 24px;
        }

        .btn-primary {
            background-color: #2575fc;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #6a11cb;
        }

        .btn-danger {
            background-color: #e74a3b;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
</head>

<body>

    <div class="settings-container">
        <h1>Settings</h1>

        <!-- Update Profile Section -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group upload-container">
                <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="previewImage(event)">
                <img id="profileImage" src="<?php echo $imgupload; ?>" alt="Profile Image">
                <div class="upload-icon">
                    <i class="fas fa-camera"></i>
                </div>
            </div>

            <div class="form-group">
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username">
            </div>

            <div class="form-group">
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" placeholder="First Name">
            </div>

            <div class="form-group">
                <textarea name="bio" rows="4" placeholder="Bio"><?php echo htmlspecialchars($bio); ?></textarea>
            </div>

            <button type="submit" name="update_profile" class="btn-primary">Update Profile</button>
        </form>

        <!-- Change Password Section -->
        <form method="POST">
            <h3>Change Password</h3>

            <div class="form-group">
                <input type="password" name="old_password" placeholder="Old Password" required>
            </div>

            <div class="form-group">
                <input type="password" name="new_password" placeholder="New Password" required>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            </div>

            <button type="submit" name="change_password" class="btn-primary">Change Password</button>
        </form>

        <!-- Delete Account Section -->
        <form method="POST">
            <h3>Delete Account</h3>
            <button type="submit" name="delete_account" class="btn-danger">Delete Account</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const image = document.getElementById('profileImage');
            image.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

</body>

</html>
