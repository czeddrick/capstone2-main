<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
 

    <div class="container my-5">
        <h1 class="text-center mb-4">All Customers</h1>
        
        <!-- Search -->
        <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" placeholder="Search by customer name...">
                <button class="btn btn-warning">Search</button>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Customer ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>501</td>
                        <td>John Doe</td>
                        <td>johndoe@example.com</td>
                        <td>+1234567890</td>
                        <td>
                            <button class="btn btn-info btn-sm">View Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>502</td>
                        <td>Jane Smith</td>
                        <td>janesmith@example.com</td>
                        <td>+0987654321</td>
                        <td>
                            <button class="btn btn-info btn-sm">View Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>503</td>
                        <td>Chris Evans</td>
                        <td>chrisevans@example.com</td>
                        <td>+1122334455</td>
                        <td>
                            <button class="btn btn-info btn-sm">View Details</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <a href="dashboard.php" class="btn btn-dark btn-sm">Back to Dashboard</a>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
