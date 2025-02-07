<?php
session_start();
include 'db/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit(json_encode(['error' => 'Unauthorized']));
}

// Calculate cart total
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
    header('HTTP/1.1 400 Bad Request');
    exit(json_encode(['error' => 'Cart is empty']));
}

// Calculate totals
$merchandiseSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $cart));
$shippingSubtotal = 40;
$voucherDiscount = 15;
$totalPayment = ($merchandiseSubtotal + $shippingSubtotal - $voucherDiscount) * 100; // Convert to cents

require_once('vendor/autoload.php');

try {
    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
        'headers' => [
            'accept' => 'application/json',
            'authorization' => 'Basic c2tfdGVzdF90Yk1qRG1qZWVvWDRrQ3lQVzRZc0NqWU46',
            'content-type' => 'application/json',
        ],
        'json' => [
            'data' => [
                'attributes' => [
                    'amount' => $totalPayment,
                    'description' => 'Order Payment',
                    'remarks' => 'Purchase from Great Wall Arts'
                ]
            ]
        ]
    ]);

    $responseBody = json_decode($response->getBody(), true);
    
    if (isset($responseBody['data']['attributes']['checkout_url'])) {
        echo json_encode([
            'checkout_url' => $responseBody['data']['attributes']['checkout_url']
        ]);
    } else {
        throw new Exception('Invalid PayMongo response');
    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}