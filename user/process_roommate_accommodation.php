<?php
require './check.php'; // Database connection

$response = ["status" => "error", "message" => "Something went wrong!"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Collect and sanitize inputs
        $user_id = intval($_SESSION['user_id']); // Assuming user is logged in
        $apartment_name = mysqli_real_escape_string($conn, trim($_POST["apartment_name"]));
        $rent_amount = floatval($_POST["rent_amount"]);
        $room_type = mysqli_real_escape_string($conn, trim($_POST["room_type"]));
        $preferred_gender = mysqli_real_escape_string($conn, trim($_POST["preferred_gender"]));
        $lease_duration = mysqli_real_escape_string($conn, trim($_POST["lease_duration"]));
        $guest_policy = mysqli_real_escape_string($conn, trim($_POST["guest_policy"]));
        $furnishing_status = mysqli_real_escape_string($conn, trim($_POST["furnishing_status"]));
        $parking = mysqli_real_escape_string($conn, trim($_POST["parking"]));
        $smoking = mysqli_real_escape_string($conn, trim($_POST["smoking"]));
        $drinking = mysqli_real_escape_string($conn, trim($_POST["drinking"]));
        $pets = mysqli_real_escape_string($conn, trim($_POST["pets"]));
        $dietary_preference = mysqli_real_escape_string($conn, trim($_POST["dietary_preference"]));
        $daily_schedule = mysqli_real_escape_string($conn, trim($_POST["daily_schedule"]));
        $description = mysqli_real_escape_string($conn, trim($_POST["description"]));
        $dob = mysqli_real_escape_string($conn, trim($_POST["dob"]));
        $address_id = isset($_POST["address_id"]) ? intval($_POST["address_id"]) : null;
        $amenities = isset($_POST["amenitiesHidden"]) ? json_decode($_POST["amenitiesHidden"], true) : [];

        // Check if user already submitted an accommodation
        $sql = "SELECT accommodation_id FROM roommate_accommodations WHERE user_id = " . $user_id . " LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $existing_accommodation = mysqli_fetch_assoc($result);

        if ($existing_accommodation) {
            $response = ["status" => "error", "message" => "You have already submitted an accommodation."];
            echo json_encode($response);
            exit(); // Stop execution if accommodation already exists.
        }

        // Store New Address if Needed
        if (!$address_id && !empty($_POST["city"])) {
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
                    throw new Exception("Address saving failed: " . mysqli_error($conn));
                }
            }
        }

        // Insert Accommodation
        $sql = "INSERT INTO roommate_accommodations (user_id, apartment_name, rent_amount,date_of_birth, room_type, preferred_gender, lease_duration, guest_policy, furnishing_status, parking, smoking, drinking, pets, dietary_preference, daily_schedule,description, address_id) 
        VALUES ('$user_id','$apartment_name','$rent_amount','$dob','$room_type','$preferred_gender','$lease_duration', '$guest_policy', '$furnishing_status', '$parking','$smoking', '$drinking', '$pets', '$dietary_preference', '$daily_schedule', '$description', '$address_id')";

        if (mysqli_query($conn, $sql)) {
            $accommodation_id = mysqli_insert_id($conn);

            // Manage Amenities (INSERT new ones)
            if (!empty($amenities)) {
                foreach ($amenities as $amenity_id) {
                    $sql = "INSERT INTO roommate_accommodation_amenities (accommodation_id, amenity_id) VALUES ('$accommodation_id', ' $amenity_id')";
                    mysqli_query($conn, $sql);
                }
            }


            if (!empty($_FILES["imageUpload"]["name"][0])) {
                $uploadDir = "../assets/uploads/roommate-accommodation/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $max_images = 5;
                $uploaded_images = 0;

                foreach ($_FILES["imageUpload"]["name"] as $index => $imageFile) {
                    if ($uploaded_images >= $max_images) {
                        break; // Stop if limit reached
                    }

                    // Get file extension
                    $file_name = $_FILES["imageUpload"]["name"][$index];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    // Validate file extension
                    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                    if (in_array($file_ext, $allowed_extensions)) {

                        $fileName = uniqid('', true) . '.' . $file_ext;
                        $imagePath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES["imageUpload"]["tmp_name"][$index], $imagePath)) {
                            $sql = "INSERT INTO images (entity_type, entity_id, image_url) VALUES ('roommate_accommodation', " . $accommodation_id . ", '" . $fileName . "')";
                            mysqli_query($conn, $sql);
                            $uploaded_images++;
                        }
                    } else {
                        // Handle invalid file extension (e.g., display an error message)
                        echo "Invalid file type for: " . $file_name . "<br>";
                    }
                }
            }

            $response = ["status" => "success", "message" => "Roommate Accommodation saved successfully!"];
        } else {
            throw new Exception("Database error: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }
}

// Return JSON response
echo json_encode($response);
