<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AfWWgIuFSgyu8PBCPZaSblbJ4tuRBURmBDp3lGvNAqcyJmX5zn84vfiPbbEgTviDvsI7kkHQqMSaxYcY"></script>
</head>
<body>
<?php
include 'navbar.php';
include 'db/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
  'body' => '{"data":{"attributes":{"amount":1000000,"description":"Purchase From Great Wall Arts","remarks":"Purchase form Great Wall Arts"}}}',
  'headers' => [
    'accept' => 'application/json',
    'authorization' => 'Basic c2tfdGVzdF90Yk1qRG1qZWVvWDRrQ3lQVzRZc0NqWU46',
    'content-type' => 'application/json',
  ],
]);
 // echo $response->getBody(); 

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT first_name, surname, email, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

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
    echo "<div class='alert alert-danger'>Your cart is empty! <a href='index.php'>Go back to the cart</a>.</div>";
    exit;
}

// Calculate totals
$merchandiseSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $cart));
$shippingSubtotal = 40;
$voucherDiscount = 15;
$totalPayment = $merchandiseSubtotal + $shippingSubtotal - $voucherDiscount;

// Handle order placement
$orderSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $voucher_used = "10% Off"; // Example voucher
    $discount = $merchandiseSubtotal * 0.10; // Calculate 10% discount
    $placed_on = date("Y-m-d H:i:s");
    $status = "Pending";

    foreach ($cart as $item) {
        // Calculate fields for binding
        $name = $user['first_name'] . " " . $user['surname'];
        $number = $user['phone'];
        $email = $user['email'];
        $address = $user['address'];
        $payment_method = "Cash on Delivery"; // Replace with actual payment method
        $message = ""; // Add user message or leave empty
        $product_name = $item['product_name'];
        $quantity = $item['quantity'];
        $total_price = $item['price'] * $item['quantity']; // Total price for the product
        $image = $item['image'];

        // Prepare the query
        $insert_order = $conn->prepare("
                INSERT INTO orders 
                (user_id, product_id, name, product_name, number, email, address, payment_method, voucher_used, total_products, total_price, placed_on, status, message, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");


        // Bind the variables
        $insert_order->bind_param(
            "iissssssissssss",
            $user_id,
            $item['product_id'], // Include product_id
            $name,
            $product_name,
            $number,
            $email,
            $address,
            $payment_method,
            $voucher_used,
            $quantity,
            $total_price,
            $placed_on,
            $status,
            $message,
            $image
        );
        // Execute the query
        if (!$insert_order->execute()) {
            echo "<div class='alert alert-danger'>Error placing order for product {$product_name}: " . $insert_order->error . "</div>";
        }

        $insert_order->close();
    }

    // Clear the user's cart after inserting all products
    $clear_cart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clear_cart->bind_param("i", $user_id);
    $clear_cart->execute();
    $clear_cart->close();

    $orderSuccess = true; // Set success flag
}

?>
<div class="container my-5">
    <?php if ($orderSuccess): ?>
        <div class="alert alert-success text-center">
            <h2 class="text-dark">Order Successfully Placed!</h2>
            <p class="text-center">Thank you for shopping with us. Your order is now being processed.</p>
            <a href="index.php" class="btn btn-dark">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> Delivery Address</h5>
                <p class="card-text">
                    <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['surname']); ?> 
                    (<?php echo htmlspecialchars($user['phone']); ?>)</strong><br>
                    <?php echo nl2br(htmlspecialchars($user['address'])); ?>
                </p>
                <a href="edit_account.php" class="btn btn-dark btn-sm">Edit</a>
            </div>
        </div>

        <h1>Checkout</h1>
        <form method="post">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($item['image']); ?>" style="width: 80px;"></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
<!-- Button to Trigger Modal -->
<div class="d-flex justify-content-between mb-3">
  <button type="button" class="btn btn-dark w-50 me-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
    Choose Payment Method
  </button>
  <button type="button" class="btn btn-warning w-50" data-bs-toggle="modal" data-bs-target="#voucherModal">
    Choose Voucher
  </button>
</div>

<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voucherModalLabel">Choose Voucher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Select a voucher from the available options:</p>
        <div class="list-group">
          <button type="button" class="list-group-item list-group-item-action">10% Off Voucher</button>
          <button type="button" class="list-group-item list-group-item-action">Free Shipping Voucher</button>
          <button type="button" class="list-group-item list-group-item-action">Buy 1 Get 1 Voucher</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Choose Payment Method</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- PayMongo Button -->
          <div class="col-12 mb-3">
            <button id="paymongo-button" class="btn btn-dark text-white w-100">
              Payment Center / E-Wallet / Online Banking
            </button>
          </div>

          <!-- PayPal Button -->
          <div class="col-12 mb-3">
            <div id="paypal-button-container" class="my-2"></div>
          </div>

          <!-- Cash on Delivery Button -->
          <div class="col-12">
            <button type="submit" name="place_order" class="btn btn-dark w-100">
              Cash on Delivery (COD)
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// PayMongo Button Click Event
document.getElementById('paymongo-button').addEventListener('click', function (e) {
  e.preventDefault();

  const button = this;
  button.disabled = true;
  button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

  fetch('create_paymongo_link.php')
    .then(response => response.json())
    .then(data => {
      if (data.checkout_url) {
        window.location.href = data.checkout_url;
      } else {
        alert('No checkout URL received. Please try again.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error creating payment link: ' + error.message);
      button.disabled = false;
      button.innerHTML = 'Payment Center / E-Wallet / Online Banking';
    });
});

// Initialize PayPal Button
paypal.Buttons().render('#paypal-button-container');
</script>

<!-- Bootstrap and PayPal SDK -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="mb-4">
    <label for="message" class="form-label">Message us!</label>
    <input type="text" class="form-control" id="message" placeholder="Please leave a message...">
</div>


            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Payment Summary</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Merchandise Subtotal:</span>
                        <span>₱<?php echo number_format($merchandiseSubtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Shipping Subtotal:</span>
                        <span>₱<?php echo number_format($shippingSubtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Voucher Discount:</span>
                        <span>-₱<?php echo number_format($voucherDiscount, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total Payment:</strong>
                        <strong>₱<?php echo number_format($totalPayment, 2); ?></strong>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
  <a href="cart.php" class="btn btn-dark me-2">Back to Cart</a>
  <button type="submit" name="place_order" class="btn btn-warning">Place Order</button>
</div>

        </form>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>