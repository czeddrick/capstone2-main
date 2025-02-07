

<?php
include 'db/connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid product ID.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Section</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>

    body{
        padding-top: 80px;
    }
    .product-section {
      background-color: whitesmoke;
      padding: 30px;
      border-radius: 10px;
    }
    .product-image img {
      width: 100%;
      border-radius: 10px;
    }
    .star-rating i {
      color: #ffc107;
    }
    .btn-buy-now, .btn-add-to-cart {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .btn-buy-now {
      background-color: #007bff;
      color: #fff;
    }
    .btn-buy-now:hover {
      background-color: #0056b3;
    }
    .btn-add-to-cart {
      background-color: #28a745;
      color: #fff;
    }
    .btn-add-to-cart:hover {
      background-color: #218838;
    }
    .btn-icon {
      margin-right: 8px;
    }
    .reviews-section {
      margin-top: 40px;
      padding: 20px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .review {
      margin-bottom: 20px;
    }
    .review p {
      margin: 0;
    }
    .review .reviewer-name {
      font-weight: bold;
    }
    .review .review-date {
      color: #888;
      font-size: 0.9rem;
    }

    .small-image {
  width: 60px;
  height: 100px;
  cursor: pointer;
  border: 1px solid #ddd;
  object-fit: cover;
  transition: transform 0.2s ease;
}

.small-image:hover {
  transform: scale(1.1);
  border-color: transparent;
}
.progress-label-left {
            float: left;
            
            line-height: 1em;
        }
        .progress-label-right {
            float: right;
          
            line-height: 1em;
        }
        .star-light {
            color:#e9ecef;
        }

        .row {
            display: flex;
        
            padding-bottom: 90px;
        }
        .text-warning mb-0 {
            font-size: 0.5rem;
        }

        .card-body .d-flex {
            margin-bottom: 10px; /* Adjust the value as needed */
        }

        
  </style>
  <title><?php echo htmlspecialchars($product['product_name']); ?> - Quick View</title>
</head>
<body>
<?php 
     include 'navbar.php'; 
     ?>
  <div class="container my-5">
    <div class="row product-section">
        <!-- Product Image -->
        <div class="col-md-5 product-image">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="img-fluid main-image" id="mainImage">
            <div class="mt-3 d-flex gap-2">
                <!-- Placeholder for additional images -->
                <img src="images/2nd.png" alt="Side Angle" class="small-image">
                <img src="images/1st.png" alt="Back Angle" class="small-image">
                <img src="images/3rd.png" alt="Top Angle" class="small-image">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-7" data-product-id="<?= $product['id']; ?>">

            <h3 class="fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></h3>
            <p class="text-muted"><?php echo htmlspecialchars($product['product_description']); ?></p>
            <p>
                <span class="fs-4 fw-bold text-dark">₱<?php echo number_format($product['discounted_price'], 2); ?></span>
                <del class="text-muted">₱<?php echo number_format($product['original_price'], 2); ?></del>
                <span class="badge bg-success"><?php echo $product['discount_percentage']; ?>% OFF</span>
            </p>
            <div class="card-body">
    <div class="d-flex align-items-center gap-4">
        <!-- Average Rating and Stars -->
        <div class="d-flex align-items-center gap-4">
            <h1 class="text-warning mb-0"style="font-size: 1.3rem;">
                <b><span class="average_rating">0.0</span> / 5</b>
            </h1>
            <div class="ml-3">
                <i class="fas fa-star star-light main_star" style="font-size: 0.8rem;"></i>
                <i class="fas fa-star star-light main_star" style="font-size: 0.8rem;"></i>
                <i class="fas fa-star star-light main_star" style="font-size: 0.8rem;"></i>
                <i class="fas fa-star star-light main_star" style="font-size: 0.8rem;"></i>
                <i class="fas fa-star star-light main_star" style="font-size: 0.8rem;"></i>
            </div>
        </div>

        <h3 class="ml-4" style="font-size: 1rem;">
            <span class="total_review">0</span> Reviews
        </h3>
    
</div>

        
    </div>
            <!-- Delivery Time -->
            <p class="text-muted"><i class="bi bi-truck"></i> Delivery in 3-5 business days</p>
            <!-- Select Options -->
            <form>
                <div class="mb-3">
                    <label for="color" class="form-label">Color:</label>
                    <select class="form-select" id="color" name="color">
                        <option selected>Select a color</option>
                        <option value="black">Black</option>
                        <option value="white">White</option>
                        <option value="red">Red</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
                </div>
                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning flex-grow-1">
                        <i class="bi bi-cart-plus btn-icon" a href="/user_account/submit_rating.php"></i>Add to Cart
                    </button>
                    <button type="button" class="btn btn-dark flex-grow-1">
                        <i class="bi bi-bag-check btn-icon" a href="checkout.php"></i>Checkout
                    </button>
                </div>
            </form>
        </div>
        
    </div>
    <h3 class="mt-3 ml-4">Product Reviews:</h3>
<div class="mt-3" style="font-size: 0.8rem;" id="review_content">
    <!-- Reviews will be loaded here -->
</div>
</div>







<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function(){
    
    var product_id = $('[data-product-id]').data('product-id');

    

    // Load rating data on page load
    load_rating_data();

    function load_rating_data() {
        $.ajax({
            url: "user_account/submit_rating.php",
            method: "POST",
            data: {
                action: 'load_data',
                product_id: product_id // Include product ID
            },
            dataType: "JSON",
            success: function(data) {
                $('.average_rating').text(data.average_rating);
                $('.total_review').text(data.total_review);

                // Update main stars
                $('.main_star').each(function(index){
                    if(Math.ceil(data.average_rating) > index){
                        $(this).addClass('text-warning').removeClass('star-light');
                    } else {
                        $(this).removeClass('text-warning').addClass('star-light');
                    }
                });

                // Update star counts
                $('.total_five_star_review').text(data.five_star_review);
                $('.total_four_star_review').text(data.four_star_review);
                $('.total_three_star_review').text(data.three_star_review);
                $('.total_two_star_review').text(data.two_star_review);
                $('.total_one_star_review').text(data.one_star_review);

                // Update progress bars
                if(data.total_review > 0){
                    $('#five_star_progress').css('width', (data.five_star_review / data.total_review) * 100 + '%');
                    $('#four_star_progress').css('width', (data.four_star_review / data.total_review) * 100 + '%');
                    $('#three_star_progress').css('width', (data.three_star_review / data.total_review) * 100 + '%');
                    $('#two_star_progress').css('width', (data.two_star_review / data.total_review) * 100 + '%');
                    $('#one_star_progress').css('width', (data.one_star_review / data.total_review) * 100 + '%');
                } else {
                    // If no reviews, set all progress bars to 0%
                    $('.progress-bar').css('width', '0%');
                }

                // Display reviews
                if(data.review_data.length > 0) {
                    var html = '';
                    for(var count = 0; count < data.review_data.length; count++) {
                        html += '<div class="row mb-3" style="padding-bottom: 10px;">';
                        html += '<div class="col-sm-1"><div class="rounded-circle bg-danger text-white pt-2 pb-2"><h3 class="text-center">' + data.review_data[count].user_name.charAt(0).toUpperCase() + '</h3></div></div>';
                        html += '<div class="col-sm-11">';
                        html += '<div class="card">';
                        html += '<div class="card-header"><b>' + data.review_data[count].user_name + '</b></div>';
                        html += '<div class="card-body">';
                        
                        for(var star = 1; star <= 5; star++) {
                            if(data.review_data[count].rating >= star){
                                html += '<i class="fas fa-star text-warning mr-1"></i>';
                            } else {
                                html += '<i class="fas fa-star star-light mr-1"></i>';
                            }
                        }

                        html += '<br />';
                        html += data.review_data[count].review ;
                        html += '</div>';
                        html += '<div class="card-footer text-right">On ' + data.review_data[count].datetime + '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    }
                    $('#review_content').html(html);
                } else {
                    $('#review_content').html('<p class="text-center">No reviews yet.</p>');
                }
            },
            error: function() {
                alert("An error occurred while loading reviews. Please try again.");
            }
        });
    
    }
});
</script>



  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
