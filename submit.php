<?php
session_start();
require 'db/connect.php';
require 'vendor/autoload.php';

$secretKey = getenv('sk_test_tbMjDmjeeoX4kCyPW4YsCjYN');

if (!isset($_GET['session_id'])) {
    die('Invalid session');
}

try {
    $client = new \GuzzleHttp\Client();
    $response = $client->get('https://api.paymongo.com/v1/checkout_sessions/' . $_GET['session_id'], [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':')
        ]
    ]);

    $sessionData = json_decode($response->getBody(), true)['data'];
    
    if ($sessionData['attributes']['payments'][0]['attributes']['status'] === 'paid') {
        // Process successful payment
        $user_id = $_SESSION['user_id'];
        
        // Move your existing order processing code here
        // Insert into orders table
        // Clear cart
        // Send confirmation email
        
        echo '<div class="alert alert-success">Payment successful! Order completed.</div>';
    } else {
        echo '<div class="alert alert-warning">Payment not completed</div>';
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Payment verification failed: ' . $e->getMessage() . '</div>';
}
?>