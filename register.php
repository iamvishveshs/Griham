<?php
session_start();
// Preserve token if available
if (isset($_GET['token'])) {
    $token = $_GET['token'];
}
require_once("./database.php"); // Database connection file
// Determine role based on session token
$default_role = "tenant";
if (isset($token) && base64_decode($token) === "owner") {
    $default_role = "owner";
} else if (isset($_POST['role']) && $_POST['role'] === "owner") {
    $default_role = "owner";
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    // Ensure OTP is verified
    if (!isset($_SESSION["otp_verified"]) || $_SESSION["otp_verified"] !== true) {
        $_SESSION['error'] = "You must verify OTP before registering.";
        header("Location: register.php");
        exit();
    }
    // Retrieve and sanitize user data
    $name = mysqli_real_escape_string($conn, trim($_POST["name"]));
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $phone = mysqli_real_escape_string($conn, trim($_POST["phone"]));
    $password = mysqli_real_escape_string($conn, trim($_POST["password"]));
    $bio = mysqli_real_escape_string($conn, trim($_POST["bio"]));
    $profile_status = "active"; // Default profile status
  // Secret Key for HMAC Hashing
  $secret_key = "thegrih";
    // Hash the password
    $hashed_password = hash_hmac("sha256", $password, $secret_key);
    // Check if email or phone already exists
    $check_query = "SELECT user_id FROM users WHERE email = '$email' OR phone = '$phone'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Email or phone number already exists.";
        header("Location: register.php");
        exit();
    }
    // Insert user without address
    $user_insert_query = "INSERT INTO users (name, email, phone, password, role, bio, profile_status)
                          VALUES ('$name', '$email', '$phone', '$hashed_password', '$default_role', '$bio', '$profile_status')";
    if (mysqli_query($conn, $user_insert_query)) {
        $_SESSION["success"] = "Registration successful! Please log in.";
        require("./libs/accoun_verification_success.php");
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "An error occurred. Please try again.";
        header("Location: register.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Registration </title>
    <?php
    require("./style-files.php");
    ?>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <section class="form-section">
        <p class="form-heading">Registration</p>
        <div class="input-box">
            <?php require("./show-message.php"); ?>
        </div>
        <form id="otpForm" class="form ">
            <div class="input-box relative-div">
                <label>Email Address</label>
                <input type="email" id="email" name="email" placeholder="johndoe@gmail.com" required>
                <span class="right-top-btn" style="display: none;" id="sendOtpBtn">Send OTP</span>
                <small id="emailMessage"></small>
            </div>
            <div class="input-box" id="otpSection" style="display: none;">
                <label>Enter OTP</label>
                <input type="text" id="otpInput" name="otp" placeholder="Enter OTP" required>
                <small id="otpMessage"></small>
                <button type="button" id="verifyOtpBtn">Verify OTP</button>
            </div>
        </form>
        <form id="registerForm" action="register.php" method="POST" class="form" style="display: none;">
            <?php if (isset($default_role) && $default_role === "owner") { ?>
                <input type="hidden" name="role" value="owner">
            <?php } ?>
            <div class="input-box">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="John Doe" required>
            </div>
            <div class="column">
                <div class="input-box">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="xxxxxxxxxx" pattern="^[1-9][0-9]{9}$"
                        title="Enter a valid phone number (10 digits)" required>
                </div>
                <div class="input-box relative-div">
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="Password"
                        pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                        title="Password must be at least 8 characters, contain an uppercase letter, a lowercase letter, a number, and a special character."
                        required />
                    <span class="right-top-btn" id="togglePassword">Show</span>
                </div>
            </div>
            <div class="input-box">
                <label>Bio</label>
                <textarea name="bio" placeholder="Tell us about yourself..." rows="3"></textarea>
            </div>
            <input type="hidden" name="email" id="hiddenEmail">
            <button type="submit" name="register">Register</button>
            <div class="login-option">
                <span>Already have an account? <a href="login.php">Log in here</a></span>
            </div>
        </form>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                let emailTimer;
                let otpCountdown;
                // Debounce email check (Triggers after 2 sec of inactivity)
                $("#email").on("input", function() {
                    clearTimeout(emailTimer);
                    $("#emailMessage").text("Checking email...").css("color", "blue");
                    $("#sendOtpBtn").prop("disabled", true);
                    emailTimer = setTimeout(function() {
                        checkEmailAvailability();
                    }, 2000);
                });
                function checkEmailAvailability() {
                    var email = $("#email").val();
                    var emailMessage = $("#emailMessage");
                    var sendOtpBtn = $("#sendOtpBtn");
                    if (email.length === 0) {
                        emailMessage.text("");
                        sendOtpBtn.hide();
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "check_email.php",
                        data: {
                            email: email
                        },
                        success: function() {
                            emailMessage.css("color", "green").text("Email is available.");
                            sendOtpBtn.show();
                        },
                        error: function(xhr) {
                            if (xhr.status === 409) {
                                emailMessage.css("color", "red").text("This email is already registered.");
                                sendOtpBtn.hide();
                            }
                        }
                    });
                }
                $("#sendOtpBtn").click(function() {
                    let email = $("#email").val();
                    $("#emailMessage").text("Sending OTP...").css("color", "blue");
                    $.ajax({
                        type: "POST",
                        url: "./libs/send_email_otp.php",
                        data: {
                            email: email
                        },
                        success: function() {
                            $("#otpSection").show();
                            $("#emailMessage").text("OTP sent! Check your email.").css("color", "green");
                        },
                        error: function() {
                            $("#emailMessage").text("Error sending OTP. Try again.").css("color", "red");
                        }
                    });
                });
                function startCountdown() {
                    clearInterval(otpCountdown);
                    var countdown = 60;
                    var countdownElement = $("#countdown");
                    var resendOtpBtn = $("#resendOtpBtn");
                    resendOtpBtn.prop("disabled", true);
                    countdownElement.text(countdown);
                    otpCountdown = setInterval(function() {
                        countdown--;
                        countdownElement.text(countdown);
                        if (countdown <= 0) {
                            clearInterval(otpCountdown);
                            resendOtpBtn.prop("disabled", false);
                            countdownElement.text("0");
                        }
                    }, 1000);
                }
                $("#verifyOtpBtn").click(function() {
                    let email = $("#email").val();
                    let otp = $("#otpInput").val();
                    $("#otpMessage").text("Verifying OTP...").css("color", "blue");
                    $.ajax({
                        type: "POST",
                        url: "verify_otp.php",
                        data: {
                            email: email,
                            otp: otp
                        },
                        success: function() {
                            $("#otpMessage").text("OTP Verified! Proceed to registration.").css("color", "green");
                            $("#otpForm").hide();
                            $("#hiddenEmail").val(email);
                            $("#registerForm").show();
                        },
                        error: function() {
                            $("#otpMessage").text("Invalid OTP. Try again.").css("color", "red");
                        }
                    });
                });
            });
        </script>
    </section>
    <?php require("./footer.php"); ?>
    <script src="./assets/js/script.js"></script>
    <script>
        const passwordField = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");
        togglePassword.addEventListener("click", function() {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.textContent = "Hide"; // Change icon to hide
            } else {
                passwordField.type = "password";
                togglePassword.textContent = "Show"; // Change icon to show
            }
        });
    </script>
</body>
</html>