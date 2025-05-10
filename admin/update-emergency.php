<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_emergency'])) {

    $service_id = mysqli_real_escape_string($conn, trim($_POST['service_id']));
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

    // ✅ Validate Phone Number
    if (!preg_match('/^[1-9][0-9]{9}$/', $contact_details)) {
        $_SESSION['error'] = "Invalid contact number format!";
        header("Location: ./update-emergency.php");
        exit();
    }

    // ✅ Check if Address Exists in `addresses` Table
    $address_query = "SELECT address_id FROM addresses WHERE LOWER(city) = '" . $city . "' AND LOWER(village) = '" . $village . "' AND LOWER(po) = '" . $po . "' AND LOWER(tehsil) = '" . $tehsil . "' AND LOWER(district) = '" . $district . "' AND LOWER(state) = '" . $state . "' AND pincode = '" . $pincode . "'";
    $address_result = mysqli_query($conn, $address_query);

    if (mysqli_num_rows($address_result) > 0) {
        $address_row = mysqli_fetch_assoc($address_result);
        $address_id = $address_row['address_id'];
    } else {
        // ✅ Insert New Address
        $insert_address_query = "INSERT INTO addresses (city, village, po, tehsil, district, state, pincode)
                                 VALUES ('$city', '$village', '$po', '$tehsil', '$district', '$state', '$pincode')";
        mysqli_query($conn, $insert_address_query);
        $address_id = mysqli_insert_id($conn);
    }

    // ✅ Ensure Category Exists in `emergency_service_categories` Table
    $category_query = "SELECT category_id FROM emergency_service_categories WHERE category_id = '$category'";
    $category_result = mysqli_query($conn, $category_query);
    if (mysqli_num_rows($category_result) == 0) {
        $_SESSION['error'] = "Invalid category!";
        header("Location: ./update-emergency.php");
        exit();
    }

    // ✅ Update Data in `Emergency Services`
    $query = "UPDATE `emergency_services` 
              SET `name` = '$name',
                  `address_id` = '$address_id',
                  `contact_details` = '$contact_details',
                  `opening_time` = " . ($opening_hours ? "'$opening_hours'" : "NULL") . ",
                  `closing_time` = " . ($closing_hours ? "'$closing_hours'" : "NULL") . ",
                  `is_24_7` = '$is_24_7',
                  `category_id` = '$category',
                  `subcategory_id` = " . ($subcategory ? "'$subcategory'" : "NULL") . "
              WHERE `service_id` = '$service_id'";

    if (!mysqli_query($conn, $query)) {
        $_SESSION['error'] = "Error updating Service: " . mysqli_error($conn);
        header("Location: ./update-emergency.php");
        exit();
    } else {
        $_SESSION['success'] = "Service updated successfully!";
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
if(isset($_GET['token'])){
$encoded_service_id = $_GET['token'];

// Decode URL-encoded value
$decoded_base64 = urldecode($encoded_service_id);

// Decode Base64 to get the original service_id
$service_id = base64_decode($decoded_base64);
$query = "SELECT es.*, 
                 a.city, a.village, a.po, a.tehsil, a.district, a.state, a.pincode 
          FROM emergency_services es 
          INNER JOIN addresses a ON es.address_id = a.address_id
          WHERE es.service_id = $service_id";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Service not found!";
    header("Location: ./emergency.php");
    exit();
}

$data = mysqli_fetch_assoc($result);
$is_24_7 = $data['is_24_7'];
$opening_time = $is_24_7 == 0 ? htmlspecialchars($data['opening_time']) : ''; // Preload if not 24/7
$closing_time = $is_24_7 == 0 ? htmlspecialchars($data['closing_time']) : ''; // Preload if not 24/7
}
else
{
    $_SESSION['error'] = "Service not found!";
    header("Location: ./emergency.php");
    exit();
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


        <form action="./update-emergency.php" class="form-container" method="POST" class="form">
            <div>
                <?php require("./show-message.php"); ?>
            </div>
            <h3>Basic Details</h3>
            <div class="form-group">
                <!-- Service Name -->
                <div>
                    <label>Service Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
                </div>

                <!-- Category -->
                <div>
                    <label for="category">Category</label>
                    <select name="category" id="category" required>
                        <option value="1" <?php echo $data['category_id'] == 1 ? 'selected' : ''; ?>>Hospital</option>
                        <option value="2" <?php echo $data['category_id'] == 2 ? 'selected' : ''; ?>>Fire Brigade</option>
                        <option value="3" <?php echo $data['category_id'] == 3 ? 'selected' : ''; ?>>Police Station</option>
                    </select>
                </div>

                <!-- Subcategory (Shown for Hospitals only) -->
                <div id="subcategory-container" style="<?php echo $data['category_id'] == 1 ? 'display: block;' : 'display: none;'; ?>">
                    <label for="subcategory">Subcategory</label>
                    <select name="subcategory" id="subcategory">
                        <option selected value="">Select Subcategory</option>
                        <option value="1" <?php echo $data['subcategory_id'] == 1 ? 'selected' : ''; ?>>Private</option>
                        <option value="2" <?php echo $data['subcategory_id'] == 2 ? 'selected' : ''; ?>>Government</option>
                        <option value="3" <?php echo $data['subcategory_id'] == 3 ? 'selected' : ''; ?>>Clinic</option>
                    </select>
                </div>
                <div>
                    <label for="is247">24/7</label>
                    <select name="is247" id="is247" required>
                        <option value="1" <?php echo $is_24_7 ? 'selected' : ''; ?>>Yes</option>
                        <option value="0" <?php echo !$is_24_7 ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div id="opening_container" style="<?php echo $is_24_7 ? 'display: none;' : 'display: block;'; ?>">
                    <label for="opening_hours">Opening Time</label>
                    <input type="text" name="opening_hours" id="opening_hours" value="<?php echo $opening_time; ?>">
                </div>

                <div id="closing_container" style="<?php echo $is_24_7 ? 'display: none;' : 'display: block;'; ?>">
                    <label for="closing_hours">Closing Time</label>
                    <input type="text" name="closing_hours" id="closing_hours" value="<?php echo $closing_time; ?>">
                </div>


            </div>

            <h3>Contact Details</h3>
            <div class="form-group">
                <div class="amenities-container relative-div">
                    <label>Search Address</label>
                    <input type="text" id="searchAddress" placeholder="Type to search..." autocomplete="off">
                    <div id="addressSuggestions" class="suggestion-box"></div>
                </div>
                <!-- City -->
                <div>
                    <label>City</label>
                    <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($data['city']); ?>" required>
                </div>

                <!-- Village -->
                <div>
                    <label>Village</label>
                    <input type="text" name="village" id="village" value="<?php echo htmlspecialchars($data['village']); ?>">
                </div>

                <!-- Post Office (PO) -->
                <div>
                    <label>Post Office (PO)</label>
                    <input type="text" name="po" id="po" value="<?php echo htmlspecialchars($data['po']); ?>">
                </div>

                <!-- Tehsil -->
                <div>
                    <label>Tehsil</label>
                    <input type="text" name="tehsil" id="tehsil" value="<?php echo htmlspecialchars($data['tehsil']); ?>">
                </div>

                <!-- District -->
                <div>
                    <label>District</label>
                    <input type="text" name="district" id="district" value="<?php echo htmlspecialchars($data['district']); ?>" required>
                </div>

                <!-- State -->
                <div>
                    <label>State</label>
                    <select name="state" id="state" required>
                        <option value="">Select State</option>
                        <option value="Himachal Pradesh" <?php echo $data['state'] == 'Himachal Pradesh' ? 'selected' : ''; ?>>Himachal Pradesh</option>
                        <!-- Add other states as needed -->
                    </select>
                </div>

                <!-- Pincode -->
                <div>
                    <label>Pincode</label>
                    <input type="text" name="pincode" id="pincode" value="<?php echo htmlspecialchars($data['pincode']); ?>" required>
                </div>

                <!-- Contact Number -->
                <div>
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($data['contact_details']); ?>" required>
                </div>
            </div>

            <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($data['service_id']); ?>">

            <button type="submit" name="update_emergency" class="custom-button">Update Service</button>
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
        // Reference DOM elements
        const is247Select = document.getElementById("is247");
        const openingContainer = document.getElementById("opening_container");
        const closingContainer = document.getElementById("closing_container");
        const openingInput = document.getElementById("opening_hours");
        const closingInput = document.getElementById("closing_hours");

        // Event listener for changes in the is247 dropdown
        is247Select.addEventListener("change", function() {
            if (this.value === "1") {
                // Hide opening and closing fields and clear values
                openingContainer.style.display = "none";
                closingContainer.style.display = "none";
                openingInput.value = "";
                closingInput.value = "";
            } else if (this.value === "0") {
                // Show opening and closing fields
                openingContainer.style.display = "block";
                closingContainer.style.display = "block";
            }
        });

        // Trigger change event on page load to set correct visibility
        is247Select.dispatchEvent(new Event("change"));
    </script>
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