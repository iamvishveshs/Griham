<?php
require("./check.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_emergency'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $subcategory = isset($_POST['subcategory']) && !empty(trim($_POST['subcategory'])) ? mysqli_real_escape_string($conn, trim($_POST['subcategory'])) : null;
    $opening_hours = isset($_POST['opening_hours']) && !empty(trim($_POST['opening_hours'])) ? mysqli_real_escape_string($conn, trim($_POST['opening_hours'])) : null;
    $closing_hours = isset($_POST['closing_hours']) && !empty(trim($_POST['closing_hours'])) ? mysqli_real_escape_string($conn, trim($_POST['closing_hours'])) : null;
    $is_24_7 = isset($_POST['is247']) && !empty(trim($_POST['is247'])) ? mysqli_real_escape_string($conn, trim($_POST['is247'])) : FALSE;
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $village = mysqli_real_escape_string($conn, trim($_POST['village']));
    $po = mysqli_real_escape_string($conn, trim($_POST['po']));
    $tehsil = mysqli_real_escape_string($conn, trim($_POST['tehsil']));
    $district = mysqli_real_escape_string($conn, trim($_POST['district']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));
    $pincode = mysqli_real_escape_string($conn, trim($_POST['pincode']));
    $contact_details = mysqli_real_escape_string($conn, trim($_POST['contact_number']));
    // Validate Phone Number
    if (!preg_match('/^[1-9][0-9]{9}$/', $contact_details)) {
        $_SESSION['error'] = "Invalid contact number format!";
        header("Location: ./add-emergency.php");
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
    // Ensure Category Exists in `emergency_service_categories` Table
    $category_query = "SELECT category_id FROM emergency_service_categories WHERE category_id = '$category'";
    $category_result = mysqli_query($conn, $category_query);
    if (mysqli_num_rows($category_result) == 0) {
        $_SESSION['error'] = "Invalid category!";
        header("Location: ./add-emergency.php");
        exit();
    }
    // Insert Data into `Emergency Services`
    $query = "INSERT INTO `emergency_services`(`name`, `address_id`, `contact_details`, `opening_time`, `closing_time`, `is_24_7`, `category_id`, `subcategory_id`)
              VALUES ('$name', '$address_id', '$contact_details', " . ($opening_hours ? "'$opening_hours'" : "NULL") . ", " . ($closing_hours ? "'$closing_hours'" : "NULL") . ", '$is_24_7', '$category', " . ($subcategory ? "'$subcategory'" : "NULL") . ")";
    if (!mysqli_query($conn, $query)) {
        $_SESSION['error'] = "Error adding Service: " . mysqli_error($conn);
        header("Location: ./add-emergency.php");
        exit();
    } else {
        $_SESSION['success'] = "Service added successfully!";
        if ($category == "1") {
            header("Location: ./hospital.php");
            exit();
        } elseif ($category == "3") {
            header("Location: ./police_station.php");
            exit();
        } elseif ($category == "2") {
            header("Location: ./fire_station.php");
            exit();
        } else {
            header("Location: ./emergency.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Add Emegency Service</title>
    <?php
    require("./style-files.php");
    ?>
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
</head>
<body>
    <?php require("./navbar.php"); ?>
    <!-- New Format -->
    <section class="form-section w-100">
        <p class="form-heading">Emergency Service</p>
        <form action="./add-emergency.php" class="form-container" method="POST" class="form">
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
                    <label for="category">Category</label>
                    <select name="category" id="category" required>
                        <option selected>Select</option>
                        <option value="1">Hospital</option>
                        <option value="2">Fire Brigade</option>
                        <option value="3">Police Station</option>
                    </select>
                </div>
                <div id="subcategory-container" style="display: none;">
                    <label for="subcategory">Subcategory</label>
                    <select name="subcategory" id="subcategory">
                        <option selected value="">Select Subcategory</option>
                        <option value="1">Private</option>
                        <option value="2">Government</option>
                        <option value="3">Clinic</option>
                    </select>
                </div>
                <div>
                    <label for="is247">24/7</label>
                    <select name="is247" id="is247" required>
                        <option selected value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div id="opening_container" style="display: none;">
                    <label for="opening_hours">Opening Time</label>
                    <input type="text" name="opening_hours" id="opening_hours">
                </div>
                <div id="closing_container" style="display: none;">
                    <label for="closing_hours">Closing Time</label>
                    <input type="text" name="closing_hours" id="closing_hours">
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
            <button type="submit" name="add_emergency" class="custom-button">Add Service</button>
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
        const is247Select = document.getElementById("is247");
        const openingHoursField = document.getElementById("opening_hours");
        const closingHoursField = document.getElementById("closing_hours");
        const openingContainer = document.getElementById("opening_container");
        const closingContainer = document.getElementById("closing_container");
        is247Select.addEventListener("change", function() {
            if (this.value === "1") {
                // Hide opening and closing hours, and remove 'required' attribute
                openingContainer.style.display = "none";
                closingContainer.style.display = "none";
                openingHoursField.removeAttribute("required");
                closingHoursField.removeAttribute("required");
            } else if (this.value === "0") {
                // Show opening and closing hours, and add 'required' attribute
                openingContainer.style.display = "block";
                closingContainer.style.display = "block";
                openingHoursField.setAttribute("required", "true");
                closingHoursField.setAttribute("required", "true");
            } else {
                // Default behavior for "Select" option
                openingContainer.style.display = "none";
                closingContainer.style.display = "none";
                openingHoursField.removeAttribute("required");
                closingHoursField.removeAttribute("required");
            }
        });
        // Initialize fields to hidden and not required by default
        openingContainer.style.display = "none";
        closingContainer.style.display = "none";
        openingHoursField.removeAttribute("required");
        closingHoursField.removeAttribute("required");
    </script>
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
    <script>
        const categorySelect = document.getElementById("category");
        const subcategorySelect = document.getElementById("subcategory");
        const subcategoryContainer = document.getElementById("subcategory-container");
        categorySelect.addEventListener("change", function() {
            if (this.value === "1") {
                // Show subcategories when Hospital is selected and make them required
                subcategoryContainer.style.display = "block";
                subcategorySelect.setAttribute("required", "true");
            } else {
                // Hide subcategories for other categories and remove the required attribute
                subcategoryContainer.style.display = "none";
                subcategorySelect.removeAttribute("required");
                subcategorySelect.value = ""; // Reset subcategory selection
            }
        });
        // Trigger the change event on page load to set the initial state
        categorySelect.dispatchEvent(new Event("change"));
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