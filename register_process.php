<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "community_hub";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Plain text for now

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM register WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($result) > 0) {
        // Email already exists
        echo "<script>alert('This email is already registered. Please use a different email.');</script>";
    } else {
        // Check if file is uploaded
        if (isset($_FILES['imgupload']) && $_FILES['imgupload']['error'] == 0) {
            // Define upload directory
            $uploadDir = "uploads/";

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create directory with write permissions
            }

            $fileName = basename($_FILES["imgupload"]["name"]);
            $targetFilePath = $uploadDir . uniqid() . "-" . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Check if file type is allowed
            if (in_array($fileType, ["jpg", "jpeg", "png", "gif"])) {
                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["imgupload"]["tmp_name"], $targetFilePath)) {
                    // Insert data into database with the image URL (relative path)
                    $sql = "INSERT INTO register (imgupload, firstname, username, email, password) 
                            VALUES ('$targetFilePath', '$firstname', '$username', '$email', '$password')";
                    if (mysqli_query($conn, $sql)) {
                        echo "<script>alert('Registration successful! Please login.'); window.location.href='login.php';</script>";
                    } else {
                        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error uploading the file. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
            }
        } else {
            echo "<script>alert('Please select a profile image.');</script>";
        }
    }
}

mysqli_close($conn);
?>
