<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <!-- Bootstrap CSS (latest version) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS for Profile -->
    <style>
        body {
            background: #f4f7fc;
            font-family: 'Nunito', sans-serif;
        }

        .profile-container {
            padding: 40px 0;
        }

        .profile-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
            text-align: center;
            margin: 0 auto;
        }

        .profile-image-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .btn-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-upload:hover {
            background-color: #0056b3;
        }

        .bio-section {
            margin-top: 20px;
        }

        .bio-section h5 {
            font-weight: bold;
            color: #007bff;
        }

        .bio-section p {
            font-size: 1.1rem;
            color: #555;
        }

        .profile-info h4 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }

        .card-body {
            text-align: center;
        }

        .profile-card .btn {
            border-radius: 30px;
            padding: 10px 30px;
            font-size: 1.1rem;
            margin-top: 30px;
            background-color: #007bff;
            color: white;
            border: none;
        }

        .profile-card .btn:hover {
            background-color: #0056b3;
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
                            <img id="profileImage" src="https://via.placeholder.com/150" alt="Profile Image" class="rounded-circle">
                            <label for="imageUpload" class="btn-upload">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>

                        <!-- Change Profile Picture Button -->
                        <input type="file" id="imageUpload" class="d-none" accept="image/*">

                        <!-- User Info Section -->
                        <div class="profile-info">
                            <h4 id="username">John Doe</h4>
                        </div>

                        <!-- Bio Section -->
                        <div class="bio-section">
                            <h5 class="font-weight-bold">Bio</h5>
                            <p id="bio">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus lacinia odio vitae vestibulum.</p>
                        </div>

                        <!-- Edit Profile Button -->
                        <a href="edit-profile.html" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional, if you want to use dropdowns or other interactive elements) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript for Image Upload -->
    <script>
        // Image upload functionality
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

        // Fetch user details using JavaScript (mock data for now)
        window.onload = function() {
            // Fetch details from a server (e.g., login_process.php) in a real-world scenario
            var userDetails = {
                username: 'Jane Doe',
                bio: 'A software developer who loves creating web applications.',
                profileImage: 'https://via.placeholder.com/150' // URL to the user profile image
            };

            // Set the fetched details
            document.getElementById('username').textContent = userDetails.username;
            document.getElementById('bio').textContent = userDetails.bio;
            document.getElementById('profileImage').src = userDetails.profileImage;
        };
    </script>

</body>

</html>
