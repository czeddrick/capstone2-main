
<?php 
session_start(); // Start session to get user details


// Assuming you store user details in session variables
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

 
?>
<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Start the session (if not already started)


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

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
        $mail->Subject = $subject;
        $mail->Body = "Name: $name <br> Email: $email <br> Message: $message";
        $mail->AltBody = "Name: $name \n Email: $email \n Message: $message";

        // Send the email
        $mail->send();
        $success_message = "Message sent successfully!";
    } catch (Exception $e) {
        $error_message = "Failed to send message. Error: {$mail->ErrorInfo}";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Great Wall Arts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #fdfdfd;
            font-family: 'Arial', sans-serif;
            padding-top: 85px;
        }
        .contact-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .contact-header {
            background: transparent;
            color: black;
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: 1.5rem;
        }
        .btn-custom {
            background-color: #F7F5BC;
            border: none;
            color: black;
            transition: all 0.3s ease-in-out;
        }
        .btn-custom:hover {
            background-color: #E8E337;
        }
        .form-control:focus {
            box-shadow: 0 0 5px rgba(255, 126, 95, 0.5);
        }
        .social-icons a {
            margin-right: 10px;
            font-size: 1.5rem;
            color: #6c757d;
            transition: color 0.3s ease-in-out;
        }
        .social-icons a:hover {
            color: #ff7e5f;
        }
        .newsletter-section {
            background: #ff7e5f;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .newsletter-section input {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            margin-right: 0.5rem;
        }
        .newsletter-section button {
            background-color: #feb47b;
            border: none;
            border-radius: 50px;
            color: white;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease-in-out;
        }
        .newsletter-section button:hover {
            background-color: #ffffff;
            color: #ff7e5f;
        }
        .btn-custom {
            background-color: #007bff; /* Change to your desired color */
            color: white;
            border-radius: 25px; /* Rounded edges */
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .btn-custom:hover {
            background-color: #0056b3; /* Darker shade on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

    </style>
</head>
<body>
<?php 
     include "navbar.php"; 
     ?>
    <div class="container mt-4">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-lg-6">
                <div class="contact-container p-4"style="height:800px;">
                    <div class="contact-header">Get in Touch</div>
                    <div class="p-4">
                        <iframe class="w-100 mb-3 rounded" height="250" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.690370390059!2d121.08091356862819!3d14.576299946551895!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c7e733d0dd8f%3A0x484e41941f8f66dc!2sSolen%20Building!5e1!3m2!1sfil!2sph!4v1726670394140!5m2!1sfil!2sph" loading="lazy"></iframe>
                        <h5>Address</h5>
                        <p>Solen Building C. Raymundo F corner F. Legaspi, Maybunga Pasig</p>
                        <h5>Call Us</h5>
                        <p><a href="tel:+639876543221" class="text-decoration-none text-dark"><i class="bi bi-telephone-fill me-2"></i>+639876543221</a></p>
                        <h5>Email</h5>
                        <p><a href="mailto:GWAexample@gmail.com" class="text-decoration-none text-dark"><i class="bi bi-envelope-fill me-2"></i>GWAexample@gmail.com</a></p>
                        <h5>Follow Us</h5>
                        <div class="social-icons">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
      <!-- Contact Form -->
                <div class="contact-container p-4" style="height:800px;">
                    <div class="contact-header">Send Us a Message</div>
                    <form id="contactForm" class="p-4" method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                value="<?php echo htmlspecialchars($full_name ?? ''); ?>" 
                                <?php echo isset($full_name) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                <?php echo isset($email) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject">
                        </div>
                       <!-- Message Input Field with Error Message -->
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message" required></textarea>
                                <div id="messageError" class="text-danger mt-2" style="display: none;">Please enter a message.</div> <!-- Hidden error message -->
                            </div>

                        <!-- Button to trigger modal -->
                        <!-- Button Container for Centering -->
                    <div class="d-flex justify-content-center mt-3">
                   
                        
                        <button type="button" class="btn btn-custom btn-lg px-5 shadow" id="validateButton">
                            Send Message
                        </button>
                   
                    </div>
                    </form>
                    
                </div>

    </div>
        </div>
    </div>
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php echo $success_message ?? ''; ?>
            </div>
            <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-success px-4" onclick="window.location.href='<?php echo BASE_URL; ?>home/contact.php'">
                OK
            </button>

            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php echo $error_message ?? ''; ?>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Show Modals if Messages Exist -->
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to send this message?
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Yes, Send</button>
            </div>
        </div>
    </div>
</div>


<script>

document.addEventListener("DOMContentLoaded", function () {
        <?php if (isset($success_message)): ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        <?php endif; ?>
    });
    document.getElementById('validateButton').addEventListener('click', function () {
        var message = document.getElementById('message').value.trim();
        var messageError = document.getElementById('messageError');

        if (message === "") {
            messageError.style.display = "block"; // Show error message
        } else {
            messageError.style.display = "none"; // Hide error message
            var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            confirmModal.show();
        }
    });

    // Submit the form if user confirms
    document.getElementById('confirmSubmit').addEventListener('click', function () {
        document.getElementById('contactForm').submit();
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
