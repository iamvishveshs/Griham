<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image'], $_POST['service_id'])) {

    $listing_id = intval($_POST['service_id']);
    $image_name = mysqli_real_escape_string($conn, $_POST['image']);
    $user_id = $_SESSION['user_id'];

    // ✅ Check if the image exists in the images table
    $query = "SELECT image_url FROM images WHERE entity_id = '$listing_id' AND entity_type = 'accommodation_service' AND image_url = '$image_name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // ✅ Delete the image from the database
        $delete_query = "DELETE FROM images WHERE entity_id = '$listing_id' AND entity_type = 'accommodation_service' AND image_url = '$image_name'";
        if (mysqli_query($conn, $delete_query)) {
            // ✅ Delete the file from the server
            $file_path = "../assets/uploads/accommodation-images/" . $image_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo json_encode(["success" => true]);
            exit();
        }
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image'], $_POST['meal_service_id'])) {
    $service_id = intval($_POST['meal_service_id']);
    $image_name = mysqli_real_escape_string($conn, $_POST['image']);

    // ✅ Check if the image exists in the images table
    $query = "SELECT image_url FROM images WHERE entity_id = '$service_id' AND entity_type = 'meal_service' AND image_url = '$image_name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // ✅ Delete the image from the database
        $delete_query = "DELETE FROM images WHERE entity_id = '$service_id' AND entity_type = 'meal_service' AND image_url = '$image_name'";
        if (mysqli_query($conn, $delete_query)) {
            // ✅ Delete the file from the server
            $file_path = "../assets/uploads/meal-services/" . $image_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo json_encode(["success" => true]);
            exit();
        }
    }
}else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image'], $_POST['gym_service_id'])) {
    $service_id = intval($_POST['gym_service_id']);
    $image_name = mysqli_real_escape_string($conn, $_POST['image']);

    // ✅ Check if the image exists in the images table
    $query = "SELECT image_url FROM images WHERE entity_id = '$service_id' AND entity_type = 'gym_service' AND image_url = '$image_name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // ✅ Delete the image from the database
        $delete_query = "DELETE FROM images WHERE entity_id = '$service_id' AND entity_type = 'gym_service' AND image_url = '$image_name'";
        if (mysqli_query($conn, $delete_query)) {
            // ✅ Delete the file from the server
            $file_path = "../assets/uploads/gym-services/" . $image_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo json_encode(["success" => true]);
            exit();
        }
    }
}

else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image'], $_POST['laundry_service_id'])) {
    $service_id = intval($_POST['laundry_service_id']);
    $image_name = mysqli_real_escape_string($conn, $_POST['image']);

    // ✅ Check if the image exists in the images table
    $query = "SELECT image_url FROM images WHERE entity_id = '$service_id' AND entity_type = 'laundry_service' AND image_url = '$image_name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // ✅ Delete the image from the database
        $delete_query = "DELETE FROM images WHERE entity_id = '$service_id' AND entity_type = 'laundry_service' AND image_url = '$image_name'";
        if (mysqli_query($conn, $delete_query)) {
            // ✅ Delete the file from the server
            $file_path = "../assets/uploads/laundry-services/" . $image_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo json_encode(["success" => true]);
            exit();
        }
    }
}



// ✅ Return failure response if image was not found or deletion failed
echo json_encode(["success" => false, "message" => "Image not found or could not be deleted."]);
exit();
?>