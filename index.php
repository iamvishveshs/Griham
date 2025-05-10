<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Griham</title>

    <!-- Preload: Load critical assets early -->
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preload" as="image" href="./assets/images/rent2.jpg">
    <link rel="preload" as="script" href="./assets/js/script.js">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="./assets/css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/home.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>


<body>

    <?php require("./navbar.php"); ?>
    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-container">
            <div class="hero-text">
                <h1>Find Your Perfect Stay</h1>
                <p>Find a Home, Not Just a Place to Stay</p>
                <p>Griham simplifies your search for accommodation and essential services. Discover verified listings,
                    find compatible roommates, and access food, laundry, and emergency services—all in one place.</p>
                <a href="./register.php" class="btn">Sign Up Now</a>
            </div>
            <div class="hero-image">
                <img src="./assets/images/rent2.jpg" alt="Modern luxury apartment">
            </div>
        </div>
    </div>


    <!-- What We Do Section -->
    <div class="section-heading">
        <h2>What We Do</h2>
        <p>We provide comprehensive solutions for students and professionals</p>
    </div>

    <section class="services">
        <div class="service-card">
            <div class="service-icon">
                <img src="./assets/images/erf.jpeg" alt="Room Finder">
            </div>
            <h3>Effortless Room Finder</h3>
            <p>Find Your Perfect Living Space</p>
            <ul>
                <li>✔ Comprehensive property listings</li>
                <li>✔ Advanced search filters</li>
                <li>✔ Virtual tour options</li>
                <li>✔ Instant property contact</li>
            </ul>

        </div>

        <div class="service-card">
            <div class="service-icon">
                <img src="./assets/images/dhaba.avif" alt="Tiffin Service">
            </div>
            <h3>Homestyle Tiffin Delivery</h3>
            <p>Delicious Meals, Doorstep Convenience</p>
            <ul>
                <li>✔ Multiple cuisine options</li>
                <li>✔ Customizable meal plans</li>
                <li>✔ Healthy, fresh ingredients</li>
                <li>✔ Flexible subscriptions</li>
            </ul>
        </div>

        <div class="service-card">
            <div class="service-icon">
                <img src="./assets/images/laundry.avif" alt="Laundry Service">
            </div>
            <h3>Professional Laundry Solutions</h3>
            <p>Clean, Fresh, Convenient</p>
            <ul>
                <li>✔ Quick turnaround times</li>
                <li>✔ Eco-friendly cleaning</li>
                <li>✔ Stain removal guarantee</li>
                <li>✔ Pickup & delivery</li>
            </ul>
        </div>
    </section>



    <div class="section-heading">
        <h2>Our Sevices</h2>
    </div>

    <section class="service-container">
        <div class="row">
            <div class="service">
                <div class="service-image"><img src="./assets/images/pg.jpeg" alt="PG & Room Services"></div>
                <p><a href="./browse-listing.php" class="service-btn block btn-blue">Get PG Or Room Services</a></p>
            </div>
            <div class="service">
                <div class="service-image"><img src="./assets/images/dhaba.avif" alt="Laundry & Gym Services"></div>
                <p><a href="./browse-meal-services.php" class="service-btn block btn-orange">Find Tiffin Or Dhaba Services</a></p>

            </div>
            <div class="service">
                <div class="service-image"><img src="./assets/images/gym.avif" alt="Laundry & Gym Services"></div>
                <p><a href="./browse-gym-services.php" class="service-btn block btn-cyan">Find Gym Services</a></p>


            </div>

        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">What Our Users Say</h2>
            <div class="testimonials-grid">

                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="./assets/images/user.png" alt="User">
                        <div>
                            <h4>Sachin Sharma</h4>
                            <p>Student</p>
                        </div>
                    </div>
                    <p class="testimonial-text">"Found my perfect PG through this platform. The verification process
                        made me feel safe and secure."</p>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="./assets/images/user.png" alt="User">
                        <div>
                            <h4>Vivek Arya</h4>
                            <p>Professional</p>
                        </div>
                    </div>
                    <p class="testimonial-text">"The tiffin service has been a lifesaver. Healthy, tasty meals delivered
                        right on time."</p>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="./assets/images/user.png" alt="User">
                        <div>
                            <h4>Aryanshi</h4>
                            <p>Working Professional</p>
                        </div>
                    </div>
                    <p class="testimonial-text">"Convenient laundry service and access to great gym facilities. Makes
                        life so much easier!"</p>
                </div>

            </div>
        </div>
    </section>


    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="container">
            <h2 class="faq-title">Frequently Asked Questions</h2>
            <div class="faq-container">

                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How do I avail any service?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <p class="faq-answer">Browse through our listings, select your preferred service, and click on
                        "<b>Call</b>". Contact to the owner directly.</p>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What food service options are available?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <p class="faq-answer">Platform offer various services such as Dhabas, PGs and Cafes options from
                        local food service providers.</p>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Are all services available in my area?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <p class="faq-answer">Enter your City on our listing page to check service availability in your area (Available after login).
                    </p>
                </div>

            </div>
        </div>
    </section>
    <?php require("./footer.php"); ?>
    <script src="./assets/js/script.js"></script>
    <script>
        // Select all FAQ items
        const faqItems = document.querySelectorAll(".faq-item");

        faqItems.forEach((item) => {
            item.addEventListener("click", () => {
                // Close all other FAQs before opening a new one
                faqItems.forEach((faq) => {
                    if (faq !== item) {
                        faq.classList.remove("active");
                    }
                });

                // Toggle the clicked FAQ
                item.classList.toggle("active");
            });
        });
    </script>

</body>

</html>




<!-- Footer Section ENDS -->