<?php
require("./check.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['meal_id'])) {
    $service_id = intval(base64_decode($_POST['meal_id']));
    $user_id = $_SESSION['user_id'];
    $upload_dir = "../assets/uploads/meal-services/";
    //Fetch main image for deletion
    $query = "SELECT main_image FROM meal_services WHERE service_id = '$service_id' AND user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Delete main image
        if (!empty($row['main_image'])) {
            $file_path = $upload_dir . $row['main_image'];
            if (file_exists($file_path)) unlink($file_path);
        }
        // Fetch and delete associated images
        $image_query = "SELECT image_url FROM images WHERE entity_type = 'meal_service' AND entity_id = '$service_id'";
        $image_result = mysqli_query($conn, $image_query);
        while ($image_row = mysqli_fetch_assoc($image_result)) {
            $image_path = $upload_dir . $image_row['image_url'];
            if (file_exists($image_path)) unlink($image_path);
        }
        // Delete images from database
        $delete_images_query = "DELETE FROM images WHERE entity_type = 'meal_service' AND entity_id = '$service_id'";
        mysqli_query($conn, $delete_images_query);
        //  Delete related amenities
        $delete_amenities_query = "DELETE FROM meal_services_amenities WHERE meal_service_id = '$service_id'";
        mysqli_query($conn, $delete_amenities_query);
        // Delete entries from saved_services table
        $sql_saved_services = "DELETE FROM saved_services WHERE service_id = $service_id AND service_type = 'meal'";
        mysqli_query($conn, $sql_saved_services);
        // Delete the accommodation service itself
        $delete_service_query = "DELETE FROM meal_services WHERE service_id = '$service_id' AND user_id = '$user_id'";
        if (mysqli_query($conn, $delete_service_query)) {
            echo json_encode(["success" => true]);
            exit();
        }
    }
}
echo json_encode(["success" => false, "message" => "Failed to delete accommodation service"]);
?>