<?php



session_start();

if (!isset($_SESSION['email'])) {

    header("Location: ../login.php");

    exit();

}

require("./database.php");

require 'vendor/autoload.php'; // Load PHPMailer



use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;







$email = $_SESSION['email'];

$name = $_SESSION['name'];



$otp = rand(100000, 999999);

$otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));



// Store OTP in database

$insert_otp = "INSERT INTO user_otps (email, otp, expires_at) 

               VALUES ('$email', '$otp', '$otp_expiry')

               ON DUPLICATE KEY UPDATE otp='$otp', expires_at='$otp_expiry'";

mysqli_query($conn, $insert_otp);



// Store OTP send time in session

$_SESSION['otp_sent_time'] = time();



$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'your_email';

    $mail->Password = 'your_email_password';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;



    $mail->setFrom('your_email', 'Griham');

    $mail->addAddress($email);

    $mail->Subject = "Your OTP for Verification";

    $mail->Body = "Hello $name,\n\nYour OTP for email verification is: $otp\n\nThis OTP will expire in 5 minutes.";



    $mail->send();

    $_SESSION['success'] = "OTP sent to your email.";

    header("Location: ../verify_email.php");

    exit();

} catch (Exception $e) {

    $_SESSION['error'] = "Failed to send OTP: {$mail->ErrorInfo}";

    header("Location: ../login.php");

    exit();

}

?>

