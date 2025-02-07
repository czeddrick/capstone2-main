
<?php
 include "db/connect.php";
 include 'navbar.php';
?>

<?php
// Fetch all products initially
$sql = "SELECT * FROM products"; 
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    // Fetch all rows as an associative array
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "No products found.";
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container my-5"> 
  <h2 class="text-center mb-4" style="font-family: fantasy; color: #343a40;">Featured Products</h2>
  <p class="text-center" style="font-family: 'Courier New', Courier, monospace; font-size: 20px; color: gray;">
    Summer Collection - New Modern Design
  </p>
  <div class="row">
    <?php if (!empty($products)): // Check if $products is not empty ?>
      <?php foreach ($products as $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card shadow-sm h-100">
            <a href="quick_view.php?id=<?php echo $product['id']; ?>">
              <img 
                src="<?php echo $product['image_url']; ?>" 
                class="card-img-top img-fluid" 
                alt="<?php echo $product['product_name']; ?>"
                style="height: 250px; object-fit: cover;"
              >
            </a>
            <div class="card-body d-flex flex-column justify-content-between text-center">
              <h5 class="card-title text-truncate"> <?php echo $product['product_name']; ?> </h5>
              <p class="small text-muted text-truncate"> <?php echo $product['product_description']; ?> </p>
              <p class="mb-2">
                <del class="text-muted">₱<?php echo number_format($product['original_price'], 2); ?></del>
                <span class="text-dark fw-bold">₱<?php echo number_format($product['discounted_price'], 2); ?></span>
                <span class="badge bg-success ms-1"> <?php echo $product['discount_percentage']; ?>% OFF</span>
              </p>
              
              <!-- Stock and Sold Display -->
              <div class="d-flex justify-content-between mb-3">
                <p class="small text-muted mb-0">Stock: 
                  <?php echo $product['stock'] > 0 ? $product['stock'] : '<span class="text-danger">Out of Stock</span>'; ?>
                </p>
                <p class="small text-muted mb-0">Sold: 
                  <span class="fw-bold"><?php echo $product['sold']; ?></span>
                </p>
              </div>

              <form action="cart.php" method="POST">
                <?php if (isset($_SESSION['user_id'])): ?>
                  <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                  <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>">
                  <input type="hidden" name="price" value="<?php echo $product['discounted_price']; ?>">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="image_url" value="<?php echo $product['image_url']; ?>">
                  <input type="hidden" name="add_to_cart" value="1">
                  <button class="btn btn-outline-warning btn-sm" type="submit" 
                    <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                  </button>
                <?php else: ?>
                  <a href="main/user_login.php" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                  </a>
                <?php endif; ?>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No products available at the moment.</p>
    <?php endif; ?>
</div>

<style> 
body {
    padding-top: 60px; /* Adjust based on the height of your navbar */
}
</style>