<?php
// Include database connection
include '../db/connect.php';

// Function to validate input
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to check if a user already exists
function user_exists($username, $email, $conn) {
    $sql = "SELECT * FROM admin_users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error); // Display the exact error
    }
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;  // Return true if the user exists
}

// Function to register a new user
function register_user($username, $email, $password, $conn) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);  // Hash the password
    $sql = "INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error); // Display the exact error
    }
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    return $stmt->execute();  // Return true if registration is successful
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = validate_input($_POST['username']);
    $email = validate_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Check if the user already exists
        if (user_exists($username, $email, $conn)) {
            $error_message = "Username or Email is already taken!";
        } else {
            // Attempt to register the user
            if (register_user($username, $email, $password, $conn)) {
                // Redirect to login page on successful registration
                header('Location: admin_login.php');
                exit();
            } else {
                $error_message = "An error occurred while registering. Please try again!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="admin_register.php" method="POST" class="form-box">
            <h2 class="form-title">Register</h2>

            <!-- Username -->
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required>
            </div>

            <!-- Email -->
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required>
            </div>

            <!-- Password -->
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <!-- Confirm Password -->
            <div class="input-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="register" class="submit-btn">Register</button>

            <!-- Links for login -->
            <div class="links">
                <a href="admin_login.php">Already have an account? Login</a>
            </div>

            <!-- Error Message -->
            <?php if (isset($error_message)) { ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php } ?>
        </form>
    </div>
</body>
</html>


<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Center the form */
.form-container {
    width: 100%;
    max-width: 400px;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Form title */
.form-title {
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
}

/* Input fields */
.input-group {
    margin-bottom: 15px;
}

.input-group label {
    font-size: 14px;
    color: #555;
}

.input-group input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.input-group input:focus {
    border-color: #0056b3;
    outline: none;
}

/* Submit button */
.submit-btn {
    width: 100%;
    padding: 10px;
    background-color: #0056b3;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.submit-btn:hover {
    background-color: #004080;
}

/* Links */
.links {
    text-align: center;
    margin-top: 15px;
}

.links a {
    color: #0056b3;
    text-decoration: none;
    font-size: 14px;
}

.links a:hover {
    text-decoration: underline;
}
</style>