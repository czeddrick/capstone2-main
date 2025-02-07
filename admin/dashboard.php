
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .navbar {
            margin-left: 250px;
            background: #007bff;
            color: white;
        }
        .card-header {
            background: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
     <?php
    include '../db/connect.php';
session_start();
if (isset($_SESSION['id'])):
     endif;  ?>

    <div class="sidebar">
        <h5 class="text-center fw-bold mb-4 text-dark ">Welcome, <?= htmlspecialchars($_SESSION['email']);?><h5>
        <ul class="nav flex-column">

            <!-- Products Section -->
            <li class="nav-item">
                <a class="nav-link bg-dark text-white" href="#">
                    <i class="fa-solid fa-box icon"></i> Products
                </a>
                <ul class="list-unstyled ms-3">
                    <li><a class="nav-link" href="all_products.php"><i class="fa-solid fa-list icon"></i> All Products</a></li>
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
<li><a class="nav-link" href="#"><i class="fa-solid fa-clock icon"></i> Pending</a></li>
<li><a class="nav-link" href="#"><i class="fa-solid fa-check-circle icon"></i> Completed</a></li>
<li><a class="nav-link" href="cancelled_order.php"><i class="fa-solid fa-ban icon"></i> Cancelled</a></li>
<li><a class="nav-link" href="#"><i class="fa-solid fa-truck icon"></i> Shipments</a></li>
<li><a class="nav-link" href="#"><i class="fa-solid fa-wallet icon"></i> Transactions</a></li>

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

    <!-- Navbar -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link " href="#"><i class="fa-solid fa-user-circle"></i> </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <div class="content">
        <h3 class="mb-4">Add New Product</h3>
        <form>
            <!-- General Information -->
            <div class="card mb-4">
                <div class="card-header bg-dark">General Information</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" id="productName" class="form-control" placeholder="Enter product name">
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" class="form-select">
                                <option selected disabled>Select category</option>
                                <option>Tumbler</option>
                                <option>Utensils</option>
                                <option>Powerbank</option>
                                <option>Mini Fan</option>
                                <option>Coat</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" class="form-control" rows="3" placeholder="Enter product description"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="basePrice" class="form-label">Base Price P</label>
                            <input type="number" id="basePrice" class="form-control" placeholder="Enter base price">
                        </div>

                    </div>
                </div>
            </div>
            

            <!-- Product Variants -->
            <div class="card mb-4">
                <div class="card-header bg-dark">Product Variants</div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <label for="size" class="form-label">Color</label>
                            <select id="size" class="form-select">
                                <option selected disabled>Select Color</option>
                                <option>Yellow</option>
                                <option>Red</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price (P)</label>
                            <input type="number" id="price" class="form-control" placeholder="Enter price">
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" id="stock" class="form-control" placeholder="Enter stock">
                        </div>
                    </div>
                    <button type="button" class="btn btn-warning">+ Add Color</button>
                </div>
            </div>

            <!-- Product Images -->
            <div class="card mb-4">
                <div class="card-header bg-dark">Product Images</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="mainImage" class="form-label">Main Image</label>
                        <input type="file" id="mainImage" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="additionalImages" class="form-label">Additional Images</label>
                        <input type="file" id="additionalImages" class="form-control" multiple>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-warning">Add Product</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
