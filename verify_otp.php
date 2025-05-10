<?php
session_start();
require("./database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["otp"])) {
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $otp = mysqli_real_escape_string($conn, trim($_POST["otp"]));

    if (!preg_match("/^\d{6}$/", $otp)) {
        http_response_code(400);
        echo "Invalid OTP format.";
        exit();
    }

    // Set MySQL time zone to match the server
    mysqli_query($conn, "SET time_zone = '+00:00'");

    // Check if OTP is correct and not expired
    $query = "SELECT * FROM user_otps 
              WHERE email = '$email' 
              AND otp = '$otp' 
              AND expires_at >= NOW()";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION["otp_verified"] = true; // ✅ Store OTP verification flag
        http_response_code(200);
        echo "OTP Verified!";
    } else {
        $_SESSION["otp_verified"] = false; // ❌ Prevent registration
        http_response_code(401);
        echo "Invalid or expired OTP.";
    }
}
?>
