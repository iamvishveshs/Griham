<?php
// Include necessary files and database connection
require("./check.php");

// Clean input function
function clean_input($input, $conn)
{
    return mysqli_real_escape_string($conn, trim($input));
}

// ✅ Fetch Filters from GET Request
$location_input = isset($_GET['location']) ? clean_input($_GET['location'], $conn) : '';
$category_filter = isset($_GET['category']) ? clean_input($_GET['category'], $conn) : '';
$subcategory_filter = isset($_GET['subcategory']) ? clean_input($_GET['subcategory'], $conn) : '';

// ✅ Handle Comma-Separated Locations
$location_parts = explode(",", $location_input);
$location = trim($location_parts[0]);  // First part (City/Tehsil/District)
$state = isset($location_parts[1]) ? trim($location_parts[1]) : ''; // Second part (State)

// ✅ Dynamic Query Construction
$where_clauses = [];
$params = [];
$types = "";

// Location Filters
if (!empty($location)) {
    $like_location = "%$location%";
    $where_clauses[] = "(adr.city LIKE ? OR adr.village LIKE ? OR adr.po LIKE ? OR adr.tehsil LIKE ? OR adr.district LIKE ?)";
    array_push($params, $like_location, $like_location, $like_location, $like_location, $like_location);
    $types .= "sssss";
}

// State Filter
if (!empty($state)) {
    $where_clauses[] = "adr.state LIKE ?";
    $params[] = "%$state%";
    $types .= "s";
}

// Category Filter
if (!empty($category_filter)) {
    $where_clauses[] = "ms.category_id LIKE ?";
    $params[] = $category_filter;
    $types .= "s";
}

// Subcategory Filter
if (!empty($subcategory_filter)) {
    $where_clauses[] = "ms.subcategory_id LIKE ?";
    $params[] = $subcategory_filter;
    $types .= "s";
}

// Finalize Query
$query = "SELECT 
            ms.service_id, 
            ms.name, 
            ms.contact_details, 
            ms.opening_time, 
            ms.closing_time, 
            ms.is_24_7, 
            adr.city, 
            adr.tehsil, 
            adr.district, 
            adr.state, 
            CONCAT_WS(', ', NULLIF(adr.village, ''), NULLIF(adr.po, ''), adr.tehsil, adr.district, adr.state, NULLIF(adr.pincode, '')) AS full_address, 
            esc.category_name, 
            COALESCE(esub.subcategory_name, '') AS subcategory_name
          FROM 
            emergency_services ms
          LEFT JOIN 
            addresses adr ON ms.address_id = adr.address_id
          INNER JOIN 
            emergency_service_categories esc ON ms.category_id = esc.category_id
          LEFT JOIN 
            emergency_subcategories esub ON ms.subcategory_id = esub.subcategory_id";

// Add WHERE conditions if there are filters
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

// Group by service ID to avoid duplicates
$query .= " GROUP BY ms.service_id";

// ✅ Prepare Statement
$stmt = mysqli_prepare($conn, $query);

// Dynamically bind parameters
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

// ✅ Execute Query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Listings</title>
    <?php require("./style-files.php"); ?>
</head>

<body>

    <?php require("./navbar.php"); ?>

    <div class="full-container">
        <div>
            <button class="toggle-btn" onclick="toggleSidebar()">☰ Filters</button>
            <div class="sidebar" id="sidebar">
                <h3>Search Filters</h3>
                <form method="GET" action="emergency.php">
                    <button type="submit" name="reset" class="btn-reset">Reset Filters</button>

                    <!-- Location Filter -->
                    <div class="form-group suggestion-container">
                        <label>Location</label>
                        <input type="text" name="location" id="location-input" placeholder="Enter city" autocomplete="off"
                            value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                        <div class="suggestions"></div>
                    </div>
                    <div class="form-group ">
                        <label for="category">Category</label>
                        <select name="category" id="category">
                            <option value="">Select</option>
                            <option value="1" <?php if (isset($_GET['category']) && $_GET['category'] == "1") echo "selected"; ?>>Hospital</option>
                            <option value="2" <?php if (isset($_GET['category']) && $_GET['category'] == "2") echo "selected"; ?>>Fire Brigade</option>
                            <option value="3" <?php if (isset($_GET['category']) && $_GET['category'] == "3") echo "selected"; ?>>Police Station</option>
                        </select>
                    </div>
                    <div id="subcategory-container" style="display: none;" class="form-group">
                        <label for="subcategory">Subcategory</label>
                        <select name="subcategory" id="subcategory">
                            <option value="" >Select Subcategory</option>
                            <option value="1" <?php if (isset($_GET['subcategory']) && $_GET['subcategory'] == "1") echo "selected"; ?>>Private</option>
                            <option value="2" <?php if (isset($_GET['subcategory']) && $_GET['subcategory'] == "2") echo "selected"; ?>>Government</option>
                            <option value="3" <?php if (isset($_GET['subcategory']) && $_GET['subcategory'] == "3") echo "selected"; ?>>Clinic</option>
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
                <h2>Emegency Listings</h2>
            </div>
            <div class="listing-container">
                <?php if (mysqli_num_rows($result)> 0) {

                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="custom-card">
                            <div class="custom-card-content">
                                <div>
                                    <span class="custom-badge"><?php echo htmlspecialchars($row['city']); ?> </span>
                                    <span class=" custom-price"><?php echo htmlspecialchars($row['category_name']) ?></span>
                                    <h3><?php echo htmlspecialchars($row['name']); 
                                    if(isset($_row['subcategory_name'])){echo "(".htmlspecialchars($row['subcategory_name']).")";}?></h3>
                                    <p class="custom-loc"><strong><i class="fa-solid fa-location-dot"></i></strong> <?php echo htmlspecialchars($row['full_address']); ?></p>
                                    <?php if ($row['is_24_7']) {
                                        echo "<p><strong>Hours:</strong> Open 24X7</p>";
                                    } else { echo "<p><strong>Hours: </strong>".date('g:i A', strtotime(htmlspecialchars($row['opening_time']))) . " - " . date('g:i A', strtotime(htmlspecialchars($row['closing_time'])))."</p>";
                                    } ?>
                                </div>
                                <a href="tel:<?php echo $row['contact_details']; ?>"
                                    class="custom-button"><i class="fa fa-phone"></i>&nbsp;<?php echo $row['contact_details']; ?></a>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="empty-state">
                        <div class="empty-state__content">
                            <div class="empty-state__message">No Service found matching your criteria.</div>
                            <div class="empty-state__help">Try adjusting your Filters.</div>
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
        const categorySelect = document.getElementById("category");
        const subcategorySelect = document.getElementById("subcategory");
        const subcategoryContainer = document.getElementById("subcategory-container");

        categorySelect.addEventListener("change", function() {
            if (this.value === "1") {
                // Show subcategories when Hospital is selected and make them required
                subcategoryContainer.style.display = "block";
            } else {
                // Hide subcategories for other categories and remove the required attribute
                subcategoryContainer.style.display = "none";
                subcategorySelect.value = ""; // Reset subcategory selection
            }
        });

        // Trigger the change event on page load to set the initial state
        categorySelect.dispatchEvent(new Event("change"));
    </script>
    <?php require("./footer.php"); ?>
</body>

</html>