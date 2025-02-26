<?php
include 'confignav.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
}
include 'confignav.php';
include 'navbar.php'; 
?>


<?php

// Include database connection


// Fetch products with sold count
$sql = "SELECT p.*, 
        COALESCE(SUM(o.total_products), 0) AS sold 
        FROM products p
        LEFT JOIN orders o ON p.id = o.product_id
        WHERE p.tags LIKE '%eco%'
        GROUP BY p.id";

$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "No eco-friendly products found.";
}

// Search functionality
if (isset($_POST['search_box'])) {
    $search_box = trim($_POST['search_box']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ?");
    $search_term = '%' . $search_box . '%';
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Search Results:</h3>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row['product_name'] . " - Sold: " . $row['sold'] . "<br>";
        }
    } else {
        echo "No products match your search."; 
    }

    $stmt->close();
}

// Close connection
$conn->close();

?>




<style>

    
    </style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Friendly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>



<div class="search-box-container bg-light py-3 ">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <form method="GET" action="search_results.php" class="d-flex w-100">
                    <input 
                        type="text" 
                        name="search_box" 
                        class="form-control border-warning" 
                        placeholder="What do you want huh?" 
                        aria-label="Search products"
                        required
                    >
                    <button class="btn btn-outline-warning text-dark" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>






<div class="container my-5"> 
  <h2 class="text-center mb-4" style="font-family: fantasy; color: #343a40;">Eco-Friendly Products</h2>
  <p class="text-center" style="font-family: 'Courier New', Courier, monospace; font-size: 20px; color: gray;">
     Collection - New Modern Design
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
                <span class="badge bg-warning ms-1 text-dark"> <?php echo $product['discount_percentage']; ?>% OFF</span>
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
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No products available at the moment.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
