<?php
include '../db/connect.php';

$showSuccessModal = false;
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate address fields
    $requiredAddressFields = ['region_name', 'province_name', 'city_name', 'barangay_name'];
    foreach ($requiredAddressFields as $field) {
        if (empty($_POST[$field])) {
            die("Please complete all address fields");
        }
    }

    // Build full address
    $addressParts = [
        $_POST['barangay_name'],
        $_POST['city_name'],
        $_POST['province_name'],
        $_POST['region_name']
    ];
    $fullAddress = implode(', ', array_filter($addressParts));

    // Validate other fields
    if (isset($_POST['email'], $_POST['first_name'], $_POST['surname'], $_POST['phone'], $_POST['password'], $_POST['gender'], $_POST['confirm_password'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $errorMessage = "<div class='alert alert-danger'>Passwords do not match!</div>";
        } else {
            // Sanitize inputs
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
            $surname = mysqli_real_escape_string($conn, $_POST['surname']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $gender = mysqli_real_escape_string($conn, $_POST['gender']);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO users 
                (first_name, surname, phone, address, password, gender, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssss",
                $first_name,
                $surname,
                $phone,
                $fullAddress,
                $password,
                $gender,
                $email
            );

            if ($stmt->execute()) {
                $showSuccessModal = true;
            } else {
                $errorMessage = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
        }
    } else {
        $errorMessage = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    }
}

$conn->close();
?>
<style>
  select[disabled] {
    background: #f5f5f5;
    cursor: wait;
}

.loading::after {
    content: " (Loading...)";
    color: #666;
    font-style: italic;
}
</style>

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
  <?php if ($errorMessage): ?>
    <div class="container mt-3"><?= $errorMessage ?></div>
  <?php endif; ?>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Registration Successful!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Your account has been created successfully. You can now login.
        </div>
        <div class="modal-footer">
          <a href="user_login.php" class="btn btn-primary">Go to Login</a>
        </div>
      </div>
    </div>
  </div>

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
            <div class="col-md-6">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="text" class="form-control" id="phone" name="phone" placeholder="639XXXXXXXXX" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
          </div>

          <!-- Address Selection -->
          <div class="form-group mb-3">
            <label>Region</label>
            <select id="region" class="form-control" required>
                <option value="">Loading regions...</option>
            </select>
            <input type="hidden" name="region_name" id="region_name">
          </div>

          <div class="form-group mb-3">
            <label>Province</label>
            <select id="province" class="form-control" disabled required>
                <option value="">Select region first</option>
            </select>
            <input type="hidden" name="province_name" id="province_name">
          </div>

          <div class="form-group mb-3">
            <label>City/Municipality</label>
            <select id="city" class="form-control" disabled required>
                <option value="">Select province first</option>
            </select>
            <input type="hidden" name="city_name" id="city_name">
          </div>

          <div class="form-group mb-3">
            <label>Barangay</label>
            <select id="barangay" class="form-control" disabled required>
                <option value="">Select city first</option>
            </select>
            <input type="hidden" name="barangay_name" id="barangay_name">
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


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // API Configuration
    const API_BASE = 'https://psgc.gitlab.io/api';

    // Region Selector
    fetch(${API_BASE}/regions/)
        .then(res => res.json())
        .then(regions => {
            const select = document.getElementById('region');
            select.innerHTML = '<option value="">Select Region</option>';
            regions.forEach(region => {
                select.innerHTML += <option value="${region.code}">${region.regionName}</option>;
            });
            select.disabled = false;
        });

    // Province Loader
    document.getElementById('region').addEventListener('change', function() {
        const regionCode = this.value;
        document.getElementById('region_name').value = this.options[this.selectedIndex].text;
        
        fetch(${API_BASE}/regions/${regionCode}/provinces)
            .then(res => res.json())
            .then(provinces => {
                const select = document.getElementById('province');
                select.innerHTML = '<option value="">Select Province</option>';
                provinces.forEach(province => {
                    select.innerHTML += <option value="${province.code}">${province.name}</option>;
                });
                select.disabled = false;
                document.getElementById('city').disabled = true;
                document.getElementById('barangay').disabled = true;
            });
    });

    // City Loader
    document.getElementById('province').addEventListener('change', function() {
        const provinceCode = this.value;
        document.getElementById('province_name').value = this.options[this.selectedIndex].text;
        
        fetch(${API_BASE}/provinces/${provinceCode}/cities-municipalities)
            .then(res => res.json())
            .then(cities => {
                const select = document.getElementById('city');
                select.innerHTML = '<option value="">Select City/Municipality</option>';
                cities.forEach(city => {
                    select.innerHTML += <option value="${city.code}">${city.name}</option>;
                });
                select.disabled = false;
                document.getElementById('barangay').disabled = true;
            });
    });

    // Barangay Loader
    document.getElementById('city').addEventListener('change', function() {
        const cityCode = this.value;
        document.getElementById('city_name').value = this.options[this.selectedIndex].text;
        
        fetch(${API_BASE}/cities-municipalities/${cityCode}/barangays)
            .then(res => res.json())
            .then(barangays => {
                const select = document.getElementById('barangay');
                select.innerHTML = '<option value="">Select Barangay</option>';
                barangays.forEach(brgy => {
                    select.innerHTML += <option value="${brgy.code}">${brgy.name}</option>;
                });
                select.disabled = false;
            });
    });

    // Update barangay name when selected
    document.getElementById('barangay').addEventListener('change', function() {
        document.getElementById('barangay_name').value = this.options[this.selectedIndex].text;
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const region = document.getElementById('region_name').value;
        const province = document.getElementById('province_name').value;
        const city = document.getElementById('city_name').value;
        const barangay = document.getElementById('barangay_name').value;

        if (!region || !province || !city || !barangay) {
            e.preventDefault();
            alert('Please complete all address fields.');
        }
    });
 
    // Password Validation
    document.getElementById('confirm_password').addEventListener('input', function() {
      const password = document.getElementById('password').value;
      const confirmPassword = this.value;
      this.setCustomValidity(password !== confirmPassword ? 'Passwords do not match' : '');
    });

    document.getElementById('password').addEventListener('input', function() {
      const password = this.value;
      const passwordHelp = document.getElementById('password_help');
      const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
      
      if (!strongPassword.test(password)) {
        passwordHelp.style.color = 'red';
        this.setCustomValidity('Password does not meet requirements');
      } else {
        passwordHelp.style.color = 'green';
        this.setCustomValidity('');
      }
    });

    // OTP Handling
    document.getElementById('send-otp-btn').addEventListener('click', async function() {
      let phoneNumber = document.getElementById('phone').value;
      
      if (/^09\d{9}$/.test(phoneNumber)) {
        phoneNumber = '63' + phoneNumber.substring(1);
      } else if (!/^639\d{9}$/.test(phoneNumber)) {
        alert('Invalid phone number format');
        return;
      }

      try {
        const response = await fetch('sms_otp.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ phone: phoneNumber })
        });

        const result = await response.json();
        if (result.success) {
          document.getElementById('otp-section').style.display = 'block';
          startTimer(60);
        }
      } catch (error) {
        console.error('Error:', error);
      }
    });

    function startTimer(duration) {
      let timer = duration;
      const timerDisplay = document.getElementById('timer');
      const countdown = setInterval(() => {
        const minutes = Math.floor(timer / 60);
        const seconds = timer % 60;
        timerDisplay.textContent = Resend in ${minutes}:${seconds.toString().padStart(2, '0')};
        
        if (--timer < 0) {
          clearInterval(countdown);
          timerDisplay.textContent = 'Resend OTP now';
        }
      }, 1000);
    }

    <?php if ($showSuccessModal): ?>
      window.onload = function() {
        new bootstrap.Modal(document.getElementById('successModal')).show();
      };
    <?php endif; ?>
   
  </script>
</body>
</html>