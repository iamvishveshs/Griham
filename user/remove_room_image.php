<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['imageName']) && isset($_POST['accommodation_id'])) {
    $imageName = $_POST['imageName'];
    $accommodation_id = $_POST['accommodation_id'];

    $filePath = "../assets/uploads/roommate-accommodation/" . $imageName;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {

            $sql = "DELETE FROM images WHERE image_url = '" . mysqli_real_escape_string($conn, $imageName) . "' AND entity_type = 'roommate_accommodation' AND entity_id = " . (int)$accommodation_id;

            if (mysqli_query($conn, $sql)) {
                echo "success";
            } else {
                echo "Database deletion failed: " . mysqli_error($conn);
            }
        } else {
            echo "File deletion failed.";
        }
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid request.";
}

mysqli_close($conn);
?>