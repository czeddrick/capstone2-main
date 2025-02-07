<?php
require_once '../vendor/autoload.php';

session_start();

$fb = new Facebook\Facebook([
    'app_id' => '666274249155260',
    'app_secret' => '60944545b891bb33e2de2e4b5a0bc3fb',
    'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();
$loginUrl = $helper->getLoginUrl('http://localhost/capstone2/index.php', ['email']); // Redirect back to login.php

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}



header('Location: ' . $loginUrl);
exit();
?>


