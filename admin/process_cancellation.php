<?php
include '../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    if (!in_array($action, ['approve', 'deny'])) {
        die("Invalid action.");
    }

    $admin_status = $action === 'approve' ? 'Approved' : 'Denied';
    $stmt = $conn->prepare("UPDATE orders SET admin_status = ?, admin_reviewed_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $admin_status, $order_id);

    if ($stmt->execute()) {
        header("Location: cancelled_orders.php?status=success");
    } else {
        header("Location: cancelled_orders.php?status=error");
    }

    $stmt->close();
}
?>
