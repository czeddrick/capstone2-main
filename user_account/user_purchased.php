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
            padding-top: 70px;
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
    </style>
</head>
<body>
<?php
// Include navbar and database connection
include '../navbar.php';
include '../db/connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$sql = "SELECT first_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// Fetch orders for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, product_name, product_id, image, payment_method, voucher_used, total_products, total_price, placed_on, status 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY placed_on DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container my-5">
    <h1 class="mb-4 text-center">My Orders</h1>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="card mb-4" data-product-id="<?= $fetch_order['product_id']; ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Placed On:</strong> 
                        <?php echo date("M d, Y h:i A", strtotime($order['placed_on'])); ?>
                    </span>
                    <button class="add_review_btn" data-product-id="<?php echo $order['product_id']; ?>">order recieved</button>
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
                            <button type="button" class="btn btn-dark mt-2 contact-us">
                                Contact Us
                            </button>
                            <button type="button" class="btn btn-danger mt-2 cancel-order" data-order-id="<?php echo $order['id']; ?>">
                                Cancel Order
                            </button>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="cancel_order.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                        <textarea name="cancel_reason" id="cancelReason" class="form-control" rows="3" required placeholder="Provide your reason here..."></textarea>
                    </div>
                    <p class="text-muted">
                        <small>
                            By canceling, you agree to our cancellation policy. Refunds may take 3-5 business days to process, and some restrictions may apply.
                        </small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                        <h5 id="detailProductName" class="card-title"></h5>
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
    <div class="modal-dialog">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cancel Order Modal
    const cancelButtons = document.querySelectorAll('.cancel-order');
    const cancelOrderModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    const cancelOrderIdInput = document.getElementById('cancelOrderId');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');
            cancelOrderIdInput.value = orderId;
            cancelOrderModal.show();
        });
    });
// View Details Modal
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    const viewDetailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));

        viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            const order = JSON.parse(this.getAttribute('data-order'));
            document.getElementById('detailImage').src = order.image.startsWith('http') ? order.image : `http://localhost/capstone2-main/${order.image}`;
            document.getElementById('detailProductName').textContent = order.product_name;
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
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title">Submit Review</h5>
	        	<div class="col-md-4 text-center">
                    <img id="detailImage" src="" alt="Product Image" class="img-fluid rounded" style="max-height: 200px;">
                </div>
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
                <span class="nav-link fw-bold text-dark" name="user_name" id="user_name">
                    Name:  <?= htmlspecialchars($_SESSION['full_name']); ?>
                </span>
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
      <?php else: ?>
        <?php endif; ?>
</div>


<script>

$(document).ready(function() {
    var rating_data = 0;
    var product_id = $('.card').data('product-id'); // Retrieve the product ID
      
    
    
    $('.add_review_btn').click(function () {
    product_id = $(this).data('product-id'); // Correctly get product ID
    
    $('#review_modal').modal('show');
});

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

    $('#save_review').click(function(){
    var user_name = "<?= htmlspecialchars($_SESSION['full_name']); ?>"; // Use PHP to get session value
    var user_review = $('#user_review').val().trim();

    if(user_name === '' || user_review === '' || rating_data === 0) {
        alert("Please fill all fields and select a rating.");
        return false;
    }
   

    $.ajax({
      
        url: "submit_rating.php",
        method: "POST",
        data: {
            action: 'submit_review',
            product_id: product_id, // Ensure product ID is included
            rating_data: rating_data, 
            user_name: user_name, 
            user_review: user_review
        },
        success: function(response) {
            $('#review_modal').modal('hide');
            $('#user_name').val('');
            $('#user_review').val('');

            // Reset the rating stars
            rating_data = 0; 
            reset_background();

            // Show success message
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