<?php
require("./database.php");
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Invalid email format.";
        exit();
    }
    $query = "SELECT user_id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        http_response_code(409);
        echo "Email already registered.";
    } else {
        http_response_code(200);
        echo "Email is available.";
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email_password"])) {
    $email = mysqli_real_escape_string($conn, trim($_POST["email_password"]));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Invalid email format.";
        exit();
    }
    $query = "SELECT user_id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 0) {
        http_response_code(409);
        echo "Email not registered to any account.";
    } else {
        http_response_code(200);
        echo "proceed";
    }
}
?>
