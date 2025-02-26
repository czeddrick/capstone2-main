
<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
</head>
<body>
<?php
// Include navbar and database connection
include 'navbar.php';
include '../db/connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../main/user_login.php');
    exit;
}

// Fetch orders for the logged-in user
$user_id = $_SESSION['user_id'];
$status = 'completed'; // Ensure the status is correctly set

$sql = "SELECT product_name, image, payment_method, voucher_used, total_products, total_price, placed_on, status 
        FROM orders 
        WHERE user_id = ? AND status = ? 
        ORDER BY placed_on DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $status); // Bind user_id as integer and status as string
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container my-4">
    <h1 class="mb-4">My Orders</h1>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Placed On:</strong> 
                        <?php 
                        // Format date and time
                        echo date("M d, Y h:i A", strtotime($order['placed_on'])); 
                        ?>
                    </span>
                    <div class="col-sm-3 text-center">
                        <button type="button" name="add_review" class="btn btn-primary form-control mt-3 add_review_btn">Rate/Review This Product</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                    <div class="col-md-2 text-center">
    <?php 
    $image_path = htmlspecialchars($order['image']);

    // Ensure the image path is not empty
    if (!empty($image_path)) {
        // If the path is relative, prepend the base URL
        if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
            $image_path = "http://localhost/capstone2-main/" . $image_path;
        }

        // Display the image
        echo '<img src="' . $image_path . '" alt="Product" class="img-fluid rounded">';
    } else {
        // Display a placeholder image if no image is found
        echo '<img src="images/placeholder.jpg" alt="Placeholder Image" class="img-fluid rounded">';
    }
    ?>
</div>
                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            
                            <p class="text-muted mb-1"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p class="text-muted mb-0"><strong>Voucher Used:</strong> <?php echo htmlspecialchars($order['voucher_used'] ? $order['voucher_used'] : 'None'); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h5 class="text-danger">₱<?php echo number_format($order['total_price'], 2); ?></h5>
                            <p class="text-muted mb-0"><strong>Total Products:</strong> <?php echo htmlspecialchars($order['total_products']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h4>No Orders Found</h4>
            <p>Looks like you haven't placed any orders yet. <a href="index.php" class="btn btn-dark btn-sm">Shop Now</a></p>
        </div>
    <?php endif; ?>
</div>

<!-- Review Modal -->
<div id="review_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
      	<div class="modal-dialog" role="document">
        	<div class="modal-content">
    	      	<div class="modal-header">
    	        	<h5 class="modal-title" id="reviewModalLabel">Submit Review</h5>
    	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    	          		<span aria-hidden="true">&times;</span>
    	        	</button>
    	      	</div>
    	      	<div class="modal-body">
    	      		<h4 class="text-center mt-2 mb-4">
    	        		<i class="fas fa-star star-light submit_star mr-1" data-rating="1"></i>
                        <i class="fas fa-star star-light submit_star mr-1" data-rating="2"></i>
                        <i class="fas fa-star star-light submit_star mr-1" data-rating="3"></i>
                        <i class="fas fa-star star-light submit_star mr-1" data-rating="4"></i>
                        <i class="fas fa-star star-light submit_star mr-1" data-rating="5"></i>
    	        	</h4>
    	        	<div class="form-group">
                        <label for="user_name">Your Name:</label>
    	        		<input type="text" name="user_name" id="user_name" class="form-control" placeholder="Enter Your Name" />
    	        	</div>
    	        	<div class="form-group">
                        <label for="user_review">Comment:</label>
    	        		<textarea name="user_review" id="user_review" class="form-control" placeholder="Type Review Here"></textarea>
    	        	</div>
    	        	<div class="form-group text-center mt-4">
    	        		<button type="button" class="btn btn-primary" id="save_review">Submit</button>
    	        	</div>
    	      	</div>
        	</div>
      	</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function(){
    var rating_data = 0;
    var product_id = $('.card').data('product-id'); // Retrieve the product ID

    // Open the review modal
    $('.add_review_btn').click(function(){
        $('#review_modal').modal('show');
    });

    // Highlight stars on mouse enter
    $(document).on('mouseenter', '.submit_star', function(){
        var rating = $(this).data('rating');
        reset_background();
        for(var count = 1; count <= rating; count++) {
            $('.submit_star[data-rating="' + count + '"]').addClass('text-warning');
        }
    });

    // Reset stars on mouse leave
    function reset_background() {
        $('.submit_star').each(function(){
            $(this).removeClass('text-warning');
        });
    }

    // Handle mouse leave for stars
    $(document).on('mouseleave', '.submit_star', function(){
        reset_background();
        for(var count = 1; count <= rating_data; count++) {
            $('.submit_star[data-rating="' + count + '"]').addClass('text-warning');
        }
    });

    // Set rating data on click
    $(document).on('click', '.submit_star', function(){
        rating_data = $(this).data('rating');
    });

    // Save review
    $('#save_review').click(function(){
        var user_name = $('#user_name').val().trim();
        var user_review = $('#user_review').val().trim();

        if(user_name === '' || user_review === '' || rating_data === 0) {
            alert("Please fill all fields and select a rating.");
            return false;
        } else {
            $.ajax({
                url: "submit_rating.php",
                method: "POST",
                data: {
                    action: 'submit_review',
                    product_id: product_id, // Include product ID
                    rating_data: rating_data, 
                    user_name: user_name, 
                    user_review: user_review
                },
                success: function(response) {
                    $('#review_modal').modal('hide');
                    load_rating_data();
                    alert(response);
                },
                error: function() {
                    alert("An error occurred while submitting your review. Please try again.");
                }
            });
        }
    });

    // 
                
            
    });

    </script>



<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        

        body {
            background-image: linear-gradient(to top, #e6e9f0 0%, #eef1f5 100%);
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding-top: 80px;
        }

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
