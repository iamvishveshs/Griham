
<?php
session_start();
require("../database.php");
require './vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Invalid email format.";
        exit();
    }
    $otp = rand(100000, 999999);
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
    $query = "INSERT INTO user_otps (email, otp, expires_at) VALUES ('$email', '$otp', '$otp_expiry')
              ON DUPLICATE KEY UPDATE otp='$otp', expires_at='$otp_expiry'";
    mysqli_query($conn, $query);
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email';
        $mail->Password = 'your_email_password'; // Ensure this is stored securely
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('your_email', 'Griham');
        $mail->addAddress($email);
        $mail->Subject = "Reset Password Griham";
        $mail->isHTML(true);
        $mail->Body = '<div style="background-color: #f4f4f4; padding: 20px;">
                <table style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 10px; text-align: center; font-family: Arial, sans-serif;">
                    <tr>
                        <td>
                            <h2 style="color: #2b55ff;">Reset Password</h2>
                            <p style="color: #333; font-size: 16px;">Your OTP for password Change is: <p style="font-size: 2rem;">'.$otp.'</p></p>
                            <p style="color: #666; font-size: 14px; margin-top: 20px;">This OTP will expire in 5 minutes.</p>
                        </td>
                    </tr>
                </table>
            </div>';
        $mail->send();

        http_response_code(200);
        echo "OTP sent successfully.";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Failed to send OTP. {$mail->ErrorInfo}";
    }
}
else
{
    http_response_code(500);
        echo "Failed to send Mail. {$mail->ErrorInfo}";
}
?>
