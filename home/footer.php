<?php 
include 'confignav.php';
?>

<style>


footer {
  font-family: 'Poppins', sans-serif;
  background: rgb(255,245,210);
background: linear-gradient(176deg, rgba(255,245,210,1) 0%, rgba(255,252,200,1) 50%, rgba(112,112,112,1) 100%);
  color: white;
}

footer a {
  color: white;
  transition: color 0.3s ease;
}

footer a:hover {
  color: yellow;
  text-decoration: underline;
}

footer h5 {
  color: white;
  font-size: 18px;
  border-bottom: 2px solid #fff;
  display: inline-block;
  margin-bottom: 15px;
  padding-bottom: 5px;
}

footer .col-lg-3 img {
  margin-bottom: 20px;
  border-radius: 10px;
}

footer .social-icons a {
  font-size: 20px;
  margin-right: 15px;
  color: black;
  transition: transform 0.3s ease;
}

footer .social-icons a:hover {
  transform: scale(1.2);
}

footer .payment-icons img {
  
  margin-top: 15px;
}

footer .text-center {
  background: black;
  font-size: 14px;
}

footer .text-center h6 {
  margin: 0;
}

html, body {
  height: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1; /* Pushes the footer down */
}

footer {
  margin-top: auto; /* Keeps the footer at the bottom */
}

</style>


<footer>
  <div class="container-fluid mt-5 p-5 bg-dark text-white" id="custom-footer">
    <div class="row">
      <!-- About Section -->
      <div class="col-lg-3">
        <img src="<?php echo BASE_URL; ?>images/log.png" alt="Logo" style="height: 70px; width:100px;">
        <h3 class="fw-bold fs-4 mb-2">GREAT WALL ARTS</h3>
        <p>
          Established in 2018 by a born-again Christian couple to produce Filipino-made handcrafted products, GWA uses the latest technology to personalize items to meet customer standards.
        </p>
        <h5>Follow us</h5>
        <div class="social-icons">
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-facebook"></i></a>
        </div>
      </div>

      <!-- Links Section -->
      <div class="col-lg-2">
        <h5>Links</h5>
        <a href="<?php echo BASE_URL; ?>index.php" class="d-block">Home</a>
        <a href="<?php echo BASE_URL; ?>home/products.php" class="d-block">Products</a>
        <a href="<?php echo BASE_URL; ?>home/contact.php" class="d-block">Contact Us</a>
      </div>

      <!-- About Section -->
      <div class="col-lg-2">
        <h5>About</h5>
        <a href="#" class="d-block">About Us</a>
        <a href="#" class="d-block">Delivery Information</a>
        <a href="#" class="d-block" data-bs-toggle="modal" data-bs-target="#productPolicyModal">Product Policy</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#termsConditionsModal">Terms & Conditions</a>
        <a href="<?php echo BASE_URL; ?>home/contact.php" class="d-block">Contact Us</a>
      </div>

      <!-- My Account Section -->
      <div class="col-lg-2">
        <h5>My Account</h5>
        <a href="<?php echo BASE_URL; ?>home/cart.php" class="d-block">View Cart</a>
        <a href="<?php echo BASE_URL; ?>home/user_purchased.php" class="d-block">Order</a>
        <a href="#" class="d-block">Help</a>
      </div>

      <!-- Payments Section -->
      <div class="col-lg-3">
        <h5>Secured Payment Gateways</h5>
        <div class="payment-icons">
          <img src="<?php echo BASE_URL; ?>images/pay.png" alt="Payment Methods" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
  <div class="text-center py-3 bg-warning text-dark">
    <h6>Designed and Developed by 4th Year BSIT Students in BCP</h6>
  </div>
</footer>

<style>
  /* Make modal header fixed */
  .modal-header {
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 1050;
  }

  /* Make modal body scrollable */
  .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }

  .modal-backdrop {
    background-color: rgba(24, 24, 24, 0.66) !important; /* Light white overlay */
  }
</style>
<!-- Product Policy Modal -->
<div class="modal fade" id="productPolicyModal" tabindex="-1" aria-labelledby="productPolicyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal Header (Fixed) -->
      <div class="modal-header">
        <h5 class="modal-title" id="productPolicyModalLabel">Product Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <!-- Modal Body (Scrollable) -->
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <p><strong>Last Updated:</strong> February 23, 2025</p>

        <p>Welcome to Great Wall Arts! This Product Policy outlines the terms and conditions regarding the purchase, use, and return of products from our website. By purchasing from us, you agree to the terms below.</p>

        <h6>1. Product Information</h6>
        <ul>
          <li>We strive to provide accurate descriptions, images, and specifications for all products listed on our website.</li>
          <li>Colors and details may vary slightly due to screen settings and manufacturer updates.</li>
          <li>Prices and availability are subject to change without notice.</li>
        </ul>

        <h6>2. Order Placement & Processing</h6>
        <ul>
          <li>Orders are processed within 3-5 business days after payment confirmation.</li>
          <li>You will receive an order confirmation email with tracking details once your order is shipped.</li>
          <li>We reserve the right to cancel or refuse any order due to fraudulent activity or stock issues.</li>
        </ul>

        <h6>3. Shipping & Delivery</h6>
        <ul>
          <li>Estimated delivery times depend on the shipping method chosen at checkout.</li>
          <li>Delays caused by customs, weather, or other external factors are beyond our control.</li>
        </ul>

        <h6>4. Returns & Refunds</h6>
        <ul>
          <li>Products can be returned within 3-5 days of delivery if they meet our return eligibility criteria.</li>
          <li>Items must be unused, in original packaging, and with all tags attached to qualify for a refund.</li>
          <li>Refunds are processed within 3-5 business days after receiving the returned item.</li>
          <li>Customers are responsible for return shipping costs unless the item is defective or incorrect.</li>
        </ul>

        <h6>5. Warranty & Product Defects</h6>
        <ul>
          <li>Some products may come with a manufacturer’s warranty. Please check the product details for warranty information.</li>
          <li>If you receive a defective or damaged item, contact us within the same day of the delivery with photos of the issue for a replacement or refund.</li>
        </ul>

        <h6>6. Cancellations & Modifications</h6>
        <ul>
          <li>Orders can be canceled or modified within 24 hours of placement. After this period, the order will be processed.</li>
          <li>Contact our support team at greatwallarts@gmail.com for cancellations.</li>
        </ul>

        <h6>7. Contact Information</h6>
        <p>If you have any questions about our Product Policy, feel free to contact us:</p>
        <p>📧 Email: greatwallarts@gmail.com</p>
        <p>📞 Phone: +639876543221</p>
      </div>
      
      <!-- Modal Footer -->
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>





<div class="modal fade" id="termsConditionsModal" tabindex="-1" aria-labelledby="termsConditionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsConditionsModalLabel">Terms & Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <p><strong>Last Updated:</strong> February 23, 2025</p>

        <p>Welcome to <strong>Great Wall Arts</strong>! These Terms and Conditions govern your use of our website, services, and purchases. By accessing or using our website, you agree to abide by these terms.</p>

        <h6>1. General Terms</h6>
        <ul>
          <li>You must be at least 18 years old or have parental consent to use our services.</li>
          <li>We reserve the right to modify these terms at any time.</li>
          <li>Continued use of our website after modifications constitutes acceptance of the new terms.</li>
        </ul>

        <h6>2. Account Registration</h6>
        <ul>
          <li>Some features require account registration with accurate information.</li>
          <li>You are responsible for maintaining the confidentiality of your account details.</li>
        </ul>

        <h6>3. Orders & Payment</h6>
        <ul>
          <li>All prices are in Philippine Pesos (PHP) unless stated otherwise.</li>
          <li>We accept payments via PayPal, Credit/Debit Cards, and PayMongo.</li>
          <li>Orders are confirmed only after full payment is received.</li>
        </ul>

        <h6>4. Shipping & Delivery</h6>
        <ul>
          <li>We offer domestic and international shipping with various options.</li>
          <li>Delivery times vary based on location and courier.</li>
          <li>We are not responsible for delays due to customs, weather, or courier issues.</li>
        </ul>

        <h6>5. Returns & Refunds</h6>
        <ul>
          <li>Returns are accepted within 3-5 days of delivery under specific conditions.</li>
          <li>Refunds are processed within 3-5 business days after inspection.</li>
        </ul>

        <h6>6. Warranty & Product Defects</h6>
        <ul>
          <li>Some products may come with a manufacturer’s warranty.</li>
          <li>If you receive a defective item, contact us within 24 hours with photos.</li>
        </ul>

        <h6>7. Cancellations & Order Modifications</h6>
        <ul>
          <li>Orders can be canceled or modified within 24 hours of placement.</li>
        </ul>

        <h6>8. User Conduct & Restrictions</h6>
        <ul>
          <li>You agree not to use our website for illegal activities or fraudulent purposes.</li>
        </ul>

        <h6>9. Privacy Policy</h6>
        <p>We respect your privacy and collect personal information only for order processing and customer service.</p>

        <h6>10. Limitation of Liability</h6>
        <p>We are not liable for indirect or incidental damages arising from website use.</p>

        <h6>11. Governing Law</h6>
        <p>These terms are governed by the laws of the Philippines.</p>

        <h6>12. Contact Us</h6>
        <p>📧 Email: <strong>greatwallarts@gmail.com</strong></p>
        <p>📞 Phone: <strong>+639876543221</strong></p>
      </div>
      <div class="modal-footer">
      
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS (Required for Modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>