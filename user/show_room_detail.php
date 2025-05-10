<?php
require("./check.php");
// Fetch room details from the database
$user_id = intval($_SESSION['user_id']);
$sql = "SELECT u.*,ra.*, a.*, CONCAT(a.city,' - ',a.village, ', ', a.po, ', ', a.tehsil, ', ', a.district, ', ', a.state) AS full_address FROM roommate_accommodations ra
        JOIN addresses a ON ra.address_id = a.address_id
        JOIN users u ON u.user_id= $user_id
        WHERE ra.user_id = $user_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching room details: " . mysqli_error($conn));
}
$roomDetails = mysqli_fetch_assoc($result);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <a href="./roommate-choice.php" class='contact-btn center'>< Go Back</a><br>

                <p><?php require("./show-message.php"); ?></p>
                    <div class="header">
                        <div class="profile">
                            <img src="../assets/uploads/profile/<?php if (!empty($roomDetails['profile_pic'])) { echo $roomDetails['profile_pic'];} else {echo "user.png"; } ?>" alt="Profile Picture">
                            <div class="profile-text">
                                <h2><?php echo $_SESSION['name'] ?></h2>
                                <p>Age: </i><?php echo (isset($roomDetails['date_of_birth']) && !empty($roomDetails['date_of_birth'])) ? (new DateTime($roomDetails['date_of_birth']))->diff(new DateTime('now'))->y : "Date of birth not available."; ?></p>
                                <p><strong><i class="fa-solid fa-location-dot"></i> &nbsp;</strong><?php echo $roomDetails['full_address'] ?></p>
                            </div>
                        </div>
                        <div>
                            <a class="custom-button" href="update_room_details.php"> <i class="fa fa-edit"></i>  &nbsp; &nbsp;Update Details</a> <br><br>
                            <button class="call-button">
                                <i class="fa fa-phone"></i> <?php echo $roomDetails['phone'] ?>
                            </button>
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
                            $dynamic_highlights[] = "✔️ " . ucfirst($roomDetails['room_type'])." Room";
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
                        <div class="info-box"><b>DOB</b> <?php echo $roomDetails['date_of_birth'] ?? 'N/A'; ?></div>
                    </div>
                    <section>
                        <h3>Amenities</h3>
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
                    <section>
                        <h3>Description</h3>
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

    <button id="deleteButton" class="custom-button delete" data-accommodation-id="<?php echo $roomDetails['accommodation_id'] ?>">Delete Room Details</button>
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
                    <script>
    document.getElementById("deleteButton").addEventListener("click", function () {
        const accommodationId = this.dataset.accommodationId; // Get ID from button data attribute
        Swal.fire({
            title: 'Are you sure?',
            text: "This action will permanently delete the accommodation.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send POST request to delete.php
                fetch('./delete-roommate-listing.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `accommodation_id=${accommodationId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire(
                            'Deleted!',
                            data.message,
                            'success'
                        ).then(() => {
                            // Redirect to roommate-choice.php
                            window.location.href = 'roommate-choice.php';
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'An unexpected error occurred.',
                        'error'
                    );
                    console.error(error);
                });
            }
        });
    });
</script>
                <?php else : ?>
                    <p>No room details found.</p>
                <?php endif; ?>
    </div>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>
</body>
</html>