<?php
include "../db/connect.php";
session_start();
?>
<?php include 'navbar.php'; ?>
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

// Fetch total sold quantities for each product
$soldQuantities = [];
$sqlSold = "SELECT product_id, COUNT(product_id) AS total_sold FROM reviews GROUP BY product_id";
$resultSold = $conn->query($sqlSold);

if ($resultSold->num_rows > 0) {
    while ($row = $resultSold->fetch_assoc()) {
        $soldQuantities[$row['product_id']] = $row['total_sold'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>


  <body>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="http://localhost/capstone2-main/images/logo.png">



  <title>Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <h3 class="mt-5 text-center" style="font-family: fantasy;">Shop Popular Categories</h3>
<div class="d-flex justify-content-center flex-wrap mt-5">
  <!-- Category 1 -->
  <div class="category-item mx-4">
    <a href="category.php?name=Tumbler" class="text-decoration-none text-center">
      <img src="../images/tumbler (1).png" alt="Tumbler">
      <p>Tumbler</p>
    </a>
  </div>
  <!-- Category 2 -->
  <div class="category-item mx-4">
    <a href="category.php?name=Powerbank" class="text-decoration-none text-center">
      <img src="../images/powerbank (1).png" alt="Powerbank">
      <p>Powerbank</p>
    </a>
  </div>
  <!-- Category 3 -->
  <div class="category-item mx-4">
    <a href="category.php?name=Mini fan" class="text-decoration-none text-center">
      <img src="../images/air.png" alt="Mini Fan">
      <p>Mini Fan</p>
    </a>
  </div>
  <!-- Category 4 -->
  <div class="category-item mx-4">
    <a href="category.php?name=Gift Set" class="text-decoration-none text-center">
      <img src="../images/gift.png" alt="Gif Set">
      <p>Gift Set</p>
    </a>
  </div>
  <!-- Category 5 -->
  <div class="category-item mx-4">
    <a href="category.php?name=Table" class="text-decoration-none text-center">
      <img src="../images/table (1).png" alt="Table">
      <p>Table</p>
    </a>
  </div>
  <!-- Category 6 -->
  <div class="category-item mx-4">
    <a href="category.php?name=Utensils" class="text-decoration-none text-center">
      <img src="../images/cutlery.png" alt="Wooden Utensils">
      <p>Utensils</p>
    </a>
  </div>
</div>
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
                    src="<?php echo BASE_URL . $product['image_url']; ?>" 
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
                    <span class="fw-bold">
                      <?php echo isset($soldQuantities[$product['id']]) ? $soldQuantities[$product['id']] : 0; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No products available at the moment.</p>
    <?php endif; ?>
</div>



<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'footer.php'; ?>
</body>
</html>