<?php
session_start();
require("./database.php");

$user_id = $_SESSION['user_id'] ?? 0;
$service_id = $_POST['service_id'] ?? 0;
$service_type = $_POST['service_type'] ?? '';

// Ensure data is valid
if ($user_id == 0 || $service_id == 0) {
    echo json_encode(["status" => "error"]);
    exit();
}

// Check if service is already saved
$check_query = "SELECT id FROM saved_services WHERE user_id = ? AND service_id = ? AND service_type = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "iis", $user_id, $service_id, $service_type);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // If already saved, remove it
    $delete_query = "DELETE FROM saved_services WHERE user_id = ? AND service_id = ? AND service_type = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $service_id, $service_type);
    mysqli_stmt_execute($stmt);
    echo json_encode(["status" => "removed"]);
} else {
    // If not saved, add it
    $insert_query = "INSERT INTO saved_services (user_id, service_id, service_type) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $service_id, $service_type);
    mysqli_stmt_execute($stmt);
    echo json_encode(["status" => "saved"]);
}
?>
