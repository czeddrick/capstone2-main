<?php
session_start(); // Start the session at the beginning
include "../db/connect.php";
include "navbar.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch orders based on status and user_id
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fetch_orders'])) {
    $status = $_POST['status'] ?? '';

    if ($status) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE status = ? AND user_id = ? ORDER BY placed_on DESC");
        $stmt->bind_param("si", $status, $user_id); // Bind user_id to the query
        $stmt->execute();
        $result = $stmt->get_result();

        ob_start(); // Start output buffering
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_name = $row['product_name'] ?? 'N/A';
                $order_id = $row['id'] ?? 'N/A';
                $total_price = isset($row['total_price']) ? "₱" . htmlspecialchars($row['total_price']) : 'Price Not Available';
                $total_products = isset($row['total_products']) ? htmlspecialchars($row['total_products']) : 'Quantity Not Available';
                $placed_on = isset($row['placed_on']) ? date("F j, Y, g:i A", strtotime($row['placed_on'])) : 'Date Not Available';
                $address = isset($row['address']) ? htmlspecialchars($row['address']) : 'Quantity Not Available';
                $image = isset($row['image']) ? htmlspecialchars($row['image']) : 'default.jpg';
                ?>
                <!-- Product Card -->
                <div class="card mb-3 shadow-sm p-3" style="min-height: 150px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Product Image -->
                        <img src="<?php echo BASE_URL . $image; ?>" 
                             alt="<?= htmlspecialchars($product_name) ?>" 
                             class="img-fluid me-3 clickable-image"
                             style="max-width: 150px; height: auto; padding-right: 30px;"
                             data-bs-toggle="modal" 
                             data-bs-target="#productModal"
                             data-order-id="<?= htmlspecialchars($row['id']) ?>"
                             data-product-name="<?= htmlspecialchars($product_name) ?>"
                             data-placed-on="<?= $placed_on ?>"
                             data-address="<?= $address ?>"
                             data-total-products="<?= $total_products ?>"
                             data-total-price="<?= $total_price ?>"
                             data-image="<?= BASE_URL . $image ?>">

                        <!-- Product Details -->
                        <div class="flex-grow-1" style="margin-top: 10px;">
                            <h5 class="card-title" style="padding-bottom: 10px;"><?= htmlspecialchars($product_name) ?></h5>
                            <p class="card-text"><small class="text-muted">Placed on: <?= $placed_on ?></small></p>
                            <p class="card-text"><small class="text-muted">Address: <?= $address ?></small></p>
                        </div>

                        <!-- Price & Buttons -->
                        <div class="text-end">
                            <p class="card-text">Total products: <?= $total_products ?></p>
                            <h5 class="text-success"><?= $total_price ?></h5>
                            <?php if ($row['status'] === 'Pending') : ?>
                                <button type="button" class="btn btn-danger mt-2 cancel-order"
                                        data-order='<?php echo json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                        data-order-id="<?= htmlspecialchars($row['id']) ?>"
                                        data-product-name="<?= htmlspecialchars($product_name) ?>">
                                    Cancel order
                                </button>
                            <?php endif; ?>
                            <?php if ($row['status'] === 'Completed') : ?>
                                <button type="button" class="btn btn-success order-received-btn custom-btn" 
                                        data-id="<?= htmlspecialchars($row['id']) ?>" 
                                        data-product-name="<?= htmlspecialchars($product_name) ?>">
                                    Order Received
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-center">No orders found for this status.</p>';
        }
        $stmt->close();
        echo ob_get_clean(); // Send buffered output
        exit;
    }
}

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'];
    $cancelReason = $_POST['cancel_reason'];
    $canceledAt = date('Y-m-d H:i:s');

    // Update the order status
    $sql = "UPDATE orders SET status = 'Pending cancel', cancel_reason = ?, canceled_at = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $cancelReason, $canceledAt, $orderId, $user_id); // Include user_id in the query

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Order cancellation submitted successfully.'];
    } else {
        $response = ['success' => false, 'message' => $stmt->error];
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Fetch user-specific vouchers
$sql = "SELECT vouchers FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$vouchers = [];
if ($row = $result->fetch_assoc()) {
    $vouchers = json_decode($row['vouchers'], true); // Convert JSON to array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation Bar -->
<?php
// Start the session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch orders specific to the logged-in user
$sql = "SELECT id, product_name, image, status 
        FROM `orders` 
        WHERE (status = 'pending' OR status = 'completed' OR status = 'received' OR status = 'Pending cancel' OR status = 'Cancelled') 
        AND user_id = ? 
        ORDER BY id DESC";

// Prepare the statement
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind the user_id parameter
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Error preparing the SQL statement.");
}
?>


<script>
let cartCount = 0;

function addToCart(button) {
    // Get the product image source
    const card = button.closest('.card');
    const imgSrc = card.querySelector('img').src;

    // Create a flying image element
    const flyingImage = document.createElement('img');
    flyingImage.src = imgSrc;
    flyingImage.style.position = 'fixed'; // Ensure fixed position for smooth transition
    flyingImage.style.width = '300px'; // Starting size of the image
    flyingImage.style.borderRadius = '100%';
    flyingImage.style.transition = 'all 1s ease-in-out';
    flyingImage.style.zIndex = 1000;

    // Append the flying image to the body
    document.body.appendChild(flyingImage);

    // Get the position of the product image
    const rect = card.querySelector('img').getBoundingClientRect();
    flyingImage.style.top = `${rect.top + window.scrollY}px`;
    flyingImage.style.left = `${rect.left + window.scrollX}px`;

    // Get the cart icon position
    const cartIcon = document.querySelector('.nav-link[aria-label="Cart"] i');
    const cartRect = cartIcon.getBoundingClientRect();

    // Animate the flying image to the cart icon
    setTimeout(() => {
        flyingImage.style.top = `${cartRect.top + window.scrollY}px`; // Adjust for scrolling
        flyingImage.style.left = `${cartRect.left + window.scrollX}px`;
        flyingImage.style.width = '100px'; // Shrink the image
        flyingImage.style.opacity = '10';
    }, 100);

    // Remove the flying image and update cart count
    flyingImage.addEventListener('transitionend', () => {
        document.body.removeChild(flyingImage);

        // Update the cart count
        cartCount += 1;
        document.getElementById('cart-count').textContent = cartCount;

        // Now submit the form (after animation is complete)
        button.closest('form').submit();
    });
}




</script>

<style>
    #cart-count {
    font-size: 0.75rem;
    min-width: 20px;
    height: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    padding: 2px;
}
</style>



<!-- Cart Sidebar -->



<!-- FontAwesome and Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/your-fontawesome-key.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <style>
    body {
      font-family: 'Arial', sans-serif;
      padding-top: 90px;
      
    }

    /* Main Banner Section */
    .banner {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
    }

    .banner .btn-primary {
      background-color: #28a745;
      border: none;
      padding: 10px 20px;
    }

    .banner img {
      width: 100%;
      border-radius: 10px;
    }

    /* Cards Section */
    .info-card {
      background-color: #f0f8ff;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
    }

    .info-card h5 {
      font-size: 16px;
    }

    .info-card .btn {
      background-color: #004085;
      color: #fff;
    }

    .info-card.yellow {
      background-color: #fff4e1;
    }

    /* Categories Section */
    .category-item {
      text-align: center;
    }

    .category-item img {
      width: 60px;
      border-radius: 50%;
      margin-bottom: 10px;
    }

    .category-item p {
      font-size: 14px;
      color: gray;
    }

    .search-box-container {
    margin-top: -8px; /* Adjust to seamlessly align with the navbar */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for better separation */
    z-index: 1000;
}

.search-box-container .form-control {
    border-radius: 0;
    border-width: 2px;
}

.search-box-container .btn {
    border-width: 2px;
}
/* Fixed Sidebar Styling */
.fixed-sidebar {
    height: 100vh; /* Full height of the viewport */
    position: sticky; /* Sticks to the top */
    top: 0;
    left: 0;
    overflow: hidden; /* Prevent scrolling */
    background-color: white; /* Ensure visibility */
    z-index: 1000; /* Higher than navbar */
    border-right: 1px solid #ddd; /* Optional: add a subtle border */
}
/* Make the sidebar fixed */
.col-md-3 {
    position: fixed;
    top: 90;
    left: 50;
    height: 100vh; /* Full height of the viewport */
    width: 25%; /* Adjust width as needed */
    overflow-y: auto; /* Allow scrolling only if content exceeds height */
    z-index: 1000; /* Ensure sidebar stays above other content */
    background-color: white; /* Optional: Add a background color */
    padding: 20px; /* Optional: Add padding */
    
}

/* Ensure the main content does not overlap with the sidebar */
.col-md-9 {
    margin-left: 25%; /* Adjust margin to match sidebar width */
    padding: 20px; /* Optional: Add padding */
}
.status-tab {
        border: 2px solid transparent;
        cursor: pointer;
        transition: border-color 0.3s ease-in-out, color 0.3s ease-in-out;
        background: none !important;
        color: black; /* Default color */
        border-color: black;
    }

    .status-tab i {
        color: black; /* Default icon color */
        transition: color 0.3s ease-in-out;
    }

    

    .status-tab.active i {
        color: black !important; /* Ensure icon stays visible */
    }

  </style>
  

<script src="../js/cart.js"></script>


<div class="container mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
    <div class="list-group">
        <a href="../my_account.php" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-user"></i> My Account
        </a>
        <a href="#" class="list-group-item list-group-item-action" id="statusBtn">
            <i class="fas fa-box-open"></i> Status
        </a>
        <a href="<?php echo BASE_URL; ?>home/user_purchased.php" class="list-group-item list-group-item-action">
            <i class="fas fa-shopping-bag"></i> Orders
        </a>
        <a href="#" class="list-group-item list-group-item-action" id="cancelBtn">
            <i class="fas fa-ban"></i> Cancel
        </a>
        <a href="#" class="list-group-item list-group-item-action" id="notificationBtn">
            <i class="fas fa-bell"></i> Notifications
        </a>
        <a href="#" id="myVouchersBtn" class="list-group-item list-group-item-action">
            <i class="fas fa-tag"></i> My Vouchers
        </a>
        <a href="#" class="list-group-item list-group-item-action" id="settingsBtn">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Notification Panel -->
            <div id="notificationPanel" class="bg-white shadow-sm p-3 rounded d-none">
                <h6 class="fw-bold text-secondary">Recently Received Notifications</h6>
                <ul class="list-unstyled">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li>
                            <div class="d-flex align-items-start mb-3">
                                <img src="<?php echo BASE_URL . htmlspecialchars($row['image']); ?>" 
                                    alt="Order Image" 
                                    class="me-3 rounded-circle border border-dark" 
                                    style="width: 40px; height: 40px;">
                                <div>
                                    <p class="mb-0 fw-bold">
                                        <?php echo htmlspecialchars($row['product_name']); ?>
                                    </p>
                                    <small class="text-muted">
                                    <?php 
                                                    if ($row['status'] === 'Pending') {
                                                        echo "Order ID (" . htmlspecialchars($row['id']) . "), Order Placed, Thank you for purchasing!";
                                                    } elseif ($row['status'] === 'Completed' || $row['status'] === 'Received') {
                                                        echo "Order ID (" . htmlspecialchars($row['id']) . ") Your Order has been delivered.";
                                                    } elseif ($row['status'] === 'Pending Cancel') {
                                                        echo "Order ID (" . htmlspecialchars($row['id']) . ") Your cancellation request is being processed.";
                                                    } elseif ($row['status'] === 'Cancelled') {
                                                        echo "Order ID (" . htmlspecialchars($row['id']) . ") Your order has been cancelled.";
                                                    }
                                    ?>
                                    </small>
                                </div>
                            </div>
                            <hr class="my-2">
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div id="orderStatusSection" class="d-flex flex-column bg-white p-3 rounded">
                <div class="d-flex justify-content-between">
                    <div class="status-tab text-center flex-fill mx-2 py-2 rounded" data-status="pending">
                        <i class="fas fa-box fa-2x"></i>
                        <p class="mb-0">To Ship</p>
                    </div>
                    <div class="status-tab text-center flex-fill mx-2 py-2 rounded" data-status="cancelled">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                        <p class="mb-0">Processing</p>
                    </div>
                    <div class="status-tab text-center flex-fill mx-2 py-2 rounded" data-status="to_receive">
                        <i class="fas fa-truck fa-2x"></i>
                        <p class="mb-0">To Receive</p>
                    </div>
                    <div class="status-tab text-center flex-fill mx-2 py-2 rounded" data-status="completed">
                        <i class="fas fa-check-circle fa-2x"></i>
                        <p class="mb-0">Completed</p>
                    </div>
                    
                </div>
                <div id="order-content" class="mt-4">
                    <p class="text-center">Select a status to view orders.</p>
                </div>
            </div>
            <!-- Vouchers List Section -->
            <div id="vouchersSection" class="bg-white p-3 rounded mt-3 d-none">
                <h5 class="mb-3">Available Vouchers</h5>
                <div class="voucher-list d-flex flex-column">
                    <?php if (!empty($vouchers)): ?>
                        <?php foreach ($vouchers as $voucher): ?>
                            <div class="voucher-item p-2 rounded bg-light mb-2">
                                <h6 class="mb-1"><?= htmlspecialchars($voucher['name']) ?></h6>
                                <p class="mb-0 text-muted">Use code: <strong><?= htmlspecialchars($voucher['code']) ?></strong></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No vouchers available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Settings Panel -->
            <div id="settingsPanel" class="card d-none mt-4">
                <div class="card-header bg-primary text-white">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="change_password.php" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_verification" class="form-label">Email Verification Code</label>
                            <input type="text" class="form-control" id="email_verification" name="email_verification" required>
                            <button type="button" class="btn btn-secondary mt-2" id="sendCode">Send Verification Code</button>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
            <div id="cancelPanel" class="card d-none mt-4">
                <div class="card-header bg-danger text-white">
                    <h5>Canceled Orders</h5>
                </div>
                <div class="card-body">
                <ul class="list-group">
                <?php
                        

                        $query = "SELECT * FROM orders WHERE status IN ('Cancelled', 'Pending Cancel') AND user_id = ? ORDER BY placed_on DESC";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $user_id); // "i" indicates the user_id is an integer
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): ?>
                                <li class="list-group-item d-flex align-items-center">
                                    <!-- Product Image -->
                                    <img src="<?php echo BASE_URL . htmlspecialchars($row['image']); ?>" 
                                        alt="Product Image" 
                                        class="me-3 rounded border border-dark" 
                                        style="width: 60px; height: 60px; object-fit: cover;">

                                    <div class="flex-grow-1">
                                        <strong><?php echo htmlspecialchars($row['product_name']); ?></strong>
                                        <p class="mb-0 text-muted">Order ID: <?php echo htmlspecialchars($row['id']); ?></p>
                                        <small class="text-muted">Placed on: <?php echo htmlspecialchars($row['placed_on']); ?></small>
                                    </div>

                                    <!-- Display badge based on order status -->
                                    <?php if ($row['status'] === 'Cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php elseif ($row['status'] === 'Pending Cancel'): ?>
                                        <span class="badge bg-warning text-dark">Pending Cancel</span>
                                    <?php endif; ?>
                                </li>
                            <?php endwhile;
                        else: ?>
                            <li class="list-group-item text-center">No canceled or pending cancel orders.</li>
                        <?php endif; ?>
                </ul>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Product Image -->
                <div class="text-center mb-3">
                    <img id="modalProductImage" src="" alt="Product Image" class="img-fluid" style="max-width: 200px;">
                </div>
                <!-- Order Details -->
                <p><strong>Order ID:</strong> <span id="modalOrderId"></span></p>
                <p><strong>Product Name:</strong> <span id="modalProductName"></span></p>
                <p><strong>Placed on:</strong> <span id="modalPlacedOn"></span></p>
                <p><strong>Address:</strong> <span id="modalAddress"></span></p>
                <p><strong>Total Products:</strong> <span id="modalTotalProducts"></span></p>
                <p><strong>Total Price:</strong> <span id="modalTotalPrice"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 1000px; overflow-y: auto; padding: 30px;">
                <div class="row">
                    <!-- Full-width Cancellation Form -->
                    <div class="col-12">
                        <input type="hidden" name="order_id" id="cancelOrderId">
                        
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                            <textarea name="cancel_reason" id="cancelReason" class="form-control" rows="4" required placeholder="Provide your reason here..."></textarea>
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-link btn-sm p-0 m-0" type="button" id="togglePolicyBtn">
                                View Cancellation Policy
                            </button>
                            <div class="collapse" id="policyDetails">
                                <div class="card card-body">
                                    <strong>Cancellation Policy:</strong>
                                    <ul>
                                        <li>Orders can be canceled within 24 hours of placement.</li>
                                        <li>Refunds will be processed within 3-5 business days.</li>
                                        <li>Digital products, personalized items, and perishable goods are non-refundable.</li>
                                        <li>Orders already shipped cannot be canceled.</li>
                                        <li>Delays beyond estimated delivery time may qualify for cancellation with a full refund.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>  

                        <!-- Checkbox and Submit Button -->
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acceptPolicy">
                                <label class="form-check-label" for="acceptPolicy">
                                    I accept the cancellation policy
                                </label>
                            </div>
                            <p class="text-muted">
                                <small>By canceling, you agree to our cancellation policy.</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger" id="submitCancel" disabled>Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="../db/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="orderReceivedModal" tabindex="-1" aria-labelledby="orderReceivedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderReceivedModalLabel">Confirm Order Received</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="order-received-message">Are you sure you want to confirm this order as received?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmOrderReceivedBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cantCancelModal" tabindex="-1" aria-labelledby="cantCancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cantCancelModalLabel">Cancellation Not Allowed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Can't cancel because 24 hours have passed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (Make sure Bootstrap is included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- JavaScript to Handle Modal Data -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var productModal = document.getElementById('productModal');
    productModal.addEventListener('show.bs.modal', function (event) {
        var image = event.relatedTarget; // Image that triggered the modal
        var orderId = image.getAttribute('data-order-id');
        var productName = image.getAttribute('data-product-name');
        var placedOn = image.getAttribute('data-placed-on');
        var address = image.getAttribute('data-address');
        var totalProducts = image.getAttribute('data-total-products');
        var totalPrice = image.getAttribute('data-total-price');
        var productImage = image.getAttribute('data-image');

        // Update the modal's content
        document.getElementById('modalOrderId').textContent = orderId;
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalPlacedOn').textContent = placedOn;
        document.getElementById('modalAddress').textContent = address;
        document.getElementById('modalTotalProducts').textContent = totalProducts;
        document.getElementById('modalTotalPrice').textContent = totalPrice;
        document.getElementById('modalProductImage').src = productImage; // Set the image source
    });
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const cancelOrderModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    const cantCancelModal = new bootstrap.Modal(document.getElementById('cantCancelModal'));
    const cancelOrderIdInput = document.getElementById('cancelOrderId');

    document.body.addEventListener('click', function (event) {
        if (event.target.classList.contains('cancel-order')) {
            const order = JSON.parse(event.target.getAttribute('data-order'));
            const orderPlacedOn = new Date(order.placed_on);
            const now = new Date();
            const hoursPassed = (now - orderPlacedOn) / (1000 * 60 * 60);

            if (hoursPassed > 24) {
                cantCancelModal.show();
            } else {
                // Populate Order Details in Cancel Modal
                cancelOrderIdInput.value = order.id;
                cancelOrderModal.show();
            }
        }
    });
});
    const acceptPolicyCheckbox = document.getElementById('acceptPolicy');
    const submitCancelButton = document.getElementById('submitCancel');

    acceptPolicyCheckbox.addEventListener('change', function () {
        submitCancelButton.disabled = !this.checked;
    });

    var togglePolicyBtn = document.getElementById("togglePolicyBtn");
    var policyDetails = document.getElementById("policyDetails");

    togglePolicyBtn.addEventListener("click", function () {
        var bsCollapse = new bootstrap.Collapse(policyDetails, {
            toggle: false
        });

        if (policyDetails.classList.contains("show")) {
            bsCollapse.hide();
            togglePolicyBtn.textContent = "View Cancellation Policy";
        } else {
            bsCollapse.show();
            togglePolicyBtn.textContent = "Hide Cancellation Policy";
        }
    });

</script>

<script>
$(document).ready(function() {
    // Enable the submit button only if the policy is accepted
    $('#acceptPolicy').change(function() {
        if (this.checked) {
            $('#submitCancel').prop('disabled', false);
        } else {
            $('#submitCancel').prop('disabled', true);
        }
    });

    // Handle the form submission
    $('#submitCancel').click(function() {
        var orderId = $('#cancelOrderId').val();
        var cancelReason = $('#cancelReason').val();

        // Send an AJAX request to the same file
        $.ajax({
            url: window.location.href, // Send to the same file
            type: 'POST',
            data: {
                cancel_order: true, // Flag to indicate cancellation request
                order_id: orderId,
                cancel_reason: cancelReason
            },
            success: function(response) {
                // Handle the response from the server
                if (response.success) {
                    alert(response.message);
                    $('#cancelOrderModal').modal('hide');
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    
                    $('#cancelOrderModal').modal('hide');
                    location.reload(); // Reload the page to reflect the changes
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while submitting the cancellation.');
            }
        });
    });

    // Populate the modal with the order ID when the cancel button is clicked
    $('.cancel-order').click(function() {
        var orderId = $(this).data('order-id');
        $('#cancelOrderId').val(orderId);
    });
});
</script>





<script>
  $(document).ready(function () {
    let firstTab = $(".status-tab").first();
    let status = firstTab.data("status");

    // Set active class for the first tab
    $(".status-tab").removeClass("bg-dark text-white").addClass("text-dark");
    firstTab.addClass("bg-dark text-white");

    // Fetch the initial order list
    fetchOrders(status);

    $(".status-tab").click(function () {
        let status = $(this).data("status");

        // Update active class
        $(".status-tab").removeClass("bg-dark text-white").addClass("text-dark");
        $(this).addClass("bg-dark text-white");

        // Fetch orders dynamically
        fetchOrders(status);
    });

    function fetchOrders(status) {
        $.ajax({
            url: "", // Same file
            type: "POST",
            data: { status: status, fetch_orders: true },
            success: function (response) {
                console.log("Response received:", response); // Debugging
                $("#order-content").html(response); // Update order display
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", error); // Debugging
            }
        });
    }
});


</script>

<script>
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Get elements
    const cancelBtn = document.getElementById("cancelBtn");
    const settingsBtn = document.getElementById("settingsBtn");
    const statusBtn = document.getElementById("statusBtn");
    const notificationBtn = document.getElementById("notificationBtn");
    const myVouchersBtn = document.getElementById("myVouchersBtn");

    const cancelPanel = document.getElementById("cancelPanel");
    const settingsPanel = document.getElementById("settingsPanel");
    const orderStatusSection = document.getElementById("orderStatusSection");
    const notificationPanel = document.getElementById("notificationPanel");
    const vouchersSection = document.getElementById("vouchersSection");

    function hideAllPanels() {
        cancelPanel.classList.add("d-none");
        settingsPanel.classList.add("d-none");
        orderStatusSection.classList.add("d-none");
        notificationPanel.classList.add("d-none");
        vouchersSection.classList.add("d-none");
    }

    cancelBtn.addEventListener("click", function() {
        hideAllPanels();
        cancelPanel.classList.remove("d-none");
    });

    settingsBtn.addEventListener("click", function() {
        hideAllPanels();
        settingsPanel.classList.remove("d-none");
    });

    statusBtn.addEventListener("click", function() {
        hideAllPanels();
        orderStatusSection.classList.remove("d-none");
    });

    notificationBtn.addEventListener("click", function() {
        hideAllPanels();
        notificationPanel.classList.remove("d-none");
    });

    myVouchersBtn.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent page jump
        hideAllPanels();
        vouchersSection.classList.remove("d-none");
    });
});


document.addEventListener("DOMContentLoaded", function () {
    // Event listener for dynamically generated "Order Received" buttons
    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("order-received-btn")) {
            let id = event.target.getAttribute("data-id"); // Get the order ID
            let productName = event.target.getAttribute("data-product-name");

            // Store 'id' in the confirm button as a data attribute
            let confirmBtn = document.getElementById("confirmOrderReceivedBtn");
            confirmBtn.setAttribute("data-id", id); // Set the correct attribute

            // Update modal content
            document.getElementById("order-received-message").textContent = `Confirm that you received "${productName}"?`;

            // Show Bootstrap modal
            let modal = new bootstrap.Modal(document.getElementById("orderReceivedModal"));
            modal.show();
        }
    });

    // Handle the confirm button click
    document.getElementById("confirmOrderReceivedBtn").addEventListener("click", function () {
        let confirmBtn = this; // Explicitly reference the button
        let id = confirmBtn.getAttribute("data-id"); // Use 'data-id' here
        console.log("Sending ID:", id); // Debugging

        if (id) {
            fetch('../home/user_purchased.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response:", data); // Debugging
                if (data.success) {
                    // Update the button text to "Received"
                    confirmBtn.innerText = "Received";
                    // Optionally, disable the button to prevent further clicks
                    confirmBtn.disabled = true;

                    // Close the modal
                    let modal = bootstrap.Modal.getInstance(document.getElementById("orderReceivedModal"));
                    modal.hide();

                    // Reload the page to reflect the updated order status
                    location.reload();
                } else {
                    alert("Failed to update order status: " + (data.error || "Unknown error"));
                }
            })
            .catch(error => console.error("Error:", error));
        } else {
            alert("Order ID is missing.");
        }
    });
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
