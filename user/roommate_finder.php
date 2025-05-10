<?php
require("./check.php");
$_SESSION['page'] = $_SERVER['PHP_SELF'];
if (isset($_GET['reset'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?location=" . $_GET['location']);
    exit();
}
function clean_input($data, $conn) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
// Get logged-in user ID
$logged_in_user_id = $_SESSION['user_id'];
require('./page-parameter-access.php');
// Fetch filters if applied
$location_filter = isset($_GET['location']) ? clean_input($_GET['location'], $conn) : '';
$budget_filter = isset($_GET['budget']) ? clean_input($_GET['budget'], $conn) : '';
$room_type_filter = isset($_GET['room_type']) ? clean_input($_GET['room_type'], $conn) : '';
$lease_duration_filter = isset($_GET['lease_duration']) ? clean_input($_GET['lease_duration'], $conn) : '';
$gender_filter = isset($_GET['gender']) ? clean_input($_GET['gender'], $conn) : '';
$smoking_filter = isset($_GET['smoking']) ? clean_input($_GET['smoking'], $conn) : '';
$drinking_filter = isset($_GET['drinking']) ? clean_input($_GET['drinking'], $conn) : '';
$pets_filter = isset($_GET['pets']) ? clean_input($_GET['pets'], $conn) : '';
$dietary_filter = isset($_GET['dietary_preference']) ? clean_input($_GET['dietary_preference'], $conn) : '';
$daily_schedule_filter = isset($_GET['daily_schedule']) ? clean_input($_GET['daily_schedule'], $conn) : '';
// Base query to fetch roommate listings (from roommate_accommodations)
$query = "SELECT
            ra.*,
            u.user_id,
            u.name,
            u.profile_pic,
            CONCAT(ad.village, ', ', ad.po, ', ', ad.tehsil, ', ', ad.district, ', ', ad.state) AS full_address,
            ad.pincode
        FROM roommate_accommodations ra
        JOIN addresses ad ON ra.address_id = ad.address_id
        JOIN users u ON ra.user_id = u.user_id
        WHERE u.user_id != '$logged_in_user_id'"; // Exclude logged-in user
// Dynamically add filters only if they are set
$conditions = [];
// Location filtering: Check for any comma-separated value
if (!empty($location_filter)) {
    // City filtering:
if (!empty($city_filter)) {
    $conditions[] = "ad.city LIKE '%$city_filter%'";
}
}
if (!empty($budget_filter)) {
    $conditions[] = "ra.rent_amount <= " . intval($budget_filter);
}
if (!empty($room_type_filter)) {
    $conditions[] = "ra.room_type = '$room_type_filter'";
}
if (!empty($lease_duration_filter)) {
    $conditions[] = "ra.lease_duration = '$lease_duration_filter'";
}
if (!empty($gender_filter)) {
    $conditions[] = "ra.preferred_gender = '$gender_filter'";
}
if (!empty($smoking_filter)) {
    $conditions[] = "ra.smoking = '$smoking_filter'";
}
if (!empty($drinking_filter)) {
    $conditions[] = "ra.drinking = '$drinking_filter'";
}
if (!empty($pets_filter)) {
    $conditions[] = "ra.pets = '$pets_filter'";
}
if (!empty($daily_schedule_filter)) {
    $conditions[] = "ra.daily_schedule = '$daily_schedule_filter'";
}
if (!empty($dietary_filter)) {
    $conditions[] = "ra.dietary_preference = '$dietary_filter'";
}
// Append conditions to query only if there are filters
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
// Sort results by rent (low to high)
$query .= " ORDER BY ra.rent_amount ASC";
// Run query
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roommate Finder</title>
    <?php require("./style-files.php"); ?>
    <style></style>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <div class="full-container">
        <div>
            <button class="toggle-btn" onclick="toggleSidebar()">☰ Filters</button>
            <div class="sidebar" id="sidebar">
                <h3>Search Filters</h3>
                <form method="GET" action="roommate_finder.php">
                    <button type="submit" name="reset" class="btn-reset">Reset Filters</button>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="location" placeholder="Enter City" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Budget (Max Rent in Rs.)</label>
                        <input type="number" name="budget" placeholder="Max Rent" value="<?php echo isset($_GET['budget']) ? htmlspecialchars($_GET['budget']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Occupancy</label>
                        <select name="room_type">
                            <option value="">Select Room Type</option>
                            <option value="Single" <?php if (isset($_GET['room_type']) && $_GET['room_type'] == "Single") echo "selected"; ?>>Single</option>
                            <option value="Shared" <?php if (isset($_GET['room_type']) && $_GET['room_type'] == "Shared") echo "selected"; ?>>Shared</option>
                            <option value="Any" <?php if (isset($_GET['room_type']) && $_GET['room_type'] == "Any") echo "selected"; ?>>Any</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lease Duration</label>
                        <select name="lease_duration">
                            <option value="">Select Lease Duration</option>
                            <option value="Short-term" <?php if (isset($_GET['lease_duration']) && $_GET['lease_duration'] == "Short-term") echo "selected"; ?>>Short-term</option>
                            <option value="Long-term" <?php if (isset($_GET['lease_duration']) && $_GET['lease_duration'] == "Long-term") echo "selected"; ?>>Long-term</option>
                            <option value="Flexible" <?php if (isset($_GET['lease_duration']) && $_GET['lease_duration'] == "Flexible") echo "selected"; ?>>Flexible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Your Gender</label>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?php if (isset($_GET['gender']) && $_GET['gender'] == "Male") echo "selected"; ?>>Male</option>
                            <option value="Female" <?php if (isset($_GET['gender']) && $_GET['gender'] == "Female") echo "selected"; ?>>Female</option>
                            <option value="Other" <?php if (isset($_GET['gender']) && $_GET['gender'] == "Other") echo "selected"; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Daily schedule</label>
                        <select name="daily_schedule">
                            <option value="">Select</option>
                            <option value="Night Owl" <?php if (isset($_GET['daily_schedule']) && $_GET['daily_schedule'] == "Night Owl") echo "selected"; ?>>Night Owl</option>
                            <option value="Early Riser" <?php if (isset($_GET['daily_schedule']) && $_GET['daily_schedule'] == "Early Riser") echo "selected"; ?>>Early Riser</option>
                            <option value="Flexible" <?php if (isset($_GET['daily_schedule']) && $_GET['daily_schedule'] == "Flexible") echo "selected"; ?>>Flexible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Smoking</label>
                        <select name="smoking">
                            <option value="">Select</option>
                            <option value="Yes" <?php if (isset($_GET['smoking']) && $_GET['smoking'] == "Yes") echo "selected"; ?>>Yes</option>
                            <option value="No" <?php if (isset($_GET['smoking']) && $_GET['smoking'] == "No") echo "selected"; ?>>No</option>
                            <option value="Flexible" <?php if (isset($_GET['smoking']) && $_GET['smoking'] == "Flexible") echo "selected"; ?>>Flexible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Drinking</label>
                        <select name="drinking">
                            <option value="">Select</option>
                            <option value="Yes" <?php if (isset($_GET['drinking']) && $_GET['drinking'] == "Yes") echo "selected"; ?>>Yes</option>
                            <option value="No" <?php if (isset($_GET['drinking']) && $_GET['drinking'] == "No") echo "selected"; ?>>No</option><option value="Flexible" <?php if (isset($_GET['drinking']) && $_GET['drinking'] == "Flexible") echo "selected"; ?>>Flexible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pets</label>
                        <select name="pets">
                            <option value="">Select</option>
                            <option value="Yes" <?php if (isset($_GET['pets']) && $_GET['pets'] == "Yes") echo "selected"; ?>>Yes</option>
                            <option value="No" <?php if (isset($_GET['pets']) && $_GET['pets'] == "No") echo "selected"; ?>>No</option>
                            <option value="Flexible" <?php if (isset($_GET['pets']) && $_GET['pets'] == "Flexible") echo "selected"; ?>>Flexible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dietary Preference</label>
                        <select name="dietary_preference">
                            <option value="">Select</option>
                            <option value="Vegetarian" <?php if (isset($_GET['dietary_preference']) && $_GET['dietary_preference'] == "Vegetarian") echo "selected"; ?>>Vegetarian</option>
                            <option value="Non-Vegetarian" <?php if (isset($_GET['dietary_preference']) && $_GET['dietary_preference'] == "Non-Vegetarian") echo "selected"; ?>>Non-Vegetarian</option>
                            <option value="Vegan" <?php if (isset($_GET['dietary_preference']) && $_GET['dietary_preference'] == "Vegan") echo "selected"; ?>>Vegan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="custom-button">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="listing-section">
            <div class="content">
                <h2>Roommate Listings</h2>

            </div>
            <div class="listing-container">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <div class="custom-card">
                            <img src="../assets<?php echo !empty($row['profile_pic']) ? "/uploads/profile/" . htmlspecialchars($row['profile_pic']) : "/images/user.png"; ?>" class="profile-pic" alt="User Image" />
                            <div class="custom-card-content">
                                <div>
                                    <span class="custom-badge"><?php echo htmlspecialchars($row['room_type']); ?> Room</span>
                                    <span class="custom-price">₹<?php echo htmlspecialchars($row['rent_amount']); ?></span>
                                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                                    <p><?php echo htmlspecialchars((DateTime::createFromFormat('Y-m-d', $row['date_of_birth']))->diff(new DateTime())->y); ?> years </p>
                                    <p class="custom-loc"><strong><i class="fa-solid fa-location-dot"></i> &nbsp;</strong><?php echo htmlspecialchars($row['full_address']); ?></p>
                                </div>
                                <a href="roommate-details.php?token=<?php echo urlencode(base64_encode($row['user_id'])); ?>" class="custom-button">View Details</a>
                            </div>
                        </div>
                    <?php endwhile;
                } else { ?>
                    <div class="empty-state">
                        <div class="empty-state__content">
                            <div class="empty-state__icon">
                                <img src="../assets/images/pencil.png" alt="">
                            </div>
                            <div class="empty-state__message">No roommates found matching your criteria.</div>
                            <div class="empty-state__help">
                                Try adjusting your filters.
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("open");
        }
    </script>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>
</body>
</html>