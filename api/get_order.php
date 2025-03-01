<?php
header("Content-Type: application/json");

$sourceDb = [
    'host' => 'localhost',
    'dbname' => 'user_db1',
    'user' => 'root',
    'pass' => ''
];

$destinationDb = [
    'host' => 'localhost',
    'dbname' => 'orders(1)',
    'user' => 'root',
    'pass' => ''
];

try {
    // Connect to Source Database
    $sourcePdo = new PDO("mysql:host={$sourceDb['host']};dbname={$sourceDb['dbname']}", $sourceDb['user'], $sourceDb['pass']);
    $sourcePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connect to Destination Database
    $destinationPdo = new PDO("mysql:host={$destinationDb['host']};dbname={$destinationDb['dbname']}", $destinationDb['user'], $destinationDb['pass']);
    $destinationPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data using source column names
    $query = $sourcePdo->query("
        SELECT 
            id, 
            placed_on AS order_date, 
            total_price AS total_amount, 
            status, 
            user_id AS customer_id, 
            payment_method AS payment_method, 
            placed_on AS created_at, 
            message AS cancellation_requested, 
            canceled_at AS cancellation_time, 
            cancel_reason AS cancellation_reason 
        FROM orders
    ");
    $orders = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($orders)) {
        echo json_encode(["message" => "No orders found"]);
        exit;
    }

    // Destination table columns
    $columns = [
        "id", "order_date", "total_amount", "status", "customer_id", 
        "payment_method", "created_at", "cancellation_requested", 
        "cancellation_time", "cancellation_reason"
    ];
    
    $columnNames = implode(", ", $columns); // ✅ Define column names properly
    $placeholders = implode(", ", array_fill(0, count($columns), "?"));
    $checkQuery = $destinationPdo->prepare("SELECT id FROM orders WHERE id = ?");
    $insertQuery = $destinationPdo->prepare("INSERT INTO orders ($columnNames) VALUES ($placeholders)");

    $insertedCount = 0;

    foreach ($orders as $order) {
        // Check if order ID already exists
        $checkQuery->execute([$order["id"]]);
        if ($checkQuery->fetch()) {
            continue; // Skip inserting if ID already exists
        }

        // Insert new order
        $insertQuery->execute(array_values($order));
        $insertedCount++;
    }

    echo json_encode(["message" => "Orders transferred successfully", "inserted" => $insertedCount]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
