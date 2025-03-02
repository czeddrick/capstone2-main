<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Start the session
session_start();

// Function to generate a random OTP
function generateOTP($length = 6) {
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Generate a random OTP
    $otp = generateOTP();

    // Store the OTP and email in the session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes

    // Create an instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'greatwallartcore@gmail.com'; // SMTP username
        $mail->Password = 'sxwt pmaw zezm ndaj'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption
        $mail->Port = 465; // TCP port to connect to

        // Recipients
        $mail->setFrom('greatwallartcore@gmail.com', 'Great Wall Art');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Your OTP for Registration';
        $mail->Body = "Eto yung Code tanga tanga ka ba?:: <b>$otp</b>";
        $mail->AltBody = "Eto yung Code tanga tanga ka ba?:: $otp";

        // Send the email
        $mail->send();
        header("Location: otp_verify.php"); // Redirect to OTP verification page
        exit();
    } catch (Exception $e) {
        echo "Failed to send OTP. Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Request</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .otp-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;

        }
        .otp-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .otp-container .form-control {
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .otp-container .btn {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffc107;
            border: none;
            color: black;
            font-size: 16px;
        }
        .otp-container .btn:hover {
            background-color: black;
            color: white;
        }
        .privacy-policy {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .privacy-policy a {
            color: black;
            text-decoration: none;
        }
        .privacy-policy a:hover {
            text-decoration: underline;
        }
    </style>
     <style>
       
        .login-container {
            display: flex;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            height: 650px;
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
       
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
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
        <div class="otp-container"style="margin-top:150px;">
        <h1>Register</h1>
        <form method="POST" action="">
            <div class="mb-3" style="margin-top:50px;">
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-warning">Send OTP</button>
        </form>
        <div class="privacy-policy text-center" style="margin-top:180px;">
    By continuing, you agree to our 
    <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a> 
    and 
    <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a>.
</div>

        <a href="user_login.php" class="back-link">Already have an Account?</a>
    </div>
    </div>

    <!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Large modal for better readability -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;"> <!-- Scrollable content -->
                <p><strong>1. Introduction</strong></p>
                <p>Welcome to our Privacy Policy. Your privacy is critically important to us. We are committed to protecting your personal data and respecting your privacy.</p>

                <p><strong>2. Information We Collect</strong></p>
                <p>We collect various types of information, including personal data such as your name, email address, phone number, and any other data you provide when using our services.</p>

                <p><strong>3. How We Use Your Information</strong></p>
                <p>Your data helps us improve our services, process transactions, and enhance your overall experience. We do not sell your personal data to third parties.</p>

                <p><strong>4. Data Security</strong></p>
                <p>We take appropriate security measures to protect your data from unauthorized access, alteration, disclosure, or destruction.</p>

                <p><strong>5. Contact Us</strong></p>
                <p>If you have any questions about our Privacy Policy, please contact us at greatwallartcore@gmail.com.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Terms of Service Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <p><strong>1. Introduction</strong></p>
                <p>By using our services, you agree to abide by these terms. If you do not agree, please discontinue use immediately.</p>

                <p><strong>2. User Responsibilities</strong></p>
                <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>

                <p><strong>3. Prohibited Activities</strong></p>
                <p>Users may not engage in illegal activities, including but not limited to fraud, hacking, or spamming through our platform.</p>

                <p><strong>4. Service Modifications</strong></p>
                <p>We reserve the right to modify or discontinue any part of our services without prior notice.</p>

                <p><strong>5. Termination</strong></p>
                <p>We may suspend or terminate your access to our services if you violate these terms.</p>

                <p><strong>6. Contact Us</strong></p>
                <p>If you have any questions about our Terms of Service, please contact us at greatwallartcore@gmail.com.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    
           





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>

    
