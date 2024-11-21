<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS for Profile -->
    
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
                            <h4>John Doe</h4>

                        </div>

                        <!-- Bio Section -->
                        <div class="bio-section">
                            <h5 class="font-weight-bold">Bio</h5>
                         
                        </div>

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
