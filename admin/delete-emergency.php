<?php
require('./check.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
    $encoded_service_id = $_POST['service_id'];
    // Decode URL-encoded value
    $decoded_base64 = urldecode($encoded_service_id);
    // Decode Base64 to get the original service_id
    $service_id = base64_decode($decoded_base64);
    if (!is_numeric($service_id)) {
        die("Invalid token");
    }
    // Perform the delete query
    $query = "DELETE FROM emergency_services WHERE service_id = $service_id";
    if (mysqli_query($conn, $query)) {
        // Send success status code
        http_response_code(200); // OK
        echo "success";
    } else {
        // Send error status code
        http_response_code(500); // Internal Server Error
        echo "error";
    }
} else {
    // Invalid method
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method";
}
