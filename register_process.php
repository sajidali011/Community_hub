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

    // Handle file upload
    $uploadDir = "uploads/";
    $fileName = basename($_FILES["imgupload"]["name"]);
    $targetFilePath = $uploadDir . uniqid() . "-" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    if (in_array($fileType, ["jpg", "jpeg", "png", "gif"])) {
        if (move_uploaded_file($_FILES["imgupload"]["tmp_name"], $targetFilePath)) {
            // Insert data into database
            $sql = "INSERT INTO register (imgupload, firstname, username, email, password) 
                    VALUES ('$targetFilePath', '$firstname', '$username', '$email', '$password')";
            if (mysqli_query($conn, $sql)) {
                echo "Registration successful! <a href='login.html'>Login</a>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
}

mysqli_close($conn);
?>
