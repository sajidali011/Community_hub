<?php
// Include your database connection file
include('db_connection.php');

// Start session to get logged in user's information
session_start();

// Get the current user id (assuming user is logged in)
$user_id = $_SESSION['user_id']; // Make sure to set this properly on login

// Fetch user data from the database
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Update user data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $bio = $_POST['bio'];

    // Handle profile image upload
    if ($_FILES['profile_image']['name']) {
        $profile_image = $_FILES['profile_image']['name'];
        $profile_image_tmp = $_FILES['profile_image']['tmp_name'];
        $profile_image_folder = "uploads/" . $profile_image;

        // Move the uploaded image to the server folder
        move_uploaded_file($profile_image_tmp, $profile_image_folder);

        // Update query to include the image path
        $update_query = "UPDATE users SET name='$name', bio='$bio', profile_image='$profile_image_folder' WHERE user_id='$user_id'";
    } else {
        // Update query without changing image
        $update_query = "UPDATE users SET name='$name', bio='$bio' WHERE user_id='$user_id'";
    }

    // Execute the update query
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php"); // Redirect to the profile page after update
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fc;
        }

        .profile-container {
            padding: 40px 0;
        }

        .profile-card {
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-image-container {
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-upload {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #3498db;
            color: white;
            border-radius: 50%;
            padding: 8px 12px;
            cursor: pointer;
            border: none;
        }

        .btn-upload:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="container profile-container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="card profile-card shadow-lg">
                    <div class="card-body">
                        <!-- Profile Image Section -->
                        <div class="profile-image-container mx-auto">
                            <img id="profileImage" src="<?php echo $user['profile_image']; ?>" alt="Profile Image" class="rounded-circle">
                            <label for="imageUpload" class="btn-upload">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>

                        <!-- Change Profile Picture Button -->
                        <input type="file" id="imageUpload" class="d-none" accept="image/*">

                        <!-- Edit Profile Form -->
                        <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>

                            <!-- Bio Field -->
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" required><?php echo $user['bio']; ?></textarea>
                            </div>

                            <!-- Profile Image Upload Field -->
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional, for interactive elements) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript for Image Upload Preview -->
    <script>
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
</html>
