<?php
require("./check.php");
$_SESSION['page'] = $_SERVER['PHP_SELF'];
$user_id = $_SESSION['user_id'] ?? 0; // Ensure user ID is set
if (isset($_GET['reset'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?location=" . $_GET['location']);
    exit();
}
require('./page-parameter-access.php');
// Function to Sanitize Input
function clean_input($data, $conn)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
// Fetch Filters from GET Request
$location_input = isset($_GET['location']) ? clean_input($_GET['location'], $conn) : '';
$property_type_filter = isset($_GET['property_type']) ? clean_input($_GET['property_type'], $conn) : '';
// Handle Comma-Separated Locations
$location_parts = explode(",", $location_input);
$location = trim($location_parts[0]);  // First part (City/Tehsil/District)
$state = isset($location_parts[1]) ? trim($location_parts[1]) : ''; // Second part (State)
// SQL Query with Joins & Filters
$query = "SELECT
            a.service_id,
            a.user_id,
            a.property_name,
            a.property_type,
            a.contact_number,
            a.main_image,
            a.description,
            a.created_at,
            adr.city,
            adr.tehsil,
            adr.district,
            adr.state,
            CONCAT_WS(', ', NULLIF(adr.village, ''), NULLIF(adr.po, ''), adr.tehsil, adr.district, adr.state, NULLIF(adr.pincode, '')) AS full_address
          FROM accommodation_services a
          LEFT JOIN addresses adr ON a.address_id = adr.address_id
          WHERE (adr.city LIKE ?
                 OR adr.village LIKE ?
                 OR adr.po LIKE ?
                 OR adr.tehsil LIKE ?
                 OR adr.district LIKE ?)";
// Add State Condition if Provided
if (!empty($state)) {
    $query .= " AND adr.state LIKE ?";
}
// Include Property Type Filter
if (!empty($property_type_filter)) {
    $query .= " AND a.property_type = ?";
}
$query .= " GROUP BY a.service_id, adr.address_id";
// Prepare Statement
$stmt = mysqli_prepare($conn, $query);
$like_location = "%$location%";
$like_state = "%$state%";
// Bind Parameters Based on Provided Filters
if (!empty($state) && !empty($property_type_filter)) {
    mysqli_stmt_bind_param($stmt, "sssssss", $like_location, $like_location, $like_location, $like_location, $like_location, $like_state, $property_type_filter);
} elseif (!empty($state)) {
    mysqli_stmt_bind_param($stmt, "ssssss", $like_location, $like_location, $like_location, $like_location, $like_location, $like_state);
} elseif (!empty($property_type_filter)) {
    mysqli_stmt_bind_param($stmt, "ssssss", $like_location, $like_location, $like_location, $like_location, $like_location, $property_type_filter);
} else {
    mysqli_stmt_bind_param($stmt, "sssss", $like_location, $like_location, $like_location, $like_location, $like_location);
}
// Execute Query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_results = mysqli_num_rows($result);
// New array to store saved service IDs
$saved_services = [];
$saved_query = "SELECT service_id FROM saved_services WHERE user_id = ?";
$saved_stmt = mysqli_prepare($conn, $saved_query);
mysqli_stmt_bind_param($saved_stmt, "i", $user_id);
mysqli_stmt_execute($saved_stmt);
$saved_result = mysqli_stmt_get_result($saved_stmt);
while ($row = mysqli_fetch_assoc($saved_result)) {
    $saved_services[] = $row['service_id'];  // Store saved service IDs in an array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accommodation Listings</title>
    <?php require("./style-files.php"); ?>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <div class="full-container">
        <div>
            <button class="toggle-btn" onclick="toggleSidebar()">☰ Filters</button>
            <div class="sidebar" id="sidebar">
                <h3>Search Filters</h3>
                <form method="GET" action="browse-listings.php">
                    <button type="submit" name="reset" class="btn-reset">Reset Filters</button>
                    <!-- Location Filter -->
                    <div class="form-group suggestion-container">
                        <label>Location</label>
                        <input type="text" name="location" id="location-input" placeholder="Enter city" autocomplete="off"
                            value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                        <div class="suggestions"></div>
                    </div>
                    <!-- Property Type Filter -->
                    <div class="form-group">
                        <label>Property Type</label>
                        <select name="property_type">
                            <option value="">Select Property Type</option>
                            <option value="Apartment" <?php if (isset($_GET['property_type']) && $_GET['property_type'] == "Apartment") echo "selected"; ?>>Apartment</option>
                            <option value="PG" <?php if (isset($_GET['property_type']) && $_GET['property_type'] == "PG") echo "selected"; ?>>PG</option>
                            <option value="Room" <?php if (isset($_GET['property_type']) && $_GET['property_type'] == "Room") echo "selected"; ?>>Room</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="custom-button">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Listings Section -->
        <div class="listing-section">
            <div class="content">
                <h2>Accommodation Listings</h2>
            </div>
            <div class="listing-container">
                <?php if ($total_results > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $service_id = $row['service_id'];
                        $is_saved = in_array($service_id, $saved_services) ? 'saved' : ''; ?>
                        <div class="custom-card">
                            <img src="../assets/uploads/accommodation-images/<?php echo !empty($row['main_image']) ?  htmlspecialchars($row['main_image']) : "default_property.jpeg"; ?>"
                                class="Property image" alt="Property image" />
                            <div class="custom-card-content">
                                <div>
                                    <span class="custom-badge"><?php echo htmlspecialchars($row['property_type']); ?> </span>
                                        <span class="custom-price save-btn <?php echo $is_saved; ?>" data-id="<?php echo $service_id; ?>" data-type="accommodation">
                                            <i class="<?php echo $is_saved ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>
                                        </span>
                                    <h3><?php echo htmlspecialchars($row['property_name']); ?></h3>
                                    <p class="custom-loc"><strong><i class="fa-solid fa-location-dot"></i></strong> <?php echo htmlspecialchars($row['full_address']); ?></p>
                                </div>
                                <a href="property-details.php?token=<?php echo urlencode(base64_encode($row['service_id'])); ?>"
                                    class="custom-button">View Details</a>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="empty-state">
                        <div class="empty-state__content">
                            <div class="empty-state__message">No Accommodations found matching your criteria.</div>
                            <div class="empty-state__help">Try adjusting your filters.</div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Sidebar Toggle -->
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("open");
        }
    </script>
    <!-- Search Suggestion Logic -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $("#location-input").on("input", function() {
                let query = $(this).val().trim();
                if (query.length >= 3) {
                    $.ajax({
                        url: "fetch_addresses.php",
                        type: "POST",
                        data: {
                            query: query
                        },
                        success: function(data) {
                            if (data.trim() !== "") {
                                $(".suggestions").html("<ul>" + data + "</ul>").show();
                            } else {
                                $(".suggestions").hide();
                            }
                        }
                    });
                } else {
                    $(".suggestions").hide();
                }
            });
            $(document).on("click", ".suggestion-item", function() {
                $("#location-input").val($(this).text());
                $(".suggestions").hide();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".save-btn").click(function() {
                var button = $(this);
                var serviceId = button.attr("data-id");
                var serviceType = button.attr("data-type");
                $.ajax({
                    url: "save_service.php",
                    type: "POST",
                    data: {
                        service_id: serviceId,
                        service_type: serviceType
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "saved") {
                            Swal.fire("Saved!", "Service has been added to your saved list.", "success");
                            button.addClass("saved").html('<i class="fa-solid fa-bookmark"></i>');
                        } else if (response.status === "removed") {
                            Swal.fire("Removed!", "Service has been removed from your saved list.", "info");
                            button.removeClass("saved").html('<i class="fa-regular fa-bookmark"></i>');
                        } else {
                            Swal.fire("Error!", "Something went wrong. Try again.", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error!", "Server error. Try again.", "error");
                    },
                });
            });
        });
    </script>
    <?php require("./footer.php"); ?>
</body>
</html>