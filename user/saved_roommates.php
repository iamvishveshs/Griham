<?php
require("./check.php");
// Fetch personal details
// Get user_id from session
$nav_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
// Fetch saved accommodation listings
$query = "SELECT
ss.service_id, ss.service_type,
    ra.*,
    u.user_id,
    u.name,
    u.profile_pic,
    CONCAT(ad.village, ', ', ad.po, ', ', ad.tehsil, ', ', ad.district, ', ', ad.state) AS full_address,
    ad.pincode
FROM saved_services ss
JOIN roommate_accommodations ra ON ss.service_id = ra.accommodation_id
JOIN addresses ad ON ra.address_id = ad.address_id
JOIN users u ON ra.user_id = u.user_id
WHERE ss.service_type = 'roommate' AND ss.user_id = '$nav_user_id' AND u.user_id != '$nav_user_id'";
$result = mysqli_query($conn, $query);
$savedCount = mysqli_num_rows($result); // Count saved listings
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>User Dashboard </title>
    <?php
    require("./style-files.php");
    ?>
</head>
<body>
    <?php require("./navbar.php"); ?>
        <!-- Listings Section -->
        <div class="listing-section">
            <div class="content">
                <h2>Saved Roommates</h2>
            </div>
            <div class="listing-container">
                <?php if ($savedCount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>

                        <div class="custom-card">
                       <?php echo ' <img src="../assets' . (!empty($row['profile_pic']) ? "/uploads/profile/" . htmlspecialchars($row['profile_pic']) : "/uploads/profile/user.png") . '" class="Property image" alt="Saved Roommate">'; ?>
                            <div class="custom-card-content">
                                <div>
                                    <span class="custom-badge"><?php echo htmlspecialchars($row['apartment_name']); ?> </span>
                                    <span class="custom-price save-btn saved" data-id="<?php echo $row['service_id']; ?>" data-type="roommate">
                                        <span>
                                            <i class="fa-solid fa-bookmark"></i>
                                        </span>
                                    </span>
                                    <h3><?php echo htmlspecialchars($row['apartment_name']); ?></h3>
                                    <p class="custom-loc"><strong><i class="fa-solid fa-location-dot"></i></strong> <?php echo htmlspecialchars($row['full_address']); ?></p>
                                </div>
                                <a href="roommate-details.php?token=<?php echo urlencode(base64_encode($row['user_id'])); ?>"
                                    class="custom-button">View Details</a>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="empty-state">
                        <div class="empty-state__content">
                            <div class="empty-state__message">No Saved Roommates</div>
                            <div class="empty-state__help">Try Browsing accomodation</div>
                            <a href="location.php?refferer=roommate" class="custom-button">Browse Roommates</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php
    mysqli_close($conn);
    ?>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
    <?php require("./footer.php"); ?>
    <?php
    require("./script-files.php");
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
</body>
</html>