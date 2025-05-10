<?php
require("./check.php");
if (isset($_GET['token'])) {
    $roommate_id = base64_decode($_GET['token'], true);
    if ($roommate_id === false || !ctype_digit($roommate_id)) {
        die("Invalid request.");
    }

    // Fetch room details from the database
    $user_id = intval($roommate_id);

    $sql = "SELECT u.*,ra.*, a.*, CONCAT(a.city,' - ',a.village, ', ', a.po, ', ', a.tehsil, ', ', a.district, ', ', a.state) AS full_address FROM roommate_accommodations ra
        JOIN addresses a ON ra.address_id = a.address_id
        JOIN users u ON u.user_id= $user_id
        WHERE ra.user_id = $user_id";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error fetching room details: " . mysqli_error($conn));
    }

    $roomDetails = mysqli_fetch_assoc($result);

    $accommodation_id = $roomDetails['accommodation_id'];
    // Fetch amenities
    $amenities = [];
    if ($roomDetails) {
        $accommodation_id = $roomDetails['accommodation_id'];
        $sql_amenities = "SELECT am.*, a.amenity_name, a.icon_class FROM roommate_accommodation_amenities raa
                      JOIN amenities a ON raa.amenity_id = a.amenity_id
                      JOIN amenities am ON a.amenity_id = am.amenity_id
                      WHERE raa.accommodation_id = $accommodation_id";
        $result_amenities = mysqli_query($conn, $sql_amenities);

        if ($result_amenities) {
            while ($row_amenity = mysqli_fetch_assoc($result_amenities)) {
                $amenities[] = $row_amenity;
            }
        }
    }
    //Fetch images
    $images = [];
    if ($roomDetails) {
        $sql_images = "SELECT image_url FROM images WHERE entity_type = 'roommate_accommodation' AND entity_id = " . $roomDetails['accommodation_id'];
        $result_images = mysqli_query($conn, $sql_images);
        if ($result_images) {
            while ($row_image = mysqli_fetch_assoc($result_images)) {
                $images[] = $row_image['image_url'];
            }
        }
    }


    $logged_in_user_id = $_SESSION['user_id'];

    $is_saved = false;

    // Check if the roommate is saved by the logged-in user
    $saved_query = "SELECT * FROM saved_services WHERE user_id = '$logged_in_user_id' AND service_id = '$accommodation_id' AND service_type = 'roommate'";
    $saved_result = mysqli_query($conn, $saved_query);

    if (mysqli_num_rows($saved_result) > 0) {
        $is_saved = true;
    }
} else {
    header("Location: ./home.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .highlights {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .highlight {
            background-color: #e0f7fa;
            padding: 8px 12px;
            border-radius: 5px;
        }
    </style>

</head>

<body>
    <?php require("./navbar.php"); ?>
    <div class="roommate-detail-container">
        <?php if ($roomDetails) : ?>


            <p><?php require("./show-message.php"); ?></p>
            <div class="header">
                <div class="profile">
                    <img src="../assets/uploads/profile/<?php if (!empty($roomDetails['profile_pic'])) {
                                                            echo $roomDetails['profile_pic'];
                                                        } else {
                                                            echo "user.png";
                                                        } ?>" alt="Profile Picture">
                    <div class="profile-text">
                        <h2><?php echo $roomDetails['name'] ?></h2>
                        <p>Age: </i><?php echo (isset($roomDetails['date_of_birth']) && !empty($roomDetails['date_of_birth'])) ? (new DateTime($roomDetails['date_of_birth']))->diff(new DateTime('now'))->y : "N/A"; ?></p>
                        <p><strong><i class="fa-solid fa-location-dot"></i> &nbsp;</strong><?php echo $roomDetails['full_address'] ?></p>

                    </div>
                </div>
                <div>
                    <button class="custom-button save-btn" data-roommate="<?php echo $accommodation_id; ?>"><?php echo $is_saved ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';
                                                                                                            echo $is_saved ? '&nbsp;&nbsp;Saved' : '&nbsp;&nbsp;Save'; ?>
                    </button> <br>
                    <a href="tel:<?php echo $roomDetails['phone'] ?>" class="call-button">
                        <i class="fa fa-phone"></i> <?php echo $roomDetails['phone'] ?>
                    </a>
                </div>

            </div>
            <h3>Highlights</h3>
            <div class="highlights">
                <?php
                // Define highlights based on available data
                $dynamic_highlights = [];

                if (!empty($roomDetails['furnishing_status'])) {
                    $dynamic_highlights[] = "✔️ " . ucfirst($roomDetails['furnishing_status']);
                }
                if (!empty($roomDetails['parking']) && strtolower($roomDetails['parking']) === "yes") {
                    $dynamic_highlights[] = "✔️ Parking Available";
                }
                if (!empty($roomDetails['room_type'])) {
                    $dynamic_highlights[] = "✔️ " . ucfirst($roomDetails['room_type']) . " Room";
                }
                if (!empty($roomDetails['guest_policy']) && strtolower($roomDetails['guest_policy']) === "flexible") {
                    $dynamic_highlights[] = "✔️ Guest Friendly";
                }
                if (!empty($roomDetails['daily_schedule']) && strtolower($roomDetails['daily_schedule']) === "flexible") {
                    $dynamic_highlights[] = "✔️ Flexible Daily Schedule";
                }
                if (!empty($roomDetails['lease_duration'])) {
                    $dynamic_highlights[] = "✔️ " . $roomDetails['lease_duration'] . " Stay";
                }

                // Display highlights
                if (!empty($dynamic_highlights)) {
                    foreach ($dynamic_highlights as $highlight) {
                        echo "<div class='highlight'>$highlight</div>";
                    }
                } else {
                    echo "<div class='highlight'>No specific highlights available</div>";
                }
                ?>
            </div>

            <h3>Basic Info</h3>
            <div class="details">
                <div class="info-box"><b>Apartment Name</b> <?php echo $roomDetails['apartment_name'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Approx Rent</b> ₹<?php echo $roomDetails['rent_amount'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Room Type</b> <?php echo $roomDetails['room_type'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>City</b> <?php echo $roomDetails['district'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Stay Duration</b> <?php echo $roomDetails['lease_duration'] ?? 'N/A'; ?></div>
            </div>

            <h3>Living Preferences</h3>
            <div class="details">
                <div class="info-box"><b>Preferred Gender</b> <?php echo $roomDetails['preferred_gender'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Allow Guests</b> <?php echo $roomDetails['guest_policy'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Furnishing Status</b> <?php echo $roomDetails['furnishing_status'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Parking</b> <?php echo $roomDetails['parking'] ?? 'N/A'; ?></div>
                <div class="info-box"><b>Age</b> <?php echo (isset($roomDetails['date_of_birth']) && !empty($roomDetails['date_of_birth'])) ? (new DateTime($roomDetails['date_of_birth']))->diff(new DateTime('now'))->y : "N/A"; ?></div>
            </div>

            <h3>Amenities</h3>
            <section>
                <div class="details">
                    <?php
                    foreach ($amenities as $amenity) {
                        echo "<div class='info-box'><i class='" . htmlspecialchars($amenity['icon_class']) . "'></i> " . htmlspecialchars($amenity['amenity_name']) . "</div>";
                    }
                    ?>
                </div>
            </section>
            <h3>Personal Preferences</h3>
            <div class="preferences">
                <?php
                // Mapping preferences to display text and icons
                $preferences = [
                    "smoking" => ["label" => "Smoking", "icon_yes" => "🚬", "icon_no" => "🚭"],
                    "drinking" => ["label" => "Drinking", "icon_yes" => "🍺", "icon_no" => "🚫🍺"],
                    "pets" => ["label" => "Pets", "icon_yes" => "🐾", "icon_no" => "🚫🐾"],
                    "dietary_preference" => ["label" => "Dietary Preference", "icon" => "🥗"],
                    "daily_schedule" => ["label" => "Schedule", "icon" => "⏰"],
                    "languages_spoken" => ["label" => "Languages", "icon" => "🗣️"],
                    "hobbies" => ["label" => "Hobbies", "icon" => "🎨"],
                ];

                // Display preferences dynamically
                foreach ($preferences as $key => $pref) {
                    if (isset($roomDetails[$key])) {
                        $value = $roomDetails[$key];
                        if ($key === "smoking" || $key === "drinking" || $key === "pets") {
                            if (strtolower($value) === "yes") {
                                echo "<div class='pref-icon'>" . $pref["icon_yes"] . " " . $pref["label"] . ": Yes</div>";
                            } elseif (strtolower($value) === "no") {
                                echo "<div class='pref-icon'>" . $pref["icon_no"] . " " . $pref["label"] . ": No</div>";
                            }
                        } else {
                            echo "<div class='pref-icon'>" . (isset($pref["icon"]) ? $pref["icon"] . " " : "") . $pref["label"] . ": " . ($value ?? 'N/A') . "</div>";
                        }
                    }
                }
                ?>
            </div>

            <h3>Description</h3>
            <section>
                <div class="details">
                    <?php echo $roomDetails['description']; ?>
                </div>
            </section>


            <div class="photos">
                <h3>Pictures</h3>
                <div class="slider-container">
                    <div class="slider">
                        <?php foreach ($images as $image) : ?>
                            <img src="../assets/uploads/roommate-accommodation/<?php echo htmlspecialchars($image) ?>" alt="Room Image">
                        <?php endforeach; ?>
                    </div>
                    <div class="slider-controls">
                        <button onclick="prevSlide()">❮</button>
                        <button onclick="nextSlide()">❯</button>
                    </div>
                </div>
            </div>
            <script>
                let currentIndex = 0;
                const slider = document.querySelector('.slider');
                const totalSlides = document.querySelectorAll('.slider img').length;

                function showSlide(index) {
                    if (index < 0) index = totalSlides - 1;
                    if (index >= totalSlides) index = 0;
                    currentIndex = index;
                    slider.style.transform = `translateX(-${currentIndex * 100}%)`;
                }

                function nextSlide() {
                    showSlide(currentIndex + 1);
                }

                function prevSlide() {
                    showSlide(currentIndex - 1);
                }

                // Auto-advance every 5 seconds
                setInterval(nextSlide, 5000);

                // Initialize first slide
                showSlide(0);
            </script>

        <?php else : ?>
            <p>No room details found.</p>
        <?php endif; ?>
    </div>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $(".save-btn").click(function() {
                var button = $(this);
                var roommateId = button.attr("data-roommate");

                $.ajax({
                    url: "save_service.php",
                    type: "POST",
                    data: {
                        service_id: roommateId,
                        service_type: "roommate"
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "saved") {
                            Swal.fire("Saved!", "Service has been added to your saved list.", "success");
                            button.addClass("saved").html('<i class="fa-solid fa-bookmark"></i>&nbsp;&nbsp; Saved');
                        } else if (response.status === "removed") {
                            Swal.fire("Removed!", "Service has been removed from your saved list.", "info");
                            button.removeClass("saved").html('<i class="fa-regular fa-bookmark"></i>&nbsp;&nbsp; Save');
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