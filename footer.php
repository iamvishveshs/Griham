<!-- Footer Section -->
<footer class="footer">
    <div class="container">
        <div class="footer-row">

            <!-- Column 1: Company -->
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="./about.php">About Us</a></li>
                    <li><a href="./index.php#services">Our Services</a></li>
                    <li><a href="./privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="./terms-and-conditions.php">Terms & Conditions</a></li>
                </ul>
            </div>

            <!-- Column 2: Get Help -->
            <div class="footer-col">
                <h4>Get Help</h4>
                <ul>
                    <li><a href="./index.php#faq">FAQ</a></li>
                    <li><a href="./user/support.php">Support</a></li>
                </ul>
            </div>

            <!-- Column 3: Sign Up -->
            <div class="footer-col">
                <h4>Sign Up</h4>
                <ul>
                    <li><?php $token = base64_encode("owner");
                    echo '<a href="./register.php?token=' . urlencode($token) . '">Sign Up as Property Owner</a>'; ?></li>
                    <li><a href="./register.php">Sign Up as User</a></li>
                </ul>
            </div>

            <!-- Column 4: Follow Us -->
            <div class="footer-col">
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Section -->
    <div class="copyright">
        <p>&copy; <?php echo date("Y"); ?> Griham. All rights reserved.</p>
    </div>
</footer>
