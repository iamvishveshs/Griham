<?php
require("./database.php");
require 'vendor/autoload.php'; // Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if (!isset($email)) {
    header("Location: ../login.php");
    exit();
}
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
    $mail->Subject = " Account Verification Successful!";
    $mail->isHTML(true);
    $mail->Body = '<div style="background-color: #f4f4f4; padding: 20px;">
            <table style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 10px; text-align: center; font-family: Arial, sans-serif;">
                <tr>
                    <td>
                        <h2 style="color: #2b55ff;">🎉 Congratulations ' . $name . '!</h2>
                        <p style="color: #333; font-size: 16px;">Your account has been successfully verified. You can now log in and enjoy our services.</p>
                        <p style="color: #666; font-size: 14px; margin-top: 20px;">If you did not request this, please ignore this email.</p>
                    </td>
                </tr>
            </table>
        </div>';
    $mail->send();
    $_SESSION['success'] = "Account Created Successfully";
    header("Location: ./login.php");
    exit();
} catch (Exception $e) {
    header("Location: ./login.php");
    exit();
}
