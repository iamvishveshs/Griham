<?php
require("./check.php");
// Fetch personal details
$nav_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
// Fetch saved laundry listings
$query = "SELECT s.service_id, s.service_type, l.name, l.main_image,
                CONCAT_WS(', ', adr.city, adr.state) AS address,
                l.pickup, l.delivery, l.dry_cleaning, l.washing, l.ironing
            FROM saved_services s
            LEFT JOIN laundry_services l ON s.service_id = l.service_id AND s.service_type = 'laundry'
            LEFT JOIN addresses adr ON l.address_id = adr.address_id
            WHERE s.user_id = '$nav_user_id' AND s.service_type = 'laundry'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
$savedCount = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Saved Laundry Services</title>
    <?php require("./style-files.php"); ?>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <!-- Listings Section -->
    <div class="listing-section">
        <div class="content">
            <h2>Saved Laundry Services</h2>
        </div>
        <div class="listing-container">
            <?php if ($savedCount > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="custom-card">
                        <img src="../assets/uploads/laundry-services/<?php echo !empty($row['main_image']) ? htmlspecialchars($row['main_image']) : "default_laundry.jpeg"; ?>"
                             class="Laundryimage" alt="Laundry service image" />
                        <div class="custom-card-content">
                            <div>
                                <span class="custom-badge">Laundry</span>
                                <span class="custom-price save-btn saved" data-id="<?php echo $row['service_id']; ?>" data-type="laundry">
                                    <i class="fa-solid fa-bookmark"></i>
                                </span>
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="custom-loc"><strong><i class="fa-solid fa-location-dot"></i></strong> <?php echo htmlspecialchars($row['address']); ?></p>
                                <p class="service-features">
                                    <?php if ($row['pickup'] == 1) { ?><span class="feature"><i class="fa-solid fa-truck-pickup"></i> Pickup</span><?php } ?>
                                    <?php if ($row['delivery'] == 1) { ?><span class="feature"><i class="fa-solid fa-truck"></i> Delivery</span><?php } ?>
                                    <?php if ($row['dry_cleaning'] == 1) { ?><span class="feature"><i class="fa-solid fa-tshirt"></i> Dry Cleaning</span><?php } ?>
                                    <?php if ($row['washing'] == 1) { ?><span class="feature"><i class="fa-solid fa-soap"></i> Washing</span><?php } ?>
                                    <?php if ($row['ironing'] == 1) { ?><span class="feature"><i class="fa-solid fa-iron"></i> Ironing</span><?php } ?>
                                </p>
                            </div>
                            <a href="./laundry-services-details.php?token=<?php echo urlencode(base64_encode($row['service_id'])); ?>"
                               class="custom-button">View Details</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-state__content">
                        <div class="empty-state__message">No Saved Laundry Services</div>
                        <div class="empty-state__help">Try Browsing Laundry Section</div>
                        <a href="location.php?refferer=laundry" class="custom-button">Browse Laundry Services</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php mysqli_close($conn); ?>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>
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
                    data: { service_id: serviceId, service_type: serviceType },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "saved") {
                            Swal.fire("Saved!", "Service has been added to your saved list.", "success");
                            button.addClass("saved").html('<i class="fa-solid fa-bookmark"></i>');
                        } else if (response.status === "removed") {
                            Swal.fire("Removed!", "Service has been removed from your saved list.", "info");
                            button.removeClass("saved").html('<i class="fa-regular fa-bookmark"></i>');
                            // Remove the card from view since we're on the saved services page
                            button.closest('.custom-card').fadeOut(300, function() {
                                $(this).remove();
                                // Check if there are any remaining cards
                                if ($('.custom-card').length === 0) {
                                    // If no cards remain, show the empty state
                                    $('.listing-container').html(`
                                        <div class="empty-state">
                                            <div class="empty-state__content">
                                                <div class="empty-state__message">No Saved Laundry Services</div>
                                                <div class="empty-state__help">Try Browsing Laundry Section</div>
                                                <a href="location.php?refferer=laundry" class="custom-button">Browse Laundry Services</a>
                                            </div>
                                        </div>
                                    `);
                                }
                            });
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