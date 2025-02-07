<?php include '../navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: rgb(254,248,222);
            background: linear-gradient(90deg, rgba(254,248,222,0.9640231092436975) 5%, rgba(254,252,235,1) 35%, rgba(255,254,243,1) 89%, rgba(255,251,202,1) 100%);
            color: white;
        }

        .sidebar {
            height: 99vh;
            width: 255px;
            position: fixed;
            top: 0;
            left: 0%;
            background-color: black;
            color: white;
            padding-top: 20px;
            
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.hidden {
            transform: translateX(-300px);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            display: block;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #FFFFC5;
            color: black;
        }

        .menu-btn {
            font-size: 1.5rem;
            border: none;
            background: transparent;
            color: white;
            cursor: pointer;
            margin: 10px 15px;
        }

        .dashboard-header {
            margin-bottom: 20px;
        }

        .content {
            margin-left: 270px;
            transition: margin-left 0.3s ease-in-out;
        }

        .content.expanded {
            margin-left: 30px;
        }

        .sidebar .active {
            background-color: gray;
        }

        .profile-section {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: transparent;
            border-radius: 10px;
            margin: 10px;
        }

        .profile-section img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .profile-section span {
            color: white;
            font-weight: bold;
        }

        .welcome-message {
            background-color: #4951;
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            background-color: #4951;
            color: white;
        }

        .card-body {
            background-color: #4951;
        }

        @media (max-width: 767px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }

            .profile-section {
                justify-content: center;
            }
        }
        .btn-gray {
            background-color: gray;
            border-color: gray;
            color: white;
        }
        .theme-toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: transparent;
            border: none;
            color: #EBEBFF;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }
        .theme-toggle-btn.light {
            color: #1D202D; /* Icon color for light theme */
        }
    </style>
</head>
<body>

   <!-- Sidebar -->
   <!-- Sidebar -->
<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <h2>Account</h2>
    <a href="#to-pay"><i class="bi bi-credit-card me-2"></i>To Pay</a>
    <a href="#to-ship"><i class="bi bi-truck me-2"></i>To Ship</a>
    <a href="#to-receive"><i class="bi bi-box-seam me-2"></i>To Receive</a>
    <a href="#to-rate"><i class="bi bi-star me-2"></i>To Rate</a>
    <a href="#order-status"><i class="bi bi-receipt me-2"></i>Order Status</a>
    <a href="#settings"><i class="bi bi-gear me-2"></i>Settings</a>
    <a href="#my-vouchers"><i class="bi bi-tags me-2"></i>My Vouchers and Coupons</a>
    <a href="../db/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>



    <!-- Content -->
    <div id="content" class="content">
        <div class="navbar d-flex justify-content-between align-items-center">
            <button id="menu-btn" class="menu-btn text-black">☰</button>
            <div class="d-flex align-items-center me-4">
                <i class="bi bi-person-circle me-2" style="font-size: 2rem; color: white;"></i>
                <span style="color: white; font-size: 1.5rem;">Hi, Thirdy</span>
            </div>
        </div>

    <!-- Main Content -->
    <div class="main-content px-4">
        <div class="welcome-message text-black">
            <h2>Welcome, Thirdy Magsalsal</h2>
            <p>Here's your dashboard overview.</p>
        </div>

        <div class="row mt-4">
            <!-- Task Overview Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-black">
                        <h5 class="card-title">Task Overview</h5>
                        <p class="card-text">View and manage your assigned tasks.</p>
                        <a href="#" class="btn btn-gray">View Tasks</a>
                    </div>
                </div>
            </div>

            <!-- Profile Settings Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-black">
                        <h5 class="card-title">Profile Settings</h5>
                        <p class="card-text">Update your personal details and settings.</p>
                        <a href="#" class="btn btn-gray">Edit Profile</a>
                    </div>
                </div>
            </div>

            <!-- Notifications Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-black">
                        <h5 class="card-title">Notifications</h5>
                        <p class="card-text">View recent updates and notifications.</p>
                        <a href="#" class="btn btn-gray">View Notifications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const menuBtn = document.getElementById("menu-btn");
        const sidebar = document.getElementById("sidebar");
        const content = document.getElementById("content");
    
        menuBtn.addEventListener("click", function() {
            sidebar.classList.toggle("hidden");
            content.classList.toggle("expanded");
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>