<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            width: 250px;
            background: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
        .sidebar .nav-link {
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .sidebar .nav-link:hover {
            background: #e9ecef;
            border-radius: 5px;
        }
        .sidebar .icon {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .product-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .gauge {
            width: 80px;
            height: 10px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .gauge .fill {
            height: 100%;
            background: green;
        }
        .stat-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat-box i {
            font-size: 1.5rem;
            color: #6c757d;
        }
        .stat-box h5 {
            margin-top: 10px;
            font-size: 1rem;
        }
        .performance-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .performance-bar .progress {
            flex: 1;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h5 class="text-center fw-bold mb-4 ">Admin Dashboard</h5>
        <ul class="nav flex-column">
            <!-- Products Section -->
            <li class="nav-item">
                <a class="nav-link bg-dark text-white" href="#">
                    <i class="fa-solid fa-box icon"></i> Products
                </a>
                <ul class="list-unstyled ms-3">
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-list icon"></i> All Products</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-tags icon"></i> Categories</a></li>
                    <li><a class="nav-link" href="inventory.php"><i class="fa-solid fa-warehouse icon"></i> Inventory</a></li>
                </ul>
            </li>
            <!-- Orders Section -->
            <li class="nav-item">
                <a class="nav-link bg-dark text-white" href="#">
                    <i class="fa-solid fa-shopping-cart icon"></i> Orders
                </a>
                <ul class="list-unstyled ms-3">
                    <li><a class="nav-link" href="view_orders.php"><i class="fa-solid fa-receipt icon"></i> All Orders</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-truck icon"></i> Shipments</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-dollar-sign icon"></i> Transactions</a></li>
                </ul>
            </li>
            <!-- Customers Section -->
            <li class="nav-item">
                <a class="nav-link bg-dark text-white" href="#">
                    <i class="fa-solid fa-users icon"></i> Customers
                </a>
                <ul class="list-unstyled ms-3">
                    <li><a class="nav-link" href="all_customers.php"><i class="fa-solid fa-user icon"></i> All Customers</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-comment icon"></i> Reviews</a></li>
                </ul>
            </li>
            <!-- Settings Section -->
            <li class="nav-item">
                <a class="nav-link bg-dark text-white" href="#">
                    <i class="fa-solid fa-cog icon"></i> Settings
                </a>
                <ul class="list-unstyled ms-3">
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-user-cog icon"></i> Profile</a></li>
                    <li><a class="nav-link" href="#"><i class="fa-solid fa-sign-out-alt icon"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h3>All Product List</h3>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <input type="text" class="form-control w-50" placeholder="Search Product">
            <button class="btn btn-primary">+ New Product</button>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-box"></i>
                    <h5>Active Product</h5>
                    <p>352</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-trophy"></i>
                    <h5>Winning Product</h5>
                    <p>3 Seater Sofa</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-star"></i>
                    <h5>Average Performance</h5>
                    <p>Good</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-boxes"></i>
                    <h5>Product Sold</h5>
                    <p>12,340</p>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <table class="table table-hover product-table">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Performance</th>
                    <th>Stock</th>
                    <th>Product Price</th>
                    <th>Visibility</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example Product Row -->
                <tr>
                    <td>
                        <img src="https://via.placeholder.com/50" alt="4 Tier Shelving">
                        4 Tier Shelving
                    </td>
                    <td>
                        <div class="performance-bar">
                            <div class="progress" style="height: 8px; width: 100px;">
                                <div class="progress-bar bg-success" style="width: 80%;"></div>
                            </div>
                            <span>Excellent</span>
                        </div>
                    </td>
                    <td>92</td>
                    <td>Custom</td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" checked>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </td>
                </tr>
                <!-- Repeat for other products -->
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
