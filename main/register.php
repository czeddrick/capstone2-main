<?php
session_start(); // Start the session
include '../db/connect.php';
include '../home/confignav.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['email'], $_POST['first_name'], $_POST['surname'], $_POST['phone'], $_POST['address'], $_POST['password'], $_POST['confirm_password'], $_POST['gender'], $_POST['otp'])
    ) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $_SESSION['error'] = "Passwords do not match!"; // Store error message in session
            header("Location: register.php"); // Redirect back
            exit;
        }

        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $surname = mysqli_real_escape_string($conn, $_POST['surname']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);

        $allowed_genders = ['Male', 'Female', 'Other'];
        if (!in_array($gender, $allowed_genders)) {
            $_SESSION['error'] = "Invalid gender selected!";
            header("Location: register.php");
            exit;
        }

        $sql = "INSERT INTO users (email, first_name, surname, phone, address, password, gender) 
                VALUES ('$email', '$first_name', '$surname', '$phone', '$address', '$password', '$gender')";

        if ($conn->query($sql) === TRUE) {
          $_SESSION['success'] = "Registration successful! You can now log in."; // Store success message
          header("Location: register.php");
          exit;
        }else {
            $_SESSION['error'] = "Error: " . $conn->error;
            header("Location: register.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: register.php");
        exit;
    }
}


if (!isset($_SESSION['email_verified']) || $_SESSION['email_verified'] !== true) {
  header("Location: otp_request.php");
  exit();
}

// Retrieve the registered email
$registeredEmail = isset($_SESSION['registered_email']) ? $_SESSION['registered_email'] : '';
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>

    
#otp-section {
  display: none; /* Hidden by default */
}
</style>
<style>
   .login-container {
    display: flex;
    flex-direction: row; /* Side-by-side layout */
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 1300px; /* Adjust as needed */
    margin: 0 auto; /* Center the container */
    overflow: hidden;
    margin-top: 40px;
}

.login-container .image-container {
    width: 35%; /* Half of the container */
    height: auto; /* Adjust height automatically */
    background: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.login-container .image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensure the image covers the container */
}

.login-container .login-form {
    width: 65%; /* Half of the container */
    padding: 30px;
}
    .card {
        border: none;
    }

    .card-header {
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 5px;
    }

    .card-header h3 {
        margin: 0;
    }


    .form-control {
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    .custom-btn {
    background: linear-gradient(45deg, #ffcc00,rgb(211, 198, 179)); /* Gradient effect */
    border: none;
    color: white;
    font-size: 15px;
    font-weight: bold;
    padding: 10px 35px;
    border-radius: 8px;
    
    transition: all 0.3s ease-in-out;
    cursor: not-allowed; /* Disabled by default */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.custom-btn:enabled {
    cursor: pointer;
    background: linear-gradient(45deg, #ff9900, #ff6600); /* Brighter color when enabled */
    box-shadow: 0 6px 10px rgba(255, 153, 0, 0.4);
}

.custom-btn:hover:enabled {
    background: linear-gradient(45deg, #ff8800, #ff5500);
    transform: scale(1.05); /* Slight zoom effect */
}

.custom-btn:active:enabled {
    transform: scale(0.98); /* Press effect */
    box-shadow: 0 3px 5px rgba(255, 153, 0, 0.3);
}
.small-text {
    font-size: 14px; /* Adjust the size as needed */
}
</style>
</head>
<body>
<div class="login-container">
    <!-- Image on the left -->
    <div class="image-container">
        <img src="../images/Great Wall Arts.png" alt="Login Illustration">
    </div>

    <!-- Form on the right -->
    <div class="login-form">
        <div class="card">
            <div class="card-header">
                <h3>Create an Account</h3>
            </div>
            <div class="card-body">
                <form id="registration-form" action="register.php" method="POST">
                    <!-- Personal Details -->
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="surname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="surname" name="surname" required>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($registeredEmail); ?>" readonly disabled required>
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($registeredEmail); ?>">  

                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="639XXXXXXXXX" required>
                            <button type="button" id="send-otp-btn" class="btn btn-primary mt-2">Send OTP</button>
                        </div>
                    </div>
                    <!-- OTP Section -->
                    <div class="row mb-1">
                    <div  class="row mb-3">
                    <div class="col-md-5">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" style="height: 38px;" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                       <div id="otp-section"class="col-md-6">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" style="height: 38px;" id="otp" name="otp" required>
                        </div> 
                    </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small id="password_help" class="form-text text-muted">Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a number, and a special character.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                 <!-- Centered Terms and Conditions & Button -->
                <div class="d-flex flex-column align-items-center text-center">
                    <!-- Terms and Conditions -->
                    <div class="mb-2 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the 
                            <a href="#" class="privacy-link" data-bs-toggle="modal" data-bs-target="#privacyModal">
                                terms and conditions
                            </a>.
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn custom-btn mt-2" id="signupButton" disabled>Sign Up</button>

                    <!-- Login Link -->
                    <a href="user_login.php" class="mt-2 small-text">Already have an account? Login here</a>

                </div>

        </div>
    </div>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Added 'modal-dialog-centered' -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<!-- Terms and Conditions Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"> <!-- Added 'modal-dialog-scrollable' -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>1. Introduction</h4>
                <p>Welcome to Great Wall Art. By registering for an account, you agree to abide by these Terms and Conditions. Please read them carefully before proceeding.</p>
                
                <h4>2. Eligibility</h4>
                <p>To register and use our services, you must:</p>
                <ul>
                    <li>Be at least 18 years old or have parental/guardian consent.</li>
                    <li>Provide accurate and complete registration details.</li>
                    <li>Not impersonate another person or provide false information.</li>
                </ul>

                <h4>3. Account Security</h4>
                <p>You are responsible for maintaining the confidentiality of your account credentials. You must:</p>
                <ul>
                    <li>Keep your password secure and not share it with anyone.</li>
                    <li>Immediately notify us if you suspect unauthorized access to your account.</li>
                    <li>Use a strong password and update it periodically.</li>
                </ul>

                <h4>4. Acceptable Use</h4>
                <p>By using our platform, you agree to:</p>
                <ul>
                    <li>Comply with all applicable laws and regulations.</li>
                    <li>Not engage in fraudulent activities, spamming, or hacking.</li>
                    <li>Respect other users and refrain from abusive behavior.</li>
                </ul>

                <h4>5. Privacy and Data Protection</h4>
                <p>Your personal information will be collected and used in accordance with our Privacy Policy. We take reasonable security measures to protect your data but cannot guarantee complete security.</p>

                <h4>6. Limitation of Liability</h4>
                <p>We are not responsible for any losses or damages incurred while using our platform. You use our services at your own risk.</p>

                <h4>7. Amendments</h4>
                <p>We reserve the right to update these Terms and Conditions at any time. Continued use of our services after changes means you accept the new terms.</p>

                <h4>8. Contact Information</h4>
                <p>If you have any questions regarding these Terms and Conditions, please contact us at <a href="<?php echo BASE_URL; ?>home/contact.php">greatwallartcore@gmail.com</a>.</p>

                <p>By clicking <strong>Sign up</strong>, you confirm that you have read, understood, and agree to these Terms and Conditions.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<?php if (isset($_SESSION['success'])): ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Centered modal -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var successModalEl = document.getElementById('successModal');
        if (successModalEl) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
        }
    });
</script>
<style>


    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: black;
    }

     /* Ensure modal appears on top */
        .modal {
            z-index: 1050;
        }
        /* Remove stuck backdrops */
        .modal-backdrop {
            z-index: 1040 !important;
        }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- JavaScript to Handle Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var errorModalEl = document.getElementById('errorModal');
        if (errorModalEl) {
            var errorModal = new bootstrap.Modal(errorModalEl);

            // Remove any existing modal backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Hide and then show the modal to ensure proper rendering
            errorModalEl.classList.remove("show");
            errorModalEl.style.display = "none";

            setTimeout(() => {
                errorModal.show();
            }, 100); // Small delay to ensure proper modal rendering
        }
    });
</script>

<!-- Bootstrap Bundle -->
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Show OTP section when "Send OTP" is clicked
  document.getElementById('send-otp-btn').addEventListener('click', async function () {
    const phone = document.getElementById('phone').value;

    // Validate phone number format
    if (!/^639\d{9}$/.test(phone)) {
      alert('Please enter a valid phone number (starting with 639XXXXXXXXX).');
      return;
    }

    // Send OTP request
    try {
      const response = await fetch('sms_otp.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ phone: phone }),
      });

      const result = await response.json();

      if (result.success) {
        alert('OTP sent successfully!');
        document.getElementById('otp-section').style.display = 'block'; // Show OTP section
      } else {
        alert('Error sending OTP. Please try again.');
      }
    } catch (error) {
      console.error(error);
      alert('An unexpected error occurred.');
    }
  });

// Enable/disable Sign Up button based on checkbox
    const termsCheckbox = document.getElementById('terms');
        const signupButton = document.getElementById('signupButton');
        termsCheckbox.addEventListener('change', function() {
            signupButton.disabled = !this.checked;
        });

   
</script>
<!-- JavaScript to Handle Modal -->

</body>
</html>