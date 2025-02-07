<?php
include '../db/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'], $_POST['cancel_reason']) && isset($_SESSION['user_id'])) {
        $order_id = intval($_POST['order_id']);
        $cancel_reason = trim($_POST['cancel_reason']);
        $user_id = $_SESSION['user_id'];

        // Update the order status to 'Canceled'
        $sql = "UPDATE orders 
                SET status = 'Canceled', cancel_reason = ?, canceled_at = NOW(), admin_status = 'Pending' 
                WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sii", $cancel_reason, $order_id, $user_id);
            if ($stmt->execute()) {
                echo "Your cancellation request has been submitted successfully.";
            } else {
                echo "Failed to submit cancellation request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            die("SQL error: " . $conn->error);
        }
    } else {
        echo "Invalid input or user not logged in.";
    }
} else {
    echo "Invalid request method.";
}
?>
