<?php
session_start();
require_once("./database.php");

// Function to redirect users based on role
function redirect()
{
  if ($_SESSION['role'] == "tenant") {
    header("Location: ./user/home.php");
    exit();
  } else if ($_SESSION['role'] == "owner") {
    header("Location: ./owner/home.php");
    exit();
  } else {
    header("Location: ./admin/home.php");
    exit();
  }
}

// If user is already logged in & verified, redirect
if (isset($_SESSION['user_id'])) {
  redirect();
}

if (isset($_POST['login'])) {

  // Retrieve and sanitize form data
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  // Secret Key for HMAC Hashing
  $secret_key = "thegrih";

  // Regex patterns
  $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
  $password_regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

  // Initialize error messages
  $_SESSION['error_message'] = [];

  // Validate email format
  if (!preg_match($email_regex, $email)) {
    $_SESSION['error_message'][] = "• Invalid email format.";
  }

  // Validate password strength
  if (!preg_match($password_regex, $password)) {
    $_SESSION['error_message'][] = "• Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.";
  }

  // If there are any errors, redirect back to login page
  if (!empty($_SESSION['error_message'])) {
    $_SESSION['error'] = implode("<br>", $_SESSION['error_message']); // Concatenate errors
    header("Location: ./login.php");
    exit();
  }

  // Sanitize email
  $email = mysqli_real_escape_string($conn, $email);

  // Hash the password using SHA256 with HMAC
  $hashed_password = hash_hmac("sha256", $password, $secret_key);

  // Check if user exists
  $user_query = "SELECT user_id, name, email, password, role, profile_pic FROM users WHERE email = '$email' AND password = '$hashed_password'";
  $user_result = mysqli_query($conn, $user_query);

  if (mysqli_num_rows($user_result) == 1) {
    $user_data = mysqli_fetch_assoc($user_result);

    // Store user data in session
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['name'] = $user_data['name'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['role'] = $user_data['role'];
    $_SESSION['nav_profile_pic'] = $row['profile_pic'] ?? "";

    // Update last login time
    $user_id = $user_data['user_id'];
    $update_last_login = "UPDATE users SET last_login = NOW() WHERE user_id = $user_id";
    mysqli_query($conn, $update_last_login);
    redirect();
  } else {
    $_SESSION['error'] = "Invalid email or password.";
    header("Location: ./login.php");
    exit();
  }
}

mysqli_close($conn);
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Login </title>
  <?php
  require("./style-files.php");
  ?>
</head>

<body>
  <?php require("./navbar.php"); ?>
  <section class="form-section">
    <p class="form-heading">Login</p>
    <form action="login.php" method="POST" class="form">
      <div class="input-box">
        <?php require("./show-message.php"); ?>

      </div>
      <div class="input-box">
        <label>Email Address</label>
        <input type="email" name="email" placeholder='"johndoe@gmail.com"'
          attern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
          title="Enter a valid email (e.g., user@example.com)" required>
      </div>
      <div class="input-box relative-div">
        <label>Password</label>
        <input type="password" id="password" name="password" placeholder="password"
          pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
          title="Password must be at least 8 characters, contain an uppercase letter, a lowercase letter, a number, and a special character."
          required />

        <span id="togglePassword" class="right-top-btn">Show</span>
      </div>
      </div>

      <button type="submit" name="login">Login</button>
      <div class="login-option">
        <span>New here? <a href="./register.php">Create an account</a> now!</span>
        <br>
        <span>Forgot Password? <a href="./reset-password.php"> Change Now!</a></span>
      </div>
    </form>
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