<?php
include '../db/connect.php';
session_start();

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
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?><br>
                        <strong>Placed On:</strong> <?php echo date("M d, Y h:i A", strtotime($order['placed_on'])); ?>
                    </span>
                    <button class="btn btn-sm btn-warning text-white" disabled>
                        <?php echo htmlspecialchars($order['admin_status'] ?: 'Pending'); ?>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <?php 
                            $image_path = htmlspecialchars($order['image']);
                            if (!empty($image_path)) {
                                if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
                                    $image_path = "http://localhost/capstone2/" . $image_path;
                                }
                                echo '<img src="' . $image_path . '" alt="Product" class="img-fluid rounded">';
                            } else {
                                echo '<img src="images/placeholder.jpg" alt="Placeholder Image" class="img-fluid rounded">';
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="text-muted mb-1"><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                            <p class="text-muted mb-1"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p class="text-muted mb-1"><strong>Voucher Used:</strong> <?php echo htmlspecialchars($order['voucher_used'] ?: 'None'); ?></p>
                            <p class="text-muted mb-0"><strong>Cancel Reason:</strong> <?php echo htmlspecialchars($order['cancel_reason']); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h5 class="text-danger">₱<?php echo number_format((float)$order['total_price'], 2); ?></h5>
                            <p class="text-muted mb-0"><strong>Total Products:</strong> <?php echo htmlspecialchars($order['total_products']); ?></p>
                            <p class="text-muted mb-1"><strong>Canceled At:</strong> <?php echo date("M d, Y h:i A", strtotime($order['canceled_at'])); ?></p>
                            <?php if (empty($order['admin_status'])): ?>
                                <form action="process_cancellation.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="btn btn-success mt-2" name="action" value="approve">
                                        Approve Cancellation
                                    </button>
                                    <button type="submit" class="btn btn-danger mt-2" name="action" value="deny">
                                        Deny Cancellation
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary mt-2" disabled>
                                    <?php echo htmlspecialchars($order['admin_status']); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No canceled orders to display.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
