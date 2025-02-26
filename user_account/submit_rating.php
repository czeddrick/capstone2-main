<?php
$host = 'localhost';
$db   = 'user_db1'; // Replace with your database name
$user = 'root';      // Replace with your database username
$pass = '';      // Replace with your database password
$charset = 'utf8mb4';
// Set up DSN and create a PDO instance
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Handle connection errors
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

// Check if 'action' is set in POST data
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Handle connection errors
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

// Check if 'action' is set in POST data
if(isset($_POST['action'])) {
    if($_POST['action'] === 'submit_review') {
        // Handle review submission

        // Retrieve and sanitize input data
        $product_id = $_POST['product_id'];
        $order_id = $_POST['order_id']; // Retrieve the order ID
        $rating = (int)$_POST['rating_data'];
        $user_name = trim($_POST['user_name']);
        $user_review = trim($_POST['user_review']);
        $datetime = date("Y-m-d H:i:s");

        // Basic validation
        if(empty($product_id) || empty($order_id) || empty($user_name) || empty($user_review) || $rating < 1 || $rating > 5) {
            http_response_code(400);
            echo "Invalid input. Please ensure all fields are filled correctly.";
            exit();
        }

        // Prepare and execute the insert statement
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, order_id, user_name, rating, review, datetime) VALUES (:product_id, :order_id, :user_name, :rating, :review, :datetime)");
        try {
            $stmt->execute([
                ':product_id' => $product_id,
                ':order_id' => $order_id, // Include the order ID
                ':user_name' => htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'),
                ':rating' => $rating,
                ':review' => htmlspecialchars($user_review, ENT_QUOTES, 'UTF-8'),
                ':datetime' => $datetime
            ]);
            echo "Thank you for your review!";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Failed to submit review: " . $e->getMessage();
        }

    } elseif($_POST['action'] === 'load_data') {
        // Handle loading review data

        // Retrieve and sanitize product_id
        $product_id = $_POST['product_id'];

        if(empty($product_id)) {
            http_response_code(400);
            echo "Product ID is missing.";
            exit();
        }

        // Fetch average rating and total reviews
        $stmt = $pdo->prepare("SELECT AVG(rating) AS average_rating, COUNT(*) AS total_review FROM reviews WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $product_id]);
        $result = $stmt->fetch();

        $average_rating = $result['average_rating'] ? number_format($result['average_rating'], 1) : "0.0";
        $total_review = $result['total_review'] ? $result['total_review'] : "0";

        // Fetch total reviews for each star
        $star_counts = [];
        for($star = 5; $star >=1; $star--) {
            $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM reviews WHERE product_id = :product_id AND rating = :rating");
            $stmt->execute([
                ':product_id' => $product_id,
                ':rating' => $star
            ]);
            $count = $stmt->fetch()['count'];
            $star_counts["{$star}_star_review"] = $count;
        }

        // Fetch all reviews for the product
        $stmt = $pdo->prepare("SELECT user_name, rating, review, datetime FROM reviews WHERE product_id = :product_id ORDER BY datetime DESC");
        $stmt->execute([':product_id' => $product_id]);
        $reviews = $stmt->fetchAll();

        // Prepare the response
        $response = [
            'average_rating' => $average_rating,
            'total_review' => $total_review,
            'five_star_review' => $star_counts['5_star_review'],
            'four_star_review' => $star_counts['4_star_review'],
            'three_star_review' => $star_counts['3_star_review'],
            'two_star_review' => $star_counts['2_star_review'],
            'one_star_review' => $star_counts['1_star_review'],
            'review_data' => $reviews
        ];

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Handle unknown actions
        http_response_code(400);
        echo "Invalid action.";
    }
} else {
    // Handle missing action
    http_response_code(400);
    echo "No action specified.";
}
?>