<?php
require("./check.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_SESSION['user_id']);
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
    // Validate Phone Number
    if (!preg_match('/^[1-9][0-9]{9}$/', $contact_number)) {
        $_SESSION['error'] = "Invalid contact number format!";
        header("Location: add-meal-services.php");
        exit();
    }
    // Check if Address Exists in `addresses` Table
    $address_query = "SELECT address_id FROM addresses WHERE LOWER(city) = '" . $city . "' AND LOWER(village) = '" . $village . "' AND LOWER(po) = '" . $po . "' AND LOWER(tehsil) = '" . $tehsil . "' AND LOWER(district) = '" . $district . "' AND LOWER(state) = '" . $state . "' AND pincode = '" . $pincode . "'";
    $address_result = mysqli_query($conn, $address_query);
    if (mysqli_num_rows($address_result) > 0) {
        $address_row = mysqli_fetch_assoc($address_result);
        $address_id = $address_row['address_id'];
    } else {
        // Insert New Address
        $insert_address_query = "INSERT INTO addresses (city, village, po, tehsil, district, state, pincode)
                                 VALUES ('$city', '$village', '$po', '$tehsil', '$district', '$state', '$pincode')";
        mysqli_query($conn, $insert_address_query);
        $address_id = mysqli_insert_id($conn);
    }
    // Handle Main Image Upload (Only Store Filename)
    $upload_dir = "../assets/uploads/meal-services/";
    $main_image_name = "";
    if (!empty($_FILES['main_image']['name'])) {
        $file_name = basename($_FILES['main_image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (in_array($file_ext, ["jpg", "jpeg", "png", "webp"])) {
            $main_image_name = "main_" . uniqid() . "." . $file_ext;
            move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $main_image_name);
        }
    }
    // Insert Data into `accommodation`
    $query = "INSERT INTO `meal_services`(`user_id`, `name`, `category`, `address_id`, `contact_number`, `main_image`, `description`, `opening_hours`, `closing_hours`)
              VALUES ('$user_id', '$name', '$category', '$address_id', '$contact_number', '$main_image_name', '$description', '$opening_hours', '$closing_hours')";
    if (!mysqli_query($conn, $query)) {
        $_SESSION['error'] = "Error adding {$category}: " . mysqli_error($conn);
        header("Location: ./add-meal-services.php");
        exit();
    }
    $service_id = mysqli_insert_id($conn); // Get generated service_id
    // Insert Amenities into `accommodation_amenities`
    if (!empty($_POST['amenities'])) {
        $amenities_list = explode(",", $_POST['amenities']);
        foreach ($amenities_list as $amenity_id) {
            $amenity_id = intval($amenity_id);
            if ($amenity_id > 0) {
                $amenity_query = "INSERT INTO meal_services_amenities (meal_service_id, amenity_id) VALUES ('$service_id', '$amenity_id')";
                mysqli_query($conn, $amenity_query);
            }
        }
    }
    // Insert Additional Images into `images` Table (Each Image in a Separate Row)
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['images']['name'][$key]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (in_array($file_ext, ["jpg", "jpeg", "png", "webp"])) {
                $new_filename = "" . uniqid() . "." . $file_ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                    $image_query = "INSERT INTO images (entity_type,entity_id, image_url) VALUES ('meal_service','$service_id', '$new_filename')";
                    mysqli_query($conn, $image_query);
                }
            }
        }
    }
    $_SESSION['success'] = "{$category} added successfully!";
    header("Location: meal-services.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dhaba/Tiffin/Cafe Registration</title>
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
</head>
<body>
    <?php require("./navbar.php"); ?>
    <!-- New Format -->
    <section class="form-section w-100">
        <p class="form-heading">Dhaba/Tiffin/Cafe Registration</p>
        <form action="./add-meal-services.php" class="form-container" method="POST" enctype="multipart/form-data" class="form">
            <div>
                <?php require("./show-message.php"); ?>
            </div>
            <h3>Basic Details</h3>
            <div class="form-group">
                <div>
                    <label>Service Name</label>
                    <input type="text" name="name" required>
                </div>
                <div>
                    <label>Service Type</label>
                    <div>
                        <select name="category" required>
                            <option value="Dhaba">Dhaba</option>
                            <option value="Cafe">Cafe</option>
                            <option value="Tiffin">Tiffin</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="opening_hours">Opening Time</label>
                    <input type="text" name="opening_hours" id="opening_hours" required>
                </div>
                <div>
                    <label for="closing_hours">Closing Time</label>
                    <input type="text" name="closing_hours" id="closing_hours" required>
                </div>
            </div>
            <h3>Contact Details</h3>
            <div class="form-group">
                <!-- Search Existing Address -->
                <div class="amenities-container relative-div">
                    <label>Search Address</label>
                    <input type="text" id="searchAddress" placeholder="Type to search..." autocomplete="off">
                    <div id="addressSuggestions" class="suggestion-box"></div>
                </div>
                <div>
                    <label>City</label>
                    <input type="text" name="city" id="city" pattern="^[a-zA-Z\s]+$" title="Only letters and spaces allowed" required>
                </div>
                <div>
                    <label>Village</label>
                    <input type="text" name="village" id="village">
                </div>
                <div>
                    <label>Post Office (PO)</label>
                    <input type="text" name="po" id="po">
                </div>
                <div>
                    <label>Tehsil</label>
                    <input type="text" name="tehsil" id="tehsil">
                </div>
                <div>
                    <label>District</label>
                    <input type="text" name="district" id="district" required>
                </div>
                <div>
                    <label>State</label>
                    <select name="state" id="state" required>
                        <option value="">Select State</option>
                        <optgroup label="States">
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                        </optgroup>
                        <optgroup label="Union Territories">
                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                            <option value="Chandigarh">Chandigarh</option>
                            <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Lakshadweep">Lakshadweep</option>
                            <option value="Puducherry">Puducherry</option>
                            <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                            <option value="Ladakh">Ladakh</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label>Pincode</label>
                    <input type="text" name="pincode" id="pincode" pattern="^\d{6}$" title="Enter a valid 6-digit pincode" required>
                </div>
                <div>
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" pattern="^[1-9][0-9]{9}$"
                        title="Enter a valid 10-digit phone number" required>
                </div>
                <input type="hidden" name="address_id" id="address_id">
            </div>
            <h3>About Service</h3>
            <div class="form-group">
                <div>
                    <label>Description (Dhaba/Tiffin/Cafe)</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
            </div>
            <h3>Facilities</h3>
            <div class="form-group">
                <div>
                    <label>Amenities</label>
                    <div class="amenities-container">
                        <input type="text" id="amenityInput" class="form-control" placeholder="Type an amenity..."
                            autocomplete="off">
                        <div id="suggestions" class="suggestion-box"></div>
                        <div id="amenitiesList" class="pill-container"></div>
                    </div>
                    <input type="hidden" name="amenities" id="amenitiesHidden">
                </div>
            </div>
            <h3>Property Photgraphs</h3>
            <div class="form-group">
                <div>
                    <label class="custum-file-upload" for="imageUploadMain">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                                <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill=""
                                        d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                                        clip-rule="evenodd" fill-rule="evenodd"></path>
                                </g>
                            </svg>
                        </div>
                        <div class="text">
                            <span>Dhaba/Tiffin/Cafe Front Image</span>
                        </div>
                        <input type="file" id="imageUploadMain" name="main_image"
                            accept="image/png, image/jpeg,image/webp, image/gif" multiple required>
                    </label>
                </div>
            </div>
            <div id="imagePreviewMain" class="preview-container"></div>
            <div class="form-group">
                <div>
                    <label class="custum-file-upload" for="imageUpload">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                                <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill=""
                                        d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                                        clip-rule="evenodd" fill-rule="evenodd"></path>
                                </g>
                            </svg>
                        </div>
                        <div class="text">
                            <span>Click to upload extra Dhaba/Tiffin/Cafe Images</span>
                        </div>
                        <input type="file" id="imageUpload" name="images[]"
                            accept="image/png, image/jpeg,image/webp, image/gif" multiple required>
                    </label>
                </div>
            </div>
            <div id="imagePreview" class="preview-container"></div>
            <button type="submit" class="custom-button">Add Dhaba/Tiffin/Cafe</button>
            <br>
            <br>
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
                    defaultTime: '09:00',
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
                    defaultTime: '19:00',
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
    </script>
    <script>
        $(document).ready(function() {
            let selectedAmenities = [];
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
                $("#amenitiesHidden").val(JSON.stringify(ids));
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
</body>
</html>