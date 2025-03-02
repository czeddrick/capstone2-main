<?php
include "../db/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]); // Ensure it's an integer
    $status = "Received";

    // Prepare SQL statement
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 60px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            font-size: 1rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .text-muted {
            font-size: 0.9rem;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn-dark {
            background-color: #343a40;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            border-bottom: 1px solid #e9ecef;
        }
        .modal-footer {
            border-top: 1px solid #e9ecef;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            border-radius: 10px;
        }

        .add_review_btn {
            background-color:rgba(1, 27, 2, 0); /* Green background */
            color: Black; /* White text */
            border-color: black;
            padding: 5px 13px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .add_review_btn:hover {
            background-color:rgb(61, 187, 67); /* Darker green on hover */
            color: white;
        }
    </style>
</head>
<body>
<?php
// Include navbar and database connection
include 'navbar.php';
include '../db/connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
   
}
$sql = "SELECT first_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// Fetch orders for the logged-in user

$sql = "SELECT id, product_name, product_id, image, color, payment_method, voucher_used, total_products, total_price, placed_on, status 
        FROM orders 
        WHERE user_id = ? AND status = 'received' 
        ORDER BY placed_on DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Define the function outside the loop
function isOrderReviewed($conn, $order_id) {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        return true; // Order has been reviewed
    } else {
        return false; // Order has not been reviewed
    }

    // Close the statement
    $stmt->close();
}
?>
<div class="container my-5">
    <h1 class="mb-4 text-center">Orders</h1>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="card mb-4" data-product-id="<?= $order['product_id']; ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Placed On:</strong> 
                        <?php echo date("M d, Y h:i A", strtotime($order['placed_on'])); ?>
                    </span>
                    <?php
                    $order_id = $order['id'];
                    if (isOrderReviewed($conn, $order_id)) {
                        echo '<span class="rated-text" style="color: green; font-weight: bold;">Rated</span>';
                    } else {
                        echo '<button class="add_review_btn" data-product-id="' . $order['product_id'] . '" data-order-id="' . $order_id . '">Rate Order</button>';
                    }
                    ?>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <?php 
                            $image_path = htmlspecialchars($order['image']);
                            if (!empty($image_path)) {
                                if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
                                    $image_path = "http://localhost/capstone2-main/" . $image_path;
                                }
                                echo '<img src="' . $image_path . '" alt="Product" class="img-fluid rounded" style="max-height: 100px;">';
                            } else {
                                echo '<img src="images/placeholder.jpg" alt="Placeholder Image" class="img-fluid rounded" style="max-height: 100px;">';
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="text-muted mb-1"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p class="text-muted mb-0"><strong>Voucher Used:</strong> <?php echo htmlspecialchars($order['voucher_used'] ?: 'None'); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h5 class="text-danger">₱<?php echo number_format((float)$order['total_price'], 2); ?></h5>
                            <p class="text-muted mb-0"><strong>Total Products:</strong> <?php echo htmlspecialchars($order['total_products']); ?></p>
                            
                            <button type="button" class="btn btn-warning mt-2 view-details" data-order='<?php echo json_encode($order); ?>'>
                                View Details
                            </button>
                            <button type="button" class="btn btn-dark mt-2"onclick="window.location.href='<?php echo BASE_URL; ?>home/contact.php'">
                                Contact Us
                            </button>
                            <!-- 
                            <button type="button" class="btn btn-danger mt-2 cancel-order" 
                                data-order='<?php echo json_encode($order); ?>'>
                                Cancel Order
                            </button> 
                            -->
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h4>No Orders Found</h4>
            <p>Looks like you haven't placed any orders yet. <a href="../index.php" class="btn btn-dark btn-sm">Shop Now</a></p>
        </div>
    <?php endif; ?>
</div>


<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="cancel_order.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body" style="max-height: 500px; overflow: hidden; padding-left: 50px; margin-bottom: 30px;">
                    <div class="row">
                        <!-- Left Side: Product Details -->
                        <div class="col-md-6">
                            <div id="cancelProductDetails" class="border rounded p-3">
                                <img id="cancelProductImage" src="" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                <h5 id="cancelProductName"></h5>
                                <p><strong>Payment:</strong> <span id="cancelPaymentMethod"></span></p>
                                <p><strong>Voucher Used:</strong> <span id="cancelVoucherUsed"></span></p>
                                <p><strong>Total Products:</strong> <span id="cancelTotalProducts"></span></p>
                                <p><strong>Total Price:</strong> <span id="cancelTotalPrice"></span></p>
                                <p><strong>Placed On:</strong> <span id="cancelPlacedOn"></span></p>
                                <p><strong>Status:</strong> <span id="cancelStatus"></span></p>
                            </div>
                        </div>
                        
                        <!-- Right Side: Scrollable Cancellation Form -->
                         
                        <div class="col-md-6 d-flex flex-column">
                            <!-- Scrollable Form Content -->
                            <div style="overflow-y: auto; max-height: 380px; padding-right: 20px; flex-grow: 1;">
                                <input type="hidden" name="order_id" id="cancelOrderId">
                                <div class="mb-3">
                                    <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                                    <textarea name="cancel_reason" id="cancelReason" class="form-control" rows="3" required placeholder="Provide your reason here..."></textarea>
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
                            </div>

                            <!-- Fixed Checkbox and Submit Button -->
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
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="detailImage" src="" alt="Product Image" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                    <div class="col-md-8">
                        <p>Order ID: <span id="detailorderid"><?php echo htmlspecialchars($order['id']); ?></span></p>
                        <h5 id="detailProductName" class="card-title" style="margin-bottom: 5px;"></h5>
                        <p class="text-muted mb-1"><strong>Color:</strong> <span id="detailColor"></span></p>
                        <p class="text-muted mb-1"><strong>Payment Method:</strong> <span id="detailPaymentMethod"></span></p>
                        <p class="text-muted mb-1"><strong>Voucher Used:</strong> <span id="detailVoucherUsed"></span></p>
                        <p class="text-muted mb-1"><strong>product id:</strong> <span id="detailproductid"></span></p>
                        <p class="text-muted mb-1"><strong>Total Products:</strong> <span id="detailTotalProducts"></span></p>
                        <p class="text-muted mb-1"><strong>Total Price:</strong> <span id="detailTotalPrice"></span></p>
                        <p class="text-muted mb-1"><strong>Placed On:</strong> <span id="detailPlacedOn"></span></p>
                        <p class="text-muted mb-1"><strong>Status:</strong> <span id="detailStatus"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            
        </div>
    </div>
</div>

<!-- Contact Us Modal -->
<div class="modal fade" id="contactUsModal" tabindex="-1" aria-labelledby="contactUsModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="contact_us.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactUsModalLabel">Contact Us</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="contactMessage" class="form-label">Your Message</label>
                        <textarea name="message" id="contactMessage" class="form-control" rows="5" required placeholder="Enter your message here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Send Message</button>
                </div>
            </form>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cancel Order Modal
    const cancelButtons = document.querySelectorAll('.cancel-order');
    const cancelOrderModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    const cantCancelModal = new bootstrap.Modal(document.getElementById('cantCancelModal'));
    const cancelOrderIdInput = document.getElementById('cancelOrderId');

    // Elements for product details in the cancel modal
    const cancelProductImage = document.getElementById('cancelProductImage');
    const cancelProductName = document.getElementById('cancelProductName');
    const cancelPaymentMethod = document.getElementById('cancelPaymentMethod');
    const cancelVoucherUsed = document.getElementById('cancelVoucherUsed');
    const cancelTotalProducts = document.getElementById('cancelTotalProducts');
    const cancelTotalPrice = document.getElementById('cancelTotalPrice');
    const cancelPlacedOn = document.getElementById('cancelPlacedOn');
    const cancelStatus = document.getElementById('cancelStatus');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const order = JSON.parse(this.getAttribute('data-order'));
            const orderPlacedOn = new Date(order.placed_on);
            const now = new Date();
            const hoursPassed = (now - orderPlacedOn) / (1000 * 60 * 60);

            if (hoursPassed > 24) {
                cantCancelModal.show();
            } else {
                // Populate Order Details in Cancel Modal
                cancelOrderIdInput.value = order.id;
                cancelProductImage.src = order.image.startsWith('http') ? order.image : `http://localhost/capstone2-main/${order.image}`;
                cancelProductName.textContent = order.product_name;
                cancelPaymentMethod.textContent = order.payment_method;
                cancelVoucherUsed.textContent = order.voucher_used || 'None';
                cancelTotalProducts.textContent = order.total_products;
                cancelTotalPrice.textContent = `₱${parseFloat(order.total_price).toFixed(2)}`;
                cancelPlacedOn.textContent = new Date(order.placed_on).toLocaleString();
                cancelStatus.textContent = order.status;

                cancelOrderModal.show();
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
// View Details Modal
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    const viewDetailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));

        viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            const order = JSON.parse(this.getAttribute('data-order'));
            document.getElementById('detailImage').src = order.image.startsWith('http') ? order.image : `http://localhost/capstone2-main/${order.image}`;
            document.getElementById('detailProductName').textContent = order.product_name;
            document.getElementById('detailColor').textContent = order.color;
            document.getElementById('detailorderid').textContent = order.id || 'N/A';
            document.getElementById('detailproductid').textContent = order.product_id;
            document.getElementById('detailPaymentMethod').textContent = order.payment_method;
            document.getElementById('detailVoucherUsed').textContent = order.voucher_used || 'None';
            document.getElementById('detailTotalProducts').textContent = order.total_products;
            document.getElementById('detailTotalPrice').textContent = `₱${parseFloat(order.total_price).toFixed(2)}`;
            document.getElementById('detailPlacedOn').textContent = new Date(order.placed_on).toLocaleString();
            document.getElementById('detailStatus').textContent = order.status;
            viewDetailsModal.show();
        });
    });

    // Contact Us Modal
    const contactUsButtons = document.querySelectorAll('.contact-us');
    const contactUsModal = new bootstrap.Modal(document.getElementById('contactUsModal'));

    contactUsButtons.forEach(button => {
        button.addEventListener('click', function () {
            contactUsModal.show();
        });
    });
});
</script>

<!-- Review Modal -->
<div id="review_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Review</h5>
            </div>
            <div class="modal-body">
           
                <h4 class="text-center mt-2 mb-4">
                    <i class="fas fa-star star-light submit_star mr-1" id="submit_star_1" data-rating="1"></i>
                    <i class="fas fa-star star-light submit_star mr-1" id="submit_star_2" data-rating="2"></i>
                    <i class="fas fa-star star-light submit_star mr-1" id="submit_star_3" data-rating="3"></i>
                    <i class="fas fa-star star-light submit_star mr-1" id="submit_star_4" data-rating="4"></i>
                    <i class="fas fa-star star-light submit_star mr-1" id="submit_star_5" data-rating="5"></i>
                </h4>
                <div class="form-group">
                    <span class="nav-link fw-bold text-dark" id="display_name">
                        Name: <?= htmlspecialchars($_SESSION['full_name']); ?>
                    </span>
                    <div class="form-check mt-2" style="padding-top: 3px;">
                    <input type="checkbox" class="form-check-input form-check-sm" id="anonymous_checkbox" style="transform: scale(0.8);">
                        <label class="form-check-label" style="font-size: 13px;" for="anonymous_checkbox">Post as Anonymous</label>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label for="">Comment:</label>
                    <textarea name="user_review" id="user_review" class="form-control" placeholder="Type Review Here"></textarea>
                </div>
                <div class="form-group text-center mt-4">
                    <button type="button" class="btn btn-primary" id="save_review">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    var rating_data = 0;
    var product_id = $('.card').data('product-id'); 
    var order_id; // Variable to store the order ID

    // Show the modal when the "Rate Order" button is clicked
    $('.add_review_btn').click(function () {
        // Get the product ID and order ID
        product_id = $(this).data('product-id');
        order_id = $(this).data('order-id'); // Retrieve the order ID

        // Show the modal
        $('#review_modal').modal('show');
    });

    // Rest of your existing code...
    $(document).on('mouseenter', '.submit_star', function () {
        var rating = $(this).data('rating');
        reset_background();
        for (var count = 1; count <= rating; count++) {
            $('#submit_star_' + count).addClass('text-warning');
        }
    });

    function reset_background() {
        for (var count = 1; count <= 5; count++) {
            $('#submit_star_' + count).addClass('star-light');
            $('#submit_star_' + count).removeClass('text-warning');
        }
    }

    $(document).on('mouseleave', '.submit_star', function () {
        reset_background();
        for (var count = 1; count <= rating_data; count++) {
            $('#submit_star_' + count).removeClass('star-light');
            $('#submit_star_' + count).addClass('text-warning');
        }
    });

    $(document).on('click', '.submit_star', function () {
        rating_data = $(this).data('rating');
    });

    $('#anonymous_checkbox').change(function () {
        var full_name = "<?= htmlspecialchars($_SESSION['full_name']); ?>";
        if ($(this).is(':checked')) {
            var anonymized_name = full_name.charAt(0) + '****' + full_name.charAt(full_name.length - 1);
            $('#display_name').text('Name: ' + anonymized_name);
        } else {
            $('#display_name').text('Name: ' + full_name);
        }
    });

    $('#save_review').click(function(){
        var user_name = "<?= htmlspecialchars($_SESSION['full_name']); ?>";
        if ($('#anonymous_checkbox').is(':checked')) {
            user_name = user_name.charAt(0) + '****' + user_name.charAt(user_name.length - 1);
        }
        var user_review = $('#user_review').val().trim();

        if(user_name === '' || user_review === '' || rating_data === 0) {
            alert("Please fill all fields and select a rating.");
            return false;
        }

        $.ajax({
            url: "../user_account/submit_rating.php",
            method: "POST",
            data: {
                action: 'submit_review',
                product_id: product_id,
                order_id: order_id, // Include the order ID in the submission
                rating_data: rating_data,
                user_name: user_name,
                user_review: user_review
            },
            success: function(response) {
                $('#review_modal').modal('hide');
                $('#user_name').val('');
                $('#user_review').val('');
                rating_data = 0;
                reset_background();
                location.reload();
                alert(response);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("An error occurred while submitting your review. Please try again.");
            }
        });
    });
});
</script>

<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        
        .progress-label-left {
            float: left;
            margin-right: 0.5em;
            line-height: 1em;
        }
        .progress-label-right {
            float: right;
            margin-left: 0.3em;
            line-height: 1em;
        }
        .star-light {
            color:#e9ecef;
        }

        .row {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
    </style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>





</body>
</html>