<?php
require("./check.php");
// Fetch personal details
// Get user_id from session
$nav_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
// Fetch saved accommodation listings
$query = "SELECT s.service_id, s.service_type,ms.category, ms.name, ms.main_image,
                          CONCAT_WS(', ', adr.city, adr.state) AS address
                      FROM saved_services s
                      LEFT JOIN meal_services ms ON s.service_id = ms.service_id AND s.service_type = 'meal'
                      LEFT JOIN addresses adr ON ms.address_id = adr.address_id
                      WHERE s.user_id = '$nav_user_id' AND s.service_type = 'meal'";
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
                <h2>Saved Dhaba/Tiffin/Cafe</h2>
            </div>
            <div class="listing-container">
                <?php if ($savedCount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>

                        <div class="custom-card">
                            <img src="../assets/uploads/meal-services/<?php echo !empty($row['main_image']) ?  htmlspecialchars($row['main_image']) : "default_property.jpeg"; ?>"
                                class="Property image" alt="Property image" />
                            <div class="custom-card-content">
                                <div>
                                    <span class="custom-badge"><?php echo htmlspecialchars($row['category']); ?> </span>
                                    <span class="custom-price save-btn saved" data-id="<?php echo $row['service_id']; ?>" data-type="meal">
                                        <span>
                                            <i class="fa-solid fa-bookmark"></i>
                                        </span>
                                    </span>
                                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                    <p class="custom-loc"><strong><i class="fa-solid fa-location-dot"></i></strong> <?php echo htmlspecialchars($row['address']); ?></p>
                                </div>
                                <a href="property-details.php?token=<?php echo urlencode(base64_encode($row['service_id'])); ?>"
                                    class="custom-button">View Details</a>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="empty-state">
                        <div class="empty-state__content">
                            <div class="empty-state__message">No Saved Dhaba/Tiffin/Cafe</div>
                            <div class="empty-state__help">Try Browsing Dhaba/Tiffin/Cafe</div>
                            <a href="location.php?refferer=meal" class="custom-button">Browse PG/Room/Apartment</a>
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