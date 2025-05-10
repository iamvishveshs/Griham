<?php


require("./check.php");

if (isset($_GET['token'])) {
    $service_id = base64_decode($_GET['token'], true);

    if ($service_id === false || !ctype_digit($service_id)) {
        $_SESSION['error'] = "Invalid Request";
    header("Location: ./support-messages.php");
    exit();
    }

    if (mysqli_query($conn, "DELETE FROM `support_requests` WHERE ticket_id='$service_id'")) {
        $_SESSION['success'] = "Successfully deleted.";
    header("Location: ./support-messages.php");
    exit();
    } else {
        $_SESSION['error'] = "Some Error Occured. Try again Later.";
    header("Location: ./support-messages.php");
    exit();
    }
}else {
    $_SESSION['error'] = "Can't access delete page without any message to delete.";
header("Location: ./support-messages.php");
exit();
}
?>