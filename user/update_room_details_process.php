<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accommodation_id'])) {
    // Sanitize and validate input data
    $accommodation_id = isset($_POST['accommodation_id']) ? intval($_POST['accommodation_id']) : 0;
    $apartment_name = mysqli_real_escape_string($conn, $_POST['apartment_name']);
    $rent_amount = floatval($_POST['rent_amount']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $preferred_gender = mysqli_real_escape_string($conn, $_POST['preferred_gender']);
    $lease_duration = mysqli_real_escape_string($conn, $_POST['lease_duration']);
    $guest_policy = mysqli_real_escape_string($conn, $_POST['guest_policy']);
    $furnishing_status = mysqli_real_escape_string($conn, $_POST['furnishing_status']);
    $parking = mysqli_real_escape_string($conn, $_POST['parking']);
    $smoking = mysqli_real_escape_string($conn, $_POST['smoking']);
    $drinking = mysqli_real_escape_string($conn, $_POST['drinking']);
    $pets = mysqli_real_escape_string($conn, $_POST['pets']);
    $dietary_preference = mysqli_real_escape_string($conn, $_POST['dietary_preference']);
    $daily_schedule = mysqli_real_escape_string($conn, $_POST['daily_schedule']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $village = mysqli_real_escape_string($conn, $_POST['village']);
    $po = mysqli_real_escape_string($conn, $_POST['po']);
    $tehsil = mysqli_real_escape_string($conn, $_POST['tehsil']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $description = mysqli_real_escape_string($conn, trim($_POST["description"]));
    $dob = mysqli_real_escape_string($conn, trim($_POST["dob"]));
    $address_id = isset($_POST["address_id"]) ? intval($_POST["address_id"]) : null;
    $amenities_json = isset($_POST["amenitiesHidden"]) ? $_POST["amenitiesHidden"] : [];


    // Store New Address if Needed
   
        $city = mysqli_real_escape_string($conn, strtolower($_POST["city"])); // Lowercase
        $village = mysqli_real_escape_string($conn, strtolower($_POST["village"])); // Lowercase
        $po = mysqli_real_escape_string($conn, strtolower($_POST["po"])); // Lowercase
        $tehsil = mysqli_real_escape_string($conn, strtolower($_POST["tehsil"])); // Lowercase
        $district = mysqli_real_escape_string($conn, strtolower($_POST["district"])); // Lowercase
        $state = mysqli_real_escape_string($conn, strtolower($_POST["state"])); // Lowercase
        $pincode = mysqli_real_escape_string($conn, $_POST["pincode"]);

        // Check if address already exists (case-insensitive)
        $sql_check = "SELECT address_id FROM addresses WHERE LOWER(city) = '" . $city . "' AND LOWER(village) = '" . $village . "' AND LOWER(po) = '" . $po . "' AND LOWER(tehsil) = '" . $tehsil . "' AND LOWER(district) = '" . $district . "' AND LOWER(state) = '" . $state . "' AND pincode = '" . $pincode . "'";
        $result_check = mysqli_query($conn, $sql_check);
        $existing_address = mysqli_fetch_assoc($result_check);

        if ($existing_address) {
            $address_id = $existing_address['address_id'];
        } else {
            $sql_insert = "INSERT INTO addresses (city, village, po, tehsil, district, state, pincode) VALUES ('" . $city . "', '" . $village . "', '" . $po . "', '" . $tehsil . "', '" . $district . "', '" . $state . "', '" . $pincode . "')";
            if (mysqli_query($conn, $sql_insert)) {
                $address_id = mysqli_insert_id($conn);
            } else {
                $_SESSION['error'] = "Details update failed: " . mysqli_error($conn);
                header("Location: update_room_detail.php");
                exit;
            }
        } 
    // Update roommate_accommodations table
    $sql_accommodation = "UPDATE roommate_accommodations SET 
                            date_of_birth = '$dob',
                            apartment_name = '$apartment_name',
                            rent_amount = $rent_amount,
                            room_type = '$room_type',
                            preferred_gender = '$preferred_gender',
                            lease_duration = '$lease_duration',
                            guest_policy = '$guest_policy',
                            furnishing_status = '$furnishing_status',
                            parking = '$parking',
                            smoking = '$smoking',
                            drinking = '$drinking',
                            pets = '$pets',
                            dietary_preference = '$dietary_preference',
                            daily_schedule = '$daily_schedule',
                            address_id = '$address_id',
                            description = '$description'
                        WHERE accommodation_id = $accommodation_id";
        if (mysqli_query($conn, $sql_accommodation)) {

            // Update Amenities
            $amenities = json_decode($amenities_json, true);

            // Delete existing amenities
            $sql_delete_amenities = "DELETE FROM roommate_accommodation_amenities WHERE accommodation_id = $accommodation_id";
            mysqli_query($conn, $sql_delete_amenities);

            // Insert new amenities
            if (!empty($amenities)) {
                foreach ($amenities as $amenity_id) {
                    $sql_insert_amenity = "INSERT INTO roommate_accommodation_amenities (accommodation_id, amenity_id) VALUES ($accommodation_id, $amenity_id)";
                    mysqli_query($conn, $sql_insert_amenity);
                }
            }

            // Handle image uploads
            if (isset($_FILES['imageUpload']) && !empty($_FILES['imageUpload']['name'][0])) {
                $total_files = count($_FILES['imageUpload']['name']);
                for ($i = 0; $i < $total_files; $i++) {
                    $file_name = $_FILES['imageUpload']['name'][$i];
                    $file_tmp = $_FILES['imageUpload']['tmp_name'][$i];
                    $file_type = $_FILES['imageUpload']['type'][$i];
                    $file_size = $_FILES['imageUpload']['size'][$i];
                    $file_error = $_FILES['imageUpload']['error'][$i];

                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif','webp');

                    if (in_array($file_ext, $allowed_extensions)) {
                        $new_file_name = uniqid('', true) . '.' . $file_ext;
                        $file_destination = '../assets/uploads/roommate-accommodation/' . $new_file_name;

                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            // Insert image path into database
                            $sql_image = "INSERT INTO images (entity_type, entity_id, image_url) VALUES ('roommate_accommodation', $accommodation_id, '$new_file_name')";
                            mysqli_query($conn, $sql_image);
                        }
                    }
                }
            }
            $_SESSION['success'] = "Details updated successfully!";
            header("Location: show_room_detail.php");
            exit;

    } else {
        $_SESSION['error'] = "Details update failed: " . mysqli_error($conn);
        header("Location: update_room_detail.php");
        exit;
    }
} else {
    // Handle cases where the request is not POST
    $_SESSION['error'] = "Invalid request.";
    header("Location: update_room_detail.php");
    exit;
}

mysqli_close($conn);
?>