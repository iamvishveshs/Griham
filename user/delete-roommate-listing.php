<?php
require './check.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json"); // Set response type

    $accommodation_id = intval($_POST["accommodation_id"]);

    // Check if the accommodation exists
    $sql_check = "SELECT accommodation_id FROM roommate_accommodations WHERE accommodation_id = $accommodation_id";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) === 0) {
        http_response_code(404); // Not Found
        echo json_encode(["status" => "error", "message" => "Accommodation not found!"]);
        exit();
    }

    try {
        // Delete related amenities
        $sql_amenities = "DELETE FROM roommate_accommodation_amenities WHERE accommodation_id = $accommodation_id";
        mysqli_query($conn, $sql_amenities);

        // Fetch and delete related images from the folder
        $sql_fetch_images = "SELECT image_url FROM images WHERE entity_type = 'roommate_accommodation' AND entity_id = $accommodation_id";
        $result_fetch_images = mysqli_query($conn, $sql_fetch_images);

        $uploadDir = "../assets/uploads/roommate-accommodation/";

        if (mysqli_num_rows($result_fetch_images) > 0) {
            while ($row = mysqli_fetch_assoc($result_fetch_images)) {
                $imagePath = $uploadDir . $row['image_url'];

                // Check if the file exists and delete it
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the file
                }
            }
        }

        // Delete image records from the database
        $sql_images = "DELETE FROM images WHERE entity_type = 'roommate_accommodation' AND entity_id = $accommodation_id";
        mysqli_query($conn, $sql_images);

        // Delete entries from saved_services table
        $sql_saved_services = "DELETE FROM saved_services WHERE service_id = $accommodation_id AND service_type = 'roommate'";
        mysqli_query($conn, $sql_saved_services);

        // Delete the accommodation itself
        $sql_delete = "DELETE FROM roommate_accommodations WHERE accommodation_id = $accommodation_id";
        if (mysqli_query($conn, $sql_delete)) {
            http_response_code(200); // OK
            echo json_encode(["status" => "success", "message" => "Accommodation, related files, and saved services entries deleted successfully!"]);
        } else {
            throw new Exception("Error deleting accommodation: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
}
?>