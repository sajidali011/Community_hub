<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- Custom styles for this page-->
    <style>
        body {
            background: linear-gradient(135deg, rgba(0, 204, 255, 0.8), rgba(0, 153, 255, 0.8));
            background-size: cover;
            font-family: 'Nunito', sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-image-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
            text-align: center;
        }

        #profileImage {
            border-radius: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .btn-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #4e73df;
            color: white;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-upload:hover {
            background-color: #2e59d9;
        }

        .btn-upload i {
            font-size: 18px;
        }

        .form-control {
            border-radius: 10px;
            padding: 15px;
        }

        .btn-user {
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 50px;
            transition: transform 0.2s ease;
        }

        .btn-user:hover {
            transform: scale(1.05);
        }

        .btn-google, .btn-facebook {
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 50px;
            transition: transform 0.2s ease;
        }

        .btn-google:hover, .btn-facebook:hover {
            transform: scale(1.05);
        }

        .text-center a {
            font-size: 14px;
            color: #4e73df;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>

                            <!-- Profile Image Section -->
                            <div class="profile-image-container mx-auto">
                                <img id="profileImage" src="https://via.placeholder.com/150" alt="Profile Image">
                                <label for="imageUpload" class="btn-upload">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>

                            <!-- Change Profile Picture Button -->
                            <input type="file" id="imageUpload" name="imgupload" class="d-none" accept="image/*">

                            <form class="user" action="register_process.php" method="POST" enctype="multipart/form-data">
    <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                   name="firstname" placeholder="First Name" required>
        </div>
        <div class="col-sm-6">
            <input type="text" class="form-control form-control-user" id="exampleLastName"
                   name="username" placeholder="User Name" required>
        </div>
    </div>
    <div class="form-group">
        <input type="email" class="form-control form-control-user" id="exampleInputEmail"
               name="email" placeholder="Email Address" required>
    </div>
    <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <input type="password" class="form-control form-control-user"
                   id="exampleInputPassword" name="password" placeholder="Password" required>
        </div>
    </div>
    <!-- Profile Image -->
    <input type="file" id="imageUpload" name="imgupload" class="d-none" accept="image/*" required>
    <button type="submit" class="btn btn-primary btn-user btn-block">
        Register Account
    </button>
</form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.html">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.html">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

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
