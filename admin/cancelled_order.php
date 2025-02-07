<?php
include '../db/connect.php';
session_start();
include "../navbar.php"; 
// Ensure admin is logged in


// Fetch canceled orders
$sql = "SELECT id, image, product_name, name, payment_method, voucher_used, total_price, total_products, cancel_reason, placed_on, canceled_at, admin_status 
        FROM orders 
        WHERE status = 'Canceled' 
        ORDER BY canceled_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Canceled Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1 class="mb-4">Canceled Orders</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Customer</th>
                    <th>Payment Method</th>
                    <th>Voucher</th>
                    <th>Total Price</th>
                    <th>Items</th>
                    <th>Cancel Reason</th>
                    <th>Placed On</th>
                    <th>Canceled At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td>
                            <?php 
                            $image_path = htmlspecialchars($order['image']);
                            if (!empty($image_path)) {
                                if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
                                    $image_path = "http://localhost/capstone2/" . $image_path;
                                }
                                echo '<img src="' . $image_path . '" alt="Product" width="80">';
                            } else {
                                echo '<img src="images/placeholder.jpg" alt="Placeholder" width="80">';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($order['voucher_used'] ?: 'None'); ?></td>
                        <td>₱<?php echo number_format((float)$order['total_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['total_products']); ?></td>
                        <td><?php echo htmlspecialchars($order['cancel_reason']); ?></td>
                        <td><?php echo date("M d, Y h:i A", strtotime($order['placed_on'])); ?></td>
                        <td><?php echo date("M d, Y h:i A", strtotime($order['canceled_at'])); ?></td>
                        <td>
                            <?php if ($order['admin_status']): ?>
                                <span class="badge bg-secondary">
                                    <?php echo htmlspecialchars($order['admin_status']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (empty($order['admin_status'])): ?>
                                <form action="process_cancellation.php" method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                        Approve
                                    </button>
                                    <button type="submit" name="action" value="deny" class="btn btn-danger btn-sm mt-1">
                                        Deny
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <?php echo htmlspecialchars($order['admin_status']); ?>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No canceled orders to display.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>