<?php
include '../db/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $user_id = $_SESSION['user_id'];

    // Retrieve product details from the order
    $sql = "SELECT product_name, total_price FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Add the product to the cart
        $sql_insert = "INSERT INTO cart (user_id, product_name, price, quantity) VALUES (?, ?, ?, 1)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isd", $user_id, $order['product_name'], $order['total_price']);

        if ($stmt_insert->execute()) {
            header('Location: cart.php'); // Redirect to the cart page
            exit;
        } else {
            echo "Failed to add product to cart.";
        }
    } else {
        echo "Order not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
