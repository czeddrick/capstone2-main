<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
include "../db/connect.php";

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart if not already
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sync session cart with database if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch cart items from the database
    $stmt = $conn->prepare("SELECT id, product_id, product_name AS product_name, price, quantity, image AS image_url FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $_SESSION['cart'] = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $name = $_POST['product_name'];
        $quantity = (int)$_POST['quantity'];
        $price = $_POST['price'];
        $image = $_POST['image_url'];

        $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingItem = $result->fetch_assoc();

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQuantity, $existingItem['id']);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, product_name, quantity, price, image) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisids", $user_id, $product_id, $name, $quantity, $price, $image);
            $stmt->execute();
        }

        // Sync session cart
        $stmt = $conn->prepare("SELECT id, product_id, product_name, price, quantity, image FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $_SESSION['cart'] = $result->fetch_all(MYSQLI_ASSOC);

        // Set session variable for modal display
        $_SESSION['addedtocartModal'] = true;
header("Location: quick_view.php?id=" . $product_id);
exit;

    } else {
        header("Location: main/user_login.php");
        exit;
    }
}


// Before (using index)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['index'])) {
    $index = (int)$_GET['index'];
    if (isset($_SESSION['cart'][$index])) {
        $product_id = $_SESSION['cart'][$index]['product_id'];
        // ... delete from session and database
    }
}

// After (using id)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $cart_item_id = (int)$_GET['id'];

    // Remove from session cart
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] === $cart_item_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex
            break;
        }
    }

    // Delete from database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
        $stmt->bind_param("i", $cart_item_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Item removed successfully!";
        } else {
            $_SESSION['message'] = "Error removing item.";
        }
    }

    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Update quantity in session
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_item['quantity'] = $quantity;
        }
    }

    // Update quantity in the database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $_SESSION['user_id'], $product_id);
        
        if (!$stmt->execute()) {
            echo "Error updating database: " . $stmt->error;
        }
    }

    exit; // Stop further execution since it's an AJAX request
}



// Fetch updated cart
$cart = $_SESSION['cart'];
?>

<?php include "navbar.php"; ?>

<style>
.empty-cart-message {
  text-align: center;
  font-size: 18px;
  color: #333;
  margin-top: 200px;
}

.empty-cart-message p {
  font-size: 22px;
  color:rgb(0, 0, 0); /* Tomato red color */
}

.empty-cart-message .btn {
  margin-top: 15px;
  padding: 10px 20px;
  font-size: 16px;
  background-color: #FF6347; /* Button color */
  color: white;
  border-radius: 5px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.empty-cart-message .btn:hover {
  background-color:rgb(97, 41, 21); /* Darker red when hovering */
}




  body {
    padding-top: 100px; /* Adjust based on the height of your navbar */
}
/* Set a consistent height for each table row */
.table tbody tr {
    height: 100px; /* Adjust as needed */
    vertical-align: middle;
}

/* Ensure images are vertically centered and don't stretch */
.table tbody tr td img {
    max-height: 80px; /* Adjust based on your needs */
    width: auto;
    display: block;
    margin: auto;
}
</style>

<div class="container cart-container">
    <div class="row">
        <div class="col-md-8">
            <h3>Your Cart</h3>
            <?php
            // Query the cart items
            $cart_query = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ?");
            $cart_query->bind_param("i", $_SESSION['user_id']);
            $cart_query->execute();
            $cart_result = $cart_query->get_result();

            // Check if the cart is empty
            if ($cart_result->num_rows == 0) {
                // Display the empty cart message
              // Display the empty cart message
    echo "<div class='empty-cart-message'>";
    echo "<p>Your cart is currently empty.</p>";
    echo "<a href='../home/products.php' class='btn'>Shop Now</a>";
    echo "</div>";
            } else {
            ?>
                <!-- Cart Form for Updating Quantities -->
                <form id="cart-form" action="checkout.php" method="post">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $cart_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_items[]" value="<?php echo $item['id']; ?>" class="product-checkbox">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>" style="width: 80px;"></td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <input type="number" 
                                               name="quantities[<?php echo $item['product_id']; ?>]" 
                                               value="<?php echo $item['quantity'] ?? 1; ?>" 
                                               min="1" 
                                               class="form-control quantity-input" 
                                               style="width: 80px;"
                                               data-product-id="<?php echo $item['product_id']; ?>">
                                    </td>
                                    <td>₱<?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2); ?></td>
                                    <td>
                                        <a href="#" onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES); ?>')" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            <?php } ?>
        </div>
                     


        
        
        <div class="col-md-4">
            <h4>Cart Totals</h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total</span>
                    <strong>
                        ₱<?php echo number_format(array_sum(array_map(function($item) {
                            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                        }, $cart)), 2); ?>
                    </strong>
                   
                </li>
            </ul> 
            <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between">
            <span>Selected item total</span>
                    <strong id="selected-total">₱0.00</strong>
                </li>
            </ul> 
            <form id="checkout-form" action="checkout.php" method="POST">
            <input type="hidden" name="selected_items" id="selected-items">
            <button type="submit" class="btn btn-warning" id="checkoutBtn" disabled>Proceed to Checkout</button>
            </form>


        </div>
    </div>
</div>
<!-- Delete Confirmation Modal (Centered) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- Centering the modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove <strong id="productName"></strong> from your cart?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Item Added to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The item has been successfully added to your cart.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <a href="cart.php" class="btn btn-primary">Go to Cart</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




<script>

document.addEventListener("DOMContentLoaded", function() {
    const productCheckboxes = document.querySelectorAll(".product-checkbox");
    const selectedTotalElement = document.getElementById("selected-total");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const selectedItemsInput = document.getElementById("selected-items");

    function updateSelectedTotal() {
        let selectedTotal = 0;
        let selectedItems = [];

        productCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const row = checkbox.closest("tr");
                const price = parseFloat(row.querySelector("td:nth-child(4)").textContent.replace("₱", "").replace(",", ""));
                const quantity = parseInt(row.querySelector(".quantity-input").value);
                selectedTotal += price * quantity;
                selectedItems.push(checkbox.value);
            }
        });

        selectedTotalElement.textContent = `₱${selectedTotal.toFixed(2)}`;
        checkoutBtn.disabled = selectedItems.length === 0;
        selectedItemsInput.value = selectedItems.join(",");
    }

    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", updateSelectedTotal);
    });

    document.querySelectorAll(".quantity-input").forEach(input => {
        input.addEventListener("change", updateSelectedTotal);
    });
});



function confirmDelete(itemId, productName) {
    // Set the product name in the modal
    document.getElementById("productName").innerText = productName;

    // Set the delete link dynamically
    document.getElementById("confirmDeleteBtn").href = "?action=delete&id=" + itemId;

    // Show the modal
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

$(document).ready(function() {
    $('.quantity-input').on('change', function() {
        let productID = $(this).data('product-id');
        let quantity = $(this).val();

        $.ajax({
            url: "cart.php",  // Same PHP file handling the request
            method: "POST",
            data: { product_id: productID, quantity: quantity },
            success: function(response) {
                console.log("Cart updated successfully");
                location.reload();  // Refresh the cart totals
            },
            error: function() {
                console.log("Error updating cart");
            }
        });
    });
});


 document.addEventListener("DOMContentLoaded", function() {
        const quantityInputs = document.querySelectorAll(".quantity-input");
        const cartSubtotal = document.getElementById("cart-subtotal");
        const cartTotal = document.getElementById("cart-total");

        function updateCart() {
            let subtotal = 0;

            quantityInputs.forEach(input => {
                const price = parseFloat(input.getAttribute("data-price"));
                const quantity = parseInt(input.value);
                const rowSubtotal = price * quantity;
                subtotal += rowSubtotal;

                // Update subtotal per row
                input.closest("tr").querySelector(".subtotal").textContent = `$${rowSubtotal.toFixed(2)}`;
            });

            // Update Cart Summary
            cartSubtotal.textContent = `$${subtotal.toFixed(2)}`;
            cartTotal.textContent = `$${subtotal.toFixed(2)}`;
        }

        // Event Listener for Quantity Change
        quantityInputs.forEach(input => {
            input.addEventListener("input", updateCart);
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
    const productCheckboxes = document.querySelectorAll(".product-checkbox");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const selectedItemsInput = document.getElementById("selected-items");
    const checkoutForm = document.getElementById("checkout-form");

    function updateCheckoutButton() {
        let selectedItems = [];
        productCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedItems.push(checkbox.value); 
            }
        });

        checkoutBtn.disabled = selectedItems.length === 0;
        selectedItemsInput.value = selectedItems.join(",");
    }

    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", updateCheckoutButton);
    });

    checkoutForm.addEventListener("submit", function(event) {
        updateCheckoutButton(); // Ensure data is up-to-date
        if (checkoutBtn.disabled) {
            event.preventDefault();
            alert("Please select at least one item before proceeding to checkout.");
        }
    });
});


</script>
</body>
</html>