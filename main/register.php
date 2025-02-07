<?php
// Include database connection
include '../db/connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if all required fields are set
    if (
        isset($_POST['email'], $_POST['first_name'], $_POST['surname'], $_POST['phone'], $_POST['address'], $_POST['password'], $_POST['gender'])
    ) {
        // Get form data and sanitize input
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $surname = mysqli_real_escape_string($conn, $_POST['surname']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);

        // Validate gender
        $allowed_genders = ['Male', 'Female', 'Other'];
        if (!in_array($gender, $allowed_genders)) {
            echo "<div class='alert alert-danger'>Invalid gender selected!</div>";
            exit;
        }

        // Prepare the SQL query
        $sql = "INSERT INTO users (email, first_name, surname, phone, address, password, gender) 
                VALUES ('$email', '$first_name', '$surname', '$phone', '$address', '$password', '$gender')";

        // Check if the query was successful
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>New record created successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/register.css">
</head>
<body>

<div class="container">
  <div class="card">
    <div class="card-header">
      <h3>Create an Account</h3>
    </div>
    <div class="card-body">
      <form action="register.php" method="POST">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="col-md-6">
            <label for="surname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="surname" name="surname" required>
          </div>
        </div>
        <div class="row mb-3">
        <div class="container my-5">
    <form id="otp-form" method="POST" action="sms_otp.php">
        <div class="row">
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="639XXXXXXXXX" required>
            </div>
        </div>

        <button type="button" id="send-otp-btn" class="btn btn-primary mt-3">Send OTP</button>

        <div id="otp-section" class="mt-3" style="display: none;">
            <label for="otp" class="form-label">Enter OTP</label>
            <input type="text" id="otp" class="form-control mb-2" placeholder="Enter OTP" required>
            <button type="button" id="validate-otp-btn" class="btn btn-success mt-2">Validate OTP</button>
            <p id="timer" class="text-danger mt-2"></p>
        </div>

        <p id="otp-result" class="mt-3"></p>
    </form>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('send-otp-btn').addEventListener('click', async function () {
    let phoneNumber = document.getElementById('phone').value;

    if (/^09\d{9}$/.test(phoneNumber)) {
        phoneNumber = '63' + phoneNumber.substring(1);
    } else if (!/^639\d{9}$/.test(phoneNumber)) {
        alert('Please enter a valid phone number (starting with 09XXXXXXXXX or 639XXXXXXXXX).');
        return;
    }

    const smsProvider = 0; // You can change this value as needed

    try {
        const response = await fetch('sms_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ phone: phoneNumber, sms_provider: smsProvider }),
        });

        const result = await response.json();

        if (result.success) {
            alert('OTP sent successfully!');
            document.getElementById('otp-section').style.display = 'block';
            localStorage.setItem('otp', result.otp);
            startTimer(60);
        } else {
            alert('Error sending OTP. Please try again.');
        }
    } catch (error) {
        console.error(error);
        alert('An unexpected error occurred.');
    }
});

document.getElementById('validate-otp-btn').addEventListener('click', function () {
    const otpInput = document.getElementById('otp').value;

    if (otpInput === localStorage.getItem('otp')) {
        showSuccessModal(); // Show the modal if OTP is correct
        document.getElementById('otp-result').textContent = 'Phone number verified.';
    } else {
        alert('Invalid OTP. Please try again.');
    }
});

// Countdown Timer
function startTimer(duration) {
    let timer = duration;
    const timerDisplay = document.getElementById('timer');
    const countdown = setInterval(() => {
        const minutes = Math.floor(timer / 120);
        const seconds = timer % 120;
        timerDisplay.textContent = `Resend OTP in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (--timer < 0) {
            clearInterval(countdown);
            timerDisplay.textContent = 'You can resend the OTP now.';
        }
    }, 1000);
}

// Show Modal for OTP Success
function showSuccessModal() {
    const modalHtml = `
        <div class="modal-overlay">
            <div class="modal-content">
                <h2>Verification Successful!</h2>
                <p>Your OTP has been verified successfully.</p>
                <button id="close-modal-btn">Close</button>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('close-modal-btn').addEventListener('click', function () {
        document.querySelector('.modal-overlay').remove();
    });
}


</script>
          <div class="col-md-6">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
        <div class="row mb-3">
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
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
          <label class="form-check-label" for="terms">
            I agree to the <a href="#" class="privacy-link" data-bs-toggle="modal" data-bs-target="#privacyModal">terms and conditions</a>.
          </label>
        </div>
        <div class="d-flex gap-3">
          <button type="submit" class="btn btn-primary">Sign Up</button>
          <a href="user_login.php"><p>Already have an account? Login here</p></a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Privacy and Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="privacyModalLabel">Privacy and Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h4>1. Introduction</h4>
        <p>We are committed to protecting your privacy. This policy outlines how we collect, use, and safeguard your personal information.</p>

        <h4>2. Information We Collect</h4>
        <p>We collect information such as your name, email address, phone number, and address when you register on our site.</p>

        <h4>3. How We Use Your Information</h4>
        <p>Your information is used to provide and improve our services, communicate with you, and ensure the security of your account.</p>

        <h4>4. Data Security</h4>
        <p>We implement industry-standard security measures to protect your data from unauthorized access, alteration, or disclosure.</p>

        <h4>5. Your Rights</h4>
        <p>You have the right to access, update, or delete your personal information at any time by contacting us.</p>

        <h4>6. Changes to This Policy</h4>
        <p>We may update this policy from time to time. Any changes will be posted on this page.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('confirm_password').addEventListener('input', function () {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    if (password !== confirmPassword) {
      this.setCustomValidity('Passwords do not match');
    } else {
      this.setCustomValidity(''); 
    }
  });

  document.getElementById('password').addEventListener('input', function () {
    const password = this.value;
    const passwordHelp = document.getElementById('password_help');
    const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!strongPassword.test(password)) {
      passwordHelp.style.color = 'red';
      this.setCustomValidity('Password does not meet the strength requirements');
    } else {
      passwordHelp.style.color = 'green';
      this.setCustomValidity('');
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>