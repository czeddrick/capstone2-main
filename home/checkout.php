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
include '../db/connect.php';
include "navbar.php";


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT first_name, surname, email, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$selected_items = $_POST['selected_items'] ?? [];

if (!is_array($selected_items)) {
    $selected_items = explode(',', $selected_items);
}

$selected_items = array_map('intval', $selected_items);
$selected_items = array_filter($selected_items, fn($item) => $item > 0);

// Fetch selected cart items
if (empty($selected_items)) {
    echo "<div class='alert alert-danger'>No items selected! <a href='cart.php'>Go back to cart</a>.</div>";
    exit;
}

$placeholders = implode(',', array_fill(0, count($selected_items), '?'));
$sql = "SELECT * FROM cart_items WHERE id IN ($placeholders) AND user_id = ?";

$stmt = $conn->prepare($sql);

$types = str_repeat('i', count($selected_items)) . 'i';  
$params = array_merge($selected_items, [$user_id]);

$stmt_params = array_merge([$types], $params);

call_user_func_array([$stmt, 'bind_param'], refValues($stmt_params));

function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

$stmt->execute();
$result = $stmt->get_result();
$cart = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($cart)) {
    echo "<div class='alert alert-danger'>No valid items found! <a href='cart.php'>Go back to cart</a>.</div>";
    exit;
}

// Calculate totals
$merchandiseSubtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
$shippingSubtotal = 40;
$voucherDiscount = $merchandiseSubtotal * 0.10; // 10% discount
$totalPayment = $merchandiseSubtotal + $shippingSubtotal - $voucherDiscount;

// Order placement
$orderSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
  $voucher_used = "10% Off"; // Example voucher
  $discount = $merchandiseSubtotal * 0.10; // Calculate 10% discount
  $placed_on = date("Y-m-d H:i:s");
  $status = "Pending";

  $conn->begin_transaction(); // Start transaction

  $orderSuccess = true; // Assume success

  foreach ($cart as $item) {
      $name = htmlspecialchars($user['first_name'] . " " . $user['surname']);
      $number = htmlspecialchars($user['phone']);
      $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
      $address = htmlspecialchars($user['address']);
      $payment_method = "Cash on Delivery";
      $message = ""; // You can add a message field if needed
      $product_name = htmlspecialchars($item['product_name']);
      $quantity = (int)$item['quantity'];
      $total_price = (float)($item['price'] * $item['quantity']);
      $image = htmlspecialchars($item['image']);

      $insert_order = $conn->prepare("
          INSERT INTO orders 
          (user_id, product_id, name, product_name, number, email, address, payment_method, voucher_used, total_products, total_price, placed_on, status, message, image) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");

      $insert_order->bind_param(
          "iissssssissssss",
          $user_id,
          $item['product_id'],
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

      if (!$insert_order->execute()) {
          $orderSuccess = false;
          echo "Error inserting order: " . $insert_order->error; // Debugging
          break;
      }

      $insert_order->close();
  }

  if ($orderSuccess) {
      // Only delete successfully ordered items
      $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
      $delete_cart = $conn->prepare("DELETE FROM cart_items WHERE id IN ($placeholders) AND user_id = ?");

      $types = str_repeat('i', count($selected_items)) . 'i';
      $params = array_merge($selected_items, [$user_id]);
      $delete_cart->bind_param($types, ...$params);

      if (!$delete_cart->execute()) {
          echo "Error deleting cart items: " . $delete_cart->error; // Debugging
          $orderSuccess = false;
      }

      $delete_cart->close();
  }

  if ($orderSuccess) {
      $conn->commit(); // Commit transaction
      echo "Transaction committed successfully."; // Debugging
  } else {
      $conn->rollback(); // Rollback if any order failed
      echo "Transaction rolled back due to errors."; // Debugging
  }
}
?>


<div class="container my-5">
    <?php if ($orderSuccess): ?>
        <div class="alert alert-success text-center" style="margin-top:120px;">
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
                            <td><img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>" style="width: 80px;"></td>
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmOrderModalLabel">Confirm Your Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to place this order?</p>
        <p><strong>Total Payment: ₱<?php echo number_format($totalPayment, 2); ?></strong></p>
      </div>
      <div class="modal-footer">
        
        <div class="d-flex justify-content-end mt-4">
  
        <form method="post">
          <?php foreach ($selected_items as $item_id): ?>
              <input type="hidden" name="selected_items[]" value="<?php echo htmlspecialchars($item_id); ?>">
          <?php endforeach; ?>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 5px";>Cancel</button>
          <button type="submit" name="place_order" class="btn btn-warning">Yes, Place Order</button>
      </form>

</div>
      </div>
    </div>
  </div>
</div>


<script>

document.getElementById('confirmPlaceOrderBtn').addEventListener('click', function() {
    document.querySelector('form').submit(); // Submit the form after confirmation
});

  </script>



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
  <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#confirmOrderModal">
    Place Order
  </button>
</div>

        </form>
    <?php endif; ?>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>