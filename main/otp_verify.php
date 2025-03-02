<?php
session_start();

// Check if the OTP and email are set in the session
if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email'])) {
    header("Location: otp_request.php"); 
    exit();
}

// Check if the OTP has expired
if (isset($_SESSION['otp_expiry']) && time() > $_SESSION['otp_expiry']) {
    echo "<script>alert('OTP has expired. Please request a new one.');</script>";
    unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_expiry']);
    header("Location: otp_request.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userOTP = implode('', $_POST['otp']);

    if ($userOTP == $_SESSION['otp']) {
        $_SESSION['email_verified'] = true;
        $_SESSION['registered_email'] = $_SESSION['otp_email']; // Ensure email is available in register.php
        header("Location: register.php");
        exit();
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }
        h1 {
            color: #333;
        }
        .otp-input-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .otp-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        button {
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
        }
        button:hover {
            background-color: black;
            color: white;
        }
        .timer {
            color: #d9534f;
            font-size: 14px;
        }
        .back-link {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
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
            position: center;
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
    <div class="container" style="margin-top:150px;">
        <h1>Enter Verification Code</h1>
        <p>Your verification code was sent to <span class="text-bold text-warning"><?php echo $_SESSION['otp_email']; ?></span></p>
        <form method="POST">
            <div class="otp-input-group">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                <?php endfor; ?>
            </div>
            <div id="timer-display" class="timer"></div>
            <button type="submit">Verify OTP</button>
        </form>
        <a href="otp_request.php" class="back-link">&larr; Back</a>
    </div>
    </div>
    
    
           



    <script>
        let timer = 50; // Set the timer in seconds
        const countdownElement = document.getElementById('timer-display');

        function startCountdown() {
            const timerDisplay = document.getElementById('timer-display');
            const countdown = setInterval(() => {
                if (timer > 0) {
                    timer--;
                    timerDisplay.textContent = `Please input the OTP within ${timer} seconds.`;
                } else {
                    clearInterval(countdown);
                    alert("Time's up! Please request a new OTP.");
                    window.location.href = "otp_request.php";
                }
            }, 1000);
        }

        function moveToNextInput(event) {
            const input = event.target;
            const maxLength = input.maxLength;
            if (input.value.length === maxLength) {
                const nextInput = input.nextElementSibling;
                if (nextInput) nextInput.focus();
            }
        }

        function handleBackspace(event) {
            if (event.key === 'Backspace' && event.target.value === '') {
                const previousInput = event.target.previousElementSibling;
                if (previousInput) previousInput.focus();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.otp-input').forEach(input => {
                input.addEventListener('input', moveToNextInput);
                input.addEventListener('keydown', handleBackspace);
            });
            startCountdown(); // Start the countdown timer
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>