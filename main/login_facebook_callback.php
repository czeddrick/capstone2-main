<?php
require_once '../vendor/autoload.php';
require_once '../db/connect.php'; // Ensure this path is correct for your database connection file

session_start();

$fb = new Facebook\Facebook([
    'app_id' => '666274249155260',
    'app_secret' => '60944545b891bb33e2de2e4b5a0bc3fb',
    'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();

    if (!isset($accessToken)) {
        throw new Exception("Failed to get access token.");
    }

    // Get user profile from Facebook
    $response = $fb->get('/me?fields=id,name,email', $accessToken);
    $user = $response->getGraphUser();

    // Extract user data
    $facebookId = $user->getId();
    $name = $user->getName();
    $email = $user->getEmail() ?? 'No email provided'; // Handle missing email gracefully

    // Database connection debug
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Check if the user already exists in the facebook_users table
    $stmt = $conn->prepare("SELECT id FROM facebook_users WHERE facebook_id = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("s", $facebookId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // Insert new Facebook user into the database
        $stmt = $conn->prepare("INSERT INTO facebook_users (facebook_id, name, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $facebookId, $name, $email);

        if (!$stmt->execute()) {
            die("Database Insert Error: " . $stmt->error);
        }
        $_SESSION['user_id'] = $conn->insert_id; // Get the last inserted ID
    } else {
        $stmt->bind_result($existingUserId);
        $stmt->fetch();
        $_SESSION['user_id'] = $existingUserId; // Use the existing user ID
    }

    // Store user info in session
    $_SESSION['full_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Redirect to the homepage or any desired page
    header('Location: index.php');
    exit();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    header('Location: user_login.php');
    exit();
}
?>
