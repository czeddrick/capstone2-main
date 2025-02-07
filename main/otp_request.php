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
</head>
<body>
    <div class="otp-container">
        <h1>Register </h1>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-warning">Send OTP</button>
        </form>
        <div class="privacy-policy">
            By continuing, you agree to our <a href="#">Privacy Policy</a> and <a href="#">Terms of Service</a>.
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>