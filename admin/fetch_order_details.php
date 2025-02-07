<?php
include '../db/connect.php';

if (isset($_GET['id'])) {
    $orderId = intval($_GET['id']);

    // Fetch order details
    $sql = "SELECT * FROM orders WHERE id = $orderId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Display order details with design
        echo "<div class='container mt-5'>
                
                    <div class='card-header bg-dark text-white text-center'>
                        <h3>Order Details</h3>
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-md-4 text-center'>";

        // Handle image path
        $imagePath = htmlspecialchars($row['image']);

        // Ensure the image path is not empty
        if (!empty($imagePath)) {
            // If the path is relative, prepend the base URL
            if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $imagePath = "http://localhost/capstone2/" . $imagePath;
            }
            echo "<img src='$imagePath' class='img-fluid img-thumbnail mb-3' alt='Product Image'>";
        } else {
            // Display placeholder if image is not found
            echo "<img src='images/placeholder.jpg' class='img-fluid img-thumbnail mb-3' alt='Placeholder Image'>";
        }

        echo "          </div>
                            <div class='col-md-8'>
                                <table class='table table-borderless'>
                                    <tr>
                                        <th>Customer Name:</th>
                                        <td>" . htmlspecialchars($row['name']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Contact Number:</th>
                                        <td>" . htmlspecialchars($row['number']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td>" . htmlspecialchars($row['address']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Product:</th>
                                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Total Products:</th>
                                        <td>" . htmlspecialchars($row['total_products']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Total Price:</th>
                                        <td>₱" . number_format($row['total_price'], 2) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method:</th>
                                        <td>" . htmlspecialchars($row['payment_method']) . "</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td><span class='badge " . ($row['status'] === 'Completed' ? 'bg-success' : 'bg-warning text-dark') . "'>" . htmlspecialchars($row['status']) . "</span></td>
                                    </tr>
                                    <tr>
                                        <th>Message:</th>
                                        <td>" . htmlspecialchars($row['message']) . "</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class='card-footer text-center'>
                        <a href='orders_list.php' class='btn btn-secondary'>Back to Orders</a>
                    </div>
                </div>
            </div>";
    } else {
        echo "<p class='text-danger text-center'>Order not found.</p>";
    }

    $conn->close();
}
?>
