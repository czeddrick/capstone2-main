<?php
// Always start with output buffering
ob_start();
session_start();
header('Content-Type: application/json');

// Disable direct HTML output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', _DIR_ . '/php_errors.log');

require '../db/connect.php';
require '../vendor/autoload.php';

try {
    // Validate session first
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access', 401);
    }

    // ------ Your existing cart calculation code ------
    $user_id = $_SESSION['user_id'];
    
    // Fetch cart items
    $cart = [];
    $cart_query = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result();
    while ($row = $cart_result->fetch_assoc()) {
        $cart[] = $row;
    }
    $cart_query->close();

    if (empty($cart)) {
        throw new Exception('Cart is empty', 400);
    }

    // Calculate totals
    $merchandiseSubtotal = array_sum(array_map(function ($item) {
        return $item['price'] * $item['quantity'];
    }, $cart));
    $shippingSubtotal = 40;
    $voucherDiscount = 15;
    $totalPayment = ($merchandiseSubtotal + $shippingSubtotal - $voucherDiscount) * 100;

    // Validate total amount
    if ($totalPayment < 10000) { // Minimum 100 pesos
        throw new Exception('Minimum payment amount is ₱100.00', 400);
    }

    // ------ PayMongo Integration ------
    $secretKey = 'sk_test_tbMjDmjeeoX4kCyPW4YsCjYN'; // Replace with actual key
    
    $client = new \GuzzleHttp\Client();
    $response = $client->post('https://api.paymongo.com/v1/checkout_sessions', [
        'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'data' => [
                'attributes' => [
                    'line_items' => [[
                        'currency' => 'PHP',
                        'amount' => $totalPayment,
                        'name' => 'Order Payment',
                        'quantity' => 1
                    ]],
                    'payment_method_types' => ['gcash', 'card'],
                    'success_url' => 'https://capstone2/success.php?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => 'https://capstone2/cancel.php',
                    'metadata' => [
                        'user_id' => $user_id,
                        'cart_total' => $totalPayment
                    ]
                ]
            ]
        ]
    ]);

    $responseBody = json_decode($response->getBody(), true);
    
    if (!isset($responseBody['data']['attributes']['checkout_url'])) {
        throw new Exception('Invalid response from payment gateway', 500);
    }

    // Clean any previous output and send JSON
    ob_end_clean();
    echo json_encode(['checkout_url' => $responseBody['data']['attributes']['checkout_url']]);
    exit;

} catch (\Throwable $e) {
    // Clean buffers and send error
    ob_end_clean();
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    exit;
}