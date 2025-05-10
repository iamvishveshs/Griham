<?php
require("./check.php");
// Fetch personal details
// Get user_id from session
$nav_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$nav_query = "SELECT profile_pic from users WHERE user_id = '$nav_user_id'";
$nav_result = mysqli_query($conn, $nav_query);
if ($rnav_result_row = mysqli_fetch_assoc($nav_result)) {
  $_SESSION['nav_profile_pic'] = $rnav_result_row['profile_pic'] ?? "";
}

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

  <!-- Hero Section -->

  <section class="profile-section">
    <div class="profile-photo">
      <img src="../assets<?php echo !empty($_SESSION['nav_profile_pic']) ? "/uploads/profile/" . htmlspecialchars($_SESSION['nav_profile_pic']) : "/images/user.png"; ?>" />

    </div>
    <div>
      <h1>Welcome!</h1>
      <h2 id="username"> <?php echo $_SESSION['name']; ?> </h2>
    </div>

  </section>

  <div class="saved-container margin-top center">
    <div>
      <h3 class="center margin-top ">Search For Services</h3>
    </div>
    <div class="categories">
      <div class="category" style="background-color: #F7ECFF;" data-color="#D0A6FF"
        onclick="redirectTo('location.php?refferer=accomodation')">
        <i class="fa-solid fa-house"></i> PG/Room/Apartment
      </div>
      <div class="category" style="background-color: #D7F9F3;" data-color="#62E4C1"
        onclick="redirectTo('roommate-choice.php')">
        <i class="fa-solid fa-person-shelter"></i> Roommate
      </div>

      <div class="category" style="background-color: #D7F9F3;" data-color="#62E4C1"
        onclick="redirectTo('location.php?refferer=meal')">
        <i class="fa-solid fa-utensils"></i> Meal
      </div>

      <div class="category" style="background-color: #E2FFD4;" data-color="#8DD76C"
        onclick="redirectTo('location.php?refferer=laundry')">
        <i class="fa-solid fa-shirt"></i> Laundry
      </div>

      <div class="category" style="background-color: #DBD5EC;" data-color="#8A7DBC"
        onclick="redirectTo('location.php?refferer=gym')">
        <i class="fa-solid fa-dumbbell"></i> Gym
      </div>
    <div class="category" style="background-color: #DBD5EC;" data-color="#8A7DBC"
        onclick="redirectTo('location.php?refferer=emergency')">
        <i class="fa-solid fa-hospital"></i> Emergency
      </div>
    </div>

  </div>
  <?php

  $accommodation_query = "SELECT s.service_id, s.service_type, a.property_name, a.property_type, a.main_image, 
                            CONCAT_WS(', ', adr.city, adr.state) AS address
                        FROM saved_services s
                        LEFT JOIN accommodation_services a ON s.service_id = a.service_id AND s.service_type = 'accommodation'
                        LEFT JOIN addresses adr ON a.address_id = adr.address_id
                        WHERE s.user_id = '$nav_user_id' AND s.service_type = 'accommodation' LIMIT 3";

  $accommodation_result = mysqli_query($conn, $accommodation_query);

  if (!$accommodation_result) {
    die("Query failed: " . mysqli_error($conn));
  }
  $accommodation_count = mysqli_num_rows($accommodation_result); // Count saved accommodations

  ?>

  <section class="saved-container">
    <div class="saved-container-header">
      <h2>Saved Properties</h2>
      <?php
      if ($accommodation_count > 0) {
      ?>
        <button class="view-all-btn" onclick="window.location.href='saved_accommodations.php'">View All</button>
      <?php } ?>
    </div>

    <div class="saved-card-list">
      <?php
      if ($accommodation_count > 0) {
        while ($row = mysqli_fetch_assoc($accommodation_result)) {
          echo '<a href="property-details.php?token=' . urlencode(base64_encode($row['service_id'])) . '">';
          echo '<div class="saved-card">';
          echo '<img src="../assets' . (!empty($row['main_image']) ? "/uploads/accommodation-images/" . htmlspecialchars($row['main_image']) : "/images/default_property.png") . '" alt="Saved Property">';
          echo '<div class="saved-card-info">';
          echo '<h4>' . htmlspecialchars($row['property_name']) . '</h4>';
          echo '<p><strong><i class="fa-solid fa-location-dot"></i></strong> ' . htmlspecialchars($row['address']) . '</p>';
          echo '</div>';
          echo '</div>';
          echo '</a>';
        }
      } else {
        echo '<p class="no-saved-message">You have not saved any properties.</p>';
      }
      ?>
    </div>
  </section>


  <?php

$meal_query = "SELECT s.service_id, s.service_type, ms.name, ms.main_image, 
                          CONCAT_WS(', ', adr.city, adr.state) AS address
                      FROM saved_services s
                      LEFT JOIN meal_services ms ON s.service_id = ms.service_id AND s.service_type = 'meal'
                      LEFT JOIN addresses adr ON ms.address_id = adr.address_id
                      WHERE s.user_id = '$nav_user_id' AND s.service_type = 'meal' LIMIT 3";

$meal_result = mysqli_query($conn, $meal_query);

if (!$meal_result) {
  die("Query failed: " . mysqli_error($conn));
}
$meal_count = mysqli_num_rows($meal_result); // Count saved accommodations

?>

<section class="saved-container">
  <div class="saved-container-header">
    <h2>Saved Dhaba/Tiffin/Cafe Services</h2>
    <?php
    if ($meal_count > 0) {
    ?>
      <button class="view-all-btn" onclick="window.location.href='saved_meal_services.php'">View All</button>
    <?php } ?>
  </div>

  <div class="saved-card-list">
    <?php
    if ($meal_count > 0) {
      while ($row = mysqli_fetch_assoc($meal_result)) {
        echo '<a href="meal-services-details.php?token=' . urlencode(base64_encode($row['service_id'])) . '">';
        echo '<div class="saved-card">';
        echo '<img src="../assets' . (!empty($row['main_image']) ? "/uploads/meal-services/" . htmlspecialchars($row['main_image']) : "/images/default_property.png") . '" alt="Saved Property">';
        echo '<div class="saved-card-info">';
        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
        echo '<p><strong><i class="fa-solid fa-location-dot"></i></strong> ' . htmlspecialchars($row['address']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</a>';
      }
    } else {
      echo '<p class="no-saved-message">You have not saved any properties.</p>';
    }
    ?>
  </div>
</section>



<?php

$meal_query = "SELECT s.service_id, s.service_type, ms.name, ms.main_image, 
                          CONCAT_WS(', ', adr.city, adr.state) AS address
                      FROM saved_services s
                      LEFT JOIN gym_services ms ON s.service_id = ms.service_id AND s.service_type = 'gym'
                      LEFT JOIN addresses adr ON ms.address_id = adr.address_id
                      WHERE s.user_id = '$nav_user_id' AND s.service_type = 'gym' LIMIT 3";

$meal_result = mysqli_query($conn, $meal_query);

if (!$meal_result) {
  die("Query failed: " . mysqli_error($conn));
}
$meal_count = mysqli_num_rows($meal_result); // Count saved accommodations

?>

<section class="saved-container">
  <div class="saved-container-header">
    <h2>Saved Gyms</h2>
    <?php
    if ($meal_count > 0) {
    ?>
      <button class="view-all-btn" onclick="window.location.href='saved_gym_services.php'">View All</button>
    <?php } ?>
  </div>

  <div class="saved-card-list">
    <?php
    if ($meal_count > 0) {
      while ($row = mysqli_fetch_assoc($meal_result)) {
        echo '<a href="gym-services-details.php?token=' . urlencode(base64_encode($row['service_id'])) . '">';
        echo '<div class="saved-card">';
        echo '<img src="../assets' . (!empty($row['main_image']) ? "/uploads/gym-services/" . htmlspecialchars($row['main_image']) : "/images/default_property.png") . '" alt="Saved Property">';
        echo '<div class="saved-card-info">';
        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
        echo '<p><strong><i class="fa-solid fa-location-dot"></i></strong> ' . htmlspecialchars($row['address']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</a>';
      }
    } else {
      echo '<p class="no-saved-message">You have not saved any Gym.</p>';
    }
    ?>
  </div>
</section>




  <?php

  $roommate_query = "SELECT 
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

  $roommate_result = mysqli_query($conn, $roommate_query);

  if (!$roommate_result) {
    die("Query failed: " . mysqli_error($conn));
  }
  $roommate_count = mysqli_num_rows($roommate_result); // Count saved accommodations

  ?>

  <section class="saved-container">
    <div class="saved-container-header">
      <h2>Saved Roommates</h2>
      <?php
      if ($roommate_count > 0) {
      ?>
        <button class="view-all-btn" onclick="window.location.href='saved_roommates.php'">View All</button>
      <?php } ?>
    </div>

    <div class="saved-card-list">
      <?php
      if ($roommate_count > 0) {
        while ($row = mysqli_fetch_assoc($roommate_result)) {
          echo '<a href="roommate-details.php?token=' . urlencode(base64_encode($row['user_id'])) . '">';
          echo '<div class="saved-card">';
          echo '<img src="../assets' . (!empty($row['profile_pic']) ? "/uploads/profile/" . htmlspecialchars($row['profile_pic']) : "/uploads/profile/user.png") . '" alt="Saved Roommate">';
          echo '<div class="saved-card-info">';
          echo '<h4>' . htmlspecialchars($row['apartment_name']) . '</h4>';
          echo '<p><strong><i class="fa-solid fa-location-dot"></i></strong> ' . htmlspecialchars($row['full_address']) . '</p>';
          echo '</div>';
          echo '</div>';
          echo '</a>';
        }
      } else {
        echo '<p class="no-saved-message">You have not saved any Roommate.</p>';
      }
      ?>
    </div>
  </section>

  <?php
$laundry_query = "SELECT s.service_id, s.service_type, ls.name, ls.main_image, 
                          ls.pickup, ls.delivery, ls.dry_cleaning,
                          CONCAT_WS(', ', adr.city, adr.state) AS address
                      FROM saved_services s
                      LEFT JOIN laundry_services ls ON s.service_id = ls.service_id AND s.service_type = 'laundry'
                      LEFT JOIN addresses adr ON ls.address_id = adr.address_id
                      WHERE s.user_id = '$nav_user_id' AND s.service_type = 'laundry' LIMIT 3";

$laundry_result = mysqli_query($conn, $laundry_query);

if (!$laundry_result) {
  die("Query failed: " . mysqli_error($conn));
}
$laundry_count = mysqli_num_rows($laundry_result);
?>

<section class="saved-container">
  <div class="saved-container-header">
    <h2>Saved Laundry Services</h2>
    <?php
    if ($laundry_count > 0) {
    ?>
      <button class="view-all-btn" onclick="window.location.href='saved_laundry_services.php'">View All</button>
    <?php } ?>
  </div>

  <div class="saved-card-list">
    <?php
    if ($laundry_count > 0) {
      while ($row = mysqli_fetch_assoc($laundry_result)) {
        echo '<a href="laundry-services-details.php?token=' . urlencode(base64_encode($row['service_id'])) . '">';
        echo '<div class="saved-card">';
        echo '<img src="../assets' . (!empty($row['main_image']) ? "/uploads/laundry-services/" . htmlspecialchars($row['main_image']) : "/images/default_property.png") . '" alt="Saved Laundry Service">';
        echo '<div class="saved-card-info">';
        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
        echo '<p><strong><i class="fa-solid fa-location-dot"></i></strong> ' . htmlspecialchars($row['address']) . '</p>';
        
        // Display service features as small icons/badges
        echo '<div class="service-badges">';
        if ($row['pickup'] == 1) echo '<small><i class="fa-solid fa-truck-pickup"></i> Pickup</small> ';
        if ($row['delivery'] == 1) echo '<small><i class="fa-solid fa-truck"></i> Delivery</small> ';
        if ($row['dry_cleaning'] == 1) echo '<small><i class="fa-solid fa-tshirt"></i> Dry Clean</small>';
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
        echo '</a>';
      }
    } else {
      echo '<p class="no-saved-message">You have not saved any Laundry Services.</p>';
    }
    ?>
  </div>
</section>

  <?php
  // Close connection
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
</body>

</html>