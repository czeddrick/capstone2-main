document.addEventListener('DOMContentLoaded', function () {
    const cartIcon = document.getElementById('cartIcon');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCartSidebar = document.getElementById('closeCartSidebar');
    const clearCart = document.getElementById('clearCart');
  
    // Open sidebar
    cartIcon.addEventListener('click', function () {
      cartSidebar.classList.add('open');
    });
  
    // Close sidebar
    closeCartSidebar.addEventListener('click', function () {
      cartSidebar.classList.remove('open');
    });
  
    // Clear cart
    clearCart.addEventListener('click', function () {
      fetch('clear_cart.php') // PHP script to clear cart session
        .then(() => location.reload());
    });
  });