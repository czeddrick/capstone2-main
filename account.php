<?php
 include "db/connect.php";
 include "navbar.php";
 
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
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
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

  <style>
    body {
      font-family: 'Arial', sans-serif;
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

  </style>
  

<script src="../js/cart.js"></script>


<?php
include 'db/connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_box'])) {
  $search_box = trim($_POST['search_box']);

  try {
      // Prepare and execute the query
      $stmt = $conn->prepare("SELECT * FROM `products` WHERE `product_name` LIKE ?");
      $search_term = '%' . $search_box . '%';
      $stmt->bind_param('s', $search_term);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
          $products = $result->fetch_all(MYSQLI_ASSOC);
      } else {
          $products = [];
      }

      $stmt->close(); // Close the statement
  } catch (mysqli_sql_exception $e) {
      echo "Error: " . $e->getMessage();
      $products = [];
  }
}

// Avoid closing the connection prematurely; move `$conn->close()` to the end of the script if needed.
?>

            
            









            
          





    
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="../my_account.php" class="list-group-item list-group-item-action  bg-dark text-white">
                        <i class="fas fa-user"></i> My Account
                    </a>
                    <a href="<?php echo BASE_URL; ?>user_account/user_purchased.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>

                    <a href="<?php echo BASE_URL; ?>admin/cancelled_order.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog"></i> Cancel
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-tag"></i> My Vouchers
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart"></i> My Wishlist
                        
                    <a href="user_account/settings.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a href="db/logout.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
</div>

            </div>

            

            
                

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
