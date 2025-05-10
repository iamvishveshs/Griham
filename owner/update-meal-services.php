<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_meal_service'])) {
    $service_id = intval($_POST['service_id']);
    $user_id = intval($_SESSION['user_id']);
    $token = urlencode(base64_encode($_POST['service_id']));
    // Fetch existing listing
    $existing_meal_service_query = "SELECT * FROM meal_services WHERE service_id = '$service_id' AND user_id = '$user_id'";
    $existing_meal_service_result = mysqli_query($conn, $existing_meal_service_query);
    if (mysqli_num_rows($existing_meal_service_result) == 0) {
        $_SESSION['error'] = "Meal Service not found.";
        header("Location: ./meal-services.php");
        exit();
    }

    

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $opening_hours = mysqli_real_escape_string($conn, trim($_POST['opening_hours']));
    $closing_hours = mysqli_real_escape_string($conn, trim($_POST['closing_hours']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $village = mysqli_real_escape_string($conn, trim($_POST['village']));
    $po = mysqli_real_escape_string($conn, trim($_POST['po']));
    $tehsil = mysqli_real_escape_string($conn, trim($_POST['tehsil']));
    $district = mysqli_real_escape_string($conn, trim($_POST['district']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));
    $pincode = mysqli_real_escape_string($conn, trim($_POST['pincode']));
    $contact_number = mysqli_real_escape_string($conn, trim($_POST['contact_number']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));

    // Validate phone number
    if (!preg_match('/^[1-9][0-9]{9}$/', $contact_number)) {
        $_SESSION['error'] = "Invalid phone number format!";
        header("Location: update-meal-services.php?token=$token");
        exit();
    }

    // Check if the address already exists
    $address_query = "SELECT address_id FROM addresses WHERE city = '$city' AND village = '$village' AND po = '$po' 
                      AND tehsil = '$tehsil' AND district = '$district' AND state = '$state' AND pincode = '$pincode'";
    $address_result = mysqli_query($conn, $address_query);

    if (mysqli_num_rows($address_result) > 0) {
        $address_row = mysqli_fetch_assoc($address_result);
        $address_id = $address_row['address_id'];
    } else {
        $insert_address_query = "INSERT INTO addresses (city, village, po, tehsil, district, state, pincode)
                                 VALUES ('$city', '$village', '$po', '$tehsil', '$district', '$state', '$pincode')";
        mysqli_query($conn, $insert_address_query);
        $address_id = mysqli_insert_id($conn);
    }
    // Handle main image update
    $upload_dir = "../assets/uploads/meal-services/";

    // Fetch existing main image from DB
    $existing_meal_service_query = "SELECT main_image FROM meal_services WHERE service_id = '$service_id' AND user_id = '$user_id'";
    $existing_meal_service_result = mysqli_query($conn, $existing_meal_service_query);
    $existing_meal_service = mysqli_fetch_assoc($existing_meal_service_result);
    $existing_main_image = $existing_meal_service['main_image'];

    $main_image_name = $existing_main_image; // Default to existing image

    if (!empty($_FILES['main_image']['name'])) {
        $file_name = basename($_FILES['main_image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, ["jpg", "jpeg", "png", "webp"])) {
            // Generate new filename
            $main_image_name = "main_" . uniqid() . "." . $file_ext;

            // Move new file to uploads folder
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $main_image_name)) {
                // Unlink old image if it exists
                if (!empty($existing_main_image) && file_exists($upload_dir . $existing_main_image)) {
                    unlink($upload_dir . $existing_main_image);
                }
            }
        }
    }

    // ✅ Update query
    $update_query = "UPDATE meal_services SET 
                 name = '$name',
                 category = '$category',
                 address_id = '$address_id',
                 contact_number = '$contact_number',
                 description = '$description',
                 opening_hours= '$opening_hours',
                 closing_hours= '$closing_hours'";

    // ✅ Only update main image if a new one was uploaded
    if (!empty($_FILES['main_image']['name'])) {
        $update_query .= ", main_image = '$main_image_name'";
    }

    $update_query .= " WHERE service_id = '$service_id' AND user_id='$user_id'";

    mysqli_query($conn, $update_query);

    // Append new amenities
    if (!empty($_POST['amenities'])) {
        $amenities_list = json_decode($_POST['amenities'], true); // Decode JSON format input
        $existing_amenities = [];

        // Step 1: Fetch current amenities from the database
        $fetch_amenities_query = "SELECT amenity_id FROM meal_services_amenities WHERE meal_service_id = '$service_id'";
        $result = mysqli_query($conn, $fetch_amenities_query);

        while ($row = mysqli_fetch_assoc($result)) {
            $existing_amenities[] = intval($row['amenity_id']);
        }

        // Step 2: Determine amenities to delete (those not in the new list)
        $amenities_to_delete = array_diff($existing_amenities, $amenities_list);
        if (!empty($amenities_to_delete)) {
            $delete_query = "DELETE FROM meal_services_amenities 
                             WHERE meal_service_id = '$service_id' 
                             AND amenity_id IN (" . implode(",", $amenities_to_delete) . ")";
            mysqli_query($conn, $delete_query);
        }

        // Step 3: Add new amenities (if not already present)
        foreach ($amenities_list as $amenity_id) {
            $amenity_id = intval($amenity_id);
            if ($amenity_id > 0 && !in_array($amenity_id, $existing_amenities)) {
                $insert_amenity_query = "INSERT INTO meal_services_amenities (meal_service_id, amenity_id) VALUES ('$service_id', '$amenity_id')";
                mysqli_query($conn, $insert_amenity_query);
            }
        }
    } else {
        // If no amenities are selected, remove all existing amenities
        $delete_all_query = "DELETE FROM meal_services_amenities WHERE meal_service_id = '$service_id'";
        mysqli_query($conn, $delete_all_query);
    }


    // Append new images
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['images']['name'][$key]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, ["jpg", "jpeg", "png", "webp"])) {
                $new_filename = uniqid() . "." . $file_ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                    $check_image_query = "SELECT * FROM images WHERE entity_type = 'meal_service' AND entity_id = '$service_id' AND image_url = '$new_filename'";
                    $image_result = mysqli_query($conn, $check_image_query);
                    if (mysqli_num_rows($image_result) == 0) {
                        $image_query = "INSERT INTO images (entity_type, entity_id, image_url) VALUES ('meal_service', '$service_id', '$new_filename')";
                        mysqli_query($conn, $image_query);
                    }
                }
            }
        }
    }

    $_SESSION['success'] = $category." updated successfully!";
    header("Location: meal-services.php");
    exit();
} else if (isset($_GET['token'])) {
    $meal_service_id = base64_decode($_GET['token']);

    // Validate decoded ID
    if ($meal_service_id === false || !is_numeric($meal_service_id)) {
        die("Invalid request.");
    }

    $user_id = $_SESSION['user_id'];
    $meal_service_id = intval($meal_service_id);

    // Fetch meal_service details
    $query = "SELECT ms.*,
                 adr.*, 
                 GROUP_CONCAT(DISTINCT CONCAT(am.amenity_name, ':', am.amenity_id) ORDER BY am.amenity_id SEPARATOR ', ') AS amenities,
                 GROUP_CONCAT(DISTINCT img.image_url ORDER BY img.image_id SEPARATOR ', ') AS images
          FROM meal_services ms
          LEFT JOIN addresses adr ON ms.address_id = adr.address_id
          LEFT JOIN meal_services_amenities msa ON ms.service_id = msa.meal_service_id
          LEFT JOIN amenities am ON msa.amenity_id = am.amenity_id
          LEFT JOIN images img ON ms.service_id = img.entity_id AND img.entity_type = 'meal_service'
          WHERE ms.service_id = '$meal_service_id'
          GROUP BY ms.service_id";
    $result = mysqli_query($conn, $query);

    if ($meal_service = mysqli_fetch_assoc($result)) {
    } else {
        $_SESSION['error'] = "Listing not found!";
        header("Location: browse-listings.php");
        exit();
    }

    mysqli_close($conn);
} else {
    die("Access Denied");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update <?php echo $meal_service['category']; ?></title>


    <?php
    require("./style-files.php");
    ?>

    <link rel="stylesheet" href="../assets/css/form.css">
    <style>
        .pill-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }

        .pill-container>* {
            width: fit-content;
            max-width: fit-content;
            min-width: fit-content;
        }

        .pill {
            background-color: #007bff;
            width: fit-content !important;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
        }

        .pill .remove-pill {
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-item {
            position: relative;
            display: inline-block;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
    </style>

    <!-- Trumbowyg CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.26.0/ui/trumbowyg.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

    <style>
        .image-container {
            position: relative;
            display: inline-block;
            margin: 5px;
        }

        .image-container img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .image-container .delete-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: white;
            color: red;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: background 0.3s ease;
        }

        .image-container .delete-image:hover {
            background-color: red;
            color: white;
        }
    </style>
</head>

<body>

    <?php require("./navbar.php"); ?>
    <section class="form-section w-100">
        <p class="form-heading">Update <?php echo $meal_service['category']; ?> Details</p>
        <form action="./update-meal-services.php" class="form-container" method="POST" enctype="multipart/form-data" class="form">
            <input type="hidden" name="service_id" value="<?php echo $meal_service['service_id']; ?>">

            <div>

                <?php require("./show-message.php"); ?>
            </div>
            <h3>Basic Details</h3>
            <div class="form-group">
                <div>
                    <label>Service Name</label>
                    <input type="text" name="name" value="<?php echo $meal_service['name']; ?>" required>
                </div>

                <div>
                    <label>Service Type</label>
                    <div class="select-box">
                        <select name="category" required>
                            <option value="Dhaba" <?= ($meal_service['category'] == "Dhaba") ? "selected" : ""; ?>>Dhaba</option>
                            <option value="Cafe" <?= ($meal_service['category'] == "Cafe") ? "selected" : ""; ?>>Cafe</option>
                            <option value="Tiffin" <?= ($meal_service['category'] == "Tiffin") ? "selected" : ""; ?>>Tiffin</option>
                        </select>
                    </div>

                </div>
                <div>
                    <label for="opening_hours">Opening Time</label>
                    <input type="text" name="opening_hours" id="opening_hours" value="<?php echo $meal_service['opening_hours']; ?>" required>
                </div>
                <div>
                    <label for="closing_hours">Closing Time</label>
                    <input type="text" name="closing_hours" id="closing_hours" value="<?php echo $meal_service['closing_hours']; ?>" required>
                </div>
            </div>
            <h3>Contact Details</h3>
            <div class="form-group">
                <!-- Search Existing Address -->
                <div class="amenities-container">
                    <label>Search Address</label>
                    <input type="text" id="searchAddress" placeholder="Type to search..." autocomplete="off">
                    <div id="addressSuggestions" class="suggestion-box"></div>
                </div>

                <div>
                    <label>City</label>
                    <input type="text" name="city" id="city" pattern="^[a-zA-Z\s]+$" title="Only letters and spaces allowed" value="<?php echo $meal_service['city']; ?>" required>
                </div>

                <div>
                    <label>Village</label>
                    <input type="text" name="village" id="village" value="<?php echo $meal_service['village']; ?>">
                </div>

                <div>
                    <label>Post Office (PO)</label>
                    <input type="text" name="po" id="po" value="<?php echo $meal_service['po']; ?>">
                </div>

                <div>
                    <label>Tehsil</label>
                    <input type="text" name="tehsil" id="tehsil" value="<?php echo $meal_service['tehsil']; ?>">
                </div>

                <div>
                    <label>District</label>
                    <input type="text" name="district" id="district" value="<?php echo $meal_service['district']; ?>" required>
                </div>

                <div>
                    <label>State</label>
                    <select name="state" id="state" required>
                        <option value="">Select State</option>
                        <optgroup label="States">
                            <option value="Andhra Pradesh" <?= ($meal_service['state'] == "Andhra Pradesh") ? "selected" : ""; ?>>Andhra Pradesh</option>
                            <option value="Arunachal Pradesh" <?= ($meal_service['state'] == "Arunachal Pradesh") ? "selected" : ""; ?>>Arunachal Pradesh</option>
                            <option value="Assam" <?= ($meal_service['state'] == "Assam") ? "selected" : ""; ?>>Assam</option>
                            <option value="Bihar" <?= ($meal_service['state'] == "Bihar") ? "selected" : ""; ?>>Bihar</option>
                            <option value="Chhattisgarh" <?= ($meal_service['state'] == "Chhattisgarh") ? "selected" : ""; ?>>Chhattisgarh</option>
                            <option value="Goa" <?= ($meal_service['state'] == "Goa") ? "selected" : ""; ?>>Goa</option>
                            <option value="Gujarat" <?= ($meal_service['state'] == "Gujarat") ? "selected" : ""; ?>>Gujarat</option>
                            <option value="Haryana" <?= ($meal_service['state'] == "Haryana") ? "selected" : ""; ?>>Haryana</option>
                            <option value="Himachal Pradesh" <?= ($meal_service['state'] == "Himachal Pradesh") ? "selected" : ""; ?>>Himachal Pradesh</option>
                            <option value="Jharkhand" <?= ($meal_service['state'] == "Jharkhand") ? "selected" : ""; ?>>Jharkhand</option>
                            <option value="Karnataka" <?= ($meal_service['state'] == "Karnataka") ? "selected" : ""; ?>>Karnataka</option>
                            <option value="Kerala" <?= ($meal_service['state'] == "Kerala") ? "selected" : ""; ?>>Kerala</option>
                            <option value="Madhya Pradesh" <?= ($meal_service['state'] == "Madhya Pradesh") ? "selected" : ""; ?>>Madhya Pradesh</option>
                            <option value="Maharashtra" <?= ($meal_service['state'] == "Maharashtra") ? "selected" : ""; ?>>Maharashtra</option>
                            <option value="Manipur" <?= ($meal_service['state'] == "Manipur") ? "selected" : ""; ?>>Manipur</option>
                            <option value="Meghalaya" <?= ($meal_service['state'] == "Meghalaya") ? "selected" : ""; ?>>Meghalaya</option>
                            <option value="Mizoram" <?= ($meal_service['state'] == "Mizoram") ? "selected" : ""; ?>>Mizoram</option>
                            <option value="Nagaland" <?= ($meal_service['state'] == "Nagaland") ? "selected" : ""; ?>>Nagaland</option>
                            <option value="Odisha" <?= ($meal_service['state'] == "Odisha") ? "selected" : ""; ?>>Odisha</option>
                            <option value="Punjab" <?= ($meal_service['state'] == "Punjab") ? "selected" : ""; ?>>Punjab</option>
                            <option value="Rajasthan" <?= ($meal_service['state'] == "Rajasthan") ? "selected" : ""; ?>>Rajasthan</option>
                            <option value="Sikkim" <?= ($meal_service['state'] == "Sikkim") ? "selected" : ""; ?>>Sikkim</option>
                            <option value="Tamil Nadu" <?= ($meal_service['state'] == "Tamil Nadu") ? "selected" : ""; ?>>Tamil Nadu</option>
                            <option value="Telangana" <?= ($meal_service['state'] == "Telangana") ? "selected" : ""; ?>>Telangana</option>
                            <option value="Tripura" <?= ($meal_service['state'] == "Tripura") ? "selected" : ""; ?>>Tripura</option>
                            <option value="Uttar Pradesh" <?= ($meal_service['state'] == "Uttar Pradesh") ? "selected" : ""; ?>>Uttar Pradesh</option>
                            <option value="Uttarakhand" <?= ($meal_service['state'] == "Uttarakhand") ? "selected" : ""; ?>>Uttarakhand</option>
                            <option value="West Bengal" <?= ($meal_service['state'] == "West Bengal") ? "selected" : ""; ?>>West Bengal</option>
                        </optgroup>
                        <optgroup label="Union Territories">
                            <option value="Andaman and Nicobar Islands" <?= ($meal_service['state'] == "Andaman and Nicobar Islands") ? "selected" : ""; ?>>Andaman and Nicobar Islands</option>
                            <option value="Chandigarh" <?= ($meal_service['state'] == "Chandigarh") ? "selected" : ""; ?>>Chandigarh</option>
                            <option value="Dadra and Nagar Haveli and Daman and Diu" <?= ($meal_service['state'] == "Dadra and Nagar Haveli and Daman and Diu") ? "selected" : ""; ?>>Dadra and Nagar Haveli and Daman and Diu</option>
                            <option value="Delhi" <?= ($meal_service['state'] == "Delhi") ? "selected" : ""; ?>>Delhi</option>
                            <option value="Lakshadweep" <?= ($meal_service['state'] == "Lakshadweep") ? "selected" : ""; ?>>Lakshadweep</option>
                            <option value="Puducherry" <?= ($meal_service['state'] == "Puducherry") ? "selected" : ""; ?>>Puducherry</option>
                            <option value="Jammu and Kashmir" <?= ($meal_service['state'] == "Jammu and Kashmir") ? "selected" : ""; ?>>Jammu and Kashmir</option>
                            <option value="Ladakh" <?= ($meal_service['state'] == "Ladakh") ? "selected" : ""; ?>>Ladakh</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label>Pincode</label>
                    <input type="text" name="pincode" id="pincode" pattern="^\d{6}$" title="Enter a valid 6-digit pincode" value="<?php echo $meal_service['pincode']; ?>" required>
                </div>

                <div>
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" pattern="^[1-9][0-9]{9}$"
                        title="Enter a valid 10-digit phone number" value="<?= $meal_service['contact_number']; ?>" required>
                </div>

                <input type="hidden" name="address_id" id="address_id" value="<?= $meal_service['address_id']; ?>">
            </div>
            <h3>About <?php echo $meal_service['category']; ?></h3>
            <div class="form-group">

                <div>
                    <label><?php echo $meal_service['category']; ?> Description</label>
                    <textarea id="description" name="description" required><?= $meal_service['description']; ?></textarea>
                </div>

                <script>
                    $(document).ready(function() {
                        $('#description').trumbowyg();
                    });
                </script>
            </div>

            <!-- 📌 Amenities Input Field -->
            <h3>Facilities</h3>
            <div class="form-group">
                <div>
                    <label>Amenities</label>
                    <div class="amenities-container">
                        <input type="text" id="amenityInput" class="form-control" placeholder="Type an amenity..." autocomplete="off">
                        <div id="suggestions" class="suggestion-box"></div>
                        <div id="amenitiesList" class="pill-container">
                            <?php
                            foreach (explode(",", $meal_service["amenities"]) as $amenity) {
                                $amenityArray = explode(":", $amenity) ?>
                                <?php echo "<div class='pill' data-id='" . htmlspecialchars($amenityArray[1]) . "'>" . htmlspecialchars($amenityArray[0]) . "<span class='remove-pill'>&times;</span></div>"; ?>
                            <?php } ?>
                        </div>
                    </div>
                    <input type="hidden" name="amenities" id="amenitiesHidden" value="[<?php
                                                                                        $amenitiesArray = [];
                                                                                        foreach (explode(",", $meal_service["amenities"]) as $amenity) {
                                                                                            $amenityArray = explode(":", $amenity);
                                                                                            $amenitiesArray[] = intval($amenityArray[1]); // Convert to integer for proper formatting
                                                                                        }
                                                                                        echo implode(",", $amenitiesArray);
                                                                                        ?>]">

                </div>
            </div>

            <!-- 📌 Main Front Image Upload -->
            <h3><?php echo $meal_service['category']; ?> Photgraphs</h3>
            <div class="form-group">
                <div>
                    <label class="custum-file-upload" for="imageUploadMain">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                                <path fill="" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10Z"></path>
                            </svg>
                        </div>
                        <div class="text">
                            <span><?php echo $meal_service['category']; ?> Front Image</span>
                        </div>
                        <input type="file" id="imageUploadMain" name="main_image" accept="image/png, image/jpeg,image/webp, image/gif">
                    </label>
                </div>

            </div>
            <?php if (isset($meal_service['main_image'])) {
                echo "<h6>Current Front Image</h6>"; ?>
                <img src="../assets/uploads/meal-services/<?= $meal_service['main_image']; ?>" width="100" height="100" alt="Main Image">
            <?php } ?>
            <input type="hidden" name="existing_main_image" value="<?= $existing_meal_service['main_image']; ?>">

            <span id="imagePreviewMain" class="preview-container"></span>
            <div class="form-group">
                <!-- 📌 Additional meal_service Images -->
                <div>
                    <label class="custum-file-upload" for="imageUpload">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                                <path fill="" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10Z"></path>
                            </svg>
                        </div>
                        <div class="text">
                            <span>Click to upload <?php echo $meal_service['category']; ?> Images</span>
                        </div>
                        <input type="file" id="imageUpload" name="images[]" accept="image/png, image/jpeg,image/webp, image/gif" multiple <?php if (empty($meal_service["images"])) echo 'required'; ?>>
                    </label>
                </div>
            </div>

            </div>
            <div>
                <br>
                <label>Upoaded Images</label>
                <div id="uploaded-images">
                    <?php
                    $images = array_filter(array_map('trim', explode(",", $meal_service["images"])));
                    foreach ($images as $img) {
                        if (!empty($img)) {
                            echo "
                <div class='image-container' id='img_$img'>
                    <img src='../assets/uploads/meal-services/$img' width='100' style='margin-right:5px;'>
                    <button type='button' class='delete-image' data-image='$img' data-listing='{$meal_service['service_id']}'>✕</button>
                </div>";
                        }
                    }
                    ?>
                </div>



                <div id="imagePreview" class="preview-container"></div>
                <button type="submit" id="submitForm" name="update_meal_service" class="custom-button">Update <?php $meal_service['category'] ?></button>
                <br><br>
        </form>
    </section>



    <?php
    require("./footer.php");
    require("./script-files.php");
    ?>
    <!-- jQuery (Required) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            try {
                $('#opening_hours').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 60,
                    minTime: '00:00',
                    maxTime: '23:59',
                    startTime: '00:00',
                    dynamic: true,
                    dropdown: true,
                    scrollbar: true
                });

                $('#closing_hours').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 60,
                    minTime: '00:00',
                    maxTime: '23:59',
                    startTime: '00:00',
                    dynamic: true,
                    dropdown: true,
                    scrollbar: true
                });
            } catch (error) {
                console.error("Timepicker error:", error);
            }
        });
    </script>
    <!-- Trumbowyg JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.26.0/trumbowyg.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#description').trumbowyg();
        });
    </script>

    <script>
        document.getElementById("imageUploadMain").addEventListener("change", function(event) {
            let previewContainer = document.getElementById("imagePreviewMain");
            previewContainer.innerHTML = ""; // Clear previous previews

            let files = event.target.files;
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let fileType = file.type;

                    // Check if file is a valid image type
                    if (fileType.match("image.*")) {
                        let reader = new FileReader();

                        reader.onload = function(e) {
                            let imgElement = document.createElement("img");
                            imgElement.src = e.target.result;
                            imgElement.style.width = "100px";
                            imgElement.style.height = "100px";
                            imgElement.style.margin = "5px";
                            imgElement.style.borderRadius = "5px";
                            imgElement.style.boxShadow = "0 2px 5px rgba(0,0,0,0.2)";
                            previewContainer.appendChild(imgElement);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        });
        // For extra images
        document.getElementById("imageUpload").addEventListener("change", function(event) {
            let previewContainer = document.getElementById("imagePreview");
            previewContainer.innerHTML = ""; // Clear previous previews

            let files = event.target.files;
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let fileType = file.type;

                    // Check if file is a valid image type
                    if (fileType.match("image.*")) {
                        let reader = new FileReader();

                        reader.onload = function(e) {
                            let imgElement = document.createElement("img");
                            imgElement.src = e.target.result;
                            imgElement.style.width = "100px";
                            imgElement.style.height = "100px";
                            imgElement.style.margin = "5px";
                            imgElement.style.borderRadius = "5px";
                            imgElement.style.boxShadow = "0 2px 5px rgba(0,0,0,0.2)";
                            previewContainer.appendChild(imgElement);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            let selectedAmenities = [];

            // Load existing amenities into selectedAmenities
            $("#amenitiesList .pill").each(function() {
                selectedAmenities.push({
                    amenity_id: $(this).data("id"),
                    amenity_name: $(this).text().trim().replace("×", "") // Remove the close button text
                });
            });

            updateHiddenInput(); // Ensure hidden input is updated on page load

            // Search Amenities
            $("#amenityInput").on("input", function() {
                let query = $(this).val().trim();
                if (query.length > 0) {
                    $.ajax({
                        url: "search_amenities.php",
                        type: "GET",
                        data: {
                            search: query
                        },
                        dataType: "json",
                        success: function(response) {
                            let suggestionsBox = $("#suggestions");
                            suggestionsBox.empty();
                            if (response.length > 0) {
                                response.forEach(amenity => {
                                    suggestionsBox.append(
                                        `<div class="suggestion-item" data-id="${amenity.amenity_id}" data-name="${amenity.amenity_name}">
                                    ${amenity.amenity_name}
                                </div>`
                                    );
                                });
                                suggestionsBox.show();
                            } else {
                                suggestionsBox.hide();
                            }
                        }
                    });
                } else {
                    $("#suggestions").hide();
                }
            });

            // Select an Amenity
            $(document).on("click", ".suggestion-item", function() {
                let amenityId = $(this).data("id");
                let amenityName = $(this).data("name");

                if (!selectedAmenities.some(item => item.amenity_id === amenityId)) {
                    selectedAmenities.push({
                        amenity_id: amenityId,
                        amenity_name: amenityName
                    });

                    $("#amenitiesList").append(`
                <div class="pill" data-id="${amenityId}">
                    ${amenityName} <span class="remove-pill">&times;</span>
                </div>
            `);
                    updateHiddenInput();
                }

                $("#suggestions").hide();
                $("#amenityInput").val("");
            });

            // Remove an Amenity
            $(document).on("click", ".remove-pill", function() {
                let pill = $(this).parent();
                let amenityId = pill.data("id");

                selectedAmenities = selectedAmenities.filter(item => item.amenity_id !== amenityId);
                pill.remove();
                updateHiddenInput();
            });

            // Update Hidden Input with Selected Amenity IDs
            function updateHiddenInput() {
                let ids = selectedAmenities.map(item => item.amenity_id);
                $("#amenitiesHidden").val(JSON.stringify(ids)); // Store as valid JSON
            }

            // Submit Form with AJAX
            $("#submitForm").on("submit", function(event) {
                event.preventDefault();

                let formData = $(this).serialize(); // Collect form data

                $.ajax({
                    url: "process_accommodation.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        alert(response.message);
                        if (response.status === "success") {
                            $("#submitForm")[0].reset();
                            $("#amenitiesList").empty();
                            selectedAmenities = [];
                            updateHiddenInput();
                        }
                    }
                });
            });
        });
    </script>

    <script>
        // AJAX-based Address Search
        $(document).ready(function() {
            $("#searchAddress").on("keyup", function() {
                let query = $(this).val();
                if (query.length < 3) {
                    $("#addressSuggestions").html("");
                    return;
                }

                $.ajax({
                    url: "fetch_address.php",
                    method: "POST",
                    data: {
                        query: query
                    },
                    success: function(response) {
                        $("#addressSuggestions").html(response);
                    }
                });
            });

            // Select an address from suggestions
            $(document).on("click", ".address-suggestion-item", function() {
                let data = $(this).data();
                $("#searchAddress").val(data.fullAddress);
                $("#address").val(data.fullAddress);
                $("#city").val(data.city);
                $("#village").val(data.village);
                $("#po").val(data.po);
                $("#tehsil").val(data.tehsil);
                $("#district").val(data.district);
                $("#state").val(data.state);
                $("#pincode").val(data.pincode);
                $("#address_id").val(data.addressId);

                $("#addressSuggestions").html(""); // Clear suggestions
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".delete-image").forEach(button => {
                button.addEventListener("click", function() {
                    let imageName = this.getAttribute("data-image");
                    let serviceId = this.getAttribute("data-listing");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to recover this image!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("delete-image.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: "image=" + encodeURIComponent(imageName) + "&meal_service_id=" + encodeURIComponent(serviceId)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById("img_" + imageName).remove();
                                        Swal.fire("Deleted!", "The image has been removed.", "success");
                                    } else {
                                        Swal.fire("Error!", data.error, "error");
                                    }
                                })
                                .catch(error => {
                                    console.error("Error:", error);
                                    Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                                });
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>