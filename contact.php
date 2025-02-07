<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Great Wall Arts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #fdfdfd;
            font-family: 'Arial', sans-serif;
            padding-top: 85px;
        }
        .contact-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .contact-header {
            background: transparent;
            color: black;
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: 1.5rem;
        }
        .btn-custom {
            background-color: #F7F5BC;
            border: none;
            color: black;
            transition: all 0.3s ease-in-out;
        }
        .btn-custom:hover {
            background-color: #E8E337;
        }
        .form-control:focus {
            box-shadow: 0 0 5px rgba(255, 126, 95, 0.5);
        }
        .social-icons a {
            margin-right: 10px;
            font-size: 1.5rem;
            color: #6c757d;
            transition: color 0.3s ease-in-out;
        }
        .social-icons a:hover {
            color: #ff7e5f;
        }
        .newsletter-section {
            background: #ff7e5f;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .newsletter-section input {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            margin-right: 0.5rem;
        }
        .newsletter-section button {
            background-color: #feb47b;
            border: none;
            border-radius: 50px;
            color: white;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease-in-out;
        }
        .newsletter-section button:hover {
            background-color: #ffffff;
            color: #ff7e5f;
        }
    </style>
</head>
<body>
<?php 
     include "navbar.php"; 
     ?>
    <div class="container mt-4">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-lg-6">
                <div class="contact-container p-4">
                    <div class="contact-header">Get in Touch</div>
                    <div class="p-4">
                        <iframe class="w-100 mb-3 rounded" height="250" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.690370390059!2d121.08091356862819!3d14.576299946551895!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c7e733d0dd8f%3A0x484e41941f8f66dc!2sSolen%20Building!5e1!3m2!1sfil!2sph!4v1726670394140!5m2!1sfil!2sph" loading="lazy"></iframe>
                        <h5>Address</h5>
                        <p>Solen Building C. Raymundo F corner F. Legaspi, Maybunga Pasig</p>
                        <h5>Call Us</h5>
                        <p><a href="tel:+639876543221" class="text-decoration-none text-dark"><i class="bi bi-telephone-fill me-2"></i>+639876543221</a></p>
                        <h5>Email</h5>
                        <p><a href="mailto:GWAexample@gmail.com" class="text-decoration-none text-dark"><i class="bi bi-envelope-fill me-2"></i>GWAexample@gmail.com</a></p>
                        <h5>Follow Us</h5>
                        <div class="social-icons">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="contact-container p-4">
                    <div class="contact-header">Send Us a Message</div>
                    <form class="p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Your name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Your email">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" placeholder="Subject">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" placeholder="Write your message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
