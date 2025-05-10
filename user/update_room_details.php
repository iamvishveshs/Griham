<?php
require("./check.php");

// Fetch accommodation details if accommodation_id is provided
$user_id = intval($_SESSION['user_id']);
$roomDetails = [];
if (isset($user_id) && is_numeric($user_id)) {
    $sql = "SELECT ra.*, a.* FROM roommate_accommodations ra
            JOIN addresses a ON ra.address_id = a.address_id
             WHERE ra.user_id = $user_id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $roomDetails = mysqli_fetch_assoc($result);
        $accommodation_id = $roomDetails['accommodation_id'];

        // Fetch amenities for this accommodation
        $amenitiesSql = "SELECT a.amenity_id, a.amenity_name FROM roommate_accommodation_amenities raa
                         JOIN amenities a ON raa.amenity_id = a.amenity_id
                         WHERE raa.accommodation_id = $accommodation_id";
        $amenitiesResult = mysqli_query($conn, $amenitiesSql);
        $roomAmenities = [];
        if ($amenitiesResult && mysqli_num_rows($amenitiesResult) > 0) {
            while ($amenity = mysqli_fetch_assoc($amenitiesResult)) {
                $roomAmenities[] = $amenity;
            }
        }

        // Fetch images for this accommodation
        $imagesSql = "SELECT image_url FROM images WHERE entity_type = 'roommate_accommodation' AND entity_id = $accommodation_id";
        $imagesResult = mysqli_query($conn, $imagesSql);
        $roomImages = [];
        if ($imagesResult && mysqli_num_rows($imagesResult) > 0) {
            while ($image = mysqli_fetch_assoc($imagesResult)) {
                $roomImages[] = $image['image_url'];
            }
        }
    } else {
        echo "Accommodation not found.";
        exit();
    }
} else {
    echo "no accomodation";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room & Stay Details </title>

    <?php require("./style-files.php"); ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php require("./navbar.php"); ?>
    <div style="text-align: center;margin-top:20px">
        <h2> Update your Room Details</h2>
        <p>So that users can contact you</p>
    </div>

    <?php require("./show-message.php"); ?>



    <form action="./update_room_details_process.php" method="POST" id="myForm" class="form-container" enctype="multipart/form-data">
        <input type="hidden" name="accommodation_id" value="<?php echo htmlspecialchars($roomDetails['accommodation_id'] ?? ''); ?>">
        <h3>Your Current Living Space </h3>
        <div class="form-group">
            <div>
                <label>Apartment/Room Name</label>
                <input type="text" name="apartment_name" placeholder="Enter Name" required value="<?php echo htmlspecialchars($roomDetails['apartment_name'] ?? ''); ?>">
            </div>
            <div>
                <label>Rent Amount ( in Rs.)</label>
                <input type="number" name="rent_amount" placeholder="Enter rent amount" required value="<?php echo htmlspecialchars($roomDetails['rent_amount'] ?? ''); ?>">
            </div>
            <div>
                <label>Occupancy</label>
                <select name="room_type" required>
                    <option value="Single" <?php echo (isset($roomDetails['room_type']) && $roomDetails['room_type'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option value="Shared" <?php echo (isset($roomDetails['room_type']) && $roomDetails['room_type'] === 'Shared') ? 'selected' : ''; ?>>Shared</option>
                    <option value="Any" <?php echo (isset($roomDetails['room_type']) && $roomDetails['room_type'] === 'Any') ? 'selected' : ''; ?>>Any</option>
                </select>
            </div>
            <div>
                <label>Looking For</label>
                <select name="preferred_gender" required>
                    <option value="Male" <?php echo (isset($roomDetails['preferred_gender']) && $roomDetails['preferred_gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($roomDetails['preferred_gender']) && $roomDetails['preferred_gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($roomDetails['preferred_gender']) && $roomDetails['preferred_gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div>
                <label>Stay Duration</label>
                <select name="lease_duration" required>
                    <option value="Short-term" <?php echo (isset($roomDetails['lease_duration']) && $roomDetails['lease_duration'] === 'Short-term') ? 'selected' : ''; ?>>Short-term</option>
                    <option value="Long-term" <?php echo (isset($roomDetails['lease_duration']) && $roomDetails['lease_duration'] === 'Long-term') ? 'selected' : ''; ?>>Long-term</option>
                    <option value="Flexible" <?php echo (isset($roomDetails['lease_duration']) && $roomDetails['lease_duration'] === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                </select>
            </div>
            <div>
                <label>Guest Policy</label>
                <select name="guest_policy" required>
                    <option value="Strict" <?php echo (isset($roomDetails['guest_policy']) && $roomDetails['guest_policy'] === 'Strict') ? 'selected' : ''; ?>>Strict</option>
                    <option value="Moderate" <?php echo (isset($roomDetails['guest_policy']) && $roomDetails['guest_policy'] === 'Moderate') ? 'selected' : ''; ?>>Moderate</option>
                    <option value="Flexible" <?php echo (isset($roomDetails['guest_policy']) && $roomDetails['guest_policy'] === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                </select>
            </div>
            <div>
                <label>Furnishing Status</label>
                <select name="furnishing_status" required>
                    <option value="Unfurnished" <?php echo (isset($roomDetails['furnishing_status']) && $roomDetails['furnishing_status'] === 'Unfurnished') ? 'selected' : ''; ?>>Unfurnished</option>
                    <option value="Semi-Furnished" <?php echo (isset($roomDetails['furnishing_status']) && $roomDetails['furnishing_status'] === 'Semi-Furnished') ? 'selected' : ''; ?>>Semi-Furnished</option>
                    <option value="Furnished" <?php echo (isset($roomDetails['furnishing_status']) && $roomDetails['furnishing_status'] === 'Furnished') ? 'selected' : ''; ?>>Furnished</option>
                </select>
            </div>
            <div>
                <label>Parking Availability</label>
                <select name="parking" required>
                    <option value="No" <?php echo (isset($roomDetails['parking']) && $roomDetails['parking'] === 'No') ? 'selected' : ''; ?>>No</option>
                    <option value="Yes" <?php echo (isset($roomDetails['parking']) && $roomDetails['parking'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
        </div>

        <h3>Lifestyle Preferences</h3>
        <div class="form-group">
            <div>
                <label>Smoking</label>
                <select name="smoking">
                    <option value="No" <?php echo (isset($roomDetails['smoking']) && $roomDetails['smoking'] === 'No') ? 'selected' : ''; ?>>No</option>
                    <option value="Yes" <?php echo (isset($roomDetails['smoking']) && $roomDetails['smoking'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Flexible" <?php echo (isset($roomDetails['smoking']) && $roomDetails['smoking'] === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                </select>
            </div>
            <div>
                <label>Drinking</label>
                <select name="drinking">
                    <option value="No" <?php echo (isset($roomDetails['drinking']) && $roomDetails['drinking'] === 'No') ? 'selected' : ''; ?>>No</option>
                    <option value="Yes" <?php echo (isset($roomDetails['drinking']) && $roomDetails['drinking'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Flexible" <?php echo (isset($roomDetails['drinking']) && $roomDetails['drinking'] === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                </select>
            </div>
            <div>
                <label>Pets</label>
                <select name="pets">
                    <option value="No" <?php echo (isset($roomDetails['pets']) && $roomDetails['pets'] === 'No') ? 'selected' : ''; ?>>No</option>
                    <option value="Yes" <?php echo (isset($roomDetails['pets']) && $roomDetails['pets'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Flexible" <?php echo (isset($roomDetails['pets']) && $roomDetails['pets'] === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                </select>
            </div>
            <div>
                <label>Dietary Preference</label>
                <select name="dietary_preference">
                    <option value="No Preference" <?php echo (isset($roomDetails['dietary_preference']) && $roomDetails['dietary_preference'] === 'No Preference') ? 'selected' : ''; ?>>No Preference</option>
                    <option value="Vegetarian" <?php echo (isset($roomDetails['dietary_preference']) && $roomDetails['dietary_preference'] === 'Vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                    <option value="Vegan" <?php echo (isset($roomDetails['dietary_preference']) && $roomDetails['dietary_preference'] === 'Vegan') ? 'selected' : ''; ?>>Vegan</option>
                    <option value="Non-Vegetarian" <?php echo (isset($roomDetails['dietary_preference']) && $roomDetails['dietary_preference'] === 'Non-Vegetarian') ? 'selected' : ''; ?>>Non-Vegetarian</option>
                </select>
            </div>
            <div>
                <label>Daily Schedule</label>
                <select name="daily_schedule">
                    <option value="Flexible" <?php echo (isset($roomDetails['daily_schedule']) && $roomDetails['daily_schedule'] === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                    <option value="Night Owl" <?php echo (isset($roomDetails['daily_schedule']) && $roomDetails['daily_schedule'] === 'Night Owl') ? 'selected' : ''; ?>>Night Owl</option>
                    <option value="Early Riser" <?php echo (isset($roomDetails['daily_schedule']) && $roomDetails['daily_schedule'] === 'Early Riser') ? 'selected' : ''; ?>>Early Riser</option>
                </select>
            </div>
            <div>
                <label>Your DOB</label>
                <input type="date" name="dob" required value="<?php echo $roomDetails['date_of_birth']; ?>">
             </div>
        </div>
        <h3>Facilities</h3>
        <div class="form-group">
            <div>
                <label>Amenities</label>
                <div class="amenities-container">
                    <input type="text" id="amenityInput" class="form-control" placeholder="Type an amenity..." autocomplete="off">
                    <div id="suggestions" class="suggestion-box"></div>
                    <div id="amenitiesList" class="pill-container">
                        <?php
                        if (!empty($roomAmenities)) {
                            foreach ($roomAmenities as $amenity) {
                                echo '<div class="pill" data-id="' . $amenity['amenity_id'] . '">' . $amenity['amenity_name'] . ' <span class="remove-pill">&times;</span></div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <input type="hidden" name="amenitiesHidden" id="amenitiesHidden" required>
            </div>
        </div>
        <h3>Room Address</h3>
        <div class="form-group">
            <div class="amenities-container">
                <label>Search City</label>
                <input type="text" id="searchAddress" placeholder="Type to City search..." autocomplete="off" value="<?php echo htmlspecialchars($roomDetails['city'] . ', ' . $roomDetails['village'] . ', ' . $roomDetails['po'] . ', ' . $roomDetails['tehsil'] . ', ' . $roomDetails['district'] . ', ' . $roomDetails['state'] . ', ' . $roomDetails['pincode']); ?>">
                <div id="addressSuggestions" class="suggestion-box"></div>
            </div>

            <div>
                <label>City</label>
                <input type="text" name="city" id="city" pattern="^[a-zA-Z\s]+$" title="Only letters and spaces allowed" required value="<?php echo htmlspecialchars($roomDetails['city'] ?? ''); ?>">
            </div>

            <div>
                <label>Village</label>
                <input type="text" name="village" id="village" required value="<?php echo htmlspecialchars($roomDetails['village'] ?? ''); ?>">
            </div>

            <div>
                <label>Post Office (PO)</label>
                <input type="text" name="po" id="po" required value="<?php echo htmlspecialchars($roomDetails['po'] ?? ''); ?>">
            </div>

            <div>
                <label>Tehsil</label>
                <input type="text" name="tehsil" id="tehsil" required value="<?php echo htmlspecialchars($roomDetails['tehsil'] ?? ''); ?>">
            </div>

            <div>
                <label>District</label>
                <input type="text" name="district" id="district" required value="<?php echo htmlspecialchars($roomDetails['district'] ?? ''); ?>">
            </div>

            <div>
                <label>State</label>
                <select name="state" id="state" required>
                    <option value="">Select State</option>
                    <optgroup label="States">
                        <option value="Andhra Pradesh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Andhra Pradesh') ? 'selected' : ''; ?>>Andhra Pradesh</option>
                        <option value="Arunachal Pradesh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Arunachal Pradesh') ? 'selected' : ''; ?>>Arunachal Pradesh</option>
                        <option value="Assam" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Assam') ? 'selected' : ''; ?>>Assam</option>
                        <option value="Bihar" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Bihar') ? 'selected' : ''; ?>>Bihar</option>
                        <option value="Chhattisgarh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Chhattisgarh') ? 'selected' : ''; ?>>Chhattisgarh</option>
                        <option value="Goa" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Goa') ? 'selected' : ''; ?>>Goa</option>
                        <option value="Gujarat" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Gujarat') ? 'selected' : ''; ?>>Gujarat</option>
                        <option value="Haryana" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Haryana') ? 'selected' : ''; ?>>Haryana</option>
                        <option value="Himachal Pradesh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Himachal Pradesh') ? 'selected' : ''; ?>>Himachal Pradesh</option>
                        <option value="Jharkhand" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Jharkhand') ? 'selected' : ''; ?>>Jharkhand</option>
                        <option value="Karnataka" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Karnataka') ? 'selected' : ''; ?>>Karnataka</option>
                        <option value="Kerala" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Kerala') ? 'selected' : ''; ?>>Kerala</option>
                        <option value="Madhya Pradesh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Madhya Pradesh') ? 'selected' : ''; ?>>Madhya Pradesh</option>
                        <option value="Maharashtra" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Maharashtra') ? 'selected' : ''; ?>>Maharashtra</option>
                        <option value="Manipur" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Manipur') ? 'selected' : ''; ?>>Manipur</option>
                        <option value="Meghalaya" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Meghalaya') ? 'selected' : ''; ?>>Meghalaya</option>
                        <option value="Mizoram" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Mizoram') ? 'selected' : ''; ?>>Mizoram</option>
                        <option value="Nagaland" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Nagaland') ? 'selected' : ''; ?>>Nagaland</option>
                        <option value="Odisha" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Odisha') ? 'selected' : ''; ?>>Odisha</option>
                        <option value="Punjab" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Punjab') ? 'selected' : ''; ?>>Punjab</option>
                        <option value="Rajasthan" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Rajasthan') ? 'selected' : ''; ?>>Rajasthan</option>
                        <option value="Sikkim" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Sikkim') ? 'selected' : ''; ?>>Sikkim</option>
                        <option value="Tamil Nadu" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Tamil Nadu') ? 'selected' : ''; ?>>Tamil Nadu</option>
                        <option value="Telangana" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Telangana') ? 'selected' : ''; ?>>Telangana</option>
                        <option value="Tripura" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Tripura') ? 'selected' : ''; ?>>Tripura</option>
                        <option value="Uttar Pradesh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Uttar Pradesh') ? 'selected' : ''; ?>>Uttar Pradesh</option>
                        <option value="Uttarakhand" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Uttarakhand') ? 'selected' : ''; ?>>Uttarakhand</option>
                        <option value="West Bengal" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'West Bengal') ? 'selected' : ''; ?>>West Bengal</option>
                    </optgroup>
                    <optgroup label="Union Territories">
                        <option value="Andaman and Nicobar Islands" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Andaman and Nicobar Islands') ? 'selected' : ''; ?>>Andaman and Nicobar Islands</option>
                        <option value="Chandigarh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Chandigarh') ? 'selected' : ''; ?>>Chandigarh</option>
                        <option value="Dadra and Nagar Haveli and Daman and Diu" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Dadra and Nagar Haveli and Daman and Diu') ? 'selected' : ''; ?>>Dadra and Nagar Haveli and Daman and Diu</option>
                        <option value="Delhi" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Delhi') ? 'selected' : ''; ?>>Delhi</option>
                        <option value="Lakshadweep" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Lakshadweep') ? 'selected' : ''; ?>>Lakshadweep</option>
                        <option value="Puducherry" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Puducherry') ? 'selected' : ''; ?>>Puducherry</option>
                        <option value="Jammu and Kashmir" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Jammu and Kashmir') ? 'selected' : ''; ?>>Jammu and Kashmir</option>
                        <option value="Ladakh" <?php echo (isset($roomDetails['state']) && $roomDetails['state'] === 'Ladakh') ? 'selected' : ''; ?>>Ladakh</option>
                    </optgroup>
                </select>
            </div>

            <div>
                <label>Pincode</label>
                <input type="text" name="pincode" id="pincode" pattern="^\d{6}$" title="Enter a valid 6-digit pincode" required value="<?php echo htmlspecialchars($roomDetails['pincode'] ?? ''); ?>">
            </div>

            <input type="hidden" name="address_id" id="address_id" value="<?php echo htmlspecialchars($roomDetails['address_id'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <div>
                <label class="custum-file-upload" for="imageUpload">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                            <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                            <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill="" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21 20 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </g>
                        </svg>
                    </div>
                    <div class="text">
                        <span>Click to upload Property Images (max 5)</span>
                    </div>
                    <input type="file" id="imageUpload" name="imageUpload[]" accept="image/png, image/jpeg,image/webp, image/gif" multiple <?php if (empty($roomImages)) echo 'required'; ?>>
                </label>
            </div>
        </div>
        <div id="imagePreview" class="preview-container">
            <?php
            if (!empty($roomImages)) {
                foreach ($roomImages as $image) {
                    echo '<div class="image-item" data-image-name="' . $image . '"><img class="preview-image" src="../assets/uploads/roommate-accommodation/' . $image . '"><span class="remove-image">&times;</span></div>';
                }
            }
            ?>
        </div>
        <h3>Description</h3>
        <div class="form-group">
            <div>
                <textarea id="description" name="description"  autocomplete="off">I am looking for a roommate for my room.</textarea>

            </div>
        </div>
    </form>

    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>

    <script>
        // ... (your JavaScript code remains the same) ...
        document.getElementById("imageUpload").addEventListener("change", function(event) {
            let previewContainer = document.getElementById("imagePreview");

            let files = Array.from(event.target.files);

            files.forEach(file => {
                if (!file.type.match("image.*")) return; // Skip non-image files

                let reader = new FileReader();
                reader.onload = function(e) {
                    let imgContainer = document.createElement("div");
                    imgContainer.classList.add("image-item");

                    let imgElement = document.createElement("img");
                    imgElement.src = e.target.result;
                    imgElement.classList.add("preview-image");

                    let removeBtn = document.createElement("span");
                    removeBtn.innerHTML = "&times;";
                    removeBtn.classList.add("remove-image");
                    removeBtn.addEventListener("click", function() {
                        previewContainer.removeChild(imgContainer);
                    });

                    imgContainer.appendChild(imgElement);
                    imgContainer.appendChild(removeBtn);
                    previewContainer.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            });
        });
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
        });
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
        //remove image
        $(document).on("click", ".remove-image", function() {
            let imageItem = $(this).parent();
            let imageName = imageItem.data("image-name");

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (imageName) {
                        $.ajax({
                            url: "remove_room_image.php",
                            type: "POST",
                            data: {
                                imageName: imageName,
                                accommodation_id: <?php echo $accommodation_id; ?>
                            },
                            success: function(response) {
                                if (response === "success") {
                                    imageItem.remove();
                                    Swal.fire(
                                        'Deleted!',
                                        'Your image has been deleted.',
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to remove image.',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred.',
                                    'error'
                                );
                            }
                        });
                    } else {
                        imageItem.remove(); // Remove locally if it's a newly added image
                        Swal.fire(
                            'Deleted!',
                            'Your image has been deleted.',
                            'success'
                        );
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let form = document.querySelector(".form-container");
            let originalValues = {};
            let saveButton = null;

            // Store initial values of all input and select fields
            form.querySelectorAll("input, select").forEach(field => {
                originalValues[field.name] = field.value;
            });

            function checkForChanges() {
                let hasChanges = false;

                // Compare current values with the original ones
                form.querySelectorAll("input, select").forEach(field => {
                    if (originalValues[field.name] !== field.value) {
                        hasChanges = true;
                    }
                });

                if (hasChanges) {
                    addSaveButton();
                } else {
                    removeSaveButton();
                }
            }

            function addSaveButton() {
                if (!saveButton) {
                    saveButton = document.createElement("button");
                    saveButton.className = "btn";
                    saveButton.type = "submit";
                    saveButton.textContent = "Update";
                    form.appendChild(saveButton);
                }
            }

            function removeSaveButton() {
                if (saveButton) {
                    saveButton.remove();
                    saveButton = null;
                }
            }

            // Listen for changes on input and select fields
            form.querySelectorAll("input, select").forEach(field => {
                field.addEventListener("input", checkForChanges);
                field.addEventListener("change", checkForChanges);
            });
        });
    </script>
</body>

</html>