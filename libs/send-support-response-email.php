

<?php

session_start();

require("../database.php");

require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["address"],$_POST['message_id'],$_POST['name'],$_POST['description'],$_POST['response']) ) {

    $email = mysqli_real_escape_string($conn, trim($_POST["address"]));

    $ticket_id = mysqli_real_escape_string($conn, trim($_POST["message_id"]));

    $name = mysqli_real_escape_string($conn, trim($_POST["name"]));

    $description = mysqli_real_escape_string($conn, trim($_POST["description"]));

    $response = trim($_POST["response"]);



    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        http_response_code(400);

        echo "Invalid email format.";

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

        $mail->Subject = "Griham: Response to your ticket";

        $mail->isHTML(true);

        $mail->Body = '<div style="background-color: #f4f4f4; padding: 20px;">

                <table style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 10px; text-align: center; font-family: Arial, sans-serif;">

                    <tr>

                        <td>

                            <h2 style="color: #2b55ff;">Ticket Id ('.$ticket_id.')</h2>

                            

                            <p style="color:rgb(43, 128, 255); font-size: 18px;  margin-top: 20px;"><b>Your Ticket :</b></p>

                            <p style="color: #333; font-size: 14px;"> '.$description.'</p>

                            <p style="color: rgb(43, 128, 255); font-size: 18px; margin-top: 30px;"><b>Response to your ticket:</b><br></p>

                            <p style="color: #333; font-size: 14px;">'.htmlspecialchars($response).'</p>

                            <p style="color: #666; font-size: 12px;">Thankyou for your patience</p>

                            

                        </td>

                    </tr>

                </table>

            </div>';



        $mail->send();

        

        http_response_code(200);

        echo "Mail sent successfully.";

    } catch (Exception $e) {

        http_response_code(500);

        echo "Failed to send Mail. {$mail->ErrorInfo}";

    }

}

else{

    http_response_code(500);

        echo "Failed to send Mail";

}

?>

