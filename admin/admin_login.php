<?php
// Include database connection
include '../db/connect.php';

// Function to validate input
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to authenticate user login
function authenticate_user($username, $password, $conn) {
    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            return $user; // Return user data on successful login
        }
    }
    return false; // Failed login
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = validate_input($_POST['username']);
    $password = $_POST['password'];

    // Attempt to authenticate the user
    $user = authenticate_user($username, $password, $conn);
    if ($user) {
        // Set session variables
        
      

        // Redirect to dashboard after successful login
        header('Location: /capstone2/admin/dashboard.php');
        exit();
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="admin_login.php" method="POST" class="form-box">
            <h2 class="form-title">Login</h2>
            
            <!-- Username -->
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required>
            </div>

            <!-- Password -->
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="login" class="submit-btn">Login</button>

            <!-- Links for registration and forgot password -->
            <div class="links">
                <a href="admin_register.php">Create an Account</a> | <a href="#">Forgot Password?</a>
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