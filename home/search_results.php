<?php
include '../db/connect.php'; // Include your database connection

// Check if the search term is provided
if (isset($_GET['search_box'])) {
    $search_box = trim($_GET['search_box']);

    try {
        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT * FROM `products` WHERE `product_description` LIKE ?");
        $search_term = '%' . $search_box . '%';
        $stmt->bind_param('s', $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch results
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
} else {
    echo "No search term provided.";
    exit;
}
$soldQuantities = [];
$sqlSold = "SELECT product_id, COUNT(product_id) AS total_sold FROM reviews GROUP BY product_id";
$resultSold = $conn->query($sqlSold);

if ($resultSold->num_rows > 0) {
    while ($row = $resultSold->fetch_assoc()) {
        $soldQuantities[$row['product_id']] = $row['total_sold'];
    }
}
$conn->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    
</head>
<body>
<?php include 'navbar.php'; ?>

<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin-top: 80px;
    padding: 0;
}

.h2{

  margin=top: 100px;
}

.container {
    max-width: 1300px;
    margin: auto;
    padding: 5px;
}

.card {
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.card img {
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    max-height: 200px;
    object-fit: cover;
    width: 100%;
}

.card-title {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.card-body p {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.price {
    color: #28a745;
    font-weight: bold;
}

.discount {
    color: #dc3545;
    font-size: 0.9rem;
    margin-left: 5px;
}

.action-buttons .btn {
    font-size: 0.8rem;
    padding: 5px 10px;
}

.ratings {
    font-size: 0.85rem;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

    </style>
    
    <div class="container mt-5" style= "margin-top: 100px">
        <h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($search_box); ?>"</h2>

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
        <div class="col-12 text-center">
            <p class="text-muted">No results found.</p>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
