<?php
session_start();
include '../db/connect.php';

// Proceed with login
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['surname'];

            header("Location: ../index.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            display: flex;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        .login-container .image-container {
            flex: 1;
            background: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0; /* Remove padding to ensure the image fits perfectly */
            overflow: hidden; /* Ensure the image doesn't overflow */
        }
        .login-container .image-container img {
            width: 100%; /* Ensure the image takes up the full width */
            height: 100%; /* Ensure the image takes up the full height */
            object-fit: cover; /* Ensure the image covers the container without distortion */
        }
        .login-container .login-form {
            flex: 1;
            padding: 40px;
        }
        .login-form h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
        .social-login {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            color: #333;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .social-btn:hover {
            background: black;
        }
        .social-btn i, .social-btn svg {
            margin-right: 8px;
        }
        .line-separator {
            width: 1px;
            background: black;
            height: 40px;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #333;
            transition: background 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .position-relative {
            margin-bottom: 20px;
        }
        .position-relative button {
            background: none;
            border: none;
            cursor: pointer;
        }
        .position-relative button i {
            color: #666;
        }
        .center-links {
            text-align: center;
            margin-top: 15px; /* Adjust spacing */
        }
        .center-links a {
            display: block; /* Make links appear on separate lines */
            margin-bottom: 10px; /* Spacing between links */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="image-container">
            <img src="../images/Great Wall Arts.png" alt="Login Illustration">
        </div>
        <div class="login-form">
            <h1 class="text-center">Login</h1>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <form action="user_login.php" method="POST" onsubmit="return validateRecaptcha();">
                <input type="email" class="form-control mb-3" name="username" placeholder="Email" required>
                <div class="position-relative mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <a href="#" class="d-block mb-3 text-dark text-decoration-none">Forgot password?</a>
                <div class="g-recaptcha" data-sitekey="6Lci_U4qAAAAADpnsZ7iksRyKzezJJp2E5jsn_nf"></div>
                <hr>
                <p class="text-center mb-3">Login with</p>
                <div class="social-login">
                    <a href="login_facebook.php" class="btn btn-outline-dark social-btn">
                        <i class="fab fa-facebook text-primary"></i> Facebook
                    </a>
                    <div class="line-separator"></div>
                    <a href="login_google.php" class="btn btn-outline-dark social-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48" style="margin-right: 8px;">
                            <path fill="#EA4335" d="M24 9.5c3.08 0 5.84 1.13 8.02 2.99l6.02-6.02C34.8 3.15 29.79 1 24 1 14.95 1 7.32 6.3 3.83 14.04l7.41 5.82C13.1 14.47 17.76 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M24 44c5.74 0 10.6-1.9 14.07-5.17l-6.6-5.39c-2.18 1.47-5.07 2.34-7.47 2.34-5.76 0-10.68-3.89-12.41-9.31H4.72v5.82C8.28 38.66 15.63 44 24 44z"/>
                            <path fill="#FBBC05" d="M4.72 28.27C3.81 25.94 3.31 23.26 3.31 20.5c0-2.76.5-5.44 1.41-7.77V6.91H4.73L4.72 28.27z"/>
                            <path fill="#34A853" d="M44 20.5c0-2.77-.5-5.44-1.41-7.77H24v8.91h11.57c-.48 2.74-2.23 4.74-4.54 5.86l7.41 5.82C41.12 29.93 44 25.58 44 20.5z"/>
                        </svg>
                        Google
                    </a>
                </div>
                <button type="submit" name="login" class="btn btn-warning w-100 mt-3">Login</button>
                
            </form>
            <div class="center-links">
                <a href="otp_request.php">Create an Account</a>
                <a href="../index.php">Browse without an account</a>
            </div>

            <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
        </div>
    </div>

    <?php if (isset($error)) { ?>
        <!-- Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="errorModalLabel">Login Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo $error; ?>
                    </div>
                    <div class="modal-footer ">
                        
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Show the error modal automatically if there's an error
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        </script>
    <?php } ?>

    <div class="modal fade" id="recaptchaErrorModal" tabindex="-1" aria-labelledby="recaptchaErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="recaptchaErrorModalLabel">Verification Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Please verify that you're not a robot.
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const toggleIcon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        function validateRecaptcha() {
            var response = grecaptcha.getResponse();
            if (response.length === 0) {
                alert("Please verify that you're not a robot.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>