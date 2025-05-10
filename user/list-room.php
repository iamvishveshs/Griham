<?php
require("./check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room & Stay Details </title>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <div style="text-align: center;margin-top:20px">
        <h2> Add your Room Details</h2>
        <p>So that users can contact you</p>
    </div>
    <?php
    require("./show-message.php");
    ?>
    <form method="POST" id="myForm" class="form-container" enctype="multipart/form-data">
        <h3>Your Current Living Space </h3>
        <div class="form-group">
            <div>
                <label>Apartment/Room Name</label>
                <input type="text" name="apartment_name" placeholder="Enter Name" required>
            </div>
            <div>
                <label>Rent Amount ( in Rs.)</label>
                <input type="number" name="rent_amount" placeholder="Enter rent amount" required>
            </div>
            <div>
                <label>Occupancy</label>
                <select name="room_type" required>
                    <option value="Single">Single</option>
                    <option value="Shared">Shared</option>
                    <option value="Any">Any</option>
                </select>
            </div>
            <div>
                <label>Looking For</label>
                <select name="preferred_gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label>Stay Duration</label>
                <select name="lease_duration" required>
                    <option value="Short-term">Short-term</option>
                    <option value="Long-term">Long-term</option>
                    <option value="Flexible">Flexible</option>
                </select>
            </div>
            <div>
                <label>Guest Policy</label>
                <select name="guest_policy" required>
                    <option value="Strict">Strict</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Flexible">Flexible</option>
                </select>
            </div>
            <div>
                <label>Furnishing Status</label>
                <select name="furnishing_status" required>
                    <option value="Unfurnished">Unfurnished</option>
                    <option value="Semi-Furnished">Semi-Furnished</option>
                    <option value="Furnished">Furnished</option>
                </select>
            </div>
            <div>
                <label>Parking Availability</label>
                <select name="parking" required>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>
        </div>
        <h3>Lifestyle Preferences</h3>
        <div class="form-group">
            <div>
                <label>Smoking</label>
                <select name="smoking">
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                    <option value="Flexible">Flexible</option>
                </select>
            </div>
            <div>
                <label>Drinking</label>
                <select name="drinking">
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                    <option value="Flexible">Flexible</option>
                </select>
            </div>
            <div>
                <label>Pets</label>
                <select name="pets">
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                    <option value="Flexible">Flexible</option>
                </select>
            </div>
            <div>
                <label>Dietary Preference</label>
                <select name="dietary_preference">
                    <option value="No Preference">No Preference</option>
                    <option value="Vegetarian">Vegetarian</option>
                    <option value="Vegan">Vegan</option>
                    <option value="Non-Vegetarian">Non-Vegetarian</option>
                </select>
            </div>
            <div>
                <label>Daily Schedule</label>
                <select name="daily_schedule">
                    <option value="Flexible">Flexible</option>
                    <option value="Night Owl">Night Owl</option>
                    <option value="Early Riser">Early Riser</option>
                </select>
            </div>
            <div>
                <label>Your DOB</label>
                <input type="date" name="dob"  required>
            </div>
        </div>
        <h3>Facilities</h3>
        <div class="form-group">
            <div>
                <label>Amenities</label>
                <div class="amenities-container">
                    <input type="text" id="amenityInput" class="form-control" placeholder="Type an amenity..." autocomplete="off">
                    <div id="suggestions" class="suggestion-box"></div>
                    <div id="amenitiesList" class="pill-container"></div>
                </div>
                <input type="hidden" name="amenitiesHidden" id="amenitiesHidden" required>
            </div>
        </div>
        <h3>Room Address</h3>
        <div class="form-group">
            <div class="amenities-container">
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
                <input type="text" name="village" id="village" required>
            </div>
            <div>
                <label>Post Office (PO)</label>
                <input type="text" name="po" id="po" required>
            </div>
            <div>
                <label>Tehsil</label>
                <input type="text" name="tehsil" id="tehsil" required>
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
            <input type="hidden" name="address_id" id="address_id" value="">
        </div>
        <div class="form-group">
            <div>
                <label class="custum-file-upload" for="imageUpload">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                            <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                            <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill="" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </g>
                        </svg>
                    </div>
                    <div class="text">
                        <span>Click to upload Property Images (max 5)</span>
                    </div>
                    <input type="file" id="imageUpload" name="imageUpload[]" accept="image/png, image/jpeg,image/webp, image/gif" multiple required>
                </label>
            </div>
        </div>
        <div id="imagePreview" class="preview-container"></div>
        <h3>Description</h3>
        <div class="form-group">
                <label>Description</label>
                <textarea type="text" id="description" name="description" autocomplete="off">I am looking for a roommate for my room.</textarea>
        </div>
        <button type="submit" id="submitForm" class="btn">Save</button>
    </form>
    <?php require("./footer.php"); ?>
    <?php
    require("./script-files.php");
    ?>
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
        document.getElementById("myForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevents page refresh
            let formData = new FormData(this);
            fetch("process_roommate_accommodation.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000 // Auto close in 2 seconds
                    }).then(() => {
                        window.location.href = "show_room_detail.php";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: "error",
                    title: "Server Error",
                    text: "Something went wrong! Please try again."
                });
            });
        });
    </script>
</body>
</html>