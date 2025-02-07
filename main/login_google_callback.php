<?php
require_once '../vendor/autoload.php'; // Include Google API Client
require_once '../db/connect.php'; // Include your database connection file

session_start();

$client = new Google_Client();
$client->setClientId('745050248523-lntke8lat215dr1raid80fn35idhrjsa.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhostcapstone2/main/login_google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    // Extract user data
    $googleId = $userInfo->getId();
    $name = $userInfo->getName();
    $email = $userInfo->getEmail();

    // Check if the user already exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE google_id = ?");
    $stmt->bind_param("s", $googleId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (google_id, name, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $googleId, $name, $email);
        $stmt->execute();
    }

    // Store user info in session
    $_SESSION['user_id'] = $stmt->insert_id ?? $stmt->fetch()->id; // Use the newly inserted ID or existing ID
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Redirect to index.php
    header('Location: index.php');
    exit();
} else {
    // Handle error
    header('Location: login.php');
    exit();
}
?>