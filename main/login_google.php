<?php
require_once '../vendor/autoload.php'; // Include Google API Client

session_start();

$client = new Google_Client();
$client->setClientId('745050248523-lntke8lat215dr1raid80fn35idhrjsa.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/capstone2/index.php/login_google_callback.php');
$client->addScope('email');
$client->addScope('profile');

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
?>