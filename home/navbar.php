<?php
include 'confignav.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
}




// Ensure user is logged in
$user_id = $_SESSION['user_id'] ?? 0;
$cart_count = 0;

if ($user_id) {
    $query = "SELECT SUM(quantity) AS total_items FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $cart_count = $row['total_items'] ?? 0;
    }
}
$sql = "SELECT id, product_name, image, status FROM `orders` 
        WHERE (status = 'pending' OR status = 'completed' OR status = 'received') 
        AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID securely
$stmt->execute();
$result = $stmt->get_result();

?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 fixed-top">
    <div class="container">
        <!-- Logo and Title -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="<?php echo BASE_URL; ?>images/logo-removebg-preview.png" alt="Logo" class="me-2" style="height: 40px;">
            <span class="fw-bold" style="color: #333; font-size: 22px;">
                Great Wall <span style="color: #ffb100;">Arts</span>
            </span>
        </a>

        <!-- Navbar Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <li class="nav-item me-4">
                    <a class="nav-link fw-bold text-dark" href="<?php echo BASE_URL; ?>index.php">Home</a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link fw-bold text-dark" href="<?php echo BASE_URL; ?>home/products.php">Products</a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link fw-bold text-dark" href="<?php echo BASE_URL; ?>home/eco_friendly.php">Eco Friendly</a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link fw-bold text-dark" href="<?php echo BASE_URL; ?>home/contact.php">Contact</a>
                </li>
                

                <!-- Icons -->
                 <style> .dropdown-menu .d-flex img {
                          border: 1px solid #ddd;
                            }
                          .dropdown-menu .d-flex small {
                          font-size: 12px;
                            }
                 </style>
                <li class="nav-item dropdown me-3">
                    <a class="nav-link text-dark" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="badge bg-warning rounded-circle text-dark position-absolute top-0 start-100 translate-middle">4</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="notificationsDropdown" style="width: 470px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header fw-bold text-secondary">Recently Received Notifications</li>
                        <ul>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li>
                                    <div class="d-flex align-items-start mb-3">
                                        <img src="<?php echo BASE_URL . htmlspecialchars($row['image']); ?>" alt="Order Image" class="me-2 rounded-circle" style="width: 40px; height: 40px; margin-right: 50px;">
                                        <div>
                                            <p class="mb-0 fw-bold" style="margin-left: 20px;">
                                                <?php echo htmlspecialchars($row['product_name']); ?>
                                            </p>
                                            <small class="text-muted" style="margin-left: 20px;">
                                                <?php 
                                                    if ($row['status'] === 'Pending') {
                                                        echo "Order ID (" . htmlspecialchars($row['id']) . "), Order Placed, Thank you for purchasing!";
                                                    } elseif ($row['status'] === 'Completed' || $row['status'] === 'Received') {
                                                        echo "Order ID (" . htmlspecialchars($row['id']) . ") Your Order has been delivered.";
                                                    }
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                     <!-- Default Notification Message -->
                        <li class="text-center text-secondary p-3">
                            <p class="fw-bold">Welcome to Your Notifications! 🎉</p>
                            <p class="small">
                                Stay updated with the latest updates on your orders, exclusive deals, and important alerts. 📦✨
                            </p>
                            <p class="small text-muted">
                                💡 Tip: Check back here regularly to never miss an update on your purchases!
                            </p>
                        </li>
                    <?php endif; ?>

                        </ul>
                        <?php if ($cart_count > 10): ?>
            <li class="text-center">
                <a href="<?php echo BASE_URL; ?>home/account.php" class="text-primary fw-bold text-decoration-none">View All</a>
            </li>
        <?php endif; ?>
                    </ul>
                </li>
            <?php if (isset($_SESSION['user_id'])): ?>               
                <li class="nav-item me-3">
                    <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>home/user_purchased.php" aria-label="Orders">
                        <i class="fas fa-shopping-bag fs-5"></i>
                    </a>
                </li>
            <?php else: ?>
                <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>home/user_purchased.php" aria-label="Orders">
                        <i class="fas fa-shopping-bag fs-5"></i>
                    </a>
            <?php endif; ?>

                <li class="nav-item me-3 position-relative">

            <!-- Cart Icon in Navbar -->
            <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>home/cart.php" aria-label="Cart">
                <i class="fas fa-shopping-cart fs-5"></i>
                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                    <?php echo $cart_count; ?>
                </span>
            </a>
</li>

<script>

 
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
                <!-- User Section -->

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="nav-link fw-bold text-dark">
                            Welcome, <?= htmlspecialchars($_SESSION['full_name']); ?>!
                        </span>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-warning text-darkfw-bold" href="<?php echo BASE_URL; ?>home/account.php">
                            <i class="fas fa-user-circle me-2"></i>My Account
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-warning fw-bold" href="<?php echo BASE_URL; ?>main/user_login.php">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-dark fw-bold" href="<?php echo BASE_URL; ?>main/register.php">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 
</body>            
          </ul>
        </div>
    </div>
</nav>
<!-- FontAwesome and Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/your-fontawesome-key.js" crossorigin="anonymous"></script>

  <style>
    body {
      font-family: 'Arial', sans-serif;
    }

    .navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: white; /* Adjust as needed */
    z-index: 1050; /* Ensure it stays above other elements */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional shadow */
    padding-top: 80px;
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
   
    margin-top: 80px; /* Adjust to seamlessly align with the navbar */
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

            
            









            
          




