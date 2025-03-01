<?php

header("Content-Type: application/json");

// Database connection
$host = "localhost";
$dbname = "user_db1";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle API requests
if ($method == 'GET') {
    // Initialize the base query
    $query = "SELECT * FROM orders";
    $params = [];
    $conditions = [];

    // Check for filter parameters (e.g., status, user_id, etc.)
    if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }
    }

    // Add WHERE clause if there are conditions
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($orders ?: []);



} elseif ($method == 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    if (isset($input['name']) && isset($input['order_id'])) {
        $stmt = $pdo->prepare("INSERT INTO orders (order_id, name) VALUES (:order_id, :name)");
        $stmt->execute(['order_id' => $input['order_id'], 'name' => $input['name']]);
        echo json_encode(["message" => "Order added", "order" => $input]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
    }
} elseif ($method == 'DELETE') {
    $orderId = $_GET['order_id'] ?? null;
    if ($orderId) {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);
        echo json_encode(["message" => "Order deleted", "order_id" => $orderId]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Missing order_id"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>