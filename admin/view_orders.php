<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h1 {
            color: #333;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .badge {
            font-size: 0.9rem;
        }
        .btn-warning, .btn-dark {
            transition: background-color 0.3s;
        }
        .btn-warning:hover, .btn-dark:hover {
            background-color: #e0a800;
        }
        .input-group input {
            border-radius: 20px 0 0 20px;
        }
        .input-group button {
            border-radius: 0 20px 20px 0;
        }
        .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Customer Orders</h1>

        <!-- Search and Filters -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <select id="statusFilter" class="form-select" style="width: 200px;">
                    <option value="all" selected>All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>
            <div class="input-group" style="width: 300px;">
                <input type="text" id="searchCustomer" class="form-control" placeholder="Search by customer name...">
                <button class="btn btn-warning">Search</button>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Product</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                        include '../db/connect.php';

                        $sql = "SELECT * FROM orders ORDER BY placed_on DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Use the default placeholder image if no image path is provided
                                $imagePath = !empty($row['image']) ? htmlspecialchars($row['image']) : "images/default-placeholder.png";

                                // Ensure the image path is absolute if relative
                                if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                    $imagePath = "http://localhost/capstone2/" . ltrim($imagePath, '/');
                                }

                                // Output the table row with the order details
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>" . htmlspecialchars($row['name']) . "</td>
                                        <td>
                                            <img src='$imagePath' alt='Product Image' 
                        style='width: 100px; height: 100px; object-fit: cover; border: none;'>
                                            
                                        </td>
                                        <td>{$row['placed_on']}</td>
                                        <td>
                                            <span class='badge bg-" . 
                                                ($row['status'] == 'Pending' ? 'warning' : ($row['status'] == 'Completed' ? 'success' : 'danger')) . "'>
                                                {$row['status']}
                                            </span>
                                        </td>
                                        <td>₱" . number_format($row['total_price'], 2) . "</td>
                                        <td>
                                            <button class='btn btn-dark btn-sm view-order' data-id='{$row['id']}'>View</button>
                                        </td>
                                    </tr>";
                                    }
                                } else {
                                    // Output a row indicating no orders found
                                    echo "<tr><td colspan='7' class='text-center'>No orders found.</td></tr>";
                                }

                                $conn->close();
                                ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="orderDetails"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <a href="dashboard.php" class="btn btn-dark btn-sm mt-4 d-inline-block">Back to Dashboard</a>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const viewButtons = document.querySelectorAll('.view-order');

            viewButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.getAttribute('data-id');

                    // Fetch order details via AJAX
                    fetch(`fetch_order_details.php?id=${orderId}`)
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('orderDetails').innerHTML = data;
                            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                            orderModal.show();
                        });
                });
            });
        });
    </script>
</body>
</html>
